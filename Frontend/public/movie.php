<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');

if (!is_logged_in()) {
    die(header("Location: login.php"));
}

$tmdb_id = (int)$_GET['tmdb_id'];
$request = array();
$request["type"] = "get_movie_details";
$request["movie_id"] = $tmdb_id;
$client = new rabbitMQClient(__DIR__ . "/../rabbitmq/testRabbitMQ.ini", "testServer");
$response = $client->send_request($request);

$title = $response['title']; 
$releaseDate = $response['releaseDate'];
$summary = $response['description'];
//$image = substr($response['image'], 1);
$image = $response['image'];
//Check to see if the image path is correct
//$average_rating;
?>

<div class="card" style="width: 18rem;">
  <img class="card-img-top" src="https://image.tmdb.org/t/p/w500<?php echo $image ?>" alt="Card image cap">
  <div class="card-body">
    <h5 class="card-title"><?php echo $title ?></h5>
    <p class="card-text"><?php echo $releaseDate ?></p>
    <p class="card-text"><?php echo $summary ?></p>
  </div>
</div>

<form action="movie.php?id=<?php echo $tmdb_id; ?>" method="POST">
    <div class="input-group">
        <div class="input-group-prepend">
            <span class="input-group-text">Review</span>
        </div>
    <textarea class="form-control" name="review" id="review"></textarea>
    </div>

    <div class="form-group">
    <label for="rating">Rating</label>
    <select class="form-control" name="rating" id="rating">
      <option>1</option>
      <option>2</option>
      <option>3</option>
      <option>4</option>
      <option>5</option>
    </select>
    </div>

    <button type="submit" class="btn btn-primary">Add review</button>
</form>