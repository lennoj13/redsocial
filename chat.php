<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: index.html");
    exit();
}

if (!isset($_GET['chat_id'])) {
    echo "Chat no especificado.";
    exit();
}

$chat_id = $_GET['chat_id'];
$username = $_SESSION['user_data']['username'];

// Obtener mensajes del chat
$stmt = $conn->prepare("SELECT * FROM messages WHERE chat_id = ? ORDER BY created_at ASC");
$stmt->bind_param("i", $chat_id);
$stmt->execute();
$messages = $stmt->get_result();

// Enviar un nuevo mensaje
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message_text'])) {
    $message_text = $_POST['message_text'];
    $stmt = $conn->prepare("INSERT INTO messages (chat_id, sender_username, text) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $chat_id, $username, $message_text);
    $stmt->execute();

    header("Location: chat.php?chat_id=" . $chat_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Chat</h1>
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
    <div class="container chat-container">
        <div class="chat-messages">
            <?php while ($message = $messages->fetch_assoc()): ?>
                <div class="message <?= $message['sender_username'] === $username ? 'my-message' : 'other-message' ?>">
                    <?php
                    // Obtener la foto de perfil del usuario que envió el mensaje
                    $user_stmt = $conn->prepare("SELECT profile_pic FROM users WHERE username = ?");
                    $user_stmt->bind_param("s", $message['sender_username']);
                    $user_stmt->execute();
                    $user_result = $user_stmt->get_result();
                    $user_data = $user_result->fetch_assoc();
                    ?>
                    <img src="<?= htmlspecialchars($user_data['profile_pic'] ?: 'default_profile.png') ?>" alt="Foto de Perfil" class="message-profile-pic">
                    <div class="message-content">
                        <strong><?= htmlspecialchars($message['sender_username']) ?>:</strong>
                        <p><?= htmlspecialchars($message['text']) ?></p>
                        <p><small>Enviado el <?= $message['created_at'] ?></small></p>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
        <form action="chat.php?chat_id=<?= $chat_id ?>" method="post" class="chat-input">
            <input type="text" name="message_text" required>
            <input type="submit" value="Enviar">
        </form>
    </div>
</body>
</html>