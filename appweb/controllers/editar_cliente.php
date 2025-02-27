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
    if (!isset($input['id_cliente']) || empty($input['id_cliente'])) {
        throw new Exception("El ID del cliente es obligatorio.");
    }

    // Validar otros campos requeridos
    if (empty($input['nombre']) || empty($input['telefono_1'])) {
        throw new Exception("El nombre y al menos un número de teléfono son obligatorios.");
    }

    // Aquí puedes agregar más validaciones si es necesario, por ejemplo:
    // if (empty($input['telefono_1'])) {
    //     throw new Exception("El teléfono 1 es obligatorio.");
    // }

    // Verificar los datos recibidos
    error_log("Datos recibidos: " . json_encode($input)); // Registra los datos recibidos en el log del servidor para inspección

    // Preparar la consulta
    $query = "CALL editar_cliente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);

    if (!$stmt) {
        throw new Exception("Error al preparar la consulta: " . $conn->error);
    }

    // Vincular los parámetros con los valores del formulario
    $stmt->bind_param(
        "issssisssss", // Los tipos de los parámetros (i = int, s = string)
        $input['id_cliente'],
        $input['nombre'],
        $input['telefono_1'],
        $input['telefono_2'],
        $input['dia'],
        $input['id_paquete_internet'],
        /*
        $input['id_antena'],
        $input['serie_antena'],
        $input['id_router'],
        $input['serie_router'],
        */
        $input['ip'],
        $input['mac'],
        $input['direccion'],
        $input['coordenadas'],
        $input['referencias']
    );

    // Ejecutar la consulta
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Usuario actualizado exitosamente.']);
    } else {
        throw new Exception("Error al actualizar el Usuario: " . $stmt->error);
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
