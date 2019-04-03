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

$campos = array("ID", "ID Externo", "Referencia", "Fecha", "Total", "Medio de Pago", "Estado", "Tienda", "Marketplace", "Cliente", "Dte");

foreach ($campos as $campo) {
	array_push($cabeceras, array_merge(array('label' => Inflector::humanize($campo)), $opciones));
}

$this->PhpExcel->addTableHeader($cabeceras, array('bold' => true));

/**
 * Escribe los datos
 */
foreach ($datos as $dato) {

	$fecha = date_format(date_create($dato['Venta']['fecha_venta']), 'd/m/Y H:i:s');

	$total = CakeNumber::currency($dato['Venta']['total'], 'CLP');

	$marketplace = "";
	if (!empty($dato['Venta']['marketplace_id'])) {
		$marketplace = $dato['Marketplace']['nombre'];
	}

	$cliente = $dato['VentaCliente']['nombre'];

	if (!empty($dato['VentaCliente']['apellido'])) {
		$cliente.= " " .$dato['VentaCliente']['apellido'];
	}
	if (!empty($dato['VentaCliente']['rut'])) {
		$cliente.= "\n";
		$cliente.= $dato['VentaCliente']['rut'];
	}
	if (!empty($dato['VentaCliente']['email'])) {
		$cliente.= "\n";
		$cliente.= $dato['VentaCliente']['email'];
	}
	if (!empty($dato['VentaCliente']['telefono'])) {
		$cliente.= "\n";
		$cliente.= $dato['VentaCliente']['telefono'];
	}

	$dtes = '';
	if (!empty($dato['Dte']['folio'])) {
		$dtes .= "Folio: " . $dato['Dte']['folio'];
	}
	if (!empty($dato['Dte']['tipo_documento'])) {
		$dtes .= "\n";
		$dtes .= "Tipo Dte: " . $this->tipoDocumento[$dato['Dte']['tipo_documento']];
	}
	if (!empty($dato['Dte']['estado'])) {
		$dtes .= "\n";
		$dtes .= "Estado: " . $this->tipoDocumento[$dato['Dte']['estado']];
	}

	$this->PhpExcel->addTableRow(
		array(
			$dato['Venta']['id'],
			$dato['Venta']['id_externo'],
			$dato['Venta']['referencia'],
			$fecha,
			$total,
			$dato['MedioPago']['nombre'],
			$dato['VentaEstado']['VentaEstadoCategoria']['nombre'],
			$dato['Tienda']['nombre'],
			$marketplace,
			$cliente,
			$dtes
		)
	);

}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(sprintf('listado-de-ventas%s.xls', date('Y-m-d_H-i-s')));
