<?php 
App::uses('AppModel', 'Model');

Class SpecificPrice extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'SpecificPrice';
	public $useTable = 'specific_price';
	public $primaryKey = 'id_specific_price';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'toolmania';

	public $belongsTo = array(
		'SpecificPrice' => array(
			'className'				=> 'SpecificPrice',
			'foreignKey'			=> 'id_product',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

}