<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: index.html");
    exit();
}

// Obtener todas las publicaciones
$posts = $conn->query("SELECT * FROM posts ORDER BY created_at DESC");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Publicaciones</title>
    <link rel="stylesheet" href="styles.css">
    <style>

        .post-profile-pic {

            vertical-align: middle;
        }
        .post-header {
            display: flex;
            align-items: center;
        }
        .post-username {
            font-size: 16px;
            font-weight: bold;
            margin-left: 5px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Publicaciones</h1>
        <nav>
            <ul>
            <div class="search-container" style="margin-left: -178px;margin-top: -12px;">
            <form action="search.php" method="get">
                <input type="text" name="query" placeholder="Buscar usuarios..." required>
           
            </form>
        </div>
                <li><a href="profile.php">Perfil</a></li>
                <li><a href="feed.php">Publicaciones</a></li>
                <li><a href="post.php">Publicar</a></li>
                <li><a href="chats.php">Mensajería</a></li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <div class="container">
        <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="post-container">
                <div class="post-header">
                    <a href="user_profile.php?username=<?= htmlspecialchars($post['username']) ?>" class="post-user-link">
                        <?php
                        // Obtener la foto de perfil del usuario
                        $user_stmt = $conn->prepare("SELECT profile_pic FROM users WHERE username = ?");
                        $user_stmt->bind_param("s", $post['username']);
                        $user_stmt->execute();
                        $user_result = $user_stmt->get_result();
                        $user_data = $user_result->fetch_assoc();
                        ?>
                        <img src="<?= htmlspecialchars($user_data['profile_pic'] ?: 'default_profile.png') ?>" alt="Foto de Perfil" class="post-profile-pic">
                        <span class="post-username"><?= htmlspecialchars($post['username']) ?></span>
                    </a>
                </div>
                <p><?= htmlspecialchars($post['text']) ?></p>
                <?php if ($post['image']): ?>
                    <img src="<?= htmlspecialchars($post['image']) ?>" alt="Imagen" class="post-img">
                <?php endif; ?>
                <?php if ($post['video']): ?>
                    <video controls class="post-video">
                        <source src="<?= htmlspecialchars($post['video']) ?>" type="video/mp4">
                    </video>
                <?php endif; ?>
                <p><small>Publicado el <?= $post['created_at'] ?></small></p>

                <!-- Mostrar comentarios -->
                <?php
                $comments = $conn->prepare("SELECT * FROM comments WHERE post_id = ? ORDER BY created_at DESC");
                $comments->bind_param("i", $post['id']);
                $comments->execute();
                $result = $comments->get_result();
                while ($comment = $result->fetch_assoc()):
                ?>
                    <div class="comment">
                        <strong><?= htmlspecialchars($comment['username']) ?></strong>
                        <p><?= htmlspecialchars($comment['text']) ?></p>
                        <p><small>Comentado el <?= $comment['created_at'] ?></small></p>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php endwhile; ?>
    </div>
    <footer>
        <p>&copy; 2024 Nuestra Red Social. Todos los derechos reservados.</p>
    </footer>
</body>
</html>