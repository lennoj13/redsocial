<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: index.html");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_SESSION['user_data']['username'];
    $text = $_POST['text'];
    $image = '';
    $video = '';

    // Manejo de la subida de la imagen
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $image = 'uploads/' . basename($_FILES['image']['name']);
        move_uploaded_file($_FILES['image']['tmp_name'], $image);
    }

    // Manejo de la subida del video
    if (isset($_FILES['video']) && $_FILES['video']['error'] == 0) {
        $video = 'uploads/' . basename($_FILES['video']['name']);
        move_uploaded_file($_FILES['video']['tmp_name'], $video);
    }

    $stmt = $conn->prepare("INSERT INTO posts (username, text, image, video) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $username, $text, $image, $video);
    $stmt->execute();

    header("Location: feed.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Publicación</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <header>
        <h1>Crear Publicación</h1>
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
        <form action="post.php" method="post" enctype="multipart/form-data">
            <label for="text">Texto:</label>
            <textarea id="text" name="text" required></textarea>

            <label for="image">Imagen:</label>
            <input type="file" id="image" name="image" accept="image/*">

            <label for="video">Video:</label>
            <input type="file" id="video" name="video" accept="video/*">

            <input  type="submit" value="Publicar">
        </form>
    </div>

</body>
<footer>
        <p>&copy; 2024 Nuestra Red Social. Todos los derechos reservados.</p>
    </footer>
</html>