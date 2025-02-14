<?php

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