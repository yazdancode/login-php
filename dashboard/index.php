<?php
include_once '../assets/controller/methods.php';

session_start();  // حتما باید session رو شروع کنید تا از $_SESSION استفاده کنید

// بررسی وجود توکن جلسه برای احراز هویت
if (!isset($_SESSION['token'])) {
    header('Location: ../index.php');  // دقت کن: بین header و مقدارش نباید فاصله باشه، 'Location: ...'
    exit();  // بعد از ریدایرکت بهتره همیشه exit بذارید
}

// بررسی وجود پارامتر logout در URL
if (isset($_GET['logout'])) {
    logout();
}
?>


<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Panel</title>
</head>
<body>
<h1>Welcome to your panel!</h1>
<hr>
<p>Username: <?php echo  $_SESSION['username']?></p>
<a href="<?php echo htmlspecialchars('index.php'); ?>">
    <button>
        logout
    </button>
</a>
</body>
</html>