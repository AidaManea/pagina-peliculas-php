<?php
// Este archivo lo usamos para traer los datos del usuario que esta logeado
// Asi podemos enseñarlos en la pagina de perfil o en el menu.

require('db.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

// Si no hay sesion, es que no se ha logeado o se ha caducado
if (!isset($_SESSION['usuario'])) {
    echo json_encode(['success' => false, 'message' => 'No hay sesion activa ahora mismo.']);
    exit();
}

// Sacamos el nombre de usuario de la sesion que guardamos en el login
$nombreUsuario = $_SESSION['usuario'];

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    
    // Vamos a buscar toda la info del usuario en la tabla
    $sql = "SELECT * FROM usuarios WHERE usuario = ? LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$nombreUsuario]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    // Por si acaso, si no lo encuentra (raro si hay sesion, pero bueno)
    if (!$usuario) {
        echo json_encode(['success' => false, 'message' => 'Vaya, no encuentro al usuario en la base de datos.']);
        exit();
    }

    // El ID es muy importante para luego buscar sus pelis. 
    // Lo sacamos probando varios nombres de columna por si acaso.
    $id = 0;
    if (isset($usuario['idusuario'])) $id = (int)$usuario['idusuario'];
    else if (isset($usuario['idUsuario'])) $id = (int)$usuario['idUsuario'];
    else if (isset($usuario['id'])) $id = (int)$usuario['id'];

    // Vamos a ver cuantas pelis tiene el usuario en favoritos y vistas
    $totalFavoritas = 0;
    $totalVistas = 0;

    if ($id > 0) {
        // Buscamos cuantas filas hay en la tabla de favoritas para este ID
        $resFav = $pdo->prepare("SELECT COUNT(*) FROM favoritas WHERE idusuario = ?");
        $resFav->execute([$id]);
        $totalFavoritas = (int)$resFav->fetchColumn();

        // Lo mismo para las pelis que ha marcado como vistas
        $resVis = $pdo->prepare("SELECT COUNT(*) FROM vistas WHERE idusuario = ?");
        $resVis->execute([$id]);
        $totalVistas = (int)$resVis->fetchColumn();
    }

    // Devolvemos todo al JavaScript para que lo pinte en la web
    echo json_encode([
        'success' => true,
        'usuario' => $usuario['usuario'],
        'email' => isset($usuario['email']) ? $usuario['email'] : '',
        'totalFavoritas' => $totalFavoritas,
        'totalVistas' => $totalVistas
    ]);

} catch (Exception $e) {
    // Si algo explota enviamos este error
    echo json_encode(['success' => false, 'message' => 'Error al intentar cargar los datos del perfil.']);
}
?>

