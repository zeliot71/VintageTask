<?php
$current_page = basename($_SERVER['PHP_SELF']);
?>
<aside class="w-64 bg-gray-900 text-white flex-shrink-0 flex flex-col">
    <!-- Logo Section -->
    <div class="p-6 border-b border-gray-800">
        <div class="flex items-center space-x-3">
            <img src="assets/images/logo.png" alt="VintageTask" class="h-8 w-auto">
            <span class="font-bold text-lg">VANTAGE TASKS</span>
        </div>
    </div>

    <!-- Navigation -->
    <nav class="flex-1 overflow-y-auto p-4 space-y-2">
        <a href="dashboard.php" class="<?php echo $current_page === 'dashboard.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-800'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path>
            </svg>
            <span class="font-medium">Dashboard</span>
        </a>

        <a href="tasks.php" class="<?php echo $current_page === 'tasks.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-800'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path>
            </svg>
            <span class="font-medium">My Tasks</span>
        </a>

        <a href="profile.php" class="<?php echo $current_page === 'profile.php' ? 'bg-primary text-white' : 'text-gray-300 hover:bg-gray-800'; ?> flex items-center space-x-3 px-4 py-3 rounded-lg transition">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
            </svg>
            <span class="font-medium">Profile</span>
        </a>
    </nav>

    <!-- User Section -->
    <div class="p-4 border-t border-gray-800">
        <?php
        $user = auth_user();
        if ($user):
        ?>
        <div class="flex items-center space-x-3 mb-3">
            <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center text-white font-bold">
                <?php echo strtoupper(substr($user['first_name'] ?? 'U', 0, 1)); ?>
            </div>
            <div class="flex-1 min-w-0">
                <p class="text-sm font-medium text-white truncate">
                    <?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?>
                </p>
                <p class="text-xs text-gray-400 truncate"><?php echo htmlspecialchars($user['email']); ?></p>
            </div>
        </div>
        <?php endif; ?>
        <a href="logout.php" class="flex items-center space-x-2 text-red-400 hover:text-red-300 text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
            </svg>
            <span>Logout</span>
        </a>
    </div>
</aside>
