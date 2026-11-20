"""Send only fictional classroom data to the explicitly required httpbin endpoint."""
import urllib.request, urllib.parse, json, re
page=urllib.request.urlopen('http://127.0.0.1:8080/lab1/headers.php',timeout=20).read().decode()
text=re.search(r'<textarea[^>]*>(.*?)</textarea>',page,re.S)[1]
print('get_headers:', 'HTTPS response received' if 'HTTP/' in text else 'handled network error; no response headers')
try:
    data=urllib.parse.urlencode([('name','PHP Test Student'),('email','student@example.com'),('type','thanks'),('message','Fictional classroom form test'),('reply[]','email')]).encode()
    response=urllib.request.urlopen('https://httpbin.org/post',data=data,timeout=20)
    result=json.load(response)
    assert result['form']['email']=='student@example.com' and result['form']['reply[]']=='email'
    print('PASS external POST: httpbin returned fictional submitted form fields')
except Exception as ex:
    print('UNVERIFIED external POST:',type(ex).__name__,str(ex))
