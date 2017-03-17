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
					$this->Session->setFlash('Su tienda principal es ' . $tiendaPrincipal['Tienda']['nombre'], null, array(), 'success');
					$this->Session->write('Tienda.id', $tiendaPrincipal['Tienda']['id']);
					$this->Session->write('Tienda.tema', $tiendaPrincipal['Tienda']['tema']);
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
				if ( empty($me->getEmails()) )
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

	public function admin_logout()
	{	
		/**
		*	Elimina la sesión de google
		*/
		$this->Session->delete('Google.token');
		$this->Session->delete('Tienda');
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
}
