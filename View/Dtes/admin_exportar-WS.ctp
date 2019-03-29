<?php
/**
 * Crea un nuevo documento Excel
 */
$this->PhpExcel->createWorksheet();

/**
 * Escribe las cabeceras
 */
$cabeceras		= array();
$opciones		= array('width' => 'auto', 'filter' => true, 'wrap' => true);

$campos = array(
	"Pedido", "Referencia", "ID Transacción/es", "Autorización Webpay", "Medio De Pago", "Total Pagado", "Total Envio", "Folio DTE", "Tipo De Documento DTE",
	"Rut Del Receptor DTE", "Estado DTE", "Fecha Emisión DTE", "Monto Pagado Webpay", "Tipo De Pago Webpay", "Fecha De Pago Webpay", "Código De Respuesta Webpay"
);

foreach ($campos as $campo) {
	array_push($cabeceras, array_merge(array('label' => Inflector::humanize($campo)), $opciones));
}

$this->PhpExcel->addTableHeader($cabeceras, array('bold' => true));

/**
 * Escribe los datos
 */
foreach ($datos as $dato) {

	$transacciones = "";

	if (!empty($dato['Venta']['VentaTransaccion'])) {

		foreach ($$dato['Venta']['VentaTransaccion'] as $VentaTransaccion) {

			if ($transacciones != "") {
				$transacciones .= ", ";
			}

			$transacciones = $VentaTransaccion['nombre'];

		}

	}

	$TipoDocumento = $TiposDocs[$dato['Dte']['tipo_documento']];

	$FechaDte = date_format(date_create($dato['Dte']['fecha']), 'd/m/Y');

	if (!empty($dato['Venta']['WebpayStore'])) {
		$WebpayAutorizacion = $dato['Venta']['WebpayStore']['authorization_code'];
		$WebpayMonto = $dato['Venta']['WebpayStore']['amount'];
		$WebpayTipoPago = $dato['Venta']['WebpayStore']['payment_type'];
		$WebpayFechaPago = $dato['Venta']['WebpayStore']['create'];
		$WebpayCodigoRespuesta = $dato['Venta']['WebpayStore']['reponse_code'];
	}

	else {
		$WebpayAutorizacion = "";
		$WebpayMonto = "";
		$WebpayTipoPago = "";
		$WebpayFechaPago = "";
		$WebpayCodigoRespuesta = "";
	}

	$this->PhpExcel->addTableRow(
		array(
			$dato['Venta']['id'],
			$dato['Venta']['referencia'],
			$transacciones,
			$WebpayAutorizacion,
			$dato['Venta']['MedioPago']['nombre'],
			$dato['Venta']['total'],
			$dato['Venta']['costo_envio'],
			$dato['Dte']['folio'],
			$TipoDocumento,
			$dato['Dte']['rut_receptor'],
			$dato['Dte']['estado'],
			$FechaDte,
			$WebpayMonto,
			$WebpayTipoPago,
			$WebpayFechaPago,
			$WebpayCodigoRespuesta
		)
	);

}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(sprintf('listado-dts_%s.xls', date('Y-m-d_H-i-s')));
