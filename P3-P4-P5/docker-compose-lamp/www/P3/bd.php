<?php

function establecer_conexion() {
    static $mysqli = null; // Variable estática para mantener la conexión

    if($mysqli === null) { // Solo se establece la conexión si no existe
        $mysqli = new mysqli('database', 'javier', 'password', 'sibw');
        if ($mysqli->connect_errno) {
            exit();
        }
    }

    return $mysqli;
}

function get_movie($id) {
    
    $mysqli = establecer_conexion();

    if(ctype_digit((string)$id)){
        $stmt = $mysqli->prepare("SELECT * FROM peliculas WHERE id = ?");   
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $consulta = $stmt->get_result();
    } else{
        return array();
    }


    $pelicula = array();
    if($consulta->num_rows > 0){
        $row = $consulta->fetch_assoc();

        $pelicula = array(
            'id' => $row['id'],
            'titulo' => $row['titulo'],
            'fecha_estreno' => $row['fecha_estreno'],
            'genero' => $row['genero'],
            'sinopsis' => $row['sinopsis'],
            'director' => $row['director'],
            'actores_principales' => $row['actores_principales'],
            'premios' => $row['premios'],
            'imagen' => $row['imagen'],  //!is_null($row['imagen']) ? base64_encode($row['imagen']) : '',
            'escena1' => $row['escena1'],   //!is_null($row['escena1']) ? base64_encode($row['escena1']) : '',
            'escena2' => $row['escena2'],   //!is_null($row['escena2']) ? base64_encode($row['escena2']) : '',
            'texto_escena1' => $row['texto_escena1'],
            'texto_escena2' => $row['texto_escena2'],
            'hashtags' => $row['hashtags'],
            'publicada' => $row['publicada'],
        );
    }

    $stmt->close();
    return $pelicula;
}

function get_all_movies() {
    
    $mysqli = establecer_conexion();

    $consulta = $mysqli->query("SELECT id, titulo, fecha_estreno, genero, director, imagen, publicada, hashtags 
                                FROM peliculas");
    
    $peliculas = array();
    
    if($consulta->num_rows > 0){
        while($row = $consulta->fetch_assoc()) {
            $peliculas[] = array(
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'fecha_estreno' => $row['fecha_estreno'],
                'genero' => $row['genero'],
                'director' => $row['director'],
                'imagen' => $row['imagen'], //!is_null($row['imagen']) ? base64_encode($row['imagen']) : '',
                'publicada' => $row['publicada'],
                'hashtags' => $row['hashtags'],
            );
        }
    }
    
    return $peliculas;
}

function get_enlaces(){
    $mysqli = establecer_conexion();

    $consulta = $mysqli->query("SELECT * FROM enlaces");

    $enlaces_agrupados = array();

    if($consulta->num_rows > 0){
        while($row = $consulta->fetch_assoc()) {
            $tipo = $row['tipo'];

            // Si este tipo no existe aún en el array, lo inicializamos
            if (!isset($enlaces_agrupados[$tipo])) {
                $enlaces_agrupados[$tipo] = array();
            }

            // Añadimos el enlace al grupo de su tipo
            $enlaces_agrupados[$tipo][] = array(
                'nombre' => $row['nombre'],
                'enlace' => $row['enlace']
            );
        }
    }

    return $enlaces_agrupados;
}

function get_comentarios($id){
    $mysqli = establecer_conexion();

    if(ctype_digit((string)$id)){ 
        $stmt = $mysqli->prepare("SELECT c.id, c.comentario, DATE_FORMAT(c.fecha_hora, '%d-%m-%Y %H:%i') AS fecha_hora, 
                                 c.modificado, u.nombre, u.id as id_usuario, u.rol 
                                 FROM Comentarios c 
                                 JOIN usuarios u ON c.id_usuario = u.id 
                                 WHERE c.id_pelicula = ? 
                                 ORDER BY c.fecha_hora DESC");   
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        $consulta = $stmt->get_result();
    } else{
        return array();
    }

    $comentarios = array();

    if($consulta->num_rows > 0){
        while($row = $consulta->fetch_assoc()) {
            $comentarios[] = array(
                'id' => $row['id'],
                'nombre' => $row['nombre'],
                'fecha_hora' => $row['fecha_hora'],
                'comentario' => $row['comentario'],
                'modificado' => $row['modificado'] == 1
            );
        }
    }
    
    $stmt->close();
    return $comentarios;
}

function insertar_comentario($fecha_hora, $comentario, $id_usuario, $id_pelicula){
    
    $mysqli = establecer_conexion();

    $stmt = $mysqli->prepare("INSERT INTO Comentarios (fecha_hora, comentario, id_usuario, id_pelicula) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssii",  $fecha_hora, $comentario, $id_usuario, $id_pelicula);
    
    $consulta = $stmt->execute();
    if(!$consulta) {
        exit;
    }

    $stmt->close();

    return $consulta;
}

function buscar_comentarios($search_term) {
    
    $mysqli = establecer_conexion();

    $search_param = "%$search_term%";
    $stmt = $mysqli->prepare(
        "SELECT c.id, c.comentario, DATE_FORMAT(c.fecha_hora, '%d-%m-%Y %H:%i') AS fecha_hora, 
                c.modificado, u.nombre as nombre_usuario, u.email as email_usuario, 
                p.id as id_pelicula, p.titulo as titulo_pelicula
         FROM Comentarios c 
         JOIN usuarios u ON c.id_usuario = u.id 
         JOIN peliculas p ON c.id_pelicula = p.id
         WHERE c.comentario LIKE ? OR u.nombre LIKE ? OR p.titulo LIKE ?
         ORDER BY c.fecha_hora DESC"
    );
    $stmt->bind_param("sss", $search_param, $search_param, $search_param);
    $stmt->execute();
    $resultado = $stmt->get_result();

    $comentarios = [];
    while ($row = $resultado->fetch_assoc()) {
        $comentarios[] = [
            'id' => $row['id'],
            'comentario' => $row['comentario'],
            'fecha_hora' => $row['fecha_hora'],
            'modificado' => $row['modificado'] == 1,
            'nombre_usuario' => $row['nombre_usuario'],
            'email_usuario' => $row['email_usuario'],
            'id_pelicula' => $row['id_pelicula'],
            'titulo_pelicula' => $row['titulo_pelicula']
        ];
    }
    $stmt->close();
    return $comentarios;
}

function get_palabras_prohibidas(){
    
    $mysqli = establecer_conexion();

    $stmt = $mysqli->prepare("SELECT palabra FROM palabrasProhibidas");
    $stmt->execute();
    $resultado = $stmt->get_result();

    $palabras = [];
    while ($row = $resultado->fetch_assoc()) {
        $palabras[] = $row['palabra'];
    }

    $stmt->close();
    return $palabras;
}

function get_galeria($id_pelicula){

    $mysqli = establecer_conexion();

    if(ctype_digit((string)$id_pelicula)){
        $stmt = $mysqli->prepare("SELECT id, imagen FROM galeria WHERE id_pelicula = ?");
        $stmt->bind_param("i", $id_pelicula);
        $stmt->execute();
        $consulta = $stmt->get_result();
    } else{
        return array();
    }

    $galeria = array();
    if($consulta->num_rows > 0){
        while ($row = $consulta->fetch_assoc()) {
            $galeria[] = array(
                'imagen' => $row['imagen'], //base64_encode($row['imagen']);
                'id' => $row['id']
            );
        }
    }

    $stmt->close();
    return $galeria;
}

function editar_comentario($id_comentario, $nuevo_texto, $marcar_editado = true) {
    $mysqli = establecer_conexion();
    
    if ($marcar_editado) {
        $stmt = $mysqli->prepare("UPDATE Comentarios SET comentario = ?, modificado = 1 WHERE id = ?");
    } else {
        $stmt = $mysqli->prepare("UPDATE Comentarios SET comentario = ? WHERE id = ?");
    }
    
    $stmt->bind_param("si", $nuevo_texto, $id_comentario);
    $resultado = $stmt->execute();
    
    $stmt->close();
    return $resultado;
}

function eliminar_comentario($id_comentario) {
    $mysqli = establecer_conexion();
    
    $stmt = $mysqli->prepare("DELETE FROM Comentarios WHERE id = ?");
    $stmt->bind_param("i", $id_comentario);
    $resultado = $stmt->execute();
    
    $stmt->close();
    return $resultado;
}

function get_all_comentarios(){
    $mysqli = establecer_conexion();

    $consulta = $mysqli->query("SELECT c.id, c.comentario, DATE_FORMAT(c.fecha_hora, '%d-%m-%Y %H:%i') AS fecha_hora, 
                                c.modificado, u.nombre as nombre_usuario, u.email as email_usuario, 
                                p.id as id_pelicula, p.titulo as titulo_pelicula
                                FROM Comentarios c 
                                JOIN usuarios u ON c.id_usuario = u.id 
                                JOIN peliculas p ON c.id_pelicula = p.id "); 

    $comentarios = array();

    if($consulta->num_rows > 0){
        while($row = $consulta->fetch_assoc()) {
            $comentarios[] = array(
                'id' => $row['id'],
                'comentario' => $row['comentario'],
                'fecha_hora' => $row['fecha_hora'],
                'modificado' => $row['modificado'] == 1,
                'nombre_usuario' => $row['nombre_usuario'],
                'email_usuario' => $row['email_usuario'],
                'id_pelicula' => $row['id_pelicula'],
                'titulo_pelicula' => $row['titulo_pelicula']
            );
        }
    }
    
    return $comentarios;
}

/* Gestionar pelis */

function buscar_peliculas($search_term) {
    $mysqli = establecer_conexion();
    
    $search_param = "%$search_term%"; // Agregar comodines para LIKE
    
    $stmt = $mysqli->prepare("SELECT id, titulo, fecha_estreno, genero, director, imagen, hashtags, publicada  
                             FROM peliculas 
                             WHERE titulo LIKE ? 
                             OR sinopsis LIKE ? 
                             OR director LIKE ?
                             OR hashtags LIKE ?
                             OR genero LIKE ?
                             OR actores_principales LIKE ?
                             OR premios LIKE ?
                             OR texto_escena1 LIKE ?
                             OR texto_escena2 LIKE ?");
    
    $stmt->bind_param("sssssssss", $search_param, $search_param, $search_param, $search_param, 
                      $search_param, $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    
    $resultado = $stmt->get_result();
    $peliculas = array();
    
    if($resultado->num_rows > 0){
        while($row = $resultado->fetch_assoc()) {
            $peliculas[] = array(
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'fecha_estreno' => $row['fecha_estreno'],
                'genero' => $row['genero'],
                'director' => $row['director'],
                'imagen' => $row['imagen'],
                'hashtags' => $row['hashtags'],
                'publicada' => $row['publicada'],
            );
        }
    }
    
    $stmt->close();
    return $peliculas;
}

function buscar_peliculas_ajax($search_term) {
    $mysqli = establecer_conexion();
    
    $search_param = "%$search_term%";
    
    $stmt = $mysqli->prepare("SELECT id, titulo, imagen FROM peliculas 
                             WHERE (titulo LIKE ? 
                             OR sinopsis LIKE ? 
                             OR director LIKE ?
                             OR hashtags LIKE ?
                             OR genero LIKE ?
                             OR actores_principales LIKE ?
                             OR premios LIKE ?
                             OR texto_escena1 LIKE ?
                             OR texto_escena2 LIKE ?) AND publicada = 1");
    
    $stmt->bind_param("sssssssss", $search_param, $search_param, $search_param, $search_param, 
                      $search_param, $search_param, $search_param, $search_param, $search_param);
    $stmt->execute();
    
    $resultado = $stmt->get_result();
    $peliculas = array();
    
    if($resultado->num_rows > 0){
        while($row = $resultado->fetch_assoc()) {
            $peliculas[] = array(
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'imagen' => $row['imagen'],
            );
        }
    }
    
    $stmt->close();
    return $peliculas;
}

function crear_pelicula($titulo, $director, $fecha_estreno, $genero, $actores, $sinopsis, $premios, $imagen, $escena1, $escena2, $texto_escena1, $texto_escena2, $hashtags, $publicada = 0) {
    $mysqli = establecer_conexion();
    
    $stmt = $mysqli->prepare("INSERT INTO peliculas 
                             (titulo, director, fecha_estreno, genero, actores_principales, sinopsis, premios, imagen, escena1, escena2, texto_escena1, texto_escena2, hashtags, publicada) 
                             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param("sssssssssssssi", $titulo, $director, $fecha_estreno, $genero, $actores, $sinopsis, $premios, $imagen, $escena1, $escena2, $texto_escena1, $texto_escena2, $hashtags, $publicada);
    $resultado = $stmt->execute();
    
    $stmt->close();
    return $resultado;
}

function actualizar_pelicula($id, $titulo, $director, $fecha_estreno, $genero, $actores, $sinopsis, $premios, $imagen, $escena1, $escena2, $texto_escena1, $texto_escena2, $hashtags, $publicada) {
    $mysqli = establecer_conexion();
    
    $stmt = $mysqli->prepare("UPDATE peliculas 
                             SET titulo = ?, director = ?, fecha_estreno = ?, genero = ?, 
                             actores_principales = ?, sinopsis = ?, premios = ?, imagen = ?, 
                             escena1 = ?, escena2 = ?, texto_escena1 = ?, texto_escena2 = ?, 
                             hashtags = ? , publicada = ?
                             WHERE id = ?");
    
    $stmt->bind_param("sssssssssssssii", $titulo, $director, $fecha_estreno, $genero, $actores, $sinopsis, $premios, $imagen, $escena1, $escena2, $texto_escena1, $texto_escena2, $hashtags, $publicada, $id);
    $resultado = $stmt->execute();
    
    $stmt->close();
    return $resultado;
}

function eliminar_pelicula($id) {
    $mysqli = establecer_conexion();
    
    // Primero, eliminar las imágenes asociadas en la galería
    $stmt = $mysqli->prepare("DELETE FROM galeria WHERE id_pelicula = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
    
    // Luego, eliminar la película
    $stmt = $mysqli->prepare("DELETE FROM peliculas WHERE id = ?");
    $stmt->bind_param("i", $id);
    $resultado = $stmt->execute();
    
    $stmt->close();
    return $resultado;
}

function agregar_imagen_galeria($id_pelicula, $ruta_imagen) {
    $mysqli = establecer_conexion();
    
    $stmt = $mysqli->prepare("INSERT INTO galeria (id_pelicula, imagen) VALUES (?, ?)");
    $stmt->bind_param("is", $id_pelicula, $ruta_imagen);
    $resultado = $stmt->execute();
    
    $stmt->close();
    return $resultado;
}

function eliminar_imagen_galeria($id_imagen) {
    $mysqli = establecer_conexion();
    
    $stmt = $mysqli->prepare("DELETE FROM galeria WHERE id = ?");
    $stmt->bind_param("i", $id_imagen);
    $resultado = $stmt->execute();
    
    $stmt->close();
    return $resultado;
}
?>