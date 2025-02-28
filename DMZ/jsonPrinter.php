<?php

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
    echo "cURL Error #:" . $err;
} else {
    // Decode the JSON response
    $data = json_decode($response, true);

    // Check if the movie meets the filter criteria

        // Print the filtered data
        echo "<pre>";
        echo json_encode($data, JSON_PRETTY_PRINT);
        echo "</pre>";
}

?>

