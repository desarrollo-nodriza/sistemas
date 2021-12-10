<?php
App::uses('AppController', 'Controller');
class RolesController extends AppController
{	
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0,
			'contain' => array(
				'Bodega'
			)
		);

		BreadcrumbComponent::add('Roles de usuario ');

		$roles	= $this->paginate();
		
		$this->set(compact('roles'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{	

			# Guardamos los permisos en un objeto json
			if (isset($this->request->data['Permisos'])) {

				$permisos = array();

				foreach ($this->request->data['Permisos'] as $key => $value) {
					if (!isset($value['controlador'])) {
						continue;
					}
					$permisos[$value['controlador']] = json_decode($value['json'], true);
				}

				$this->request->data['Rol']['permisos'] = json_encode($permisos, true);
			}

			$this->Rol->create();
			if ( $this->Rol->saveAll($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Roles de usuario ', '/roles');
		BreadcrumbComponent::add('Agregar ');
		$modulos	= $this->Rol->Modulo->find('list');
		$app_perfiles = $this->Rol->app;

		$bodegas = $this->Rol->Bodega->find('list', array('conditions' => array('activo' => 1)));

		$this->set(compact('modulos', 'app_perfiles', 'bodegas'));
	}


	public function admin_edit($id = null)
	{
		if ( ! $this->Rol->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			
			# Guardamos los permisos en un objeto json
			if (isset($this->request->data['Permisos'])) {

				$permisos = array();

				foreach ($this->request->data['Permisos'] as $key => $value) {
					if (!isset($value['controlador'])) {
						continue;
					}
					$permisos[$value['controlador']] = json_decode($value['json'], true);
				}

				$this->request->data['Rol']['permisos'] = json_encode($permisos, true);
			}

			# eliminamos los registros de la bodegas para crear los nuevos
			$this->Rol->BodegasRol->deleteAll(array(
				'rol_id' => $id
			));
			
			if ( $this->Rol->saveAll($this->request->data) )
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
			$this->request->data	= $this->Rol->find('first', array(
				'conditions'	=> array('Rol.id' => $id),
				'contain' => array(
					'Bodega'
				)
			));
		}

		BreadcrumbComponent::add('Roles de usuario ', '/roles');
		BreadcrumbComponent::add('Editar ');
		$modulos	= $this->Rol->Modulo->find('list');
		$app_perfiles = $this->Rol->app;
		$bodegas = $this->Rol->Bodega->find('list', array('conditions' => array('activo' => 1)));

		$this->set(compact('modulos', 'app_perfiles', 'bodegas'));
	}


	public function admin_delete($id = null)
	{
		$this->Rol->id = $id;
		if ( ! $this->Rol->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Rol->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}


	public function admin_clone($id = null)
	{
		$this->Rol->id = $id;
		if ( ! $this->Rol->exists() )
		{
			$this->Session->setFlash('No existe registro.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$rol = $this->Rol->find('first', array(
			'conditions' => array(
				'Rol.id' => $id
			),
			'contain' => array(
				'Bodega',
				'Modulo'
			)
		));

		$nwrol = [
			'Rol' => $rol['Rol']
		];

		# Quitamos campos
		unset($nwrol['Rol']['id']);
		unset($nwrol['Rol']['created']);
		unset($nwrol['Rol']['modified']);
		
		# copiamos bodegas
		foreach ($rol['Bodega'] as $b)
		{
			$nwrol['Bodega'][] =  array(
				'bodega_id' => $b['BodegasRol']['bodega_id'],
				'orden' => $b['BodegasRol']['orden']
			);
		}

		# compiamos modulos
		foreach ($rol['Modulo'] as $m)
		{
			$nwrol['Modulo'][] = array(
				'modulo_id' => $m['ModulosRol']['modulo_id']
			);
		}
		
		# Serializamos los permisos
		$nwrol['Rol']['permisos'] = json_encode($rol['Rol']['permisos'], true);
		
		$this->Rol->create();
		if ($this->Rol->saveAll($nwrol))
		{
			$this->Session->setFlash('Registro clonado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'edit', $this->Rol->id));
		}

		$this->Session->setFlash('Error al clonar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));

	}


	public function admin_exportar()
	{
		$datos			= $this->Rol->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Rol->_schema);
		$modelo			= $this->Rol->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
