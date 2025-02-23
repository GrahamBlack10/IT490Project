<?php
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

function is_logged_in() {
	$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini","testServer");
	$request = array();
	$request['type'] = "validate_session";
	$request['session_id'] = session_id();
	//send session ID to see if logged in

	$response = $client->send_request($request);

	if($response === 'success') {
		$isLoggedIn = true;
	} else {
		$isLoggedIn = false;
	}
	return $isLoggedIn;
}
function getMovies() {
    // Sample movie data; Will get replaced with Database Data
    return [
        ['title' => 'Movie One', 'image' => 'images/movie1.jpg', 'rating' => 4],
        ['title' => 'Movie Two', 'image' => 'images/movie2.jpg', 'rating' => 5],
        ['title' => 'Movie Three', 'image' => 'images/movie3.jpg', 'rating' => 3],
    ];
}

?>
