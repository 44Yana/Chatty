<?php

class User {
    private $db;
    private $user_id;
    private $username;
    private $email;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
        
        if (isset($_SESSION['user_id'])) {
            $this->user_id = $_SESSION['user_id'];
            $this->username = $_SESSION['username'];
        }
    }

    public function register($username, $email, $password) {
        if (empty($username) || empty($email) || empty($password)) {
            return [
                'success' => false,
                'message' => '❌ Не е учтиво да пропускаш поле! Върни се!'
            ];
        }

        if (strlen($password) < 6) {
            return [
                'success' => false,
                'message' => '❌  Съжалявам! Парола трябва да е минимум 6 символа!'
            ];
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return [
                'success' => false,
                'message' => '❌ Май си забравил имейла си? Опитай пак!'
            ];
        }

        $stmt = $this->db->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);

        if ($stmt->rowCount() > 0) {
            return [
                'success' => false,
                'message' => '❌ Потребителят вече съществува! Да не си забравил паролата?'
            ];
        }
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $stmt = $this->db->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
        
        if ($stmt->execute([$username, $email, $hashed_password])) {
            return [
                'success' => true,
                'message' => '✅ Регистрацията е успешна!'
            ];
        } else {
            return [
                'success' => false,
                'message' => '❌ Грешка при регистрация!'
            ];
        }
    }

    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return [
                'success' => false,
                'message' => '❌ Съжалявам, не можеш без имейл и/или парола!'
            ];
        }

        $stmt = $this->db->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $this->user_id = $user['id'];
            $this->username = $user['username'];

            return [
                'success' => true,
                'message' => '✅ Браво успя да влезеш в групата! Честито!'
            ];
        } else {
            return [
                'success' => false,
                'message' => '❌ Май си забравил имейла или паролата!'
            ];
        }
    }

    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }
    public function getUserId() {
        return $this->user_id ?? null;
    }

    public function getUsername() {
        return $this->username ?? null;
    }
    public function logout() {
        session_destroy();
        return true;
    }
}
?>
