<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] != true) {
    header("location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome - <?php echo $_SESSION['username']; ?></title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <script src="https://code.jquery.com/jquery-3.4.1.min.js"></script>
    <link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
    <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js"></script>
    <style>
        body {
            background-color: #f9f9f9;
            margin: 0;
        }
        .content {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            height: calc(100vh - 56px);
            padding: 20px;
        }
        img {
            max-width: 50%;
            height: auto;
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
    </style>
</head>
<body>

<div class="content">
    <img src="ser.gif" alt="Search Image">

    <div class="search-box">
        <form action="result.php" method="get">
            <input type="text" id="search-input" name="query" placeholder="Search..." required>
            <button type="submit" class="btn btn-primary">Search</button>
        </form>
    </div>
</div>

<script>
    $(document).ready(function () {
        $('#search-input').autocomplete({
            source: function (request, response) {
                $.ajax({
                    url: 'suggest.php',
                    dataType: 'json',
                    data: {
                        term: request.term
                    },
                    success: function (data) {
                        response(data);
                    },
                    error: function (xhr, status, error) {
                        console.error('AJAX Error:', status, error);
                    }
                });
            },
            minLength: 1
        });
    });
</script>
</body>
</html>