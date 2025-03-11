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

$tmdb_id = (int)$_GET['tmdb_id'];
$request = array();
$request["type"] = "get_movie_details";
$request["movie_id"] = $tmdb_id;
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response= $client->send_request($request);

$title       = $response['title']; 
$releaseDate = $response['releaseDate'];
$summary     = $response['description'];
$image       = $response['image'];

$request = array();
$request['type'] = 'get_average_rating';
$request['movie_id'] = $tmdb_id;
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);
$averageRating = $response['average_rating']; 


if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['review'], $_POST['rating'])) {
    $request = array();
    $request['type']       = 'create_movie_review';
    $request['movie_id']   = $tmdb_id;
    $request['session_id'] = session_id();
    $request['review']     = $_POST['review'];
    $request['rating']     = $_POST['rating']; 
    $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);
    header('Location: movie.php?tmdb_id=' . $tmdb_id);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['watchlist'])) {
    $request = array();
    $request['type'] = 'add_to_watchlist';
    $request['session_id'] = session_id();
    $request['image'] = $image;
    $request['tmdb_id'] = $tmdb_id;
    $client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
    $response = $client->send_request($request);
    header('Location: movie.php?tmdb_id=' . $tmdb_id);
    exit;
}

$request = array();
$request['type']     = 'get_movie_reviews';
$request['movie_id'] = $tmdb_id;
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$reviews = $client->send_request($request);
?>

<div class="container py-5">
    <!-- Movie Details Section -->
    <form action="movie.php?tmdb_id=<?php echo $tmdb_id; ?>" method="POST">
        <div class="row mb-4">
            <div class="col-md-4">
                <img src="https://image.tmdb.org/t/p/w500<?php echo $image ?>" class="img-fluid rounded" alt="<?php echo $title ?>">
            </div>
            <div class="col-md-8">
                <h2><?php echo $title ?></h2>
                <p class="text-muted">Release Date: <?php echo $releaseDate ?></p>
                <p><strong>Average Rating:</strong> <?php echo $averageRating ?></p>
                <p><?php echo $summary ?></p>
                <input type="submit" name="watchlist" value="Add to watchlist" class="btn btn-primary">
            </div>
        </div>
    </form>
    
    <!-- Review Submission Form -->
    <div class="row mb-4">
        <div class="col-md-8 offset-md-2">
            <div class="card shadow-sm">
                <div class="card-header">Add Your Review</div>
                <div class="card-body">
                    <form action="movie.php?tmdb_id=<?php echo $tmdb_id; ?>" method="POST">
                        <div class="mb-3">
                            <label for="review" class="form-label">Review</label>
                            <textarea class="form-control" name="review" id="review" rows="3"></textarea>
                        </div>
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select class="form-select" name="rating" id="rating">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Reviews Section -->
    <div class="row mb-5">
        <div class="col-md-8 offset-md-2">
            <h3>User Reviews</h3>
            <?php if (!empty($reviews)) : ?>
                <?php foreach($reviews as $review) : ?>
                    <div class="card mb-3">
                        <div class="card-body">
                            <h6 class="card-subtitle mb-2 text-muted"><?php echo $review['user'] ?></h6>
                            <p class="card-text"><?php echo $review['review'] ?></p>
                            <p class="card-text">
                                <small class="text-muted">
                                    Rating: <?php echo $review['rating'] ?> | <?php echo $review['created'] ?>
                                </small>
                            </p>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p>No reviews yet. Be the first to review!</p>
            <?php endif; ?>
        </div>
    </div>
</div>
