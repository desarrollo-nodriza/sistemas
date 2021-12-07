<?php
App::uses('AppModel', 'Model');
class Rol extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $useDbConfig = 'reportes';
	public $displayField	= 'nombre';


	public $app = array(
		'general'            => 'No definido',
		'vendedor'           => 'Vendedor',
		'encargado_bodega'   => 'Encargado de bodega',
		'chofer_transporte'  => 'Chofer',
		'ayudante_bodega'    => 'Ayudante de bodega',
		'pioneta_transporte' => 'Pioneta'
	);

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

	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		/*'Bodega' => array(
			'className'				=> 'Bodega',
			'foreignKey'			=> 'bodega_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Comentario')
        )*/
    );

	public $hasMany = array(
		'Administrador' => array(
			'className'				=> 'Administrador',
			'foreignKey'			=> 'rol_id',
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
		'Modulo' => array(
			'className'				=> 'Modulo',
			'joinTable'				=> 'modulos_roles',
			'foreignKey'			=> 'rol_id',
			'associationForeignKey'	=> 'modulo_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Bodega' => array(
			'className'				=> 'Bodega',
			'joinTable'				=> 'bodegas_roles',
			'foreignKey'			=> 'rol_id',
			'associationForeignKey'	=> 'bodega_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'width'					=> 'BodegasRol',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);


	public function afterFind($results, $primary = false) {


		# Convertimos los permisos en un "Modelo"
		foreach ($results as $key => $val) {

	        if (isset($val['Rol']['permisos'])) {
	            $results[$key]['Rol']['permisos'] = json_decode($results[$key]['Rol']['permisos'], true);
	        }
	    }
	    return $results;
	}

	public function beforeSave($options = array())
	{	
		if (isset($this->data['Bodega']))
		{	
			
			# Tomamos la primera bodega y la seteamos a por defecto
			foreach ($this->data['Bodega'] as $i => $b) 
			{
				if (!isset($b['bodega_id']))
				{
					unset($this->data['Bodega'][$i]);
					continue;
				}
				
				# Llevamos todas las bodegas a no default
				$this->data['Bodega'][$i]['default'] = 0;

			}

			$this->data['Bodega'] = array_values($this->data['Bodega']);
			
			# Normalizamos
			if (!empty($this->data['Bodega']))
			{
				$this->data['Rol']['bodega_id'] = $this->data['Bodega'][0]['bodega_id'];
				$this->data['Bodega'][0]['default'] = 1;
			}
			
			return true;
		}
	}
}
