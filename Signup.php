<?php
session_start();
$showAlert = false;
$showError = false;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    include 'partials/_dbconnect.php';
    $username = $_POST["username"];
    $password = $_POST["password"];
    $cpassword = $_POST["cpassword"];
    $captcha = $_POST["captcha"];

    if ($captcha != $_SESSION['captcha']) {
        $showError = "The CAPTCHA answer is incorrect.";
    } else {
        $exists = false;
        $sql = "SELECT * FROM `users` WHERE `username` = '$username'";
        $result = mysqli_query($conn_users, $sql);
        if (mysqli_num_rows($result) > 0) {
            $exists = true;
        }

        if (($password == $cpassword) && !$exists) {
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "INSERT INTO `users` (`username`, `password`, `dt`) VALUES ('$username', '$hashed_password', current_timestamp())";
            $result = mysqli_query($conn_users, $sql);
            if ($result) {
                $showAlert = true;
                header("location: login.php");
                exit();
            }
        } else {
            if ($exists) {
                $showError = "Username already exists.";
            } else {
                $showError = "Passwords do not match.";
            }
        }
    }
}
$num1 = rand(1, 10);
$num2 = rand(1, 10);
$_SESSION['captcha'] = $num1 + $num2;
?>

<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css">
    <title>SignUp</title>
</head>
<body>
    <?php require 'partials/_nav.php' ?>
    <?php
    if ($showAlert) {
        echo ' <div class="alert alert-success alert-dismissible fade show" role="alert">
            <strong>Success!</strong> Your account has been created, you are being redirected to login.
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>';
    }
    if ($showError) {
        echo ' <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>Error!</strong> ' . $showError . '
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">×</span>
            </button>
        </div>';
    }
    ?>

    <div class="container my-4">
        <h1 class="text-center">Signup to our website</h1>
        <form action="/searchweb/Signup.php" method="post">
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" class="form-control" id="username" name="username" aria-describedby="emailHelp" required>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="cpassword">Confirm Password</label>
                <input type="password" class="form-control" id="cpassword" name="cpassword" required>
                <small id="emailHelp" class="form-text text-muted">Make sure to type the same password</small>
            </div>
            <div class="form-group">
                <label for="captcha">What is <?php echo $num1; ?> + <?php echo $num2; ?>?</label>
                <input type="text" class="form-control" id="captcha" name="captcha" required>
            </div>
            <button type="submit" class="btn btn-primary">SignUp</button>
        </form>

        <div class="mt-4 text-center">
            <p>If you are already a user, click the button below to log in:</p>
            <a href="login.php" class="btn btn-secondary">Login</a>
        </div>
    </div>
    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js"></script>
</body>
</html>

