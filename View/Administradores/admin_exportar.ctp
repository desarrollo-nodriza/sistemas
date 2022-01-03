<?php
/**
 * Crea un nuevo documento Excel
 */
$this->PhpSpreadsheet->createWorksheet();

/**
 * Escribe las cabeceras
 */
$headers		= array();
$opciones		= array('width' => 'auto', 'filter' => true, 'wrap' => true);
foreach ( $cabeceras as $campo )
{
	array_push($headers, array_merge(array('label' => Inflector::humanize($campo)), $opciones));
}
$this->PhpSpreadsheet->addTableHeader($headers, array('bold' => true));

/**
 * Escribe los datos
 */
foreach ( $datos as $dato )
{

	$this->PhpSpreadsheet->addTableRow(array(
		$dato['Administrador']['id'],
		$dato['Administrador']['nombre'],
		$dato['Administrador']['email'],
		$dato['Rol']['nombre'],
	));
}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpSpreadsheet->addTableFooter();
$this->PhpSpreadsheet->output(sprintf('Listado_%s_%s.xls', $modelo, date('Y_m_d-H_i_s')));
