<?php
App::uses('AppController', 'Controller');
class ModulosController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);

		BreadcrumbComponent::add('Módulos ');

		$modulos	= $this->paginate();
		$this->set(compact('modulos'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{	
			$this->request->data['Modulo']['url'] = $this->request->data['Modulo']['url_controlador'] . '/' . $this->request->data['Modulo']['url_action'];

			$this->Modulo->create();
			if ( $this->Modulo->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		$parentModulos	= $this->Modulo->ParentModulo->find('list', array('conditions' => array('parent_id' => NULL)));

		BreadcrumbComponent::add('Módulos ', '/modulos');
		BreadcrumbComponent::add('Agregar ');

		$roles	= $this->Modulo->Rol->find('list');
		$this->set(compact('parentModulos', 'roles'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Modulo->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			$this->request->data['Modulo']['url'] = $this->request->data['Modulo']['url_controlador'] . '/' . $this->request->data['Modulo']['url_action'];

			if ( $this->Modulo->save($this->request->data) )
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
			$this->request->data	= $this->Modulo->find('first', array(
				'conditions'	=> array('Modulo.id' => $id),
				'contain'		=> array('Rol')
			));
		}

		BreadcrumbComponent::add('Módulos ', '/modulos');
		BreadcrumbComponent::add('Editar ');

		$parentModulos	= $this->Modulo->ParentModulo->find('list', array('conditions' => array('parent_id' => NULL)));

		$actions = array();

		if (!empty($this->request->data['Modulo']['url_controlador'])) {

			$controlador = ucfirst($this->request->data['Modulo']['url_controlador']) . 'Controller';

			App::import('Controller', ucfirst($this->request->data['Modulo']['url_controlador']));

			$metodos = get_class_methods($controlador);
	
			if (!empty($metodos)) {
				foreach ($metodos as $value) {

					if ( is_int(strpos($value, 'admin_')) ) {
						$actions[str_replace('admin_', '', $value)] = str_replace('admin_', '', $value);
					}
				}
			}	
		}

		$roles	= $this->Modulo->Rol->find('list');
		$this->set(compact('parentModulos', 'roles', 'actions'));
	}

	public function admin_delete($id = null)
	{
		$this->Modulo->id = $id;
		if ( ! $this->Modulo->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Modulo->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Modulo->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Modulo->_schema);
		$modelo			= $this->Modulo->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	public function admin_obtener_metodos($controlador = '')
	{	

		App::import('Controller', ucfirst($controlador));

		$html = '<option value="">Seleccione</option>';	
		$metodos = get_class_methods(ucfirst($controlador) . 'Controller');
		
		if (!empty($metodos)) {
			foreach ($metodos as $value) {

				if ( is_int(strpos($value, 'admin_')) ) {
					$html  .= '<option value="'.str_replace('admin_', '', $value).'">'.str_replace('admin_', '', $value).'</option>';
				}
			}
		}

		echo $html;
		exit;
	}
}
