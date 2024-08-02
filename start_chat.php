<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['receiver_username'])) {
    $sender_username = $_SESSION['user_data']['username'];
    $receiver_username = $_POST['receiver_username'];

    // Verificar si ya existe un chat entre estos usuarios
    $stmt = $conn->prepare("SELECT id FROM chats WHERE (user1 = ? AND user2 = ?) OR (user1 = ? AND user2 = ?)");
    $stmt->bind_param("ssss", $sender_username, $receiver_username, $receiver_username, $sender_username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // Crear un nuevo chat
        $stmt = $conn->prepare("INSERT INTO chats (user1, user2) VALUES (?, ?)");
        $stmt->bind_param("ss", $sender_username, $receiver_username);
        $stmt->execute();
        $chat_id = $stmt->insert_id;
    } else {
        $chat = $result->fetch_assoc();
        $chat_id = $chat['id'];
    }

    header("Location: chat.php?chat_id=" . $chat_id);
    exit();
}
?>