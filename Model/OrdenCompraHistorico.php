<?php
App::uses('AppModel', 'Model');
class OrdenCompraHistorico extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */

	public $belongsTo = array(
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'orden_compra_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

}