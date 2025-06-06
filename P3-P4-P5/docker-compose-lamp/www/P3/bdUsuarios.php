<?php

require_once(__DIR__ . '/bd.php');

// Comprueba si el usuario y la contraseña son correctos
function checkLogin($email, $pass) {
    
  $mysqli = establecer_conexion();
    
    $stmt = $mysqli->prepare("SELECT id, password FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        $usuario = $resultado->fetch_assoc();
        if (password_verify($pass, $usuario['password'])) {
            return true;
        }
    }
    
    return false;
  }

  // Obtiene el usuario por su email
  function getUser($email) {
    $mysqli = establecer_conexion();
    
    $stmt = $mysqli->prepare("SELECT id, nombre, email, rol FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    if ($resultado->num_rows > 0) {
        return $resultado->fetch_assoc();
    }
    
    return null;
  }

  // Registra un nuevo usuario
  function registroUsuario($nombre, $email, $pass) {
    
    $mysqli = establecer_conexion();

    $stmt = $mysqli->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows > 0) {
        // El email ya está registrado
        return false;
    }

    $rol = "registrado"; // Rol por defecto

    // Insertar el nuevo usuario
    $hash = password_hash($pass, PASSWORD_DEFAULT);
    $stmt = $mysqli->prepare("INSERT INTO usuarios (nombre, email, password, rol) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $nombre, $email, $hash, $rol);
    $result = $stmt->execute();
    
    $stmt->close();
    return $result;

  }

  function actualizar_usuario($email, $nombre, $nuevo_email, $password) {
    $mysqli = establecer_conexion();

    // Actualizar el usuario en la base de datos
    if (!empty($password)) {
        $stmt = $mysqli->prepare("UPDATE usuarios SET nombre = ?, email = ?, password = ? WHERE email = ?");
        $hash = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param("ssss", $nombre, $nuevo_email, $hash, $email);
    } else {
        $stmt = $mysqli->prepare("UPDATE usuarios SET nombre = ?, email = ? WHERE email = ?");
        $stmt->bind_param("sss", $nombre, $nuevo_email, $email);
    }

    if ($stmt->execute()) {
        return true;
    } else {
        return false;
    }
  }

// Cambiar rol de usuario
function cambiar_rol_usuario($user_email, $new_role) {
    $mysqli = establecer_conexion();
    
    $stmt = $mysqli->prepare("UPDATE usuarios SET rol = ? WHERE email = ?");
    $stmt->bind_param("ss", $new_role, $user_email);
    $resultado = $stmt->execute();
    
    $stmt->close();
    return $resultado;
}

function buscar_usuarios($search_term = '', $role_filter = ''){
    $mysqli = establecer_conexion();

    // Construir la consulta base
    $query = "SELECT nombre, email, rol FROM usuarios WHERE 1=1";
    $params = [];
    $types = "";
    
    // Aplicar filtro de búsqueda si se proporciona
    if (!empty($search_term)) {
        $search_param = "%$search_term%"; 
        $query .= " AND (nombre LIKE ? OR email LIKE ?)";
        $params[] = $search_param;
        $params[] = $search_param;
        $types .= "ss";
    }
    
    // Aplicar filtro de rol si se proporciona
    if(!empty($role_filter)) {
        $query .= " AND rol = ?";
        $params[] = $role_filter;
        $types .= "s";
    }
        
    // Preparar y ejecutar la consulta
    $stmt = $mysqli->prepare($query);
    
    // Vincular parámetros solo si hay parámetros para vincular
    if (!empty($types) && !empty($params)) {
        $bind_params = array();
        $bind_params[] = &$types;
        
        foreach ($params as $key => $value) {
            $bind_params[] = &$params[$key];
        }
        
        call_user_func_array([$stmt, 'bind_param'], $bind_params); // Vincular parámetros
    }
    
    $stmt->execute();
    $resultado = $stmt->get_result();
    
    $usuarios = [];
    while ($row = $resultado->fetch_assoc()) {
        $usuarios[] = $row;
    }
    
    $stmt->close();
    return $usuarios;
}
?>