<?php 
App::uses('AppModel', 'Model');

Class ClienteMensaje extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'ClienteMensaje';
	public $useTable = 'customer_message';
	public $primaryKey = 'id_customer_message';

	/**
	* Config
	*/
	public $displayField	= 'id_customer_message';

	/**
	 * Asosiaciones
	 * @var array
	 */
	public $belongsTo = array(
		'ClienteHilo' => array(
			'className'				=> 'ClienteHilo',
			'foreignKey'			=> 'id_customer_thread',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Empleado' => array(
			'className'				=> 'Empleado',
			'foreignKey'			=> 'id_employee',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

}