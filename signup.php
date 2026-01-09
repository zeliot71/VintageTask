<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';
require_guest();

$error = '';
$name_val = '';
$email_val = '';
$company_val = '';
$phone_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $terms = isset($_POST['terms']) ? true : false;

    $name_val = htmlspecialchars($name);
    $email_val = htmlspecialchars($email);
    $company_val = htmlspecialchars($company_name);
    $phone_val = htmlspecialchars($phone);

    if (empty($name) || empty($email) || empty($password) || empty($confirm_password) || !$terms) {
        $error = 'Please fill in all fields and accept the terms.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format.';
    } elseif (strlen($password) < 8) {
        $error = 'Password must be at least 8 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        try {
            require_once 'includes/db.php';

            $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
            $stmt->execute([$email]);

            if ($stmt->fetch()) {
                $error = 'Email already in use.';
            } else {
                $name_parts = explode(' ', $name, 2);
                $first_name = $name_parts[0];
                $last_name = isset($name_parts[1]) ? $name_parts[1] : '';

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("INSERT INTO users (first_name, last_name, email, password, company_name, phone, role) VALUES (?, ?, ?, ?, ?, ?, 'employee')");

                if ($stmt->execute([$first_name, $last_name, $email, $hashed_password, $company_name ?: null, $phone ?: null])) {
                    session_regenerate_id(true);
                    $_SESSION['user_id'] = $pdo->lastInsertId();
                    $redirect = file_exists('dashboard.php') ? 'dashboard.php' : 'tasks.php';
                    header("Location: $redirect");
                    exit;
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
    <title>Sign Up - VintageTask</title>
    <?php include 'includes/head.php'; ?>
</head>
<body class="bg-white">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Left: Brand -->
        <div class="w-full md:w-1/2 bg-gray-50 flex flex-col items-center justify-center p-4 md:p-8">
            <img src="assets/images/logo.png" alt="VintageTask" class="h-14 mb-6">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 text-center">VANTAGE<br>TASKS</h1>
        </div>

        <!-- Right: Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-4 md:p-8">
            <div class="w-full max-w-md">
                <h2 class="text-3xl font-bold text-gray-900 mb-6">Sign up</h2>

                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-700 px-4 py-3 rounded mb-6">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="signup.php" class="space-y-4 mb-6">
                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Name</label>
                        <input type="text" id="name" name="name" value="<?php echo $name_val; ?>" required class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $email_val; ?>" required autocomplete="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label for="company_name" class="block text-sm font-medium text-gray-700 mb-1">Company Name</label>
                        <input type="text" id="company_name" name="company_name" value="<?php echo $company_val; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label for="phone" class="block text-sm font-medium text-gray-700 mb-1">Phone</label>
                        <input type="tel" id="phone" name="phone" value="<?php echo $phone_val; ?>" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" required autocomplete="new-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" required autocomplete="new-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <label class="flex items-center">
                        <input type="checkbox" name="terms" required class="w-4 h-4 border-gray-300 rounded">
                        <span class="ml-2 text-sm text-gray-600">I agree to all terms</span>
                    </label>

                    <button type="submit" class="w-full bg-primary text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition shadow-md">
                        Sign up
                    </button>
                </form>

                <p class="text-center text-gray-600">
                    Already have an account? <a href="login.php" class="text-primary font-semibold hover:underline">Log in</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
