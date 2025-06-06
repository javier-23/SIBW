<?php
    require_once '/usr/local/lib/php/vendor/autoload.php';
    include('bdUsuarios.php');

    $loader = new \Twig\Loader\FilesystemLoader('../plantillas');
    $twig = new \Twig\Environment($loader);

    session_start();

    // Verificar si el usuario está logueado
    if (!isset($_SESSION['email'])) {
        header('Location: login.php');
        exit();
    }

    $mensaje = '';
    $error = '';

    // Procesar el formulario si se envía
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $nombre = trim($_POST['nombre']); 
        $email = trim($_POST['email']);
        $password = $_POST['password'];
        $password_confirm = $_POST['password_confirm'];
        
        // Validar datos
        if (empty($nombre)) {
            $error = 'El nombre no puede estar vacío';
        } else if (!empty($password) && $password !== $password_confirm) {
            $error = 'Las contraseñas no coinciden';
        } else if (!empty($password) && strlen($password) < 4) {
            $error = 'La contraseña debe tener al menos 4 caracteres';
        } else if (!empty($email) && !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error = 'Por favor ingrese un email válido';
        } else {
            // Actualizar perfil en la BD
            $resultado = actualizar_usuario($_SESSION['email'], $nombre, $email, $password);
            
            if ($resultado) {
                $mensaje = 'Perfil actualizado correctamente';
                // Actualizar email en sesión si cambió
                if ($email !== $_SESSION['email'] && !empty($email)) {
                    $_SESSION['email'] = $email;
                }
            } else {
                $error = 'Error al actualizar el perfil';
            }
        }
    }

    $usuario = getUser($_SESSION['email']);

    echo $twig->render('perfil.html', [
        'user' => $usuario,
        'mensaje' => $mensaje,
        'error' => $error
    ]);
?>