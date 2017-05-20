<?php 
App::uses('AppModel', 'Model');

Class EspecificacionValor extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'EspecificacionValor';
	public $useTable = 'feature_value';
	public $primaryKey = 'id_feature_value';


	public $belongsTo = array(
		'Especificacion' => array(
			'className'				=> 'Especificacion',
			'foreignKey'			=> 'id_feature',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

	public $hasAndBelongsToMany = array(
		'Lang' => array(
			'className'				=> 'Lang',
			'joinTable'				=> 'feature_value_lang',
			'foreignKey'			=> 'id_feature_value',
			'associationForeignKey'	=> 'id_lang',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '', 
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'EspecificacionValorIdioma',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Productotienda' => array(
			'className'				=> 'Productotienda',
			'joinTable'				=> 'feature_product',
			'foreignKey'			=> 'id_feature_value',
			'associationForeignKey'	=> 'id_product',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '', 
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'EspecificacionValorProductotienda',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);
}