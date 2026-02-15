<?php
session_start();
require_once 'classes/Database.php';
require_once 'classes/User.php';
require_once 'classes/Post.php';
require_once 'classes/Like.php';
require_once 'classes/FileUpload.php';

$database = new Database();
$user = new User($database);

if (!$user->isLoggedIn()) {
    header('Location: index.php');
    exit;
}

$user_id = $user->getUserId();
$username = $user->getUsername();


$post = new Post($database, $user_id);
$like = new Like($database, $user_id);
$fileUpload = new FileUpload();


if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = isset($_POST['content']) ? trim($_POST['content']) : '';
    $image_path = null;

    if (isset($_FILES['image']) && $_FILES['image']['size'] > 0) {
        $upload_result = $fileUpload->upload($_FILES['image']);
        
        if ($upload_result['success']) {
            $image_path = $upload_result['path'];
        }
    }


    $create_result = $post->create($content, $image_path);

    if ($create_result['success']) {
        header('Location: home.php');
        exit;
    }
}

if (isset($_GET['like'])) {
    $post_id = (int)$_GET['like'];
    $like_result = $like->toggle($post_id);
    
    header('Location: home.php');
    exit;
}

if (isset($_GET['delete'])) {
    $post_id = (int)$_GET['delete'];
    $delete_result = $post->delete($post_id);
    
    header('Location: home.php');
    exit;
}

$posts = $post->getAll();
?>
<!DOCTYPE html>
<html lang="bg">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>🏠 Начало - Чат стая</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav>
        <h1>🌐 Чат стая</h1>
        <div>
            <span>Здрасти, <strong><?= htmlspecialchars($username) ?></strong> 👋</span>
            <a href="logout.php" class="btn-logout">🚪 Чао</a>
        </div>
    </nav>
    
    <div class="container">
        <div class="post-box">
            <h3>📝 Какво ти е в ума? Кажи го тук!</h3>
            <form method="POST" enctype="multipart/form-data">
                <textarea name="content" placeholder="Споделете нещо..."></textarea>
                
                <!-- ЕМОДЖИ ПИКЪР -->
                <!--https://unicode.org/emoji/charts/full-emoji-list.html-->  
                   <div class="emoji-picker">
                    <button type="button" onclick="addEmoji('😀')">😀</button>
                    <button type="button" onclick="addEmoji('😂')">😂</button>
                    <button type="button" onclick="addEmoji('😍')">😍</button>
                    <button type="button" onclick="addEmoji('🎉')">🎉</button>
                    <button type="button" onclick="addEmoji('🔥')">🔥</button>
                    <button type="button" onclick="addEmoji('👍')">👍</button>
                    <button type="button" onclick="addEmoji('❤️')">❤️</button>
                    <button type="button" onclick="addEmoji('😢')">😢</button>
                    <button type="button" onclick="addEmoji('🤔')">🤔</button>
                    <button type="button" onclick="addEmoji('😎')">😎</button>
                </div>
                
                <!-- КАЧВАНЕ НА СНИМКА -->
                <div class="file-input-wrapper">
                    <input type="file" name="image" accept="image/*">
                    <button type="submit">📤 Качи готина снимка оттук!</button>
                </div>
            </form>
        </div>
        
        <!-- ЛЕНТА НА ПУБЛИКАЦИИ -->
        <div class="posts">
            <?php if (empty($posts)): ?>
                <p class="no-posts">Бъди първия да изкажеш своята мисъл! 📝</p>
            <?php else: ?>
                <?php foreach ($posts as $post_item): ?>
                <div class="post">
                    <div class="post-header">
                        <div>
                            <strong>@<?= htmlspecialchars($post_item['username']) ?></strong>
                            <small><?= date('d.m.Y H:i', strtotime($post_item['created_at'])) ?></small>
                        </div>
                        <?php if ($post_item['user_id'] == $user_id): ?>
                            <a href="?delete=<?= $post_item['id'] ?>" class="delete-btn" onclick="return confirm('Сигурни ли сте?')">🗑️ Изтрий</a>
                        <?php endif; ?>
                    </div>
                    <p class="post-content"><?= nl2br(htmlspecialchars($post_item['content'])) ?></p>
                    
                    <!-- СНИМКА -->
                    <?php if (!empty($post_item['image_path']) && file_exists($post_item['image_path'])): ?>
                        <img src="<?= htmlspecialchars($post_item['image_path']) ?>" alt="Post image" class="post-image">
                    <?php endif; ?>
                    
                    <!-- LIKE БУТОН -->
                    <div class="post-footer">
                        <a href="?like=<?= $post_item['id'] ?>" class="like-btn <?= $post_item['user_liked'] ? 'liked' : '' ?>">
                            ❤️ <?= $post_item['likes'] ?>
                        </a>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
    
        function addEmoji(emoji) {
            const textarea = document.querySelector('textarea[name="content"]');
            textarea.value += emoji;
            textarea.focus();
        }
    </script>
</body>
</html>

