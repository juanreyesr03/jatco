<?php
    include '../config/db_connection.php';

    try {
        // Verificar si se recibe el id del cliente
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];

            // Verificar si el cliente existe
            $verificar_cliente = "call buscar_cliente_venta_id(?)";
            if ($stmt_verificar = $conn->prepare($verificar_cliente)) {
                $stmt_verificar->bind_param("i", $id);
                $stmt_verificar->execute();
                $stmt_verificar->store_result();

                // Si el cliente existe, proceder a obtener los datos
                if ($stmt_verificar->num_rows > 0) {
                    $stmt_verificar->bind_result(
                        $id_cliente, 
                        $nombre, 
                        $dia, 
                        $id_paquete_internet,
                        $paquete_internet,
                        $precio_internet,
                        $id_estado, 
                        $id_estado_pago, 
                    );

                    // Recuperar los datos del cliente
                    $stmt_verificar->fetch();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Cliente encontrado exitosamente',
                        'cliente' => [
                            'id_cliente' => $id_cliente,
                            'nombre' => $nombre,
                            'dia' => $dia,
                            'id_paquete_internet' => $id_paquete_internet,
                            'paquete_internet' => $paquete_internet,
                            'precio_internet' => $precio_internet,
                            'id_estado' => $id_estado,
                            'id_estado_pago' => $id_estado_pago
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'El cliente no existe']);
                }

                // Cerrar la sentencia de verificación
                $stmt_verificar->close();
            } else {
                // Error al preparar la consulta de verificación
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de verificación']);
            }

            // Cerrar la conexión
            $conn->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'ID de cliente no proporcionado']);
        }
        exit();
    } catch (Exception $e) {
        // Si ocurre un error, capturarlo y mostrarlo
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        // Cerrar la conexión
        $conn->close();
        exit();
    }
?>
