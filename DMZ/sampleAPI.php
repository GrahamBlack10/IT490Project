<?php

require_once(__DIR__ . '/rabbitmq/path.inc');
require_once(__DIR__ . '/rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/rabbitmq/rabbitMQLib.inc');

$api_key = "86a1bb882411e830da6e1187379aa81d";
$url = "https://api.themoviedb.org/3/movie/latest?api_key=$api_key"; 

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if the required fields exist and are not NULL
    if (
        isset($data['imdb_id']) && $data['imdb_id'] !== NULL &&
        isset($data['poster_path']) && $data['poster_path'] !== NULL &&
        isset($data['title']) && !empty($data['title']) &&
        isset($data['overview']) && !empty($data['overview']) &&
        isset($data['release_date']) && !empty($data['release_date'])
    ) {
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
        echo "Title: " . htmlspecialchars($data['title']) . "<br>";
        echo "Release Date: " . htmlspecialchars($data['release_date']) . "<br>";
    } else {
        // Handle invalid data: missing required fields
        echo "<pre>Invalid movie data received. Skipping...</pre>";
    }
}

?>
