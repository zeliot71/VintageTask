<?php
session_start();
require_once '../includes/db.php';
require_once '../includes/auth.php';

$user = auth_user();
if (!$user) {
    header('Location: ../login.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $list_id = $_POST['list_id'] ?? null;
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $priority = $_POST['priority'] ?? 'medium';
    $due_date = $_POST['due_date'] ?? null;

    if (!$list_id || empty($title)) {
        header('Location: ../dashboard.php?error=missing_fields');
        exit;
    }

    try {
        // Get the highest position in this list
        $pos_stmt = $pdo->prepare("SELECT MAX(card_position) as max_pos FROM tasks WHERE list_id = ?");
        $pos_stmt->execute([$list_id]);
        $max_pos = $pos_stmt->fetch(PDO::FETCH_ASSOC)['max_pos'] ?? 0;

        // Insert new task
        $insert_stmt = $pdo->prepare("
            INSERT INTO tasks (title, description, priority, due_date, list_id, card_position, created_by)
            VALUES (?, ?, ?, ?, ?, ?, ?)
        ");

        $insert_stmt->execute([
            $title,
            $description ?: null,
            $priority,
            $due_date ?: null,
            $list_id,
            $max_pos + 1,
            $user['id']
        ]);

        header('Location: ../dashboard.php?success=card_added');
        exit;
    } catch (PDOException $e) {
        header('Location: ../dashboard.php?error=failed');
        exit;
    }
} else {
    header('Location: ../dashboard.php');
    exit;
}
?>
