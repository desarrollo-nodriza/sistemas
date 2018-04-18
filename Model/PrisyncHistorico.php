<?php
App::uses('AppModel', 'Model');
class PrisyncHistorico extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'precio';

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
		'PrisyncRuta' => array(
			'className'				=> 'PrisyncRuta',
			'foreignKey'			=> 'ruta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'PrisyncProducto')
		)
	);
}
