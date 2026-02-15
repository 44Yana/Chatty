<?php
require 'db.php';

try {
    $check = $pdo->query("SHOW COLUMNS FROM posts LIKE 'image_path'");
    
    if ($check->rowCount() == 0) {
        $pdo->exec("ALTER TABLE posts ADD COLUMN image_path VARCHAR(255) AFTER content");
        echo "Database updated successfully! Column 'image_path' added to posts table.";
    } else {
        echo "Database already has the 'image_path' column.";
    }
} catch (PDOException $e) {
    echo "❌ Error: " . $e->getMessage();
}
?>
