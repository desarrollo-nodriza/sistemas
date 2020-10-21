<?php
/**
 * Crea un nuevo documento Excel
 */
$this->PhpSpreadsheet->createWorksheet();

$headers  = array();
$opciones = array('width' => 'auto', 'filter' => true, 'wrap' => true);

foreach ($cabeceras as $campo) {
	array_push($headers, array_merge(array('label' => Inflector::humanize($campo)), $opciones));
}
$this->PhpSpreadsheet->addTableHeader($headers, array('bold' => true));

/**
 * Escribe los datos
 */
foreach ( $datos as $dato )
{
	$this->PhpSpreadsheet->addTableRow(array(
		$dato['Cotizacion']['id'],
		$dato['Cotizacion']['vendedor'],
		$dato['Cotizacion']['email_cliente'],
		$dato['Cotizacion']['nombre_cliente'],
		CakeNumber::currency(h($dato['Cotizacion']['total_neto']), 'CLP'),
		CakeNumber::currency(h($dato['Cotizacion']['iva']), 'CLP'),
		CakeNumber::currency(h($dato['Cotizacion']['descuento']), 'CLP'),
		CakeNumber::currency(h($dato['Cotizacion']['total_bruto']), 'CLP'),
		$dato['Cotizacion']['created']
	));
}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpSpreadsheet->addTableFooter();
$this->PhpSpreadsheet->output(sprintf('listado-cotizaciones-%s.xlsx', date('Y-m-d_H-i-s')));
