<?php

require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQFanout.inc');

function rabbitFanoutConnect($request){	
	$client = new rabbitMQClient("primaryLogMQ.ini","testServer");
	$response = $client->send_request($request);
	return $response;
}
?>