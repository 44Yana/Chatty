<?php
session_start();

require_once 'classes/Database.php';
require_once 'classes/User.php';

$database = new Database();
$user = new User($database);

$user->logout();

header('Location: index.php');
exit;
?>
