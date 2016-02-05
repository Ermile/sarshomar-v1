http		= require 'http'
querystring	= require 'querystring'

postData = querystring.stringify 
  chat_id 	: '58164083'
  text 		: 'text'

options = 
	hostname: 'sarshomar.dev',
	port: 80,
	path: '/bot142711391:AAFH0ULw7BzwdmmiZHv2thKQj7ibb49DJ44/sendMessage',
	method: 'GET',
	headers:
		'Content-Type': 'application/x-www-form-urlencoded',
		'Content-Length': postData.length

request = http.request options, (response) ->
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