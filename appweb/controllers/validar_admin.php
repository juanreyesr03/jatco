<?php
    include '../config/db_connection.php';
    
    // Obtener las variables de usuario y contraseña desde el formulario
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Llamar al procedimiento almacenado 'iniciar_sesion_borrado'
    try {
        // Preparamos la consulta para llamar al procedimiento almacenado
        $stmt = $conn->prepare("CALL iniciar_sesion_borrado(?, ?)");

        // Vinculamos los parámetros con las variables de entrada
        $stmt->bind_param("ss", $username, $password);  // 'ss' indica que ambos parámetros son de tipo string

        // Ejecutamos el procedimiento almacenado
        $stmt->execute();

        // Obtenemos el resultado
        $result = $stmt->get_result();

        // Si obtenemos un resultado
        if ($result) {
            $row = $result->fetch_assoc();
            // Verificamos el mensaje del procedimiento almacenado
            if (isset($row['mensaje']) && $row['mensaje'] == 'Inicio de sesión exitoso') {
                echo json_encode(['success' => true, 'message' => 'Inicio de sesión exitoso', 'data' => $row]);
            } else {
                echo json_encode(['success' => false, 'message' => $row['mensaje'] ?? 'Error al iniciar sesión']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al ejecutar el procedimiento almacenado']);
        }

        // Cerramos la sentencia
        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    }

    // Cerramos la conexión
    $conn->close();
?>
