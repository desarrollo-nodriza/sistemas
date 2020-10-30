<?php 
App::uses('AppModel', 'Model');

Class Contacto extends AppModel {

	/**
	* Config
	*/
	public $displayField	= 'id';

	/**
	 * Asosiaciones
	 * @var array
	 */
	public $belongsTo = array(
		'VentaCliente' => array(
			'className'				=> 'VentaCliente',
			'foreignKey'			=> 'cliente_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
        ),
        'Tienda' => array(
			'className'				=> 'Tienda',
			'foreignKey'			=> 'tienda_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Administrador' => array(
			'className'				=> 'Administrador',
			'foreignKey'			=> 'administrador_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);


	public function origenes()
	{
		return $this->find('list', array(
			'fields' => array(
				'origen',
				'origen'
			),
			'group' => array('origen')
		));
	}

	public function asuntos()
	{
		return $this->find('list', array(
			'fields' => array(
				'asunto',
				'asunto'
			),
			'group' => array('asunto')
		));
	}


	public function ultimo_admin_id()
	{	
		$ultimo = $this->find('first', array(
			'fields' => array(
				'administrador_id'
			),
			'order' => array('id' => 'desc')
		));

		if (empty($ultimo['Contacto']['administrador_id']))
		{
			return null;
		}

		return $ultimo['Contacto']['administrador_id'];

	}

}