<?php
function auth_user() {
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    try {
        require_once __DIR__ . '/db.php';
        $stmt = $pdo->prepare("SELECT id, email, first_name, last_name FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        return null;
    }
}

function require_guest() {
    if (isset($_SESSION['user_id'])) {
        $redirect = file_exists(__DIR__ . '/../dashboard.php') ? 'dashboard.php' : 'tasks.php';
        header("Location: $redirect");
        exit;
    }
}
?>
