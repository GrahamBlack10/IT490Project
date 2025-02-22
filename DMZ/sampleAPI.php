<?php

require_once(__DIR__ . '/rabbitmq/path.inc');
require_once(__DIR__ . '/rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/rabbitmq/rabbitMQLib.inc');

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "http://www.omdbapi.com/?i=tt3896198&apikey=6c8db619",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    // Decode the JSON response
    $data = json_decode($response, true);
    
   
    // Send this data to the server for processing
    $request = [
        "type" => "populate_database",
        "data" => $data
    ];

    // Debug: Output the request array to ensure it's populated correctly
    echo "<pre>Sending request to RabbitMQ:</pre>";
    var_dump($request);

    // Establish connection to the RabbitMQ broker and send the request
    $client = new rabbitMQClient(__DIR__ . "/rabbitmq/testRabbitMQ.ini", "testServer");
    $rabbitResponse = $client->send_request($request);

    // Output the response received from the RabbitMQ server
    echo "<pre>RabbitMQ Response: ";
    print_r($rabbitResponse);
    echo "</pre>";

    // Display the decoded JSON data in a readable format
    echo "<pre>Decoded JSON Data:";
    print_r($data);
    echo "</pre>";

    // Extract and display specific movie details
    echo "<h2>Most Popular Movies (TMDb):</h2>";
    
        echo "Title: " . htmlspecialchars($data['Title']) . "<br>";
        echo "Release Date: " . htmlspecialchars($data['Released']) . "<br>";
    
}
?>