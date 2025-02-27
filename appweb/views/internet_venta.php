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

        /* Atrasado de Pago */
        .estado-atrasado {
            background-color: #f8d7da; /* Rojo claro */
            color: #721c24;            /* Rojo oscuro */
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;
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
        <h1 class="h3 mb-0 text-gray-800">Registrar Venta de Internet</h1>
    </div>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tabla de Clientes Venta de Internet</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Nombre</th>
                            <th style="text-align: center;">Teléfono 1</th>
                            <th style="text-align: center;">Teléfono 2</th>
                            <th style="text-align: center;">Día</th>
                            <th style="text-align: center;">Estado de Pago</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                       try {
                            // Llamada al procedimiento almacenado
                            $query = "CALL mostrar_clientes";
                            $result = $conn->query($query); // Usa query() en lugar de prepare() para procedimientos almacenados.
                        
                            if ($result) {
                                if ($result->num_rows > 0) {
                                    // Iterar sobre los registros
                                    while ($row = $result->fetch_assoc()) {
                                        $estadoClass = '';  // Default class
                                    
                                        // Determine the class based on id_estado_pago
                                        if ($row['id_estado_pago'] == 1) {
                                            $estadoClass = 'estado-pendiente';  // Pending payment
                                        } elseif ($row['id_estado_pago'] == 2) {
                                            $estadoClass = 'estado-atrasado';   // Late payment
                                        } elseif ($row['id_estado_pago'] == 3) {
                                            $estadoClass = 'estado-pagado';     // Paid
                                        }
                                    
                                        // Generate the table row with dynamic class
                                        echo "<tr>";
                                        echo "<td style='text-align: center;'>{$row['nombre']}</td>";
                                        echo "<td style='text-align: center;'>{$row['telefono_1']}</td>";
                                        echo "<td style='text-align: center;'>{$row['telefono_2']}</td>";
                                        echo "<td style='text-align: center;'>{$row['dia']}</td>";
                                        echo "<td class='$estadoClass'>{$row['descripcion']}</td>";  // Apply dynamic class
                                        echo "<td style='text-align: center;'>";
                                        if($row['id_estado_pago'] != 3){
                                            echo "<a href='#formularioCliente' class='btn btn-info btn-icon-split' title='Información' onclick='cancelarVenta(); mostrarFormulario(\"" . $row['id_cliente'] . "\");'><span class='icon text-white-50'><i class='fas fa-info-circle'></i></span></a> ";
                                        }
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
                    <!-- Row with 3 Columns -->
                    <div class="d-flex flex-column">
                        <p>Usuario: <span class="m-0 font-weight-bold text-primary"><?php echo $_SESSION['nombre']; ?></span></p>
                        <p>Tipo Usuario: <span class="m-0 font-weight-bold text-primary"><?php echo $_SESSION['rol']; ?></span></p>
                        <p>Fecha: <span class="m-0 font-weight-bold text-primary"><?php echo date('d-m-Y'); ?></span></p>
                    </div>
                    <div class="row">
                        <input type="hidden" id="id_cliente" name="id_cliente">
                        <input type="hidden" id="id_paquete_internet" name="id_paquete_internet">
                        <input type="hidden" id="id_estado" name="id_estado">
                        <div class="col-md-4 form-group">
                            <label for="nombre_cliente">Nombre</label>
                            <input type="text" class="form-control" id="nombre_cliente" name="nombre_cliente"  disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="id_estado_pag_cliente">Estado del Cliente</label>
                            <input type="text" class="form-control" id="id_estado_pago" name="id_estado_pago" disabled>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="dia_cliente">Dia de Pago</label>
                            <input type="text" class="form-control" id="dia_cliente" name="dia_cliente" disabled>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="mensualidad_pago_cliente">Mensualidad</label>
                            <input type="text" class="form-control" id="mensualidad_pago_cliente" name="mensualidad_pag_clienteo"  placeholder = "<?php echo obtenerNombreDelMes();?>" disabled>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="paquete_internet">Paquete de Internet</label>
                            <input type="text" class="form-control" id="paquete_internet" name="paquete_internet" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 form-group">
                            <label for="dias_atrasados">Dias Atrasados</label>
                            <span id="mensaje"></span>
                            <input type="text" class="form-control" id="dias_atrasados" name="dias_atrasados" disabled>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="no_mes_cliente">No. Meses</label>
                            <select name="no_mes_cliente" id="no_mes_cliente" class="form-control">
                                <option value="1">1</option>
                                <option value="2">2</option>
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                                <option value="7">7</option>
                                <option value="8">8</option>
                                <option value="9">9</option>
                                <option value="10">10</option>
                                <option value="11">11</option>
                                <option value="12">12</option>
                            </select>
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="descuento">Descuento</label>
                            <input type="text" class="form-control" id="descuento" name="descuento" placeholder="$0.00">
                        </div>
                        <div class="col-md-3 form-group">
                            <label for="recargo_domicilio">Recargo a Domicilio</label>
                            <input type="text" class="form-control" id="recargo_domicilio" name="recargo_domicilio" placeholder="$0.00">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 form-group"></div>
                        <div class="col-md-3 form-group"></div>
                        <div class="col-md-3 form-group"></div>
                        <div class="col-md-3 form-group">
                            <label for="precio_internet_cliente">Subtotal</label>
                            <input type="text" class="form-control" id="precio_internet_cliente" name="precio_internet_cliente" disabled>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3 form-group"></div>
                        <div class="col-md-3 form-group"></div>
                        <div class="col-md-3 form-group"></div>
                        <div class="col-md-3 form-group">
                            <label for="recargo_dia_tardio">Recargos</label>
                            <input type="text" class="form-control" id="recargo_dia_tardio" name="recargo_dia_tardio" placeholder="$0.00" disabled>
                        </div>
                    </div>
                    <div class="my-2"></div>
                </form>
                <div class="col-md-0 form-group text-left">
                    <button class="btn btn-danger btn-icon-split" onclick="cancelarVenta()">
                        <span class="icon text-white-50">
                            <i class="fas fa-times"></i>
                        </span>
                        <span class="text">Cancelar Venta</span>
                    </button>
                    <button class="btn btn-info btn-icon-split" onclick="verificarVenta()">
                        <span class="icon text-white-50">
                            <i class="fas fa-info-circle"></i>
                        </span>
                        <span class="text">Verificar Venta</span>
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
            fetch('../controllers/buscar_cliente_venta.php?id=' + idCliente, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Completar los campos del formulario con los datos del cliente
                    document.getElementById("id_cliente").value = data.cliente.id_cliente || '';
                    document.getElementById("nombre_cliente").value = data.cliente.nombre || '';
                    document.getElementById("dia_cliente").value = data.cliente.dia || '';
                    document.getElementById("id_paquete_internet").value = data.cliente.id_paquete_internet || '';
                    document.getElementById("paquete_internet").value = data.cliente.paquete_internet || '';
                    document.getElementById("precio_internet_cliente").value = "$" + (data.cliente.precio_internet || 0).toFixed(2);
                    document.getElementById("id_estado").value = data.cliente.id_estado || '';
                    document.getElementById("id_estado_pago").value = data.cliente.id_estado_pago || '';

                    // Calcular los días de atraso y asignarlos al campo correspondiente
                    let diaPago = data.cliente.dia; // Obtener el día de pago
                    let diasAtraso = calcularDiasDeAtraso(diaPago);
                    // Asignar los días de atraso a un campo de entrada (o hacer lo que necesites con el valor)
                    document.getElementById("dias_atrasados").value = diasAtraso;

                    // Muestra un mensaje de éxito
                    Swal.fire({
                        icon: 'success',
                        title: '¡Datos Encontrados!',
                        text: data.message
                    });

                    // Verificar si hay multas si el atraso es mayor a 1 o 3 días
                    if (diasAtraso > 1) {
                        let valor;  // Variable para almacenar el valor de multa
                        if (diasAtraso > 1 && diasAtraso < 3) {
                            valor = 1;  // Multa de 1 si el atraso está entre 1 y 3 días
                        } else {
                            valor = 3;  // Multa de 3 si el atraso es mayor a 3 días
                        }

                        // Realizar una solicitud para obtener la multa (si existe)
                        fetch('../controllers/multas_internet.php?dia=' + valor, {
                            method: 'GET'
                        })
                        .then(response => response.json())
                        .then(multaData => {
                            if (multaData.success) {
                                // Mostrar la multa si existe
                                document.getElementById("recargo_dia_tardio").value = '$' + multaData.multa.costo + '.00';
                                document.getElementById("recargo_dia_tardio").style.color = 'red';
                            } else {
                                // Si no hay multa
                                document.getElementById("recargo_dia_tardio").value = '$0.00';
                            }
                        })
                        .catch(error => {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Hubo un problema al obtener la multa: ' + error
                            });
                        });
                    }
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

   // Función para calcular los días de atraso
    function calcularDiasDeAtraso(diaPago) {
        const fechaActual = new Date(); // Obtener la fecha actual
        const fechaPago = new Date(fechaActual.getFullYear(), fechaActual.getMonth(), diaPago); // Crear la fecha de pago usando el mes y año actuales

        // Calcular la diferencia en milisegundos
        const diferenciaEnMilisegundos = fechaActual - fechaPago;

        // Convertir la diferencia a días
        const diasAtraso = Math.floor(diferenciaEnMilisegundos / (1000 * 60 * 60 * 24));

        // Obtener el campo de días atrasados
        const diasAtrasadosInput = document.getElementById("dias_atrasados");
        const mensajeSpan = document.getElementById("mensaje"); // Elemento donde mostramos el mensaje "Ninguno"

        if (diasAtraso > 0) {
            // Si hay atraso, poner el color en rojo
            diasAtrasadosInput.style.color = 'red';
            if (mensajeSpan) {
                mensajeSpan.textContent = ''; // Limpiar el mensaje si hay atraso
            }
        } else {
            // Si no hay atraso, mostrar el mensaje "Ninguno" en verde
            diasAtrasadosInput.style.color = 'black';  // Cambiar a color negro (o el color original)
            if (mensajeSpan) {
                mensajeSpan.textContent = 'Ninguno'; // Mostrar el mensaje
                mensajeSpan.style.color = 'green';   // Cambiar el color del mensaje a verde
            }
        }

        // Retornar los días de atraso (si hay atraso)
        return (diasAtraso >= 0) ? diasAtraso : 0;
    }

    function verificarVenta(){
        // Obtener los valores necesarios
        const id_cliente = parseFloat(document.getElementById("id_cliente").value) || '';
        const noMeses = parseInt(document.getElementById("no_mes_cliente").value) || 1; // Default to 1 month if no value is selected
        const recargoTardio = parseFloat(document.getElementById("recargo_dia_tardio").value.replace('$', '').replace(',', '')) || 0;
        const recargoDomicilio = parseFloat(document.getElementById("recargo_domicilio").value.replace('$', '').replace(',', '')) || 0;
        const descuento = parseFloat(document.getElementById("descuento").value.replace('$', '').replace(',', '')) || 0;
        const precio_internet_cliente = parseFloat(document.getElementById("precio_internet_cliente").value.replace('$', '').replace(',', '')) || 0;
        
        // Calcular subtotal
        const subtotal = precio_internet_cliente * noMeses;

        // Calcular total (subtotal + recargo a domicilio - descuento)
        const total = (subtotal + recargoDomicilio + recargoTardio) - descuento;

        // Obtener el valor de $_SESSION['idUser'] en JavaScript
        const idUser = <?php echo $_SESSION['idUser']; ?>;
        const mensualidad_numero = <?php echo date('m'); ?>;

        // Crear FormData para enviar los datos de la venta
        var formData = new FormData();
        formData.append('id_user', idUser);  // Agregar el idUser de la sesión
        formData.append('no_meses', noMeses);
        formData.append('mensualidad', mensualidad_numero);
        formData.append('id_cliente', id_cliente);
        formData.append('recargo_tardio', recargoTardio);
        formData.append('recargo_domicilio', recargoDomicilio);
        formData.append('descuento', descuento);
        formData.append('subtotal', precio_internet_cliente);
        formData.append('total', total);

        Swal.fire({
            icon: 'success',
            title: '¡Resumen de Venta!',
            html: `
                <strong>Resumen de Venta:</strong><br>
                <strong>Descuento:</strong> $${descuento.toFixed(2)}<br>
                <strong>No Meses:</strong> ${noMeses}<br>
                <strong>Recargos:</strong> $${recargoTardio.toFixed(2)}<br>
                <strong>Recargo a Domicilio:</strong> $${recargoDomicilio.toFixed(2)}<br>
                <strong>Subtotal:</strong> $${precio_internet_cliente.toFixed(2)}<br>
                <strong>Total:</strong> $${total.toFixed(2)}<br><br>
                <button class="btn btn-success btn-icon-split" onclick="generarVenta()">
                    <span class="icon text-white-50">
                        <i class="fas fa-check"></i>
                    </span>
                    <span class="text">Generar Venta</span>
                </button>
            `,            
            focusConfirm: true,
        });

    }

    function generarVenta() {
        // Obtener los valores necesarios
        const id_cliente = parseFloat(document.getElementById("id_cliente").value) || '';
        const noMeses = parseInt(document.getElementById("no_mes_cliente").value) || 1; // Default to 1 month if no value is selected
        const recargoTardio = parseFloat(document.getElementById("recargo_dia_tardio").value.replace('$', '').replace(',', '')) || 0;
        const recargoDomicilio = parseFloat(document.getElementById("recargo_domicilio").value.replace('$', '').replace(',', '')) || 0;
        const descuento = parseFloat(document.getElementById("descuento").value.replace('$', '').replace(',', '')) || 0;
        const precio_internet_cliente = parseFloat(document.getElementById("precio_internet_cliente").value.replace('$', '').replace(',', '')) || 0;
        
        // Calcular subtotal
        const subtotal = precio_internet_cliente * noMeses;

        // Calcular total (subtotal + recargo a domicilio - descuento)
        const total = (subtotal + recargoDomicilio + recargoTardio) - descuento;

        // Obtener el valor de $_SESSION['idUser'] en JavaScript
        const idUser = <?php echo $_SESSION['idUser']; ?>;
        const mensualidad_numero = <?php echo date('m'); ?>;

        // Crear FormData para enviar los datos de la venta
        var formData = new FormData();
        formData.append('id_user', idUser);  // Agregar el idUser de la sesión
        formData.append('no_meses', noMeses);
        formData.append('mensualidad', mensualidad_numero);
        formData.append('id_cliente', id_cliente);
        formData.append('recargo_tardio', recargoTardio);
        formData.append('recargo_domicilio', recargoDomicilio);
        formData.append('descuento', descuento);
        formData.append('subtotal', precio_internet_cliente);
        formData.append('total', total);

        // Hacer la solicitud AJAX para registrar la venta
        fetch('../controllers/registrar_venta_internet.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Mostrar mensaje de éxito
                Swal.fire({
                    icon: 'success',
                    title: '¡Venta Registrada!',
                    html: `
                        <strong>Resumen de Venta:</strong><br>
                        <strong>Descuento:</strong> $${descuento.toFixed(2)}<br>
                        <strong>No Meses:</strong> ${noMeses}<br>
                        <strong>Recargos:</strong> $${recargoTardio.toFixed(2)}<br>
                        <strong>Recargo a Domicilio:</strong> $${recargoDomicilio.toFixed(2)}<br>
                        <strong>Subtotal:</strong> $${precio_internet_cliente.toFixed(2)}<br>
                        <strong>Total:</strong> $${total.toFixed(2)}<br><br>
                    `,
                    confirmButtonText: 'Aceptar',
                    showCancelButton: true,
                    focusConfirm: true,
                }).then((result) => {
                    if (result.isConfirmed) {
                        // Limpiar manualmente los campos
                        document.getElementById("id_cliente").value = '';
                        document.getElementById("nombre_cliente").value = '';
                        document.getElementById("dia_cliente").value = '';
                        document.getElementById("id_paquete_internet").value = '';
                        document.getElementById("paquete_internet").value = '';
                        document.getElementById("precio_internet_cliente").value = '';
                        document.getElementById("id_estado").value = '';
                        document.getElementById("id_estado_pago").value = '';
                        document.getElementById("dias_atrasados").value = '';
                        document.getElementById("no_mes_cliente").value = '1';
                        document.getElementById("descuento").value = '';
                        document.getElementById("recargo_domicilio").value = '';
                        document.getElementById("recargo_dia_tardio").value = '';
                        // Ocultar el formulario después de la venta
                        document.getElementById("formularioCliente").style.display = "none";

                        const idCliente = document.getElementById("id_cliente").value;

                        window.location.href = `internet_venta_historico.php`;
                        // Redirigir al ticket de impresión (si es necesario)
                        //window.location.href = `../ticket_mike42/index.php?id=${idCliente}`;
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
        document.getElementById("nombre_cliente").value = '';
        document.getElementById("dia_cliente").value = '';
        document.getElementById("id_paquete_internet").value = '';
        document.getElementById("paquete_internet").value = '';
        document.getElementById("precio_internet_cliente").value = '';
        document.getElementById("id_estado").value = '';
        document.getElementById("id_estado_pago").value = '';
        document.getElementById("dias_atrasados").value = '';
        document.getElementById("no_mes_cliente").value = '1';
        document.getElementById("descuento").value = '';
        document.getElementById("recargo_domicilio").value = '';
        document.getElementById("recargo_dia_tardio").value = '';

        // Ocultar el formulario de cliente
        document.getElementById("formularioCliente").style.display = "none";
    }

</script>
<?php
    require_once '../include/footer.php';
?>
