<?php 
include __DIR__ . "/../partials/header.php";
include __DIR__ . "/../partials/nav.php"; 
include __DIR__ . "/../lib/functions.php";



// Redirect users who are not logged in
if (!is_logged_in()) {
    header("Location: login.php");
    exit();
}

$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Retrieve movies using your getMovies() function
if (!isset($_GET['filter'])) {
    $movies = getMovies();
}

else if (isset($_GET['filter'])){
    $movies = getMoviesWithFilter($_GET['filter']);
}

// Filter movies if a search term is provided

if ($searchQuery !== '') {
    $movies = array_filter($movies, function($movie) use ($searchQuery) {
        return stripos($movie['title'], $searchQuery) !== false;
    });
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
                        <img src="https://image.tmdb.org/t/p/w500<?= htmlspecialchars($movie['image']) ?>" class="card-img-top" alt="<?= htmlspecialchars($movie['title']) ?>">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($movie['title']) ?></h5>
                            <p class="card-text">Release Date: <?= htmlspecialchars($movie['releaseDate']) ?></p>
                        </div>
                        <div class="card-footer text-center">
                            <a href="movie.php?tmdb_id=<?= urlencode($movie['tmdb_id']) ?>" class="btn btn-outline-danger btn-sm">More Info</a>
                        </div>

                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</body>


