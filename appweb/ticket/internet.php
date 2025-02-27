<?php
include '../config/db_connection.php';

// Venta
$id_cliente_venta = $_GET['id'];

// CONFIGURACIÓN PREVIA
require('fpdf/fpdf.php');
$pdf = new FPDF('P', 'mm', array(45, 200)); // Tamaño ticket 80mm x 150mm (largo aprox)
$pdf->AddPage();


// CABECERA
// Configuración de la empresa
$buscar_empresa = mysqli_query($conn, "SELECT * FROM configuracion_empresa");
$result_empresa = mysqli_num_rows($buscar_empresa);
while ($empresa = mysqli_fetch_array($buscar_empresa)) {
    $nombre_empresa = $empresa['nombre'];
    $rfc = $empresa['rfc'];
    $dire = $empresa['direccion'];
}

$telefonos_array = [];

$buscar_telefonos = mysqli_query($conn, "SELECT * FROM configuracion_empresa_telefono");
$result_telefonos = mysqli_num_rows($buscar_telefonos);
while ($telefonos = mysqli_fetch_array($buscar_telefonos)) {
    $telefonos_array[] = $telefonos['numero']; // Agregar cada teléfono al array
}

// Llamar al procedimiento almacenado con el ID del cliente
$buscar_venta = $conn->prepare("CALL buscar_venta_ticket_internet(?)");
$buscar_venta->bind_param("i", $id_cliente_venta);
$buscar_venta->execute();
$result = $buscar_venta->get_result();

$venta = $result->fetch_assoc();
if ($venta) {
    $id_venta = $venta['id_venta_internet'];
    $encargado = $venta['nombre_usuario'];
    $mensualidad = $venta['mensualidad'];
    $no_mes = $venta['mes_pago'];
    $nombre_cliente = $venta['nombre_cliente'];
    $recargo = $venta['recargo'];
    $cobro_domicilio = $venta['recargo_domicilio'];
    $descuento = $venta['descuento'];
    $subtotal = $venta['subtotal'];
    $total = $venta['total'];
    $estado = $venta['id_estado'];
    $fecha = $venta['fecha'];
    $fecha_vencimiento = $venta['fecha_vencimiento'];
    $folio = $venta['folio'];

    // Ensure other necessary fields are set (example placeholder values)
    $descripcion = isset($venta['descripcion']) ? $venta['descripcion'] : "Paquete de internet no especificado";
} else {
    // Handle case where no data is returned from the query
    echo "No se encontró la venta para este cliente.";
    exit;
}

// Cerrar la consulta para evitar errores en conexiones múltiples
$buscar_venta->close();
$conn->close();

// Encabezado
$pdf->image("../img/logo3.jpg", 8, 2, 30, 30, 'JPG');
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(25, 50, $nombre_empresa, 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 12);
$pdf->Cell(60, -20, " ", 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(25, 4, "Calle Morelos 63", 0, 1, 'C');
$pdf->Cell(25, 4, "Av. Reforma 34", 0, 1, 'C');
$pdf->Cell(22, 4, "________________________________________", 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(0, 4, "Datos de Soporte y atencion", 0, 1, 'C');
$pdf->Ln(3);

// Datos Empresa
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(25, 4, 'Para duda o soporte, marque', 0, 1, 'C');
$pdf->Cell(25, 4, 'a los siguientes numeros:', 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(25, 4, $telefonos_array[0], 0, 1, 'C');
$pdf->Cell(25, 4, $telefonos_array[1], 0, 1, 'C');
$pdf->Cell(25, 4, $telefonos_array[2], 0, 1, 'C');
$pdf->Cell(25, 4, 'Horarios de:', 0, 1, 'C');
$pdf->Cell(25, 4, '10:00 a.m a 10:00 p.m', 0, 1, 'C');
$pdf->Cell(22, 4, "________________________________________", 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(0, 4, "Datos de Ticket", 0, 1, 'C');
$pdf->Ln(3);

// Datos Factura
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(25, 4, 'Vendedor: ' . $encargado, 0, 1, 'C');
$pdf->Cell(25, 4, 'ID Ticket: ', 0, 1, 'C');
$pdf->Cell(25, 4, $folio, 0, 1, 'C');


// Fecha
date_default_timezone_set('America/Mexico_City');
$pdf->Cell(25, 4, 'Fecha: ' . $fecha, 0, 1, 'C');
$pdf->Cell(25, 4, 'Fecha Vencimiento: ' . $fecha_vencimiento, 0, 1, 'C');
$pdf->Cell(25, 4, 'Metodo de pago: Efectivo', 0, 1, 'C');
$pdf->Cell(22, 4, "________________________________________", 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(0, 4, "Datos de cliente", 0, 1, 'C');
$pdf->Ln(3);

// Columna Clientes
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(-8, 5, 'Nombre', 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->MultiCell(25, 4, $nombre_cliente, 0, 'C');
$pdf->Ln(1);
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(3, 5, 'Paquete de internet', 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 5);
$pdf->MultiCell(25, 4, $descripcion, 0, 'C');
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(7, 5, 'Descripcion de cobros', 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->Cell(15, 4, 'Subtotal:', 0, 1, 'C');
$pdf->Cell(50, -4, '$' . $subtotal, 0, 1, 'C');
$pdf->Ln(4);
$pdf->Cell(15, 4, 'Recargo:', 0, 1, 'C');
$pdf->Cell(50, -4, '$' . $recargo, 0, 1, 'C');
$pdf->Ln(4);
$pdf->Cell(15, 4, 'Cobro a Domicilio:', 0, 1, 'C');
$pdf->Cell(50, -4, '$' . $cobro_domicilio, 0, 1, 'C');
$pdf->Ln(4);
$pdf->Cell(15, 4, 'Descuento:', 0, 1, 'C');
$pdf->Cell(50, -4, '$' . $descuento, 0, 1, 'C');
$pdf->Ln(4);
$pdf->Cell(15, 4, 'Total:', 0, 1, 'C');
$pdf->Cell(50, -4, '$' . $total, 0, 1, 'C');
$pdf->Ln(4);

$pdf->Output('ticket.pdf', 'i');
?>
