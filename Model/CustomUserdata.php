<?php 
App::uses('AppModel', 'Model');

Class CustomUserdata extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'CustomUserdata';
	public $useTable = 'fmm_custom_userdata';
	public $primaryKey = 'value_id';

	/**
	* Config
	*/

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
		),
		'CustomField' => array(
			'className'				=> 'CustomField',
			'foreignKey'			=> 'id_custom_field',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);
}