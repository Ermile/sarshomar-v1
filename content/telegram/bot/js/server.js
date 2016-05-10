(function() {
  var fs, host, http, iHost, port, server;

  http = require('http');

  fs = require('fs');

  iHost = 'sarshomar.com';

  port = 8000;

  host = '127.0.0.2';

  server = http.createServer(function(request, response) {
    var bin_data;
    if (/\.dev$/.test(request.headers['x-forwarded-server'])) {
      iHost = iHost.replace(/\.[^\.]+$/, '.dev');
    }
    response.writeHead(200, {
      "Content-Type": "text/plain"
    });
    response.write("On request...");
    bin_data = '';
    request.on('data', function(data) {
      return bin_data += data;
    });
    request.on('end', function(data) {
      var _error, error, obj, options;
      bin_data = bin_data.toString();
      try {
        obj = JSON.parse(bin_data);
      } catch (error) {
        _error = error;
        fs.appendFileSync('./error.txt', bin_data + "\n");
      }
      options = {
        hostname: iHost,
        port: 80,
        path: '/telegram',
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Content-Length': bin_data.length,
          'Accept': 'application/json'
        }
      };
      request = http.request(options, function(response) {
        response.setEncoding('utf8');
        response.on('data', function(chunk) {
          return console.log("BODY: " + chunk);
        });
        return response.on('end', function() {
          return console.log('No more data in response.');
        });
      });
      request.write(bin_data);
      return request.end();
    });
    response.end();
    return void 0;
  });

  server.listen(port, host);

  console.log("Server running at http://" + host + ":" + port);

}).call(this);
