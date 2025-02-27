<?php
    include '../config/db_connection.php';

    try {
        // Verificar si se recibe el id del cliente
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];

            // Verificar si el cliente existe
            $verificar_cliente = "SELECT * FROM paquete_plataforma WHERE id_paquete_plataforma = ?";
            if ($stmt_verificar = $conn->prepare($verificar_cliente)) {
                $stmt_verificar->bind_param("i", $id);
                $stmt_verificar->execute();
                $stmt_verificar->store_result();

                // Si el cliente existe, proceder con la eliminación
                if ($stmt_verificar->num_rows > 0) {
                    // Liberar el resultado antes de hacer otra consulta
                    $stmt_verificar->free_result();

                    // Llamar al procedimiento de eliminación
                    $eliminar_cliente = "CALL eliminar_paquete_plataforma(?)";
                    if ($stmt_eliminar = $conn->prepare($eliminar_cliente)) {
                        $stmt_eliminar->bind_param("i", $id);
                        $stmt_eliminar->execute();

                        if ($stmt_eliminar->affected_rows > 0) {
                            echo json_encode(['success' => true, 'message' => 'Paquete de Internet eliminado correctamente']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Error al eliminar el Paquete de Internet']);
                        }

                        // Cerrar la sentencia de eliminación
                        $stmt_eliminar->close();
                    } else {
                        // Error al preparar la consulta de eliminación
                        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de eliminación']);
                    }
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
            echo json_encode(['success' => false, 'message' => 'ID del Paquete de Internet no proporcionado']);
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
