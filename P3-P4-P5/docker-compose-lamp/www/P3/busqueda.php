<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include('bd.php');

    $loader = new \Twig\Loader\FilesystemLoader('../plantillas');
    $twig = new \Twig\Environment($loader);

    // Devolver resultados en formato JSON
    header('Content-Type: application/json');

    if( isset($_GET['buscar']) && $_GET['buscar'] != '') { 
        $busqueda = $_GET['buscar'];
        $peliculas = buscar_peliculas_ajax($busqueda);

        echo json_encode($peliculas);
    }
    else {
        echo json_encode([]);
    }

?>