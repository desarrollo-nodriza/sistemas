<?php 
App::uses('AppModel', 'Model');

Class EspecificacionIdioma extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'EspecificacionIdioma';
	public $useTable = 'feature_lang';
	public $primaryKey = 'id_feature';
	

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