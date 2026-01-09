<?php
session_start();
require_once '../includes/auth.php';
require_once '../includes/db.php';

$user = auth_user();
if (!$user) {
    header('Location: ../login.php');
    exit;
}

$list_name = trim($_GET['name'] ?? '');

if (empty($list_name)) {
    header('Location: ../dashboard.php?error=empty_name');
    exit;
}

try {
    // Get the highest position
    $pos_stmt = $pdo->prepare("SELECT MAX(position) as max_pos FROM lists WHERE created_by = ?");
    $pos_stmt->execute([$user['id']]);
    $max_pos = $pos_stmt->fetch(PDO::FETCH_ASSOC)['max_pos'] ?? 0;

    // Insert new list
    $insert_stmt = $pdo->prepare("INSERT INTO lists (name, position, created_by) VALUES (?, ?, ?)");
    $insert_stmt->execute([$list_name, $max_pos + 1, $user['id']]);

    header('Location: ../dashboard.php?success=list_added');
    exit;
} catch (PDOException $e) {
    header('Location: ../dashboard.php?error=failed');
    exit;
}
?>
