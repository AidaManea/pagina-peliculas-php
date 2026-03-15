<?php
// Este archivo sirve para crear una cuenta nueva en Filmora.
// Pide los datos al usuario y los mete en la base de datos.

require('db.php');

// Cabecera para que el navegador sepa que le mandamos un JSON
header('Content-Type: application/json; charset=utf-8');

try {
  // Conectamos a la DB usando las variables de db.php
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Aqui pillamos todo lo que nos llega desde el formulario de registro (auth.js)
  // He usado los nombres que pusimos en los inputs
  $nombre = isset($_POST['user']) ? trim($_POST['user']) : '';
  $apellido = isset($_POST['apellido']) ? trim($_POST['apellido']) : '';
  $usuario = isset($_POST['usuario']) ? trim($_POST['usuario']) : '';
  $email = isset($_POST['email']) ? trim($_POST['email']) : '';
  $telefono = isset($_POST['numTelefono']) ? trim($_POST['numTelefono']) : '0';
  $fecha = isset($_POST['fechaNacimiento']) ? trim($_POST['fechaNacimiento']) : '2000-01-01';
  $pass = isset($_POST['pass']) ? $_POST['pass'] : '';

  // Miramos que no se deje lo mas importante vacio
  if ($nombre == '' || $usuario == '' || $email == '' || $pass == '') {
    echo json_encode(['success' => false, 'message' => '¡Oye! Faltan datos obligatorios para el registro.']);
    exit();
  }

  // Primero tenemos que mirar si el nombre de usuario o el email ya estan pillados por otro
  // No queremos tener dos personas con el mismo nombre de usuario.
  $sql_check = "SELECT * FROM usuarios WHERE usuario = ? OR email = ? LIMIT 1";
  $stmt_check = $pdo->prepare($sql_check);
  $stmt_check->execute([$usuario, $email]);

  if ($stmt_check->fetch()) {
    echo json_encode(['success' => false, 'message' => 'Vaya, el usuario o el email ya estan registrados. Prueba con otro.']);
    exit();
  }

  // Si todo esta bien, insertamos al nuevo usuario en la tabla
  $sql_insert = "INSERT INTO usuarios (nombre, email, contraseña, usuario, apellido, numTelefono, fechaNacimiento)
                 VALUES (?, ?, ?, ?, ?, ?, ?)";
  $stmt_insert = $pdo->prepare($sql_insert);
  $stmt_insert->execute([$nombre, $email, $pass, $usuario, $apellido, $telefono, $fecha]);
  
  // Lo logeamos directamente para que no tenga que hacerlo el despues.
  // Iniciamos la sesion.
  session_start();
  $_SESSION['usuario'] = $usuario;
  $_SESSION['usuario_id'] = (int)$pdo->lastInsertId();
  $_SESSION['usuario_email'] = $email;
  
  // Guardamos una cookie por si acaso se cierra el navegador pronto
  setcookie('usuario', $usuario, time() + (86400 * 30), "/");

  // Respuesta de que todo ha ido perfecto
  echo json_encode(['success' => true, 'message' => '¡Usuario registrado con éxito! Bienvenido.']);

} catch(Exception $e) {
  // Si algo falla soltamos el error
  echo json_encode(['success' => false, 'message' => 'Hubo un error raro: ' . $e->getMessage()]);
}
?>