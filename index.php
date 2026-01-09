<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header('Location: ' . (file_exists('dashboard.php') ? 'dashboard.php' : 'tasks.php'));
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>VintageTask - Group Work Management</title>
    <?php include 'includes/head.php'; ?>
</head>
<body class="bg-white">
    <div class="min-h-screen flex flex-col md:flex-row items-center">
        <div class="w-full md:w-1/2 flex items-center justify-center p-4 md:p-8">
            <div class="relative w-full max-w-md">
                <img src="assets/images/image 2.png" alt="Dashboard" class="w-full h-auto -rotate-12 drop-shadow-lg">
            </div>
        </div>

        <div class="w-full md:w-1/2 flex flex-col items-center md:items-start justify-center p-4 md:p-8">
            <img src="assets/images/logo.png" alt="VintageTask Logo" class="h-12 mb-6">
            <h1 class="text-4xl md:text-5xl font-bold text-gray-900 mb-2">VANTAGE TASKS</h1>
            <p class="text-xl text-gray-700 mb-8 text-center md:text-left">
                To smooth your Group<br>Work Experience
            </p>
            <a href="login.php" class="w-full md:w-auto px-8 py-3 bg-primary text-white font-semibold rounded-lg shadow-md hover:shadow-lg transition">
                Get Started
            </a>
        </div>
    </div>
</body>
</html>