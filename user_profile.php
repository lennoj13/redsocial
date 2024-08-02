<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: index.html");
    exit();
}

$username = $_GET['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Usuario no encontrado.";
    exit();
}

$user_data = $result->fetch_assoc();

$posts_stmt = $conn->prepare("SELECT * FROM posts WHERE username = ? ORDER BY created_at DESC");
$posts_stmt->bind_param("s", $username);
$posts_stmt->execute();
$posts = $posts_stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de <?= htmlspecialchars($user_data['full_name']) ?></title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Perfil de <?= htmlspecialchars($user_data['full_name']) ?></h1>
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
    <div class="container profile-container">
        <?php if (!empty($user_data['profile_pic'])): ?>
            <img src="<?= htmlspecialchars($user_data['profile_pic']) ?>" alt="Foto de Perfil">
        <?php else: ?>
            <img src="default_profile.png" alt="Foto de Perfil">
        <?php endif; ?>
        <p><strong>Nombre de Usuario:</strong> <?= htmlspecialchars($user_data['username']) ?></p>
        <p><strong>Nombre Completo:</strong> <?= htmlspecialchars($user_data['full_name']) ?></p>
        <p><strong>Correo Electrónico:</strong> <?= htmlspecialchars($user_data['email']) ?></p>

        <?php if ($username !== $_SESSION['user_data']['username']): ?>
            <h2>Enviar Mensaje</h2>
            <form action="start_chat.php" method="post">
                <input type="hidden" name="receiver_username" value="<?= htmlspecialchars($user_data['username']) ?>">
                <input type="submit" value="Enviar Mensaje">
            </form>
        <?php endif; ?>

        <h2>Publicaciones</h2>
        <?php while ($post = $posts->fetch_assoc()): ?>
            <div class="post">
                <strong><?= htmlspecialchars($post['username']) ?></strong>
                <p><?= htmlspecialchars($post['text']) ?></p>
                <?php if ($post['image']): ?>
                    <img src="<?= htmlspecialchars($post['image']) ?>" alt="Imagen">
                <?php endif; ?>
                <?php if ($post['video']): ?>
                    <video controls>
                        <source src="<?= htmlspecialchars($post['video']) ?>" type="video/mp4">
                    </video>
                <?php endif; ?>
                <p><small>Publicado el <?= $post['created_at'] ?></small></p>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>