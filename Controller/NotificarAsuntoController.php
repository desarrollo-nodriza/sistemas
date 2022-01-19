<?php

App::uses('AppController', 'Controller');

class NotificarAsuntoController extends AppController
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

		$responsables          = ClassRegistry::init('Notificar')->find('all', []);
		$asuntos               = ClassRegistry::init('Asunto')->find('all', []);
		$NotificarAsuntos = ClassRegistry::init('NotificarAsunto')->find(
			'all',
			[
				'contain' =>
				[
					'Asunto' =>
					[
						'fields' => ['Asunto.nombre']
					],
					'Notificar' =>
					[
						'fields' => [
							'Notificar.nombre',
							'Notificar.correo'
						]
					]
				]
			]
		);
		$responsables_activos = [];
		foreach ($responsables as $value) {
			if ($value['Notificar']['activo']) {
				$responsables_activos[$value['Notificar']['id']] = "{$value['Notificar']['nombre']} - {$value['Notificar']['correo']}";
			}
		}

		$asuntos_activos      = ClassRegistry::init('Asunto')->find('list', ['conditions' => ['Asunto.activo' => true]]);

		$this->set(compact('asuntos', 'responsables', 'NotificarAsuntos', 'responsables_activos', 'asuntos_activos'));
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
					ClassRegistry::init('NotificarAsunto')->create();
					if (ClassRegistry::init('NotificarAsunto')->deleteAll(['NotificarAsunto.asunto_id' => $this->request->data['id']])) {
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

			ClassRegistry::init('NotificarAsunto')->create();
			if (ClassRegistry::init('NotificarAsunto')->deleteAll(['NotificarAsunto.asunto_id' => $this->request->data['id']])) {

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
				$nuevo_responsable[] = ['Notificar' => $value];
			}

			ClassRegistry::init('Notificar')->create();

			if (ClassRegistry::init('Notificar')->saveAll($nuevo_responsable)) {
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

			ClassRegistry::init('Notificar')->create();

			if (ClassRegistry::init('Notificar')->save(['Notificar' => $this->request->data])) {

				$mensaje = 'Registro editado correctamente.';

				if (!$this->request->data['activo']) {
					ClassRegistry::init('NotificarAsunto')->create();
					if (ClassRegistry::init('NotificarAsunto')->deleteAll(['NotificarAsunto.notificar_id' => $this->request->data['id']])) {
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

			ClassRegistry::init('NotificarAsunto')->create();
			if (ClassRegistry::init('NotificarAsunto')->deleteAll(['NotificarAsunto.notificar_id' => $this->request->data['id']])) {

				ClassRegistry::init('Notificar')->create();
				if (ClassRegistry::init('Notificar')->deleteAll(['Notificar.id' => $this->request->data['id']])) {
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
				return (!empty($key['notificar_id'] && !empty($key['asunto_id'])));
			}, ARRAY_FILTER_USE_BOTH);
			$nuevas_relacion = [];
			foreach ($relaciones as  $value) {
				$nuevas_relacion[] = ['NotificarAsunto' => $value];
			}

			ClassRegistry::init('NotificarAsunto')->create();

			if (ClassRegistry::init('NotificarAsunto')->saveAll($nuevas_relacion)) {
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

			ClassRegistry::init('NotificarAsunto')->create();
			if (ClassRegistry::init('NotificarAsunto')->delete(['NotificarAsunto.id' => $this->request->data['id']])) {

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
