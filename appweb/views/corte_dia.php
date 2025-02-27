<?php
    require_once '../include/menu.php';
    include '../config/db_connection.php';
?>

<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Corte del Día</h1>
    </div>

    <div class="card shadow mb-4" id="formularioVenta">
        <!-- Collapsable Card Example -->
        <a href="#collapseCardExample" class="d-block card-header py-3" data-toggle="collapse"
            role="button" aria-expanded="true" aria-controls="collapseCardExample">
            <h6 class="m-0 font-weight-bold text-primary">Generar Corte del Día</h6>
        </a>

        <div class="collapse show" id="collapseCardExample">
            <div class="card-body">
                <form class="user">
                    <div class="d-flex flex-column">
                        <p>Usuario: <span class="m-0 font-weight-bold text-primary"><?php echo $_SESSION['nombre']; ?></span></p>
                        <p>Tipo Usuario: <span class="m-0 font-weight-bold text-primary"><?php echo $_SESSION['rol']; ?></span></p>
                        <p>Fecha: <span class="m-0 font-weight-bold text-primary"><?php echo date('d-m-Y'); ?></span></p>
                    </div>
                    <div class="row">
                        <div class="col-md-4 form-group">
                            <label for="id_user">Usuario</label>
                            <select name="id_user" id="id_user" class="form-control">
                                <option value="">Seleccione un Usuario</option>
                                <?php
                                    try {
                                        $query = "CALL mostrar_usuarios()";
                                        $result = $conn->query($query);
                                        while ($row = $result->fetch_assoc()) {
                                            echo "<option value='{$row['id_usuario']}'>{$row['nombre']}</option>";
                                        }
                                    } catch (Exception $e) {
                                        echo '<p class="text-danger">Error al cargar usuarios: ' . $e->getMessage() . '</p>';
                                    }
                                ?>
                            </select>
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="fecha">Fecha</label>
                            <input type="date" class="form-control" id="fecha" name="fecha">
                        </div>
                        <div class="col-md-4 form-group">
                            <label for="total">Total</label>
                            <input type="input" class="form-control" id="total" name="total" placeholder = $0.00 disabled>
                        </div>
                    </div>
                </form>
                <div class="col-md-0 form-group text-left">
                    <button class="btn btn-danger btn-icon-split" onclick="cancelarVenta()">
                        <span class="icon text-white-50"><i class="fas fa-times"></i></span>
                        <span class="text">Cancelar</span>
                    </button>
                    <button class="btn btn-info btn-icon-split" onclick="mostrarFormulario()">
                        <span class="icon text-white-50"><i class="fas fa-info-circle"></i></span>
                        <span class="text">Generar Corte Día</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <div class="card shadow mb-4" id="tablaVenta" style="display:none;">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">Ventas Registradas</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>Encargado</th>
                            <th>Cliente</th>
                            <th>Mensualidad</th>
                            <th>No. Meses</th>
                            <th>Recargo</th>
                            <th>Recargo Domicilio</th>
                            <th>Descuento</th>
                            <th>Subtotal</th>
                            <th>Total</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody id="tablaCorteVentas">
                        <tr><td colspan="8" style="text-align: center;">Seleccione usuario y fecha para ver ventas.</td></tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function mostrarFormulario() {
        var formulario = document.getElementById("tablaVenta");
        if (formulario.style.display === "none" || formulario.style.display === "") {
            formulario.style.display = "block";
            cargarVentas();
        } else {
            formulario.style.display = "none";
        }
    }

    function cargarVentas() {
        const idUser = document.getElementById("id_user").value;
        const fecha = document.getElementById("fecha").value;
        const totalInput = document.getElementById("total");

        if (!idUser || !fecha) {
            Swal.fire({ icon: 'warning', title: 'Atención', text: 'Seleccione un usuario y una fecha.' });
            return;
        }

        var formData = new FormData();
        formData.append('id_user', idUser);
        formData.append('fecha', fecha);

        fetch('../controllers/buscar_venta_corte.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            let tabla = document.getElementById("tablaCorteVentas");
            tabla.innerHTML = "";
            let sumaTotal = 0; // Variable para almacenar la suma total

            if (data.success) {
                data.ventas.forEach(venta => {
                    sumaTotal += parseFloat(venta.total); // Sumamos los totales de las ventas
                    let fila = `<tr>
                        <td>${venta.nombre_usuario}</td>
                        <td>${venta.nombre_cliente}</td>
                        <td>${venta.mensualidad}</td>
                        <td>${venta.mes_pago}</td>
                        <td>${venta.recargo}</td>
                        <td>${venta.recargo_domicilio}</td>
                        <td>${venta.descuento}</td>
                        <td>${venta.subtotal}</td>
                        <td>${venta.total}</td>
                        <td>${venta.fecha}</td>
                    </tr>`;
                    tabla.innerHTML += fila;
                });

                // Mostrar el total en el input y aplicar estilo rojo
                totalInput.value = `$${sumaTotal.toFixed(2)}`;
                totalInput.style.color = "red";
            } else {
                tabla.innerHTML = `<tr><td colspan="8" style="text-align: center;">No hay registros.</td></tr>`;
                totalInput.value = "$0.00"; // Reiniciamos el total si no hay datos
                totalInput.style.color = "black"; // Restauramos el color
            }
        })
        .catch(error => {
            Swal.fire({ icon: 'error', title: 'Error', text: 'Hubo un problema al cargar las ventas.' });
        });
    }

    function cancelarVenta() {
        document.getElementById("tablaVenta").style.display = "none";
    }
</script>

<?php
    require_once '../include/footer.php';
?>
