<?php 
App::uses('AppModel', 'Model');

Class SpecificPricePriority extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'SpecificPricePriority';
	public $useTable = 'specific_price_priority';
	public $primaryKey = 'id_specific_price_priority';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'toolmania';

	public $belongsTo = array(
		'SpecificPricePriority' => array(
			'className'				=> 'SpecificPricePriority',
			'foreignKey'			=> 'id_product',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

}