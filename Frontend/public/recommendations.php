<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

// Redirect users who are not logged in
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$request = array();
$request['type']= 'get_recommendations';
$request['session_id'] = session_id();
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);

?>

<br>
<p class="text-center">Here are some recommendations based on your favorite genre</p>

<?php
foreach($response as $movie) { ?>
    <br>
    <div class="card" style="width: 18rem;">
    <img class="card-img-top" src="https://image.tmdb.org/t/p/w500<?php echo $movie['image']?>" class="img-fluid rounded">
    <div class="card-body">
        <a href="https://www.themoviedb.org/movie/<?php echo $movie['tmdb_id']?>" class="btn btn-primary">Check it out!</a>
    </div>
    </div>
<?php
}
?>