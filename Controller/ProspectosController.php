<?php
App::uses('AppController', 'Controller');
class ProspectosController extends AppController
{

	public function admin_index()
	{	

		$paginate = array(); 
    	$conditions = array();

		if ( $this->request->is('post') ) {

			

		}

		// Opciones de paginación
		$paginate = array_replace_recursive(array(
			'limit' => 10,
			'fields' => array(),
			'joins' => array(),
			'contain' => array('Tienda', 'EstadoProspecto', 'Moneda', 'VentaCliente'),
			'conditions' => array(
					'Prospecto.tienda_id' => $this->Session->read('Tienda.id')
				),
			'recursive'	=> 0,
			'order' => 'Prospecto.id DESC'
		));

		/**
		* Buscar por
		*/
		if ( !empty($this->request->params['named']['findby']) && empty($this->request->params['named']['f_inicio']) && empty($this->request->params['named']['f_final']) ) {

			
			$paginate		= array_replace_recursive($paginate, array(
				'conditions'	=> array(
					'Prospecto.estado_prospecto_id' => trim($this->request->params['named']['findby']),
					'Prospecto.tienda_id' => $this->Session->read('Tienda.id')
				)
			));
			
		}

		if ( empty($this->request->params['named']['findby']) && ! empty($this->request->params['named']['f_inicio']) && ! empty($this->request->params['named']['f_final']) ) {

			$f_inicio = date('Y-m-d 00:00:00', strtotime($this->request->params['named']['f_inicio']));
			$f_final  = date('Y-m-d 23:59:59', strtotime($this->request->params['named']['f_final']));

			$paginate		= array_replace_recursive($paginate, array(
				'conditions'	=> array(
					'Prospecto.created BETWEEN ? AND ?' => array($f_inicio, $f_final),
					'Prospecto.tienda_id' => $this->Session->read('Tienda.id')
				)
			));
			
		}

		if ( !empty($this->request->params['named']['findby']) && !empty($this->request->params['named']['f_inicio']) && !empty($this->request->params['named']['f_final']) ) {

			$f_inicio = date('Y-m-d 00:00:00', strtotime($this->request->params['named']['f_inicio']));
			$f_final  = date('Y-m-d 23:59:59', strtotime($this->request->params['named']['f_final']));

			$paginate		= array_replace_recursive($paginate, array(
				'conditions'	=> array(
					'Prospecto.estado_prospecto_id' => trim($this->request->params['named']['findby']),
					'Prospecto.created BETWEEN ? AND ?' => array($f_inicio, $f_final),
					'Prospecto.tienda_id' => $this->Session->read('Tienda.id')
				)
			));
			
		}

		$this->paginate = $paginate;


		$prospectos	= $this->paginate();


		$estadoProspectos = $this->Prospecto->EstadoProspecto->find('list');

		BreadcrumbComponent::add('Prospectos ');
		$this->set(compact('prospectos', 'estadoProspectos'));
	}

	public function admin_add()
	{	
		if ( $this->request->is('post') )
		{	
			// Forzamos el id de tienda
			$this->request->data['Prospecto']['tienda_id'] = $this->Session->read('Tienda.id');
			$this->request->data['Prospecto']['estado_prospecto_id'] = 'creado';


			$this->Prospecto->create();
			if ( $this->Prospecto->saveAll($this->request->data) )
			{	
				if( $this->request->data['Prospecto']['cotizacion'] ) {

					# Verificamos que exista la información mínima para pasar a cotización
					if ( empty($this->request->data['Prospecto']['venta_cliente_id']) || empty($this->request->data['Prospecto']['direccion_id']) || empty($this->request->data['Prospecto']['descripcion']) || empty($this->request->data['VentaDetalleProducto'])) {

						# Se pasa a estado esperando información
						$this->Prospecto->saveField('estado_prospecto_id', 'esperando_informacion');

						$this->Session->setFlash('El prospecto fue creado exitósamente, pero no puede pasar a cotización. Necesita agregar al cliente, seleccionar dirección y añadir productos.', null, array(), 'success');
						$this->redirect(array('action' => 'edit', $this->Prospecto->id));
					}else{

						# Se pasa a estado esperando información
						$this->Prospecto->saveField('estado_prospecto_id', 'cotizacion');
						$this->Session->setFlash('El prospecto fue creado exitósamente, puede crear la cotización.', null, array(), 'success');
						$this->redirect(array('controller' => 'cotizaciones', 'action' => 'add', $this->Prospecto->id));
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


		$estadoProspectos = $this->Prospecto->EstadoProspecto->find('list', array('conditions' => array('EstadoProspecto.activo' => 1)));
		$monedas          = $this->Prospecto->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$origenes         = $this->Prospecto->Origen->find('list', array('conditions' => array('Origen.activo' => 1)));
		$transportes      = $this->Prospecto->Transporte->find('list');
		$token            = $this->Auth->user('token.token');
		$tipo_cliente     = ClassRegistry::init('VentaCliente')->obtener_tipo_cliente();
		$comunas 		  = ClassRegistry::init('Comuna')->find('list');

		BreadcrumbComponent::add('Prospectos ', '/prospectos');
		BreadcrumbComponent::add('Agregar ');
		$this->set(compact('estadoProspectos', 'monedas', 'origenes', 'transportes', 'token', 'tipo_cliente', 'comunas'));
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
			$this->request->data['Prospecto']['estado_prospecto_id'] = 'creado';
			
			$this->Prospecto->ProductosProspecto->deleteAll(array('prospecto_id' => $id));

			if ( $this->Prospecto->saveAll($this->request->data) )
			{	

				if( $this->request->data['Prospecto']['cotizacion'] ) {

					# Verificamos que exista la información mínima para pasar a cotización
					if ( empty($this->request->data['Prospecto']['venta_cliente_id']) || empty($this->request->data['Prospecto']['direccion_id']) || empty($this->request->data['Prospecto']['descripcion']) || empty($this->request->data['VentaDetalleProducto'])) {

						# Se pasa a estado esperando información
						$this->Prospecto->saveField('estado_prospecto_id', 'esperando_informacion');

						$this->Session->setFlash('El prospecto fue creado exitósamente, pero no puede pasar a cotización. Necesita agregar al cliente, seleccionar dirección y añadir productos.', null, array(), 'success');
						$this->redirect(array('action' => 'edit', $this->Prospecto->id));
					}else{

						# Se pasa a estado esperando información
						$this->Prospecto->saveField('estado_prospecto_id', 'cotizacion');
						$this->Session->setFlash('El prospecto fue creado exitósamente, puede crear la cotización.', null, array(), 'success');
						$this->redirect(array('controller' => 'cotizaciones', 'action' => 'add', $this->Prospecto->id));
					}

				}

				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		
		$this->request->data	= $this->Prospecto->find('first', array(
			'conditions'	=> array('Prospecto.id' => $id),
			'contain' => array(
				'VentaDetalleProducto'
			)
		));

		$estadoProspectos	= $this->Prospecto->EstadoProspecto->find('list', array('conditions' => array('EstadoProspecto.activo' => 1)));
		$monedas	= $this->Prospecto->Moneda->find('list', array('conditions' => array('Moneda.activo' => 1)));
		$origenes	= $this->Prospecto->Origen->find('list', array('conditions' => array('Origen.activo' => 1)));
		$tiendas	= $this->Prospecto->Tienda->find('list', array('conditions' => array('Tienda.activo' => 1)));
		$transportes = $this->Prospecto->Transporte->find('list');
		$token            = $this->Auth->user('token.token');
		$tipo_cliente     = ClassRegistry::init('VentaCliente')->obtener_tipo_cliente();
		$comunas 		  = ClassRegistry::init('Comuna')->find('list');


		BreadcrumbComponent::add('Prospectos ', '/prospectos');
		BreadcrumbComponent::add('Editar ');
		$this->set(compact('estadoProspectos', 'monedas', 'origenes', 'transportes', 'token', 'tipo_cliente', 'comunas'));
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
