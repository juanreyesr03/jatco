<?php
		include "../../conexion.php";
			$dni=$_GET['id'];
			$ide=$_GET['ide'];
			//--------------------------------------------------------------------------------------
			$datosempresa = mysqli_query($conexion, "SELECT * FROM configuracion");
			$query1 = mysqli_fetch_assoc($datosempresa);
			//--------------------------------------------------------------------------------------
			$cliente = mysqli_query($conexion, "SELECT * FROM cliente WHERE dni = '$dni'");
			$query2 = mysqli_fetch_assoc($cliente);
			//--------------------------------------------------------------------------------------
			$paquetesql=$query2['paquete'];
      		$paquetes = mysqli_query($conexion, "SELECT * FROM producto WHERE codproducto = $paquetesql");
			$query3 = mysqli_fetch_assoc($paquetes);
			//--------------------------------------------------------------------------------------
			$venta = mysqli_query($conexion, "SELECT * FROM lista WHERE id = '$ide'");
			$query4 = mysqli_fetch_assoc($venta);
			//--------------------------------------------------------------------------------------
			date_default_timezone_set('America/Mexico_City');
			$DateAndTime = date('d-m-Y H:i:s a', time());
			//--------------------------------------------------------------------------------------
			require_once 'fpdf.php';
			$pdf = new FPDF('P', 'mm', array(200, 320));
			$pdf->AddPage();
			$pdf->SetMargins(1, 0, 0);
			$pdf->SetTitle("Ticket");
			$pdf->SetFont('Arial', 'B', 50);
			$pdf->Cell(250, 20, utf8_decode($query1['nombre']), 0, 1, 'C');
			$pdf->Ln();
			$pdf->image("img/logo1.jpg", 20, 5, 40, 40, 'JPG');

			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(50, 8, "RFC: ", 0, 0, 'R');

			$pdf->SetFont('Arial', '', 10);
			$pdf->Cell(23, 8, utf8_decode($query1['razon_social']), 0, 1, 'L');

			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(50, 8, "Telefono: ", 0, 0, 'R');

			$pdf->SetFont('Arial', '', 10);
			$pdf->Cell(18, 8, utf8_decode($query1['telefono']), 0, 1, 'L');

			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(50, 8, "Direccion: ", 0, 0, 'R');

			$pdf->SetFont('Arial', '', 10);
			$pdf->Cell(20, 8, utf8_decode($query1['direccion']), 0, 1, 'R');

			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(50, 8, "Ticket: ", 0, 0, 'R');

			$pdf->SetFont('Arial','', 11);
			$pdf->Cell(23, 8,($query4['id']), 0, 0, 'L');

			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(70, -40, "Fecha de Cobro: ", 0, 0, 'R');

			$pdf->SetFont('Arial', '', 10);
			$pdf->Cell(40, -40, ($DateAndTime), 0, 1, 'R');

			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(73, 120, "Datos del cliente:", 0, 1, 'C');
			$pdf->Cell(60, -100, "Nombre:", 0, 0, 'C');
			$pdf->Cell(60, -100, utf8_decode('Teléfono:'), 0, 0, 'C');
			$pdf->SetFont('Arial', '', 10);
			$pdf->Cell(-160, -85, utf8_decode($query2['nombre']), 0, 0, 'C');
			$pdf->Cell(260, -85, utf8_decode($query2['telefono']), 0, 0, 'C');

			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(-360, -60, "Detalle de Productos:", 0, 1, 'C');
			$pdf->SetTextColor(0, 0, 0);
			$pdf->SetFont('Arial', 'B', 12);
			$pdf->Cell(60, 80, 'Nombre', 0, 0, 'C');
			$pdf->Cell(40, 80, 'SubTotal', 0, 0, 'C');
			$pdf->Cell(50, 80, 'Recargo', 0, 0, 'C');
			$pdf->Cell(40, 80, 'Total', 0, 1, 'C');
			$pdf->SetFont('Arial', '', 10);
			$pdf->Cell(70, -65, utf8_decode($query3['descripcion']), 0, 0, 'C');
			$pdf->Cell(20, -65, utf8_decode($query3['precio']), 0, 0, 'C');
			$pdf->Cell(70, -65, utf8_decode($query4['sub']), 0, 0, 'C');
			$pdf->Cell(20, -65, utf8_decode($query4['total']), 0, 0, 'C');

			$pdf->SetFont('Arial', '', 12);
			$pdf->Cell(-280, 10, utf8_decode("Gracias por su preferencia"), 0, 1, 'C');
			$pdf->Output("compra.pdf", "I");
?>
