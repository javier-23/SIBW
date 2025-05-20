<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include('bd.php');
    include('bdUsuarios.php');

    $loader = new \Twig\Loader\FilesystemLoader('../plantillas');
    $twig = new \Twig\Environment($loader);

    $user = null;

    session_start();
  
    if (isset($_SESSION['email'])) {
        $user = getUser($_SESSION['email']);
    }

    $peliculas = get_all_movies();
    $enlaces = get_enlaces();

    echo $twig->render('portada.html', [
        'peliculas' => $peliculas,
        'enlaces' => $enlaces,
        'user' => $user,
    ]);
?>
