<?php 
App::uses('AppModel', 'Model');

Class Atributo extends AppModel {

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
		'atributo_grupo_id' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		)
	);


    public $belongsTo = array(
		'AtributoGrupo' => array(
			'className'				=> 'AtributoGrupo',
			'foreignKey'			=> 'atributo_grupo_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

    public $hasAndBelongsToMany = array(
		'VentaDetalle' => array(
			'className'				=> 'VentaDetalle',
			'joinTable'				=> 'venta_detalles_atributos',
			'foreignKey'			=> 'atributo_id',
			'associationForeignKey'	=> 'venta_detalle_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
    );


    /**
     * obtener_por_nombre
     *
     * @param  mixed $nombre
     * @return void
     */
    public function obtener_por_nombre_grupo($nombre, $grupo_id)
    {
        $atributo = $this->find('first', array(
            'conditions' => array(
                'nombre' => trim($nombre),
                'atributo_grupo_id' => $grupo_id
            )
        ));

        if (!$atributo)
        {   
            $this->create();
            $this->save(array(
                'Atributo' => array(
                    'nombre' => $nombre,
                    'atributo_grupo_id' => $grupo_id
                )
            ));

            $atributo = array(
                'Atributo' => array(
                    'id' => $this->id,
                    'atributo_grupo_id' => $grupo_id,
                    'nombre' => $this->field('nombre'),
                    'created' => $this->field('created'),
                    'modified' => $this->field('modified')
                )
            );
        }

        return $atributo;
    }
}