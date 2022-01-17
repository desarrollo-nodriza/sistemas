<?php

App::uses('AppController', 'Controller');

class AsuntosResponsablesController extends AppController
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

		BreadcrumbComponent::add('Asuntos|Responsables');

		$responsables          = ClassRegistry::init('AtencionCliente')->find('all', []);
		$asuntos               = ClassRegistry::init('Asunto')->find('all', []);
		$asuntoAtencionClientes = ClassRegistry::init('AsuntoAtencionCliente')->find(
			'all',
			[
				'contain' =>
				[
					'Asunto' =>
					[
						'fields' => ['Asunto.nombre']
					],
					'AtencionCliente' =>
					[
						'fields' => [
							'AtencionCliente.nombre',
							'AtencionCliente.correo'
						]
					]
				]
			]
		);
		$responsables_activos = [];
		foreach ($responsables as $value) {
			if ($value['AtencionCliente']['activo']) {
				$responsables_activos[$value['AtencionCliente']['id']] = "{$value['AtencionCliente']['nombre']} - {$value['AtencionCliente']['correo']}";
			}
		}

		$asuntos_activos      = ClassRegistry::init('Asunto')->find('list', ['conditions' => ['Asunto.activo' => true]]);

		$this->set(compact('asuntos', 'responsables', 'asuntoAtencionClientes', 'responsables_activos', 'asuntos_activos'));
	}

	public function admin_asuntos_add()
	{

		if ($this->request->is('post')) {

			$asuntos = array_filter($this->request->data, function ($key, $value) {
				return  !empty($key['nombre']);
			}, ARRAY_FILTER_USE_BOTH);
			$nuevo_asuntos = [];
			foreach ($asuntos as  $value) {
				$nuevo_asuntos[] = ['Asunto' => $value];
			}

			ClassRegistry::init('Asunto')->create();

			if (ClassRegistry::init('Asunto')->saveAll($nuevo_asuntos)) {
				$this->Session->setFlash(
					'Registro agregado correctamente.',
					null,
					array(),
					'success'
				);
			} else {
				$this->Session->setFlash(
					'Error al guardar el registro. Por favor intenta nuevamente.',
					null,
					array(),
					'danger'
				);
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_asuntos_edit()
	{

		if ($this->request->is('post')) {

			ClassRegistry::init('Asunto')->create();

			if (ClassRegistry::init('Asunto')->save(['Asunto' => $this->request->data])) {

				$mensaje = 'Registro editado correctamente.';
				if (!$this->request->data['activo']) {
					ClassRegistry::init('AsuntoAtencionCliente')->create();
					if (ClassRegistry::init('AsuntoAtencionCliente')->deleteAll(['AsuntoAtencionCliente.asunto_id' => $this->request->data['id']])) {
						$mensaje = $mensaje . " Se han eliminado las relaciones ya que ha inactivo {$this->request->data['nombre']}";
					}
				}
				$this->Session->setFlash(
					$mensaje,
					null,
					array(),
					'success'
				);
			} else {
				$this->Session->setFlash(
					'Error al guardar el registro. Por favor intenta nuevamente.',
					null,
					array(),
					'danger'
				);
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_asuntos_delete()
	{


		if ($this->request->is('post')) {

			ClassRegistry::init('AsuntoAtencionCliente')->create();
			if (ClassRegistry::init('AsuntoAtencionCliente')->deleteAll(['AsuntoAtencionCliente.asunto_id' => $this->request->data['id']])) {

				ClassRegistry::init('Asunto')->create();
				if (ClassRegistry::init('Asunto')->deleteAll(['Asunto.id' => $this->request->data['id']])) {
					$this->Session->setFlash(
						'Se ha eliminado Asunto y sus respectivas relaciones.',
						null,
						array(),
						'success'
					);
				} else {
					$this->Session->setFlash(
						'Solo se han eliminado las relaciones. Sin embaego no se a podido eliminar el asunto.',
						null,
						array(),
						'danger'
					);
				}
			} else {

				$this->Session->setFlash(
					'No se han podido eliminar las relaciones ni el asunto',
					null,
					array(),
					'danger'
				);
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_responsable_add()
	{


		if ($this->request->is('post')) {

			$responsable = array_filter($this->request->data, function ($key, $value) {
				return (!empty($key['nombre'] && !empty($key['correo'])));
			}, ARRAY_FILTER_USE_BOTH);
			$nuevo_responsable = [];
			foreach ($responsable as  $value) {
				$nuevo_responsable[] = ['AtencionCliente' => $value];
			}

			ClassRegistry::init('AtencionCliente')->create();

			if (ClassRegistry::init('AtencionCliente')->saveAll($nuevo_responsable)) {
				$this->Session->setFlash(
					'Registro agregado correctamente.',
					null,
					array(),
					'success'
				);
			} else {
				$this->Session->setFlash(
					'Error al guardar el responsable. Por favor intenta nuevamente.',
					null,
					array(),
					'danger'
				);
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_responsable_edit()
	{

		if ($this->request->is('post')) {

			ClassRegistry::init('AtencionCliente')->create();

			if (ClassRegistry::init('AtencionCliente')->save(['AtencionCliente' => $this->request->data])) {

				$mensaje = 'Registro editado correctamente.';

				if (!$this->request->data['activo']) {
					ClassRegistry::init('AsuntoAtencionCliente')->create();
					if (ClassRegistry::init('AsuntoAtencionCliente')->deleteAll(['AsuntoAtencionCliente.atencion_cliente_id' => $this->request->data['id']])) {
						$mensaje = $mensaje . " Se han eliminado las relaciones ya que ha inactivo {$this->request->data['nombre']}";
					}
				}
				$this->Session->setFlash(
					$mensaje,
					null,
					array(),
					'success'
				);
			} else {
				$this->Session->setFlash(
					'Error al guardar el registro. Por favor intenta nuevamente.',
					null,
					array(),
					'danger'
				);
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_responsable_delete()
	{


		if ($this->request->is('post')) {

			ClassRegistry::init('AsuntoAtencionCliente')->create();
			if (ClassRegistry::init('AsuntoAtencionCliente')->deleteAll(['AsuntoAtencionCliente.atencion_cliente_id' => $this->request->data['id']])) {

				ClassRegistry::init('AtencionCliente')->create();
				if (ClassRegistry::init('AtencionCliente')->deleteAll(['AtencionCliente.id' => $this->request->data['id']])) {
					$this->Session->setFlash(
						'Se ha eliminado responsable y sus respectivas relaciones.',
						null,
						array(),
						'success'
					);
				} else {
					$this->Session->setFlash(
						'Solo se han eliminado las relaciones. Sin embaego no se a podido eliminar el responsable.',
						null,
						array(),
						'danger'
					);
				}
			} else {

				$this->Session->setFlash(
					'No se han podido eliminar las relaciones ni el responsable',
					null,
					array(),
					'danger'
				);
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_asuntos_responsables_add()
	{


		if ($this->request->is('post')) {

			$relaciones = array_filter($this->request->data, function ($key, $value) {
				return (!empty($key['atencion_cliente_id'] && !empty($key['asunto_id'])));
			}, ARRAY_FILTER_USE_BOTH);
			$nuevas_relacion = [];
			foreach ($relaciones as  $value) {
				$nuevas_relacion[] = ['AsuntoAtencionCliente' => $value];
			}

			ClassRegistry::init('AsuntoAtencionCliente')->create();

			if (ClassRegistry::init('AsuntoAtencionCliente')->saveAll($nuevas_relacion)) {
				$this->Session->setFlash(
					'Registro agregado correctamente.',
					null,
					array(),
					'success'
				);
			} else {
				$this->Session->setFlash(
					'Error al guardar el responsable. Por favor intenta nuevamente.',
					null,
					array(),
					'danger'
				);
			}
		}

		$this->redirect(array('action' => 'index'));
	}

	public function admin_asuntos_responsables_delete()
	{
		if ($this->request->is('post')) {

			ClassRegistry::init('AsuntoAtencionCliente')->create();
			if (ClassRegistry::init('AsuntoAtencionCliente')->delete(['AsuntoAtencionCliente.id' => $this->request->data['id']])) {

				$this->Session->setFlash(
					'Se ha eliminado relacion.',
					null,
					array(),
					'success'
				);
			} else {

				$this->Session->setFlash(
					'No se han podido eliminar la relacion',
					null,
					array(),
					'danger'
				);
			}
		}

		$this->redirect(array('action' => 'index'));
	}
}
