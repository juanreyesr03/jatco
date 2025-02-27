<?php
    require '../config/db_connection.php';

    header('Content-Type: application/json');

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $idUser = $_POST['id_user'] ?? '';
        $fecha = $_POST['fecha'] ?? '';

        if (empty($idUser) || empty($fecha)) {
            echo json_encode(['success' => false, 'message' => 'Faltan parámetros']);
            exit;
        }

        try {
            $stmt = $conn->prepare("CALL mostrar_ventas_internet_corte(?, ?)");
            $stmt->bind_param("is", $idUser, $fecha);
            $stmt->execute();
            $result = $stmt->get_result();

            $ventas = [];
            while ($row = $result->fetch_assoc()) {
                $ventas[] = $row;
            }

            echo json_encode(['success' => true, 'ventas' => $ventas]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => $e->getMessage()]);
        } finally {
            $stmt->close();
            $conn->close();
        }
    }
?>
