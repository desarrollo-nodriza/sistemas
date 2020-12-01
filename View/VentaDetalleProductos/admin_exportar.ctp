<?php
/**
 * Crea un nuevo documento Excel
 */
$this->PhpSpreadsheet->createWorksheet('Listado');

/**
 * Escribe las cabeceras
 */
$cabeceras		= array();
$opciones		= array('width' => 'auto', 'filter' => true, 'wrap' => true);
foreach ( $campos as $campo )
{
	array_push($cabeceras, array_merge(array('label' => Inflector::humanize($campo)), $opciones));
}

foreach ($bodegas as $ib => $bodega) {
	array_push($cabeceras, array_merge(array('label' => 'Stock ' . $bodega), $opciones));
}

array_push($cabeceras, array_merge(array('label' => 'Stock Total'), $opciones));
array_push($cabeceras, array_merge(array('label' => 'Stock Reservado'), $opciones));
array_push($cabeceras, array_merge(array('label' => 'UPC'), $opciones));

foreach ($marketplaces as $im => $m) {

	if ($m['Marketplace']['marketplace_tipo_id'] != 2)
		continue;
	
	array_push($cabeceras, array_merge(array('label' => 'Precio ' . $m['Marketplace']['nombre']), $opciones));
	array_push($cabeceras, array_merge(array('label' => 'Costo transporte ' . $m['Marketplace']['nombre']), $opciones));
}

$this->PhpSpreadsheet->addTableHeader($cabeceras, array('bold' => true));

/**
 * Escribe los datos
 */
foreach ( $datos as $dato )
{
	$this->PhpSpreadsheet->addTableRow(current($dato));
}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpSpreadsheet->addTableFooter();
$this->PhpSpreadsheet->output(sprintf('Listado_%s_%s.csv', $modelo, date('Y_m_d-H_i_s')), 'Csv');