<?php
session_start();

require_once 'classes/Database.php';
require_once 'classes/User.php';

$database = new Database();
$user = new User($database);

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $user->login($email, $password);

    if ($result['success']) {
        header('Location: home.php');
        exit;
    } else {
        $error = $result['message'];
    }
}

if ($user->isLoggedIn()) {
    header('Location: home.php');
    exit;
}
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🌐 Вход - Чат стая</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="auth-box">
            <h1>🌐 Чат стая</h1>
            <h2>Вход</h2>
            
            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="email" name="email" placeholder="📧 Имейл" required>
                <input type="password" name="password" placeholder="🔒 Парола" required>
                <button type="submit">🔓 Влез</button>
            </form>
            
            <p>Защо нямаш акаунт? <a href="register.php">📝Глупав/а ли си? Не създавай оттук!!!</a></p>
        </div>
    </div>
</body>
</html>
