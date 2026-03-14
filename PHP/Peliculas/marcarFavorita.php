<?php
require('../db.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida. Vuelve a iniciar sesión.'
    ]);
    exit();
}

$usuarioId = (int) $_SESSION['usuario_id'];

$movieId = $_POST['movieId'] ?? '';
$title = $_POST['title'] ?? '';
$year = $_POST['year'] ?? '';
$director = $_POST['director'] ?? '';

if ($movieId === '' || $title === '') {
    echo json_encode([
        'success' => false,
        'message' => 'Datos de película incompletos.'
    ]);
    exit();
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Aseguramos que exista una entrada en la tabla de pelis
    $stmt = $conn->prepare("SELECT idpelicula FROM pelis WHERE titulo = :titulo LIMIT 1");
    $stmt->bindParam(':titulo', $title);
    $stmt->execute();
    $peli = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$peli) {
        $insertPeli = $conn->prepare(
            "INSERT INTO pelis (titulo, genero, director, duracion, descripcion, valoracion, año)
             VALUES (:titulo, :genero, :director, :duracion, :descripcion, :valoracion, :anio)"
        );

        $anioVal = $year !== '' ? $year : date('Y-m-d');
        $duracionVal = 0;
        $valoracionVal = 0;
        $descripcionVal = '';
        $generoVal = '';

        $insertPeli->bindParam(':titulo', $title);
        $insertPeli->bindParam(':genero', $generoVal);
        $insertPeli->bindParam(':director', $director);
        $insertPeli->bindParam(':duracion', $duracionVal, PDO::PARAM_INT);
        $insertPeli->bindParam(':descripcion', $descripcionVal);
        $insertPeli->bindParam(':valoracion', $valoracionVal);
        $insertPeli->bindParam(':anio', $anioVal);
        $insertPeli->execute();

        $peliId = (int) $conn->lastInsertId();
    } else {
        $peliId = (int) $peli['idpelicula'];
    }

    // Comprobar si ya está como favorita para este usuario
    $checkFav = $conn->prepare(
        "SELECT id FROM favoritas WHERE idusuario = :idusuario AND idpelicula = :idpelicula LIMIT 1"
    );
    $checkFav->bindParam(':idusuario', $usuarioId, PDO::PARAM_INT);
    $checkFav->bindParam(':idpelicula', $peliId, PDO::PARAM_INT);
    $checkFav->execute();
    $favRow = $checkFav->fetch(PDO::FETCH_ASSOC);

    if ($favRow) {
        // Si existe, la quitamos (toggle off)
        $del = $conn->prepare("DELETE FROM favoritas WHERE id = :id");
        $del->bindParam(':id', $favRow['id']);
        $del->execute();

        echo json_encode([
            'success'   => true,
            'favorite'  => false,
            'message'   => 'Película eliminada de favoritos.'
        ]);
    } else {
        // Si no existe, la añadimos
        $ins = $conn->prepare(
            "INSERT INTO favoritas (idusuario, idpelicula, creado_en)
             VALUES (:idusuario, :idpelicula, NOW())"
        );
        $ins->bindParam(':idusuario', $usuarioId, PDO::PARAM_INT);
        $ins->bindParam(':idpelicula', $peliId, PDO::PARAM_INT);
        $ins->execute();

        echo json_encode([
            'success'   => true,
            'favorite'  => true,
            'message'   => 'Película añadida a favoritos.'
        ]);
    }
} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error al actualizar favoritos en la base de datos.'
    ]);
}

