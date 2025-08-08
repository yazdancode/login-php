<?php

include_once 'assets/controller/methods.php';

$errorMessage = '';

if (isset($_POST['signup_btn'])) {
    $data = $_POST['frm'] ?? [];
    $result = signup($data);

}

if (isset($_POST['login_btn'])) {
    if (!empty($_POST['email']) && !empty($_POST['pswd'])) {
        $email = $_POST['email'];
        $password = $_POST['pswd'];
        echo "Login: Email: $email, Password: $password";
    }
}
?>

<!DOCTYPE html>
<html lang="fa">
<head>
    <meta charset="UTF-8" />
    <title>فرم ورود و ثبت‌نام</title>
    <link href="https://fonts.googleapis.com/css2?family=Jost:wght@500&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<div class="main">
    <div class="switch-buttons">
        <label id="signup-btn" class="active" tabindex="0">ثبت‌نام</label>
        <label id="login-btn" tabindex="0">ورود</label>
    </div>

    <div class="signup">
        <?php if (!empty($errorMessage)) : ?>
            <div style="color: red; margin-bottom: 10px;">
                <?php echo $errorMessage; ?>
            </div>
        <?php endif; ?>

        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <label>
                <input type="text" name="frm[username]" placeholder="نام کاربری" required />
            </label>
            <label>
                <input type="tel" name="frm[phone]" placeholder="شماره تلفن" required />
            </label>
            <label>
                <input type="email" name="frm[email]" placeholder="ایمیل" required />
            </label>
            <label>
                <input type="password" name="frm[password]" placeholder="رمز عبور" required />
            </label>
            <label>
                <input type="password" name="frm[confirm_password]" placeholder="تأیید رمز عبور" required />
            </label>
            <input type="submit" value="ثبت‌نام" name="signup_btn" />
        </form>
    </div>

    <div class="login" style="display:none;">
        <form method="post" action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>">
            <label>
                <input type="email" name="email" placeholder="ایمیل" required />
            </label>
            <label>
                <input type="password" name="pswd" placeholder="رمز عبور" required />
            </label>
            <input type="submit" value="ورود" name="login_btn" />
        </form>
    </div>

</div>

<script>
    const signupBtn = document.getElementById('signup-btn');
    const loginBtn = document.getElementById('login-btn');
    const signupForm = document.querySelector('.signup');
    const loginForm = document.querySelector('.login');

    function showSignup() {
        signupForm.style.display = 'block';
        loginForm.style.display = 'none';
        signupBtn.classList.add('active');
        loginBtn.classList.remove('active');
        signupForm.scrollIntoView({ behavior: 'smooth' });
    }

    function showLogin() {
        signupForm.style.display = 'none';
        loginForm.style.display = 'block';
        loginBtn.classList.add('active');
        signupBtn.classList.remove('active');
        loginForm.scrollIntoView({ behavior: 'smooth' });
    }

    signupBtn.addEventListener('click', showSignup);
    loginBtn.addEventListener('click', showLogin);

    signupBtn.addEventListener('keydown', e => {
        if(e.key === 'Enter' || e.key === ' ') {
            showSignup();
        }
    });
    loginBtn.addEventListener('keydown', e => {
        if(e.key === 'Enter' || e.key === ' ') {
            showLogin();
        }
    });
</script>
</body>
</html>
