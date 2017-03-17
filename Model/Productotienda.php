<?php 
App::uses('AppModel', 'Model');

Class Productotienda extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Productotienda';
	public $useTable = 'product';
	public $primaryKey = 'id_product';


	/**
	* Config
	*/
	public $displayField	= 'reference';

	/**
	* Asociaciones
	*/
	public $hasAndBelongsToMany = array(
		'Categoria' => array(
			'className'				=> 'Categoria',
			'joinTable'				=> 'categorias_productotiendas',
			'foreignKey'			=> 'id_product',
			'associationForeignKey'	=> 'categoria_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'CategoriasProductotienda',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Prospecto' => array(
			'className'				=> 'Prospecto',
			'joinTable'				=> 'productotiendas_prospectos',
			'foreignKey'			=> 'id_product',
			'associationForeignKey'	=> 'prospecto_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'ProductotiendaProspecto',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Cotizacion' => array(
			'className'				=> 'Cotizacion',
			'joinTable'				=> 'productotiendas_cotizaciones',
			'foreignKey'			=> 'id_product',
			'associationForeignKey'	=> 'cotizacion_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'ProductotiendaCotizacion',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Lang' => array(
			'className'				=> 'Lang',
			'joinTable'				=> 'product_lang',
			'foreignKey'			=> 'id_product',
			'associationForeignKey'	=> 'id_lang',
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
		)
	);

	public $belongsTo = array(
		'TaxRulesGroup' => array(
			'className'				=> 'TaxRulesGroup',
			'foreignKey'			=> 'id_tax_rules_group',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

	public $hasMany = array(
		'SpecificPrice' => array(
			'className'				=> 'SpecificPrice',
			'foreignKey'			=> 'id_product',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'SpecificPricePriority' => array(
			'className'				=> 'SpecificPricePriority',
			'foreignKey'			=> 'id_product',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'ProductotiendaDescuento' => array(
			'className'				=> 'ProductotiendaDescuento',
			'foreignKey'			=> 'id_product',
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
	
	/**
	* CAllbacks
	*/

	public function beforeSave($options = array()) {
		parent::beforeSave();
	}

	public function afterSave($created = null, $options = Array()) {
		parent::afterSave();
	}

}
	
?>