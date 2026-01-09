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

// Fetch all lists with their tasks
try {
    $lists_stmt = $pdo->prepare("
        SELECT id, name, position, color
        FROM lists
        WHERE created_by = ? OR created_by = 1
        ORDER BY position ASC
    ");
    $lists_stmt->execute([$user['id']]);
    $lists = $lists_stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch tasks for each list
    foreach ($lists as &$list) {
        $tasks_stmt = $pdo->prepare("
            SELECT t.*, u.first_name, u.last_name
            FROM tasks t
            LEFT JOIN users u ON t.assigned_to = u.id
            WHERE t.list_id = ? AND (t.created_by = ? OR t.assigned_to = ?)
            ORDER BY t.card_position ASC, t.created_at DESC
        ");
        $tasks_stmt->execute([$list['id'], $user['id'], $user['id']]);
        $list['tasks'] = $tasks_stmt->fetchAll(PDO::FETCH_ASSOC);
    }
} catch (PDOException $e) {
    $lists = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard - VintageTask</title>
    <?php include 'includes/head.php'; ?>
</head>
<body class="bg-gray-100">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <?php require 'includes/sidebar.php'; ?>

        <div class="flex-1 flex flex-col min-w-0">
            <!-- Header -->
            <?php require 'includes/header.php'; ?>

            <!-- Main Board Canvas -->
            <main class="flex-1 overflow-x-auto overflow-y-hidden p-6">
                <div class="inline-flex h-full items-start space-x-4 pb-6">

                    <?php foreach ($lists as $list): ?>
                    <!-- List Container -->
                    <div class="w-72 flex-shrink-0 bg-gray-200 rounded-md p-2 flex flex-col max-h-full" data-list-id="<?php echo $list['id']; ?>">
                        <!-- List Header -->
                        <div class="flex justify-between items-center px-2 py-1 mb-1 <?php echo $list['color'] ? 'border-t-4 border-' . $list['color'] . '-400 rounded-t' : ''; ?>">
                            <h3 class="font-bold text-sm text-gray-700"><?php echo htmlspecialchars($list['name']); ?></h3>
                            <div class="flex items-center space-x-1">
                                <span class="text-xs text-gray-500 bg-gray-300 px-2 py-0.5 rounded"><?php echo count($list['tasks']); ?></span>
                                <button class="text-gray-500 hover:bg-gray-300 rounded px-2 py-1 text-sm">⋯</button>
                            </div>
                        </div>

                        <!-- Cards Stack -->
                        <div class="overflow-y-auto min-h-0 space-y-2 px-1 custom-scrollbar sortable-cards"
                             id="list-<?php echo $list['id']; ?>"
                             data-list-id="<?php echo $list['id']; ?>">

                            <?php if (empty($list['tasks'])): ?>
                                <!-- Empty state placeholder -->
                                <div class="text-center py-8 text-gray-400 text-sm">
                                    No cards yet
                                </div>
                            <?php else: ?>
                                <?php foreach ($list['tasks'] as $task): ?>
                                <!-- Task Card -->
                                <div class="bg-white p-3 rounded shadow-sm cursor-pointer hover:bg-gray-50 transition group"
                                     data-task-id="<?php echo $task['id']; ?>">

                                    <?php if ($task['priority'] === 'high'): ?>
                                    <div class="flex items-center space-x-1 mb-2">
                                        <span class="px-2 py-0.5 bg-red-100 text-red-700 text-xs font-medium rounded">High Priority</span>
                                    </div>
                                    <?php endif; ?>

                                    <h4 class="text-sm font-medium text-gray-800 mb-1"><?php echo htmlspecialchars($task['title']); ?></h4>

                                    <?php if ($task['description']): ?>
                                    <p class="text-xs text-gray-600 mb-2 line-clamp-2"><?php echo htmlspecialchars(substr($task['description'], 0, 80)); ?></p>
                                    <?php endif; ?>

                                    <div class="flex items-center justify-between mt-2">
                                        <!-- Due Date -->
                                        <?php if ($task['due_date']): ?>
                                        <div class="flex items-center space-x-1 text-xs text-gray-500">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span><?php echo date('M d', strtotime($task['due_date'])); ?></span>
                                        </div>
                                        <?php endif; ?>

                                        <!-- Assigned User -->
                                        <?php if ($task['first_name']): ?>
                                        <div class="w-6 h-6 rounded-full bg-primary text-white text-xs flex items-center justify-center font-medium"
                                             title="<?php echo htmlspecialchars($task['first_name'] . ' ' . $task['last_name']); ?>">
                                            <?php echo strtoupper(substr($task['first_name'], 0, 1)); ?>
                                        </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Add Card Button -->
                        <button class="mt-2 w-full text-left text-gray-600 hover:bg-gray-300 p-2 rounded text-sm transition"
                                onclick="showAddCardModal(<?php echo $list['id']; ?>)">
                            + Add a card
                        </button>
                    </div>
                    <?php endforeach; ?>

                    <!-- Add List Button -->
                    <button class="w-72 flex-shrink-0 bg-white bg-opacity-50 hover:bg-opacity-80 rounded-md p-3 text-left text-gray-700 font-medium transition"
                            onclick="showAddListModal()">
                        + Add another list
                    </button>

                </div>
            </main>
        </div>
    </div>

    <!-- Floating Dock (Bottom centered) -->
    <div class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white px-4 py-2 rounded-full shadow-lg border border-gray-200 flex space-x-2 z-50">
        <button class="px-4 py-1.5 rounded-full bg-blue-100 text-blue-700 font-medium text-sm">Board</button>
        <button class="px-4 py-1.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium text-sm" onclick="window.location.href='tasks.php'">My Tasks</button>
        <button class="px-4 py-1.5 rounded-full text-gray-600 hover:bg-gray-100 font-medium text-sm">Calendar</button>
    </div>

    <!-- Add Card Modal -->
    <div id="addCardModal" class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 w-full max-w-md">
            <h3 class="text-xl font-bold mb-4">Add New Card</h3>
            <form id="addCardForm" method="POST" action="actions/add_card.php">
                <input type="hidden" name="list_id" id="modal_list_id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Title</label>
                        <input type="text" name="title" required class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                        <textarea name="description" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary"></textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Priority</label>
                        <select name="priority" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                            <option value="low">Low</option>
                            <option value="medium" selected>Medium</option>
                            <option value="high">High</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Due Date</label>
                        <input type="date" name="due_date" class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-primary">
                    </div>
                </div>
                <div class="flex space-x-3 mt-6">
                    <button type="submit" class="flex-1 bg-primary text-white py-2 rounded-lg hover:bg-blue-800 transition">Add Card</button>
                    <button type="button" onclick="hideAddCardModal()" class="flex-1 bg-gray-200 text-gray-700 py-2 rounded-lg hover:bg-gray-300 transition">Cancel</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        // Initialize Sortable.js on all card containers
        document.addEventListener('DOMContentLoaded', function() {
            const lists = document.querySelectorAll('.sortable-cards');

            lists.forEach(list => {
                new Sortable(list, {
                    group: 'shared',
                    animation: 150,
                    ghostClass: 'sortable-ghost',
                    dragClass: 'sortable-drag',
                    onEnd: function(evt) {
                        // Get task ID and new list ID
                        const taskId = evt.item.dataset.taskId;
                        const newListId = evt.to.dataset.listId;
                        const newPosition = evt.newIndex;

                        // Send AJAX request to update task position
                        fetch('actions/update_task_position.php', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                task_id: taskId,
                                list_id: newListId,
                                position: newPosition
                            })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (!data.success) {
                                console.error('Failed to update task position');
                                // Optionally reload the page
                                location.reload();
                            }
                        })
                        .catch(error => {
                            console.error('Error:', error);
                        });
                    }
                });
            });
        });

        // Modal functions
        function showAddCardModal(listId) {
            document.getElementById('modal_list_id').value = listId;
            document.getElementById('addCardModal').classList.remove('hidden');
        }

        function hideAddCardModal() {
            document.getElementById('addCardModal').classList.add('hidden');
            document.getElementById('addCardForm').reset();
        }

        function showAddListModal() {
            const listName = prompt('Enter list name:');
            if (listName) {
                window.location.href = 'actions/add_list.php?name=' + encodeURIComponent(listName);
            }
        }
    </script>
</body>
</html>
