<?php 
App::uses('AppModel', 'Model');

Class HistorialEmbalajeProductoWarehouse extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $useDbConfig = 'warehouse';
	public $useTable = 'historial_embalajes';
	public $displayField	= 'id';


	/**
	 * Asosiaciones
	 * @var array
	 */
	public $belongsTo = array(
		'EmbalajeProductoWarehouse' => array(
			'className'				=> 'EmbalajeProductoWarehouse',
			'foreignKey'			=> 'embalaje_productos_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
        )
	);

}