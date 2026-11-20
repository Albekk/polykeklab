"""Build a deployment archive using an explicit allowlist; never include secrets."""
from pathlib import Path
from zipfile import ZipFile, ZIP_DEFLATED

root = Path(__file__).resolve().parent.parent
destination = root / '.runtime' / 'php-labs-hosting.zip'
destination.parent.mkdir(exist_ok=True)
files = [root / name for name in ['index.php', '.htaccess', 'config.example.php']]
for directory in ['app', 'assets', 'includes', 'lab1', 'lab2', 'sql']:
    files.extend(path for path in (root / directory).rglob('*') if path.is_file())
with ZipFile(destination, 'w', ZIP_DEFLATED) as archive:
    for path in sorted(files):
        archive.write(path, path.relative_to(root).as_posix())
with ZipFile(destination) as archive:
    names = archive.namelist()
    assert 'config.php' not in names
    assert not any(name.startswith(('.git/', '.runtime/', 'tests/', 'tools/')) for name in names)
print(f'{destination}: {len(names)} files, {destination.stat().st_size} bytes; no local config or test database')
