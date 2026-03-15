<?php
require('../db.php');

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Obtener datos del POST
  $titulo = $_POST['titulo'] ?? '';
  $genero = $_POST['genero'] ?? 'Desconocido';
  $duracion = (int)($_POST['duracion'] ?? 0);
  $descripcion = $_POST['descripcion'] ?? 'Sin descripción';
  $anio = $_POST['año'] ?? date('Y-m-d');
  
  // Stars / Director
  $director = $_POST['director'] ?? 'Desconocido';
  $valoracion = (float)($_POST['valoracion'] ?? 0);

  if ($titulo !== '') {
      // Comprobar si ya existe una pelicula con ese titulo (opcional, para no duplicar)
      $check = $conn->prepare("SELECT idpelicula FROM pelis WHERE titulo = :titulo");
      $check->bindParam(':titulo', $titulo);
      $check->execute();
      
      if ($check->rowCount() == 0) {
          $stmt = $conn->prepare("INSERT INTO `pelis` (`titulo`, `genero`, `director`, `duracion`, `descripcion`, `valoracion`, `año`) VALUES (:titulo, :genero, :director, :duracion, :descripcion, :valoracion, :anio)");
          $stmt->bindParam(':titulo', $titulo);
          $stmt->bindParam(':genero', $genero);
          $stmt->bindParam(':director', $director);
          $stmt->bindParam(':duracion', $duracion, PDO::PARAM_INT);
          $stmt->bindParam(':descripcion', $descripcion);
          $stmt->bindParam(':valoracion', $valoracion);
          $stmt->bindParam(':anio', $anio);

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
