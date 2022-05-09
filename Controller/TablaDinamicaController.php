<?php

App::uses('AppController', 'Controller');

class TablaDinamicaController extends AppController
{


	public $helpers = array('Html', 'Form');

	public function filtrar($controlador = '', $accion = '')
	{
		$redirect = array(
			'controller' => $controlador,
			'action' => $accion
		);

		foreach ($this->request->data['Filtro'] as $campo => $valor) {
			if ($valor != '') {
				$redirect[$campo] = str_replace('/', '-', $valor);
			}
		}

		$this->redirect($redirect);
	}

	public function admin_index()
	{
		$filtro = [];

		if ($this->request->is('post')) {
		}

		$this->paginate	 = [
			'recursive'	=> 0,
			'limit' 	=> 20,
			'order' 	=> ['id' => 'DESC']
		];

		$tablasDinamicas	= $this->paginate();

		BreadcrumbComponent::add('Tablas Dinámicas');
		$this->set(compact('tablasDinamicas'));
	}

	public function admin_add()
	{


		if ($this->request->is('post') || $this->request->is('put')) {

			ClassRegistry::init('TablaDinamica')->create();
			if (ClassRegistry::init('TablaDinamica')->saveAll($this->request->data)) {

				$this->Session->setFlash('Registro creado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'edit', ClassRegistry::init('TablaDinamica')->id));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}


		$dependencias 			= ClassRegistry::init('MetodoEnvio')->dependencias();

		BreadcrumbComponent::add('Tabla dinámica', '/tablaDinamica');
		BreadcrumbComponent::add('Crear');

		$this->set(compact('dependencias'));
	}

	public function admin_edit($id = null)
	{

		if (!$this->TablaDinamica->exists($id)) {
			$this->Session->setFlash("No existe cuenta corriente con id {$id}", null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			ClassRegistry::init('TablaDinamica')->create();
			if (ClassRegistry::init('TablaDinamica')->saveAll($this->request->data)) {
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'edit', $id));
			} else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$this->request->data 	= ClassRegistry::init('TablaDinamica')->find('first', [
			'conditions' 	=> ['id' => $id],
			'contain'		=> ['AtributoDinamico', 'CategoriaTablaDinamica']
		]);
		$dependencias 			= ClassRegistry::init('MetodoEnvio')->dependencias();
		$atributos 				= ClassRegistry::init('AtributoDinamico')->find('list', ['conditios' => ['activo' => true]]);
		$categorias				= ClassRegistry::init('CategoriaTablaDinamica')->find('list', []);
		BreadcrumbComponent::add('Tabla dinámica', '/tablaDinamica');
		BreadcrumbComponent::add('Editar');
		// prx($this->request->data);
		$this->set(compact('dependencias', 'atributos', 'categorias'));
	}

	public function admin_atributo_add($id)
	{

		$valor_tabla_dinamica = array_filter($this->request->data, function ($v, $k) {
			return !empty($v['atributo_dinamico_id']) and !empty($v['nombre_referencia']);
		}, ARRAY_FILTER_USE_BOTH);

		$informacion_a_guardar = [];

		foreach ($valor_tabla_dinamica as $value) {
			$informacion_a_guardar[] = ['TablaAtributo' => $value];
		}

		ClassRegistry::init('TablaAtributo')->create();
		ClassRegistry::init('TablaAtributo')->saveAll($informacion_a_guardar);

		$this->redirect(array('action' => 'edit', $id));
	}

	public function admin_delete($id)
	{
		ClassRegistry::init('TablaAtributo')->id = $id;
		if (!ClassRegistry::init('TablaAtributo')->exists()) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
		} else {

			if (ClassRegistry::init('TablaAtributo')->delete(['TablaAtributo.id' => $id])) {
				$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			} else {
				$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		$this->redirect(array('action' => 'edit', $this->request->data['id']));
	}

	public function admin_categoria_delete($id)
	{

		if (ClassRegistry::init('CategoriaTabla')->delete(['CategoriaTabla.id' => $this->request->data['id']])) {
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
		} else {
			$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		}

		$this->redirect(array('action' => 'edit', $id));
	}

	public function admin_categoria_add($id)
	{
		// prx([$id, $this->request->data]);
		$informacion_a_guardar = [];

		foreach ($this->request->data as $value) {
			$informacion_a_guardar[] = ['CategoriaTabla' => $value];
		}

		ClassRegistry::init('CategoriaTabla')->create();
		ClassRegistry::init('CategoriaTabla')->saveAll($informacion_a_guardar);

		$this->redirect(array('action' => 'edit', $id));
	}
}
