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
    echo 'Request sent to data server' . PHP_EOL;
    echo 'Waiting for response...' . PHP_EOL;
    $serverListener = new rabbitMQClient("testRabbitMQListener.ini","testServer");
    $response = $serverListener->send_request($request);
    echo 'Response: ' . $response . PHP_EOL;
    echo 'Returning response to client' . PHP_EOL;
    echo "-------------------" . PHP_EOL;
    return $response;
  }
}

$server = new rabbitMQServer("testRabbitMQ.ini","testServer");

echo "testRabbitMQServer BEGIN".PHP_EOL;
$server->process_requests('requestProcessor');
echo "testRabbitMQServer END".PHP_EOL;
exit();
?>