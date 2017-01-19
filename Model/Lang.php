<?php 
App::uses('AppModel', 'Model');

Class Lang extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Lang';
	public $useTable = 'lang';
	public $primaryKey = 'id_lang';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'toolmania';


	public $belongsTo = array(
		'TaxLang' => array(
			'className'				=> 'TaxLang',
			'foreignKey'			=> 'id_lang',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);
}