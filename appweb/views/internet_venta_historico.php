<?php
    require_once '../include/menu.php';
    include '../config/db_connection.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Historial de Ventas Registradas</h1>
        <div class="d-flex">
            <!--
            <?php 
                $rol = $_SESSION['rol'];; 
                if($rol == 'Administrador'){
            ?>
            <a href="#" class="btn btn-info btn-icon-split mr-2" onClick="corteDiaGeneral()">
                <span class="icon text-white-50">
                    <i class="fas fa-info-circle"></i>
                </span>
                <span class="text">Corte del Dia General</span>
            </a>
            <?php 
                }
            ?>
            <a href="#" class="btn btn-success btn-icon-split mr-2" onClick="corteDia()">
                <span class="icon text-white-50">
                    <i class="fas fa-check"></i>
                </span>
                <span class="text">Corte del Dia</span>
            </a>
            -->
            <a href="#" class="btn btn-danger btn-icon-split mr-2" onClick="truncateVentas()">
                <span class="icon text-white-50">
                    <i class="fas fa-trash"></i>
                </span>
                <span class="text">Vacias Tablas</span>
            </a>
        </div>
    </div>


    <!-- DataTales Example -->
    <div class="card shadow mb-4">
            <style>
                /* Pendiente de Pago */
                .estado-vencido{
                    background-color: #fff3cd; /* Amarillo claro */
                    color: #856404;           /* Marrón oscuro */
                    font-weight: bold;
                    padding: 5px;
                    border-radius: 5px;       /* Bordes redondeados */
                    text-align: center;
                }

                /* Pagado */
                .estado-activo{
                    background-color: #d4edda; /* Verde claro */
                    color: #155724;           /* Verde oscuro */
                    font-weight: bold;
                    padding: 5px;
                    border-radius: 5px;
                    text-align: center;
                }
            </style>
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tabla de Ventas Registradas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Encargado</th>
                            <th style="text-align: center;">Cliente</th>
                            <th style="text-align: center;">Subtotal</th>
                            <th style="text-align: center;">No Meses</th>
                            <th style="text-align: center;">Descuento</th>
                            <th style="text-align: center;">Total Recargo</th>
                            <th style="text-align: center;">Total</th>
                            <th style="text-align: center;">Fecha</th>
                            <th style="text-align: center;">Fecha Vencimiento</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                        try {                        
                            // Preparar la consulta sin parámetros
                            $query = "CALL mostrar_ventas_internet()";
                            $stmt = $conn->prepare($query);
                        
                            if ($stmt) {
                                // Ejecutar la consulta
                                $stmt->execute();
                        
                                // Obtener el resultado
                                $result = $stmt->get_result();
                        
                                if ($result && $result->num_rows > 0) {
                                    while ($row = $result->fetch_assoc()) {
                                        // Determinar la clase de la fila basada en id_estado_pago
                                        $estadoClass = ($row['id_estado'] == 42) ? 'estado-vencido' : (($row['id_estado'] == 41) ? 'estado-activo' : '');
                        
                                        echo "<tr class='$estadoClass'>";
                                        echo "<td style='text-align: center;'>{$row['nombre_usuario']}</td>";
                                        echo "<td style='text-align: center;'>{$row['nombre_cliente']}</td>";
                                        echo "<td style='text-align: center;'>{$row['subtotal']}</td>";
                                        echo "<td style='text-align: center;'>{$row['mes_pago']}</td>";
                                        echo "<td style='text-align: center;'>{$row['descuento']}</td>";
                                        echo "<td style='text-align: center;'>{$row['total_recargo']}</td>";
                                        echo "<td style='text-align: center;'>{$row['total']}</td>";
                                        echo "<td style='text-align: center;'>{$row['fecha']}</td>";
                                        echo "<td style='text-align: center;'>{$row['fecha_vencimiento']}</td>";
                                        echo "<td style='text-align: center;'>";
                                        echo "<a href='../ticket/internet.php?id=" . $row['id_venta_internet'] . "' class='btn btn-success btn-circle btn-sm' title='Imprimir' target='_blank'><i class='fas fa-check'></i></a> ";
                                        echo "<a href='#' class='btn btn-danger btn-circle btn-sm' title='Eliminar' onclick='eliminarVenta(\"" . $row['id_venta_internet'] . "\")'><i class='fas fa-trash'></i></a>";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='10' style='text-align: center;'>No hay registros disponibles.</td></tr>";
                                }
                        
                                // Liberar el resultado
                                $result->free();
                                $stmt->close();
                            } else {
                                echo "<tr><td colspan='10' style='text-align: center;'>Error en la consulta: " . $conn->error . "</td></tr>";
                            }
                        } catch (Exception $e) {
                            echo "<tr><td colspan='10' style='text-align: center;'>Error al cargar clientes: " . $e->getMessage() . "</td></tr>";
                        } finally {
                            $conn->close();
                        }                       
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function eliminarVenta(idVenta) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¡Esta acción no se puede deshacer!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('../controllers/eliminar_venta_internet.php?id=' + idVenta, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: data.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Si el mes_pago es mayor que 1, mostramos el mensaje correspondiente
                        if (data.message.includes('No se puede eliminar')) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No se puede eliminar',
                                text: data.message,
                                showCancelButton: true,
                                confirmButtonColor: '#d33',
                                cancelButtonColor: '#3085d6',
                                confirmButtonText: 'Sí, eliminar'
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    Swal.fire({
                                        title: 'Iniciar sesión',
                                        html: `
                                            <input id="login-username" class="swal2-input" placeholder="Usuario" type="text">
                                            <input id="login-password" class="swal2-input" placeholder="Contraseña" type="password">
                                        `,
                                        showCancelButton: true,
                                        confirmButtonText: 'Iniciar sesión',
                                        preConfirm: () => {
                                            const username = document.getElementById('login-username').value;
                                            const password = document.getElementById('login-password').value;
                                            
                                            // Aquí, usamos URLSearchParams para enviar los datos como formulario
                                            const formData = new URLSearchParams();
                                            formData.append('username', username);
                                            formData.append('password', password);
                                            
                                            // Realizamos la solicitud POST
                                            return fetch('../controllers/validar_admin.php', {
                                                method: 'POST',
                                                body: formData
                                            })
                                            .then(response => response.json())
                                            .then(data => {
                                                if (!data.success) {
                                                    Swal.showValidationMessage('Usuario o contraseña incorrectos');
                                                    return false;
                                                }
                                                //Eliminar cuando este sea verdadero
                                                fetch('../controllers/eliminar_venta_internet_confirmado.php?id=' + idVenta, {
                                                    method: 'GET'
                                                })
                                                .then(response => response.json())
                                                .then(data => {
                                                    if (data.success) {
                                                        Swal.fire({
                                                            icon: 'success',
                                                            title: '¡Eliminado!',
                                                            text: data.message
                                                        }).then(() => {
                                                            location.reload();
                                                        });
                                                    }
                                                });
                                            })
                                            .catch(error => {
                                                Swal.showValidationMessage('Hubo un error en la solicitud');
                                                console.error(error);
                                            });
                                        }
                                    });
                                }
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        }
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al procesar la solicitud: ' + error
                    });
                });
            }
        });
    }

    function truncateVentas() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¡Esta acción no se puede deshacer!',
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, eliminar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('../controllers/truncate_venta_internet.php', {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            icon: 'success',
                            title: '¡Eliminado!',
                            text: data.message
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Si el mes_pago es mayor que 1, mostramos el mensaje correspondiente
                        if (data.message.includes('No se puede eliminar')) {
                            Swal.fire({
                                icon: 'warning',
                                title: 'No se puede eliminar',
                                text: data.message
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: data.message
                            });
                        }
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al procesar la solicitud: ' + error
                    });
                });
            }
        });
    }
/*
    function corteDiaGeneral() {
        //Datos de Fecha
        let date = new Date();
        let dia = date.getDate();
        let mes = date.getMonth();
        let year = date.getFullYear();
        
        Swal.fire({
            title: '¿Realizar Corte del Dia?',
            html: `¡Corrte del dia: ${dia}/${mes}/${year}!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, Realizar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch('../controllers/corte_dia.php', {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Asegurarse de que el total_ventas no sea null ni undefined
                        const cantidadVentas = data.cantidad_ventas;
                        const totalVentas = data.total_ventas ?? 0; // Usamos el operador de fusión null para asignar 0 si es null o undefined
                        // Mostrar la cantidad de ventas y el total
                        Swal.fire({
                            icon: 'success',
                            title: 'Resumen de Ventas del Día',
                            html: `Cantidad de ventas: ${cantidadVentas}<br>Total de ventas: $${totalVentas}`
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        // Si no se encontraron ventas o hubo un error
                        Swal.fire({
                            icon: 'info',
                            title: 'No hay ventas',
                            text: data.message || 'No se encontraron ventas para el día de hoy.'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al procesar la solicitud: ' + error
                    });
                });
            }
        });
    }

    function corteDia() {
        // Datos de Fecha
        let date = new Date();
        let dia = date.getDate();
        let mes = date.getMonth() + 1; // Se suma 1 porque getMonth() devuelve un índice (0-11)
        let year = date.getFullYear();

        const idUser = <?php echo $_SESSION['idUser']; ?>; // Capturar idUser desde PHP

        Swal.fire({
            title: '¿Realizar Corte del Día?',
            html: `¡Corte del día: ${dia}/${mes}/${year}!`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Sí, Realizar'
        }).then((result) => {
            if (result.isConfirmed) {
                fetch(`../controllers/corte_dia_usuario.php?idUser=${idUser}`, {
                    method: 'GET'
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const cantidadVentas = data.cantidad_ventas;
                        const totalVentas = data.total_ventas ?? 0;
                        Swal.fire({
                            icon: 'success',
                            title: 'Resumen de Ventas del Día',
                            html: `Cantidad de ventas: ${cantidadVentas}<br>Total de ventas: $${totalVentas}`
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            icon: 'info',
                            title: 'No hay ventas',
                            text: data.message || 'No se encontraron ventas para el día de hoy.'
                        });
                    }
                })
                .catch(error => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al procesar la solicitud: ' + error
                    });
                });
            }
        });
    }
*/
</script>

<?php
    require_once '../include/footer.php';
?>
