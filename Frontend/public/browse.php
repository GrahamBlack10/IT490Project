<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Only allow access if the user is logged in. If not, redirect to login.
//if (!is_logged_in()) {
  //  header("Location: login.php");
    //exit();
//}

$movies = getMovies();

// Fallback in case no movies are returned.
if (!$movies) {
    $movies = [
        [
            "title"  => "Default Movie",
            "image"  => "images/default.jpg",
            "rating" => 3
        ]
    ];
}
?>

<body>
    <main class="browse-container">
        <?php foreach($movies as $movie): ?>
        <div class="card">
            <div class="image">
                <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
            </div>
            <div class="caption">
                <div class="rate">
                    <?php 
                    // Display star icons based on the movie rating.
                    for ($i = 0; $i < (int)$movie['rating']; $i++): 
                    ?>
                        <i class="fas fa-star"></i>
                    <?php endfor; ?>
                </div>
                <p class="movie_name"><?= htmlspecialchars($movie['title']) ?></p>
            </div>
        </div>
        <?php endforeach; ?>
    </main>
</body>




