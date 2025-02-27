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
        $perfil = $_POST['Perfil'];
        $pin = $_POST['PIN'];

        try {
            // Verificar si el cliente ya existe
            $checkQuery = "CALL buscar_perfil(?);";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("s", $perfil);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                $result->close(); // Cerrar el resultado
                $checkStmt->close(); // Cerrar la consulta preparada
                echo json_encode(['success' => false, 'message' => 'El perfil ya está registrado']);
                exit();
            }
            $result->close();
            $checkStmt->close();

            // Preparar la llamada al procedimiento almacenado para insertar cliente
            $query = "CALL insertar_perfil(?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sis", 
                $correo,
                $perfil,
                $pin
            );

            // Ejecutar el procedimiento
            $stmt->execute();

            // Verificar si se ejecutó correctamente
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Perfil registrado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Hubo un error al registrar al perfil']);
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
