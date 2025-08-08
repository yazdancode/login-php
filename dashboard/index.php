<?php

include_once __DIR__ . '/../assets/controller/methods.php';


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// چک احراز هویت
if (!isset($_SESSION['token'])) {
    header('Location: ../index.php');
    exit;
}

// خروج
if (isset($_GET['logout'])) {
    logout(); // تابع logout باید با exit تمام بشه
}
?>
<!doctype html>
<html lang="fa">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>پنل کاربری</title>
</head>
<body>
<h1>به پنل کاربری خوش آمدید!</h1>
<hr>
<p>نام کاربری: <?php echo htmlspecialchars($_SESSION['username']); ?></p>
<a href="?logout=1">
    <button>خروج</button>
</a>
</body>
</html>
