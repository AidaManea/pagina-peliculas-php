<?php
require('db.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no iniciada.'
    ]);
    exit();
}

$usuarioNombre = $_SESSION['usuario'];

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Datos básicos del usuario (buscamos por nombre de usuario para no depender del nombre de la columna ID)
    $stmtUser = $conn->prepare("SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1");
    $stmtUser->bindParam(':usuario', $usuarioNombre, PDO::PARAM_STR);
    $stmtUser->execute();
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        echo json_encode([
            'success' => false,
            'message' => 'No se han encontrado datos del usuario.'
        ]);
        exit();
    }

    // Obtenemos el ID del usuario para usarlo en las tablas favoritas / vistas
    $usuarioId = 0;
    if (isset($user['idusuario'])) {
        $usuarioId = (int) $user['idusuario'];
    } elseif (isset($user['idUsuarios'])) {
        $usuarioId = (int) $user['idUsuarios'];
    } elseif (isset($user['idUsuario'])) {
        $usuarioId = (int) $user['idUsuario'];
    } elseif (isset($user['id'])) {
        $usuarioId = (int) $user['id'];
    }

    // Contadores basados en las tablas favoritas y vistas
    $totalFav = 0;
    $totalVista = 0;

    if ($usuarioId > 0) {
        try {
            $stmtFav = $conn->prepare("SELECT COUNT(*) AS total FROM favoritas WHERE idusuario = :id");
            $stmtFav->bindParam(':id', $usuarioId, PDO::PARAM_INT);
            $stmtFav->execute();
            $rowFav = $stmtFav->fetch(PDO::FETCH_ASSOC);
            $totalFav = (int) (isset($rowFav['total']) ? $rowFav['total'] : 0);
        } catch (PDOException $e) {
            // Si la tabla no existe, simplemente dejamos el contador a 0
        }

        try {
            $stmtVista = $conn->prepare("SELECT COUNT(*) AS total FROM vistas WHERE idusuario = :id");
            $stmtVista->bindParam(':id', $usuarioId, PDO::PARAM_INT);
            $stmtVista->execute();
            $rowVista = $stmtVista->fetch(PDO::FETCH_ASSOC);
            $totalVista = (int) (isset($rowVista['total']) ? $rowVista['total'] : 0);
        } catch (PDOException $e) {
            // Si la tabla no existe, simplemente dejamos el contador a 0
        }
    }

    echo json_encode([
        'success'        => true,
        'usuario'        => $user['usuario'],
        'email'          => isset($user['email']) ? $user['email'] : '',
        'totalFavoritas' => $totalFav,
        'totalVistas'    => $totalVista,
    ]);
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al obtener los datos del perfil.'
    ]);
}

