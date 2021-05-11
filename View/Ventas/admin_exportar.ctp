<?php
/**
 * Crea un nuevo documento Excel
 */
$this->PhpSpreadsheet->createWorksheet();

/**
 * Escribe las cabeceras
 */
$cabeceras		= array();
$opciones		= array('width' => 'auto', 'filter' => true, 'wrap' => true);

$campos = array("ID", "ID Externo", "Referencia", "Fecha", "Total", "Medio de Pago", "Estado", "Tienda", "Marketplace", "Cliente", "Dte","Estado picking");

foreach ($campos as $campo) {
	array_push($cabeceras, array_merge(array('label' => Inflector::humanize($campo)), $opciones));
}

$this->PhpSpreadsheet->addTableHeader($cabeceras, array('bold' => true));
$picking = ClassRegistry::init('Venta')->picking_estados_lista;
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
	if (isset($dato['Dte'])) {
		foreach ($dato['Dte'] as $dte) {
			if (!empty($dte['folio'])) {
				$dtes .= "Folio: " . $dte['folio'];
			}
			if (!empty($dte['tipo_documento'])) {
				$dtes .= "\n";
				$dtes .= "Tipo Dte: " . $this->Html->tipoDocumento[$dte['tipo_documento']];
			}
			if (!empty($dte['estado'])) {
				$dtes .= "\n";
				$dtes .= "Estado: " . $dte['estado'];
			}	
		}
	}



	$this->PhpSpreadsheet->addTableRow(
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
			$dtes,
			$picking[$dato['Venta']['picking_estado']]
		)
	);

}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpSpreadsheet->addTableFooter();
$this->PhpSpreadsheet->output(sprintf('listado-de-ventas%s.xlsx', date('Y-m-d_H-i-s')));
