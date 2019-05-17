<?php
App::uses('AppModel', 'Model');
class MetodoEnvio extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';

	/**
	 * ASOCIACIONES
	 */
	public $hasMany = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'metodo_envio_id',
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


	public function obtener_metodo_envio_por_nombre($nombre = '')
	{
		return $this->find('first', array(
			'conditions' => array(
				'MetodoEnvio.nombre' => trim($nombre)
				)
			)
		);
	}
}
