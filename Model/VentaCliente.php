<?php
App::uses('AppModel', 'Model');
class VentaCliente extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';


	private static $tipo_cliente = array(
		'persona' => 'Persona natural',
		'empresa' => 'Empresa'
	);


	/**
	 * ASOCIACIONES
	 */
	public $hasMany = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_cliente_id',
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
		'Direccion' => array(
			'className'				=> 'Direccion',
			'foreignKey'			=> 'venta_cliente_id',
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
		'Prospecto' => array(
			'className'				=> 'Prospecto',
			'foreignKey'			=> 'venta_cliente_id',
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


	function obtener_tipo_cliente()
	{
		return self::$tipo_cliente;
	}
}
