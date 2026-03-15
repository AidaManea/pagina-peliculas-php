<?php
require('../db.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

$title = $_POST['title'] ?? '';
$genero = $_POST['genero'] ?? 'Desconocido';
$director = $_POST['director'] ?? 'Desconocido';
$duracion = (int)($_POST['duracion'] ?? 0);
$descripcion = $_POST['descripcion'] ?? 'Sin descripción';
$valoracion = (float)($_POST['valoracion'] ?? 0);
$anio = $_POST['año'] ?? date('Y-m-d');

if ($title === '') {
    echo json_encode(['success' => false, 'message' => 'Título de película incompleto.']);
    exit();
}

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Comprobar si ya está
    $checkVista = $conn->prepare("SELECT idpelicula FROM vistas WHERE titulo = :titulo LIMIT 1");
    $checkVista->bindParam(':titulo', $title);
    $checkVista->execute();
    $vistaRow = $checkVista->fetch(PDO::FETCH_ASSOC);

    if ($vistaRow) {
        $del = $conn->prepare("DELETE FROM vistas WHERE titulo = :titulo");
        $del->bindParam(':titulo', $title);
        $del->execute();

        echo json_encode(['success' => true, 'seen' => false, 'message' => 'Película quitada de tu lista de vistas.']);
    } else {
        $ins = $conn->prepare("INSERT INTO vistas (titulo, genero, director, duracion, descripcion, valoracion, año) VALUES (:titulo, :genero, :director, :duracion, :descripcion, :valoracion, :anio)");
        $ins->bindParam(':titulo', $title);
        $ins->bindParam(':genero', $genero);
        $ins->bindParam(':director', $director);
        $ins->bindParam(':duracion', $duracion, PDO::PARAM_INT);
        $ins->bindParam(':descripcion', $descripcion);
        $ins->bindParam(':valoracion', $valoracion);
        $ins->bindParam(':anio', $anio);
        $ins->execute();

        echo json_encode(['success' => true, 'seen' => true, 'message' => 'Película marcada como vista.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar vistas: ' . $e->getMessage()]);
}

