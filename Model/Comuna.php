<?php
App::uses('AppModel', 'Model');
class Comuna extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';

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
		'Direccion' => array(
			'className'				=> 'Direccion',
			'foreignKey'			=> 'comuna_id',
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
			'foreignKey'			=> 'comuna_id',
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


	public function obtener_id_comuna_por_nombre($nombre)
	{
		$comuna = $this->find('first', array(
			'conditions' => array(
				'Comuna.nombre' => $nombre
			)
		));

		if (empty($comuna)) {
			$nwComuna = array(
				'nombre' => $nombre
			);

			$this->save($nwComuna);

			return $this->id;
		}

		return $comuna['Comuna']['id'];
	}
}
