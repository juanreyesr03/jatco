<?php
    include '../config/db_connection.php';
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    
    // Recibir datos de la solicitud
    $data = json_decode(file_get_contents("php://input"));
    
    if (!isset($data->usuario) || !isset($data->password)) {
        echo json_encode(["success" => false, "message" => "Datos incompletos"]);
        exit;
    }
    
    $usuario = $data->usuario;
    $password = $data->password;
    $platform = 'mobile';  // Añadir el valor del parámetro de la app móvil
    
    // Llamar al procedimiento almacenado
    $stmt = $conn->prepare("CALL iniciar_sesion(?, ?, ?)");
    $stmt->bind_param("sss", $usuario, $password, $platform);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $response = [];
    
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        
        if (isset($row['id_usuario'])) {
            // Inicio de sesión exitoso
            $response = [
                "success" => true,
                "id_usuario" => $row['id_usuario'],
                "nombre" => $row['nombre'],
                "id_rol" => $row['id_rol'],
                "rol_descripcion" => $row['rol_descripcion'],
                "id_estado" => $row['id_estado'],
                "id_area" => $row['id_area'],
                "message" => "Inicio de sesión exitoso"
            ];
        } else {
            // Mensaje de error personalizado
            $response = [
                "success" => false,
                "message" => $row['mensaje']
            ];
        }
    } else {
        $response = ["success" => false, "message" => "Error en la consulta"];
    }
    
    echo json_encode($response);
    $stmt->close();
?>
    