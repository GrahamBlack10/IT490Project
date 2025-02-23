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

// Process search query if provided
$searchQuery = isset($_GET['search']) ? trim($_GET['search']) : '';

// Retrieve movies (this function will later connect to your database/API)
$movies = getMovies();

// Filter movies based on the search query
if ($searchQuery !== '') {
    $movies = array_filter($movies, function($movie) use ($searchQuery) {
        return stripos($movie['title'], $searchQuery) !== false;
    });
}

// Fallback: if no movies are found, provide a default card
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

<!--CSS For the Browse Page-->
<style>
    .browse-container {
        padding: 20px;
        background-color: #f4f4f4;
        min-height: 100vh;
    }
    .search-filter {
        margin-bottom: 20px;
        text-align: center;
    }
    .search-filter form {
        display: inline-block;
        width: 100%;
        max-width: 600px;
    }
    .search-filter input[type="text"] {
        width: 70%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        font-size: 1em;
    }
    .search-filter button {
        padding: 10px 20px;
        background-color: #e50914;
        border: none;
        color: #fff;
        cursor: pointer;
        border-radius: 4px;
        font-size: 1em;
    }
    .movie-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 20px;
    }
    .card {
        background-color: #fff;
        border-radius: 8px;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        overflow: hidden;
        transition: transform 0.3s;
    }
    .card:hover {
        transform: translateY(-5px);
    }
    .card .image img {
        width: 100%;
        height: auto;
        display: block;
    }
    .caption {
        padding: 10px 15px;
    }
    .movie_name {
        font-size: 1.1em;
        margin: 10px 0 5px;
        color: #333;
    }
    .rate {
        color: #FFD700; /* Gold color for stars */
    }
    .details-link {
        display: inline-block;
        margin-top: 10px;
        text-decoration: none;
        color: #e50914;
        font-weight: bold;
    }
</style>

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
                        <!-- Link to a detailed movie page (to be developed later) -->
                        <a href="movie.php?id=<?= urlencode($movie['id'] ?? $movie['title']) ?>" class="details-link">More Info</a>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    </main>
</body>




