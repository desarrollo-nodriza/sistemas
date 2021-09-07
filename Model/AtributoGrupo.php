<?php 
App::uses('AppModel', 'Model');

Class AtributoGrupo extends AppModel {

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
		)
	);

	public $hasMany = array(
		'Atributo' => array(
			'className'				=> 'Atributo',
			'foreignKey'			=> 'atributo_grupo_id',
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

    
    /**
     * obtener_por_nombre
     *
     * @param  mixed $nombre
     * @return void
     */
    public function obtener_por_nombre($nombre)
    {
        $atributo_grupo = $this->find('first', array(
            'conditions' => array(
                'nombre' => trim($nombre)
            )
        ));

        if (!$atributo_grupo)
        {   
            $this->create();
            $this->save(array(
                'AtributoGrupo' => array(
                    'nombre' => $nombre
                )
            ));

            $atributo_grupo = array(
                'AtributoGrupo' => array(
                    'id' => $this->id,
                    'nombre' => $this->field('nombre'),
                    'created' => $this->field('created'),
                    'modified' => $this->field('modified')
                )
            );
        }

        return $atributo_grupo;
    }
}