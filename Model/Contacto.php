<?php
App::uses('AppModel', 'Model');

class Contacto extends AppModel
{

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
		),
		'AtencionCliente' => array(
			'className'				=> 'AtencionCliente',
			'foreignKey'			=> 'administrador_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,

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

		if (empty($ultimo['Contacto']['administrador_id'])) {
			return null;
		}

		return $ultimo['Contacto']['administrador_id'];
	}

	/**
	 * Obtenemos el adminsitrador que atendera la siguiente solicitud de atencion.(Solo uno)
	 * 
	 * Se obtienen los administradores que tienen asignada la atencion al asunto en base al array entrante(Se calcula la mitad). 
	 * 
	 * @param  mixed $atencionCliente_ids
	 * @return void
	 */
	public function obtener_atencion_cliente($atencionCliente_ids)
	{
		
		$ids_atencion = $this->find('all', array(
			'fields'     => array('administrador_id'),
			'conditions' => ['administrador_id' => $atencionCliente_ids],
			'order' 	 => array('id' => 'desc'),
			'limit'	     => round(count($atencionCliente_ids) / 2)
		));

		$ids_atencion =	count($ids_atencion) == 1 ? Hash::extract($ids_atencion, '{n}.Contacto.administrador_id') : array_diff($atencionCliente_ids, Hash::extract($ids_atencion, '{n}.Contacto.administrador_id'));
		
		$ids_atencion = array_values($ids_atencion);
		$ids_atencion = $ids_atencion[rand(0, (count($ids_atencion) - 1))] ?? null;

		if (!$ids_atencion) {
			
			$ids_atencion = $this->find('first', array(
				'fields'     => array('administrador_id'),
				'order' 	 => array('id' => 'desc')
			));
			$ids_atencion = $ids_atencion['Contacto']['administrador_id'] ?? null;
		}

		return $ids_atencion;
	}
}
