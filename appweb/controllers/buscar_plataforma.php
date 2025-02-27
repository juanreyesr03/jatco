<?php
    include '../config/db_connection.php';

    try {
        // Verificar si se recibe el id del cliente
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];

            // Verificar si el cliente existe
            $verificar_cliente = "call buscar_paquete_plataforma_id(?)";
            if ($stmt_verificar = $conn->prepare($verificar_cliente)) {
                $stmt_verificar->bind_param("i", $id);
                $stmt_verificar->execute();
                $stmt_verificar->store_result();

                // Si el cliente existe, proceder a obtener los datos
                if ($stmt_verificar->num_rows > 0) {
                    $stmt_verificar->bind_result(
                        $id_paquete_internet , 
                        $descripcion, 
                        $precio, 
                        $id_estado,
                    );

                    // Recuperar los datos del cliente
                    $stmt_verificar->fetch();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Paquete de Internet encontrado exitosamente',
                        'internet' => [
                            'id_paquete_internet' => $id_paquete_internet,
                            'descripcion' => $descripcion,
                            'precio' => $precio,
                            'id_estado' => $id_estado
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'El Paquete de Internet no existe']);
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
            echo json_encode(['success' => false, 'message' => 'ID de Paquete de Internet no proporcionado']);
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
