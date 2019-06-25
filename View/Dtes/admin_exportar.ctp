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

$campos = array( 'Pedido', 'Referencia', 'ID Transacción/es', 'Medio De Pago', 'Total Pagado', 'Total Envio', 'Folio DTE', 'Tipo De Documento DTE', 'Rut Del Receptor DTE', 'Estado DTE', 'Fecha Emisión DTE');

foreach ($campos as $campo) {
	array_push($cabeceras, array_merge(array('label' => Inflector::humanize($campo)), $opciones));
}

$this->PhpSpreadsheet->addTableHeader($cabeceras, array('bold' => true));

/**
 * Escribe los datos
 */
foreach ($datos as $dato) {

	$transacciones = '';

	if (!empty($dato['Venta']['VentaTransaccion'])) {

		foreach ($dato['Venta']['VentaTransaccion'] as $VentaTransaccion) {

			if ($transacciones != '') {
				$transacciones .= ', ';
			}

			$transacciones = $VentaTransaccion['nombre'];

		}

	}

	$TipoDocumento = $TiposDocs[$dato['Dte']['tipo_documento']];

	$FechaDte = date_format(date_create($dato['Dte']['fecha']), 'd/m/Y');

	$d = array(
		$dato['Venta']['id'],
		$dato['Venta']['referencia'],
		$transacciones,
		(isset($dato['Venta']['MedioPago']['nombre'])) ? $dato['Venta']['MedioPago']['nombre'] : 'No definido' ,
		$dato['Venta']['total'],
		$dato['Venta']['costo_envio'],
		$dato['Dte']['folio'],
		$TipoDocumento,
		$dato['Dte']['rut_receptor'],
		$dato['Dte']['estado'],
		$FechaDte
	);

	$this->PhpSpreadsheet->addTableRow($d);

}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpSpreadsheet->addTableFooter();
$this->PhpSpreadsheet->output(sprintf('listado-dts_%s.xlsx', date('Y-m-d_H-i-s')));
