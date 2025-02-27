<?php
    require_once '../include/menu.php';
    include '../config/db_connection.php';
?>

<div class="container-fluid">
    <style>
        /* Inactivo */
        .inactivo {
            background-color: #f8d7da; /* Rojo claro */
            color: #721c24;            /* Rojo oscuro */
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;
            text-align: center;
        }

        /* Activo */
        .activo {
            background-color: #d4edda; /* Verde claro */
            color: #155724;           /* Verde oscuro */
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;
            text-align: center;
        }

        .activo2 {
            background-color: #fff3cd; /* Amarillo claro */
            color: #856404;           /* Marrón oscuro */
            font-weight: bold;
            padding: 5px;
            border-radius: 5px;       /* Bordes redondeados */
            text-align: center;
        }
    </style>
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Correo</h1>
        <a href="#" class="btn btn-success btn-icon-split" onclick='habilitarRegistro()'>
            <span class="icon text-white-50">
                <i class="fas fa-check"></i>
            </span>
            <span class="text">Registrar Correo</span>
        </a>
    </div>
    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Tabla de Correos</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th style="text-align: center;">Correo</th>
                            <th style="text-align: center;">PIN</th>
                            <th style="text-align: center;">Netflix</th>
                            <th style="text-align: center;">Amazon</th>
                            <th style="text-align: center;">HBO</th>
                            <th style="text-align: center;">Disney</th>
                            <th style="text-align: center;">Estado</th>
                            <th style="text-align: center;">Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        try {
                            // Consulta para obtener los registros de correos
                            $query = "CALL mostrar_paquete_correos";
                            $result = $conn->query($query);

                            if ($result) {
                                if ($result->num_rows > 0) {
                                    // Iterar sobre los registros
                                    while ($row = $result->fetch_assoc()) {
                                        // Determinar la clase según el estado
                                        $estadoClass = $row['id_estado'] == 1 ? 'activo' : 'inactivo';
                                        $netflix = $row['num_plataforma_1'] == 41 ? 'activo2' : 'inactivo';
                                        $amazon = $row['num_plataforma_2'] == 41 ? 'activo2' : 'inactivo';
                                        $hbo = $row['num_plataforma_3'] == 41 ? 'activo2' : 'inactivo';
                                        $disney = $row['num_plataforma_4'] == 41 ? 'activo2' : 'inactivo';
                                        echo "<tr>";
                                        echo "<td style='text-align: center;'>{$row['correo']}</td>";
                                        echo "<td style='text-align: center;'>{$row['pin']}</td>";
                                        echo "<td class='$netflix' style='text-align: center;'>{$row['descripcion_1']}</td>";
                                        echo "<td class='$amazon' style='text-align: center;'>{$row['descripcion_2']}</td>";
                                        echo "<td class='$hbo' style='text-align: center;'>{$row['descripcion_3']}</td>";
                                        echo "<td class='$disney' style='text-align: center;'>{$row['descripcion_4']}</td>";
                                        echo "<td class='$estadoClass' style='text-align: center;'>{$row['descripcion_estado']}</td>";
                                        echo "<td style='text-align: center;'>";
                                        echo "<a href='#' class='btn btn-info btn-circle btn-sm' title='Información' onclick='mostrarFormulario(\"" . $row['id_correo'] . "\")'><i class='fas fa-info-circle'></i></a> ";
                                        echo "<a href='#' class='btn btn-danger btn-circle btn-sm' title='Eliminar' onclick='eliminarInternet(\"" . $row['id_correo'] . "\")'><i class='fas fa-trash'></i></a>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='6' style='text-align: center;'>No hay registros disponibles.</td></tr>";
                                }

                                // Liberar el resultado
                                $result->free();
                            } else {
                                echo "<tr><td colspan='6' style='text-align: center;'>Error en la consulta: " . $conn->error . "</td></tr>";
                            }
                        } catch (Exception $e) {
                            echo "<tr><td colspan='6' style='text-align: center;'>Error al cargar clientes: " . $e->getMessage() . "</td></tr>";
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

    <!-- Collapsable Editar -->
    <div class="card shadow mb-4" id="formularioInternetEditar" style="display:none;">
        <!-- Collapsable Card Example -->
        <!-- Card Header - Accordion -->
        <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
            role="button" aria-expanded="true" aria-controls="collapseCardExample">
            <h6 class="m-0 font-weight-bold text-primary">Mostrar Información Paquete</h6>
        </a>
        <!-- Card Content - Mostrar Datos -->
        <div class="collapse show" id="collapseCardExample">
            <div class="card-body">
                <form class="user">
                    <!-- Row with 3 Columns -->
                    <div class="row">
                        <input type="hidden" id="id_correo" name="id_correo">
                        <div class="col-md-6 form-group">
                            <label for="correo">Descripcion</label>
                            <input type="text" class="form-control" id="correo_editar" name="correo_editar"  disabled>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="PIN">PIN</label>
                            <input type="text" class="form-control" id="PIN_editar" name="PIN_editar"  disabled>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="Netflix">Netflix</label>
                            <input type="text" class="form-control" id="Netflix_editar" name="Netflix_editar"  disabled>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="Amazon">Amazon</label>
                            <input type="text" class="form-control" id="Amazon_editar" name="Amazon_editar" disabled>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="HBO">HBO</label>
                            <input type="text" class="form-control" id="HBO_editar" name="HBO_editar"  disabled>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="Disney">Disney</label>
                            <input type="text" class="form-control" id="Disney_editar" name="Disney_editar"  disabled>
                        </div>
                    </div>
                    <a href="#" class="btn btn-warning btn-icon-split" id="editarInformacion" onclick="habilitarEdicion()">
                        <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <span class="text">Editar Información</span>
                    </a>
                    <a href="#" class="btn btn-success btn-icon-split" id="guardarCambios" onclick="guardarCambios()" disabled>
                        <span class="icon text-white-50">
                            <i class="fas fa-check"></i>
                        </span>
                        <span class="text">Guardar Cambios</span>
                    </a>
                </form>
            </div>
        </div>
    </div>

    <!-- Collapsable Registrar -->
    <div class="card shadow mb-4" id="formularioInternetRegistrar" style="display:none;">
        <!-- Collapsable Card Example -->
        <!-- Card Header - Accordion -->
        <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
            role="button" aria-expanded="true" aria-controls="collapseCardExample">
            <h6 class="m-0 font-weight-bold text-primary">Registrar Correo</h6>
        </a>
        <!-- Card Content - Mostrar Datos -->
        <div class="collapse show" id="collapseCardExample">
            <div class="card-body">
                <form class="user" id="formRegistrarInternet">
                    <!-- Row with 3 Columns -->
                    <div class="row">
                        <div class="col-md-6 form-group">
                            <label for="correo">Correo</label>
                            <input type="text" class="form-control" id="correo" name="correo" placeholder="Correo Electronico">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="PIN">PIN</label>
                            <input type="text" class="form-control" id="PIN" name="PIN" placeholder="PIN del Correo">
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="Netflix">Netflix</label>
                            <select name="Netflix" id="Netflix" class="form-control">
                                <option value="41">Seleciona el estado de la plataforma</option>
                                <option value="41">Activo</option>
                                <option value="34">No Activo</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="Amazon">Amazon</label>
                            <select name="Amazon" id="Amazon" class="form-control">
                                <option value="41">Seleciona el estado de la plataforma</option>
                                <option value="41">Activo</option>
                                <option value="34">No Activo</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="HBO">HBO</label>
                            <select name="HBO" id="HBO" class="form-control">
                                <option value="41">Seleciona el estado de la plataforma</option>
                                <option value="41">Activo</option>
                                <option value="34">No Activo</option>
                            </select>
                        </div>
                        <div class="col-md-6 form-group">
                            <label for="Disney">Disney</label>
                            <select name="Disney" id="Disney" class="form-control">
                                <option value="41">Seleciona el estado de la plataforma</option>
                                <option value="41">Activo</option>
                                <option value="34">No Activo</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-success btn-icon-split">
                        <span class="icon text-white-50">
                            <i class="fas fa-check"></i>
                        </span>
                        <span class="text">Registrar Paquete de Internet</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>

    //Eliminar Datos
    function eliminarInternet(idInternet) {
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
                fetch('../controllers/eliminar_correo.php?id=' + idInternet, {
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

    //Mostrar Datos
    function mostrarFormulario(idInternet) {
        var formulario = document.getElementById("formularioInternetEditar");
        // Cambia el estado de visibilidad del formulario
        if (formulario.style.display === "none" || formulario.style.display === "") {
            formulario.style.display = "block";  // Muestra el formulario
        } else {
            formulario.style.display = "none";  // Oculta el formulario
        }

        
        if (formulario.style.display === "block") {
            // Realizamos la solicitud para obtener los datos del cliente
            fetch('../controllers/buscar_correo.php?id=' + idInternet, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Completar los campos del formulario con los datos del cliente
                    document.getElementById("id_correo").value = data.internet.id_correo || '';
                    document.getElementById("correo_editar").value = data.internet.correo || '';
                    document.getElementById("PIN_editar").value = data.internet.pin || '';
                    document.getElementById("Netflix_editar").value = data.internet.netflix || '';
                    document.getElementById("Amazon_editar").value = data.internet.amazon || '';
                    document.getElementById("HBO_editar").value = data.internet.hbo || '';
                    document.getElementById("Disney_editar").value = data.internet.disney || '';
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
        // Habilitar todos los inputs en el formulario
        document.querySelectorAll('#formularioInternetEditar input, #formularioInternetEditar textarea').forEach(input => {
            input.disabled = false;
        });

        // Habilitar el botón "Guardar Cambios"
        document.getElementById('guardarCambios').disabled = false;

        // Mostrar mensaje de edición habilitada
        Swal.fire({
            icon: 'warning',
            title: 'Modo Edición Activado',
            text: 'Ahora puedes editar la información del cliente.'
        });
    }

    // Funciones para habilitar agregar
    function habilitarRegistro() {
        var formulario = document.getElementById("formularioInternetRegistrar");
        // Cambia el estado de visibilidad del formulario
        if (formulario.style.display === "none" || formulario.style.display === "") {
            formulario.style.display = "block";  // Muestra el formulario
        } else {
            formulario.style.display = "none";  // Oculta el formulario
        }

        Swal.fire({
            icon: 'warning',
            title: 'Modo Registro Activado',
            text: 'Ahora puedes registrar un nuevo paquete.'
        });
        
        if (formulario.style.display === "block") {
            document.getElementById('formRegistrarInternet').addEventListener('submit', function(e) {
                e.preventDefault();  // Evitar la recarga de la página

                // Crear FormData para enviar los datos del formulario
                var formData = new FormData(this);

                // Hacer la solicitud AJAX
                fetch('../controllers/registrar_correo.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Mostrar mensaje de éxito
                        Swal.fire({
                            icon: 'success',
                            title: 'Éxito',
                            text: data.message
                        }).then(() => {
                            // Redirigir si es necesario
                            window.location.href = '../views/plataforma_correo.php';
                        });
                    } else {
                        // Mostrar mensaje de error
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: data.message
                        });
                    }
                })
                .catch(error => {
                    // En caso de error
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Hubo un problema al procesar la solicitud.'
                    });
                });
            });
        }
    }

    // Guardar cambios al editar
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
                const idInternet = document.getElementById('id_paquete_plataforma').value;
                const descripcion = document.getElementById('descripcion_editar').value;
                const precio = document.getElementById('precio_editar').value;

                if (!idInternet || !descripcion || !precio) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campos incompletos',
                        text: 'Por favor, asegúrate de completar todos los campos obligatorios.'
                    });
                    return;
                }

                // Obtener datos del formulario
                const datosFormulario = {
                    id_paquete_plataforma: idInternet,
                    descripcion: descripcion,
                    precio: precio
                };

                console.log(datosFormulario);
                // Enviar datos al servidor
                fetch('../controllers/editar_plataforma.php', {
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
                                text: 'La información del paquete de internet ha sido actualizada.'
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
