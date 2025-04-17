<?php

require_once(__DIR__ . '/rabbitmq/path.inc');
require_once(__DIR__ . '/rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/rabbitmq/rabbitMQLib.inc');

$api_key = "86a1bb882411e830da6e1187379aa81d";

function rabbitConnect($request) {
	$fp = @fsockopen("192.168.196.26" , 5672);

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

// Function to fetch genres from TMDB and return an associative array [id => name]
function getGenresFromTMDB($api_key) {

    $response = rabbitConnect($request);

    $url = "https://api.themoviedb.org/3/genre/movie/list?api_key={$api_key}&language=en-US";
    
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 30,
    ]);

    $response = curl_exec($ch);
    curl_close($ch);
    
    $data = json_decode($response, true);
    $genreMap = [];

    if (isset($data['genres']) && is_array($data['genres'])) {
        foreach ($data['genres'] as $genre) {
            $genreMap[$genre['id']] = $genre['name']; // Map ID to name
        }
    }

    return $genreMap;
}

// Fetch genres and map them
$genreMap = getGenresFromTMDB($api_key);

// Fetch upcoming movies
$url = "https://api.themoviedb.org/3/movie/upcoming?language=en-US&page=1&api_key=$api_key"; 
$curl = curl_init();

curl_setopt_array($curl, [
    CURLOPT_URL => $url,
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_TIMEOUT => 30,
]);

$response = curl_exec($curl);
$err = curl_error($curl);
curl_close($curl);

if ($err) {
    echo "cURL Error #:" . $err . PHP_EOL;
} else {
    // Decode the JSON response
    $data = json_decode($response, true);

    if (isset($data['results']) && is_array($data['results']) && count($data['results']) > 0) {
        $moviesList = [];

        foreach ($data['results'] as $movie) {
            // Convert genre IDs to readable names
            $genreNames = [];
            if (isset($movie['genre_ids']) && is_array($movie['genre_ids'])) {
                foreach ($movie['genre_ids'] as $genre_id) {
                    if (isset($genreMap[$genre_id])) {
                        $genreNames[] = $genreMap[$genre_id]; // Store genre name
                    }
                }
            }

            $moviesList[] = [
                "Title" => $movie['title'] ?? 'N/A',
                "Release Date" => $movie['release_date'] ?? 'N/A',
                "Genres" => !empty($genreNames) ? implode(", ", $genreNames) : "Unknown" // Ensure proper formatting
            ];
        }

        // Send data to RabbitMQ server
        $request = [
            "type" => "populate_database",
            "data" => $data
        ];

        $client = new rabbitMQClient(__DIR__ . "/rabbitmq/testRabbitMQ.ini", "testServer");
        $rabbitResponse = $client->send_request($request);

        // Display RabbitMQ response
        echo "RabbitMQ Response:\n";
        print_r($rabbitResponse);
        echo "\n";

        // Print formatted output for CLI
        echo "Upcoming Movies (TMDb):\n";
        foreach ($moviesList as $movie) {
            echo "------------------------------------\n";
            echo "Title: " . $movie["Title"] . "\n";
            echo "Release Date: " . $movie["Release Date"] . "\n";
            echo "Genres: " . $movie["Genres"] . "\n";
        }
        echo "------------------------------------\n";

    } else {
        echo "No upcoming movies found in API response.\n";
    }
    
}

if ($fp = @fsockopen("192.168.196.26" , 5672)) {
    echo "192.168.196.86 is reachable!";
}

else {
    echo "Cannot reach 192.168.196.26...\n";
}

if ($fp = @fsockopen("192.168.196.138" , 5672)) {
    echo "192.168.196.229 is reachable!";
}

else {
    echo "Cannot reach 192.168.196.138...\n";
}

?>
