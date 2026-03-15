<?php
require('../db.php');
header('Content-Type: application/json; charset=utf-8');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $stmt = $conn->query("SELECT idpelicula, titulo, genero, director, duracion, descripcion, valoracion, año FROM pelis ORDER BY idpelicula DESC");
    $peliculas = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'data' => $peliculas]);

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error SQL: ' . $e->getMessage()]);
}
?>
