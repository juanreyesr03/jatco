<?php
    // Habilitar la visualización de errores
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
    error_reporting(E_ALL);

    // Incluir archivo de configuración
    include '../config/db_connection.php';
    header('Content-Type: application/json'); // Asegura que la respuesta sea JSON

    if ($_SERVER["REQUEST_METHOD"] !== "POST") {
        echo json_encode(['success' => false, 'message' => 'Método no permitido']);
        exit();
    }

    // Capturar los valores del formulario
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $usuario = trim($_POST['usuario']);
    $pwd = trim($_POST['pwd']);
    $rol = intval($_POST['rol']);
    $area = intval($_POST['area']);

    try {
        // Verificar si el usuario ya existe
        $checkQuery = "CALL buscar_usuario(?, ?, ?)";
        $checkStmt = $conn->prepare($checkQuery);
        if (!$checkStmt) {
            throw new Exception("Error en la consulta: " . $conn->error);
        }

        $checkStmt->bind_param("sss", $nombre, $correo, $usuario);
        $checkStmt->execute();
        $result = $checkStmt->get_result();

        if ($result && $result->num_rows > 0) {
            $result->close();
            $checkStmt->close();
            echo json_encode(['success' => false, 'message' => 'El usuario ya está registrado']);
            exit();
        }

        $result->close();
        $checkStmt->close();

        // Preparar la inserción
        $query = "CALL insertar_usuario(?, ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error en la consulta de inserción: " . $conn->error);
        }

        $stmt->bind_param("ssssii", $nombre, $correo, $usuario, $pwd, $rol, $area);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            echo json_encode(['success' => true, 'message' => 'Usuario registrado exitosamente']);
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
