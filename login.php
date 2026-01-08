<?php
session_start();

$error = '';
$email_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $email_val = htmlspecialchars($email);

    if (empty($email) || empty($password)) {
        $error = 'Please fill in all fields';
    } else {
        try {
            require_once 'config/database.php';

            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                header('Location: dashboard.php');
                exit;
            } else {
                $error = 'Invalid email or password';
            }
        } catch (Exception $e) {
            $error = 'Login failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - VintageTask</title>
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inder&display=swap">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="logo-section">
                <img src="assets/images/logo.png" alt="VintageTask Logo" class="auth-logo">
                <h1>Welcome Back</h1>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <form method="POST" action="login.php" class="auth-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email_val; ?>" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="current-password">
                </div>

                <button type="submit" class="btn-submit">Login</button>
            </form>

            <div class="auth-footer">
                <p>Don't have an account? <a href="signup.php">Sign Up</a></p>
                <p><a href="index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>
