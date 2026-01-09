<?php
/**
 * Database Migration Runner
 * Run this file once to set up the lists table and update the tasks table
 * Access via: http://localhost/vintagetask/run_migration.php
 */

require_once 'config/database.php';

echo "<h2>Running Database Migration...</h2>";

try {
    // Create lists table
    $sql1 = "CREATE TABLE IF NOT EXISTS lists (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        position INT NOT NULL DEFAULT 0,
        color VARCHAR(20),
        created_by INT NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";

    $pdo->exec($sql1);
    echo "<p style='color: green;'>✓ Lists table created successfully</p>";

    // Add list_id column to tasks (check if exists first)
    $check = $pdo->query("SHOW COLUMNS FROM tasks LIKE 'list_id'")->fetch();
    if (!$check) {
        $sql2 = "ALTER TABLE tasks ADD COLUMN list_id INT AFTER project_id";
        $pdo->exec($sql2);
        echo "<p style='color: green;'>✓ Added list_id column to tasks</p>";
    } else {
        echo "<p style='color: blue;'>ℹ list_id column already exists</p>";
    }

    // Add card_position column to tasks (check if exists first)
    $check2 = $pdo->query("SHOW COLUMNS FROM tasks LIKE 'card_position'")->fetch();
    if (!$check2) {
        $sql3 = "ALTER TABLE tasks ADD COLUMN card_position INT DEFAULT 0 AFTER list_id";
        $pdo->exec($sql3);
        echo "<p style='color: green;'>✓ Added card_position column to tasks</p>";
    } else {
        echo "<p style='color: blue;'>ℹ card_position column already exists</p>";
    }

    // Add foreign key if not exists
    try {
        $pdo->exec("ALTER TABLE tasks ADD FOREIGN KEY (list_id) REFERENCES lists(id) ON DELETE SET NULL");
        echo "<p style='color: green;'>✓ Added foreign key constraint</p>";
    } catch (PDOException $e) {
        if (strpos($e->getMessage(), 'Duplicate') !== false) {
            echo "<p style='color: blue;'>ℹ Foreign key already exists</p>";
        } else {
            throw $e;
        }
    }

    // Insert default lists (use admin user or first user)
    $user_stmt = $pdo->query("SELECT id FROM users ORDER BY id ASC LIMIT 1");
    $first_user = $user_stmt->fetch(PDO::FETCH_ASSOC);

    if ($first_user) {
        $user_id = $first_user['id'];

        // Check if lists already exist
        $existing_lists = $pdo->query("SELECT COUNT(*) as count FROM lists")->fetch()['count'];

        if ($existing_lists == 0) {
            $insert_lists = "INSERT INTO lists (name, position, color, created_by) VALUES
                ('Trello Starter Guide', 0, NULL, $user_id),
                ('Today', 1, 'yellow', $user_id),
                ('This Week', 2, 'blue', $user_id),
                ('Done', 3, 'green', $user_id)";

            $pdo->exec($insert_lists);
            echo "<p style='color: green;'>✓ Default lists created successfully</p>";
        } else {
            echo "<p style='color: blue;'>ℹ Lists already exist ($existing_lists lists found)</p>";
        }
    } else {
        echo "<p style='color: orange;'>⚠ No users found. Please create a user account first.</p>";
    }

    echo "<h3 style='color: green;'>✅ Migration completed successfully!</h3>";
    echo "<p><a href='dashboard.php' style='color: blue; text-decoration: underline;'>Go to Dashboard</a></p>";

} catch (PDOException $e) {
    echo "<p style='color: red;'>❌ Error: " . $e->getMessage() . "</p>";
}
?>
