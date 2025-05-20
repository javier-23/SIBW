<?php
require_once '/usr/local/lib/php/vendor/autoload.php';
include('bd.php');

$loader = new \Twig\Loader\FilesystemLoader('../plantillas');
$twig = new \Twig\Environment($loader);

if (isset($_GET['id'])) {
    $idPelicula = $_GET['id'];
} else {
    // Redireccionar a la portada si no hay ID
    header('Location: portada.php');
    exit();
}

$pelicula = get_movie($idPelicula);

// Si no se encuentra la película, redirigir a portada
if (empty($pelicula)) {
    header('Location: portada.php');
    exit();
}

echo $twig->render('pelicula_imprimir.html', [
    'pelicula' => $pelicula
]);

?>
