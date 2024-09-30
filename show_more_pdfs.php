<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("location: login.php");
    exit;
}
require 'partials/_dbconnect.php';
$query = isset($_GET['query']) ? htmlspecialchars($_GET['query']) : '';
$pdfs = [];

if (!empty($query)) {
    $sql = "SELECT * FROM pdfdb WHERE title LIKE '%$query%'";
    $result = $conn_search_app->query($sql);

    if ($result && $result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $pdfs[] = $row;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>More PDFs</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="#">More PDFs</a>
    <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
            <li class="nav-item">
                <a class="nav-link" href="results.php?query=<?php echo urlencode($query); ?>#pdfs">Back to Results</a>
            </li>
        </ul>
    </div>
</nav>

<div class="container mt-4">
    <h2>PDFs</h2>
    <div class="row">
        <?php if (!empty($pdfs)): ?>
            <?php foreach ($pdfs as $pdf): ?>
                <div class="col-md-4">
                    <div class="card mb-4">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($pdf['title']); ?></h5>
                            <a href="<?php echo htmlspecialchars($pdf['url']); ?>" class="btn btn-primary" target="_blank">View PDF</a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p>No PDFs found for "<?php echo htmlspecialchars($query); ?>"</p>
        <?php endif; ?>
    </div>
</div>
</body>
</html>



