<?php
App::uses('AppController', 'Controller');
class AdministradoresController extends AppController
{
	/*public function crear()
	{
		$administrador		= array(
			'nombre'			=> 'Desarrollo Nodriza Spa',
			'email'				=> 'desarrollo@nodriza.cl',
			'clave'				=> 'admin'
		);
		$this->Administrador->deleteAll(array('Administrador.email' => 'desarrollo@nodriza.cl'));
		$this->Administrador->save($administrador);
		$this->Session->setFlash('Administrador creado correctamente. Email: desarrollo@nodriza.cl -- Clave: admin', null, array(), 'success');
		$this->redirect($this->Auth->redirectUrl());
	}*/

	public function admin_login()
	{
		/**
		 * Login normal
		 */
		if ( $this->request->is('post') )
		{
			if ( $this->Auth->login() )
			{	
				# Obtenemos la tienda principal
				$tiendaPrincipal = ClassRegistry::init('Tienda')->find('first', array(
					'conditions' => array('Tienda.principal' => 1),
					'order' => array('Tienda.modified' => 'DESC')
					));
				
				if ( empty($tiendaPrincipal) ) {
					
					# Enviamos mensaje de porque la redirección
					$this->Session->setFlash('No existe una tienda principal, porfavor contácte al encargado.', null, array(), 'danger');

					# Elimina la sesión de google
					$this->Session->delete('Google.token');
					# Eliminamos la sesión tienda
					$this->Session->delete('Tienda');
					# Deslogeamos
					$this->admin_logout();
				}else {

					# Crear Token
    				$token = ClassRegistry::init('Token')->crear_token($this->Auth->id, null, 8760);

					$this->Session->setFlash('Su tienda principal es ' . $tiendaPrincipal['Tienda']['nombre'], null, array(), 'success');
					$this->Session->write('Tienda', $tiendaPrincipal['Tienda']);
					$this->Session->write('Auth.Administrador.token', $token);
				}

				$this->Session->delete('Google.token');
				$this->redirect($this->Auth->redirectUrl());
			}
			else
			{
				$this->Session->setFlash('Nombre de usuario y/o clave incorrectos.', null, array(), 'danger');
			}
		}

		/**
		 * Login con sesion Google
		 */
		if ( $this->Session->check('Google.token') )
		{
			/**
			 * Si el usuario ya tiene sesion de cake activa, lo redirecciona
			 */
			if ( $this->Auth->user() )
			{
				$this->redirect('/');
			}

			/**
			 * Obtiene los datos del usuario
			 */
			$google			= $this->Session->read('Google');
			$this->Google->plus();
			$me				= null;

			/**
			 * Si no obtiene los datos del usuario es porque el token fue invalidado
			 */
			try
			{
				$me				= $this->Google->plus->people->get('me');
			}
			catch ( Exception $e )
			{	
				$this->Auth->logout();
				$this->Session->setFlash('Tu sesión ha expirado. Por favor ingresa nuevamente.', null, array(), 'success');
			}

			/**
			 * Con los datos del usuario google, intenta autenticarlo
			 */

			if ( $me )
			{
				$emails			= $me->getEmails();

				/**
				 * Verificamos que tenemos el email
				 */
				$meEmail = $me->getEmails();
				if ( empty($meEmail) )
				{
					$this->Session->setFlash('No tienes acceso a esta aplicación.', null, array(), 'danger');
				}
				else
				{
					/**
					 * Verificamos que exista el usuario en la DB y esté activo
					 */
					$administrador			= $this->Administrador->find('first', array(
						'conditions'			=> array('Administrador.email' => $emails[0]->value)
					));

					if ( ! $administrador || ! $administrador['Administrador']['activo'] )
					{
						$this->Session->setFlash('No tienes acceso a esta aplicación.', null, array(), 'danger');
					}
					else
					{	
						/**
						 * Si no tiene google_id, es primera vez que entra. Actualiza datos
						 */
						if ( ! $administrador['Administrador']['google_id'] )
						{
							$usuario		= array_merge($administrador['Administrador'], array(
								'google_id'			=> $me->getId(),
								'google_dominio'	=> $me->getDomain(),
								'google_nombre'		=> $me->getName()->givenName,
								'google_apellido'	=> $me->getName()->familyName,
								'google_imagen'		=> $me->getImage()->url
							));

							unset($usuario['clave']);
							$this->Administrador->id = $usuario['id'];
							$this->Administrador->save($usuario);
						}

						/**
						 * Normaliza los datos segun AuthComponent::identify
						 */
						$administrador = $administrador['Administrador'];

						# Obtenemos la tienda principal
						$tiendaPrincipal = ClassRegistry::init('Tienda')->find('first', array(
							'conditions' => array('Tienda.principal' => 1),
							'order' => array('Tienda.modified' => 'DESC')
							));

						if ( empty($tiendaPrincipal) ) {
					
							# Enviamos mensaje de porque la redirección
							$this->Session->setFlash('No existe una tienda principal, porfavor contácte al encargado.', null, array(), 'danger');

							# Elimina la sesión de google
							$this->Session->delete('Google.token');
							# Eliminamos la sesión tienda
							$this->Session->delete('Tienda');
							# Deslogeamos
							$this->admin_logout();
						}else {
							$this->Session->setFlash('Su tienda principal es ' . $tiendaPrincipal['Tienda']['nombre'], null, array(), 'success');
							$this->Session->write('Tienda', $tiendaPrincipal['Tienda']);
						}

						/**
						 * Logea al usuario y lo redirecciona
						 */
						
						$this->Auth->login($administrador);
						$this->Administrador->save(array('id' => $administrador['id'], 'last_login' => date('Y-m-d H:m:s')));
						$this->redirect($this->Auth->redirectUrl());
					}
				}
			}
		}


		/**
		 * Inicializa y configura el cliente Google
		 */
		$authUrl			= $this->Google->cliente->createAuthUrl();
		$this->set(compact('authUrl'));

		$this->layout	= 'login';
	}


	public function admin_login2()
	{	

		if ( $this->Auth->user() )
		{
			$this->redirect('/');
		}

		/**
		 * Login normal
		 */
		if ( $this->request->is('post') )
		{	
			if ( !$this->request->data['Administrador']['login_externo'] && $this->Auth->login() )
			{	
				# Obtenemos la tienda principal
				$tiendaPrincipal = ClassRegistry::init('Tienda')->find('first', array(
					'conditions' => array('Tienda.principal' => 1),
					'order' => array('Tienda.modified' => 'DESC')
					));
				
				if ( empty($tiendaPrincipal) ) {
					
					# Enviamos mensaje de porque la redirección
					$this->Session->setFlash('No existe una tienda principal, porfavor contácte al encargado.', null, array(), 'danger');

					# Elimina la sesión de google
					$this->Session->delete('Google.token');
					# Eliminamos la sesión tienda
					$this->Session->delete('Tienda');
					# Deslogeamos
					$this->admin_logout();
				}else {

					# Crear Token
    				$token = ClassRegistry::init('Token')->crear_token($this->Session->read('Auth.Administrador.id'), null, 8760);

					$this->Session->setFlash('Su tienda principal es ' . $tiendaPrincipal['Tienda']['nombre'], null, array(), 'success');
					$this->Session->write('Tienda', $tiendaPrincipal['Tienda']);

					$this->Session->write('Auth.Administrador.token', $token);
				}

				$this->Session->delete('Google.token');
				$this->redirect($this->Auth->redirectUrl());
			}
			elseif ($this->request->data['Administrador']['login_externo']) {

				$this->Firebase = $this->Components->load('Firebase');

				$logeado = $this->Firebase->isLogged($this->request->data['Administrador']['login_externo']);

				if (!$logeado['logged'])
				{
					$this->Session->setFlash($logeado['message'], null, array(), 'danger');
					$this->redirect(array('action' => 'login2', '?' => array('nologged' => 1)));
				}

				/**
				 * Verificamos que exista el usuario en la DB y esté activo
				 */
				$administrador			= $this->Administrador->find('first', array(
					'conditions'			=> array('Administrador.email' => $this->request->data['Administrador']['email'])
				));

				if ( ! $administrador || ! $administrador['Administrador']['activo'] ) {
					$this->Session->setFlash('No tienes acceso a esta aplicación.', null, array(), 'danger');
				}else{	
					
					/**
					 * Normaliza los datos segun AuthComponent::identify
					 */
					$administrador = $administrador['Administrador'];

					# Obtenemos la tienda principal
					$tiendaPrincipal = ClassRegistry::init('Tienda')->find('first', array(
						'conditions' => array('Tienda.principal' => 1),
						'order' => array('Tienda.modified' => 'DESC')
						));

					if ( empty($tiendaPrincipal) ) {
				
						# Enviamos mensaje de porque la redirección
						$this->Session->setFlash('No existe una tienda principal, porfavor contácte al encargado.', null, array(), 'danger');

						# Elimina la sesión de google
						$this->Session->delete('Google.token');
						# Eliminamos la sesión tienda
						$this->Session->delete('Tienda');
						# Deslogeamos
						$this->admin_logout();
					}else {

						$this->Session->setFlash('Su tienda principal es ' . $tiendaPrincipal['Tienda']['nombre'], null, array(), 'success');
						$this->Session->write('Tienda', $tiendaPrincipal['Tienda']);
					}

					/**
					 * Logea al usuario y lo redirecciona
					 */
					
					$this->Auth->login($administrador);
					$this->Administrador->save(array('id' => $administrador['id'], 'last_login' => date('Y-m-d H:m:s')));
					
					# Crear Token
    				$token = ClassRegistry::init('Token')->crear_token($administrador['id'], null, 8760);
					$this->Session->write('Auth.Administrador.token', $token);
					$this->Session->write('Auth.Administrador.g_token', $this->request->data['Administrador']['login_externo']);
					
					$this->Session->write('Auth.Administrador.Google', $logeado['user']);

					$this->redirect($this->Auth->redirectUrl());
				}

			}else{
				$this->Session->setFlash('Nombre de usuario y/o clave incorrectos.', null, array(), 'danger');
			}
		}

		$this->layout	= 'login';
	}


	public function admin_logout()
	{	
		/**
		*	Elimina la sesión de google
		*/
		$this->Session->delete('Google.token');
		$this->Session->delete('Tienda');
		$this->Session->delete('Marketplace');
		$this->redirect($this->Auth->logout());

	}

	public function admin_lock()
	{
		$this->layout		= 'login';

		if ( ! $this->request->is('post') )
		{
			if ( ! $this->Session->check('Admin.lock') )
			{
				$this->Session->write('Admin.lock', array(
					'status'		=> true,
					'referer'		=> $this->referer()
				));
			}
		}
		else
		{
			$administrador		= $this->Administrador->findById($this->Auth->user('id'));
			if ( $this->Auth->password($this->request->data['Administrador']['clave']) === $administrador['Administrador']['clave'] )
			{
				$referer		= $this->Session->read('Admin.lock.referer');
				$this->Session->delete('Admin.lock');
				$this->redirect($referer);
			}
			else
				$this->Session->setFlash('Clave incorrecta.', null, array(), 'danger');
		}
	}

	public function admin_index()
	{	
		$this->paginate		= array(
			'recursive'			=> 0
		);
		$administradores	= $this->paginate();
		BreadcrumbComponent::add('Administradores ');

		$this->set(compact('administradores'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Administrador->create();
			if ( $this->Administrador->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Administradores ', '/administradores');
		BreadcrumbComponent::add('Agregar ');

		$roles = $this->Administrador->Rol->find('list', array('conditions' => array('Rol.activo' => 1)));

		$this->set(compact('roles'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Administrador->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Administrador->save($this->request->data) )
			{
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		else
		{
			$this->request->data	= $this->Administrador->find('first', array(
				'conditions'	=> array('Administrador.id' => $id)
			));
		}

		BreadcrumbComponent::add('Administradores ', '/administradores');
		BreadcrumbComponent::add('Editar ');

		$roles = $this->Administrador->Rol->find('list', array('conditions' => array('Rol.activo' => 1)));

		$this->set(compact('roles'));
	}

	public function admin_delete($id = null)
	{
		$this->Administrador->id = $id;
		if ( ! $this->Administrador->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Administrador->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Administrador->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Administrador->_schema);
		$modelo			= $this->Administrador->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}



	/**
	 * REST methods
	 */

	public function api_test() {
		App::uses('HttpSocket', 'Network/Http');
		$socket			= new HttpSocket();
		$request		= $socket->post(
			Router::url('/api/administradores/auth.json', true),
			array(
				'email' => 'cristian.rojas@nodriza.cl',
				'clave' => 'vendetta88'
			)
		);

		prx( $request->body );
	}



	/**
	 * Obtiene el token de acceso a los otros recursos
	 * Endpoint :  /api/administradores/auth.json
	 */
    public function api_login() {

    	if ($this->request->is('post')) {

    		$email = $this->request->data['email'];
    		$clave = $this->request->data['clave'];

    		# Que los campos de autenticacion no esten vacios
    		if (empty($email) || empty($clave)) {
    			$response = array(
					'code'    => 502, 
					'message' => 'Email y contraseña son requeridos'
				);

				throw new CakeException($response);
    		}
    		
    		# Buscar usuario
    		$usuario = $this->Administrador->find('first', array(
    			'conditions' => array(
    				'Administrador.email' => $email
    			)
    		));

    		# No existe usuario
    		if (empty($usuario)) {
    			$response = array(
					'code'    => 404, 
					'message' => 'El email ingresado no existe'
				);

				throw new CakeException($response);
    		}

    		# Contraseña no válida
    		if (AuthComponent::password($clave) != $usuario['Administrador']['clave']) {
    			$response = array(
					'code'    => 403, 
					'message' => 'La contraseña ingresada no es correcta'
				);

				throw new CakeException($response);
    		}

    		# Crear Token
    		$token = ClassRegistry::init('Token')->crear_token($usuario['Administrador']['id'], null, 8760);

    		$this->set(array(
	            'response' => $token,
	            '_serialize' => array('response')
	        ));

    	}else{

    		$response = array(
				'code'    => 501, 
				'message' => 'Only POST request allow'
			);

			throw new CakeException($response);
    	}
	}
	

	/**
	 * Permite generar un token interno mediante la autenticación de google
	 * 
	 * Para utilizar le servicio, se debe enviar el token de google, una vez validado por el sistema
	 * se retorna el token interno.
	 * 
	 * @return mixed
	 */
	public function api_google_auth()
	{
		if ($this->request->is('post')) {

    		$token = $this->request->data['token'];
    		
    		# Que los campos de autenticacion no esten vacios
    		if (empty($token)) {
    			$response = array(
					'code'    => 501, 
					'message' => 'Token es requerido'
				);

				throw new CakeException($response);
			}
			
			# Obtenemos al usuario de google mediante el token
			$this->Firebase = $this->Components->load('Firebase');

			$logeado = $this->Firebase->isLogged($token);

			if (!$logeado['logged'])
			{
				$response = array(
					'code'    => 401, 
					'message' => $logeado['message']
				);

				throw new CakeException($response);
			}
    		
    		# Buscar usuario
    		$usuario = $this->Administrador->find('first', array(
    			'conditions' => array(
    				'Administrador.email' => $logeado['user']['email']
    			)
    		));

    		# No existe usuario
    		if (empty($usuario)) {
    			$response = array(
					'code'    => 404, 
					'message' => 'Usuario no encontrado'
				);

				throw new CakeException($response);
    		}

    		# Crear Token
    		$tokeninterno = ClassRegistry::init('Token')->crear_token($usuario['Administrador']['id'], null, 8760);

			# Se agrega id del usuario
			$logeado['user']['administrador_id'] = $usuario['Administrador']['id'];

    		$this->set(array(
	            'response' => array(
					'token' => $tokeninterno,
					'g_token' => $token,
					'usuario' => $logeado['user']
				),
	            '_serialize' => array('response')
	        ));

    	}else{

    		$response = array(
				'code'    => 501, 
				'message' => 'Only POST request allow'
			);

			throw new CakeException($response);
    	}
	}
	
	
	/**
	 * api_validate_token
	 *
	 * @return void
	 */
	public function api_validate_token()
	{	

		if (!$this->request->is('post')) 
		{
			$response = array(
				'code'    => 400, 
				'message' => 'Only post method'
			);

			throw new CakeException($response);
		}

		$token = '';

    	if (isset($this->request->data['token'])) {
    		$token = $this->request->data['token'];
    	}

    	# Existe token
		if (!isset($token)) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($token)) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		$this->set(array(
            'response' => array(
				'code' => 200,
				'message' => 'Token válido'
			),
            '_serialize' => array('response')
        ));
	}


    /**
     * [api_obtener_usuario description]
     * @return [type] [description]
     */
    public function api_obtener_usuario()
    {	

    	$token = '';

    	if (isset($this->request->query['token'])) {
    		$token = $this->request->query['token'];
    	}

    	# Existe token
		if (!isset($token)) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($token)) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		$tokenData = ClassRegistry::init('Token')->find('first', array(
			'conditions' => array(
				'Token.token' => $token
			),
			'contain' => array(
				'Administrador' => array(
					'Rol' => array(
						'fields' => array(
							'Rol.nombre', 'Rol.app_retiro', 'Rol.app_despacho', 'Rol.app_entrega', 'Rol.app_agencia', 'Rol.app_picking', 'Rol.app_perfil', 'Rol.app_embalajes', 'Rol.bodega_id'
						)
					),
					'fields' => array(
						'Administrador.id', 'Administrador.nombre', 'Administrador.email', 'Administrador.google_imagen'
					)
				)
			),
			'fields' => array('Token.token', 'Token.administrador_id')
		));

		# Validamos usuario
		if (empty($tokenData['Administrador'])) {
			$response = array(
				'code'    => 404, 
				'message' => 'User not found'
			);

			throw new CakeException($response);
		}

		$response = array(
			'Usuario' => array(
				'id' => $tokenData['Administrador']['id'],
				'nombre' => $tokenData['Administrador']['nombre'],
				'email'  => $tokenData['Administrador']['email'],
				'avatar' => (!empty($tokenData['Administrador']['google_imagen'])) ? $tokenData['Administrador']['google_imagen'] : 'https://ui-avatars.com/api/?size=50&background=fff&color=771D97&name=' . urlencode($tokenData['Administrador']['nombre']),
				'bodega_predeterminada' => ($tokenData['Administrador']['Rol']['bodega_id']) ? $tokenData['Administrador']['Rol']['bodega_id'] : null
			)
		);

		if (!empty($tokenData['Administrador']['Rol'])) {

			$permisos = array(
				'Usuario' => array(
					'perfil' => (isset(ClassRegistry::init('Rol')->app[$tokenData['Administrador']['Rol']['app_perfil']])) ? ClassRegistry::init('Rol')->app[$tokenData['Administrador']['Rol']['app_perfil']] : ClassRegistry::init('Rol')->app['general'] 
				),
				'Opciones' => array(
					'retirar_en_tienda' => $tokenData['Administrador']['Rol']['app_retiro'],
					'despachar'         => $tokenData['Administrador']['Rol']['app_despacho'],
					'entrega_domicilio' => $tokenData['Administrador']['Rol']['app_entrega'],
					'entrega_agencia'   => $tokenData['Administrador']['Rol']['app_agencia'],
					'picking'           => $tokenData['Administrador']['Rol']['app_picking']
				),
				'Ambientes' => array()
			);
		
			if ($tokenData['Administrador']['Rol']['app_embalajes'])
			{
				$permisos = array_replace_recursive($permisos, array(
					'Ambientes' => array(
						'embalajes' => true
					)
				));
			}
			
			if ($tokenData['Administrador']['Rol']['app_perfil'])
			{
				$permisos = array_replace_recursive($permisos, array(
					'Ambientes' => array(
						'app_mobile' => true
					)
				));
			}

			$response = array_replace_recursive($response, $permisos);
		}

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response')
        ));
    }


    /**
     * [api_obtener_usuarios_por_perfil description]
     * @return [type] [description]
     */
    public function api_obtener_usuarios_por_perfil()
    {
    	$token = '';
    	$perfil = '';

    	if (isset($this->request->query['token'])) {
    		$token = $this->request->query['token'];
    	}

    	# viene perfil
    	if (isset(ClassRegistry::init('Rol')->app[$this->request->query['profile']])) {
    		$perfil = $this->request->query['profile'];
    	}else{
    		# Existe token
			$response = array(
				'code'    => 502, 
				'message' => 'Expected profile param'
			);

			throw new CakeException($response);
    	}

    	# Existe token
		if (!isset($token)) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($token)) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		$roles = ClassRegistry::init('Rol')->find('all', array(
			'conditions' => array(
				'app_perfil' => $perfil,
				'activo' => 1
			),
			'contain' => array(
				'Administrador' => array(
					'conditions' => array(
						'Administrador.activo' => 1
					),
					'fields' => array(
						'Administrador.id', 'Administrador.nombre', 'Administrador.email'
					)
				)
			),
			'fields' => array(
				'Rol.id'
			)
		));

		$response = array();

		foreach (Hash::extract($roles, '{n}.Administrador.{n}') as $ia => $admin) {
			$response[$admin['email']] = $admin['nombre'];
		}

		$this->set(array(
            'response' => $response,
            '_serialize' => array('response')
        ));

    }
}
