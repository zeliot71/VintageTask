<?php
$user = auth_user();
$page_title = 'Dashboard';
if (basename($_SERVER['PHP_SELF']) === 'tasks.php') {
    $page_title = 'My Tasks';
} elseif (basename($_SERVER['PHP_SELF']) === 'profile.php') {
    $page_title = 'Profile';
}
?>
<header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between">
    <div class="flex items-center space-x-4">
        <h1 class="text-2xl font-bold text-gray-900"><?php echo $page_title; ?></h1>
    </div>

    <div class="flex items-center space-x-4">
        <!-- Search Bar -->
        <div class="relative hidden md:block">
            <input type="text"
                   placeholder="Search tasks..."
                   class="w-64 px-4 py-2 pl-10 bg-gray-50 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary focus:border-transparent">
            <svg class="w-5 h-5 text-gray-400 absolute left-3 top-1/2 transform -translate-y-1/2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
            </svg>
        </div>

        <!-- Date Display -->
        <div class="text-right hidden lg:block">
            <p class="text-xs text-gray-500">Today</p>
            <p class="text-sm font-semibold text-gray-900"><?php echo date('l, M d'); ?></p>
        </div>

        <!-- Notifications -->
        <button class="relative p-2 text-gray-600 hover:bg-gray-100 rounded-lg transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path>
            </svg>
            <span class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full"></span>
        </button>
    </div>
</header>
