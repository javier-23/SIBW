<?php
// Controlador

function establecer_conexion($abierta = true) {
    static $mysqli = null; // Variable estática para mantener la conexión

    if($mysqli === null) { // Solo se establece la conexión si no existe
        $mysqli = new mysqli('database', 'javier', 'password', 'sibw');
        if ($mysqli->connect_errno) {
            echo "Error de conexión: " . $mysqli->connect_error;
            exit();
        }
    }

    if(!$abierta) { // Cerrar conexión
        if($mysqli !== null) {
            $mysqli->close();
            $mysqli = null; // Reiniciar la variable estática
        }
        return true;
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
        echo "Error: ID no válido.";
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
            'imagen' => !is_null($row['imagen']) ? base64_encode($row['imagen']) : '',
            'escena1' => !is_null($row['escena1']) ? base64_encode($row['escena1']) : '',
            'escena2' => !is_null($row['escena2']) ? base64_encode($row['escena2']) : '',
            'texto_escena1' => $row['texto_escena1'],
            'texto_escena2' => $row['texto_escena2'],
        );
    }

    $stmt->close();
    return $pelicula;
}

function get_all_movies() {
    
    $mysqli = establecer_conexion();

    $consulta = $mysqli->query("SELECT * FROM peliculas");
    
    $peliculas = array();
    
    if($consulta->num_rows > 0){
        while($row = $consulta->fetch_assoc()) {
            $peliculas[] = array(
                'id' => $row['id'],
                'titulo' => $row['titulo'],
                'fecha_estreno' => $row['fecha_estreno'],
                'genero' => $row['genero'],
                'sinopsis' => $row['sinopsis'],
                'director' => $row['director'],
                'actores_principales' => $row['actores_principales'],
                'premios' => $row['premios'],
                'imagen' => !is_null($row['imagen']) ? base64_encode($row['imagen']) : '',
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
        $stmt = $mysqli->prepare("SELECT nombre, comentario, DATE_FORMAT(fecha_hora, '%d-%m-%Y %H:%i') AS fecha_hora FROM Comentarios WHERE id_pelicula = ?");   
        $stmt->bind_param("i", $id); 
        $stmt->execute();
        $consulta = $stmt->get_result();
    } else{
        echo "Error: ID no válido.";
        return array();
    }

    $comentarios = array();

    if($consulta->num_rows > 0){
        while($row = $consulta->fetch_assoc()) {
            $comentarios[] = array(
                'nombre' => $row['nombre'],
                'fecha_hora' => $row['fecha_hora'],
                'comentario' => $row['comentario']
            );
        }
    }
    
    $stmt->close();
    return $comentarios;
}

function insertar_comentario($fecha_hora, $nombre, $comentario, $email, $id_pelicula){
    
    $mysqli = establecer_conexion();

    $stmt = $mysqli->prepare("INSERT INTO Comentarios (nombre, fecha_hora, comentario, email, id_pelicula) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("ssssi", $nombre, $fecha_hora, $comentario, $email, $id_pelicula);
    
    $consulta = $stmt->execute();
    if(!$consulta) {
        echo json_encode(["exito" => false, "error" => "Error ejecutando la consulta Insert: " . $stmt->error]);
        exit;
    }

    $stmt->close();

    return $consulta;
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
        $stmt = $mysqli->prepare("SELECT imagen FROM galeria WHERE id_pelicula = ?");
        $stmt->bind_param("i", $id_pelicula);
        $stmt->execute();
        $consulta = $stmt->get_result();
    } else{
        echo "Error: ID no válido.";
        return array();
    }

    $galeria = array();
    if($consulta->num_rows > 0){
        while ($row = $consulta->fetch_assoc()) {
            $galeria[] = base64_encode($row['imagen']);
        }
    }

    $stmt->close();
    return $galeria;
}
?>