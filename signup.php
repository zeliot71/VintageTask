<?php
session_start();

$error = '';
$success = '';
$username_val = '';
$email_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    $username_val = htmlspecialchars($username);
    $email_val = htmlspecialchars($email);

    if (empty($username) || empty($email) || empty($password) || empty($confirm_password)) {
        $error = 'Please fill in all fields';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters';
    } else {
        try {
            require_once 'config/database.php';

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = 'Email already exists';
            } else {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");

                if ($stmt->execute([$username, $email, $hashed_password])) {
                    $success = 'Account created successfully! You can now login.';
                    $username_val = '';
                    $email_val = '';
                } else {
                    $error = 'Registration failed. Please try again.';
                }
            }
        } catch (Exception $e) {
            $error = 'Registration failed. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - VintageTask</title>
    <link rel="stylesheet" href="assets/css/auth.css">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Inder&display=swap">
</head>
<body>
    <div class="auth-container">
        <div class="auth-box">
            <div class="logo-section">
                <img src="assets/images/logo.png" alt="VintageTask Logo" class="auth-logo">
                <h1>Create Account</h1>
            </div>

            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>

            <?php if ($success): ?>
                <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
            <?php endif; ?>

            <form method="POST" action="signup.php" class="auth-form">
                <div class="form-group">
                    <label for="username">Username</label>
                    <input type="text" id="username" name="username" value="<?php echo $username_val; ?>" required autocomplete="username">
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $email_val; ?>" required autocomplete="email">
                </div>

                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autocomplete="new-password">
                </div>

                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password">
                </div>

                <button type="submit" class="btn-submit">Sign Up</button>
            </form>

            <div class="auth-footer">
                <p>Already have an account? <a href="login.php">Login</a></p>
                <p><a href="index.php">Back to Home</a></p>
            </div>
        </div>
    </div>
</body>
</html>
