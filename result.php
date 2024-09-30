<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("location: login.php");
    exit;
}

require 'partials/_dbconnect.php'; 

$query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';
$images = [];
$videos = [];
$pdfs = [];
$category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : 'all';

function limit_words($text, $limit) {
    $words = explode(' ', $text);
    return (count($words) > $limit) ? implode(' ', array_slice($words, 0, $limit)) . '...' : $text;
}

$keywords = explode(' ', $query);

if (!empty($query)) {
    if ($category === 'all' || $category === 'images') {
        $sql = "SELECT * FROM images WHERE ";
        $sqlConditions = [];
        foreach ($keywords as $word) {
            $word = $conn_search_app->real_escape_string($word);
            $sqlConditions[] = "description LIKE '%$word%'";
        }
        $sql .= implode(' OR ', $sqlConditions);

        $result = $conn_search_app->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $images[] = $row;
            }
        }
    }

    if ($category === 'all' || $category === 'videos') {
        $sql = "SELECT * FROM videos WHERE ";
        $sqlConditions = [];
        foreach ($keywords as $word) {
            $word = $conn_search_app->real_escape_string($word);
            $sqlConditions[] = "title LIKE '%$word%' OR description LIKE '%$word%'";
        }
        $sql .= implode(' OR ', $sqlConditions);

        $result = $conn_search_app->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $videos[] = $row;
            }
        }
    }

    if ($category === 'all' || $category === 'pdfs') {
        $sql = "SELECT * FROM pdfdb WHERE ";
        $sqlConditions = [];
        foreach ($keywords as $word) {
            $word = $conn_search_app->real_escape_string($word);
            $sqlConditions[] = "title LIKE '%$word%'";  
        }
        $sql .= implode(' OR ', $sqlConditions);

        $result = $conn_search_app->query($sql);
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $pdfs[] = $row;
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        .navbar {
            background-color: #343a40;
        }
        .navbar .nav-link,
        .navbar .navbar-brand {
            color: #ffffff !important;
        }
        .card-container {
            display: flex;
            flex-wrap: wrap;
            overflow: visible;
        }
        .card-container .card {
            display: flex;
            flex: 0 0 25%;
            margin-bottom: 1em;
        }
        .card img {
            flex: 0 0 70%;
        }
        .card-body {
            flex: 1;
            padding: 1em;
        }
        .show-more {
            text-align: center;
            margin-top: 1em;
        }
    </style>
</head>

<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">Search Results</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="?query=<?php echo urlencode($query); ?>&category=images">Images</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?query=<?php echo urlencode($query); ?>&category=videos">Videos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?query=<?php echo urlencode($query); ?>&category=pdfs">PDFs</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="?query=<?php echo urlencode($query); ?>&category=all">All</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <?php if ($category === 'all' || $category === 'images'): ?>
        <?php if (!empty($images)): ?>
            <h2 id="images">Images</h2>
            <div class="card-container">
                <?php foreach (array_slice($images, 0, 4) as $image): ?>
                    <div class="card mb-4">
                        <img src="<?php echo htmlspecialchars($image['url']); ?>" class="card-img-top" alt="Image">
                        <div class="card-body">
                            <p class="card-text"><?php echo limit_words(htmlspecialchars($image['description']), 20); ?></p>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($images) > 4): ?>
                <div class="show-more">
                    <a href="show_more_images.php?query=<?php echo urlencode($query); ?>" class="btn btn-primary">Show More</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($category === 'all' || $category === 'videos'): ?>
        <?php if (!empty($videos)): ?>
            <h2 id="videos">Videos</h2>
            <div class="card-container">
                <?php foreach (array_slice($videos, 0, 4) as $video): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($video['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($video['description']); ?></p>
                            <a href="<?php echo htmlspecialchars($video['url']); ?>" class="btn btn-primary" target="_blank">Watch Video</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($videos) > 4): ?>
                <div class="show-more">
                    <a href="show_more_videos.php?query=<?php echo urlencode($query); ?>" class="btn btn-primary">Show More</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>

    <?php if ($category === 'all' || $category === 'pdfs'): ?>
        <?php if (!empty($pdfs)): ?>
            <h2 id="pdfs">PDFs</h2>
            <div class="card-container">
                <?php foreach (array_slice($pdfs, 0, 4) as $pdf): ?>
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($pdf['title']); ?></h5>
                            <a href="<?php echo htmlspecialchars($pdf['url']); ?>" class="btn btn-primary" target="_blank">View PDF</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <?php if (count($pdfs) > 4): ?>
                <div class="show-more">
                    <a href="show_more_pdfs.php?query=<?php echo urlencode($query); ?>" class="btn btn-primary">Show More</a>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    <?php endif; ?>
</div>

</body>
</html>
