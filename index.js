var ipAddrness = "0.0.0.0";
var port = 8090;

var http = require("http");
var url = require("url");

function application(req, res){
  var contentLength = parseInt(req.headers["content-length"]);
  // contentLength nay be NaN.
  if(!(contentLength > 0)){
    res.writeHead(400);
    res.write("400 Bad request.");
    res.end();
    return;
  }
  var requestBuffer = [];

  req.on("data", function(requestChunk){
    requestBuffer.push(requestChunk);
  });

  req.on("end", function(){
    var option = url.parse("http://mini5.opera-mini.net/");
    option.method = "POST";

    option.headers = {
      "Content-Type": "application/xml",
      "Connection": "Keep-Alive",
      "Accept-Encoding": "gzip",
      "Content-Length": contentLength,
      "User-Agent": req.headers['user-agent']
    };

    var originRequest = http.request(option, function(originResponse){
      if(!res.headersSent){
        res.writeHead(200, {"Content-Type": "application/octet-stream", "Cache-Control": "private, no-cache"});
      }
      originResponse.on("data", function(originDataChunk){
        res.write(originDataChunk);
      }).on("end", function(){ res.end(); }).on("error", function(error){ res.write(error); res.end(); });
    });

    originRequest.setTimeout(20000, function(){
      originRequest.abort();
      res.end();
    });
    originRequest.on("error", function(error){
      console.log(error);
      res.end();
    });
    originRequest.write(Buffer.concat(requestBuffer));
    originRequest.end();
  });
}

http.createServer(application).listen(port, ipAddrness);