<?php
require('../Conexiondb.php');

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

   //aquí empieza el insert 
  $stmt = $conn->prepare("INSERT INTO `pelis`
  (`id`, `titulo`, `genero`, `duracion`, `descripcion`, `anio`,`estrellas`,`director`) 
  VALUES (NULL, :titulo, :genero, :duracion, :descripcion, :anio , :estrellas, :director)");
  $stmt->bindParam(':titulo', $_POST['titulo']);
  $stmt->bindParam(':genero', $_POST['genero']);
  $stmt->bindParam(':duracion', $_POST['duracion']);
  $stmt->bindParam(':descripcion', $_POST['descripcion']);
  $stmt->bindParam(':año', $_POST['año']);
  $stmt->bindParam(':estrellas', $_POST['estrellas']);
  $stmt->bindParam(':director', $_POST['director']);


  $stmt->execute();
  header("Location: /html/registro.php");//< -- CAMBAIR LA RUTA DEL JS

echo "Conexión exitosa";
} catch(PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
}
?>