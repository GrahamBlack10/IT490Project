<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";
require_once(__DIR__ . '/../rabbitmq/path.inc');
require_once(__DIR__ . '/../rabbitmq/get_host_info.inc');
require_once(__DIR__ . '/../rabbitmq/rabbitMQLib.inc');


// Only allow access if the user is logged in. If not, redirect to login.
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

// Handle a simple search query from the user.
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Retrieve movies
$movies = getMovies();

// If a search term is provided, filter the movies.
if ($searchQuery !== '') {
    $movies = array_filter($movies, function($movie) use ($searchQuery) {
        return stripos($movie['title'], $searchQuery) !== false;
    });
}

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
        <!-- Search/Filter Section -->
        <section class="search-filter">
            <form method="GET" action="browse.php">
                <input type="text" name="search" placeholder="Search movies..." value="<?= htmlspecialchars($searchQuery) ?>">
                <button type="submit">Search</button>
            </form>
        </section>
        
        <!-- Movies Grid Section -->
        <section class="movie-grid">
            <?php foreach($movies as $movie): ?>
            <div class="card">
                <div class="image">
                    <img src="<?= htmlspecialchars($movie['image']) ?>" alt="<?= htmlspecialchars($movie['title']) ?>">
                </div>
                <div class="caption">
                    <h3 class="movie_name"><?= htmlspecialchars($movie['title']) ?></h3>
                    <div class="rate">
                        <?php 
                        // Display star icons based on the movie rating.
                        for ($i = 0; $i < (int)$movie['rating']; $i++): 
                        ?>
                            <i class="fas fa-star"></i>
                        <?php endfor; ?>
                    </div>
                    <!-- Link to a detailed movie page (assuming you have or will create movie.php) -->
                    <a href="movie.php?id=<?= urlencode($movie['id'] ?? $movie['title']) ?>" class="details-link">More Info</a>
                </div>
            </div>
            <?php endforeach; ?>
        </section>
    </main>
</body>




