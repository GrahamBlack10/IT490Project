<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";

if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}


$request = array();
$request['type'] = 'get_favorite_genre';
$request['session_id'] = session_id();
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);
if ($response === 'No genre found') {
    $output = 'No genre selected!';
} else {
    $output = $response['genre'];
}


if (isset($_POST["genre"])) {
    $request = array();
    $request["type"] = "update_favorite_genre";
    $request["genre"] = $_POST["genre"];
    $request["session_id"] = session_id();
    $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);
    header('Location: profile.php');
    exit;
}

$request = array();
$request['type'] = 'get_watchlist';
$request['session_id'] = session_id();
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$watchlist = $client->send_request($request);
?>

<div class="container py-5">
    <!-- Favorite Genre Section -->
    <div class="row mb-5">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    Favorite Genre
                </div>
                <div class="card-body">
                    <p>Your favorite genre is: <strong><?php echo $output; ?></strong></p>
                    <form action="profile.php" method="POST">
                        <div class="mb-3">
                            <label for="genre" class="form-label">Update your favorite genre:</label>
                            <select class="form-select" name="genre" id="genre">
                                <option>Adventure</option>
                                <option>Fantasy</option>
                                <option>Drama</option>
                                <option>Horror</option>
                                <option>Comedy</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Update Genre</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Watchlist Section -->
    <div class="row mb-5">
        <div class="col-12">
            <h4 class="text-center mb-4">Your Watchlist</h4>
            <div class="row">
                <?php if (!empty($watchlist)): ?>
                    <?php foreach ($watchlist as $movie): ?>
                        <div class="col-md-3 mb-4">
                            <div class="card h-100">
                                <img class="card-img-top" src="https://image.tmdb.org/t/p/w500<?php echo $movie['image'] ?>" alt="<?php echo $movie['title'] ?>">
                                <div class="card-body text-center">
                                    <h6 class="card-title"><?php echo $movie['title'] ?></h6>
                                </div>
                                <div class="card-footer text-center">
                        <!--Button to be worked on Monday--><a href="" class="btn btn-primary">Details</a> 
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-center">No movies in your watchlist.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
