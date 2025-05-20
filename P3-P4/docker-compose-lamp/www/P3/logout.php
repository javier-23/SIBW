<?php
  session_start();
      
  session_destroy();

  header("Location: portada.php");
    
  exit();
?>