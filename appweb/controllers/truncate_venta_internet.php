<?php
    include '../config/db_connection.php';

    try {
        // Llamar al procedimiento almacenado que realiza la eliminación y respaldo
        $eliminar_cliente = "CALL truncate_internet_ventas()";
        
        // Preparar la consulta
        if ($stmt_eliminar = $conn->prepare($eliminar_cliente)) {
            
            // Ejecutar la consulta
            $stmt_eliminar->execute();
            
            // Verificar si el procedimiento fue ejecutado correctamente
            if ($stmt_eliminar) {
                // Si no hay error, el procedimiento debería haber realizado la acción correctamente
                echo json_encode(['success' => true, 'message' => 'Paquete de Internet eliminado y respaldado correctamente']);
            } else {
                // Si hay un error durante la ejecución
                echo json_encode(['success' => false, 'message' => 'Error al ejecutar el procedimiento de eliminación']);
            }
            
            // Cerrar la sentencia preparada
            $stmt_eliminar->close();
            exit();
        } else {
            // Error al preparar la consulta de eliminación
            echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de eliminación']);
            exit();
        }
        
    } catch (Exception $e) {
        // Capturar cualquier excepción o error inesperado y devolverlo
        echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
        // Cerrar la conexión
        $conn->close();
        exit();
    }
?>
