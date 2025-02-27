<?php
    // Incluir archivo de configuración y menú
    include '../config/db_connection.php';
    require_once '../include/menu.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Registrar Clientes</h1>
    </div>
    <div class="col-lg-12">
        <!-- Tarjeta de Datos de la Empresa -->
        <div class="card shadow mb-4">
            <a href="#collapseCardStatus" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardStatus">
                <h6 class="m-0 font-weight-bold text-primary">Registrar Clientes</h6>
            </a>
            <div class="collapse show" id="collapseCardStatus">
                <div class="card-body">
                    <form class="user" id="formRegistrarCliente">
                        <!-- Row with 3 Columns -->
                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="nombre">Nombre</label>
                                <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del Cliente">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="telefono_1">Teléfono 1</label>
                                <input type="text" class="form-control" id="telefono_1" name="telefono_1" placeholder="000-000-00-00">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="telefono_2">Teléfono 2</label>
                                <input type="text" class="form-control" id="telefono_2" name="telefono_2" placeholder="000-000-00-00">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="dia">Día de Contrato</label>
                                <input type="number" class="form-control" id="dia" name="dia">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="paquete_internet">Paquete de Internet</label>
                                <?php
                                    try {
                                        // Llamada al procedimiento almacenado
                                        $query_internet = "CALL mostrar_paquete_internet";
                                        $stmt = $conn->prepare($query_internet);
                                        if (!$stmt->execute()) {
                                            throw new Exception($conn->error);
                                        }
                                        
                                        $resultado = $stmt->get_result();
                                        if ($resultado->num_rows > 0) {
                                            echo '<select name="paquete_internet" id="paquete_internet" class="form-control">';
                                            echo '<option value="">Seleccione un Paquete</option>';
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
                                <?php
                                    try {
                                        // Llamada al procedimiento almacenado
                                        $query_antena = "CALL mostrar_modelo_antena";
                                        $stmt = $conn->prepare($query_antena);
                                        if (!$stmt->execute()) {
                                            throw new Exception($conn->error);
                                        }

                                        $resultado = $stmt->get_result();
                                        if ($resultado->num_rows > 0) {
                                            echo '<select name="marca_antena" id="marca_antena" class="form-control" onchange="updateModeloAntena()">';
                                            echo '<option value="">Seleccione una Antena</option>';
                                            while ($row = $resultado->fetch_assoc()) {
                                                echo "<option value='{$row['id_modelo_antena']}' data-modelo='{$row['marca']}' data-marca='{$row['marca']}'>{$row['modelo']}</option>";
                                            }
                                            echo '</select>';
                                        } else {
                                            echo '<p class="text-danger">No hay registros disponibles.</p>';
                                        }
                                        $resultado->free(); // Libera el conjunto de resultados
                                        $stmt->close();     // Cierra el statement
                                    } catch (Exception $e) {
                                        echo '<p class="text-danger">Error al cargar antenas: ' . $e->getMessage() . '</p>';
                                    }
                                ?>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="modelo_antena">Modelo Antena</label>
                                <input type="text" class="form-control" id="modelo_antena" name="modelo_antena" readonly>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="serie_antena">No. Serie</label>
                                <input type="text" class="form-control" id="serie_antena" name="serie_antena" placeholder="Número Serie">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="marca_router">Marca Router</label>
                                <?php
                                try {
                                    // Llamada al procedimiento almacenado
                                    $query_router = "CALL mostrar_modelo_router";
                                    $stmt = $conn->prepare($query_router);
                                    if (!$stmt->execute()) {
                                        throw new Exception($conn->error);
                                    }

                                    $resultado = $stmt->get_result();
                                    if ($resultado->num_rows > 0) {
                                        echo '<select name="marca_router" id="marca_router" class="form-control" onchange="updateModeloRouter()">';
                                        echo '<option value="">Seleccione un Router</option>';
                                        while ($row = $resultado->fetch_assoc()) {
                                            // Ajustar para que el combobox no muestre el modelo directamente
                                            echo "<option value='{$row['id_modelo_router']}' data-modelo='{$row['marca']}' data-marca='{$row['marca']}'>{$row['modelo']}</option>";
                                        }
                                        echo '</select>';
                                    } else {
                                        echo '<p class="text-danger">No hay registros disponibles.</p>';
                                    }
                                    $resultado->free(); // Libera el conjunto de resultados
                                    $stmt->close();     // Cierra el statement
                                } catch (Exception $e) {
                                    echo '<p class="text-danger">Error al cargar routers: ' . $e->getMessage() . '</p>';
                                }
                            ?>

                            </div>
                            <div class="col-md-4 form-group">
                                <label for="modelo_router">Modelo Router</label>
                                <input type="text" name="modelo_router" class="form-control" id="modelo_router" readonly>
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="serie_router">No. Serie</label>
                                <input type="text" name="serie_router" class="form-control" id="serie_router" placeholder="Número Serie">
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="mac">IP</label>
                                <input type="text" name="ip" class="form-control" id="ip" placeholder="0.0.0.0">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="panelFrecuencia">MAC</label>
                                <input type="text" name="mac" class="form-control" id="mac" placeholder="00:00:00:00:00">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="enlace">Enlace</label>
                                <?php
                                    try {
                                        // Llamada al procedimiento almacenado
                                        $query_enlaces = "CALL mostrar_enlaces";
                                        $stmt = $conn->prepare($query_enlaces);
                                        if (!$stmt->execute()) {
                                            throw new Exception($conn->error);
                                        }
                                        $resultado = $stmt->get_result();

                                        if ($resultado->num_rows > 0) {
                                            echo '<select name="enlace" id="enlace" class="form-control">';
                                            echo '<option value="">Seleccione un Enlace</option>';
                                            while ($row = $resultado->fetch_assoc()) {
                                                echo "<option value='{$row['id_panel_enlace']}'>{$row['nombre']}</option>";
                                            }
                                            echo '</select>';
                                        } else {
                                            echo '<p class="text-danger">No hay registros disponibles.</p>';
                                        }
                                        $resultado->free(); // Libera el conjunto de resultados
                                        $stmt->close();     // Cierra el statement
                                    } catch (Exception $e) {
                                        echo '<p class="text-danger">Error al cargar enlaces: ' . $e->getMessage() . '</p>';
                                    }
                                ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 form-group">
                                <label for="direccion">Dirección</label>
                                <input type="text" name="direccion" class="form-control" id="direccion" placeholder="Dirección">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="coordenadas">Coordenadas</label>
                                <input type="text" name="coordenadas" class="form-control" id="coordenadas" placeholder="Coordenadas GPS">
                            </div>
                            <div class="col-md-4 form-group">
                                <label for="referencias">Referencias</label>
                                <textarea name="referencias" class="form-control" id="referencias" placeholder="Referencias adicionales"></textarea>
                            </div>
                        </div>

                        <div class="my-2"></div>
                        <button type="submit" class="btn btn-success btn-icon-split">
                            <span class="icon text-white-50">
                                <i class="fas fa-check"></i>
                            </span>
                            <span class="text">Registrar Cliente</span>
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<script>
    // Enviar el formulario por AJAX
    document.getElementById('formRegistrarCliente').addEventListener('submit', function(e) {
        e.preventDefault();  // Evitar la recarga de la página

        // Obtener el botón de registro y deshabilitarlo
        var btnRegistrar = document.querySelector('button[type="submit"]');
        btnRegistrar.disabled = true;  // Deshabilitar el botón

        // Crear FormData para enviar los datos del formulario
        var formData = new FormData(this);

        // Hacer la solicitud AJAX
        fetch('../controllers/registrar_cliente.php', {
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
                    window.location.href = '../views/clientes_registro.php';
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
        })
        .finally(() => {
            // Habilitar el botón nuevamente después de la respuesta
            btnRegistrar.disabled = false;  // Habilitar el botón
        });
    });
    
    // Función para actualizar el modelo de antena cuando se selecciona una opción
    function updateModeloAntena() {
        var select = document.getElementById('marca_antena');
        var modeloAntena = document.getElementById('modelo_antena');
        var selectedOption = select.options[select.selectedIndex];
        
        // Verificar si se ha seleccionado una opción
        if (selectedOption.value !== "") {
            // Obtener el modelo y marca del option seleccionado
            var modelo = selectedOption.getAttribute('data-modelo');
            var marca = selectedOption.getAttribute('data-marca');
            
            // Actualizar el valor del campo "Modelo Antena"
            modeloAntena.value = modelo;

            // Cambiar el placeholder del campo si se seleccionó un modelo
            if (modelo === "") {
                modeloAntena.placeholder = "Selecciona una antena";
                modeloAntena.value = '';
            } else {
                modeloAntena.placeholder = marca;
            }
        } else {
            // Si no se ha seleccionado ninguna antena, limpiar el campo modelo
            modeloAntena.value = '';
            modeloAntena.placeholder = "Selecciona una antena";
        }
    }

</script>

<?php
    require_once '../include/footer.php';
?>
