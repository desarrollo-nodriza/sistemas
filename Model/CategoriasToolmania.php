<?php
App::uses('AppModel', 'Model');
class CategoriasToolmania extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	
	/**
	 * Set Cake config DB
	 */
	public $name = 'CategoriasToolmania';
	public $useTable = 'categorias_toolmanias';
	public $primaryKey = 'id';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'newsletter';

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