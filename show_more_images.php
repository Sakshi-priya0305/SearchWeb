<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("location: login.php");
    exit;
}
require 'partials/_dbconnect.php';

$query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';
$images = [];

if (!empty($query)) {
    $sql = "SELECT * FROM images WHERE description LIKE '%$query%'";
    $result = $conn_search_app->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $images[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>More Images</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        .card-img-top {
            object-fit: cover;
            height: 200px; 
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">More Images</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="results.php?query=<?php echo urlencode($query); ?>#images">Back to Results</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2>Images</h2>
    <div class="row">
        <?php if (!empty($images)): ?>
            <?php foreach ($images as $image): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <img src="<?php echo htmlspecialchars($image['url']); ?>" class="card-img-top" alt="Image">
                        <div class="card-body">
                            <p class="card-text"><?php echo htmlspecialchars($image['description']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No images found for "<?php echo htmlspecialchars($query); ?>"</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>

