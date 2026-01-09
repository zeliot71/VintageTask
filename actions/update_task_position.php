<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

header('Content-Type: application/json');

$user = auth_user();
if (!$user) {
    echo json_encode(['success' => false, 'error' => 'Not authenticated']);
    exit;
}

// Get JSON input
$input = json_decode(file_get_contents('php://input'), true);
$task_id = $input['task_id'] ?? null;
$list_id = $input['list_id'] ?? null;
$position = $input['position'] ?? 0;

if (!$task_id || !$list_id) {
    echo json_encode(['success' => false, 'error' => 'Missing parameters']);
    exit;
}

try {
    // Verify user owns or is assigned to this task
    $verify_stmt = $pdo->prepare("SELECT id FROM tasks WHERE id = ? AND (created_by = ? OR assigned_to = ?)");
    $verify_stmt->execute([$task_id, $user['id'], $user['id']]);

    if (!$verify_stmt->fetch()) {
        echo json_encode(['success' => false, 'error' => 'Unauthorized']);
        exit;
    }

    // Update task position and list
    $update_stmt = $pdo->prepare("UPDATE tasks SET list_id = ?, card_position = ? WHERE id = ?");
    $update_stmt->execute([$list_id, $position, $task_id]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}
?>
