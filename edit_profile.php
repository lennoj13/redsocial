<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: index.html");
    exit();
}

$username = $_SESSION['user_data']['username'];
$stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Usuario no encontrado.";
    exit();
}

$user_data = $result->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_username = $_POST['username'];
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $profile_pic = $user_data['profile_pic'];

    // Verificar si el nuevo nombre de usuario ya existe
    if ($new_username !== $username) {
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ?");
        $stmt->bind_param("s", $new_username);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            echo "<script>alert('El nombre de usuario ya está registrado. Por favor, elige otro.'); window.location.href='edit_profile.php';</script>";
            exit();
        }
    }

    // Manejo de la subida de la foto de perfil
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $profile_pic = 'uploads/' . basename($_FILES['profile_pic']['name']);
        move_uploaded_file($_FILES['profile_pic']['tmp_name'], $profile_pic);
    }

    // Actualizar los datos en la base de datos
    $stmt = $conn->prepare("UPDATE users SET username = ?, full_name = ?, email = ?, profile_pic = ? WHERE username = ?");
    $stmt->bind_param("sssss", $new_username, $full_name, $email, $profile_pic, $username);
    $stmt->execute();

    // Actualizar los datos de la sesión
    $_SESSION['user_data']['username'] = $new_username;
    $_SESSION['user_data']['full_name'] = $full_name;
    $_SESSION['user_data']['email'] = $email;
    $_SESSION['user_data']['profile_pic'] = $profile_pic;

    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Perfil</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Editar Perfil</h1>
    </header>
    <div class="container">
        <form action="edit_profile.php" method="post" enctype="multipart/form-data">
            <label for="username">Nombre de Usuario:</label>
            <input type="text" id="username" name="username" value="<?= htmlspecialchars($user_data['username']) ?>" required>

            <label for="full_name">Nombre Completo:</label>
            <input type="text" id="full_name" name="full_name" value="<?= htmlspecialchars($user_data['full_name']) ?>" required>

            <label for="email">Correo Electrónico:</label>
            <input type="email" id="email" name="email" value="<?= htmlspecialchars($user_data['email']) ?>" required>

            <label for="profile_pic">Foto de Perfil:</label>
            <input type="file" id="profile_pic" name="profile_pic" accept="image/*">

            <input type="submit" value="Guardar Cambios">
        </form>
    </div>
</body>
</html>