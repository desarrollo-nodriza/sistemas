<?php 
App::uses('AppModel', 'Model');

Class TaxLang extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'TaxLang';
	public $useTable = 'tax_lang';
	public $primaryKey = 'id_lang';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'toolmania';


	public $hasMany = array(
		'Lang' => array(
			'className'				=> 'Lang',
			'foreignKey'			=> 'id_lang',
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