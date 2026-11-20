"""Read-only checks of the deployed site, including assets and live headers."""
import re
import sys
import urllib.request

base = (sys.argv[1] if len(sys.argv) > 1 else 'https://polykeklab.alwaysdata.net').rstrip('/')
for path in ['/assets/site.css', '/assets/blog.css', '/assets/logo.svg']:
    with urllib.request.urlopen(base + path, timeout=20) as response:
        body = response.read()
        assert response.status == 200 and len(body) > 100, path
        print('PASS asset', path)
with urllib.request.urlopen(base + '/lab1/headers.php', timeout=25) as response:
    body = response.read().decode('utf-8')
    result = re.search(r'<textarea[^>]*>(.*?)</textarea>', body, re.S)[1]
    assert 'HTTP/' in result, 'get_headers returned the handled network error rather than headers'
    print('PASS get_headers received actual HTTPS headers on alwaysdata')
with urllib.request.urlopen(base + '/about-me', timeout=20) as response:
    body = response.read().decode('utf-8')
    assert 'Беккер Александр Павлович' in body and '253-321' in body
    print('PASS author name and group')
print('5 hosting checks passed')
