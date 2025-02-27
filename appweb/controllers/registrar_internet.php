<?php
    // Incluir archivo de configuración
    include '../config/db_connection.php';

    // Verificar si el formulario fue enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Función para asignar un valor por defecto si el valor está vacío
        function getValue($value, $default) {
            return empty($value) ? $default : $value;
        }

        // Capturar los valores del formulario
        $descripcion = strtoupper($_POST['descripcion_registro']);
        $precio = strtoupper($_POST['precio_registro']);

        try {
            // Verificar si el cliente ya existe
            $checkQuery = "CALL buscar_internet(?);";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("s", $descripcion);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                $result->close(); // Cerrar el resultado
                $checkStmt->close(); // Cerrar la consulta preparada
                echo json_encode(['success' => false, 'message' => 'El paquete ya está registrado']);
                exit();
            }
            $result->close();
            $checkStmt->close();

            // Preparar la llamada al procedimiento almacenado para insertar cliente
            $query = "CALL insertar_internet(?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sd", 
                $descripcion,
                $precio
            );

            // Ejecutar el procedimiento
            $stmt->execute();

            // Verificar si se ejecutó correctamente
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Cliente registrado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Hubo un error al registrar al cliente']);
            }

            // Cerrar la declaración
            $stmt->close();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
        }

        // Cerrar la conexión
        $conn->close();
        exit();
    }
?>
