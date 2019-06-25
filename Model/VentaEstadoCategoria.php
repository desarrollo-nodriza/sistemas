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


	public function final_unico($data = array())
	{
		if ($data['VentaEstadoCategoria']['final']) {
			$estados = $this->find('all', array(
				'conditions' => array(
					'VentaEstadoCategoria.final' => 1
				)
			));

			foreach ($estados as $ie => $e) {
				$estados[$ie]['VentaEstadoCategoria']['final'] = 0;
			}
			$this->saveMany($estados, array('callbacks' => false));
		}

		return;
	}



	public function aceptado_rechazo($data = array())
	{	
		if ($data['VentaEstadoCategoria']['rechazo'] && $data['VentaEstadoCategoria']['rechazo']) {
			return false;		
		}
		return true;
	}
}
