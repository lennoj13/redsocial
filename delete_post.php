<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['post_id'])) {
    $post_id = $_POST['post_id'];
    $username = $_SESSION['user_data']['username'];

    // Verificar que el post pertenece al usuario
    $stmt = $conn->prepare("SELECT * FROM posts WHERE id = ? AND username = ?");
    $stmt->bind_param("is", $post_id, $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // Eliminar el post
        $stmt = $conn->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->bind_param("i", $post_id);
        $stmt->execute();
    }
}

header("Location: profile.php");
exit();
?>