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
		'Tienda' => array(
			'className'				=> 'Tienda',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
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
}
