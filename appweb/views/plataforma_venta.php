<?php
    require_once '../include/menu.php';
    include '../config/db_connection.php';

    function obtenerNombreDelMes() {
        // Array de los meses en español
        $meses = array(
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        );
        
        // Obtener el número del mes actual
        $mes = date('n');  // 'n' devuelve el número del mes sin ceros a la izquierda
        
        // Retornar el nombre del mes correspondiente
        return $meses[$mes];
    }        
?>

<div class="container-fluid">
    <style>
        /* Pendiente de Pago */
        .estado-pendiente {
            background-color: #fff3cd; /* Amarillo claro */
            color: #856404;           /* Marrón oscuro */
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;       /* Bordes redondeados */
            text-align: center;
        }

        /* Pagado */
        .estado-pagado {
            background-color: #d4edda; /* Verde claro */
            color: #155724;           /* Verde oscuro */
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;
            text-align: center;
        }
    </style>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Registrar Venta de Plataforma</h1>
    </div>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tabla de Clientes Venta de Plataforma</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Nombre</th>
                            <th style="text-align: center;">Netflix</th>
                            <th style="text-align: center;">Amazon</th>
                            <th style="text-align: center;">HBO</th>
                            <th style="text-align: center;">Disney</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                       try {
                            // Llamada al procedimiento almacenado
                            $query = "CALL mostrar_clientes_plataforma";
                            $result = $conn->query($query); // Usa query() en lugar de prepare() para procedimientos almacenados.
                        
                            if ($result) {
                                if ($result->num_rows > 0) {
                                    // Iterar sobre los registros
                                    while ($row = $result->fetch_assoc()) {
                                        // Determinar la clase de la fila basada en id_estado_pago
                                        $estadoNetflix = ($row['id_netflix'] == 6) ? 'estado-pendiente' : (($row['id_netflix'] == 5) ? 'estado-pagado' : '');
                                        $estadoAmazon = ($row['id_amazon'] == 6) ? 'estado-pendiente' : (($row['id_amazon'] == 5) ? 'estado-pagado' : '');
                                        $estadoHBO = ($row['id_hbo'] == 6) ? 'estado-pendiente' : (($row['id_hbo'] == 5) ? 'estado-pagado' : '');
                                        $estadoDisney = ($row['id_disney'] == 6) ? 'estado-pendiente' : (($row['id_disney'] == 5) ? 'estado-pagado' : '');

                                        // Generate the table row with dynamic class
                                        echo "<tr>";
                                        echo "<td style='text-align: center;'>{$row['nombre']}</td>";
                                        echo "<td class='$estadoNetflix' style='text-align: center;'>{$row['descripcion_netflix']}</td>";
                                        echo "<td class='$estadoAmazon' style='text-align: center;'>{$row['descripcion_amazon']}</td>";
                                        echo "<td class='$estadoHBO' style='text-align: center;'>{$row['descripcion_hbo']}</td>";
                                        echo "<td class='$estadoDisney' style='text-align: center;'>{$row['descripcion_disney']}</td>";
                                        echo "<td style='text-align: center;'>";
                                        echo "<a href='#' class='btn btn-info btn-icon-split' title='Información' onclick='mostrarFormulario(\"" . $row['id_cliente'] . "\")'><span class='icon text-white-50'><i class='fas fa-info-circle'></i></span></a> ";
                                        echo "</td>";
                                        echo "</tr>";
                                    }                                    
                                } else {
                                    echo "<tr><td colspan='5' style='text-align: center;'>No hay registros disponibles.</td></tr>";
                                }
                        
                                // Liberar el resultado para evitar conflictos con futuras consultas
                                $result->free();
                            } else {
                                echo "<tr><td colspan='5' style='text-align: center;'>Error en la consulta: " . $conn->error . "</td></tr>";
                            }
                        } catch (Exception $e) {
                            echo "<tr><td colspan='5' style='text-align: center;'>Error al cargar clientes: " . $e->getMessage() . "</td></tr>";
                        } finally {
                            // Cerrar la conexión
                            $conn->close();
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4" id="formularioCliente" style="display:none;">
    <!-- Collapsable Card Example -->
    <!-- Card Header - Accordion -->
    <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
        role="button" aria-expanded="true" aria-controls="collapseCardExample">
        <h6 class="m-0 font-weight-bold text-primary">Generar Venta Cliente</h6>
    </a>
    <!-- Card Content - Mostrar Datos -->
    <div class="collapse show" id="collapseCardExample">
        <div class="card-body">
                <form class="user">
                    <!-- Datos del Cliente -->
                    <div class="d-flex flex-column">
                        <p>Usuario: <span class="m-0 font-weight-bold text-primary"><?php echo $_SESSION['nombre']; ?></span></p>
                        <p>Tipo Usuario: <span class="m-0 font-weight-bold text-primary"><?php echo $_SESSION['rol']; ?></span></p>
                        <p>Fecha: <span class="m-0 font-weight-bold text-primary"><?php echo date('d-m-Y'); ?></span></p>
                    </div>
                    <div class="row">
                        <input type="hidden" id="id_cliente" name="id_cliente">
                        <input type="hidden" id="id_estado" name="id_estado">
                        <div class="col-md-4 form-group">
                            <label for="nombre_cliente">Nombre:</label>
                            <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente" disabled>
                        </div>
                    </div>

                    <!-- Campos para agregar cuentas a la tabla -->
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="no_cuentas">No. Cuentas:</label>
                            <input type="number" class="form-control" id="no_cuentas" name="no_cuentas">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="paquete_plataforma">Plataforma:</label>
                            <?php
                                include '../config/db_connection.php';
                                try {
                                    // Llamada al procedimiento almacenado
                                    $query_internet = "CALL mostrar_paquete_plataforma";
                                    $stmt = $conn->prepare($query_internet);
                                    if (!$stmt->execute()) {
                                        throw new Exception($conn->error);
                                    }

                                    $resultado = $stmt->get_result();
                                    if ($resultado->num_rows > 0) {
                                        echo '<select name="paquete_plataforma" id="paquete_plataforma" class="form-control">';
                                        echo '<option value="">Seleccione un Paquete</option>';
                                        while ($row = $resultado->fetch_assoc()) {
                                            echo "<option value='{$row['descripcion']}'>{$row['descripcion']}</option>";
                                        }
                                        echo '</select>';
                                    } else {
                                        echo '<p class="text-danger">No hay paquetes disponibles.</p>';
                                    }
                                    $resultado->free(); // Libera el conjunto de resultados
                                    $stmt->close();     // Cierra el statement
                                } catch (Exception $e) {
                                    echo '<p class="text-danger">Error al cargar paquetes: ' . $e->getMessage() . '</p>';
                                }
                            ?>
                        </div>
                        <div class="col-md-12 form-group">
                            <button class="btn btn-success btn-icon-split" onclick="agregarCuenta(event)">
                                <span class="icon text-white-50">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="text">Agregar Cuenta</span>
                            </button>
                        </div>
                    </div>

                    <!-- Tabla para mostrar las cuentas -->
                    <table class="table table-bordered mt-3" id="tablaCuentas">
                        <thead>
                            <tr>
                                <th>Plataforma</th>
                                <th>No. Cuentas</th>
                                <th>Acción</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Las filas se agregarán aquí -->
                        </tbody>
                    </table>
                    <div class="my-2"></div>
                </form>
                <div class="col-md-0 form-group text-left">
                    <button class="btn btn-danger btn-icon-split" onclick="cancelarVenta()">
                        <span class="icon text-white-50">
                            <i class="fas fa-times"></i>
                        </span>
                        <span class="text">Cancelar Venta</span>
                    </button>
                    <button class="btn btn-success btn-icon-split" onclick="generarVenta()">
                        <span class="icon text-white-50">
                            <i class="fas fa-check"></i>
                        </span>
                        <span class="text">Generar Venta</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    // Funcion para mostrar los datos del cliente seleccionado
    function mostrarFormulario(idCliente) {
        var formulario = document.getElementById("formularioCliente");
        // Cambia el estado de visibilidad del formulario
        if (formulario.style.display === "none" || formulario.style.display === "") {
            formulario.style.display = "block";  // Muestra el formulario
        } else {
            formulario.style.display = "none";  // Oculta el formulario
        }

        if (formulario.style.display === "block") {
            // Realizamos la solicitud para obtener los datos del cliente
            fetch('../controllers/buscar_cliente_plataforma.php?id=' + idCliente, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Completar los campos del formulario con los datos del cliente
                    document.getElementById("id_cliente").value = data.cliente.id_cliente || '';
                    document.getElementById("nombre_cliente").value = data.cliente.nombre || '';
                    document.getElementById("id_estado").value = data.cliente.id_estado || '';
                    // Muestra un mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Datos Encontrados!',
                        text: data.message
                    }); 
                } else {
                    // En caso de error, mostrar el mensaje de error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message
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
    }

    let cuentasTemp = [];  // Array temporal para almacenar las cuentas agregadas
    function agregarCuenta(event) {
        event.preventDefault();  // Evita la recarga de la página
        var noCuentas = document.getElementById("no_cuentas").value;
        var plataforma = document.getElementById("paquete_plataforma").value;

        // Verificar si ambos campos están llenos
        if (noCuentas && plataforma) {
            var tablaCuentas = document.getElementById("tablaCuentas").getElementsByTagName('tbody')[0]; // Accede al <tbody>

            // Insertar una nueva fila en la tabla
            var fila = tablaCuentas.insertRow();

            // Insertar las celdas con la plataforma y el número de cuentas
            fila.insertCell(0).textContent = plataforma;
            fila.insertCell(1).textContent = noCuentas;
            fila.insertCell(2).innerHTML = "<button class='btn btn-danger btn-sm' onclick='eliminarCuenta(this)'>Eliminar</button>";

            // Guardar los datos temporalmente en un array
            cuentasTemp.push({
                plataforma: plataforma,
                no_cuentas: parseInt(noCuentas)
            });

            // Limpiar los campos para agregar otra cuenta si lo desean
            document.getElementById("no_cuentas").value = '';
            document.getElementById("paquete_plataforma").value = '';
        } else {
            Swal.fire({
                icon: 'warning',
                title: 'Campos Incompletos',
                text: 'Por favor complete ambos campos (No. Cuentas y Plataforma).'
            });
        }
    }

    // Función para eliminar una cuenta de la tabla y también del array temporal
    function eliminarCuenta(button) {
        var row = button.closest("tr");
        var plataforma = row.cells[0].textContent;  // Obtener la plataforma de la fila
        var noCuentas = parseInt(row.cells[1].textContent);  // Obtener el número de cuentas

        // Eliminar el elemento del array temporal
        cuentasTemp = cuentasTemp.filter(cuenta => cuenta.plataforma !== plataforma || cuenta.no_cuentas !== noCuentas);

        // Eliminar la fila de la tabla
        row.remove();
    }

    function generarVenta() {
        // Obtener los valores necesarios
        const idUser = <?php echo $_SESSION['idUser']; ?>;
        const id_cliente = parseInt(document.getElementById("id_cliente").value) || '';
        const tabla_cuentas = cuentasTemp;

        // Crear FormData para enviar los datos de la venta
        var formData = new FormData();
        formData.append('id_user', idUser);  // Agregar el idUser de la sesión
        formData.append('id_cliente', id_cliente);
        formData.append('tabla', JSON.stringify(tabla_cuentas));

        // Imprimir todos los valores de FormData
        formData.forEach((value, key) => {
            console.log(`${key}: ${value}`);
        });
        
        // Hacer la solicitud AJAX para registrar la venta
        fetch('../controllers/registrar_venta_plataforma.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: 'Recibido',
                    text: data.message
                }).then((result) => {
                    // Este código se ejecuta cuando se hace clic en el botón de OK
                    if (result.isConfirmed) {
                        // Recargar la página
                        window.location.reload(); // Cambiar reload() por window.location.reload()
                    }
                });
            } else {
                // En caso de error, mostrar mensaje de error
                Swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: data.message
                });
            }
        })
        .catch(error => {
            // En caso de error en la solicitud
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al procesar la solicitud.'
            });
        });
    }


    // Función para cancelar la venta
    function cancelarVenta() {
        // Limpiar todos los campos
        document.getElementById("id_cliente").value = '';
        document.getElementById("id_estado").value = '';
        document.getElementById("nombre_cliente").value = '';
        document.getElementById("no_cuentas").value = '';
        document.getElementById("paquete_plataforma").value = '';

        // Ocultar el formulario de cliente
        document.getElementById("formularioCliente").style.display = "none";
    }

</script>
<?php
    require_once '../include/footer.php';
?>
