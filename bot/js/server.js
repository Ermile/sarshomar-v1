(function() {
  var host, http, port, server;

  http = require('http');

  port = 8000;

  host = '127.0.0.2';

  server = http.createServer(function(request, response) {
    response.writeHead(200, {
      "Content-Type": "text/plain"
    });
    response.write("On request...");
    console.log(request);
    response.end();
    return void 0;
  });

  server.listen(port, host);

  console.log("Server running at http://" + host + ":" + port);

}).call(this);
