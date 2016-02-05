http = require('http')
fs = require('fs')

https		= require 'https'
querystring	= require 'querystring'

iHost = 'sarshomar.com'
port = 8000
host = '127.0.0.2'
server = http.createServer (request, response) -> 
	if /\.dev$/.test request.headers['x-forwarded-server']
		iHost = iHost.replace(/\.[^\.]+$/, '.dev')

	response.writeHead 200, {"Content-Type": "text/plain"}
	
	response.write "On request...";
	bin_data = ''
	request.on 'data', (data) ->
		bin_data += data;

	request.on 'end', (data) ->
		bin_data = bin_data.toString()
		try
			obj = JSON.parse(bin_data)
		catch _error
			fs.appendFileSync './error.txt', bin_data + "\n"
		
		postData = querystring.stringify 
			chat_id 		: obj.message.chat.id,
			text	 		: obj.message.text,
			reply_markup	: '{"keyboard":[["a"],["b"]],"one_time_keyboard":true}'

		options = 
			hostname: 'api.telegram.org',
			port: 443,
			path: '/bot142711391:AAFH0ULw7BzwdmmiZHv2thKQj7ibb49DJ44/sendMessage',
			method: 'POST',
			headers:
				'Content-Type': 'application/x-www-form-urlencoded',
				'Content-Length': postData.length

		request = https.request options, (response) ->
			console.log "STATUS: #{response.statusCode}"

			console.log "HEADERS: #{JSON.stringify(response.headers)}";

			response.setEncoding('utf8');
			response.on 'data', (chunk) -> 
				console.log "BODY: #{chunk}";
			response.on 'end', () ->
				console.log 'No more data in response.'


		request.on 'error', (e) ->
			console.log "problem with request: #{e.message}"

		request.write postData
		request.end()

	response.end()
	undefined


server.listen port, host

console.log("Server running at http://#{host}:#{port}");

