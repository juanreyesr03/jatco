<?php
    // Incluir archivo de configuración
    include '../config/db_connection.php';

    // Verificar si el formulario fue enviado
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Función para asignar un valor por defecto si el valor está vacío
        function getValue($value, $default) {
            return empty($value) ? $default : $value;
        }

        // Capturar los valores del formulario
        $nombre = strtoupper($_POST['nombre']);
        $telefono_1 = $_POST['telefono_1'];
        $dia = $_POST['dia'];
        $paquete_internet = $_POST['paquete_internet'];
        $id_modelo_antena = $_POST['marca_antena'];
        $id_modelo_router = $_POST['marca_router'];
        $enlace = $_POST['enlace'];
        
        $telefono_2 = strtoupper(getValue($_POST['telefono_2'], 'SIN S/N'));
        $serie_antena =  strtoupper(getValue($_POST['serie_antena'], 'SIN S/N'));
        $serie_router =  strtoupper(getValue($_POST['serie_router'], 'SIN S/N'));
        $ip = getValue($_POST['ip'], '000.000.000.000');
        $mac = getValue($_POST['mac'], '00:00:00:00:00:00');
        $direccion =  strtoupper(getValue($_POST['direccion'], 'SIN DIRECCIÓN'));
        $coordenadas = getValue($_POST['coordenadas'], '00.000000, 00.000000');
        $referencias =  strtoupper(getValue($_POST['referencias'], 'SIN REFERENCIAS'));

        // Array con las variables y sus mensajes de error
        $campos = [
            'nombre' => 'Se necesita agregar un nombre',
            'telefono_1' => 'Se necesita agregar un número de teléfono1',
            'dia' => 'Se necesita agregar un día',
            'paquete_internet' => 'Se necesita seleccionar un paquete de internet',
            'marca_antena' => 'Se necesita seleccionar una marca de antena',
            'marca_router' => 'Se necesita seleccionar una marca de router',
            'enlace' => 'Se necesita seleccionar un enlace'
        ];

        // Recorremos el array de campos y verificamos si están vacíos
        foreach ($campos as $campo => $mensaje) {
            if (empty($_POST[$campo])) {
                echo json_encode(['success' => false, 'message' => $mensaje]);
                exit; // Detenemos la ejecución al encontrar el primer error
            }
        }

        try {
            // Verificar si el cliente ya existe
            $checkQuery = "CALL buscar_cliente(?);";
            $checkStmt = $conn->prepare($checkQuery);
            $checkStmt->bind_param("s", $nombre);
            $checkStmt->execute();
            $result = $checkStmt->get_result();

            if ($result->num_rows > 0) {
                $result->close(); // Cerrar el resultado
                $checkStmt->close(); // Cerrar la consulta preparada
                echo json_encode(['success' => false, 'message' => 'El cliente ya está registrado']);
                exit();
            }
            $result->close();
            $checkStmt->close();

            // Preparar la llamada al procedimiento almacenado para insertar cliente
            $query = "CALL insertar_cliente(?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($query);
            $stmt->bind_param("sssisssssssssss", 
                $nombre, 
                $telefono_1, 
                $telefono_2, 
                $dia, 
                $paquete_internet, 
                $id_modelo_antena, 
                $serie_antena, 
                $id_modelo_router, 
                $serie_router, 
                $ip, 
                $mac, 
                $enlace, 
                $direccion, 
                $coordenadas, 
                $referencias
            );

            // Ejecutar el procedimiento
            $stmt->execute();

            // Verificar si se ejecutó correctamente
            if ($stmt->affected_rows > 0) {
                echo json_encode(['success' => true, 'message' => 'Cliente registrado exitosamente']);
            } else {
                echo json_encode(['success' => false, 'message' => 'Hubo un error al registrar al cliente']);
            }

            // Cerrar la declaración
            $stmt->close();
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
        }

        // Cerrar la conexión
        $conn->close();
        exit();
    }
?>
