<?php
require('db.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $usernamePost = isset($_POST['user']) ? trim($_POST['user']) : '';
  $emailPost = isset($_POST['email']) ? trim($_POST['email']) : '';
  $passPost = isset($_POST['pass']) ? $_POST['pass'] : '';

  if ($usernamePost === '' || $emailPost === '' || $passPost === '') {
    echo json_encode([
      'success' => false,
      'message' => 'Faltan usuario, correo o contraseña.'
    ]);
    exit();
  }

  // En la base de datos la tabla es "usuarios" y el campo de login es "usuario".
  // Seleccionamos todo para no depender del nombre concreto de la columna ID.
  // Validamos tanto usuario como email.
  $stmt = $conn->prepare("SELECT * FROM usuarios WHERE usuario = :usuario AND email = :email LIMIT 1");
  $stmt->bindParam(':usuario', $usernamePost);
  $stmt->bindParam(':email', $emailPost);
  $stmt->execute();
  $user = $stmt->fetch(PDO::FETCH_ASSOC);

  // Usamos la contraseña tal y como está almacenada en la tabla
  if (!$user || !isset($user['contraseña']) || $user['contraseña'] !== $passPost) {
    echo json_encode([
      'success' => false,
      'message' => 'Usuario, correo o contraseña incorrectos.'
    ]);
    exit();
  }

  $_SESSION['usuario'] = $user['usuario'];

  // Intentamos obtener el ID del usuario sin asumir un nombre fijo de columna
  $usuarioId = 0;
  if (isset($user['idusuario'])) {
    $usuarioId = (int) $user['idusuario'];
  } elseif (isset($user['idUsuario'])) {
    $usuarioId = (int) $user['idUsuario'];
  } elseif (isset($user['id'])) {
    $usuarioId = (int) $user['id'];
  }

  $_SESSION['usuario_id'] = $usuarioId;
  if (isset($user['email'])) {
    $_SESSION['usuario_email'] = $user['email'];
  }
  setcookie('usuario', $user['usuario'], time() + (86400 * 30), "/");

  echo json_encode([
    'success' => true,
    'message' => 'Login correcto.'
  ]);
} catch(PDOException $e) {
  echo json_encode([
    'success' => false,
    'message' => 'Error en el servidor. Inténtalo de nuevo más tarde.'
  ]);
}

$conn = null;
?>
