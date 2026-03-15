<?php
require('db.php');
session_start();

header('Content-Type: application/json; charset=utf-8');

if (!isset($_SESSION['usuario']) || !isset($_SESSION['usuario_id'])) {
    echo json_encode([
        'success' => false,
        'message' => 'Sesión no válida. Vuelve a iniciar sesión.'
    ]);
    exit();
}

$usuarioId = (int) $_SESSION['usuario_id'];
$action = isset($_POST['action']) ? $_POST['action'] : '';

try {
    $conn = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Obtener información actual del usuario para validar
    $stmtUser = $conn->prepare("SELECT * FROM usuarios WHERE idusuario = :id LIMIT 1");
    // Puede que la columna ID no sea idusuario, comprobamos varias opciones como en otros archivos
    // Para simplificar aquí la buscaremos por el nombre de usuario de la sesión
    $stmtUser = $conn->prepare("SELECT * FROM usuarios WHERE usuario = :usuario LIMIT 1");
    $stmtUser->bindParam(':usuario', $_SESSION['usuario']);
    $stmtUser->execute();
    $user = $stmtUser->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
         echo json_encode([
             'success' => false,
             'message' => 'No se encontraron los datos del perfil actual.'
         ]);
         exit();
    }

    // Buscamos cuál es el nombre real de la columna del id
    $idColName = '';
    if (isset($user['idusuario'])) {
        $idColName = 'idusuario';
    } elseif (isset($user['idUsuarios'])) {
        $idColName = 'idUsuarios';
    } elseif (isset($user['idUsuario'])) {
        $idColName = 'idUsuario';
    } elseif (isset($user['id'])) {
        $idColName = 'id';
    }

    if ($idColName === '') {
        echo json_encode([
            'success' => false,
            'message' => 'No se ha podido identificar la columna ID del usuario.'
        ]);
        exit();
    }

    if ($action === 'updateDatos') {
        $newUsuario = trim(isset($_POST['usuario']) ? $_POST['usuario'] : '');
        $newEmail = trim(isset($_POST['email']) ? $_POST['email'] : '');

        if ($newUsuario === '' || $newEmail === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Todos los campos son obligatorios.'
            ]);
            exit();
        }

        // Comprobamos que el nuevo email no esté en uso por OTRO usuario
        if ($newEmail !== $user['email']) {
            $stmtCheck = $conn->prepare("SELECT 1 FROM usuarios WHERE email = :email LIMIT 1");
            $stmtCheck->bindParam(':email', $newEmail);
            $stmtCheck->execute();
            if ($stmtCheck->fetch()) {
                 echo json_encode([
                     'success' => false,
                     'message' => 'El correo electrónico ya está en uso.'
                 ]);
                 exit();
            }
        }

        // Actualizamos los datos
        // Aseguramos que las columnas existen (nombre, email, usuario)
        $updateStmt = $conn->prepare("UPDATE usuarios SET usuario = :usuario, email = :email, nombre = :nombre WHERE $idColName = :id");
        $updateStmt->bindParam(':usuario', $newUsuario);
        $updateStmt->bindParam(':email', $newEmail);
        $updateStmt->bindParam(':nombre', $newUsuario); // Asumimos que "nombre" y "usuario" se mantiene igual para el display
        $updateStmt->bindParam(':id', $user[$idColName], PDO::PARAM_INT);
        $updateStmt->execute();

        $_SESSION['usuario'] = $newUsuario;
        if (isset($_COOKIE['usuario'])) {
            setcookie('usuario', $newUsuario, time() + (86400 * 30), "/");
        }

        echo json_encode([
            'success' => true,
            'message' => 'Datos actualizados correctamente.'
        ]);
        exit();

    } elseif ($action === 'updatePassword') {
        $oldPass = isset($_POST['oldPass']) ? $_POST['oldPass'] : '';
        $newPass = isset($_POST['newPass']) ? $_POST['newPass'] : '';

        if ($oldPass === '' || $newPass === '') {
            echo json_encode([
                'success' => false,
                'message' => 'Faltan campos por rellenar.'
            ]);
            exit();
        }

        if ($oldPass !== $user['contraseña']) {
             echo json_encode([
                 'success' => false,
                 'message' => 'La contraseña actual es incorrecta.'
             ]);
             exit();
        }

        $updatePass = $conn->prepare("UPDATE usuarios SET contraseña = :newpass WHERE $idColName = :id");
        $updatePass->bindParam(':newpass', $newPass);
        $updatePass->bindParam(':id', $user[$idColName], PDO::PARAM_INT);
        $updatePass->execute();

        echo json_encode([
            'success' => true,
            'message' => 'Contraseña actualizada correctamente.'
        ]);
        exit();
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Acción no válida.'
        ]);
        exit();
    }

} catch (PDOException $e) {
    echo json_encode([
        'success' => false,
        'message' => 'Error DB: ' . $e->getMessage()
    ]);
}
?>
