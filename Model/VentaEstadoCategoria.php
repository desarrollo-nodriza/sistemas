<?php
App::uses('AppModel', 'Model');
class VentaEstadoCategoria extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';

	/**
	 * ASOCIACIONES
	 */
	public $hasMany = array(
		'VentaEstado' => array(
			'className'				=> 'VentaEstado',
			'foreignKey'			=> 'venta_estado_categoria_id',
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
