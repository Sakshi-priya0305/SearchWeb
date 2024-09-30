<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Web</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f9f9f9;
            margin: 0;
        }
        .navbar {
            background-color: #343a40;
        }
        .navbar .nav-link,
        .navbar .navbar-brand {
            color: #ffffff !important;
        }
        .content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 56px); 
            padding: 20px;
        }
        .search-box {
            width: 100%;
            max-width: 600px;
            margin-top: 20px;
        }
        .search-box form {
            display: flex;
            justify-content: center;
            align-items: center;
            width: 100%;
        }
        .search-box input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 4px 0 0 4px;
            font-size: 16px;
        }
        .search-box button {
            padding: 10px 20px;
            border: 1px solid #007bff;
            border-radius: 0 4px 4px 0;
            background-color: #007bff;
            color: white;
            cursor: pointer;
            font-size: 16px;
        }
        .blank-img {
            background-color: #e9ecef;
            width: 100%;
            height: 200px;
        }
    </style>
</head>
<body>

<nav class="navbar navbar-expand-lg navbar-dark">
    <a class="navbar-brand" href="index.php">Home</a>
    <div class="collapse navbar-collapse">
        <ul class="navbar-nav">
            <li class="nav-item">
                <a class="nav-link" href="login.php">Login</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="signup.php">Signup</a>
            </li>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <li class="nav-item">
                    <a class="nav-link" href="logout.php">Logout</a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</nav>

<div class="content">
    <h1>Welcome to Search Web</h1>
    <div class="search-box">
        <form action="result2.php" method="get">
            <input type="text" id="search-input" name="query" placeholder="Search..." required>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>
