<?php
    include '../config/db_connection.php';

    try {
        // Verificar si se recibe el id del cliente
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];

            // Verificar si el cliente existe
            $verificar_cliente = "call buscar_paquete_perfil_id(?)";
            if ($stmt_verificar = $conn->prepare($verificar_cliente)) {
                $stmt_verificar->bind_param("i", $id);
                $stmt_verificar->execute();
                $stmt_verificar->store_result();

                // Si el cliente existe, proceder a obtener los datos
                if ($stmt_verificar->num_rows > 0) {
                    $stmt_verificar->bind_result(
                        $id_perfil, 
                        $id_correo, 
                        $nombre, 
                        $pin,
                        $id_estado_perfil,
                        $descripcion
                    );

                    // Recuperar los datos del cliente
                    $stmt_verificar->fetch();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Perfil encontrado exitosamente',
                        'internet' => [
                            "id_perfil" => $id_perfil, 
                            "id_correo" => $id_correo, 
                            "nombre" => $nombre, 
                            "pin" => $pin,
                            "id_estado_perfil" => $id_estado_perfil,
                            "descripcion" => $descripcion
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'El Perfil no existe']);
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
            echo json_encode(['success' => false, 'message' => 'ID de Perfil no proporcionado']);
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
