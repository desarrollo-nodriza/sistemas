<?php
App::uses('AppModel', 'Model');
class Dte extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */

	public $displayField	= 'folio';

	/**
	 * BEHAVIORS
	 */
	var $actsAs			= array(
		/**
		 * IMAGE UPLOAD
		 */
		/*
		'Image'		=> array(
			'fields'	=> array(
				'imagen'	=> array(
					'versions'	=> array(
						array(
							'prefix'	=> 'mini',
							'width'		=> 100,
							'height'	=> 100,
							'crop'		=> true
						)
					)
				)
			)
		)
		*/
	);

	/**
	 * VALIDACIONES
	 */
	public $belongsTo = array(
		'Orden' => array(
			'className'				=> 'Orden',
			'foreignKey'			=> 'id_order',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Administrador' => array(
			'className'				=> 'Administrador',
			'foreignKey'			=> 'administrador_id',
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
		),
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Venta')
		)
	);

	public $hasMany = array(
		'DteReferencia' => array(
			'className'				=> 'DteReferencia',
			'foreignKey'			=> 'dte_id',
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
		'DteDetalle' => array(
			'className'				=> 'DteDetalle',
			'foreignKey'			=> 'dte_id',
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


	public function beforeSave($options = array())
	{	
		if (!isset($this->data['Dte']['tienda_id'])) {
			$this->data['Dte']['tienda_id'] = CakeSession::read('Tienda.id');
		}
	}


	/**
	 * Retorna solo las boletas-facturas que no esten nvalidadas por una NTD
	 * @param  array  $dtes [description]
	 * @return [type]       [description]
	 */
	public function preparar_dte_venta_valido($dtes = array())
	{
		$dteValido = array();

		foreach ($dtes as $i => $dte) {

			if ($dte['invalidado']) {
				continue;
			}

			if ($dte['estado'] != 'dte_real_emitido') {
				continue;
			}

			# solo boleta o factura no invalidada
			if ($dte['tipo_documento'] == 39 || $dte['tipo_documento'] == 33) {
				$dteValido[$i] = $dte;	
			}
		}

		return $dteValido;
	}


		
	/**
	 * obtener_dtes_mal_emitidos
	 *
	 * @return void
	 */
	public function obtener_dtes_mal_emitidos()
	{
		return $this->find('all', array(
			'conditions' => array(
				'Dte.estado' => array(
					'no_generado',
					'dte_temporal_no_emitido',
					'dte_real_no_emitido',
					'dte_no_emitido',
					''
				)
			),
			'contain' => array(
				'DteDetalle',
				'DteReferencia'
			)
		));
	}
	

	/**
	 * limpiar_dte
	 *
	 * @return void
	 */
	public function limpiar_dte()
	{
		return $this->deleteAll(array(
			'Dte.estado' => array(
				'no_generado',
				'dte_temporal_no_emitido',
				'dte_real_no_emitido',
				'dte_no_emitido'
			)
		), false);
	}

		
	/**
	 * obtener_dte_valido_venta
	 *
	 * @param  mixed $id_venta
	 * @return void
	 */
	public function obtener_dte_valido_venta($id_venta)
	{
		return $this->find('first', array(
			'conditions' => array(
				'Dte.venta_id' => $id_venta,
				'Dte.estado' => 'dte_real_emitido',
				'Dte.tipo_documento' => array(
					33, 39
				),
				'Dte.invalidado' => 0
			)
		));
	}
}
