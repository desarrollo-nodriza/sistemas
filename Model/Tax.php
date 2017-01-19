<?php 
App::uses('AppModel', 'Model');

Class Tax extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Tax';
	public $useTable = 'tax';
	public $primaryKey = 'id_tax';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'toolmania';

	public $hasMany = array(
		'TaxRule' => array(
			'className'				=> 'TaxRule',
			'foreignKey'			=> 'id_tax',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		)
	);

}