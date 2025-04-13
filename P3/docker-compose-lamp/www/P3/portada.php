<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include('bd.php');

    $loader = new \Twig\Loader\FilesystemLoader('../plantillas');
    $twig = new \Twig\Environment($loader);

    $peliculas = get_all_movies();
    $enlaces = get_enlaces();

    echo $twig->render('portada.html', [
        'peliculas' => $peliculas,
        'enlaces' => $enlaces
    ]);

establecer_conexion(false);
?>
