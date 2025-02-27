<?php
    include '../config/db_connection.php';

    try {
        // Verificar si se recibe el id del usuario
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];

            // Verificar si el usuario existe
            $verificar_usuario = "call mostrar_usuario_id(?)";
            if ($stmt_verificar = $conn->prepare($verificar_usuario)) {
                $stmt_verificar->bind_param("i", $id);
                $stmt_verificar->execute();
                $stmt_verificar->store_result();

                // Si el usuario existe, proceder a obtener los datos
                if ($stmt_verificar->num_rows > 0) {
                    $stmt_verificar->bind_result(
                        $id_usuario, 
                        $nombre, 
                        $correo, 
                        $usuario,
                        $pwd,
                        $id_estado, 
                        $estado,
                        $id_rol,
                        $rol,
                        $id_area,
                        $area
                    );

                    // Recuperar los datos del usuario
                    $stmt_verificar->fetch();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Usuario encontrado exitosamente',
                        'usuario' => [
                            'id_usuario' => $id_usuario,
                            'nombre' => $nombre,
                            'correo' => $correo,
                            'usuario' => $usuario,
                            'pwd' => $pwd,
                            'id_estado' => $id_estado,
                            'estado' => $estado,
                            'id_rol' => $id_rol,
                            'rol' => $rol,
                            'id_area' => $id_area,
                            'area' => $area
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'El usuario no existe']);
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
            echo json_encode(['success' => false, 'message' => 'ID de usuario no proporcionado']);
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
