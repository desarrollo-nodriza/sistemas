<?php
App::uses('AppModel', 'Model');
class EspecificacionValorProductotienda extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $name = 'EspecificacionValorProductotienda';
	public $useTable = 'feature_product';
	public $primaryKey = 'id_product';

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
}