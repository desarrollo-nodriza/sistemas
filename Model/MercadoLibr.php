<?php
App::uses('AppModel', 'Model');
class MercadoLibr extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';

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
	public $validate = array(
		'mercado_libre_plantilla_id' => array(
			'numeric' => array(
				'rule'			=> array('numeric'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'nombre' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'producto' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
			'maxLength' => array(
				'rule'			=> array('maxLength', 60),
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'id_product' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'html' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
	);

	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'Tienda' => array(
			'className'				=> 'Tienda',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		),
		'MercadoLibrePlantilla' => array(
			'className'				=> 'MercadoLibrePlantilla',
			'foreignKey'			=> 'mercado_libre_plantilla_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		),
		'Productotienda' => array(
			'className'				=> 'Productotienda',
			'foreignKey'			=> 'id_product',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		)
	);

	/**
	 * Functión que permite armar un arreglo ordenado con las secciones del html
	 * @param  array() 	$html 	arreglo que contien la data del html
	 * @return array() 	$htmlEmail 	Arreglo con la data ordenada	
	 */
	public function armarHtml($html = null) {
		
		// Inicio y fin de la fila 
		$block_inicio 	= strpos($html['MercadoLibrePlantilla']['html'], '[FOREACH]');
		$block_final 	= ( strpos($html['MercadoLibrePlantilla']['html'], '[/FOREACH]') - $block_inicio);

		// html fila
		$htmlBlock = substr($html['MercadoLibrePlantilla']['html'], $block_inicio, $block_final );

		// html fila condicionado
		if ( preg_match('/IF/i', $html['MercadoLibrePlantilla']['html']) ) {
    		if ( preg_match('/ELSE/i', $html['MercadoLibrePlantilla']['html']) ) {
    			$ifUno_i = strpos($html['MercadoLibrePlantilla']['html'], '[IF]');
				$ifUno_f = ( strpos($html['MercadoLibrePlantilla']['html'], '[ELSE]') - $ifUno_i);
				$ifDos_i = strpos($html['MercadoLibrePlantilla']['html'], '[ELSE]');
				$ifDos_f = ( strpos($html['MercadoLibrePlantilla']['html'], '[/IF]') - $ifDos_i);

				$htmlIf_uno = substr($html['MercadoLibrePlantilla']['html'], $ifUno_i, $ifUno_f );
				$htmlIf_dos = substr($html['MercadoLibrePlantilla']['html'], $ifDos_i, $ifDos_f );
    		}
    	}

		// Contenido de cabecera y footer
		$htmlCabecera 	= substr($html['MercadoLibrePlantilla']['html'], 0, $block_inicio );
		$htmlFooter 	= substr($html['MercadoLibrePlantilla']['html'], strpos($html['MercadoLibrePlantilla']['html'], '[/FOREACH]') );
		
		// Limpiamos el html de las etiquetas
		$htmlBlock = str_replace('[FOREACH]', '', trim($htmlBlock));

		if ( preg_match('/IF/i', $html['MercadoLibrePlantilla']['html']) ) {
    		if ( preg_match('/ELSE/i', $html['MercadoLibrePlantilla']['html']) ) {
    			$htmlIf_uno = str_replace('[IF]', '', trim($htmlIf_uno));
				$htmlIf_uno = str_replace('[ELSE]', '', trim($htmlIf_uno));
				$htmlIf_dos = str_replace('[ELSE]', '', trim($htmlIf_dos));
				$htmlIf_dos = str_replace('[/IF]', '', trim($htmlIf_dos));
    		}
    	}

		$htmlFooter = str_replace('[/FOREACH]', '', trim($htmlFooter));
		
		$final = array(
			'cabecera' => $htmlCabecera,
			'foreach' => $htmlBlock,
			'footer' => $htmlFooter
			);

		// agrega la fila condicionada al arreglo
		if ( preg_match('/IF/i', $html['MercadoLibrePlantilla']['html']) ) {
    		if ( preg_match('/ELSE/i', $html['MercadoLibrePlantilla']['html']) ) {
    			$final['if_uno'] = $htmlIf_uno;
				$final['if_dos'] = $htmlIf_dos;
    		}
    	}
		
		return $final;
	}
}
