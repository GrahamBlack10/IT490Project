<?php
require_once('../rabbitmq/path.inc');
require_once('../rabbitmq/get_host_info.inc');
require_once('../rabbitmq/rabbitMQFanout.inc');

function writeLog($logMessage, $logFile) {
    	file_put_contents($logFile, $logMessage . PHP_EOL, FILE_APPEND);
}

function requestProcessor($request){
	echo "received request".PHP_EOL;
	var_dump($request);
	if(!isset($request['type'])){
		return "ERROR: unsupported message type";
	}
        switch ($request['type']){
		case "logging":
			$logFile = 'primary.log';
			writeLog("{$request['timestamp']} {$request['source']}: {$request['message']}", $logFile);
			break;
  	}
	return array("returnCode" => '0', 'message'=>"Logging Listener received request and processed");
}

$server = new rabbitMQFanoutServer("../rabbitmq/primaryLogMQ.ini", "testServer");

echo "Log Listener Active" . PHP_EOL;
$server->process_requests('requestProcessor');
echo "Log Listener Processed Logs" . PHP_EOL;
?>