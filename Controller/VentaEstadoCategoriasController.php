<?php
App::uses('AppController', 'Controller');
class VentaEstadoCategoriasController extends AppController
{

	public function admin_index () {

		$this->paginate = array(
			'recursive' => 0,
			'sort' => 'VentaEstadoCategoria.nombre',
			'direction' => 'ASC'
		);

		$ventaEstadoCategorias = $this->paginate();

		BreadcrumbComponent::add('Categoria Estados de Ventas');

		$this->set(compact('ventaEstadoCategorias'));

	}

	public function admin_edit($id = null)
	{
		if ( ! $this->VentaEstadoCategoria->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			$this->VentaEstadoCategoria->final_unico($this->request->data);

			if (!$this->VentaEstadoCategoria->aceptado_rechazo($this->request->data)) {
				$this->Session->setFlash('Un estado no puede ser venta y rechazo a la vez.', null, array(), 'danger');
				$this->redirect(array('action' => 'edit', $id));
			}

			if ( $this->VentaEstadoCategoria->save($this->request->data) )
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
			$this->request->data = $this->VentaEstadoCategoria->find(
				'first',
				array(
					'conditions' => array(
						'VentaEstadoCategoria.id' => $id
					)
				)
			);
		}

		$colores = array(
			'primary'  => '<span class="label label-primary">Primario</span>',
			'success'  => '<span class="label label-success">Verde</span>',
			'danger'   => '<span class="label label-danger">Rojo</span>',
			'warning'  => '<span class="label label-warning">Naranjo</span>',
			'amarillo' => '<span class="label label-amarillo">Amarillo</span>',
			'verde'    => '<span class="label label-verde">Verde 2</span>',
			'info'     => '<span class="label label-info">Celeste</span>',
			'lila'     => '<span class="label label-lila">Lila</span>',
			'link'     => '<span class="label label-link">Blanco</span>'
		);

		$plantillas = array();

		$dirPlantillas = APP . 'View' . DS . 'VentaEstados' . DS . 'emails';
		$gestor_dir    = opendir($dirPlantillas);

		while (false !== ($nombre_fichero = readdir($gestor_dir))) {
			if (!in_array($nombre_fichero, array('..', '.'))) {
				$nombre_fichero              = str_replace('admin_', '', $nombre_fichero);
				$nombre_fichero              = str_replace('.ctp', '', $nombre_fichero);
				$plantillas[$nombre_fichero] = $nombre_fichero;	
			}
		}

		BreadcrumbComponent::add('Categoria Estados de Ventas', '/ventaEstadoCategorias');
		BreadcrumbComponent::add('Editar Categoria');

		$this->set(compact('colores', 'plantillas'));
	}


	public function admin_add()
	{
		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			$this->VentaEstadoCategoria->final_unico($this->request->data);

			if (!$this->VentaEstadoCategoria->aceptado_rechazo($this->request->data)) {
				$this->Session->setFlash('Un estado no puede ser venta y rechazo a la vez.', null, array(), 'danger');
				$this->redirect(array('action' => 'add'));
			}

			$this->VentaEstadoCategoria->create();
			if ( $this->VentaEstadoCategoria->save($this->request->data) )
			{
				$this->Session->setFlash('Registro creado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$colores = array(
			'primary'  => '<span class="label label-primary">Primario</span>',
			'success'  => '<span class="label label-success">Verde</span>',
			'danger'   => '<span class="label label-danger">Rojo</span>',
			'warning'  => '<span class="label label-warning">Naranjo</span>',
			'amarillo' => '<span class="label label-amarillo">Amarillo</span>',
			'verde'    => '<span class="label label-verde">Verde 2</span>',
			'info'     => '<span class="label label-info">Celeste</span>',
			'lila'     => '<span class="label label-lila">Lila</span>',
			'link'     => '<span class="label label-link">Blanco</span>'
		);

		$plantillas = array();

		$dirPlantillas = APP . 'View' . DS . 'VentaEstados' . DS . 'emails';
		$gestor_dir    = opendir($dirPlantillas);

		while (false !== ($nombre_fichero = readdir($gestor_dir))) {
			if (!in_array($nombre_fichero, array('..', '.'))) {
				$nombre_fichero              = str_replace('admin_', '', $nombre_fichero);
				$nombre_fichero              = str_replace('.ctp', '', $nombre_fichero);
				$plantillas[$nombre_fichero] = $nombre_fichero;	
			}
		}

		BreadcrumbComponent::add('Categoria Estados de Ventas', '/ventaEstadoCategorias');
		BreadcrumbComponent::add('Agregar Categoria');

		$this->set(compact('colores', 'plantillas'));
	}



	public function admin_activar($id = null) {

		if ( ! $this->VentaEstadoCategoria->exists($id) ){
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['VentaEstadoCategoria']['id'] = $id;
			$this->request->data['VentaEstadoCategoria']['activo'] = 1;

			if ( $this->VentaEstadoCategoria->save($this->request->data) ) {
				$this->Session->setFlash('Registro activado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

	public function admin_desactivar($id = null) {

		if ( ! $this->VentaEstadoCategoria->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['VentaEstadoCategoria']['id'] = $id;
			$this->request->data['VentaEstadoCategoria']['activo'] = 0;

			if ( $this->VentaEstadoCategoria->save($this->request->data) ) {
				$this->Session->setFlash('Registro desactivado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

}