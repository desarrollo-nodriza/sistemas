<?php
App::uses('AppModel', 'Model');
class VentaEstado extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';

	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'VentaEstadoCategoria' => array(
			'className'				=> 'VentaEstadoCategoria',
			'foreignKey'			=> 'venta_estado_categoria_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		)
	);
	public $hasMany = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_estado_id',
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


	public function obtener_estado_por_nombre($estado = '')
	{
		return $this->find('first', array(
			'conditions' => array(
				'VentaEstado.nombre' => trim($estado)
				)
			)
		);
	}


}
