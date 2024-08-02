<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['user_data']['username'];

// Obtener todos los chats del usuario
$stmt = $conn->prepare("SELECT * FROM chats WHERE user1 = ? OR user2 = ?");
$stmt->bind_param("ss", $username, $username);
$stmt->execute();
$chats = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajería</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Chats</h1>
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
    <div class="container chat-list-container">
        <?php while ($chat = $chats->fetch_assoc()): ?>
            <?php
            // Obtener el nombre del otro usuario en el chat
            $other_user = ($chat['user1'] === $username) ? $chat['user2'] : $chat['user1'];

            // Obtener la foto de perfil del otro usuario
            $user_stmt = $conn->prepare("SELECT profile_pic FROM users WHERE username = ?");
            $user_stmt->bind_param("s", $other_user);
            $user_stmt->execute();
            $user_result = $user_stmt->get_result();
            $user_data = $user_result->fetch_assoc();
            ?>
            <div class="chat-list-item">
                <a href="chat.php?chat_id=<?= $chat['id'] ?>" class="chat-user-link">
                    <img src="<?= htmlspecialchars($user_data['profile_pic'] ?: 'default_profile.png') ?>" alt="Foto de Perfil" class="chat-profile-pic">
                    <span><?= htmlspecialchars($other_user) ?></span>
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>