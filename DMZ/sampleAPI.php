<?php

require_once(__DIR__ . '/rabbitmq/path.inc');
require_once(__DIR__ . '/rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/rabbitmq/rabbitMQLib.inc');

$api_key = "86a1bb882411e830da6e1187379aa81d";
$url = "https://api.themoviedb.org/3/movie/upcoming?language=en-US&page=1&api_key=$api_key"; 

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
    echo "cURL Error #:" . $err . PHP_EOL;
} else {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if 'results' key exists and is an array
    if (isset($data['results']) && is_array($data['results']) && count($data['results']) > 0) {
        // Send this data to the server for processing
        $request = [
            "type" => "populate_database",
            "data" => $data
        ];

        // Establish connection to the RabbitMQ broker and send the request
        $client = new rabbitMQClient(__DIR__ . "/rabbitmq/testRabbitMQ.ini", "testServer");
        $rabbitResponse = $client->send_request($request);

        // Display RabbitMQ response
        echo "RabbitMQ Response:\n";
        print_r($rabbitResponse);
        echo "\n";

        // Organize the movies into an array
        $moviesList = [];
        foreach ($data['results'] as $movie) {
            $moviesList[] = [
                "Title" => $movie['title'] ?? 'N/A',
                "Release Date" => $movie['release_date'] ?? 'N/A'
            ];
        }

        // Print formatted output for CLI
        echo "Upcoming Movies (TMDb):\n";
        foreach ($moviesList as $movie) {
            echo "------------------------------------\n";
            echo "Title: " . $movie["Title"] . "\n";
            echo "Release Date: " . $movie["Release Date"] . "\n";
        }
        echo "------------------------------------\n";

    } else {
        echo "No upcoming movies found in API response.\n";
    }
}

?>
