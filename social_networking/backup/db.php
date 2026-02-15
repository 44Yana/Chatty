<?php

require_once 'classes/Database.php';
$db_instance = new Database();
$pdo = $db_instance->getConnection();

session_start();
?>

