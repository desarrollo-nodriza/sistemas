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
		),
		'Especificacion' => array(
			'className'				=> 'Especificacion',
			'joinTable'				=> 'feature_product',
			'foreignKey'			=> 'id_product',
			'associationForeignKey'	=> 'id_feature',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'EspecificacionProductotienda',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'EspecificacionValor' => array(
			'className'				=> 'EspecificacionValor',
			'joinTable'				=> 'feature_product',
			'foreignKey'			=> 'id_product',
			'associationForeignKey'	=> 'id_feature_value',
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

	public $belongsTo = array(
		'TaxRulesGroup' => array(
			'className'				=> 'TaxRulesGroup',
			'foreignKey'			=> 'id_tax_rules_group',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Fabricante' => array(
			'className'				=> 'Fabricante',
			'foreignKey'			=> 'id_manufacturer',
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
		),
		'ClienteHilo' => array(
			'className'				=> 'ClienteHilo',
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
		'OrdenDetalle' => array(
			'className'				=> 'OrdenDetalle',
			'foreignKey'			=> 'product_id',
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
	
?>