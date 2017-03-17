<?php 
App::uses('AppModel', 'Model');

Class Orders extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Orders';
	public $useTable = 'orders';
	public $primaryKey = 'id_order';


	public $belongsTo = array(
		'OrdenEstado' => array(
			'className'				=> 'OrdenEstado',
			'foreignKey'			=> 'current_state',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'OrdenEstado')
		)
	);

}