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
foreach ($pago['OrdenCompraFactura'] as $factura) {

	$this->PhpSpreadsheet->addTableRow(
		array(
			$factura['Proveedor']['rut_empresa'],
			$factura['Proveedor']['nombre'],
			$factura['monto_facturado'],
			$factura['folio'],
		)
    );
}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpSpreadsheet->addTableFooter();
$this->PhpSpreadsheet->output(sprintf('listado-de-pagos-%d-%s.xlsx', $pago['Pago']['id'], date('Y-m-d_H-i-s')));
