<?php
session_start();
include 'database.php';

if (!isset($_SESSION['user_data'])) {
    header("Location: index.html");
    exit();
}

$query = $_GET['query'];
$stmt = $conn->prepare("SELECT username, full_name, profile_pic FROM users WHERE username LIKE ? OR full_name LIKE ?");
$search_query = "%" . $query . "%";
$stmt->bind_param("ss", $search_query, $search_query);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buscar Usuarios</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .user-result {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            padding: 10px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .user-result img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            margin-right: 10px;
        }
        .user-result a {
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <header>
        <h1>Resultados de Búsqueda</h1>
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
        <?php while ($user = $result->fetch_assoc()): ?>
            <div class="user-result">
                <img src="<?= htmlspecialchars($user['profile_pic'] ?: 'default_profile.png') ?>" alt="Foto de Perfil">
                <a href="user_profile.php?username=<?= htmlspecialchars($user['username']) ?>">
                    <?= htmlspecialchars($user['full_name']) ?> (<?= htmlspecialchars($user['username']) ?>)
                </a>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>