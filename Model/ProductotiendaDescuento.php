<?php 
App::uses('AppModel', 'Model');

Class ProductotiendaDescuento extends AppModel {

	/**
	 * Set Cake config DB
	 */
	

	/**
	* Config
	*/
	public $useDbConfig = 'reportes';
	public $displayField	= 'nombre';

	/**
	* Asociaciones
	*/
	public $belongsTo = array(
		'Productotienda' => array(
			'className'				=> 'Productotienda',
			'foreignKey'			=> 'id_product',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Tienda' => array(
			'className'				=> 'Tienda',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
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