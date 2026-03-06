<?php
require('db.php');
    
try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  // set the PDO error mode to exception
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);


  //aquí empieza el insert 
  $stmt = $conn->prepare("INSERT INTO users (username, password)
  VALUES (:username, :password)");
  $stmt->bindParam(':username', $_POST['user']);
  $stmt->bindParam(':password', hash('sha256',$_POST['pass']));

  // use exec() because no results are returned
  $stmt->execute();
  header("Location: login.php");

} catch(PDOException $e) {
  echo $sql . "<br>" . $e->getMessage();
}

$conn = null;




?>