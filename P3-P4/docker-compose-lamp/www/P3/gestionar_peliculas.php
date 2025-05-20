<?php
// filepath: c:\Users\Javier\Desktop\P3\docker-compose-lamp\www\P3\gestionar_peliculas.php
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

    // Recuperar mensajes de sesión
    if (isset($_SESSION['success_message'])) {
        $success = $_SESSION['success_message'];
        unset($_SESSION['success_message']);
    }

    if (isset($_SESSION['error_message'])) {
        $error = $_SESSION['error_message'];
        unset($_SESSION['error_message']);
    }

    // Procesar acciones de eliminación directa
    if (isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['id'])) {
        $id = intval($_GET['id']);
        if (eliminar_pelicula($id)) {
            $_SESSION['success_message'] = 'Película eliminada correctamente';
        } else {
            $_SESSION['error_message'] = 'Error al eliminar la película';
        }
        header('Location: gestionar_peliculas.php');
        exit();
    }

    // Obtener parámetros de búsqueda
    $search_term = isset($_GET['search_term']) ? $_GET['search_term'] : '';

    // Obtener listado de películas (filtrado si hay búsqueda)
    $peliculas = !empty($search_term) ? buscar_peliculas($search_term) : get_all_movies_admin();

    echo $twig->render('gestionar_peliculas.html', [
        'success' => $success,
        'error' => $error,
        'peliculas' => $peliculas,
        'search_term' => $search_term,
        'user' => $usuario
    ]);
?>