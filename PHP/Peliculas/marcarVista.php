<?php
// Este archivo sirve para marcar una pelicula como "vista".
// Cuando el usuario le da al check en la tarjetita, venimos aqui.

require('../db.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

// Los datos nos llegan por POST desde favoritos.js
$title = $_POST['title'] ?? '';
$genero = $_POST['genero'] ?? 'Desconocido';
$director = $_POST['director'] ?? 'Desconocido';
$duracion = (int)($_POST['duracion'] ?? 0);
$descripcion = $_POST['descripcion'] ?? 'Sin descripción';
$valoracion = (float)($_POST['valoracion'] ?? 0);
$anio = $_POST['año'] ?? date('Y-m-d');

if ($title === '') {
    echo json_encode(['success' => false, 'message' => 'No me has pasado el titulo de la peli.']);
    exit();
}

try {
    // Conectamos a MySQL
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Miramos si ya la habiamos marcado como vista antes
    $checkVista = $conn->prepare("SELECT idpelicula FROM vistas WHERE titulo = :titulo LIMIT 1");
    $checkVista->bindParam(':titulo', $title);
    $checkVista->execute();
    $vistaRow = $checkVista->fetch(PDO::FETCH_ASSOC);

    if ($vistaRow) {
        // Si ya estaba, es que el usuario quiere DESMARCARLA
        $del = $conn->prepare("DELETE FROM vistas WHERE titulo = :titulo");
        $del->bindParam(':titulo', $title);
        $del->execute();

        echo json_encode(['success' => true, 'seen' => false, 'message' => 'Peli quitada de vistas.']);
    } else {
        // Si no estaba, la metemos en la tabla de vistas
        $ins = $conn->prepare("INSERT INTO vistas (titulo, genero, director, duracion, descripcion, valoracion, año) 
                               VALUES (:titulo, :genero, :director, :duracion, :descripcion, :valoracion, :anio)");
        $ins->bindParam(':titulo', $title);
        $ins->bindParam(':genero', $genero);
        $ins->bindParam(':director', $director);
        $ins->bindParam(':duracion', $duracion, PDO::PARAM_INT);
        $ins->bindParam(':descripcion', $descripcion);
        $ins->bindParam(':valoracion', $valoracion);
        $ins->bindParam(':anio', $anio);
        $ins->execute();

        echo json_encode(['success' => true, 'seen' => true, 'message' => '¡Peli marcada como vista!']);
    }
} catch (PDOException $e) {
    // Error generico por si falla la DB
    echo json_encode(['success' => false, 'message' => 'Error al intentar guardar que has visto la peli.']);
}
?>

