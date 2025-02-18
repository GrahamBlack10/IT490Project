#!/usr/bin/php
<?php
require_once('path.inc');
require_once('get_host_info.inc');
require_once('rabbitMQLib.inc');

function requestProcessor($request)
{
  echo "received request".PHP_EOL;
  var_dump($request);
  if(!isset($request['type']))
  {
    return "ERROR: unsupported message type";
  }
  else {
    echo 'Sending to data server:' . PHP_EOL;
    $server = new rabbitMQClient("testRabbitMQListener.ini","testServer");
    $response = $server->send_request($request);
    echo $response;
    echo 'Above is the returned data' . PHP_EOL;
    return $response;
  }
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>