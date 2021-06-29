<?php
App::uses('AppController', 'Controller');
class TransportesController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);
		$transportes	= $this->paginate();

		BreadcrumbComponent::add('Transportes');

		$this->set(compact('transportes'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Transporte->create();
			if ( $this->Transporte->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		BreadcrumbComponent::add('Transportes', '/transportes');
		BreadcrumbComponent::add('Agregar');
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Transporte->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Transporte->save($this->request->data) )
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
			$this->request->data	= $this->Transporte->find('first', array(
				'conditions'	=> array('Transporte.id' => $id)
			));
		}

		BreadcrumbComponent::add('Transportes', '/transportes');
		BreadcrumbComponent::add('Editar');
	}

	public function admin_delete($id = null)
	{
		$this->Transporte->id = $id;
		if ( ! $this->Transporte->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Transporte->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Transporte->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Transporte->_schema);
		$modelo			= $this->Transporte->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	/**
	 * [obtener_transporte description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_obtener_transporte($id)
	{
		if ( ! $this->Transporte->exists($id) )
		{
			$result = array(
				'code'    => 404,
				'message' => 'Transporte no encontrado',
				'data'    => array()
			);

			echo json_encode($result);
			exit;
		}

		$transporte = $this->Transporte->find('first', array(
			'conditions' => array(
				'Transporte.id' => $id
			)
		));

		$result = array(
			'code'    => 200,
			'message' => 'Transporte obtenido con éxito',
			'data'    => $transporte['Transporte']
		);

		echo json_encode($result);
		exit;
	}


	public function admin_quitar_transporte()
	{	
		$res = array(
			'code' => 500,
			'message' => 'Error inexplicable'
		);

		if ($this->request->is('post')) {
			
			if(ClassRegistry::init('TransportesVenta')->delete($this->request->data['id'])){
				
				ClassRegistry::init('EnvioHistorico')->deleteAll(array('EnvioHistorico.transporte_venta_id' => $this->request->data['id']));

				$res['code'] = 200;
				$res['message'] = 'Registro eliminado con éxito.';
			}else{
				$res['code'] = 501;
				$res['message'] = 'Error al elimnar el registro.';
			}			
		}

		echo json_encode($res);
		exit;
	}



	/**
	 * [api_obtener_transportes description]
	 * @return [type] [description]
	 */
	public function api_obtener_transportes()
	{	
		# Sólo método Get
		if (!$this->request->is('get')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only GET request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		$transportistas = $this->Transporte->find('list', array('conditions' => array('activo' => 1)));

		$this->set(array(
            'response' => $transportistas,
            '_serialize' => array('response')
        ));

	}

}
