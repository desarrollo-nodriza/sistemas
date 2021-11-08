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
		),
		'EstadoEnvioCategoria' => array(
			'className'				=> 'EstadoEnvioCategoria',
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


	public function obtener_estado_por_id($id = '')
	{
		return $this->find('first', array(
			'conditions' => array(
				'VentaEstado.id' => trim($id)
				)
			)
		);
	}


	public function es_estado_pagado($estado_id)
	{
		$est = $this->find('first', array(
			'conditions' => array(
				'VentaEstado.id' => $estado_id
			),
			'contain' => array(
				'VentaEstadoCategoria' => array(
					'fields' => array(
						'VentaEstadoCategoria.id',
						'VentaEstadoCategoria.venta'
					)
				)
			),
			'fields' => array(
				'VentaEstado.id'
			)
		));
		
		if (empty($est))
			return false;

		if (empty($est['VentaEstadoCategoria']))
			return false;

		return $est['VentaEstadoCategoria']['venta'];

	}


	public function es_estado_entregado($estado_id)
	{
		$est = $this->find('first', array(
			'conditions' => array(
				'VentaEstado.id' => $estado_id
			),
			'contain' => array(
				'VentaEstadoCategoria' => array(
					'fields' => array(
						'VentaEstadoCategoria.id',
						'VentaEstadoCategoria.final'
					)
				)
			),
			'fields' => array(
				'VentaEstado.id'
			)
		));
		
		if (empty($est))
			return false;

		if (empty($est['VentaEstadoCategoria']))
			return false;

		return $est['VentaEstadoCategoria']['final'];

	}


	public function es_estado_rechazo($estado_id)
	{
		$est = $this->find('first', array(
			'conditions' => array(
				'VentaEstado.id' => $estado_id
			),
			'contain' => array(
				'VentaEstadoCategoria' => array(
					'fields' => array(
						'VentaEstadoCategoria.id',
						'VentaEstadoCategoria.rechazo'
					)
				)
			),
			'fields' => array(
				'VentaEstado.id'
			)
		));

		if (empty($est))
			return false;

		if (empty($est['VentaEstadoCategoria']))
			return false;

		return $est['VentaEstadoCategoria']['rechazo'];
	}


	public function es_estado_cancelado($estado_id)
	{
		$est = $this->find('first', array(
			'conditions' => array(
				'VentaEstado.id' => $estado_id
			),
			'contain' => array(
				'VentaEstadoCategoria' => array(
					'fields' => array(
						'VentaEstadoCategoria.id',
						'VentaEstadoCategoria.cancelado'
					)
				)
			),
			'fields' => array(
				'VentaEstado.id'
			)
		));

		if (empty($est))
			return false;

		if (empty($est['VentaEstadoCategoria']))
			return false;

		return $est['VentaEstadoCategoria']['cancelado'];
	}

	public function es_un_estado_de_una_venta($estado_id)
	{
		$est = $this->find('first', array(
			'conditions' => array(
				'VentaEstado.id' => $estado_id
			),
			'contain' => array(
				'VentaEstadoCategoria' => array(
					'fields' => array(
						'VentaEstadoCategoria.id'
					)
					,'conditions' => array(
						'VentaEstadoCategoria.venta' => true
					)
				)
			),
			'fields' => array(
				'VentaEstado.id'
			)
		));		
		return ($est['VentaEstadoCategoria']['id'])?true:false;
	}


	public function estado_mueve_bodega($estado_id)
	{
		$est = $this->find('first', array(
			'conditions' => array(
				'VentaEstado.id' => $estado_id
			),
			'contain' => array(
				'VentaEstadoCategoria' => array(
					'fields' => array(
						'VentaEstadoCategoria.id',
						'VentaEstadoCategoria.retiro_en_tienda',
						'VentaEstadoCategoria.listo_para_envio',
						'VentaEstadoCategoria.envio',
						'VentaEstadoCategoria.final'
					)
				)
			),
			'fields' => array(
				'VentaEstado.id'
			)
		));

		if (empty($est))
			return false;

		if (empty($est['VentaEstadoCategoria']))
			return false;

		if ($est['VentaEstadoCategoria']['final'])
			return false;

		if ($est['VentaEstadoCategoria']['retiro_en_tienda']
			|| $est['VentaEstadoCategoria']['listo_para_envio']
			|| $est['VentaEstadoCategoria']['envio'])
			{
				return true;
			}
		
			return false;
	}


	public function obtener_estado_preparacion()
	{
		return $this->find('first', array('conditions' => array('preparacion' => 1, 'origen' => 0)));
	}


	public function obtener_estados_logistica($lista = false)
	{	
		if ($lista) {
			return $this->find('list', array('conditions' => array('logistica' => 1)));
		}else{
			return $this->find('all', array('conditions' => array('logistica' => 1)));
		}
		
	}


}
