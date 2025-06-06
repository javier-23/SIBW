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

    $success = '';
    $error = '';

    $usuario_actual = getUser($_SESSION['email']);
    if (!$usuario_actual || $usuario_actual['rol'] !== 'superusuario') {
        // Si no es superusuario, redirigir a la portada
        header('Location: portada.php');
        exit();
    }

    // Procesar acciones POST (cambio de rol o eliminación)
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $user_email = $_POST['user_email'] ?? '';
        
        if ($user_email === '') {
            $error = 'Email no válido';
        } else {
            // Obtener el usuario objetivo
            $usuario_objetivo = getUser($user_email);
            
            if (!$usuario_objetivo) {
                $error = 'Usuario no encontrado';
            } else {
                // Evitar que un superusuario se elimine a sí mismo o cambie su propio rol
                if ($usuario_objetivo['email'] === $_SESSION['email']) {
                    $error = 'No puedes modificar tu propio usuario';
                } else {
                    $new_role = $_POST['new_role'] ?? '';
                    $roles_validos = ['registrado', 'moderador', 'gestor', 'superusuario'];
                            
                    if (!in_array($new_role, $roles_validos)) {
                        $error = 'Rol no válido';
                    } else {
                        // Actualizar el rol del usuario
                        $resultado = cambiar_rol_usuario($user_email, $new_role);
                                
                        if ($resultado) {
                            $success = "Rol de {$usuario_objetivo['nombre']} actualizado a {$new_role}";
                        } else {
                            $error = 'Error al actualizar el rol';
                        }
                    }
                }
                
            }
        }
        header('Location: superusuario.php');
        exit();
    }

    // Parámetros de búsqueda
    $search_term = $_GET['search_term'] ?? '';
    $role_filter = $_GET['role_filter'] ?? '';

    // Determinar si se está realizando una búsqueda activa
    $is_searching = !empty($search_term) || !empty($role_filter);

    // Obtener usuarios filtrados y paginados
    $users = $is_searching ? buscar_usuarios($search_term, $role_filter) : [];
    if($role_filter == 'Todos los roles') {
        $users = buscar_usuarios();
    }   

    echo $twig->render('superusuario.html', [
        'success' => $success,
        'error' => $error,
        'users' => $users,
    ]);
?>