<?php
require_once '../config/db_connection.php';

try {
    // Configurar encabezados de respuesta
    header('Content-Type: application/json; charset=utf-8');

    // Leer los datos enviados en formato JSON
    $input = json_decode(file_get_contents('php://input'), true);

    // Verificar si los datos fueron recibidos correctamente
    if ($input === null) {
        throw new Exception("No se recibieron datos JSON válidos.");
    }

    // Verificar que el ID del cliente esté presente
    if (!isset($input['id_numero_parte']) || empty($input['id_numero_parte'])) {
        throw new Exception("El ID del usuario es obligatorio.");
    }

    // Aquí puedes agregar más validaciones si es necesario, por ejemplo:
    // if (empty($input['telefono_1'])) {
    //     throw new Exception("El teléfono 1 es obligatorio.");
    // }

    // Verificar los datos recibidos
    error_log("Datos recibidos: " . json_encode($input)); // Registra los datos recibidos en el log del servidor para inspección

    // Preparar la consulta
    $query = "CALL editar_parte(?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular los parámetros con los valores del formulario
    $stmt->bind_param(
        "issi", // Los tipos de los parámetros (i = int, s = string)
        $input['id_numero_parte'],
        $input['numero'],
        $input['nombre'],
        $input['id_estado']
    );

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Parte actualizado exitosamente.']);
    } else {
        throw new Exception("Error al actualizar la parte: " . $stmt->error);
    }
} catch (Exception $e) {
    // En caso de error, devolver un mensaje en formato JSON
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
} finally {
    // Cerrar el statement y la conexión
    if (isset($stmt) && $stmt instanceof mysqli_stmt) {
        $stmt->close(); // Cerrar el statement si existe
    }
    $conn->close(); // Cerrar la conexión
}
?>
