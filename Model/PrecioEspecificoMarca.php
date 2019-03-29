<?php
App::uses('AppModel', 'Model');
class PrecioEspecificoMarca extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';


	public $belongsTo = array(
		'Marca' => array(
			'className'				=> 'Marca',
			'foreignKey'			=> 'marca_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		)
	);

}
