<?php
require('db.php');

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
      'message' => 'Rellena todos los campos.'
    ]);
    exit();
  }

  // Comprobar que no exista ya el usuario o el correo en la tabla "usuarios"
  // No dependemos del nombre de la columna ID, solo queremos saber si hay alguna fila
  $check = $conn->prepare("SELECT 1 FROM usuarios WHERE usuario = :usuario OR email = :email LIMIT 1");
  $check->bindParam(':usuario', $usernamePost);
  $check->bindParam(':email', $emailPost);
  $check->execute();

  if ($check->fetch(PDO::FETCH_ASSOC)) {
    echo json_encode([
      'success' => false,
      'message' => 'El usuario o el email ya están registrados.'
    ]);
    exit();
  }

  // En tu tabla "usuarios" existen más campos (apellido, numTelefono, fechaNacimiento).
  // De momento guardamos valores por defecto para que el registro no falle.
  $apellidoDefecto = '';
  $telefonoDefecto = 0;
  $fechaNacDefecto = '2000-01-01';

  $stmt = $conn->prepare(
    "INSERT INTO usuarios (nombre, email, contraseña, usuario, apellido, numTelefono, fechaNacimiento)
     VALUES (:nombre, :email, :contrasena, :usuario, :apellido, :telefono, :fechaNac)"
  );

  $stmt->bindParam(':nombre', $usernamePost);
  $stmt->bindParam(':email', $emailPost);
  $stmt->bindParam(':contrasena', $passPost);
  $stmt->bindParam(':usuario', $usernamePost);
  $stmt->bindParam(':apellido', $apellidoDefecto);
  $stmt->bindParam(':telefono', $telefonoDefecto, PDO::PARAM_INT);
  $stmt->bindParam(':fechaNac', $fechaNacDefecto);

  $stmt->execute();
  
  // Start session and log user in immediately after registration
  session_start();
  $_SESSION['usuario'] = $usernamePost;
  
  // Try to get the newly created user ID
  $userId = $conn->lastInsertId();
  $_SESSION['usuario_id'] = (int) $userId;
  $_SESSION['usuario_email'] = $emailPost;
  
  // Set cookie for consistency with login.php
  setcookie('usuario', $usernamePost, time() + (86400 * 30), "/");

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