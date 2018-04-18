<?php
App::uses('AppModel', 'Model');
class PrisyncRuta extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'url';

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

	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'PrisyncProducto' => array(
			'className'				=> 'PrisyncProducto',
			'foreignKey'			=> 'prisync_producto_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'PrisyncProducto')
		)
	);

	public $hasMany = array(
		'PrisyncHistorico' => array(
			'className'				=> 'PrisyncHistorico',
			'foreignKey'			=> 'ruta_id',
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
