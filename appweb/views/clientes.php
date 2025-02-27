<?php
    require_once '../include/menu.php';
    include '../config/db_connection.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Clientes Registrados</h1>
    </div>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tabla de Clientes Registrados</h6>
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
                            <?php 
                                if($_SESSION['rol'] == 'Administrador'){
                            ?>
                            <th style="text-align: center;">Acciones</th>
                            <?php
                                }
                            ?>
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
                                        echo "<tr>";
                                        echo "<td style='text-align: left;'>{$row['nombre']}</td>";
                                        echo "<td style='text-align: left;'>{$row['telefono_1']}</td>";
                                        echo "<td style='text-align: left;'>{$row['telefono_2']}</td>";
                                        echo "<td style='text-align: left;'>{$row['dia']}</td>";
                                        if($_SESSION['rol'] == 'Administrador'){
                                        echo "<td style='text-align: left;'>";
                                        echo "<a href='#formularioCliente' class='btn btn-info btn-circle btn-sm' title='Información' onclick='mostrarFormulario(\"" . $row['id_cliente'] . "\")'><i class='fas fa-info-circle'></i></a> ";
                                        echo "<a href='#' class='btn btn-danger btn-circle btn-sm' title='Eliminar' onclick='eliminarCliente(\"" . $row['id_cliente'] . "\")'><i class='fas fa-trash'></i></a>";
                                        echo "</td>";
                                        }
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
            <h6 class="m-0 font-weight-bold text-primary">Mostrar Información Cliente</h6>
        </a>
        <!-- Card Content - Mostrar Datos -->
        <div class="collapse show" id="collapseCardExample">
            <div class="card-body">
                <form class="user">
                    <!-- Row with 3 Columns -->
                    <div class="row">
                    <input type="hidden" id="id_cliente" name="id_cliente" value="<?php echo $cliente['id_cliente']; ?>">
                        <input type="hidden" id="id_paquete_internet" name="id_paquete_internet">
                        <input type="hidden" id="id_paquete_plataforma" name="id_paquete_plataforma">
                        <input type="hidden" id="id_antena" name="id_antena">
                        <input type="hidden" id="id_router" name="id_router">
                        <input type="hidden" id="id_panel_enlace" name="id_panel_enlace">
                        <input type="hidden" id="id_estado" name="id_estado">
                        <div class="col-md-4 form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="nombre" name="nombre"  disabled>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="telefono_1">Teléfono 1</label>
                            <input type="text" class="form-control" id="telefono_1" name="telefono_1"  disabled>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="telefono_2">Teléfono 2</label>
                            <input type="text" class="form-control" id="telefono_2" name="telefono_2"  disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="dia">Día de Contrato</label>
                            <input type="text" class="form-control" id="dia" name="dia" disabled >
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="paquete_internet_editar">Paquete de Internet</label>
                            <?php
                                include '../config/db_connection.php';
                                try {
                                    // Llamada al procedimiento almacenado
                                    $query_internet = "CALL mostrar_paquete_internet";
                                    $stmt = $conn->prepare($query_internet);
                                    if (!$stmt->execute()) {
                                        throw new Exception($conn->error);
                                    }
                                    
                                    $resultado = $stmt->get_result();
                                    if ($resultado->num_rows > 0) {
                                        echo '<select name="paquete_internet_editar" id="paquete_internet_editar" class="form-control" disabled>';
                                        echo '<option id="paquete_internet" name="paquete_internet" value=""></option>';
                                        while ($row = $resultado->fetch_assoc()) {
                                            echo "<option value='{$row['id_paquete_internet']}'>{$row['descripcion']} - $ {$row['precio']}</option>";
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
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="marca_antena">Marca Antena</label>
                            <input type="text" class="form-control" id="marca_antena" name="marca_antena"  disabled>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="modelo_antena">Modelo Antena</label>
                            <input type="text" class="form-control" id="modelo_antena" name="modelo_antena"  disabled>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="serie_antena">No. Serie</label>
                            <input type="text" class="form-control" id="serie_antena" name="serie_antena"  disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="marca_router">Marca Router</label>
                            <input type="text" class="form-control" id="marca_router" name="marca_router"  disabled>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="modelo_router">Modelo Router</label>
                            <input type="text"  class="form-control" id="modelo_router" name="modelo_router"  disabled>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="serie_router">No. Serie</label>
                            <input type="text" class="form-control" id="serie_router" name="serie_router" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="mac">IP</label>
                            <input type="text" class="form-control" id="ip" name="ip" disabled>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="panelFrecuencia">MAC</label>
                            <input type="text" class="form-control" id="mac" name="mac" disabled>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="enlace">Enlace</label>
                            <input type="text" class="form-control" id="enlace" name="enlace" disabled>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="direccion">Dirección</label>
                            <input type="text" class="form-control" id="direccion" name="direccion" disabled>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="coordenadas">Coordenadas</label>
                            <input type="text" class="form-control" id="coordenadas" name="coordenadas" disabled>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="referencias">Referencias</label>
                            <textarea class="form-control" id="referencias" name="referencias" disabled></textarea>
                        </div>
                    </div>
                    <a href="#formularioCliente" class="btn btn-warning btn-icon-split" id="editarInformacion" onclick="habilitarEdicion()">
                        <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <span class="text">Editar Información</span>
                    </a>
                    <a href="#formularioCliente" class="btn btn-success btn-icon-split" id="guardarCambios" onclick="guardarCambios()" disabled>
                        <span class="icon text-white-50">
                            <i class="fas fa-check"></i>
                        </span>
                        <span class="text">Guardar Cambios</span>
                    </a>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    function eliminarCliente(idCliente) {
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
                fetch('../controllers/eliminar_cliente.php?id=' + idCliente, {
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
        });
    }

    function mostrarFormulario(idCliente) {
        var formulario = document.getElementById("formularioCliente");
        // Cambia el estado de visibilidad del formulario
        document.getElementById('guardarCambios').disabled = false;
        if (formulario.style.display === "none" || formulario.style.display === "") {
            formulario.style.display = "block";  // Muestra el formulario
        } else {
            formulario.style.display = "none";  // Oculta el formulario
        }

        
        if (formulario.style.display === "block") {
            // Realizamos la solicitud para obtener los datos del cliente
            fetch('../controllers/buscar_cliente.php?id=' + idCliente, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Completar los campos del formulario con los datos del cliente
                    document.getElementById("id_cliente").value = data.cliente.id_cliente || '';
                    document.getElementById("nombre").value = data.cliente.nombre || '';
                    document.getElementById("telefono_1").value = data.cliente.telefono_1 || '';
                    document.getElementById("telefono_2").value = data.cliente.telefono_2 || '';
                    document.getElementById("dia").value = data.cliente.dia || '';
                    var descripcionValor = data.cliente.paquete_internet || '';
                    var descripcionValorid = data.cliente.id_paquete_internet || '';
                    document.getElementById("id_paquete_internet").value = descripcionValorid;
                    var optionElement = document.getElementById("paquete_internet");
                    optionElement.value = descripcionValorid;
                    optionElement.textContent = descripcionValor;
                    document.getElementById("id_antena").value = data.cliente.id_antena || '';
                    document.getElementById("marca_antena").value = data.cliente.marca_antena || '';
                    document.getElementById("modelo_antena").value = data.cliente.modelo_antena || '';
                    document.getElementById("serie_antena").value = data.cliente.serie_antena || '';
                    document.getElementById("id_router").value = data.cliente.id_router || '';
                    document.getElementById("marca_router").value = data.cliente.marca_router || '';
                    document.getElementById("modelo_router").value = data.cliente.modelo_router || '';
                    document.getElementById("serie_router").value = data.cliente.serie_router || '';
                    document.getElementById("ip").value = data.cliente.ip || '';
                    document.getElementById("mac").value = data.cliente.mac || '';
                    document.getElementById("id_panel_enlace").value = data.cliente.id_panel_enlace || '';
                    document.getElementById("enlace").value = data.cliente.enlace || '';
                    document.getElementById("direccion").value = data.cliente.direccion || '';
                    document.getElementById("coordenadas").value = data.cliente.coordenadas || '';
                    document.getElementById("referencias").value = data.cliente.referencias || '';
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

    // Funciones para habilitar edición
    function habilitarEdicion() {
        // Habilitar solo los campos específicos por su id
        document.getElementById('nombre').disabled = false;
        document.getElementById('telefono_1').disabled = false;
        document.getElementById('telefono_2').disabled = false;
        document.getElementById('dia').disabled = false;
        document.getElementById('paquete_internet_editar').disabled = false;
        //document.getElementById('marca_antena').disabled = false;
        //document.getElementById('modelo_antena').disabled = false;
        //document.getElementById('serie_antena').disabled = false;
        //document.getElementById('marca_router').disabled = false;
        //document.getElementById('modelo_router').disabled = false;
        //document.getElementById('serie_router').disabled = false;
        document.getElementById('ip').disabled = false;
        document.getElementById('mac').disabled = false;
        //document.getElementById('enlace').disabled = false;
        document.getElementById('direccion').disabled = false;
        document.getElementById('coordenadas').disabled = false;
        document.getElementById('referencias').disabled = false;


        // Habilitar el botón "Guardar Cambios"
        document.getElementById('guardarCambios').disabled = false;

        // Mostrar mensaje de edición habilitada
        Swal.fire({
            icon: 'warning',
            title: 'Modo Edición Activado',
            text: 'Ahora puedes editar la información del cliente.'
        });
    }

    function guardarCambios() {
        Swal.fire({
            title: '¿Estás seguro?',
            text: '¿Deseas guardar los cambios realizados?',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Validar campos antes de enviar
                const idCliente = document.getElementById('id_cliente').value;
                const nombre = document.getElementById('nombre').value;

                // Verificar que el campo id_cliente no esté vacío
                if (!idCliente) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campo incompleto',
                        text: 'Por favor, asegúrate de que el campo ID del cliente esté completo.'
                    });
                    return;
                }

                // Verificar que el campo nombre no esté vacío
                if (!nombre) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campo incompleto',
                        text: 'Por favor, asegúrate de completar el nombre del cliente.'
                    });
                    return;
                }

                // Obtener datos del formulario
                const datosFormulario = {
                    id_cliente: document.getElementById('id_cliente').value,
                    nombre: document.getElementById('nombre').value,
                    telefono_1: document.getElementById('telefono_1').value,
                    telefono_2: document.getElementById('telefono_2').value,
                    dia: document.getElementById('dia').value,
                    id_paquete_internet: document.getElementById('paquete_internet_editar').value,
                    //------------------------------------------------------------------------
                    /*
                    id_antena: document.getElementById('id_antena').value,
                    serie_antena: document.getElementById('serie_antena').value,
                    //------------------------------------------------------------------------
                    id_router: document.getElementById('id_router').value,
                    serie_router: document.getElementById('serie_router').value,
                    */
                    //------------------------------------------------------------------------
                    ip: document.getElementById('ip').value,
                    mac: document.getElementById('mac').value,
                    /*
                    id_panel_enlace: document.getElementById('id_panel_enlace').value,
                    */
                    //------------------------------------------------------------------------
                    direccion: document.getElementById('direccion').value,
                    coordenadas: document.getElementById('coordenadas').value,
                    referencias: document.getElementById('referencias').value
                };


                //console.log('ID Cliente:', document.getElementById('id_cliente').value);

                // Enviar datos al servidor
                fetch('../controllers/editar_cliente.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(datosFormulario)
                })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error('Error en la respuesta del servidor');
                        }
                        return response.json();
                    })
                    .then(data => {
                        if (data.success) {
                            Swal.fire({
                                icon: 'success',
                                title: '¡Cambios Guardados!',
                                text: 'La información del cliente ha sido actualizada.'
                            }).then(() => {
                                // Recargar si es necesario
                                location.reload();
                            });
                        } else {
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
                            text: 'Hubo un problema al procesar la solicitud: ' + error.message
                        });
                    });
            }
        });
    }
</script>

<?php
    require_once '../include/footer.php';
?>
