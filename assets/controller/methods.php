<?php
include __DIR__ . '/../StatusCodes/status.php';
include 'config.php';

$errorMessage = '';

function validate($data): string
{
    $data = trim($data);
    $data = stripslashes($data);
    return htmlspecialchars($data);
}

function getPDOConnection(): PDO
{
    try {
        $dsn = "mysql:host=" . DB_SERVER . ";dbname=" . DB_NAME . ";charset=utf8";
        $pdo = new PDO($dsn, DB_USER, DB_PASS);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        return $pdo;
    } catch (PDOException $e) {
        die('Connection failed: ' . $e->getMessage());
    }
}

function checkUsername(string $username): bool
{
    $username = validate($username);
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT id FROM accounts WHERE username = ?");
    $stmt->execute([$username]);
    return $stmt->fetchColumn() !== false;
}

function checkEmail(string $email): bool
{
    $email = validate($email);
    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT id FROM accounts WHERE email = ?");
    $stmt->execute([$email]);
    return $stmt->fetchColumn() !== false;
}

function checkphone($phone): bool
{
    $number = toEn($phone);
    $number = preg_replace('/[\s\-()]/', '', $number);
    $pattern = '/^(0|\+98)?9\d{9}$/';

    if (preg_match($pattern, $number)) {
        return true;
    }
    return false;
}

function toEn(string $string): string
{
    $persian = ['۰', '۱', '۲', '۳', '۴', '۵', '۶', '۷', '۸', '۹'];
    $num = range(0, 9);

    return str_replace($persian, $num, $string);
}

function isPhoneExists($phone): bool
{
    $number = toEn($phone);
    $number = preg_replace('/[\s\-()]/', '', $number);

    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT id FROM accounts WHERE phone = ?");
    $stmt->execute([$number]);
    return $stmt->fetchColumn() !== false;
}

function createAccount(string $username, string $phone, string $email, string $password): bool
{
    $pdo = getPDOConnection();
    $hashedPassword = password_hash($password, PASSWORD_BCRYPT);

    $stmt = $pdo->prepare("INSERT INTO accounts (username, phone, email, password) VALUES (?, ?, ?, ?)");
    return $stmt->execute([$username, $phone, $email, $hashedPassword]);
}

function signup(array $data): bool
{
    global $errorMessage;

    $username = validate($data['username'] ?? '');
    $phone = validate($data['phone'] ?? '');
    $email = validate($data['email'] ?? '');
    $password = validate($data['password'] ?? '');
    $confirm_password = validate($data['confirm_password'] ?? '');

    // چک پر بودن فیلدها
    if (empty($username) || empty($phone) || empty($email) || empty($password) || empty($confirm_password)) {
        HttpStatus::setStatus(HttpStatus::BAD_REQUEST);
        $errorMessage = 'لطفاً همه فیلدها را پر کنید';
        return false;
    }

    // چک تطابق رمز
    if ($password !== $confirm_password) {
        HttpStatus::setStatus(HttpStatus::BAD_REQUEST);
        $errorMessage = 'رمز عبور و تکرار آن مطابقت ندارند';
        return false;
    }

    // چک ایمیل معتبر
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        HttpStatus::setStatus(HttpStatus::BAD_REQUEST);
        $errorMessage = 'ایمیل وارد شده معتبر نیست';
        return false;
    }

    // چک تکراری نبودن نام کاربری
    if (checkUsername($username)) {
        HttpStatus::setStatus(HttpStatus::Conflict);
        $errorMessage = 'این نام کاربری قبلاً ثبت شده است';
        return false;
    }

    // چک تکراری نبودن ایمیل
    if (checkEmail($email)) {
        HttpStatus::setStatus(HttpStatus::Conflict);
        $errorMessage = 'این ایمیل قبلاً ثبت شده است';
        return false;
    }

    // چک شماره تلفن معتبر بودن
    if (!checkphone($phone)) {
        HttpStatus::setStatus(HttpStatus::BAD_REQUEST);
        $errorMessage = 'شماره تلفن وارد شده معتبر نیست.';
        return false;
    }

    // چک تکراری نبودن شماره تلفن
    if (isPhoneExists($phone)) {
        HttpStatus::setStatus(HttpStatus::Conflict);
        $errorMessage = 'این شماره تلفن قبلاً ثبت شده است';
        return false;
    }

    // ذخیره کاربر
    if (createAccount($username, $phone, $email, $password)) {
        HttpStatus::setStatus(HttpStatus::Created);
        return true;
    } else {
        HttpStatus::setStatus(HttpStatus::INTERNAL_SERVER_ERROR);
        $errorMessage = 'خطا در ثبت نام کاربر';
        return false;
    }
}

function login(array $data): bool {
    global $errorMessage;
    $email = validate($data['email'] ?? '');
    $password = validate($data['pswd'] ?? '');

    if (empty($email) || empty($password)) {
        HttpStatus::setStatus(HttpStatus::BAD_REQUEST);
        $errorMessage = 'ایمیل و رمز عبور باید وارد شوند.';
        return false;
    }

    if (!checkEmail($email)) {
        HttpStatus::setStatus(HttpStatus::UNAUTHORIZED);
        $errorMessage = 'ایمیل یا رمز عبور اشتباه است.';
        return false;
    }

    $pdo = getPDOConnection();
    $stmt = $pdo->prepare("SELECT password, username FROM accounts WHERE email = ?");
    $stmt->execute([$email]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row || !password_verify($password, $row['password'])) {
        HttpStatus::setStatus(HttpStatus::UNAUTHORIZED);
        $errorMessage = 'ایمیل یا رمز عبور اشتباه است.';
        return false;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $_SESSION['username'] = $row['username'];
    $_SESSION['token'] = md5($row['username']);

    header('Location: dashboard/index.php');
    exit;
}


function logout()
{
    session_unset();
    session_destroy();

    // حذف کوکی سشن هم بهتره
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }

    header('Location: ../login.php');
    exit;
}


//function forgot_password()
//{
//    $errorMessage = '';
//    $successMessage = '';
//    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
//        $email = $_POST['email'] ?? '';
//        if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
//            if (checkEmail($email)) {
//                $token = bin2hex(random_bytes(16));
//                $pdo = getPDOConnection();
//                $stmt = $pdo->prepare("INSERT INTO accounts (email, token, created_at) VALUES (?, ?, NOW())");
//                $stmt->execute([$email, $token]);
//                sendPasswordResetEmail($email, $token);
//            }
//        }
//    }
//
//}




