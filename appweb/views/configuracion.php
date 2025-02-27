<?php
    require_once '../include/menu.php';
?>

<!-- Begin Page Content -->
<div class="container-fluid">

    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">Configuración Generales</h1>
        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
            <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
        </a>
    </div>

    <div class="row">
        <!-- Columna para las tarjetas de descuentos y multas -->
        <div class="col-lg-4">
            <!-- Tarjeta de Descuentos -->
            <div class="card shadow mb-4">
                <a href="#collapseCardDescuentos" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardDescuentos">
                    <h6 class="m-0 font-weight-bold text-primary">Descuentos</h6>
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

            <!-- Tarjeta de Multas -->
            <div class="card shadow mb-4">
                <a href="#collapseCardMultas" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardMultas">
                    <h6 class="m-0 font-weight-bold text-primary">Multas</h6>
                </a>
                <div class="collapse show" id="collapseCardMultas">
                    <div class="card-body">
                        <input type="text" id="inputTextMultas" class="form-control form-control-user" disabled>
                        <div class="my-2"></div>
                        <ul class="list-group" id="registroListMultas">
                            <?php
                                $registros = ["Registro 1", "Registro 2", "Registro 3"];
                                for ($i = 0; $i < count($registros); $i++) {
                                    echo '<li class="list-group-item d-flex justify-content-between">' . $registros[$i] . '
                                        <a href="#" class="btn btn-danger btn-circle btn-sm delete-btn" data-index="' . $i . '" data-list="Multas"><i class="fas fa-trash"></i></a>
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

            <!-- Tarjeta de Directorio Telefónico -->
            <div class="card shadow mb-4">
                <a href="#collapseCardDirectorio" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardDirectorio">
                    <h6 class="m-0 font-weight-bold text-primary">Directorio Telefónico</h6>
                </a>
                <div class="collapse show" id="collapseCardDirectorio">
                    <div class="card-body">
                        <!-- Input para seleccionar un teléfono -->
                        <input type="text" id="inputTextDirectorio" class="form-control form-control-user" disabled placeholder="Selecciona un número">
                        <div class="my-2"></div>

                        <!-- Input para el área -->
                        <input type="text" id="inputAreaDirectorio" class="form-control form-control-user" disabled placeholder="Área">
                        <div class="my-2"></div>

                        <!-- Lista generada con PHP -->
                        <ul class="list-group" id="registroListDirectorio">
                            <?php
                                $telefonos = [
                                    "555-1234" => "Área 1",
                                    "555-5678" => "Área 2",
                                    "555-8765" => "Área 3"
                                ];

                                foreach ($telefonos as $telefono => $area) {
                                    echo '<li class="list-group-item d-flex justify-content-between">
                                            ' . $telefono . '
                                            <a href="#" class="btn btn-danger btn-circle btn-sm delete-btn" data-telefono="' . $telefono . '" data-area="' . $area . '">
                                                <i class="fas fa-trash"></i>
                                            </a>
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
                <a href="#collapseCardEmpresa" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardEmpresa">
                    <h6 class="m-0 font-weight-bold text-primary">Datos de la Empresa</h6>
                </a>
                <div class="collapse show" id="collapseCardEmpresa">
                    <div class="card-body">
                        <form>
                            <div class="form-group">
                                <label for="empresaNombre">Nombre de la Empresa</label>
                                <input type="text" class="form-control" id="empresaNombre" placeholder="Nombre de la Empresa">
                            </div>
                            <div class="form-group">
                                <label for="empresaRFC">RFC</label>
                                <input type="text" class="form-control" id="empresaRFC" placeholder="RFC de la Empresa">
                            </div>
                            <div class="form-group">
                                <label for="empresaDireccion">Dirección</label>
                                <input type="text" class="form-control" id="empresaDireccion" placeholder="Dirección de la Empresa">
                            </div>
                            <div class="my-2"></div>
                            <a href="#" class="btn btn-success btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="text">Split Button Success</span>
                            </a>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Tarjeta de Datos de la Empresa -->
            <div class="card shadow mb-4">
                <a href="#collapseCardStatus" class="d-block card-header py-3" data-toggle="collapse" role="button" aria-expanded="true" aria-controls="collapseCardStatus">
                    <h6 class="m-0 font-weight-bold text-primary">Status Por Departamento</h6>
                </a>
                <div class="collapse show" id="collapseCardStatus">
                    <div class="card-body">
                        <form>
                            <div class="form-group">
                                <label for="empresaNombre">Nombre de la Empresa</label>
                                <input type="text" class="form-control" id="empresaNombre" placeholder="Nombre de la Empresa">
                            </div>
                            <div class="form-group">
                                <label for="empresaRFC">RFC</label>
                                <input type="text" class="form-control" id="empresaRFC" placeholder="RFC de la Empresa">
                            </div>
                            <div class="form-group">
                                <label for="empresaDireccion">Dirección</label>
                                <input type="text" class="form-control" id="empresaDireccion" placeholder="Dirección de la Empresa">
                            </div>
                            <div class="my-2"></div>
                            <a href="#" class="btn btn-success btn-icon-split">
                                <span class="icon text-white-50">
                                    <i class="fas fa-check"></i>
                                </span>
                                <span class="text">Split Button Success</span>
                            </a>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

</div>
<!-- /.container-fluid -->

<?php
    require_once '../include/footer.php';
?>

<!-- Script para manejar el bloqueo del input, eliminar ítems y seleccionar ítems -->
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Función para manejar los botones de eliminación de los registros de descuentos y multas
        const deleteButtons = document.querySelectorAll(".delete-btn");

        deleteButtons.forEach(button => {
            button.addEventListener("click", function (e) {
                e.preventDefault();

                const list = button.getAttribute("data-list");
                const inputText = document.getElementById(`inputText${list}`);
                
                // Bloquear el input
                inputText.disabled = true;

                // Eliminar el registro correspondiente
                const listItem = button.closest("li");
                listItem.remove();
            });
        });

        // Función para seleccionar un ítem (descuento o multa) y colocar el valor en el input correspondiente
        document.querySelectorAll(".list-group-item").forEach(item => {
            item.addEventListener("click", function () {
                const listId = item.closest(".collapse").id;
                const list = listId.replace("collapseCard", "");
                const inputText = document.getElementById(`inputText${list}`);

                inputText.value = item.firstChild.textContent.trim();
                inputText.disabled = false;
            });
        });

        // Directorio Telefónico: Selección de teléfono y área
        const telefonoInput = document.getElementById("inputTextDirectorio");
        const areaInput = document.getElementById("inputAreaDirectorio");

        const telefonoListItems = document.querySelectorAll("#registroListDirectorio .list-group-item");
        telefonoListItems.forEach(item => {
            item.addEventListener("click", function () {
                const telefono = item.firstChild.textContent.trim();
                const area = item.querySelector("a").getAttribute("data-area");

                telefonoInput.value = telefono;
                areaInput.value = area;

                telefonoInput.disabled = false;
                areaInput.disabled = false;
            });
        });
    });
</script>
