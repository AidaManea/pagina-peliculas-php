<?php
// Este archivo sirve para que cuando el usuario le de a la estrella, 
// se guarde la peli como favorita en la base de datos.
// Tambien sirve para quitarla si ya estaba.

require('../db.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

// Pillamos los datos que nos manda el favoritos.js
$title = $_POST['title'] ?? '';
$genero = $_POST['genero'] ?? 'Desconocido';
$director = $_POST['director'] ?? 'Desconocido';
$duracion = (int)($_POST['duracion'] ?? 0);
$descripcion = $_POST['descripcion'] ?? 'Sin descripción';
$valoracion = (float)($_POST['valoracion'] ?? 0);
$anio = $_POST['año'] ?? date('Y-m-d');

// El titulo es lo minimo para saber de que peli hablamos
if ($title === '') {
    echo json_encode(['success' => false, 'message' => 'Vaya, el título de la película está vacío.']);
    exit();
}

try {
    // Conectamos a la DB
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Primero miramos si el usuario ya tiene esta peli en favoritas.
    // Buscamos por titulo.
    $checkVista = $conn->prepare("SELECT idpelicula FROM favoritas WHERE titulo = :titulo LIMIT 1");
    $checkVista->bindParam(':titulo', $title);
    $checkVista->execute();
    $vistaRow = $checkVista->fetch(PDO::FETCH_ASSOC);

    if ($vistaRow) {
        // Si ya está, significa que el usuario quiere QUITARLA.
        $del = $conn->prepare("DELETE FROM favoritas WHERE titulo = :titulo");
        $del->bindParam(':titulo', $title);
        $del->execute();

        echo json_encode(['success' => true, 'favorite' => false, 'message' => 'Ya no es favorita.']);
    } else {
        // Si no está, la AÑADIMOS a la tabla de favoritas.
        $ins = $conn->prepare("INSERT INTO favoritas (titulo, genero, director, duracion, descripcion, valoracion, año) 
                               VALUES (:titulo, :genero, :director, :duracion, :descripcion, :valoracion, :anio)");
        $ins->bindParam(':titulo', $title);
        $ins->bindParam(':genero', $genero);
        $ins->bindParam(':director', $director);
        $ins->bindParam(':duracion', $duracion, PDO::PARAM_INT);
        $ins->bindParam(':descripcion', $descripcion);
        $ins->bindParam(':valoracion', $valoracion);
        $ins->bindParam(':anio', $anio);
        $ins->execute();

        echo json_encode(['success' => true, 'favorite' => true, 'message' => '¡Guardada en favoritas!']);
    }
} catch (PDOException $e) {
    // Por si algo sale mal con la base de datos
    echo json_encode(['success' => false, 'message' => 'Error al intentar actualizar tus favoritas.']);
}
?>

