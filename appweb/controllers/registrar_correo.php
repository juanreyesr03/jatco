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
        $correo = $_POST['correo'];
        $pin = $_POST['PIN'];
        $plataforma_1 = $_POST['Netflix'];
        $plataforma_2 = $_POST['Amazon'];
        $plataforma_3 = $_POST['HBO'];
        $plataforma_4 = $_POST['Disney'];

        try {
            // Verificar si el cliente ya existe
            $checkQuery = "CALL buscar_correo(?);";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("s", $correo);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                $result->close(); // Cerrar el resultado
                $checkStmt->close(); // Cerrar la consulta preparada
                echo json_encode(['success' => false, 'message' => 'El correo ya está registrado']);
                exit();
            }
            $result->close();
            $checkStmt->close();

            // Preparar la llamada al procedimiento almacenado para insertar cliente
            $query = "CALL insertar_correo(?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("ssiiii", 
                $correo,
                $pin,
                $plataforma_1,
                $plataforma_2,
                $plataforma_3,
                $plataforma_4
            );

            // Ejecutar el procedimiento
            $stmt->execute();

            // Verificar si se ejecutó correctamente
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Correo registrado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Hubo un error al registrar el Correo']);
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
