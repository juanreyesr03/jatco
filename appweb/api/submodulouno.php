<?php
    include '../config/db_connection.php';
    
    header("Access-Control-Allow-Origin: *");
    header("Content-Type: application/json");
    
    try {
        // Verificar si se recibe el id del usuario
        if (!empty($_GET['id'])) {
            $id = $_GET['id'];

            // Verificar si el usuario existe
            $verificar_usuario = "call mostrar_partes_numero(?)";
            if ($stmt_verificar = $conn->prepare($verificar_usuario)) {
                $stmt_verificar->bind_param("s", $id);
                $stmt_verificar->execute();
                $stmt_verificar->store_result();

                // Si el usuario existe, proceder a obtener los datos
                if ($stmt_verificar->num_rows > 0) {

                    // Recuperar los datos del usuario
                    $stmt_verificar->fetch();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Número de Parte encontrada con exito'
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'El Número de Parte No Existe']);
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
            echo json_encode(['success' => false, 'message' => 'El Número de parte no proporcionado']);
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
