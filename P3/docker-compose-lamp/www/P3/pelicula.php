<?php
require_once '/usr/local/lib/php/vendor/autoload.php';
include('bd.php');

$loader = new \Twig\Loader\FilesystemLoader('../plantillas');
$twig = new \Twig\Environment($loader);

// Comprobar si el usuario ha enviado un comentario
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    header('Content-Type: application/json'); // Que el encabezado sea json

    try {
        // Leer datos del json
        $contenido = file_get_contents("php://input");

        $datos = json_decode($contenido, true); // Decodificar json
        if (!$datos){
            throw new Exception("Error al decodificar JSON: " . json_last_error_msg());
        }

        // Validar datos
        $idPelicula = $datos['idPelicula'] ?? null;
        $nombre = $datos['nombre'] ?? '';
        $email = $datos['email'] ?? '';
        $comentario = $datos['comentario'] ?? '';
        $fecha_hora = $datos['fecha_hora'] ?? date('Y-m-d H:i');

        if (!$idPelicula || empty($nombre) || empty($email) || empty($comentario)) {
            throw new Exception("Todos los campos son obligatorios");
        }

        // Insertar comentario
        $resultado = insertar_comentario($fecha_hora, $nombre, $comentario, $email, $idPelicula);
        if ($resultado) {
            echo json_encode([
                "exito" => true
            ]);
        } else {
            throw new Exception("Error al guardar el comentario");
        }
    } catch (Exception $e) {
        echo json_encode([
            "exito" => false,
            "error" => $e->getMessage()
        ]);
    }
    exit; // Terminar ejecución después de manejar POST
}

// Manejar solicitudes GET (renderizar HTML)
if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    // Obtener el ID de la película desde la URL
    if (isset($_GET['id']) && ctype_digit($_GET['id'])) {
        $idPelicula = (int)$_GET['id'];
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

    $comentarios = get_comentarios($idPelicula);
    $enlaces = get_enlaces();
    $palabrasProhibidas = json_encode(get_palabras_prohibidas());
    $galeria = get_galeria($idPelicula);

    echo $twig->render('pelicula.html', [
        'pelicula' => $pelicula,
        'comentarios' => $comentarios,
        'enlaces' => $enlaces,
        'palabrasProhibidas' => $palabrasProhibidas,
        'galeria' => $galeria
    ]);
}

establecer_conexion(false);
?>
