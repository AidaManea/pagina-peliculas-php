<?php
// Este archivo lo llama el api.js para guardar las peliculas que 
// trae de OMDB en nuestra propia base de datos.
// Asi las tenemos guardadas y no hay que pedirlas siempre a internet.

require('../db.php');

try {
  // Conectamos a la DB local
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Pillamos los datos que nos manda el script con un isset por si acaso alguno falta
  $titulo = isset($_POST['titulo']) ? $_POST['titulo'] : '';
  $genero = isset($_POST['genero']) ? $_POST['genero'] : 'Desconocido';
  $duracion = isset($_POST['duracion']) ? (int)$_POST['duracion'] : 0;
  $descripcion = isset($_POST['descripcion']) ? $_POST['descripcion'] : 'Sin descripción';
  $anio = isset($_POST['año']) ? $_POST['año'] : date('Y-m-d');
  $director = isset($_POST['director']) ? $_POST['director'] : 'Desconocido';
  $valoracion = isset($_POST['valoracion']) ? (float)$_POST['valoracion'] : 0;

  // El titulo es obligatorio, si no hay titulo no guardamos nada
  if ($titulo !== '') {
      
      // Vamos a mirar si ya tenemos esta pelicula guardada.
      // No queremos tener la misma peli en el catalogo dos veces.
      $check = $conn->prepare("SELECT idpelicula FROM pelis WHERE titulo = :titulo");
      $check->bindParam(':titulo', $titulo);
      $check->execute();
      
      if ($check->rowCount() == 0) {
          // Si no existe, la insertamos con un INSERT normal
          $stmt = $conn->prepare("INSERT INTO `pelis` (`titulo`, `genero`, `director`, `duracion`, `descripcion`, `valoracion`, `año`) 
                                 VALUES (:titulo, :genero, :director, :duracion, :descripcion, :valoracion, :anio)");
          $stmt->bindParam(':titulo', $titulo);
          $stmt->bindParam(':genero', $genero);
          $stmt->bindParam(':director', $director);
          $stmt->bindParam(':duracion', $duracion, PDO::PARAM_INT);
          $stmt->bindParam(':descripcion', $descripcion);
          $stmt->bindParam(':valoracion', $valoracion);
          $stmt->bindParam(':anio', $anio);

          $stmt->execute();
          echo "Peli guardada correctamente en el catalogo";
      } else {
          // Si ya estaba, no hacemos nada y avisamos por consola
          echo "La peli ya la teniamos en la base de datos";
      }
  } else {
      echo "Oye, que el titulo esta vacio";
  }

} catch(PDOException $e) {
  // Si algo falla al conectar o guardar
  echo "Vaya, ha habido un problema con la DB: " . $e->getMessage();
}
?>
