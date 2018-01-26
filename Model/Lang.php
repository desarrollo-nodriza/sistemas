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

	public $hasAndBelongsToMany = array(
		'Paise' => array(
			'className'				=> 'Paise',
			'joinTable'				=> 'country_lang',
			'foreignKey'			=> 'id_lang',
			'associationForeignKey'	=> 'id_country',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'PaisIdioma',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'OrdenEstado' => array(
			'className'				=> 'OrdenEstado',
			'joinTable'				=> 'order_state_lang',
			'foreignKey'			=> 'id_lang',
			'associationForeignKey'	=> 'id_order_state',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'OrdenEstadoIdioma',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Productotienda' => array(
			'className'				=> 'Productotienda',
			'joinTable'				=> 'product_lang',
			'foreignKey'			=> 'id_lang',
			'associationForeignKey'	=> 'id_product',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'ProductotiendaIdioma',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Especificacion' => array(
			'className'				=> 'Especificacion',
			'joinTable'				=> 'feature_lang',
			'foreignKey'			=> 'id_lang',
			'associationForeignKey'	=> 'id_feature',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> '',
			'finderQuery'			=> 'EspecificacionIdioma',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'EspecificacionValor' => array(
			'className'				=> 'EspecificacionValor',
			'joinTable'				=> 'feature_value_lang',
			'foreignKey'			=> 'id_lang',
			'associationForeignKey'	=> 'id_feature_value',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> '',
			'finderQuery'			=> 'EspecificacionValorIdioma',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'CustomField' => array(
			'className'				=> 'CustomField',
			'joinTable'				=> 'fmm_custom_fields_lang',
			'foreignKey'			=> 'id_lang',
			'associationForeignKey'	=> 'id_custom_field',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> '',
			'finderQuery'			=> 'CustomFieldLang',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),

	);

	public $hasMany = array(
		'ClienteHilo' => array(
			'className'				=> 'ClienteHilo',
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