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

    // Verificar si todas las variables POST están definidas
    $missing_fields = [];
    if (!isset($_POST['nombre'])) $missing_fields[] = 'S/D';
    if (!isset($_POST['correo'])) $missing_fields[] = 'S/D';
    if (!isset($_POST['usuario'])) $missing_fields[] = 'S/D';
    if (!isset($_POST['pwd'])) $missing_fields[] = 'S/D';
    if (!isset($_POST['rol'])) $missing_fields[] = 'S/D';

    if (!empty($missing_fields)) {
        echo json_encode([
            'success' => false,
            'message' => 'Faltan datos en la solicitud',
            'missing_fields' => $missing_fields
        ]);
        exit();
    }


    // Capturar los valores del formulario
    $nombre = trim($_POST['nombre']);
    $correo = trim($_POST['correo']);
    $usuario = trim($_POST['usuario']);
    $pwd = trim($_POST['pwd']);
    $rol = intval($_POST['rol']); // Asegura que el rol sea un número

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
        $query = "CALL insertar_usuario(?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        if (!$stmt) {
            throw new Exception("Error en la consulta de inserción: " . $conn->error);
        }

        $stmt->bind_param("ssssi", $nombre, $correo, $usuario, $pwd, $rol);
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
