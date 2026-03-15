<?php
// Primero de todo, activamos las sesiones para que el servidor sepa quien es quien
session_start();

// Importamos el archivo que tiene la conexion
require('db.php');

// Decimos que vamos a responder en formato JSON, que es mas facil para el JS
header('Content-Type: application/json; charset=utf-8');

try {
  // Conectamos a la base de datos con PDO
  $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
  $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

  // Aqui pillamos lo que el usuario ha escrito en el login
  $userPost = isset($_POST['user']) ? trim($_POST['user']) : '';
  $emailPost = isset($_POST['email']) ? trim($_POST['email']) : '';
  $passPost = isset($_POST['pass']) ? $_POST['pass'] : '';

  // Miramos que no se haya dejado nada vacio, porque si no no podemos buscar
  if ($userPost == '' || $emailPost == '' || $passPost == '') {
    echo json_encode(['success' => false, 'message' => 'Oye, rellena todos los campos.']);
    exit();
  }

  // Buscamos en la tabla de usuarios. He puesto que coincidan usuario y email 
  // para que sea mas seguro, como en los requisitos.
  $consulta = $pdo->prepare("SELECT * FROM usuarios WHERE usuario = ? AND email = ? LIMIT 1");
  $consulta->execute([$userPost, $emailPost]);
  $fila = $consulta->fetch(PDO::FETCH_ASSOC);

  // Si existe el usuario, miramos si la contraseña es la misma
  if ($fila && $fila['contraseña'] == $passPost) {
    
    // Guardamos los datos importantes en la SESION
    $_SESSION['usuario'] = $fila['usuario'];
    
    // El ID puede llamarse de varias formas segun la DB, asi que pruebo varias
    $id = 0;
    if (isset($fila['idusuario'])) $id = $fila['idusuario'];
    else if (isset($fila['idUsuario'])) $id = $fila['idUsuario'];
    else if (isset($fila['id'])) $id = $fila['id'];
    
    $_SESSION['usuario_id'] = (int)$id;
    $_SESSION['usuario_email'] = $fila['email'];
    
    // Tambien pongo una cookie por si la sesion se borra pronto
    setcookie('usuario', $fila['usuario'], time() + (86400 * 30), "/");

    // Todo ha ido bien!
    echo json_encode(['success' => true, 'message' => '¡Genial! Has entrado.']);
  } else {
    // Si no coincide, le avisamos sin darle muchas pistas de que ha fallado
    echo json_encode(['success' => false, 'message' => 'El usuario o la contraseña no son correctos.']);
  }

} catch(Exception $e) {
  // Si algo falla en el servidor, soltamos este error generico
  echo json_encode(['success' => false, 'message' => 'Vaya, parece que hay un problema en el servidor.']);
}
?>
