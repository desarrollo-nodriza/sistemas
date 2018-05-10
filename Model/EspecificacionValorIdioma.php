<?php 
App::uses('AppModel', 'Model');

Class EspecificacionValorIdioma extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'EspecificacionValorIdioma';
	public $useTable = 'feature_value_lang';
	public $primaryKey = 'id_feature_value';
	

	/*public $hasMany = array(
		'Idioma' => array(
			'className'				=> 'Idioma',
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
	);*/
}