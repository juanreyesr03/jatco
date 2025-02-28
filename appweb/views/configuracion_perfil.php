<?php
    require_once '../include/menu.php';
    include '../config/db_connection.php';
?>
<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Configuración de Perfil</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
    </div>

    <!-- DataTales Example -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Muestra el Historial de Entradas</h6>
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
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Usuario</th>
                            <th>Contraseña</th>
                            <th>Rol</th>
                            <th>Area</th>
                            <th>Estado</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                    <?php
                       try {
                            // Llamada al procedimiento almacenado
                            $query = "CALL mostrar_usuarios";
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
                                        echo "<td style='text-align: left;'>{$row['nombre']}</td>";
                                        echo "<td style='text-align: left;'>{$row['correo']}</td>";
                                        echo "<td style='text-align: left;'>{$row['usuario']}</td>";
                                        echo "<td style='text-align: left;'>{$row['pwd']}</td>";
                                        echo "<td style='text-align: left;'>{$row['rol']}</td>";
                                        echo "<td style='text-align: left;'>{$row['area']}</td>";
                                        echo "<td class='$estadoClass'; style='text-align: left;'>{$row['estado']}</td>";
                                        echo "<td style='text-align: left;'>";
                                        echo "<a href='#formularioPerfil' class='btn btn-info btn-circle btn-sm' title='Editar' onclick='mostrarFormulario(\"" . $row['id_usuario'] . "\")'><i class='fas fa-info-circle'></i></a> ";
                                        if($row['id_estado'] == 1){
                                            echo "<a href='#' class='btn btn-danger btn-circle btn-sm' title='Activar' onclick='cambiarEstado(\"" . $row['id_usuario'] . "\", \"" . $row['estado'] . "\")'><i class='fa fa-power-off'></i></a> ";
                                        }else if($row['id_estado'] == 2){
                                            echo "<a href='#' class='btn btn-primary btn-circle btn-sm' title='Desactivar' onclick='cambiarEstado(\"" . $row['id_usuario'] . "\", \"" . $row['estado'] . "\")'><i class='fa fa-power-off'></i></a> ";
                                        }
                                        echo "<a href='#' class='btn btn-danger btn-circle btn-sm' title='Eliminar' onclick='eliminarCliente(\"" . $row['id_usuario'] . "\")'><i class='fas fa-trash'></i></a> ";
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
                    <h6 class="m-0 font-weight-bold text-primary">Registrar Usuario</h6>
                </a>
                <div class="collapse show" id="collapseCardStatus">
                    <div class="card-body">
                        <form class="user" id="formRegistrarUsuario">
                            <div class="form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del Usuario">
                            </div>
                            <div class="form-group">
                                <label for="correo">Correo</label>
                                <input type="text" class="form-control" id="correo" name="correo" placeholder="Correo del Usuario">
                            </div>
                            <div class="form-group">
                                <label for="usuario">Usuario</label>
                                <input type="text" class="form-control" id="usuario" name="usuario" placeholder="Usuario">
                            </div>
                            <div class="form-group">
                                <label for="pwd">Contraseña</label>
                                <input type="text" class="form-control" id="pwd" name="pwd" placeholder="Contraseña">
                            </div>
                            <div class="form-group">
                                <label for="rol">Rol</label>
                                <?php
                                    include '../config/db_connection.php';
                                    try {
                                        $query_rols = "CALL mostrar_configuracion_usuario_rol";
                                        $result_rols = $conn->query($query_rols);

                                        if ($result_rols) {
                                            if ($result_rols->num_rows > 0) {
                                                echo '<select name="rol" id="rol" class="form-control">';
                                                echo '<option value="">Seleccione un Rol</option>';
                                                while ($row = $result_rols->fetch_assoc()) {
                                                    echo "<option value='{$row['id_rol']}'>{$row['descripcion']}</option>";
                                                }
                                                echo '</select>';
                                            } else {
                                                echo '<p class="text-danger">No hay registros disponibles.</p>';
                                            }
                                        } else {
                                            echo '<p class="text-danger">Error al cargar rol: ' . $conn->error . '</p>';
                                        }

                                        $result_rols->free();
                                    } catch (Exception $e) {
                                        echo '<p class="text-danger">Error al cargar roles: ' . $e->getMessage() . '</p>';
                                    }
                                ?>
                            </div>
                            <div class="form-group">
                                <label for="area">Área</label>
                                <?php
                                    include '../config/db_connection.php';
                                    try {
                                        $query_rols = "CALL mostrar_configuracion_area";
                                        $result_rols = $conn->query($query_rols);

                                        if ($result_rols) {
                                            if ($result_rols->num_rows > 0) {
                                                echo '<select name="area" id="area" class="form-control">';
                                                echo '<option value="">Seleccione un Rol</option>';
                                                while ($row = $result_rols->fetch_assoc()) {
                                                    echo "<option value='{$row['id_area']}'>{$row['descripcion']}</option>";
                                                }
                                                echo '</select>';
                                            } else {
                                                echo '<p class="text-danger">No hay registros disponibles.</p>';
                                            }
                                        } else {
                                            echo '<p class="text-danger">Error al cargar rol: ' . $conn->error . '</p>';
                                        }

                                        $result_rols->free();
                                    } catch (Exception $e) {
                                        echo '<p class="text-danger">Error al cargar roles: ' . $e->getMessage() . '</p>';
                                    }
                                ?>
                            </div>
                            <div class="my-2"></div>
                            <button type="submit" class="btn btn-success btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="text">Registrar Usuario</span>
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
            <h6 class="m-0 font-weight-bold text-primary">Editar Usuario</h6>
        </a>
        <!-- Card Content - Mostrar Datos -->
        <div class="collapse show" id="collapseCardExample">
            <div class="card-body">
                <form class="user">
                    <input type="hidden" id="id_usuario" name="id_usuario">
                    <input type="hidden" id="id_estado" name="id_estado">
                    <input type="hidden" id="id_rol" name="id_rol">
                    <input type="hidden" id="id_area" name="id_area">
                    <div class="form-group">
                        <label for="nombre_editar">Nombre</label>
                        <input type="text" class="form-control" id="nombre_editar" name="nombre_editar" placeholder="Nombre del Usuario" disabled>
                    </div>
                    <div class="form-group">
                        <label for="correo_editar">Correo</label>
                        <input type="text" class="form-control" id="correo_editar" name="correo_editar" placeholder="Correo del Usuario" disabled>
                    </div>
                    <div class="form-group">
                        <label for="usuario_editar">Usuario</label>
                        <input type="text" class="form-control" id="usuario_editar" name="usuario_editar" placeholder="Usuario" disabled>
                    </div>
                    <div class="form-group">
                        <label for="pwd_editar">Contraseña</label>
                        <input type="text" class="form-control" id="pwd_editar" name="pwd_editar" placeholder="Contraseña" disabled>
                    </div>
                    <div class="form-group">
                        <label for="rol_editar">Rol</label>
                        <?php
                            include '../config/db_connection.php';
                            try {
                                $query_rols = "CALL mostrar_configuracion_usuario_rol";
                                $result_rols = $conn->query($query_rols);

                                if ($result_rols) {
                                    if ($result_rols->num_rows > 0) {
                                        echo '<select name="rol_editar" id="rol_editar" class="form-control" disabled>';
                                        echo '<option id="descripcion_rol" name="descripcion_rol" value=""></option>';
                                        while ($row = $result_rols->fetch_assoc()) {
                                            echo "<option value='{$row['id_rol']}'>{$row['descripcion']}</option>";
                                        }
                                        echo '</select>';
                                    } else {
                                        echo '<p class="text-danger">No hay registros disponibles.</p>';
                                    }
                                } else {
                                    echo '<p class="text-danger">Error al cargar rol: ' . $conn->error . '</p>';
                                }

                                $result_rols->free();
                            } catch (Exception $e) {
                                echo '<p class="text-danger">Error al cargar roles: ' . $e->getMessage() . '</p>';
                            }
                        ?>
                    </div>
                    <div class="form-group">
                        <label for="area_editar">Área</label>
                        <?php
                            include '../config/db_connection.php';
                            try {
                                $query_rols = "CALL mostrar_configuracion_area";
                                $result_rols = $conn->query($query_rols);

                                if ($result_rols) {
                                    if ($result_rols->num_rows > 0) {
                                        echo '<select name="area_editar" id="area_editar" class="form-control" disabled>';
                                        echo '<option id="descripcion_area" name="descripcion_area" value=""></option>';
                                        while ($row = $result_rols->fetch_assoc()) {
                                            echo "<option value='{$row['id_area']}'>{$row['descripcion']}</option>";
                                        }
                                        echo '</select>';
                                    } else {
                                        echo '<p class="text-danger">No hay registros disponibles.</p>';
                                    }
                                } else {
                                    echo '<p class="text-danger">Error al cargar rol: ' . $conn->error . '</p>';
                                }

                                $result_rols->free();
                            } catch (Exception $e) {
                                echo '<p class="text-danger">Error al cargar roles: ' . $e->getMessage() . '</p>';
                            }
                        ?>
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
            fetch('../controllers/buscar_usuario.php?id=' + idCliente, {
                method: 'GET'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Completar los campos del formulario con los datos del cliente
                    document.getElementById("id_usuario").value = data.usuario.id_usuario || '';
                    document.getElementById("id_estado").value = data.usuario.id_estado || '';
                    document.getElementById("id_rol").value = data.usuario.id_rol || '';
                    document.getElementById("id_area").value = data.usuario.id_area || '';

                    document.getElementById("nombre_editar").value = data.usuario.nombre || '';
                    document.getElementById("correo_editar").value = data.usuario.correo || '';
                    document.getElementById("usuario_editar").value = data.usuario.usuario || '';
                    document.getElementById("pwd_editar").value = data.usuario.pwd || '';
                    
                    // Establecer el valor del option de descripcion
                    var descripcionRolid = data.usuario.id_rol || '';
                    var descripcionRol = data.usuario.rol || '';
                    document.getElementById("descripcion_rol").value = descripcionRolid;
                    document.getElementById("descripcion_rol").textContent = descripcionRol;

                    var descripcionAreaid = data.usuario.id_area || '';
                    var descripcionArea = data.usuario.area || '';
                    document.getElementById("descripcion_area").textContent = descripcionArea;
                    document.getElementById("descripcion_area").value = descripcionAreaid;

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

        // Mostrar los datos en consola antes de enviarlos
        /*
            for (let pair of formData.entries()) {
                console.log(pair[0] + ": " + pair[1]);
            }
        */

        fetch('../controllers/registrar_usuario.php', {
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
                    window.location.href = 'configuracion_perfil.php';
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
        document.getElementById('nombre_editar').disabled = false;
        document.getElementById('correo_editar').disabled = false;
        document.getElementById('usuario_editar').disabled = false;
        document.getElementById('pwd_editar').disabled = false;
        document.getElementById('rol_editar').disabled = false;
        document.getElementById('area_editar').disabled = false;

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
                const id_usuario = document.getElementById('id_usuario').value;
                const nombre =  document.getElementById('nombre_editar').value;
                const correo = document.getElementById('correo_editar').value;
                const usuario = document.getElementById('usuario_editar').value;
                const pwd = document.getElementById('pwd_editar').value;
                const rol = document.getElementById('rol_editar').value;
                const area = document.getElementById('area_editar').value;

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
                    { value: id_usuario, name: 'ID del cliente' },
                    { value: nombre, name: 'Nombre del usuario' },
                    { value: usuario, name: 'Usuario' },
                    { value: pwd, name: 'Contraseña' },
                    { value: rol, name: 'Rol' },
                    { value: area, name: 'Area' },
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
                    id_usuario: document.getElementById('id_usuario').value,
                    nombre: document.getElementById('nombre_editar').value,
                    correo: document.getElementById('correo_editar').value,
                    usuario: document.getElementById('usuario_editar').value,
                    pwd: document.getElementById('pwd_editar').value,
                    rol: document.getElementById('rol_editar').value,
                    area: document.getElementById('area_editar').value
                };


                //console.log('ID Cliente:', document.getElementById('id_cliente').value);

                // Enviar datos al servidor
                fetch('../controllers/editar_usuario.php', {
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

    function cambiarEstado(idCliente, idEstado) {
        Swal.fire({
            title: '¿Estás seguro?',
            text: 'Cambiar Estado del Usuario',
            icon: 'question',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Sí, guardar',
            cancelButtonText: 'Cancelar'
        }).then((result) => {
            if (result.isConfirmed) {
                // Verificar que el idCliente y idEstado no estén vacíos o inválidos
                if (!idCliente || !idEstado) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Faltan datos',
                        text: 'Por favor, asegúrate de que los campos de ID y Estado estén correctamente definidos.'
                    });
                    return; // Salir si los datos no son válidos
                }

                // Realizar la solicitud fetch para cambiar el estado
                fetch(`../controllers/buscar_usuario_estado.php?id=${idCliente}&estado=${idEstado}`, {
                    method: 'GET'
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
                            title: '¡Estado actualizado!',
                            text: 'El estado del usuario ha sido actualizado correctamente.'
                        }).then(() => {
                            // Recargar si es necesario o realizar alguna acción posterior
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