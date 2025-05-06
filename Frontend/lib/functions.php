<?php
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

function rabbitConnect($request) {
<<<<<<< HEAD
<<<<<<< HEAD
	$fp = @fsockopen("192.168.196.26" , 5672);
=======
	$fp = @fsockopen("192.168.196.37" , 5672);
>>>>>>> 241c7811c418b11dd5b53ec8858e67edfe8841f5
=======
	$fp = @fsockopen("192.168.196.26" , 5672);
>>>>>>> f64603bf30eeb88ff9ba4bc5622f14a30de2e458

	if ($fp) {
		$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
		$response = $client->send_request($request);
		return $response;
	}

	else {
		$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/spareRabbitMQ.ini", "testServer");
		$response = $client->send_request($request);
		return $response;
	}
}

function is_logged_in() {
	$request = array();
	$request['type'] = "validate_session";
	$request['session_id'] = session_id();
	//send session ID to see if logged in

	$response = rabbitConnect($request);

	if($response === 'success') {
		$isLoggedIn = true;
	} else {
		$isLoggedIn = false;
	}
	return $isLoggedIn;
}

function getMovies() {
    
    $request = array();
    $request['type'] = 'get_movies';
    $response = rabbitConnect($request);
    return $response;
}

function getMoviesWithFilter($filter) {

	$request = array();
	$request['type'] = 'get_movies_with_filter';
	$request['filter'] = $filter;
	$response = rabbitConnect($request);
    return $response;
}

?>
