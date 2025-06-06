<?php
  require_once "/usr/local/lib/php/vendor/autoload.php";

  $loader = new \Twig\Loader\FilesystemLoader('../plantillas');
  $twig = new \Twig\Environment($loader);

  include('bdUsuarios.php');

  $error = null;
  $success = null;

  // Si ya está logueado, redirigir a portada
  if (isset($_SESSION['email'])) {
    header("Location: portada.php");
    exit();
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $password_confirm = $_POST['password_confirm'];
    
    // Validaciones
    if (empty($nombre) || empty($email) || empty($password) || empty($password_confirm)) {
      $error = "Por favor complete todos los campos";
    } 
    else if ($password !== $password_confirm) {
      $error = "Las contraseñas no coinciden";
    }
    else if (strlen($password) < 4) {
      $error = "La contraseña debe tener al menos 4 caracteres";
    }
    else if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      $error = "Por favor ingrese un email válido";
    }
    else {
      // Intentar registrar el usuario
      $result = registroUsuario($nombre, $email, $password);
      
      if ($result) {
        $success = "Registro exitoso. Ya puedes iniciar sesión.";
        header("Location: login.php");
      } else {
        $error = "El correo electrónico ya está registrado";
      }
    }
  }

  echo $twig->render('registro.html', [
    'error' => $error,
    'success' => $success
  ]);
?>