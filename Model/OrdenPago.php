<?php 
App::uses('AppModel', 'Model');

Class OrdenPago extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'OrdenPago';
	public $useTable = 'order_payment';
	public $primaryKey = 'id_order_payment';

	/**
	* Config
	*/
	public $displayField	= 'id_order_payment';

	public $validate = array(

	);


	/**
	 * Asosiaciones
	 * @var array
	 */
	
	public $belongsTo = array(
		'Orden' => array(
			'className'				=> 'Orden',
			'foreignKey'			=> false,
			'conditions'			=> array('OrdenPago.order_reference' => 'Orden.reference'),
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);
}