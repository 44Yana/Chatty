<?php
class Like {
    private $db;
    private $user_id;

    public function __construct(Database $database, $user_id) {
        $this->db = $database->getConnection();
        $this->user_id = $user_id;
    }

    public function toggle($post_id) {
        if ($this->hasLiked($post_id)) {
            return $this->removeLike($post_id);
        } else {
            return $this->addLike($post_id);
        }
    }

    public function addLike($post_id) {
        $stmt = $this->db->prepare(
            "INSERT INTO post_likes (post_id, user_id) VALUES (?, ?)"
        );

        if (!$stmt->execute([$post_id, $this->user_id])) {
            return [
                'success' => false,
                'message' => '❌ Грешка при харесване!'
            ];
        }

        $stmt = $this->db->prepare(
            "UPDATE posts SET likes = likes + 1 WHERE id = ?"
        );
        $stmt->execute([$post_id]);

        return [
            'success' => true,
            'message' => '✅ Харесано!',
            'action' => 'added'
        ];
    }

    public function removeLike($post_id) {
        $stmt = $this->db->prepare(
            "DELETE FROM post_likes WHERE post_id = ? AND user_id = ?"
        );

        if (!$stmt->execute([$post_id, $this->user_id])) {
            return [
                'success' => false,
                'message' => '❌ Опитай пак!'
            ];
        }

        $stmt = $this->db->prepare(
            "UPDATE posts SET likes = likes - 1 WHERE id = ?"
        );
        $stmt->execute([$post_id]);

        return [
            'success' => true,
            'message' => '✅ Успя да премахнеш харесването!',
            'action' => 'removed'
        ];
    }

    public function hasLiked($post_id) {
        $stmt = $this->db->prepare(
            "SELECT id FROM post_likes WHERE post_id = ? AND user_id = ?"
        );
        $stmt->execute([$post_id, $this->user_id]);

        return $stmt->rowCount() > 0;
    }


    public function getCount($post_id) {
        $stmt = $this->db->prepare(
            "SELECT likes FROM posts WHERE id = ?"
        );
        $stmt->execute([$post_id]);
        $result = $stmt->fetch();

        return $result ? $result['likes'] : 0;
    }

    public function getUserLikes() {
        $stmt = $this->db->prepare(
            "SELECT post_id FROM post_likes WHERE user_id = ?"
        );
        $stmt->execute([$this->user_id]);

        return $stmt->fetchAll(PDO::FETCH_COLUMN);
    }
}
?>
