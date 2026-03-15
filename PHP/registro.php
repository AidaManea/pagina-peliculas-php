<?php
require('db.php');

header('Content-Type: application/json; charset=utf-8');

try {
  $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  $nombrePost = isset($_POST['user']) ? trim($_POST['user']) : '';
  $apellidoPost = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
  $usuarioPost = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
  $emailPost = isset($_POST['email']) ? trim($_POST['email']) : '';
  $numTelefonoPost = isset($_POST['numTelefono']) ? trim($_POST['numTelefono']) : '0';
  $fechaNacimientoPost = isset($_POST['fechaNacimiento']) ? trim($_POST['fechaNacimiento']) : '2000-01-01';
  $passPost = isset($_POST['pass']) ? $_POST['pass'] : '';

  if ($nombrePost === '' || $apellidoPost === '' || $usuarioPost === '' || $emailPost === '' || $passPost === '') {
    echo json_encode([
      'success' => false,
      'message' => 'Faltan datos por rellenar en el formulario.'
    ]);
    exit();
  }

  // Comprobar que no exista ya el usuario o el correo en la tabla "usuarios"
  // No dependemos del nombre de la columna ID, solo queremos saber si hay alguna fila
  $check = $conn->prepare("SELECT 1 FROM usuarios WHERE usuario = :usuario OR email = :email LIMIT 1");
  $check->bindParam(':usuario', $usuarioPost);
  $check->bindParam(':email', $emailPost);
  $check->execute();

  if ($check->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode([
      'success' => false,
      'message' => 'El usuario o el email ya están registrados.'
    ]);
    exit();
  }

  $stmt = $conn->prepare(
    "INSERT INTO usuarios (nombre, email, contraseña, usuario, apellido, numTelefono, fechaNacimiento)
     VALUES (:nombre, :email, :contrasena, :usuario, :apellido, :telefono, :fechaNac)"
  );

  $stmt->bindParam(':nombre', $nombrePost);
  $stmt->bindParam(':email', $emailPost);
  $stmt->bindParam(':contrasena', $passPost);
  $stmt->bindParam(':usuario', $usuarioPost);
  $stmt->bindParam(':apellido', $apellidoPost);
  $stmt->bindParam(':telefono', $numTelefonoPost, PDO::PARAM_INT);
  $stmt->bindParam(':fechaNac', $fechaNacimientoPost);

  $stmt->execute();
  
  // Start session and log user in immediately after registration
  session_start();
  $_SESSION['usuario'] = $usuarioPost;
  
  // Try to get the newly created user ID
  $userId = $conn->lastInsertId();
  $_SESSION['usuario_id'] = (int) $userId;
  $_SESSION['usuario_email'] = $emailPost;
  
  // Set cookie for consistency with login.php
  setcookie('usuario', $usuarioPost, time() + (86400 * 30), "/");

  echo json_encode([
    'success' => true,
    'message' => 'Usuario registrado correctamente.'
  ]);
} catch(PDOException $e) {
  echo json_encode([
    'success' => false,
    // Durante el desarrollo mostramos el mensaje real para poder ver qué está fallando.
    'message' => $e->getMessage()
  ]);
}

$conn = null;
?>