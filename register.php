<?php
session_start();
include 'database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $profile_pic = '';

    // Verificar si el nombre de usuario ya existe
    $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo "<script>alert('El nombre de usuario ya está registrado. Por favor, elige otro.'); window.location.href='register.html';</script>";
        exit();
    }

    // Manejo de la subida de la foto de perfil
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $profile_pic = 'uploads/' . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
    }

    // Encriptar la contraseña
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);

    // Insertar los datos en la base de datos
    $stmt = $conn->prepare("INSERT INTO users (username, full_name, email, password, profile_pic) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $username, $full_name, $email, $hashed_password, $profile_pic);

    if ($stmt->execute()) {
        echo "<script>alert('Registro exitoso. Ahora puedes iniciar sesión.'); window.location.href='index.html';</script>";
    } else {
        echo "<script>alert('Error en el registro. Inténtalo de nuevo.'); window.location.href='register.html';</script>";
    }
} else {
    header("Location: register.html");
    exit();
}
?>