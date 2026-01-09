<?php
session_start();
require_once 'includes/auth.php';
require_once 'includes/db.php';

// Protect the page
$user = auth_user();
if (!$user) {
    header('Location: login.php');
    exit;
}

$success = '';
$error = '';

// Fetch full user details
try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user['id']]);
    $user_details = $stmt->fetch(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $error = 'Failed to load profile';
}

// Handle profile update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name'] ?? '');
    $last_name = trim($_POST['last_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $company_name = trim($_POST['company_name'] ?? '');
    $position = trim($_POST['position'] ?? '');

    if (empty($first_name)) {
        $error = 'First name is required';
    } else {
        try {
            $update_stmt = $pdo->prepare("
                UPDATE users
                SET first_name = ?, last_name = ?, phone = ?, company_name = ?, position = ?
                WHERE id = ?
            ");

            $update_stmt->execute([
                $first_name,
                $last_name,
                $phone ?: null,
                $company_name ?: null,
                $position ?: null,
                $user['id']
            ]);

            $success = 'Profile updated successfully';

            // Refresh user details
            $stmt->execute([$user['id']]);
            $user_details = $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            $error = 'Failed to update profile';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Profile - VintageTask</title>
    <?php include 'includes/head.php'; ?>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php require 'includes/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <?php require 'includes/header.php'; ?>

            <!-- Main Content -->
            <main class="flex-1 overflow-y-auto p-6">
                <div class="max-w-4xl mx-auto">
                    <!-- Profile Card -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <!-- Header -->
                        <div class="bg-primary px-6 py-8 text-white">
                            <div class="flex items-center space-x-4">
                                <div class="w-20 h-20 rounded-full bg-white flex items-center justify-center text-primary text-3xl font-bold">
                                    <?php echo strtoupper(substr($user_details['first_name'], 0, 1)); ?>
                                </div>
                                <div>
                                    <h2 class="text-2xl font-bold">
                                        <?php echo htmlspecialchars($user_details['first_name'] . ' ' . $user_details['last_name']); ?>
                                    </h2>
                                    <p class="text-blue-100"><?php echo htmlspecialchars($user_details['email']); ?></p>
                                </div>
                            </div>
                        </div>

                        <!-- Form -->
                        <div class="p-6">
                            <?php if ($success): ?>
                            <div class="mb-6 bg-green-50 text-green-700 px-4 py-3 rounded-lg border border-green-200">
                                <?php echo htmlspecialchars($success); ?>
                            </div>
                            <?php endif; ?>

                            <?php if ($error): ?>
                            <div class="mb-6 bg-red-50 text-red-700 px-4 py-3 rounded-lg border border-red-200">
                                <?php echo htmlspecialchars($error); ?>
                            </div>
                            <?php endif; ?>

                            <form method="POST" action="profile.php" class="space-y-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">First Name</label>
                                        <input type="text"
                                               name="first_name"
                                               value="<?php echo htmlspecialchars($user_details['first_name']); ?>"
                                               required
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Last Name</label>
                                        <input type="text"
                                               name="last_name"
                                               value="<?php echo htmlspecialchars($user_details['last_name']); ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Email</label>
                                        <input type="email"
                                               value="<?php echo htmlspecialchars($user_details['email']); ?>"
                                               disabled
                                               class="w-full px-4 py-2 bg-gray-50 border border-gray-300 rounded-lg text-gray-500 cursor-not-allowed">
                                        <p class="text-xs text-gray-500 mt-1">Email cannot be changed</p>
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Phone</label>
                                        <input type="tel"
                                               name="phone"
                                               value="<?php echo htmlspecialchars($user_details['phone'] ?? ''); ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Company Name</label>
                                        <input type="text"
                                               name="company_name"
                                               value="<?php echo htmlspecialchars($user_details['company_name'] ?? ''); ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>

                                    <div>
                                        <label class="block text-sm font-medium text-gray-700 mb-2">Position</label>
                                        <input type="text"
                                               name="position"
                                               value="<?php echo htmlspecialchars($user_details['position'] ?? ''); ?>"
                                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                                    </div>
                                </div>

                                <div class="flex space-x-4">
                                    <button type="submit"
                                            class="px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-800 transition font-medium">
                                        Save Changes
                                    </button>
                                    <a href="dashboard.php"
                                       class="px-6 py-2 bg-gray-200 text-gray-700 rounded-lg hover:bg-gray-300 transition font-medium">
                                        Cancel
                                    </a>
                                </div>
                            </form>
                        </div>
                    </div>

                    <!-- Account Info -->
                    <div class="mt-6 bg-white rounded-lg shadow-sm p-6">
                        <h3 class="text-lg font-semibold text-gray-900 mb-4">Account Information</h3>
                        <div class="space-y-3 text-sm">
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Member Since</span>
                                <span class="font-medium text-gray-900">
                                    <?php echo date('F d, Y', strtotime($user_details['created_at'])); ?>
                                </span>
                            </div>
                            <div class="flex justify-between py-2 border-b border-gray-100">
                                <span class="text-gray-600">Account Role</span>
                                <span class="font-medium text-gray-900">
                                    <?php echo ucfirst($user_details['role']); ?>
                                </span>
                            </div>
                            <div class="flex justify-between py-2">
                                <span class="text-gray-600">Last Updated</span>
                                <span class="font-medium text-gray-900">
                                    <?php echo date('F d, Y', strtotime($user_details['updated_at'])); ?>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
