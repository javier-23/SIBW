<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include('bdUsuarios.php');

    session_start();

    // Verificar si el usuario está logueado
    if (!isset($_SESSION['email'])) {
        echo json_encode(['success' => false, 'error' => 'No has iniciado sesión']);
        exit();
    }

    // Verificar si el usuario es moderador o superusuario
    $usuario = getUser($_SESSION['email']);
    if (!$usuario || !in_array($usuario['rol'], ['moderador', 'superusuario'])) {
        echo json_encode(['success' => false, 'error' => 'No tienes permisos para realizar esta acción']);
        exit();
    }

    // Obtener datos enviados desde JavaScript
    $contenido = file_get_contents("php://input");
    $datos = json_decode($contenido, true);

    if (!$datos || !isset($datos['action']) || !isset($datos['comment_id'])) {
        echo json_encode(['success' => false, 'error' => 'Datos inválidos']);
        exit();
    }

    $action = $datos['action'];
    $comment_id = intval($datos['comment_id']);

    if ($comment_id <= 0) {
        echo json_encode(['success' => false, 'error' => 'ID de comentario inválido']);
        exit();
    }

    switch ($action) {
        case 'edit':
            // Verificar que existe el texto nuevo
            if (!isset($datos['nuevo_texto']) || empty($datos['nuevo_texto'])) {
                echo json_encode(['success' => false, 'error' => 'El texto del comentario no puede estar vacío']);
                exit();
            }
            
            $nuevo_texto = $datos['nuevo_texto'];
            
            // Marcar el comentario como editado
            $resultado = editar_comentario($comment_id, $nuevo_texto, true);
            
            if ($resultado) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al editar el comentario']);
            }
            break;
            
        case 'delete':
            // Eliminar el comentario
            $resultado = eliminar_comentario($comment_id);
            
            if ($resultado) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Error al eliminar el comentario']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'error' => 'Acción no válida']);
    }