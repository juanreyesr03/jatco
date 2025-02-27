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
$buscar_venta = $conn->prepare("CALL buscar_venta_ticket_plataforma(?)");
$buscar_venta->bind_param("i", $id_cliente_venta); // Suponiendo que $id_cliente_venta es el valor que pasas
$buscar_venta->execute();
$result = $buscar_venta->get_result();

// Array para almacenar las ventas
$ventas = [];

// Procesar la venta
while ($venta = $result->fetch_assoc()) {
    // Por cada venta, agregamos la información en un array
    $ventas[] = [
        'id_venta' => $venta['id_venta_plataforma'],
        'encargado' => $venta['nombre_perfil'],
        'nombre_cliente' => $venta['nombre_cliente'],
        'recargo' => 0,  // Puedes ajustar si es necesario
        'descuento' => 0,  // Puedes ajustar si es necesario
        'subtotal' => $venta['precio'],
        'total' => $venta['precio'],
        'estado' => $venta['id_estado_venta_plataforma'],
        'fecha' => $venta['fecha_asignacion'],
        'folio' => $venta['folio'],
        'descripcion' => isset($venta['nombre_paquete']) ? $venta['nombre_paquete'] : "Paquete de internet no especificado",
        'precio' => $venta['precio'],
        'valor' => 0,  // Puedes ajustar si es necesario
        'cobro_domicilio' => 0,  // Puedes ajustar si es necesario
        'correo' => $venta['correo'],
        'pin_correo' => $venta['pin_correo'],
        'perfil' => $venta['nombre_perfil'],
        'pin_perfil' => $venta['pin_perfil'],
        'fecha_contratacion' => $venta['fecha_asignacion'], // Fecha de contratación
        'fecha_vencimiento' => $venta['fecha_vencimiento'] // Fecha de vencimiento
    ];
}

// Si no se encuentran ventas, mostramos un mensaje
if (empty($ventas)) {
    echo "No se encontró ninguna venta para este cliente.";
    exit;
}

// Cerrar la consulta para evitar errores en conexiones múltiples
$buscar_venta->close();
$conn->close();

// Para el encabezado, que es común para todas las ventas
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

// Mostrar solo una vez los datos generales (Vendedor, ID Ticket, Fecha, Método de pago)
$venta = $ventas[0]; // Tomamos la primera venta para mostrar los datos generales

$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(25, 4, 'Vendedor: ' . $venta['encargado'], 0, 1, 'C');
$pdf->Cell(25, 4, 'ID Ticket: ', 0, 1, 'C');
$pdf->Cell(25, 4, $venta['folio'], 0, 1, 'C');
$pdf->Cell(25, 4, 'Fecha: ' . $venta['fecha'], 0, 1, 'C');
$pdf->Cell(25, 4, 'Metodo de pago: Efectivo', 0, 1, 'C');
$pdf->Cell(22, 4, "________________________________________", 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 8);
$pdf->Cell(0, 4, "Datos de cliente", 0, 1, 'C');
$pdf->Ln(3);

// Columna Clientes
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(-8, 5, 'Nombre', 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->MultiCell(25, 4, $venta['nombre_cliente'], 0, 'C');
$pdf->Ln(1);

// Mostrar fecha de contratación y vencimiento
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(3, 5, 'Fecha Contratacion', 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 5);
$pdf->MultiCell(25, 4, $venta['fecha_contratacion'], 0, 'C');

$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(3, 5, 'Fecha Vencimiento', 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 5);
$pdf->MultiCell(25, 4, $venta['fecha_vencimiento'], 0, 'C');

// Iterar sobre cada venta y generar un ticket por venta
foreach ($ventas as $venta) {

    
    // Descripción de Paquete de Internet
    $pdf->SetFont('Helvetica', 'B', 6);
    $pdf->Cell(3, 5, 'Paquete de internet', 0, 1, 'C');
    $pdf->SetFont('Helvetica', 'B', 5);
    $pdf->MultiCell(25, 4, $venta['descripcion'], 0, 'C');
    
    // Mostrar correo y pin_correo
    $pdf->SetFont('Helvetica', 'B', 6);
    $pdf->Cell(3, 5, 'Correo', 0, 1, 'C');
    $pdf->SetFont('Helvetica', 'B', 5);
    $pdf->MultiCell(25, 4, $venta['correo'], 0, 'C');
    
    $pdf->SetFont('Helvetica', 'B', 6);
    $pdf->Cell(3, 5, 'Pin Correo', 0, 1, 'C');
    $pdf->SetFont('Helvetica', 'B', 5);
    $pdf->MultiCell(25, 4, $venta['pin_correo'], 0, 'C');
    
    // Mostrar perfil y pin_perfil
    $pdf->SetFont('Helvetica', 'B', 6);
    $pdf->Cell(3, 5, 'Perfil', 0, 1, 'C');
    $pdf->SetFont('Helvetica', 'B', 5);
    $pdf->MultiCell(25, 4, $venta['perfil'], 0, 'C');
    
    $pdf->SetFont('Helvetica', 'B', 6);
    $pdf->Cell(3, 5, 'Pin Perfil', 0, 1, 'C');
    $pdf->SetFont('Helvetica', 'B', 5);
    $pdf->MultiCell(25, 4, $venta['pin_perfil'], 0, 'C');
}

// Datos de cobros
$pdf->SetFont('Helvetica', 'B', 6);
$pdf->Cell(7, 5, 'Descripcion de cobros', 0, 1, 'C');
$pdf->SetFont('Helvetica', 'B', 7);
$pdf->Cell(15, 4, 'Subtotal:', 0, 1, 'C');
$pdf->Cell(50, -4, '$' . $venta['precio'], 0, 1, 'C');
$pdf->Ln(4);
$pdf->Cell(15, 4, 'Recargo:', 0, 1, 'C');
$pdf->Cell(50, -4, '$' . $venta['valor'], 0, 1, 'C');
$pdf->Ln(4);
$pdf->Cell(15, 4, 'Cobro a Domicilio:', 0, 1, 'C');
$pdf->Cell(50, -4, '$' . $venta['cobro_domicilio'], 0, 1, 'C');
$pdf->Ln(4);
$pdf->Cell(15, 4, 'Descuento:', 0, 1, 'C');
$pdf->Cell(50, -4, '$' . $venta['descuento'], 0, 1, 'C');
$pdf->Ln(4);

// Total
$pdf->Cell(15, 4, 'Total:', 0, 1, 'C');
$pdf->Cell(50, -4, '$' . $venta['total'], 0, 1, 'C');
$pdf->Ln(4);

$pdf->output();
?>
