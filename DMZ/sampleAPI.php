<?php
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => "https://imdb236.p.rapidapi.com/imdb/most-popular-movies",
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_ENCODING => "",
    CURLOPT_MAXREDIRS => 10,
    CURLOPT_TIMEOUT => 30,
    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
    CURLOPT_CUSTOMREQUEST => "GET",
    CURLOPT_HTTPHEADER => [
        "x-rapidapi-host: imdb236.p.rapidapi.com",
        "x-rapidapi-key: 7ef8cd4381msh4f040364062547dp18b9cfjsnf003671460e7"
    ],
]);

$response = curl_exec($curl);
$err = curl_error($curl);

curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err;
} else {
    // Decode JSON response
    $data = json_decode($response, true);

    // Send this data to the server for processing 
    $request = array();
    $request["type"] = "populate_database";
    $request["data"] = $data;
    // Check to make sure that array is populated. Not 100% sure if this will work 
    var_dump($request);
    // Establish connection to broker then send request and get a response
    $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);
    // Gonna either be success or failure 
    echo $response;

    // Display the decoded JSON in a readable format
    echo "<pre>";
    print_r($data);
    echo "</pre>";

    // Optional: Extract specific movie details
    if (isset($data['movies'])) {
        echo "<h2>Most Popular Movies:</h2>";
        foreach ($data['movies'] as $movie) {
            echo "Title: " . $movie['title'] . "<br>";
            echo "Year: " . $movie['year'] . "<br>";
            echo "IMDb ID: " . $movie['imdb_id'] . "<br><br>";
        }
    } else {
        echo "No movie data found.";
    }
}
?>