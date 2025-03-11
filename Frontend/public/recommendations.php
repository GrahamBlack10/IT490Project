<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');


if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$request = array();
$request['type'] = 'get_recommendations';
$request['session_id'] = session_id();
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);
?>

<div class="container py-5">
    <h3 class="text-center mb-4">Here are some recommendations based on your favorite genre</h3>
    <div class="row">
        <?php foreach($response as $movie): ?>
            <div class="col-md-3 mb-4">
                <div class="card h-100">
                    <img src="https://image.tmdb.org/t/p/w500<?php echo $movie['image']; ?>" class="card-img-top img-fluid rounded" alt="<?php echo $movie['title']; ?>">
                    <div class="card-body text-center">
                        <a href="https://www.themoviedb.org/movie/<?php echo $movie['tmdb_id']; ?>" class="btn btn-primary">Check it out!</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>
