-- Add lists table for Trello-like board functionality
USE vintagetasks_db;

-- Create lists table (columns like To Do, In Progress, Done, etc.)
CREATE TABLE IF NOT EXISTS lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    position INT NOT NULL DEFAULT 0,
    color VARCHAR(20),
    created_by INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Add list_id and card_position to tasks table
ALTER TABLE tasks ADD COLUMN IF NOT EXISTS list_id INT AFTER project_id;
ALTER TABLE tasks ADD COLUMN IF NOT EXISTS card_position INT DEFAULT 0 AFTER list_id;
ALTER TABLE tasks ADD FOREIGN KEY (list_id) REFERENCES lists(id) ON DELETE SET NULL;

-- Insert default lists
INSERT INTO lists (name, position, color, created_by) VALUES
('Trello Starter Guide', 0, NULL, 1),
('Today', 1, 'yellow', 1),
('This Week', 2, 'blue', 1),
('Done', 3, 'green', 1)
ON DUPLICATE KEY UPDATE name=name;
