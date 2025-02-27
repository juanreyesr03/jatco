<?php
    include '../config/db_connection.php';

    try {
        // Verificar si se recibe el día del cliente
        if (!empty($_GET['dia'])) {  // Ahora se recibe 'dia' en lugar de 'id'
            $dia = $_GET['dia'];  // Obtener el valor de 'dia' de la URL

            // Verificar si existe una multa asociada al día
            $verificar_multa = "CALL buscar_multa_internet(?)";  // Cambié a usar 'dia'
            if ($stmt_verificar = $conn->prepare($verificar_multa)) {
                $stmt_verificar->bind_param("i", $dia); // Se pasa el 'dia' al procedimiento almacenado
                $stmt_verificar->execute();
                $stmt_verificar->store_result();

                // Si existe una multa asociada al día
                if ($stmt_verificar->num_rows > 0) {
                    // Vuelvo a realizar la lectura de los resultados
                    $stmt_verificar->bind_result($dias, $multa);  // Aquí 'dias' y 'multa' corresponden a las columnas seleccionadas

                    // Recuperar el valor de la multa
                    $stmt_verificar->fetch();

                    echo json_encode([
                        'success' => true,
                        'message' => 'Multa encontrada exitosamente',
                        'multa' => [
                            'costo' => $multa   // Se pasa el valor de la multa
                        ]
                    ]);
                } else {
                    echo json_encode(['success' => false, 'message' => 'No se encontró multa asociada al día']);
                }
                // Cerrar la sentencia de verificación
                $stmt_verificar->close();
            } else {
                // Error al preparar la consulta de verificación
                echo json_encode(['success' => false, 'message' => 'Error al preparar la consulta de multa']);
            }

            // Cerrar la conexión
            $conn->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Día no proporcionado']);
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
