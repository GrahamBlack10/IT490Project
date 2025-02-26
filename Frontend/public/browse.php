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

// Process search query (for design, filtering is optional)
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Retrieve movies (using the static function for now)
$movies = getMovies();

// Filter movies if a search term is provided
if ($searchQuery !== '') {
    $movies = array_filter($movies, function($movie) use ($searchQuery) {
        return stripos($movie['title'], $searchQuery) !== false;
    });
}

// Fallback: if no movies are returned, display a default card
if (!$movies) {
    $movies = [
        [
            "title"  => "No movies found",
            "image"  => "https://via.placeholder.com/300x450?text=No+Image",
            "rating" => 0,
            "id"     => "none"
        ]
    ];
}
?>

<body>
    <div class="container py-5">
        <!-- Search/Filter Section -->
        <div class="row mb-4">
            <div class="col-md-8 offset-md-2">
                <form method="GET" action="browse.php" class="input-group">
                    <input type="text" name="search" class="form-control" placeholder="Search movies..." value="<?= htmlspecialchars($searchQuery) ?>">
                    <button class="btn btn-danger" type="submit">Search</button>
                </form>
            </div>
        </div>
        
        <!-- Movies Grid Section -->
        <div class="row">
            <?php foreach($movies as $movie): ?>
                <div class="col-md-4 mb-4">
                    <div class="card h-100">
                        <img src="<?= htmlspecialchars($movie['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($movie['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($movie['title']) ?></h5>
                            <p class="card-text">
                                <?php for ($i = 0; $i < (int)$movie['rating']; $i++): ?>
                                    <i class="fas fa-star text-warning"></i>
                                <?php endfor; ?>
                            </p>
                        </div>
                        <div class="card-footer text-center">
                            <a href="movie.php?id=<?= urlencode($movie['id'] ?? $movie['title']) ?>" class="btn btn-outline-danger btn-sm">More Info</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>



