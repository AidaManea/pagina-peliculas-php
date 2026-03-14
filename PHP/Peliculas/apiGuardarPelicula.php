<?php
require('../db.php');

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Obtener datos del POST
  $titulo = $_POST['titulo'] ?? '';
  $genero = $_POST['genero'] ?? 'Desconocido';
  $duracion = $_POST['duracion'] ?? '0 min';
  $descripcion = $_POST['descripcion'] ?? 'Sin descripción';
  $anio = $_POST['año'] ?? date('Y-m-d');
  $estrellas = $_POST['estrellas'] ?? 'Desconocidos';
  $director = $_POST['director'] ?? 'Desconocido';

  if ($titulo !== '') {
      // Comprobar si ya existe una pelicula con ese titulo (opcional, para no duplicar)
      $check = $conn->prepare("SELECT id FROM pelis WHERE titulo = :titulo");
      $check->bindParam(':titulo', $titulo);
      $check->execute();
      
      if ($check->rowCount() == 0) {
          $stmt = $conn->prepare("INSERT INTO `pelis` (`titulo`, `genero`, `duracion`, `descripcion`, `anio`, `estrellas`, `director`) VALUES (:titulo, :genero, :duracion, :descripcion, :anio, :estrellas, :director)");
          $stmt->bindParam(':titulo', $titulo);
          $stmt->bindParam(':genero', $genero);
          $stmt->bindParam(':duracion', $duracion);
          $stmt->bindParam(':descripcion', $descripcion);
          $stmt->bindParam(':anio', $anio);
          $stmt->bindParam(':estrellas', $estrellas);
          $stmt->bindParam(':director', $director);

          $stmt->execute();
          echo "Película insertada correctamente";
      } else {
          echo "La película ya existe en la base de datos";
      }
  } else {
      echo "El título no puede estar vacío";
  }

} catch(PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
}
?>
