<?php
    // Incluir archivo de configuración
    include '../config/db_connection.php';

    // Verificar si el formulario fue enviado a través de AJAX (POST)
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Función para asignar un valor por defecto si el valor está vacío
        function getValue($value, $default) {
            return empty($value) ? $default : $value;
        }

        // Capturar los valores del formulario enviados por AJAX
        $id_user = $_POST['id_user'];
        $no_meses = $_POST['no_meses'];
        $id_cliente = $_POST['id_cliente'];
        $mensualidad = $_POST['mensualidad'];
        $recargo_tardio = $_POST['recargo_tardio'];
        $recargo_domicilio = $_POST['recargo_domicilio'];
        $descuento = $_POST['descuento'];
        $subtotal = $_POST['subtotal'];
        $total = $_POST['total'];
        $fecha = (string) date('Y-m-d');
        $id_estado = 41; //Activo

        try {
            // Verificar si el cliente ya existe en la base de datos
            $checkQuery = "SELECT * FROM cliente_datos WHERE id_cliente = ?";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("i", $id_cliente);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            // Si el cliente no existe, retornar un error
            if ($result->num_rows == 0) {
                echo json_encode(['success' => false, 'message' => 'El cliente no existe']);
                exit();
            }

            // Preparar la llamada al procedimiento almacenado para registrar la venta
            $query = "CALL insertar_venta_internet(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("iiiidddddsi", 
                $id_user, 
                $no_meses, 
                $mensualidad, 
                $id_cliente, 
                $recargo_tardio, 
                $recargo_domicilio, 
                $descuento, 
                $subtotal, 
                $total,
                $fecha,
                $id_estado
            );

            // Ejecutar la consulta
            $stmt->execute();

            // Verificar si se registró correctamente
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Venta registrada exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Hubo un error al registrar la venta'. $conn->error]);
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
