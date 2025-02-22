<?php
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

echo "Testing to see if I can get Username and User ID: " . PHP_EOL;

$session_key = session_id();
echo $session_key;

$request = array();
$request['type'] = 'get_user_id';
$request['session_id'] = $session_key;
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);
echo "User ID: $response";

$request = array();
$request['type'] = 'get_username';
$request['session_id'] = $session_key;
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);
echo "Username: $response";
?>


