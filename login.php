<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Validar las credenciales del usuario
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();

    if ($user && password_verify($password, $user['password'])) {
        // Credenciales correctas, establecer la sesión
        $_SESSION['user_data'] = [
            'username' => $user['username'],
            'full_name' => $user['full_name'],
            'email' => $user['email'],
            'profile_pic' => $user['profile_pic']
        ];
        header("Location: profile.php");
    } else {
        echo "<script>alert('Nombre de usuario o contraseña incorrectos'); window.location.href='index.html';</script>";
    }
} else {
    header("Location: index.html");
    exit();
}
?>
