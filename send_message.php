<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['message_text']) && isset($_POST['receiver_username'])) {
    $sender_username = $_SESSION['user_data']['username'];
    $receiver_username = $_POST['receiver_username'];
    $message_text = $_POST['message_text'];

    $stmt = $conn->prepare("INSERT INTO private_messages (sender_username, receiver_username, text) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $sender_username, $receiver_username, $message_text);
    $stmt->execute();

    header("Location: profile.php?username=" . $receiver_username);
    exit();
}
?>