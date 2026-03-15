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
    $checkVista = $conn->prepare("SELECT idpelicula FROM favoritas WHERE titulo = :titulo LIMIT 1");
    $checkVista->bindParam(':titulo', $title);
    $checkVista->execute();
    $vistaRow = $checkVista->fetch(PDO::FETCH_ASSOC);

    if ($vistaRow) {
        $del = $conn->prepare("DELETE FROM favoritas WHERE titulo = :titulo");
        $del->bindParam(':titulo', $title);
        $del->execute();

        echo json_encode(['success' => true, 'favorite' => false, 'message' => 'Película quitada de tu lista de favoritas.']);
    } else {
        $ins = $conn->prepare("INSERT INTO favoritas (titulo, genero, director, duracion, descripcion, valoracion, año) VALUES (:titulo, :genero, :director, :duracion, :descripcion, :valoracion, :anio)");
        $ins->bindParam(':titulo', $title);
        $ins->bindParam(':genero', $genero);
        $ins->bindParam(':director', $director);
        $ins->bindParam(':duracion', $duracion, PDO::PARAM_INT);
        $ins->bindParam(':descripcion', $descripcion);
        $ins->bindParam(':valoracion', $valoracion);
        $ins->bindParam(':anio', $anio);
        $ins->execute();

        echo json_encode(['success' => true, 'favorite' => true, 'message' => 'Película marcada como favorita.']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error al actualizar favoritas: ' . $e->getMessage()]);
}

