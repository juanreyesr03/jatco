<?php
    include '../config/db_connection.php';
    session_start();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $usuario = $_POST['usuario'];
        $pwd = $_POST['password'];
        $ip = $_POST['ip'];
        $deviceType = $_POST['deviceType'];
        $p_platform = 'web';


        // Llamada al procedimiento almacenado
        $query = "CALL iniciar_sesion(?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $usuario, $pwd, $p_platform);
        $stmt->execute();

        // Obtenemos el resultado
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();

        // Validamos la respuesta
        $mensaje = $row['mensaje'] ?? 'Error desconocido';
        $_SESSION['mensaje'] = $mensaje; // Guardamos el mensaje en la sesión

        if ($mensaje === 'Inicio de sesión exitoso') {
            $_SESSION['active'] = true;
            $_SESSION['idUser'] = $row['id_usuario'];
            $_SESSION['nombre'] = $row['nombre'];
            $_SESSION['idRol'] = $row['id_rol'];
            $_SESSION['rol'] = $row['rol_descripcion'];
            $_SESSION['status'] = $row['id_estado'];
            $_SESSION['area'] = $row['id_area'];

            //Variables del SP
            $fecha = date('Y-m-d');
            $hora_e = date('H:i:s');
            $id_usuario = $row['id_usuario'];
            //Insertar dato al iniciar Sesion
            $conn->next_result(); // Esto asegura que no haya resultados pendientes
            $stmt = $conn->prepare("CALL insertar_configuracion_ingreso(?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $fecha, $ip, $hora_e, $id_usuario, $deviceType);
            // Ejecutar la consulta
            if ($stmt->execute()) {
                echo "Inserción exitosa";
            } else {
                echo "Error en la inserción: " . $stmt->error;
            }
            header("Location: ../index.php");
        } else {
            header("Location: ../index.php");
        }
        exit();
    }
?>
