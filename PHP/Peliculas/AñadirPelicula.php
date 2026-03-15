<?php
require('../db.php');
header('Content-Type: application/json; charset=utf-8');

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
    $genero = isset($_POST['genero']) ? $_POST['genero'] : '';
    $duracion = isset($_POST['duracion']) ? (int)$_POST['duracion'] : 0;
    $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : '';
    $ano = isset($_POST['año']) ? $_POST['año'] : '';
    $director = isset($_POST['director']) ? $_POST['director'] : '';
    $valoracion = isset($_POST['valoracion']) ? (float)$_POST['valoracion'] : 0.0;

    if (empty($titulo) || empty($director)) {
        echo json_encode(['success' => false, 'message' => 'Faltan datos obligatorios.']);
        exit;
    }

    $stmt = $conn->prepare("INSERT INTO pelis 
        (titulo, genero, duracion, descripcion, año, valoracion, director) 
        VALUES (:titulo, :genero, :duracion, :descripcion, :ano, :valoracion, :director)");
        
    $stmt->bindParam(':titulo', $titulo);
    $stmt->bindParam(':genero', $genero);
    $stmt->bindParam(':duracion', $duracion, PDO::PARAM_INT);
    $stmt->bindParam(':descripcion', $descripcion);
    $stmt->bindParam(':ano', $ano);
    $stmt->bindParam(':valoracion', $valoracion);
    $stmt->bindParam(':director', $director);

    $stmt->execute();
    
    echo json_encode(['success' => true, 'message' => 'Pelicula guardada con exito.']);

} catch(PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión: ' . $e->getMessage()]);
}
?>