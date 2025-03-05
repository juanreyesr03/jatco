<?php
    require_once '../include/menu.php';
    include '../config/db_connection.php';
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Registrar Mercancia</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Muestra Listado de Productos Registrados</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <style>
                    /* Pendiente de Pago */
                    .inactivo {
                        background-color: #fff3cd; /* Amarillo claro */
                        color: #856404;           /* Marrón oscuro */
                        font-weight: bold;
                        padding: 5px;
                        border-radius: 5px;       /* Bordes redondeados */
                        text-align: center;
                    }

                    .activo {
                        background-color: #d4edda; /* Verde claro */
                        color: #155724;           /* Verde oscuro */
                        font-weight: bold;
                        padding: 5px;
                        border-radius: 5px;
                        text-align: center;
                    }
                </style>
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Numero de Parte</th>
                            <th>Nombre</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                       try {
                            // Llamada al procedimiento almacenado
                            $query = "CALL mostrar_partes";
                            $result = $conn->query($query); // Usa query() en lugar de prepare() para procedimientos almacenados.
                        
                            if ($result) {
                                if ($result->num_rows > 0) {
                                    // Iterar sobre los registros
                                    while ($row = $result->fetch_assoc()) {
                                        $estadoClass = '';  // Default class
                                        // Determine the class based on id_estado_pago
                                        if ($row['id_estado'] == 1) {
                                            $estadoClass = 'activo';  // Pending payment
                                        } elseif ($row['id_estado'] == 2) {
                                            $estadoClass = 'inactivo';   // Late payment
                                        }

                                        echo "<tr>";
                                        echo "<td style='text-align: left;'>{$row['numero_parte']}</td>";
                                        echo "<td style='text-align: left;'>{$row['nombre']}</td>";
                                        echo "<td class='$estadoClass'; style='text-align: left;'>{$row['descripcion']}</td>";
                                        echo "<td style='text-align: left;'>";
                                        echo "<a href='#formularioPerfil' class='btn btn-info btn-circle btn-sm' title='Editar' onclick='mostrarFormulario(\"" . $row['id_numero_parte'] . "\")'><i class='fas fa-info-circle'></i></a> ";
                                        echo "<a href='#' class='btn btn-danger btn-circle btn-sm' title='Eliminar' onclick='eliminarCliente(\"" . $row['id_numero_parte'] . "\")'><i class='fas fa-trash'></i></a> ";
                                        echo "</td>";
                                        echo "</tr>";
                                    }
                                } else {
                                    echo "<tr><td colspan='4' style='text-align: center;'>No hay registros disponibles.</td></tr>";
                                }
                        
                                // Liberar el resultado para evitar conflictos con futuras consultas
                                $result->free();
                            } else {
                                echo "<tr><td colspan='4' style='text-align: center;'>Error en la consulta: " . $conn->error . "</td></tr>";
                            }
                        } catch (Exception $e) {
                            echo "<tr><td colspan='4' style='text-align: center;'>Error al cargar clientes: " . $e->getMessage() . "</td></tr>";
                        }
                    ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Columna para los datos de la empresa y directorio telefónico -->
        <div class="col-lg-12">
            <!-- Tarjeta de Datos de la Empresa -->
            <div class="card shadow mb-4">
                <a href="#collapseCardStatus" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardStatus">
                    <h6 class="m-0 font-weight-bold text-primary">Registrar Número de Parte</h6>
                </a>
                <div class="collapse show" id="collapseCardStatus">
                    <div class="card-body">
                        <form class="user" id="formRegistrarUsuario">
                            <div class="form-group">
                                <label for="numero">Número de Parte</label>
                                <input type="text" class="form-control" id="numero" name="numero" placeholder="Número de Parte">
                            </div>
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre de Parte">
                            </div>
                            <div class="my-2"></div>
                            <button type="submit" class="btn btn-success btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="text">Registrar Parte</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Collapsable Card Example -->
    <div class="card shadow mb-4" id="formularioPerfil" style="display:none;">
        <!-- Collapsable Card Example -->
        <!-- Card Header - Accordion -->
        <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
            role="button" aria-expanded="true" aria-controls="collapseCardExample">
            <h6 class="m-0 font-weight-bold text-primary">Editar Parte</h6>
        </a>
        <!-- Card Content - Mostrar Datos -->
        <div class="collapse show" id="collapseCardExample">
            <div class="card-body">
                <form class="user">
                    <input type="hidden" id="id_numero_parte" name="id_numero_parte">
                    <input type="hidden" id="id_estado" name="id_estado">
                    <div class="form-group">
                        <label for="numero_editar">Número de Parte</label>
                        <input type="text" class="form-control" id="numero_editar" name="numero_editar" placeholder="Número de Parte" disabled>
                    </div>
                    <div class="form-group">
                        <label for="nombre_editar">Nombre de Parte</label>
                        <input type="text" class="form-control" id="nombre_editar" name="nombre_editar" placeholder="Nombre de Parte" disabled>
                    </div>
                </form>
                <div class="col-md-0 form-group text-left">
                    <a href="#formularioPerfil" class="btn btn-warning btn-icon-split" id="editarInformacion" onclick="habilitarEdicion()">
                        <span class="icon text-white-50">
                            <i class="fas fa-exclamation-triangle"></i>
                        </span>
                        <span class="text">Editar Información</span>
                    </a>
                    <a href="#formularioPerfil" class="btn btn-success btn-icon-split" id="guardarCambios" onclick="guardarCambios()" disabled>
                        <span class="icon text-white-50">
                            <i class="fas fa-check"></i>
                        </span>
                        <span class="text">Guardar Cambios</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- /.container-fluid -->
<?php
    require_once '../include/footer.php';
?>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function mostrarFormulario(idCliente) {
        var formulario = document.getElementById("formularioPerfil");
        // Cambia el estado de visibilidad del formulario
        if (formulario.style.display === "none" || formulario.style.display === "") {
            formulario.style.display = "block";  // Muestra el formulario
        } else {
            formulario.style.display = "none";  // Oculta el formulario
        }

        if (formulario.style.display === "block") {
            // Realizamos la solicitud para obtener los datos del cliente
            fetch('../controllers/buscar_parte.php?id=' + idCliente, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Completar los campos del formulario con los datos del cliente
                    document.getElementById("id_numero_parte").value = data.usuario.id_numero_parte || '';
                    document.getElementById("id_estado").value = data.usuario.id_estado || '';

                    document.getElementById("numero_editar").value = data.usuario.numero_parte || '';
                    document.getElementById("nombre_editar").value = data.usuario.nombre || '';                    

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
                fetch('../controllers/eliminar_usuario.php?id=' + idCliente, {
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

    document.getElementById('formRegistrarUsuario').addEventListener('submit', function(e) {
        e.preventDefault();  // Evita la recarga de la página

        var formData = new FormData(this);

        fetch('../controllers/registrar_parte.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                Swal.fire({
                    icon: 'success',
                    title: 'Éxito',
                    text: data.message
                }).then(() => {
                    window.location.href = 'mercancia.php';
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
            console.error("Error en la solicitud:", error);
            Swal.fire({
                icon: 'error',
                title: 'Error',
                text: 'Hubo un problema al procesar la solicitud.'
            });
        });
    });

    // Funciones para habilitar edición
    function habilitarEdicion() {
        // Habilitar solo los campos específicos por su id
        document.getElementById('numero_editar').disabled = false;
        document.getElementById('nombre_editar').disabled = false;

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
                const id_numero_parte = document.getElementById('id_numero_parte').value;
                const id_estado =  document.getElementById('id_estado').value;
                const numero = document.getElementById('numero_editar').value;
                const nombre = document.getElementById('nombre_editar').value;

                // Función que muestra el mensaje de advertencia
                function showWarning(fieldName) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Campo incompleto',
                        text: `Por favor, asegúrate de completar el campo ${fieldName}.`
                    });
                }

                // Verificar que todos los campos estén completos
                const campos = [
                    { value: id_numero_parte, name: 'ID de Parte' },
                    { value: id_estado, name: 'Estado' },
                    { value: numero, name: 'Número' },
                    { value: nombre, name: 'Nombre' }
                ];

                // Iterar sobre los campos y verificar si están vacíos
                for (let i = 0; i < campos.length; i++) {
                    if (!campos[i].value) {
                        showWarning(campos[i].name);
                        return; // Salir del proceso si algún campo está vacío
                    }
                }


                // Obtener datos del formulario
                const datosFormulario = {
                    id_numero_parte: document.getElementById('id_numero_parte').value,
                    numero: document.getElementById('numero_editar').value,
                    nombre: document.getElementById('nombre_editar').value,
                    id_estado: document.getElementById('id_estado').value
                };


                //console.log('ID Cliente:', document.getElementById('id_cliente').value);

                // Enviar datos al servidor
                fetch('../controllers/editar_parte.php', {
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
                            text: 'La información del usuario ha sido actualizada.'
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