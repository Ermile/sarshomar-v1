http = require('http')
fs = require('fs')

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
		
		http		= require 'http'
		options = 
			hostname: iHost,
			port: 80,
			path: '/telegram',
			method: 'POST',
			headers:
				'Content-Type'		: 'application/json'
				'Content-Length'	: bin_data.length
				'Accept'			: 'application/json'

		request = http.request options, (response) ->
			response.setEncoding('utf8');
			response.on 'data', (chunk) -> 
				console.log "BODY: #{chunk}";
			response.on 'end', () ->
				console.log 'No more data in response.'

		request.write bin_data
		request.end()

	response.end()
	undefined


server.listen port, host

console.log("Server running at http://#{host}:#{port}");

