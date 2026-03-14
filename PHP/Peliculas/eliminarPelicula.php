<?php

require('../db.php');

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


  //aquí empieza el insert 
  $stmt = $conn->prepare("DELETE FROM filmora WHERE `pelis`.`idpelicula` = :id");
  $stmt->bindParam(':id', $_GET['id']);
  

  // use exec() because no results are returned
  $stmt->execute();
  header("Location: Añadir.php");

} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage();
}

$conn = null;


