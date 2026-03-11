<?php
require('db.php');

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  
  //aquí empieza el insert 
  $stmt = $conn->prepare("UPDATE `pelis` SET `titulo`=:titulo,`genero`= :genero,`duracion`=:duracion,`descripcion`=:descripcion,
  `año`=:año,`estrellas`=:estrellas,`director`=:director WHERE `pelis`.`id` = :id");
  $stmt->bindParam(':titulo', $_POST['titulo']);
  $stmt->bindParam(':genero', $_POST['genero']);
  $stmt->bindParam(':duracion', $_POST['duracion']);
  $stmt->bindParam(':descripcion', $_POST['descripcion']);
  $stmt->bindParam(':año', $_POST['año']);
  $stmt->bindParam(':estrellas', $_POST['estrellas']);
  $stmt->bindParam(':director', $_POST['director']);
  $stmt->bindParam(':id', $_POST['id']);
  // use exec() because no results are returned
  $stmt->execute();
  header("Location: .php");//lanzar un mensaje de confirmacion en javaSrcipt


echo "Conexión exitosa";
} catch(PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
}

?>