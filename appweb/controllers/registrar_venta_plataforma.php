<?php
// Incluir archivo de configuración
include '../config/db_connection.php';

// Verificar si el formulario fue enviado a través de AJAX (POST)
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Verificar si los parámetros están presentes
    if (!isset($_POST['id_user']) || !isset($_POST['id_cliente']) || !isset($_POST['tabla'])) {
        echo json_encode(['success' => false, 'message' => 'Faltan parámetros']);
        exit();
    }

    // Capturar los valores del formulario enviados por AJAX
    $id_user = $_POST['id_user'];
    $id_cliente = $_POST['id_cliente'];
    $tabla = $_POST['tabla']; // JSON recibido como string

    $datos = json_decode($tabla, true); // Decodificar JSON a un array asociativo

    if (empty($datos) || !is_array($datos)) {
        echo json_encode(['success' => false, 'message' => 'Error: JSON inválido o vacío']);
        exit();
    }

    // Verificar si el cliente existe en la base de datos
    $checkQuery = "SELECT * FROM cliente_datos WHERE id_cliente = ?";
    $checkStmt = $conn->prepare($checkQuery);
    $checkStmt->bind_param("i", $id_cliente);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows == 0) {
        echo json_encode(['success' => false, 'message' => 'El cliente no existe']);
        $checkStmt->close();
        $conn->close();
        exit();
    }
    $checkStmt->close();

    // Verificación de perfiles disponibles
    $perfilesDisponibles = true;
    $plataformasFaltantes = [];

    foreach ($datos as $registro) {
        $plataforma = $registro['plataforma'];
        $no_cuenta = $registro['no_cuentas'];

        // Asignar ID de plataforma
        switch ($plataforma) {
            case 'NETFLIX':
                $id_plataforma = 2;
                break;
            case 'AMAZON':
                $id_plataforma = 3;
                break;
            case 'DISNEY':
                $id_plataforma = 4;
                break;
            case 'HBO':
                $id_plataforma = 5;
                break;
            default:
                $id_plataforma = 1;
                break;
        }

        // Verificar perfiles disponibles en la base de datos
        $perfilQuery = "SELECT COUNT(*) AS total FROM paquete_perfil WHERE id_plataforma = ? AND id_estado_perfil = 1";
        $perfilStmt = $conn->prepare($perfilQuery);
        $perfilStmt->bind_param("i", $id_plataforma);
        $perfilStmt->execute();
        $perfilResult = $perfilStmt->get_result();
        $perfilRow = $perfilResult->fetch_assoc();
        $perfilStmt->close();

        // Si no hay suficientes perfiles, almacenar el error
        if ($perfilRow['total'] < $no_cuenta) {
            $perfilesDisponibles = false;
            $plataformasFaltantes[] = $plataforma;
        }
    }

    // Si falta alguna plataforma, detener ejecución y mostrar error
    if (!$perfilesDisponibles) {
        echo json_encode([
            'success' => false,
            'message' => 'No hay suficientes perfiles disponibles para las plataformas: ' . implode(', ', $plataformasFaltantes)
        ]);
        $conn->close();
        exit();
    }

    // Convertir valores a JSON
    $json_valores = json_encode(array_map(fn($r) => ['plataforma' => $r['plataforma']], $datos));

    try {
        // Ejecutar procedimiento AsignarPlataformasVerificacion
        $sql = "CALL AsignarPlataformasVerificacion()";
        if ($conn->query($sql) === TRUE) {
            // Ejecutar AsignarPlataformas
            $sql = "CALL AsignarPlataformas(?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $id_cliente, $tabla);
            if ($stmt->execute()) {
                // Ejecutar AsignarPlataformasPerfil
                $sql = "CALL AsignarPlataformasPerfil(?)";
                $stmtPerfil = $conn->prepare($sql);
                $stmtPerfil->bind_param("i", $id_cliente);
                if ($stmtPerfil->execute()) {
                    // Ejecutar AsignarDatosClientePlataforma
                    $sql = "CALL AsignarDatosClientePlataforma(?, ?)";
                    $stmtAsignarDatos = $conn->prepare($sql);
                    $stmtAsignarDatos->bind_param("is", $id_cliente, $json_valores);
                    if ($stmtAsignarDatos->execute()) {
                        // Ejecutar generar_venta_plataforma después de AsignarDatosClientePlataforma
                        $sql = "CALL generar_venta_plataforma()";
                        if ($conn->query($sql) === TRUE) {
                            echo json_encode(['success' => true, 'message' => 'Plataformas, perfiles y venta asignados correctamente']);
                        } else {
                            echo json_encode(['success' => false, 'message' => 'Error en generar_venta_plataforma: ' . $conn->error]);
                        }
                    } else {
                        echo json_encode(['success' => false, 'message' => 'Error en AsignarDatosClientePlataforma: ' . $stmtAsignarDatos->error]);
                    }
                    $stmtAsignarDatos->close();
                } else {
                    echo json_encode(['success' => false, 'message' => 'Error en AsignarPlataformasPerfil: ' . $stmtPerfil->error]);
                }
                $stmtPerfil->close();
            } else {
                echo json_encode(['success' => false, 'message' => 'Error en AsignarPlataformas: ' . $stmt->error]);
            }
            $stmt->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'Error en AsignarPlataformasVerificacion: ' . $conn->error]);
        }
    } catch (Exception $e) {
        echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
    }

    // Cerrar conexión
    $conn->close();
    exit();
}
?>
