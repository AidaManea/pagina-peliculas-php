<?php
require('db.php');

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $usernamePost = isset($_POST['user']) ? trim($_POST['user']) : '';
  $emailPost = isset($_POST['email']) ? trim($_POST['email']) : '';
  $passPost = isset($_POST['pass']) ? $_POST['pass'] : '';

  if ($usernamePost === '' || $emailPost === '' || $passPost === '') {
    header("Location: ../html/Registro.html");
    exit();
  }
  $stmt = $conn->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
  $stmt->bindParam(':username', $usernamePost);
  $stmt->bindParam(':email', $emailPost);
  $passwordHash = hash('sha256', $passPost);
  $stmt->bindParam(':password', $passwordHash);

  $stmt->execute();
  header("Location: ../html/login.html");
  exit();
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

$conn = null;
?>