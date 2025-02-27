<?php
include '../config/db_connection.php';

$id = $_GET['id']; // Evita error si 'id' no está definido

if (!$id) {
    echo json_encode(['success' => false, 'message' => 'ID del Venta de Internet no proporcionado']);
    exit();
}

try {
    // Verificar si el cliente existe antes de eliminar
    $verificar_cliente = "SELECT * FROM venta_internet WHERE id_venta_internet = ?";
    if ($stmt_verificar = $conn->prepare($verificar_cliente)) {
        $stmt_verificar->bind_param("i", $id);
        $stmt_verificar->execute();
        $stmt_verificar->store_result();

        if ($stmt_verificar->num_rows > 0) {
            // Proceder a eliminar la venta
            $eliminar_venta = "CALL eliminar_venta_internet(?)";
            if ($stmt_eliminar = $conn->prepare($eliminar_venta)) {
                $stmt_eliminar->bind_param("i", $id);
                $stmt_eliminar->execute();

                if ($stmt_eliminar->affected_rows > 0) {
                    echo json_encode(['success' => true, 'message' => 'Venta de Internet eliminada correctamente']);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se eliminó ninguna fila. Verifica el procedimiento almacenado.']);
                }
                $stmt_eliminar->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de eliminación']);
            }
        } else {
            echo json_encode(['success' => false, 'message' => 'La venta de Internet no existe']);
        }
        $stmt_verificar->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de verificación']);
    }

    $conn->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
    $conn->close();
    exit();
}
?>
