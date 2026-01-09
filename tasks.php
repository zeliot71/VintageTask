<?php
session_start();
require_once 'includes/db.php';
require_once 'includes/auth.php';

// Protect the page
$user = auth_user();
if (!$user) {
    header('Location: login.php');
    exit;
}

// Fetch all tasks for current user
try {
    $stmt = $pdo->prepare("
        SELECT t.*, l.name as list_name, u.first_name, u.last_name
        FROM tasks t
        LEFT JOIN lists l ON t.list_id = l.id
        LEFT JOIN users u ON t.assigned_to = u.id
        WHERE t.created_by = ? OR t.assigned_to = ?
        ORDER BY t.created_at DESC
    ");
    $stmt->execute([$user['id'], $user['id']]);
    $tasks = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $tasks = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>My Tasks - VintageTask</title>
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
                <div class="max-w-6xl mx-auto">
                    <!-- Stats Cards -->
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Total Tasks</p>
                                    <p class="text-3xl font-bold text-gray-900"><?php echo count($tasks); ?></p>
                                </div>
                                <div class="w-12 h-12 bg-blue-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">In Progress</p>
                                    <p class="text-3xl font-bold text-gray-900">
                                        <?php echo count(array_filter($tasks, fn($t) => $t['status'] === 'in_progress')); ?>
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-yellow-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>

                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-sm text-gray-600">Completed</p>
                                    <p class="text-3xl font-bold text-gray-900">
                                        <?php echo count(array_filter($tasks, fn($t) => $t['status'] === 'completed')); ?>
                                    </p>
                                </div>
                                <div class="w-12 h-12 bg-green-100 rounded-lg flex items-center justify-center">
                                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tasks Table -->
                    <div class="bg-white rounded-lg shadow-sm overflow-hidden">
                        <div class="px-6 py-4 border-b border-gray-200 flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-gray-900">All Tasks</h2>
                            <a href="dashboard.php" class="text-primary hover:underline text-sm font-medium">View Board</a>
                        </div>

                        <?php if (empty($tasks)): ?>
                        <div class="p-12 text-center">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            <p class="text-gray-500 mb-4">No tasks yet. Start by adding some tasks!</p>
                            <a href="dashboard.php" class="inline-block px-6 py-2 bg-primary text-white rounded-lg hover:bg-blue-800 transition">Go to Dashboard</a>
                        </div>
                        <?php else: ?>
                        <div class="overflow-x-auto">
                            <table class="w-full">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Task</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">List</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Priority</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Due Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    <?php foreach ($tasks as $task): ?>
                                    <tr class="hover:bg-gray-50">
                                        <td class="px-6 py-4">
                                            <div class="font-medium text-gray-900"><?php echo htmlspecialchars($task['title']); ?></div>
                                            <?php if ($task['description']): ?>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($task['description'], 0, 60)); ?>...</div>
                                            <?php endif; ?>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo htmlspecialchars($task['list_name'] ?? 'No list'); ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded
                                                <?php
                                                    if ($task['priority'] === 'high') echo 'bg-red-100 text-red-700';
                                                    elseif ($task['priority'] === 'medium') echo 'bg-yellow-100 text-yellow-700';
                                                    else echo 'bg-gray-100 text-gray-700';
                                                ?>">
                                                <?php echo ucfirst($task['priority']); ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-900">
                                            <?php echo $task['due_date'] ? date('M d, Y', strtotime($task['due_date'])) : '-'; ?>
                                        </td>
                                        <td class="px-6 py-4">
                                            <span class="px-2 py-1 text-xs font-medium rounded
                                                <?php
                                                    if ($task['status'] === 'completed') echo 'bg-green-100 text-green-700';
                                                    elseif ($task['status'] === 'in_progress') echo 'bg-blue-100 text-blue-700';
                                                    else echo 'bg-gray-100 text-gray-700';
                                                ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $task['status'])); ?>
                                            </span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </main>
        </div>
    </div>
</body>
</html>
