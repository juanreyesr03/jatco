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
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Nombre</th>
                            <th>Correo</th>
                            <th>Usuario</th>
                            <th>Contraseña</th>
                            <th>Rol</th>
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
                                        echo "<tr>";
                                        echo "<td style='text-align: left;'>{$row['nombre']}</td>";
                                        echo "<td style='text-align: left;'>{$row['correo']}</td>";
                                        echo "<td style='text-align: left;'>{$row['usuario']}</td>";
                                        echo "<td style='text-align: left;'>{$row['pwd']}</td>";
                                        echo "<td style='text-align: left;'>{$row['descripcion']}</td>";
                                        echo "<td style='text-align: left;'>";
                                        echo "<a href='#formularioPerfil' class='btn btn-info btn-circle btn-sm' title='Editar' onclick='mostrarFormulario(\"" . $row['id_usuario'] . "\")'><i class='fas fa-info-circle'></i></a> ";
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
        <!-- Columna para las tarjetas de descuentos y multas -->
        <div class="col-lg-4">
            <!-- Tarjeta de Descuentos -->
            <div class="card shadow mb-4">
                <a href="#collapseCardDescuentos" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardDescuentos">
                    <h6 class="m-0 font-weight-bold text-primary">Roles</h6>
                </a>
                <div class="collapse show" id="collapseCardDescuentos">
                    <div class="card-body">
                        <input type="text" id="inputTextDescuentos" class="form-control form-control-user" disabled>
                        <div class="my-2"></div>
                        <ul class="list-group" id="registroListDescuentos">
                            <?php
                                $registros = ["Registro 1", "Registro 2", "Registro 3"];
                                for ($i = 0; $i < count($registros); $i++) {
                                    echo '<li class="list-group-item d-flex justify-content-between">' . $registros[$i] . '
                                        <a href="#" class="btn btn-danger btn-circle btn-sm delete-btn" data-index="' . $i . '" data-list="Descuentos"><i class="fas fa-trash"></i></a>
                                    </li>';
                                }
                            ?>
                        </ul>
                        <div class="my-2"></div>
                        <a href="#" class="btn btn-success btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-check"></i>
                            </span>
                            <span class="text">Split Button Success</span>
                        </a>
                    </div>
                </div>
            </div>
        </div>
        <!-- Columna para los datos de la empresa y directorio telefónico -->
        <div class="col-lg-8">
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
                                        $query_rols = "CALL configuracion_usuario_rol";
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
                                $query_rols = "CALL configuracion_usuario_rol";
                                $result_rols = $conn->query($query_rols);

                                if ($result_rols) {
                                    if ($result_rols->num_rows > 0) {
                                        echo '<select name="rol_editar" id="rol_editar" class="form-control" disabled>';
                                        echo '<option id="descripcion_valor" name="descripcion_valor" value=""></option>';
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
                    document.getElementById("nombre_editar").value = data.usuario.nombre || '';
                    document.getElementById("correo_editar").value = data.usuario.correo || '';
                    document.getElementById("usuario_editar").value = data.usuario.usuario || '';
                    document.getElementById("pwd_editar").value = data.usuario.pwd || '';
                    document.getElementById("rol_editar").value = data.usuario.paquete_internet || '';
                    document.getElementById("id_estado").value = data.usuario.id_estado || '';
                    // Establecer el valor del option de descripcion
                    var descripcionValor = data.usuario.descripcion || '';
                    var descripcionValorid = data.usuario.id_rol || '';
                    document.getElementById("descripcion_valor").value = descripcionValorid;
                    document.getElementById("descripcion_valor").textContent = descripcionValor; // Actualizar el texto que aparece en el option

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
        for (let pair of formData.entries()) {
            console.log(pair[0] + ": " + pair[1]);
        }

        fetch('../controllers/registrar_usuario.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            console.log("Respuesta del servidor:", data); // Verifica la respuesta del backend
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
</script>