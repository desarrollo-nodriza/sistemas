<?php
App::uses('AppModel', 'Model');
class EspecificacionProductotienda extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $name = 'EspecificacionProductotienda';
	public $useTable = 'feature_product';
	public $primaryKey = 'id_feature';

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
