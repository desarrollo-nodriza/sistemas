<?php
App::uses('AppController', 'Controller');
class ProspectosController extends AppController
{

	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0,
			'conditions' => array(
				'tienda_id' => $this->Session->read('Tienda.id')
				)
		);
		$prospectos	= $this->paginate();

		BreadcrumbComponent::add('Prospectos ');
		$this->set(compact('prospectos'));
	}

	public function admin_add()
	{	
		if ( $this->request->is('post') )
		{	
			// Forzamos el id de tienda
			$this->request->data['Prospecto']['tienda_id'] = $this->Session->read('Tienda.id');

			// Configuración de tablas externas
			$this->cambiarConfigDB($this->tiendaConf($this->request->data['Prospecto']['tienda_id']));

			// Se normalizan las direcciones
			if ( ! empty($this->request->data['Cliente'])) {
				$this->request->data['Cliente'] = $this->limpiarDirecciones($this->request->data['Cliente']);
			}
			
			if ( ! empty($this->request->data['Cliente'])) {
				
				// Verificamos si el cliente es nuevo o existente
				if ( ! $this->request->data['Prospecto']['existente'] ) {

					# Se crea un password para el cliente default y la fecha de creación y actualización
					$this->request->data['Cliente'][1]['id_lang'] 			= 1; 					# Idioma español por defeco
					$this->request->data['Cliente'][1]['id_default_group'] 	= 1; 					# Grupo de clientes por defecto
					$this->request->data['Cliente'][1]['passwd'] 			= 'cliente123456'; 		# Contraseña defecto
					$this->request->data['Cliente'][1]['date_add'] 			= date('Y-m-d H:i:s');	# Fecha creación
					$this->request->data['Cliente'][1]['date_upd'] 			= date('Y-m-d H:i:s'); 	# fecha de actualización

					# Cliente nuevo, se crea.
					$this->Cliente = ClassRegistry::init('Cliente');
					
					if( $this->Cliente->saveAll($this->request->data['Cliente'][1]) ) {
						// Agregamos el id del cliente y su dirección
						$clienteNuevo = $this->Cliente->find('first', array(
							'fields' => array('Cliente.id_customer'),
							'order' => array('Cliente.id_customer' => 'DESC'),
							'contain' => array('Clientedireccion')
							));

						// Seteamos los id de cliente y direccion del prospecto
						$this->request->data['Prospecto']['id_customer'] = $clienteNuevo['Cliente']['id_customer'];
						$this->request->data['Prospecto']['id_address'] = $clienteNuevo['Clientedireccion'][0]['id_address'];

					}else{
						$this->Session->setFlash('No se pudo guardar el nuevo cliente.', null, array(), 'danger');
					}

					// Eliminamos a cliente del arreglo para que no se vuelva a actualizar
					unset($this->request->data['Cliente']);

				}

			}
			
			$this->Prospecto->create();
			if ( $this->Prospecto->save($this->request->data) )
			{	
				# Una vez guardado el prospecto se actualiza la información si es que cambio del cliente y la dirección
				# Cliente existente
				if ( $this->request->data['Prospecto']['existente'] && isset($this->request->data['Cliente']) ) {
					$this->Cliente = ClassRegistry::init('Cliente');
					
					if( $this->Cliente->saveAll($this->request->data['Cliente'][1]) ) {
						$this->Session->setFlash('Información del cliente actualizada con éxito.', null, array(), 'success');
					}else{
						prx($this->validationErrors);
						$this->Session->setFlash('Error al actualizar la información del cliente.', null, array(), 'error');
					}
				}

				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

			
		}


		$estadoProspectos	= $this->Prospecto->EstadoProspecto->find('list', array('conditions' => array('EstadoProspecto.activo' => 1)));
		$monedas	= $this->Prospecto->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$origenes	= $this->Prospecto->Origen->find('list', array('conditions' => array('Origen.activo' => 1)));
		$tiendas	= $this->Prospecto->Tienda->find('list', array('conditions' => array('Tienda.activo' => 1)));

		BreadcrumbComponent::add('Prospectos ', '/prospectos');
		BreadcrumbComponent::add('Agregar ');
		$this->set(compact('estadoProspectos', 'monedas', 'origenes', 'tiendas'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Prospecto->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			// Forzamos el id de tienda
			$this->request->data['Prospecto']['tienda_id'] = $this->Session->read('Tienda.id');
			
			if ( $this->Prospecto->save($this->request->data) )
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
			$this->request->data	= $this->Prospecto->find('first', array(
				'conditions'	=> array('Prospecto.id' => $id)
			));
		}
		$estadoProspectos	= $this->Prospecto->EstadoProspecto->find('list', array('conditions' => array('EstadoProspecto.activo' => 1)));
		$monedas	= $this->Prospecto->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$origenes	= $this->Prospecto->Origen->find('list', array('conditions' => array('Origen.activo' => 1)));
		$tiendas	= $this->Prospecto->Tienda->find('list', array('conditions' => array('Tienda.activo' => 1)));
		BreadcrumbComponent::add('Prospectos ', '/prospectos');
		BreadcrumbComponent::add('Editar ');
		$this->set(compact('estadoProspectos', 'monedas', 'origenes', 'tiendas'));
	}

	public function admin_delete($id = null)
	{
		$this->Prospecto->id = $id;
		if ( ! $this->Prospecto->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Prospecto->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Prospecto->find('all', array(
			'recursive'		=> -1
		));

		$campos			= array_keys($this->Prospecto->_schema);
		$modelo			= $this->Prospecto->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	/**
	 * 	Función que retorna los pedidos realizados en una tienda en específico,
	 *  sus estados, monto, fecha, etc. dado un cliente
	 * @param  int 		$tienda  		Identificador de la tienda
	 * @param  int 	$cliente 			Idenfitifcador del cliente
	 * @return string          Retorna un html con la información.
	 */
	public function admin_historial_pedidos($tienda, $cliente) {
		if ( empty($tienda) || empty($cliente) ) {
			throw new Exception('No se permiten campos vacios');
		}

		// Configuración de tablas externas
		$this->cambiarConfigDB($this->tiendaConf($tienda));

		$pedidos = ClassRegistry::init('Orders')->find('all', array(
			'contain' => array(
				'OrdenEstado' => array('Lang')
			),
			'conditions' => array(
				'Orders.id_customer' => $cliente
			),
			'order' => array(
				'Orders.date_add' => 'DESC'
			)
		));
		
		$htmlPedidos = '';
		$totalComprado = 0;

		if ( ! empty($pedidos)) {
			$htmlPedidos  .= '<div class="table-responsive">';
			$htmlPedidos  .= '<table class="table table-striped table-bordered">';
			$htmlPedidos  .= '<th>Referencia</th>';
			$htmlPedidos  .= '<th>Estado</th>';
			$htmlPedidos  .= '<th>Método de pago</th>';
			$htmlPedidos  .= '<th>Monto</th>';
			$htmlPedidos  .= '<th>Fecha</th>';
			foreach ($pedidos as $pedido) {
				$htmlPedidos  .= '<tr>';
				$htmlPedidos  .= sprintf('<td>%s</td>', $pedido['Orders']['reference']);
				$htmlPedidos  .= sprintf('<td><label class="label label-form" style="background-color: %s;">%s</td>', $pedido['OrdenEstado']['color'], $pedido['OrdenEstado']['Lang'][0]['OrdenEstadoIdioma']['name']);
				$htmlPedidos  .= sprintf('<td>%s</td>', $pedido['Orders']['payment']);
				$htmlPedidos  .= sprintf('<td>%s</td>', CakeNumber::currency($pedido['Orders']['total_paid_tax_incl'] , 'CLP'));
				$htmlPedidos  .= sprintf('<td>%s</td>', $pedido['Orders']['date_add']);
				$htmlPedidos  .= '</tr>';
				$totalComprado = $totalComprado + $pedido['Orders']['total_paid_tax_incl'];
			}
			$htmlPedidos  .= '<tfoot>';
			$htmlPedidos  .= '<tr>';
			$htmlPedidos  .= '<td colspan="3">';
			$htmlPedidos  .= '<b>Total comprado:</b>';
			$htmlPedidos  .= '</td>';
			$htmlPedidos  .= '<td colspan="2">';
			$htmlPedidos  .= sprintf('<b>%s pesos</b>', CakeNumber::currency($totalComprado , 'CLP'));
			$htmlPedidos  .= '</td>';
			$htmlPedidos  .= '</tr>';
			$htmlPedidos  .= '</tfoot>';
			$htmlPedidos  .= '</table>';
			$htmlPedidos  .= '</div>';
			echo $htmlPedidos;
			exit;
		}

		echo "<h4>El cliente no registra pedidos.</h4>";
		exit;
	}
}
