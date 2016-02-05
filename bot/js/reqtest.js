(function() {
  var https, options, postData, querystring, request;

  https = require('https');

  querystring = require('querystring');

  postData = querystring.stringify({
    chat_id: '58164083',
    text: 'jitter',
    reply_markup: '{"keyboard":[["a"],["b"]],"one_time_keyboard":true}'
  });

  options = {
    hostname: 'api.telegram.org',
    port: 443,
    path: '/bot142711391:AAFH0ULw7BzwdmmiZHv2thKQj7ibb49DJ44/sendMessage',
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Content-Length': postData.length
    }
  };

  request = https.request(options, function(response) {
    console.log("STATUS: " + response.statusCode);
    console.log("HEADERS: " + (JSON.stringify(response.headers)));
    response.setEncoding('utf8');
    response.on('data', function(chunk) {
      return console.log("BODY: " + chunk);
    });
    return response.on('end', function() {
      return console.log('No more data in response.');
    });
  });

  request.on('error', function(e) {
    return console.log("problem with request: " + e.message);
  });

  request.write(postData);

  request.end();

}).call(this);
