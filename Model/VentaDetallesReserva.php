<?php
App::uses('AppModel', 'Model');

class VentaDetallesReserva extends AppModel
{
    public $displayField  = 'id';
    public $virtualFields = array(
        'cantidad_reservada_total' => 'SUM(cantidad_reservada)'
    );
    public $useTable = 'venta_detalles_reservas';
    public $belongsTo = array(
        'Bodega' => array(
            'className'     => 'Bodega',
            'foreignKey'    => 'bodega_id',
            'conditions'    => '',
            'fields'        => '',
            'order'         => '',
            'counterCache'  => true,
        ),

    );
}
