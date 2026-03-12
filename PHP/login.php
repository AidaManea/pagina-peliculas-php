<?php
require('db.php');
session_start();

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $usernamePost = isset($_POST['user']) ? trim($_POST['user']) : '';
  $passPost = isset($_POST['pass']) ? $_POST['pass'] : '';

  if ($usernamePost === '' || $passPost === '') {
    header("Location: ../html/login.html");
    exit();
  }

  $stmt = $conn->prepare("SELECT username, password FROM users WHERE username = :username LIMIT 1");
  $stmt->bindParam(':username', $usernamePost);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  $passHash = hash('sha256', $passPost);
  if (!$user || !isset($user['password']) || $user['password'] !== $passHash) {
    header("Location: ../html/login.html");
    exit();
  }

  $_SESSION['usuario'] = $user['username'];
  setcookie('usuario', $user['username'], time() + (86400 * 30), "/");

  header("Location: ../html/index.html");
  exit();
} catch(PDOException $e) {
  echo "Error: " . $e->getMessage();
}

$conn = null;
?>
