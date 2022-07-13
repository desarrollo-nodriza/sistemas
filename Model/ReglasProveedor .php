<?php
App::uses('AppModel', 'Model');

class ReglasProveedor extends AppModel
{

	public $useTable 		= 'reglas_proveedores';
	public $primaryKey 		= 'id';

	public $hasMany = array(
		
		'ReglasGenerarOC' => array(
			'className'				=> 'ReglasGenerarOC',
			'foreignKey'			=> 'regla_generar_oc_id',
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
