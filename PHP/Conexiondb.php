<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "peliculas";

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


echo "Conexión exitosa";
} catch(PDOException $e) {
  echo "Error de conexión: " . $e->getMessage();
}

?>