<?php
// filepath: c:\Users\Javier\Desktop\P3\docker-compose-lamp\www\P3\editar_pelicula.php
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
    $modo = 'crear';

    // Verificar si estamos editando una película existente
    if (isset($_GET['id']) && intval($_GET['id']) > 0) {
        $id = intval($_GET['id']);
        $pelicula = get_movie($id);
        
        if (!$pelicula) {
            $_SESSION['error_message'] = 'La película que intentas editar no existe';
            header('Location: gestionar_peliculas.php');
            exit();
        }
        
        $modo = 'editar';
    }

    // Procesar formulario
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Recoger datos del formulario
        $id = isset($_POST['id']) ? intval($_POST['id']) : 0;
        $titulo = trim($_POST['titulo']);
        $director = trim($_POST['director']);
        $fecha_estreno = trim($_POST['fecha_estreno']);
        $genero = trim($_POST['genero']);
        $actores = trim($_POST['actores_principales']);
        $sinopsis = trim($_POST['sinopsis']);
        $premios = trim($_POST['premios']);
        $hashtags = trim($_POST['hashtags']);
        $texto_escena1 = trim($_POST['texto_escena1']);
        $texto_escena2 = trim($_POST['texto_escena2']);
        $publicada = isset($_POST['publicada']) ? 1 : 0;

        // Validar datos
        if (empty($titulo) || empty($director)) {
            $error = 'Por favor, completa todos los campos obligatorios (título y director)';
        } else {
            // Procesar imágenes si se han subido
            $imagen = null;
            $escena1 = null;
            $escena2 = null;
            $imagen_path = null;
            $escena1_path = null;
            $escena2_path = null;
            
            // Procesar imagen principal
            if (!empty($_FILES['imagen']['name'])) {
                $upload_dir = '../images/portadas/';
                $imagen_name = 'pelicula_' . time() . '_' . basename($_FILES['imagen']['name']);
                $imagen_path = 'images/portadas/' . $imagen_name;
                $imagen_upload = $upload_dir . $imagen_name;
                
                if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $imagen_upload)) {
                    $error = 'Error al subir la imagen principal';
                }
            }
            
            // Procesar escena 1
            if (!empty($_FILES['escena1']['name'])) {
                $upload_dir = '../images/escenas/';
                $escena1_name = 'escena1_' . time() . '_' . basename($_FILES['escena1']['name']);
                $escena1_path = 'images/escenas/' . $escena1_name;
                $escena1_upload = $upload_dir . $escena1_name;
                
                if (!move_uploaded_file($_FILES['escena1']['tmp_name'], $escena1_upload)) {
                    $error = 'Error al subir la imagen de escena 1';
                }
            }
            
            // Procesar escena 2
            if (!empty($_FILES['escena2']['name'])) {
                $upload_dir = '../images/escenas/';
                $escena2_name = 'escena2_' . time() . '_' . basename($_FILES['escena2']['name']);
                $escena2_path = 'images/escenas/' . $escena2_name;
                $escena2_upload = $upload_dir . $escena2_name;
                
                if (!move_uploaded_file($_FILES['escena2']['tmp_name'], $escena2_upload)) {
                    $error = 'Error al subir la imagen de escena 2';
                }
            }

            if (empty($error)) {
                if ($id > 0) {
                    // Actualizar película existente
                    $pelicula_actual = get_movie($id);
                    
                    // Usar las imágenes actuales si no se han subido nuevas
                    if (empty($imagen_path)) {
                        $imagen_path = $pelicula_actual['imagen'];
                    }
                    if (empty($escena1_path)) {
                        $escena1_path = $pelicula_actual['escena1'];
                    }
                    if (empty($escena2_path)) {
                        $escena2_path = $pelicula_actual['escena2'];
                    }
                    
                    $resultado = actualizar_pelicula($id, $titulo, $director, $fecha_estreno, $genero, $actores, $sinopsis, $premios, $imagen_path, $escena1_path, $escena2_path, $texto_escena1, $texto_escena2, $hashtags, $publicada);
                    
                    if ($resultado) {
                        $_SESSION['success_message'] = 'Película actualizada correctamente';
                        header('Location: gestionar_peliculas.php');
                        exit();
                    } else {
                        $error = 'Error al actualizar la película en la base de datos';
                    }
                } else {
                    // Crear nueva película
                    $resultado = crear_pelicula($titulo, $director, $fecha_estreno, $genero, $actores, $sinopsis, $premios, $imagen_path, $escena1_path, $escena2_path, $texto_escena1, $texto_escena2, $hashtags, $publicada);
                    
                    if ($resultado) {
                        $_SESSION['success_message'] = 'Película creada correctamente';
                        header('Location: gestionar_peliculas.php');
                        exit();
                    } else {
                        $error = 'Error al crear la película en la base de datos';
                    }
                }
            }
        }
    }

    echo $twig->render('editar_pelicula.html', [
        'pelicula' => $pelicula,
        'modo' => $modo,
        'error' => $error,
        'success' => $success,
        'user' => $usuario
    ]);
?>