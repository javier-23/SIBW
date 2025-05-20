<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include('bd.php');
    include('bdUsuarios.php');

    $loader = new \Twig\Loader\FilesystemLoader('../plantillas');
    $twig = new \Twig\Environment($loader);

    session_start();

    // Verificar si el usuario está logueado
    if (!isset($_SESSION['email'])) {
        header('Location: login.php');
        exit();
    }

    // Verificar si el usuario es gestor o superior
    $usuario = getUser($_SESSION['email']);
    if (!$usuario || !in_array($usuario['rol'], ['gestor', 'superusuario'])) {
        header('Location: portada.php');
        exit();
    }

    $success = '';
    $error = '';
    $pelicula = null;

    // Verificar si se proporciona ID de película
    if (!isset($_GET['id']) || intval($_GET['id']) <= 0) {
        $_SESSION['error_message'] = 'ID de película no válido';
        header('Location: gestionar_peliculas.php');
        exit();
    }

    $id_pelicula = intval($_GET['id']);
    $pelicula = get_movie($id_pelicula);

    if (!$pelicula) {
        $_SESSION['error_message'] = 'La película no existe';
        header('Location: gestionar_peliculas.php');
        exit();
    }

    // Obtener imágenes de la galería
    $galeria = get_galeria($id_pelicula);

    // Procesar formulario para añadir imagen a la galería
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add') {
        if (!empty($_FILES['nueva_imagen']['name'])) {
            $upload_dir = '../images/galeria/';
            $imagen_name = 'galeria_' . time() . '_' . basename($_FILES['nueva_imagen']['name']);
            $imagen_path = 'images/galeria/' . $imagen_name;
            $imagen_upload = $upload_dir . $imagen_name;
            
            if (move_uploaded_file($_FILES['nueva_imagen']['tmp_name'], $imagen_upload)) { // Subir imagen
                $resultado = agregar_imagen_galeria($id_pelicula, $imagen_path);
                
                if ($resultado) {
                    $_SESSION['success_message'] = 'Imagen agregada a la galería correctamente';
                } else {
                    $_SESSION['error_message'] = 'Error al guardar la imagen en la base de datos';
                }
            } else {
                $_SESSION['error_message'] = 'Error al subir la imagen';
            }
            
            header('Location: gestionar_galeria.php?id=' . $id_pelicula);
            exit();
        } else {
            $error = 'Debe seleccionar una imagen para subir';
        }
    }

    // Procesar eliminación de imagen
    if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['image_id'])) {
        $image_id = intval($_GET['image_id']);
        $resultado = eliminar_imagen_galeria($image_id);
        
        if ($resultado) {
            $_SESSION['success_message'] = 'Imagen eliminada correctamente';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar la imagen';
        }
        
        header('Location: gestionar_galeria.php?id=' . $id_pelicula);
        exit();
    }

    // Recuperar mensajes de sesión
    if (isset($_SESSION['success_message'])) {
        $success = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    }

    if (isset($_SESSION['error_message'])) {
        $error = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
    }

    echo $twig->render('gestionar_galeria.html', [
        'pelicula' => $pelicula,
        'galeria' => $galeria,
        'error' => $error,
        'success' => $success,
        'user' => $usuario
    ]);
?>