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
foreach ( $campos as $campo )
{
	array_push($cabeceras, array_merge(array('label' => Inflector::humanize($campo)), $opciones));
}
$this->PhpExcel->addTableHeader($cabeceras, array('bold' => true));

/**
 * Escribe los datos
 */
foreach ( $datos as $dato )
{
	$this->PhpExcel->addTableRow(current($dato));
}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpExcel->addTableFooter();
$this->PhpExcel->output(sprintf('Listado_%s_%s.xls', $modelo, date('Y_m_d-H_i_s')));
