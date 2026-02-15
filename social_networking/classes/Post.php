<?php
class Post {
    private $db;
    private $user_id;

    public function __construct(Database $database, $user_id) {
        $this->db = $database->getConnection();
        $this->user_id = $user_id;
    }

    public function create($content, $image_path = null) {
        if (empty($content) && empty($image_path)) {
            return [
                'success' => false,
                'message' => '❌ Публикацията трябва да има текст или снимка! Бъди креативен/на!'
            ];
        }

        $stmt = $this->db->prepare(
            "INSERT INTO posts (user_id, content, image_path) 
             VALUES (?, ?, ?)"
        );

        if ($stmt->execute([$this->user_id, $content, $image_path])) {
            return [
                'success' => true,
                'message' => '✅ Публикация успешна!'
            ];
        } else {
            return [
                'success' => false,
                'message' => '❌ Грешка при публикация!'
            ];
        }
    }
    public function getAll() {
        $sql = "
            SELECT p.*, u.username, 
            EXISTS(SELECT 1 FROM post_likes 
                   WHERE post_id = p.id AND user_id = ?) as user_liked
            FROM posts p 
            JOIN users u ON p.user_id = u.id 
            ORDER BY p.created_at DESC
        ";

        $stmt = $this->db->prepare($sql);
        $stmt->execute([$this->user_id]);
        return $stmt->fetchAll();
    }

    public function getById($post_id) {
        $stmt = $this->db->prepare("SELECT * FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        return $stmt->fetch();
    }

    public function update($post_id, $content, $image_path = null) {
        if (!$this->isOwner($post_id)) {
            return [
                'success' => false,
                'message' => '❌ Нямаш право да редактираш чужда публикация! Защо опитваш?'
            ];
        }

        $stmt = $this->db->prepare(
            "UPDATE posts SET content = ?, image_path = ? WHERE id = ? AND user_id = ?"
        );

        if ($stmt->execute([$content, $image_path, $post_id, $this->user_id])) {
            return [
                'success' => true,
                'message' => '✅ Публикация актуализирана! Яко, нали?'
            ];
        } else {
            return [
                'success' => false,
                'message' => '❌ Грешка при актуализиране! Опитай пак!'
            ];
        }
    }

    public function delete($post_id) {
        if (!$this->isOwner($post_id)) {
            return [
                'success' => false,
                'message' => '❌ Не можеш да изтриваш чужда публикация! Защо опитваш?'
            ];
        }

        $post = $this->getById($post_id);

        if ($post && !empty($post['image_path']) && file_exists($post['image_path'])) {
            unlink($post['image_path']);
        }

        $stmt = $this->db->prepare("DELETE FROM posts WHERE id = ? AND user_id = ?");

        if ($stmt->execute([$post_id, $this->user_id])) {
            return [
                'success' => true,
                'message' => '✅ Успешно успя да изтриеш публикация!'
            ];
        } else {
            return [
                'success' => false,
                'message' => '❌ ХА! не можа да изтриеш публикацията! Опитай пак!'
            ];
        }
    }

    private function isOwner($post_id) {
        $stmt = $this->db->prepare("SELECT user_id FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
        $post = $stmt->fetch();

        return $post && $post['user_id'] == $this->user_id;
    }

    public function getCount() {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM posts");
        $result = $stmt->fetch();
        return $result['count'];
    }
}
?>
