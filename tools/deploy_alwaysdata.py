"""Deploy through pinned SSH/SFTP. Credentials are read only from ignored .runtime."""
import base64
import hashlib
import json
import re
import shlex
import stat
import sys
from datetime import datetime, timezone
from pathlib import Path

ROOT = Path(__file__).resolve().parent.parent
sys.path.insert(0, str(ROOT / '.runtime' / 'python'))
import paramiko

HOST = 'ssh-polykeklab.alwaysdata.net'
FINGERPRINTS = {
    'ssh-ed25519': '5i/vJYokzNsnXAeHkwzEm+3kxPQWwsRzwXFPQ7oOvNI',
    'ecdsa-sha2-nistp256': 'QAA/SWd369ht78q/gnoAXIGEAfwi2G1ilM5je/WfJRw',
    'ssh-rsa': 'fabBPw3mNvZcsBz9bZvqw/CBdYJ5enWihI15pFaCpUg',
}

class PinnedHost(paramiko.MissingHostKeyPolicy):
    def missing_host_key(self, client, hostname, key):
        digest = base64.b64encode(hashlib.sha256(key.asbytes()).digest()).decode().rstrip('=')
        if hostname != HOST or FINGERPRINTS.get(key.get_name()) != digest:
            raise RuntimeError('SSH host identity does not match the supplied fingerprints')
        print('Verified SSH host key:', key.get_name(), 'SHA256:' + digest, flush=True)

def command(client, text):
    _, out, err = client.exec_command(text, timeout=45)
    result = out.read().decode('utf-8', errors='replace')
    error = err.read().decode('utf-8', errors='replace')
    if out.channel.recv_exit_status() != 0:
        raise RuntimeError('Remote operation failed: ' + error[:400])
    return result

def main():
    secrets = json.loads((ROOT / '.runtime/alwaysdata-secrets.json').read_text(encoding='utf-8-sig'))
    if secrets['ssh_host'] != HOST or secrets['ssh_user'] != 'polykeklab':
        raise RuntimeError('Unexpected SSH destination')
    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(PinnedHost())
    try:
        client.connect(HOST, username=secrets['ssh_user'], password=secrets['ssh_password'],
                       look_for_keys=False, allow_agent=False, timeout=20, auth_timeout=20)
        with client.open_sftp() as sftp:
            print('Connected. Home:', sftp.normalize('.'), flush=True)
            print('Existing web root:', flush=True)
            for item in sftp.listdir_attr('/home/polykeklab/www'):
                print(('directory' if stat.S_ISDIR(item.st_mode) else 'file'), item.filename, item.st_size, flush=True)
            print('Remote PHP:', command(client, 'php -v').splitlines()[0], flush=True)
            if '--activate' in sys.argv:
                manifest = json.loads((ROOT / '.runtime/alwaysdata-release.json').read_text(encoding='utf-8'))
                release = manifest['release']
                if not re.fullmatch(r'/home/polykeklab/php-labs-\d{8}-\d{6}', release):
                    raise RuntimeError('Unexpected release path')
                webroot = '/home/polykeklab/www'
                backup = '/home/polykeklab/www-before-php-labs-' + manifest['stamp']
                sftp.stat(release + '/index.php')
                sftp.stat(release + '/config.php')
                # Reversible activation: retain the complete former webroot.
                sftp.rename(webroot, backup)
                try:
                    sftp.rename(release, webroot)
                except Exception:
                    sftp.rename(backup, webroot)
                    raise
                manifest.update({'active_root':webroot, 'backup':backup})
                (ROOT / '.runtime/alwaysdata-release.json').write_text(json.dumps(manifest), encoding='utf-8')
                print('Activated:', webroot, 'Previous webroot preserved:', backup, flush=True)
                return
            if '--upload' not in sys.argv:
                return
            stamp = datetime.now(timezone.utc).strftime('%Y%m%d-%H%M%S')
            release = '/home/polykeklab/php-labs-' + stamp
            sftp.mkdir(release, mode=0o750)
            files = [ROOT / 'index.php', ROOT / '.htaccess']
            for directory in ['app', 'assets', 'includes', 'lab1', 'lab2']:
                files.extend(p for p in (ROOT / directory).rglob('*') if p.is_file())
            made = {release}
            for path in sorted(files):
                relative = path.relative_to(ROOT).as_posix()
                remote = release + '/' + relative
                parent = remote.rsplit('/', 1)[0]
                if parent not in made:
                    current = release
                    for part in relative.split('/')[:-1]:
                        current += '/' + part
                        if current not in made:
                            sftp.mkdir(current, mode=0o750); made.add(current)
                sftp.put(str(path), remote)
                sftp.chmod(remote, 0o640)
            config = {'base_path': '', 'db': {'host': secrets['db_host'], 'port': 3306,
                'name': secrets['db_name'], 'user': secrets['db_user'], 'password': secrets['db_password']}}
            encoded = base64.b64encode(json.dumps(config).encode()).decode()
            with sftp.open(release + '/config.php', 'w') as handle:
                handle.write("<?php\nreturn json_decode(base64_decode('" + encoded + "'), true, 512, JSON_THROW_ON_ERROR);\n")
            sftp.chmod(release + '/config.php', 0o600)
            result = command(client, 'find ' + shlex.quote(release) + " -name '*.php' -exec php -l {} \\;")
            if 'Errors parsing' in result or 'Fatal error' in result:
                raise RuntimeError('Remote PHP lint failed')
            print('Uploaded and linted', len(files), 'application files. Configuration permission: 0600.', flush=True)
            (ROOT / '.runtime/alwaysdata-release.json').write_text(json.dumps({'release': release, 'previous_root': '/home/polykeklab/www', 'stamp':stamp}), encoding='utf-8')
            # Query only the already imported database; never print credentials.
            check = "define('APP_ENTRY',true); $_SERVER['SCRIPT_NAME']='/index.php'; require " + json.dumps(release + '/includes/bootstrap.php') + "; foreach(['contacts','users','articles'] as $t) { echo $t.': '.db()->query('SELECT COUNT(*) FROM '.$t)->fetchColumn().PHP_EOL; }"
            print(command(client, 'php -r ' + shlex.quote(check)), flush=True)
            print('Prepared release:', release, flush=True)
    finally:
        client.close()

if __name__ == '__main__':
    try:
        main()
    except paramiko.AuthenticationException:
        print('Authentication rejected. Check the SSH/SFTP password in the ignored credentials file.', file=sys.stderr)
        sys.exit(1)
