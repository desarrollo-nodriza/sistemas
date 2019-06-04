<?php
App::uses('AppModel', 'Model');
class Administrador extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $useDbConfig = 'reportes';
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
		'email' => array(
			'email' => array(
				'rule'			=> array('email'),
				'last'			=> true,
				//'message'		=> 'Mensaje de validación personalizado',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'clave' => array(
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
		'repetir_clave' => array(
			'repetirClave' => array(
				'rule'			=> array('repetirClave'),
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
		'Rol' => array(
			'className'				=> 'Rol',
			'foreignKey'			=> 'rol_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		)
	);
	public $hasMany = array(
		'Log' => array(
			'className'				=> 'Log',
			'foreignKey'			=> 'administrador_id',
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
			'foreignKey'			=> 'administrador_id',
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
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'administrador_id',
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
		'Dte' => array(
			'className'				=> 'Dte',
			'foreignKey'			=> 'administrador_id',
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
	 * CALLBACKS
	 */
	public function beforeSave($options = array())
	{
		if ( isset($this->data[$this->alias]['clave']) )
		{
			if ( trim($this->data[$this->alias]['clave']) == false )
			{
				unset($this->data[$this->alias]['clave'], $this->data[$this->alias]['repetir_clave']);
			}
			else
			{
				$this->data[$this->alias]['clave']	= AuthComponent::password($this->data[$this->alias]['clave']);
			}
		}
		return true;
	}


	/**
	 * Obtiene un listado de emails segun l tipo de notificacion activa.
	 *
	 * pagar_oc : Envia un email avisando que hay una OC lista para ser pagada
	 * revision_oc : Envía un email avisando que hay un OC lista para ser revisada
	 * ventas: Notifica las ventas retrasadas
	 * bodegas: Notifica segun la fecha de llegada de un producto de bodega.
	 * 
	 * @param  	string $tipo (pagar_oc, revision_oc, ventas, bodegas)
	 * @return 	array    Lista de emails
	 */
	public function obtener_email_por_tipo_notificacion($tipo = '')
	{

		if (empty($tipo))
			return array();

		$admins = $this->find('all', array(
			'conditions' => array(
				'Administrador.activo' => 1
			),
			'fields' => array(
				'Administrador.email',
				'Administrador.notificaciones'
			)
		));

		$emailsNotificar = array();

		// Obtenemos a los administradores que tiene activa la notificación de oc revision
		foreach ($admins as $ia => $admin) {
			if (!empty($admin['Administrador']['notificaciones'])) {

				$confNotificacion = json_decode($admin['Administrador']['notificaciones'], true);
				
				if ( array_key_exists('pagar_oc', $confNotificacion) && $confNotificacion[$tipo] ) {
					$emailsNotificar[] = $admin['Administrador']['email'];
				}
			}
		}

		return $emailsNotificar;
	}
}
