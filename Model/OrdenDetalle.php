<?php 
App::uses('AppModel', 'Model');

Class OrdenDetalle extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'OrdenDetalle';
	public $useTable = 'order_detail';
	public $primaryKey = 'id_order_detail';

	/**
	* Config
	*/
	public $displayField	= 'id_order_detail';

	public $validate = array(

	);


	/**
	 * Asosiaciones
	 * @var array
	 */
	
	public $belongsTo = array(
		'Orden' => array(
			'className'				=> 'Orden',
			'foreignKey'			=> 'id_order',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);
}