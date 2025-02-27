<?php
include '../config/db_connection.php';
require __DIR__ . '/vendor/autoload.php';

use Mike42\Escpos\Printer;
use Mike42\Escpos\PrintConnectors\WindowsPrintConnector;
use Mike42\Escpos\EscposImage;

function obtenerNombreDelMes($mes) {
    $meses = [
        1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
        7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
    ];
    return ($mes >= 1 && $mes <= 12) ? $meses[$mes] : "Mes fuera de rango";
}

function obtenerDatosDeEmpresa($conn) {
    $sql = "SELECT `nombre`, `rfc`, `direccion` FROM `configuracion_empresa` LIMIT 1";
    $result = $conn->query($sql);
    if ($result && $row = $result->fetch_assoc()) {
        return $row;
    }
    throw new Exception("No se encontraron resultados para la empresa.");
}

function obtenerTelefonosDeEmpresa($conn) {
    $sql = "SELECT `numero` FROM `configuracion_empresa_telefono`";
    $result = $conn->query($sql);
    $telefonos = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            $telefonos[] = $row["numero"];
        }
    }
    return $telefonos;
}

function obtenerVentaCliente($conn, $id_cliente) {
    $sql = "CALL obtener_ventas_por_cliente(?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_cliente);
    $stmt->execute();
    return $stmt->get_result();
}

try {
    // Verificar si el parámetro 'id' está presente en la URL
    $id_cliente = isset($_GET['id']) && !empty($_GET['id']) ? $_GET['id'] : null; // Valor predeterminado: 357

    $printerName = "POS-58";
    $connector = new WindowsPrintConnector($printerName);
    $printer = new Printer($connector);

    // Cargar la imagen del logo
    $logo = EscposImage::load("logo2.png", false);
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->bitImage($logo);
    $printer->feed();

    // Obtener datos de la empresa
    $empresa = obtenerDatosDeEmpresa($conn);
    $telefonos = obtenerTelefonosDeEmpresa($conn);

    // Obtener datos de la venta
    $venta_result = obtenerVentaCliente($conn, $id_cliente);
    $venta = $venta_result->fetch_assoc();

    $nombre_mes = obtenerNombreDelMes($venta['mes_pago']);
    
    // Encabezado del ticket
    $printer->setTextSize(2, 2);
    $printer->text($empresa['nombre'] . "\n");
    $printer->text("\n");
    $printer->setTextSize(1, 2);
    $printer->text("Soporte y Atencion\n");
    $printer->setTextSize(1, 1);
    $printer->text($empresa['direccion'] . "\n");
    foreach ($telefonos as $telefono) {
        $printer->text("Telefono: " . $telefono . "\n");
    }
    $printer->text("\n");
    $printer->text("Vendedor: " . $venta['vendedor'] . "\n");
    $printer->text("Ticket #: " . str_pad(rand(1000, 9999), 4, "0", STR_PAD_LEFT) . "\n");
    $printer->text("Fecha: " . date('Y-m-d H:i:s') . "\n");
    $printer->text("------------------------------\n");
    $cliente = $venta['cliente'];
    if (strlen($cliente) > 22) {
        $cliente = substr($cliente, 0, 22);
    }
    $printer->text("Cliente: " . $cliente . "\n");
    $printer->text("Paquete: " . $venta['descripcion'] . "\n");
    $printer->text("No. Meses: " . $venta['mes_pago'] . "\n");
    $printer->text("Mensualidad: " . $nombre_mes . "\n");
    $printer->text("------------------------------\n");

    // Totales
    $subtotal = "$" . number_format($venta['subtotal'], 2, '.', '');
    $recargos = "$" . number_format($venta['recargos'], 2, '.', '');
    $total = "$" . number_format($venta['total'], 2, '.', '');
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("Subtotal: " . str_pad($subtotal, 20, " ", STR_PAD_LEFT) . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("Recargo:  " . str_pad($recargos, 20, " ", STR_PAD_LEFT) . "\n");
    $printer->setJustification(Printer::JUSTIFY_LEFT);
    $printer->text("Total:    " . str_pad($total, 20, " ", STR_PAD_LEFT) . "\n");
    $printer->text("----------------------------\n");

    // Código de barras y QR
    $printer->setJustification(Printer::JUSTIFY_CENTER);
    $printer->barcode("ABC 012", Printer::BARCODE_CODE39);
    $printer->feed();
    $printer->text("No. de Ticket: " . str_pad(rand(1000, 9999), 4, "0", STR_PAD_LEFT) . "\n");
    $printer->qrCode("https://www.tiendaejemplo.com", Printer::QR_ECLEVEL_L, 6);
    $printer->feed();

    // Información adicional

    // Tipo de pago y pie de página
    $printer->text("Pago realizado: Efectivo\n");
    $printer->text("Gracias por su compra\n");
    $printer->text("Vuelva pronto!\n");

    // Espacio antes del corte
    $printer->feed(4);

    // Cortar papel
    $printer->cut();

    echo "Ticket impreso con éxito.";
} catch (Exception $e) {
    echo "Se produjo un error: " . $e->getMessage();
} finally {
    if (isset($printer)) {
        $printer->close();
    }
    if (isset($conn)) {
        $conn->close(); // Asegurar cierre de la conexión
    }

    // Al final de tu script, después de que se haya impreso el ticket:
    header('Location: ../views/internet_venta.php');
    exit;
}
