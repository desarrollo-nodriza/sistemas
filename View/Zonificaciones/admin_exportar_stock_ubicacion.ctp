<?php
/**
 * Crea un nuevo documento Excel
 */
$this->PhpSpreadsheet->createWorksheet('Stock_Ubicación');

/**
 * Escribe las cabeceras
 */
$cabeceras		= array();
$opciones		= array('width' => 'auto', 'filter' => true, 'wrap' => true);
foreach ( $campos as $campo )
{
	array_push($cabeceras, array_merge(array('label' => Inflector::humanize($campo)), $opciones));
}

$this->PhpSpreadsheet->addTableHeader($cabeceras, array('bold' => true));

/**
 * Escribe los datos
 */
foreach ( $datos as $dato )
{   
	$this->PhpSpreadsheet->addTableRow($dato);
}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpSpreadsheet->addTableFooter();
$this->PhpSpreadsheet->output(sprintf('Ubicaciones_%s.csv', date('Y_m_d-H_i_s')), 'Csv');
