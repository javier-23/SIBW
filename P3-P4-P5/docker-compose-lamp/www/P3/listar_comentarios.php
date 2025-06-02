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

    // Verificar si el usuario es moderador o superusuario
    $usuario = getUser($_SESSION['email']);
    if (!$usuario || !in_array($usuario['rol'], ['moderador', 'superusuario'])) {
        header('Location: portada.php');
        exit();
    }

    $search_term = isset($_GET['search_term']) ? trim($_GET['search_term']) : '';

    // Recuperar mensajes de sesión
    if (!empty($search_term)) {
        $comentarios = buscar_comentarios($search_term);
    } else {
        $comentarios = get_all_comentarios();
    }

    echo $twig->render('listaComentarios.html', [
        'comentarios' => $comentarios
    ]);
?>