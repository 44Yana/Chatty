<?php
session_start();

require_once 'classes/Database.php';
require_once 'classes/User.php';

$database = new Database();
$user = new User($database);

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $result = $user->register($username, $email, $password);

    if ($result['success']) {
        $success = '✅ ' . $result['message'] . ' <a href="index.php">🔓 Влезте тук</a>';
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
    <title>📝 Защо нямаш регeстрация? Направи си оттук!- Чат- стая</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <div class="auth-box">
            <h1>📝 Защо нямаш регестрация? Направи си оттук! </h1>
            
            <?php if (!empty($error)): ?>
                <div class="error"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="success"><?= $success ?></div>
            <?php endif; ?>
            
            <form method="POST">
                <input type="text" name="username" placeholder="👤 Потребителско име" required>
                <input type="email" name="email" placeholder="📧 Имейл" required>
                <input type="password" name="password" placeholder="🔒 Парола (мин. 6 символа)" required minlength="6">
                <button type="submit">📋 Давай, регестрирай се<!DOCTYPE html></button>
            </form>
            
            <p>Защо нямаш акаунт? <a href="index.php">🔓 Добре, можеш да влезеш!</a></p>
        </div>
    </div>
</body>
</html>