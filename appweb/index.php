<?php
    session_start();  // Asegúrate de que la sesión esté iniciada

    // Intentar obtener la dirección IP real del cliente
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        // Si hay proxy, se puede obtener la IP real desde esta cabecera
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } else {
        // Si no, obtenemos la IP directamente
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    // Si se tiene una IP de tipo IPv6, reemplazarla por la IPv4 de localhost
    if ($ip === '::1') {
        $ip = '127.0.0.1';  // Reemplazamos con la IP local en IPv4
    }

    // Detectar el tipo de dispositivo
    $userAgent = $_SERVER['HTTP_USER_AGENT'];
    $deviceType = "Desconocido";

    // Comprobamos si el dispositivo parece ser un móvil o tablet
    if (preg_match('/mobile/i', $userAgent)) {
        $deviceType = "Móvil";
    } elseif (preg_match('/tablet/i', $userAgent)) {
        $deviceType = "Tablet";
    } else {
        $deviceType = "Desktop";
    }
    
    // Verificar si hay un mensaje en la sesión
    if (isset($_SESSION['mensaje'])) {
        $mensaje = $_SESSION['mensaje'];
        echo "<script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>";
        echo "<script>
            document.addEventListener('DOMContentLoaded', function () {
                if ('{$mensaje}' === 'Inicio de sesión exitoso') {
                    Swal.fire({
                        icon: 'success',
                        title: '¡Éxito!',
                        text: '{$mensaje}',
                        confirmButtonText: 'Continuar'
                    }).then(() => {
                        window.location.href = 'views/dashboard.php';
                    });
                } else if ('{$mensaje}' === 'Credenciales incorrectas') {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: '{$mensaje}',
                        confirmButtonText: 'Intentar de nuevo'
                    }).then(() => {
                        location.reload(); // Recargar la página después de un intento fallido
                    });
                } else {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Advertencia',
                        text: '{$mensaje}',
                        confirmButtonText: 'Revisar'
                    }).then(() => {
                        location.reload(); // Recargar la página después de un intento fallido
                    });
                }
            });
        </script>";
        unset($_SESSION['mensaje']); // Eliminamos el mensaje después de mostrarlo
    }
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <meta name="description" content="">
        <meta name="author" content="">
        <title>Jatco - Login</title>
        <!-- Custom fonts for this template-->
        <link href="vendor/fontawesome-free/css/all.min.css" rel="stylesheet" type="text/css">
        <link href="https://fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
        <!-- Custom styles for this template-->
        <link href="css/sb-admin-2.css" rel="stylesheet">
        <link rel="icon" href="img/favicon.ico" type="image/x-icon">
    </head>
    <body class="bg-gradient-primary">
        <div class="container">
            <!-- Outer Row -->
            <div class="d-flex justify-content-center align-items-center vh-100">
                <div class="col-xl-6 col-lg-8 col-md-10">
                    <div class="card o-hidden border-0 shadow-lg">
                        <div class="card-body p-0">
                            <!-- Nested Row within Card Body -->
                            <div class="row">
                                <div class="col-12 text-center">
                                    <div class="p-5">
                                        <img src="img/Jatco_logo.svg" alt="Login Image" style="width: 30%; height: 30%;">
                                        <br><br>
                                        <form class="user" method="POST" action="controllers/procesar_login.php">
                                            <div class="form-group">
                                                <input type="text" name="usuario" class="form-control form-control-user" id="exampleInputEmail" placeholder="Ingresa tu Usuario" required>
                                            </div>
                                            <div class="form-group">
                                                <input type="password" name="password" class="form-control form-control-user" id="exampleInputPassword" placeholder="Ingresa tu Contraseña" required>
                                            </div>
                                            
                                            <!-- Campos ocultos para enviar IP y tipo de dispositivo -->
                                            <input type="hidden" name="ip" value="<?php echo $ip; ?>">
                                            <input type="hidden" name="deviceType" value="<?php echo $deviceType; ?>">

                                            <button type="submit" class="btn btn-primary btn-user btn-block">Login</button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- Bootstrap core JavaScript-->
        <script src="vendor/jquery/jquery.min.js"></script>
        <script src="vendor/bootstrap/js/bootstrap.bundle.min.js"></script>
        <script src="vendor/jquery-easing/jquery.easing.min.js"></script>
        <script src="js/sb-admin-2.min.js"></script>
    </body>
</html>
