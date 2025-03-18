<?php
    // Incluir archivo de configuración
    include '../config/db_connection.php';
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    
    // Recibir datos de la solicitud
    $data = json_decode(file_get_contents("php://input"));
    
    // Verificar si todas las variables POST están definidas
    
    $missing_fields = [];

    // Verifica si existen y no están vacíos
    if (empty($_POST['usuario'])) $missing_fields[] = 'usuario';
    if (empty($_POST['numero_parte'])) $missing_fields[] = 'numero_parte';
    if (empty($_POST['mensaje'])) $missing_fields[] = 'mensaje';


    // Capturar los valores del formulario
    $usuario = trim($_POST['usuario']);
    $mensaje = trim($_POST['mensaje']);
    $numero_parte = trim($_POST['numero_parte']);

    try {
        // Preparar la inserción
        $checkQuery = "CALL insertar_llegada_proveedor(?, ?, ?)";
        $stmt = $conn->prepare($checkQuery);
        if (!$stmt) {
            throw new Exception("Error en la consulta de inserción: " . $conn->error);
        }

        $stmt->bind_param("sss", $usuario, $numero_parte, $mensaje);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Reporte registrado exitosamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Hubo un error al registrar al usuario']);
        }

        $stmt->close();
        $conn->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
    }

    exit();
?>
