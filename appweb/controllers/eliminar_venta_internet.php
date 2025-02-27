<?php
include '../config/db_connection.php';

try {
    // Verificar si se recibe el id del cliente
    if (!empty($_GET['id'])) {
        $id = $_GET['id'];

        // Verificar si el cliente existe y obtener los datos necesarios
        $verificar_cliente = "SELECT mes_pago, mensualidad, id_estado FROM venta_internet WHERE id_venta_internet = ?";
        if ($stmt_verificar = $conn->prepare($verificar_cliente)) {
            $stmt_verificar->bind_param("i", $id);
            $stmt_verificar->execute();
            $stmt_verificar->store_result();

            // Si el cliente existe, proceder
            if ($stmt_verificar->num_rows > 0) {
                // Obtener los datos de la venta
                $stmt_verificar->bind_result($mes_pago, $mensualidad, $id_estado);
                $stmt_verificar->fetch();

                // Verificar si id_estado es 43 antes de proceder a la eliminación
                if ($id_estado == 43) {
                    // Verificar si mes_pago es mayor a 1
                    if ($mes_pago > 1) {
                        // Validar que la mensualidad contenga un valor entre 1 y 12
                        if (empty($mensualidad) || $mensualidad < 1 || $mensualidad > 12) {
                            echo json_encode([
                                'success' => false,
                                'message' => "Valor de mensualidad no válido. No se puede calcular la fecha de vencimiento."
                            ]);
                        } else {
                            // Construir la fecha de pago considerando el año actual
                            $año_actual = date('Y');
                            $fecha_pago = "$año_actual-$mensualidad-01";

                            // Verificar que la fecha de pago sea válida
                            if (!strtotime($fecha_pago)) {
                                echo json_encode([
                                    'success' => false,
                                    'message' => "Fecha de pago no válida. No se puede calcular la fecha de vencimiento."
                                ]);
                            } else {
                                // Calcular la fecha de vencimiento basándonos en el mes de pago
                                $fecha_vencimiento = date('Y-m-d', strtotime("$fecha_pago +$mes_pago months"));
                                echo json_encode([
                                    'success' => false,
                                    'message' => "No se puede eliminar. El cliente ha pagado $mes_pago meses, y el pago se vence el $fecha_vencimiento."
                                ]);
                            }
                        }
                    } else {
                        // Liberar el resultado antes de hacer la eliminación
                        $stmt_verificar->free_result();

                        // Llamar al procedimiento de eliminación
                        $eliminar_venta = "CALL eliminar_venta_internet(?)";
                        if ($stmt_eliminar = $conn->prepare($eliminar_venta)) {
                            $stmt_eliminar->bind_param("i", $id);
                            $stmt_eliminar->execute();

                            if ($stmt_eliminar->affected_rows > 0) {
                                echo json_encode(['success' => false, 'message' => 'Error al eliminar la Venta de Internet']);
                            } else {
                                echo json_encode(['success' => true, 'message' => 'Venta de Internet eliminada correctamente']);
                            }

                            // Cerrar la sentencia de eliminación
                            $stmt_eliminar->close();
                        } else {
                            // Error al preparar la consulta de eliminación
                            echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de eliminación']);
                        }
                    }
                } else {
                    // Construir la fecha de pago considerando el año actual
                    $año_actual = date('Y');
                    $fecha_pago = "$año_actual-$mensualidad-01";

                    // Verificar que la fecha de pago sea válida
                    if (!strtotime($fecha_pago)) {
                        echo json_encode([
                            'success' => false,
                            'message' => "Fecha de pago no válida. No se puede calcular la fecha de vencimiento."
                        ]);
                    } else {
                        // Calcular la fecha de vencimiento basándonos en el mes de pago
                        $fecha_vencimiento = date('d-m-Y', strtotime("$fecha_pago +$mes_pago months"));
                        echo json_encode([
                            'success' => false,
                            'message' => "No se puede eliminar. El cliente ha pagado $mes_pago meses, y el pago se vence el $fecha_vencimiento."
                        ]);
                    }
                }
            } else {
                echo json_encode(['success' => false, 'message' => 'El Venta de Internet no existe']);
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