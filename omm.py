from tornado.httpclient import AsyncHTTPClient, HTTPRequest
from tornado.web import asynchronous, RequestHandler

class OperaMiniMirror(RequestHandler):
  def get(self):
    self.set_header('Content-Type', 'text/plain; charset=ascii')
    self.set_status(400, 'This page can only access with POST request.')
    self.finish('This page can only access with POST request.')
  @asynchronous
  def post(self):
    server_url = 'http://mini5.opera-mini.net/'
    prefer_list = self.request.headers.get_list('x-prefer-server')
    if prefer_list: server_url = 'http://' + prefer_list[0] + '/'
    headers = {'Content-Type': 'application/xml', 'Connection': 'Keep-Alive'}
    req = HTTPRequest(server_url, 'POST', headers, self.request.body)
    http_client = AsyncHTTPClient()
    http_client.fetch(req, self.echo_to_client)
    self.set_header('Content-Type', 'application/octet-stream')
    self.set_header('Cache-Control', 'private, no-cache')
  def echo_to_client(self, response):
    self.finish(response.body)