<?php
  require_once "/usr/local/lib/php/vendor/autoload.php";

  $loader = new \Twig\Loader\FilesystemLoader('../plantillas');
  $twig = new \Twig\Environment($loader);
  
  include('bdUsuarios.php');

  $error = null;
  
  if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $pass = $_POST['contraseña'];
    
    if (empty($email) || empty($pass))
      $error = "Por favor complete todos los campos";
    else if (checkLogin($email, $pass)) {
      session_start();
      
      $_SESSION['email'] = $email;
      
      header("Location: portada.php");
      exit();
    }
    else
      $error = "Email o contraseña incorrectos";
    
  }
  
  echo $twig->render('login.html', [
    'error' => $error
  ]);
?>