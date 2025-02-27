<?php
require_once '../config/db_connection.php';

try {
    // Configurar encabezados de respuesta
    header('Content-Type: application/json; charset=utf-8');

    // Leer los datos enviados en formato JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // Verificar que los datos sean válidos
    if (!isset($input['id_paquete_plataforma'])) {
        throw new Exception("El ID del paquete de internet es obligatorio.");
    }

    // Validar otros campos requeridos
    if (empty($input['descripcion']) || empty($input['precio'])) {
        throw new Exception("El nombre y precio son obligatorios.");
    }

    // Preparar la consulta
    $query = "CALL editar_plataforma(?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular los parámetros
    $stmt->bind_param(
        "isd",
        $input['id_paquete_plataforma'],
        $input['descripcion'],
        $input['precio']
    );

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Paquete Internet actualizado exitosamente.']);
    } else {
        throw new Exception("Error al actualizar el cliente: " . $stmt->error);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close(); // Cerrar el statement si existe
    }
    $conn->close(); // Cerrar la conexión
}
?>

