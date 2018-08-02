<?php
App::uses('AppModel', 'Model');
class CategoriasProductoTienda extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	
	/**
	 * Set Cake config DB
	 */
	public $name = 'CategoriasProductotienda';
	public $useTable = 'categorias_productotiendas';
	public $primaryKey = 'id';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'reportes';

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