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
foreach ($pagos as $pago) {

    $facturas    = implode(',', array_unique(Hash::extract($pago, 'OrdenCompraFactura.{n}.folio')));
    $ocs         = array();
    $proveedores = array();
    $proveedor   = array();

    # Ordenamos las ocs
    if (!empty($pago['OrdenCompra'])){
        $ocs[] = $pago['Pago']['orden_compra_id'];
    }

    $ocs = array_replace_recursive($ocs, Hash::extract($pago, 'OrdenCompraFactura.orden_compra_id'));
    $ocs = implode(',', array_unique($ocs));

    if (!empty($pago['Proveedor']))
    {
        $proveedor = $pago['Proveedor'];
    } 
    else if (!empty($pago['OrdenCompra']['Proveedor']))
    {
        $proveedor = $pago['OrdenCompra']['Proveedor'];
    }
    else if (!empty($pago['OrdenCompraFactura']))
    {
        $proveedor = Hash::extract($pago, 'OrdenCompraFactura.{n}.Proveedor')[0];
    }
    
    switch($formato)
    {
        case 'pago':
            $this->PhpSpreadsheet->addTableRow(
                array(
                    $pago['CuentaBancaria']['numero_cuenta'],
                    'CLP',
                    $proveedor['cuenta_bancaria'],
                    'CLP',
                    $proveedor['codigo_banco'],
                    str_replace('-', '', str_replace('.', '', $proveedor['rut_empresa'])),
                    $proveedor['nombre'],
                    $pago['Pago']['monto_pagado'],
                    '',
                    $proveedor['email_contacto']
                )
            );
        break;

        case 'normal':
            $this->PhpSpreadsheet->addTableRow(
                array(
                    $pago['Pago']['id'],
                    $pago['CuentaBancaria']['alias'],
                    $pago['Moneda']['nombre'],
                    $pago['Pago']['identificador'],
                    $pago['Pago']['fecha_pago'],
                    CakeNumber::currency(h($pago['Pago']['monto_pagado']), 'CLP'),
                    ($pago['Pago']['pagado']) ? 'SI' : 'NO' ,
                    $pago['Pago']['created'],
                    $pago['Pago']['modified'],
                    $facturas,
                    $ocs,
                    $proveedor['nombre']
                )
            );
        break;
    }

}

/**
 * Cierra la tabla y crea el archivo
 */
$this->PhpSpreadsheet->addTableFooter();
$this->PhpSpreadsheet->output(sprintf('listado-de-pagos-%s.xlsx', date('Y-m-d_H-i-s')));
