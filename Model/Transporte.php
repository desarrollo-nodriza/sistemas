<?php
App::uses('AppModel', 'Model');
class Transporte extends AppModel
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
		'precio' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'activo' => array(
			'boolean' => array(
				'rule'			=> array('boolean'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
	);


	public $hasMany = array(
		'Prospecto' => array(
			'className'				=> 'Prospecto',
			'foreignKey'			=> 'transporte_id',
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
		'Cotizacion' => array(
			'className'				=> 'Cotizacion',
			'foreignKey'			=> 'transporte_id',
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
		'Manifiesto' => array(
			'className'				=> 'Manifiesto',
			'foreignKey'			=> 'transporte_id',
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

	public $hasAndBelongsToMany = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'joinTable'				=> 'transportes_ventas',
			'foreignKey'			=> 'transporte_id',
			'associationForeignKey'	=> 'venta_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);


	public function obtener_transporte_por_nombre($nombre = '', $solo_id = true, $opt = array())
	{
		$transporte = $this->find('first', array(
			'conditions' => array(
				'Transporte.nombre' => trim($nombre)
			)
		));

		if (empty($transporte)) {
			# Lo creamos
			
			$nw_transporte = array(
				'Transporte' => array(
					'nombre' => $nombre
				)
			);

			if (!empty($opt)) {
				$nw_transporte = array_replace_recursive($nw_transporte, $opt);
			}

			$this->create();
			$this->save($nw_transporte);				

			if ($solo_id) {
				return $this->id;
			}

			$transporte = $this->find('first', array(
				'conditions' => array(
					'Transporte.id' => $this->id
				)
			));

		}

		return ($solo_id) ? $transporte['Transporte']['id'] : $transporte;
		
	}
}
