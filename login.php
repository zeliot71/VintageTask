<?php
session_start();
require_once 'includes/auth.php';
require_guest();

$error = '';
$email_val = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $email_val = htmlspecialchars($email);

    if (empty($email) || empty($password)) {
        $error = 'Invalid email or password.';
    } else {
        try {
            require_once 'includes/db.php';
            $stmt = $pdo->prepare("SELECT id, password FROM users WHERE email = ?");
            $stmt->execute([$email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password'])) {
                session_regenerate_id(true);
                $_SESSION['user_id'] = $user['id'];
                $redirect = file_exists('dashboard.php') ? 'dashboard.php' : 'tasks.php';
                header("Location: $redirect");
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } catch (Exception $e) {
            $error = 'Invalid email or password.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Login - VintageTask</title>
    <?php include 'includes/head.php'; ?>
</head>
<body class="bg-white">
    <div class="min-h-screen flex flex-col md:flex-row">
        <!-- Left: Illustration -->
        <div class="w-full md:w-1/2 bg-gray-50 flex items-center justify-center p-4 md:p-8">
            <img src="assets/images/R 2.png" alt="Login illustration" class="w-full max-w-md h-auto">
        </div>

        <!-- Right: Form -->
        <div class="w-full md:w-1/2 flex items-center justify-center p-4 md:p-8">
            <div class="w-full max-w-md">
                <img src="assets/images/logo.png" alt="VintageTask" class="h-10 mb-6">
                <h1 class="text-3xl font-bold text-gray-900 mb-2">Log in</h1>
                <p class="text-gray-600 mb-6">Sign in to your account via email</p>

                <?php if ($error): ?>
                    <div class="bg-red-50 text-red-700 px-4 py-3 rounded mb-6">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>

                <form method="POST" action="login.php" class="space-y-4 mb-6">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" id="email" name="email" value="<?php echo $email_val; ?>" required autocomplete="email" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                        <input type="password" id="password" name="password" required autocomplete="current-password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>

                    <div class="flex items-center justify-between">
                        <label class="flex items-center">
                            <input type="checkbox" class="w-4 h-4 border-gray-300 rounded">
                            <span class="ml-2 text-sm text-gray-600">Remember me</span>
                        </label>
                        <a href="#" class="text-sm text-primary hover:underline">Forget password?</a>
                    </div>

                    <button type="submit" class="w-full bg-primary text-white font-semibold py-2 rounded-lg hover:bg-blue-700 transition">
                        Log in
                    </button>
                </form>

                <div class="relative mb-6">
                    <div class="absolute inset-0 flex items-center">
                        <div class="w-full border-t border-gray-300"></div>
                    </div>
                    <div class="relative flex justify-center text-sm">
                        <span class="px-2 bg-white text-gray-500">Sign in with social media</span>
                    </div>
                </div>

                <div class="space-y-2">
                    <button type="button" class="w-full border border-gray-300 text-gray-700 font-medium py-2 rounded-lg flex items-center justify-center space-x-2 hover:bg-gray-50 transition">
                        <img src="assets/images/Clip path group.svg" alt="Google" class="w-5 h-5">
                        <span>Google</span>
                    </button>
                    <button type="button" class="w-full border border-gray-300 text-gray-700 font-medium py-2 rounded-lg flex items-center justify-center space-x-2 hover:bg-gray-50 transition">
                        <img src="assets/images/Facebook (icon — Colour).svg" alt="Facebook" class="w-5 h-5">
                        <span>Facebook</span>
                    </button>
                </div>

                <p class="text-center text-gray-600 mt-6">
                    Not a member? <a href="signup.php" class="text-primary font-semibold hover:underline">create a new account</a>
                </p>
            </div>
        </div>
    </div>
</body>
</html>
