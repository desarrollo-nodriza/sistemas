<?php
App::uses('AppController', 'Controller');
class RolesController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
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
			if ( $this->Rol->save($this->request->data) )
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

		$this->set(compact('modulos', 'app_perfiles'));
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
			
			if ( $this->Rol->save($this->request->data) )
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
				'conditions'	=> array('Rol.id' => $id)
			));
		}

		BreadcrumbComponent::add('Roles de usuario ', '/roles');
		BreadcrumbComponent::add('Editar ');
		$modulos	= $this->Rol->Modulo->find('list');
		$app_perfiles = $this->Rol->app;

		$this->set(compact('modulos', 'app_perfiles'));
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
