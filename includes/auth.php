<?php
function auth_user() {
    global $pdo;
    
    if (!isset($_SESSION['user_id'])) {
        return null;
    }
    
    if (!isset($pdo)) {
        die("Database connection missing in auth.");
    }
    
    try {
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
