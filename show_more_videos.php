<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("location: login.php");
    exit;
}
require 'partials/_dbconnect.php';
$query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';
$videos = [];
function convertToEmbedUrl($url) {
    if (strpos($url, 'watch?v=') !== false) {
        return str_replace("watch?v=", "embed/", $url);
    }
    return $url;
}

if (!empty($query)) {
    $sql = "SELECT * FROM videos WHERE title LIKE '%$query%' OR description LIKE '%$query%' LIMIT 15"; 
    $result = $conn_search_app->query($sql); 
    

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $videos[] = [
                'title' => $row['title'],
                'description' => $row['description'],
                'videoUrl' => convertToEmbedUrl($row['url']) 
            ];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>More Videos</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">More Videos</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="result.php?query=<?php echo urlencode($query); ?>#videos">Back to Results</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2>Videos</h2>
    <div class="row">
        <?php if (!empty($videos)): ?>
            <?php foreach ($videos as $video): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <iframe width="100%" height="315" src="<?php echo htmlspecialchars($video['videoUrl']); ?>" frameborder="0" allowfullscreen></iframe>
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($video['title']); ?></h5>
                            <p class="card-text"><?php echo htmlspecialchars($video['description']); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No videos found for "<?php echo htmlspecialchars($query); ?>"</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>



