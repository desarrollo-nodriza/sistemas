<?php

App::uses('AppController', 'Controller');
App::uses('VentaDetalleProductosController', 'Controller');

//App::import('Vendor', 'Mercadopago', array('file' => 'Mercadopago/mercadopago.php'));
App::import('Vendor', 'Mercadolibre', array('file' => 'Meli/meli.php'));

App::uses('CakeTime', 'Utility');

require_once __DIR__ . '/../Vendor/SellerCenterSDK/vendor/autoload.php';

use RocketLabs\SellerCenterSdk\Core\Client;
use RocketLabs\SellerCenterSdk\Core\Configuration;
use RocketLabs\SellerCenterSdk\Core\Request\GenericRequest;
use RocketLabs\SellerCenterSdk\Core\Response\ErrorResponse;
use RocketLabs\SellerCenterSdk\Core\Response\SuccessResponseInterface;
use RocketLabs\SellerCenterSdk\Endpoint\Endpoints;

require_once (__DIR__ . '/../Vendor/PSWebServiceLibrary/PSWebServiceLibrary.php');

class VentasController extends AppController {

	//public $Mercadopago;
	public static $Mercadolibre;
	public $shell = false;


	/**
     * Crea un redirect y agrega a la URL los parámetros del filtro
     * @param 		$controlador 	String 		Nombre del controlador donde redirijirá la petición
     * @param 		$accion 		String 		Nombre del método receptor de la petición
     * @return 		void
     */
    public function filtrar($controlador = '', $accion = '')
    {
    	$redirect = array(
    		'controller' => $controlador,
    		'action' => $accion
    		);

		foreach ($this->request->data['Venta'] as $campo => $valor) {
			if (!empty($valor)) {
				$redirect[$campo] = str_replace('/', '-', $valor);
			}
		}

    	$this->redirect($redirect);

    }

	public function admin_index () {

		$condiciones = array();
		$joins = array();

		$FiltroVenta                = '';
		$FiltroCliente              = '';
		$FiltroTienda               = '';
		$FiltroMarketplace          = '';
		$FiltroMedioPago            = '';
		$FiltroVentaEstadoCategoria = '';
		$FiltroAtendida             = '';
		$FiltroFechaDesde           = '';
		$FiltroFechaHasta          = '';

		// Filtrado de ordenes por formulario
		if ( $this->request->is('post') ) {

			$this->filtrar('ventas', 'index');

		}


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'filtroventa':
						$FiltroVenta = trim($valor);

						if ($FiltroVenta != "") {

							$condiciones["OR"] = array(
								"Venta.id LIKE '%" .$FiltroVenta. "%'",
								"Venta.id_externo LIKE '%" .$FiltroVenta. "%'",
								"Venta.referencia LIKE '%" .$FiltroVenta. "%'"
							);
							
						}
						break;
					case 'filtrocliente':
						$FiltroCliente = trim($valor);

						if ($FiltroCliente != "") {

							$joins[] = array(
								'table' => 'rp_venta_clientes',
								'alias' => 'clientes',
								'type' => 'INNER',
								'conditions' => array(
									'clientes.id = Venta.venta_cliente_id',
									'OR' => array(
										"clientes.nombre LIKE '%" .$FiltroCliente. "%'",
										"clientes.apellido LIKE '%" .$FiltroCliente. "%'",
										"clientes.rut LIKE '%" .$FiltroCliente. "%'",
										"clientes.email LIKE '%" .$FiltroCliente. "%'",
										"clientes.telefono LIKE '%" .$FiltroCliente. "%'"
									)
								)
							);
							
						}
						break;
					case 'tienda_id':
						$FiltroTienda = $valor;

						if ($FiltroTienda != "") {
							$condiciones['Venta.tienda_id'] = $FiltroTienda;
						} 
						break;
					case 'marketplace_id':
						$FiltroMarketplace = $valor;

						if ($FiltroMarketplace != "") {
							$condiciones['Venta.marketplace_id'] = $FiltroMarketplace;
						} 
						break;
					case 'medio_pago_id':
						$FiltroMedioPago = $valor;

						if ($FiltroMedioPago != "") {
							$condiciones['Venta.medio_pago_id'] = $FiltroMedioPago;
						} 
						break;
					case 'venta_estado_categoria_id':
						$FiltroVentaEstadoCategoria = $valor;

						if ($FiltroVentaEstadoCategoria != "") {

							$joins[] = array(
								'table' => 'rp_venta_estados',
								'alias' => 'ventas_estados',
								'type' => 'INNER',
								'conditions' => array(
									'ventas_estados.id = Venta.venta_estado_id',
									"ventas_estados.venta_estado_categoria_id = " .$FiltroVentaEstadoCategoria
								)
							);

						}
						break;
					case 'atendida':
						$FiltroAtendida = $valor;

						if ($FiltroAtendida != "") {
							$condiciones['Venta.atendida'] = $FiltroAtendida;
						} 
						break;
					case 'FechaDesde':
						$FiltroFechaDesde = trim($valor);

						if ($FiltroFechaDesde != "") {

							$ArrayFecha = explode("-", $FiltroFechaDesde);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 00:00:00"));

							$condiciones["Venta.fecha_venta >="] = $Fecha;

						}
						break;
					case 'FechaHasta':
						$FiltroFechaHasta = trim($valor);

						if ($FiltroFechaHasta != "") {

							$ArrayFecha = explode("-", $FiltroFechaHasta);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 23:59:59"));

							$condiciones["Venta.fecha_venta <="] = $Fecha;

						} 
						break;
				}
			}
		}


		# Es bodega
		try {
			$permisos = $this->hasPermission();
		} catch (Exception $e) {
			$permisos = $e;
		}

		if (isset($permisos['storage'])) {

			if ($permisos['storage']) {

				$joins[] = array(
					'table' => 'rp_venta_estados',
					'alias' => 'ventas_estados',
					'type' => 'INNER',
					'conditions' => array(
						'ventas_estados.id = Venta.venta_estado_id',
						"ventas_estados.permitir_retiro_oc"  => 1
					)
				);

				$condiciones["Venta.subestado_oc !="] = 'entregado';	
			}
		}


		$paginate = array(
			'recursive' => 0,
			'contain' => array(
				'VentaEstado' => array(
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
						)
					),
					'fields' => array(
						'VentaEstado.id', 'VentaEstado.nombre', 'VentaEstado.venta_estado_categoria_id', 'VentaEstado.permitir_dte', 'VentaEstado.permitir_retiro_oc'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.id', 'Tienda.nombre'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.id', 'Marketplace.nombre'
					)
				),
				'MedioPago' => array(
					'fields' => array(
						'MedioPago.id', 'MedioPago.nombre'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono',
					)
				),
				'VentaTransaccion' => array(
					'fields' => array(
						'VentaTransaccion.nombre', 'VentaTransaccion.monto'
					)
				),
				'Dte' => array(
					'fields' => array(
						'Dte.id', 'Dte.estado'
					)
				)
			),
			'conditions' => $condiciones,
			'joins' => $joins,
			'fields' => array(
				'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo',
				'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id'
			),
			'sort' => 'Venta.fecha_venta',
			'direction' => 'DESC',
			'limit' => 10
		);

		//----------------------------------------------------------------------------------------------------
		$this->paginate = $paginate;

		$ventas = $this->paginate();

		//----------------------------------------------------------------------------------------------------
		$tiendas = $this->Venta->Tienda->find(
			'list',
			array(
				'conditions' => array(
					'Tienda.activo' => 1
				),
				'order' => 'Tienda.nombre ASC'
			)
		);

		//----------------------------------------------------------------------------------------------------
		$marketplaces = $this->Venta->Marketplace->find(
			'list',
			array(
				'conditions' => array(
					'Marketplace.activo' => 1
				),
				'order' => 'Marketplace.nombre ASC'
			)
		);

		//----------------------------------------------------------------------------------------------------
		$ventaEstadoCategorias = $this->Venta->VentaEstado->VentaEstadoCategoria->find('list');

		//----------------------------------------------------------------------------------------------------
		$medioPagos = $this->Venta->MedioPago->find(
			'list',
			array(
				'conditions' => array(
					'MedioPago.activo' => 1
				),
				'order' => 'MedioPago.nombre ASC'
			)
		);

		# Mercadolibre conectar
		$meliConexion = $this->admin_verificar_conexion_meli();

		BreadcrumbComponent::add('Ventas', '/ventas');

		$this->set(compact(
			'ventas', 'tiendas', 'marketplaces', 'ventaEstadoCategorias', 'medioPagos',
			'FiltroVenta', 'FiltroCliente', 'FiltroTienda', 'FiltroMarketplace', 'FiltroMedioPago', 'FiltroVentaEstadoCategoria', 'FiltroAtendida', 'FiltroFechaDesde', 'FiltroFechaHasta', 'meliConexion'
		));

	}

	public function admin_activar($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['activo'] = 1;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Registro activado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

	public function admin_desactivar($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['activo'] = 0;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Registro desactivado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

	public function admin_marcar_atendida($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['atendida'] = 1;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Venta marcada como Atendida', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

	public function admin_marcar_no_atendida($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['atendida'] = 0;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Venta marcada como No Atendida', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}

	/****************************************************************************************************/
	//obtiene las tiendas (prestashop) que deben ser actualizadas
	private function obtener_tiendas () {

		return $this->Venta->Tienda->find(
			'all',
			array(
				'conditions' => array(
					'Tienda.activo' => 1,
					'Tienda.actualizacion_automatica_ventas' => 1
				),
				'contain' => array(
					'Marketplace' => array(
						'conditions' => array(
							'Marketplace.activo' => 1
						),
						'fields' => array(
							'Marketplace.id', 'Marketplace.tienda_id', 'Marketplace.nombre', 'Marketplace.fee', 'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.marketplace_tipo_id', 'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token', 'Marketplace.seller_id'
						),
						'MarketplaceTipo' => array(
							'conditions' => array(
								'MarketplaceTipo.activo' => 1
							),
							'fields' => array(
								'MarketplaceTipo.nombre'
							)
						)
					)
				),
				'fields' => array(
					'Tienda.id', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.configuracion'
				)
			)
		);

	}

	private function prestashop_obtener_ventas_antiguo($params = array(), $tienda = array())
	{
		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden'), $tienda);

		$ordenes	= ClassRegistry::init('Orden')->find('all', $params);

		$result = array();

		if (!empty($ordenes)) {

			foreach ($ordenes as $key => $value) {
				$result['order'][$key]['id']                       = $value['Orden']['id_order'];
				$result['order'][$key]['id_address_delivery']      = $value['Orden']['id_address_delivery'];
				$result['order'][$key]['id_customer']              = $value['Orden']['id_customer'];
				$result['order'][$key]['current_state']            = $value['Orden']['current_state'];
				$result['order'][$key]['date_add']                 = $value['Orden']['date_add'];
				$result['order'][$key]['payment']                  = $value['Orden']['payment'];
				$result['order'][$key]['total_discounts_tax_incl'] = $value['Orden']['total_discounts_tax_incl'];
				$result['order'][$key]['total_paid']               = $value['Orden']['total_paid'];
				$result['order'][$key]['total_products']           = $value['Orden']['total_products'];
				$result['order'][$key]['total_shipping_tax_incl']  = $value['Orden']['total_shipping_tax_incl'];
				$result['order'][$key]['reference']                = $value['Orden']['reference'];
			}
		}

		return $result;

	}

	/****************************************************************************************************/
	//obtiene las órdenes de una tienda
	private function prestashop_obtener_ventas ($tienda, $ConexionPrestashop) {

		$opt = array();
		$opt['resource'] = 'orders';
		$opt['display'] = '[id,id_customer,current_state,date_add,payment,total_discounts_tax_incl,total_paid,total_products,total_shipping_tax_incl,reference,id_address_delivery]';

		//se obtiene la última venta registrada para consultar solo las nuevas a prestashop
		$venta = $this->Venta->find(
			'first',
			array(
				'conditions' => array(
					'Venta.tienda_id' => $tienda['Tienda']['id'],
					'Venta.marketplace_id' => null
				),
				'fields' => array(
					'Venta.id_externo'
				),
				'order' => 'Venta.id_externo DESC'
			)
		);
		
		//$venta['Venta']['id_externo'] = 8300;

		if (!empty($venta)) {
			$opt['filter[id]'] = '>[' .$venta['Venta']['id_externo']. ']';
		}

		# Se descartan las ventas que no tienen cliente
		#$opt['filter[id_customer]'] = '>[0]';

		$xml = $ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$json = json_encode($PrestashopResources);
		$DataVentas = json_decode($json, true);
		
		if (empty($DataVentas) && !empty($venta)) {

			$opt = array(
				'conditions'	=> array('Orden.id_order >' => $venta['Venta']['id_externo']),
				'fields' => array(
					'Orden.id_order',
					'Orden.id_address_delivery',
					'Orden.id_customer',
					'Orden.current_state',
					'Orden.date_add',
					'Orden.payment',
					'Orden.total_discounts_tax_incl',
					'Orden.total_paid',
					'Orden.total_products',
					'Orden.total_shipping_tax_incl',
					'Orden.reference'
				)
			);

			$DataVentas = $this->prestashop_obtener_ventas_antiguo($opt, $tienda);
		}
	
		return $DataVentas;

	}

	/**
	 * Obtiene la dirección de entrega de una venta
	 * @param  [type] $id                 id_address
	 * @param  [type] $ConexionPrestashop [description]
	 * @return [type]                     [description]
	 */
	private function prestashop_obtener_venta_direccion($id, $ConexionPrestashop)
	{
		$opt = array();
		$opt['resource'] = 'addresses';
		$opt['display'] = '[id,firstname,lastname,address1,address2,city,other, phone, phone_mobile]';
		$opt['filter[id]'] = '[' .$id. ']';
		$xml = $ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$json = json_encode($PrestashopResources);
		$Address = json_decode($json, true);

		return $Address;
	}

	/****************************************************************************************************/
	//obtiene el detalle de una venta
	private function prestashop_obtener_venta_detalles ($venta_id, $ConexionPrestashop) {

		$opt = array();
		$opt['resource'] = 'order_details';
		$opt['display'] = '[product_id,product_name,product_quantity,unit_price_tax_excl]';
		$opt['filter[id_order]'] = '[' .$venta_id. ']';

		$xml = $ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$json = json_encode($PrestashopResources);
		$DataVentaDetalle = json_decode($json, true);

		return $DataVentaDetalle;

	}

	/****************************************************************************************************/
	//obtiene las transacciones de una venta
	private function prestashop_obtener_venta_transacciones ($referencia, $ConexionPrestashop) {

		$opt = array();
		$opt['resource'] = 'order_payments';
		$opt['display'] = '[transaction_id,amount]';
		$opt['filter[order_reference]'] = '[' .$referencia. ']';

		$xml = $ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$json = json_encode($PrestashopResources);
		$DataVentaDetalle = json_decode($json, true);

		return $DataVentaDetalle;

	}

	/****************************************************************************************************/
	//obtiene el estado de venta
	private function prestashop_obtener_venta_estado ($estado_id, $ConexionPrestashop) {

		$opt = array();
		$opt['resource'] = 'order_states';
		$opt['display'] = '[name]';
		$opt['filter[id]'] = '[' .$estado_id. ']';

		$xml = $ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$json = json_encode($PrestashopResources);
		$estado = json_decode($json, true);
		if (!isset($estado['order_state'])) {
			return 1; // Sin estado
		}
		
		$VentaEstado = $this->Venta->VentaEstado->find(
			'first',
			array(
				'conditions' => array(
					'VentaEstado.nombre' => $estado['order_state']['name']['language']
				),
				'fields' => array(
					'VentaEstado.id'
				)
			)
		);

		if (!empty($VentaEstado)) {
			return $VentaEstado['VentaEstado']['id'];
		}

		//si el estado de venta no existe, se crea
		$data = array();
		$data['VentaEstado']['nombre'] = $estado['order_state']['name']['language'];

		$this->Venta->VentaEstado->create();
		$this->Venta->VentaEstado->save($data);

		return $this->Venta->VentaEstado->id;

	}

	/****************************************************************************************************/
	//obtiene el id de un medio de pago
	private function prestashop_obtener_medio_pago ($medio_pago) {

		$MedioPago = $this->Venta->MedioPago->find(
			'first',
			array(
				'conditions' => array(
					'MedioPago.nombre' => $medio_pago
				),
				'fields' => array(
					'MedioPago.id'
				)
			)
		);

		if (!empty($MedioPago)) {
			return $MedioPago['MedioPago']['id'];
		}

		//si el medio de pago no existe, se crea
		$data = array();
		$data['MedioPago']['nombre'] = $medio_pago;

		$this->Venta->MedioPago->create();
		$this->Venta->MedioPago->save($data);

		return $this->Venta->MedioPago->id;

	}

	/****************************************************************************************************/
	//guarda un producto si no existe
	private function prestashop_guardar_producto ($DetalleVenta, $excluir = array()) {

		$producto = $this->Venta->VentaDetalle->VentaDetalleProducto->find(
			'first',
			array(
				'conditions' => array(
					'VentaDetalleProducto.id_externo' => $DetalleVenta['product_id']
				),
				'fields' => array(
					'VentaDetalleProducto.id',
					'VentaDetalleProducto.cantidad_virtual'
				)
			)
		);

		$nuevaCantidad = 0;	

		if (empty($producto)) {

			$data = array();
			$data['VentaDetalleProducto']['id']         			= $DetalleVenta['product_id'];
			$data['VentaDetalleProducto']['id_externo'] 			= $DetalleVenta['product_id'];
			$data['VentaDetalleProducto']['nombre']     			= $DetalleVenta['product_name'];
			$data['VentaDetalleProducto']['cantidad_virtual']     	= 10 + $DetalleVenta['product_quantity'];

			$this->Venta->VentaDetalle->VentaDetalleProducto->create();
			$this->Venta->VentaDetalle->VentaDetalleProducto->save($data);
			
			$nuevaCantidad = $data['VentaDetalleProducto']['cantidad_virtual'] - $DetalleVenta['product_quantity'];

		}else{

			if ($producto['VentaDetalleProducto']['cantidad_virtual'] > $DetalleVenta['product_quantity']) {
				$nuevaCantidad = $producto['VentaDetalleProducto']['cantidad_virtual'] - $DetalleVenta['product_quantity'];
			}
		
		}
		
		# Descontar stock virtual y refrescar canales
		$productosController = new VentaDetalleProductosController();

		//$productosController->descontar_stock_virtual($DetalleVenta['product_id'], $DetalleVenta['product_id'], $nuevaCantidad, $excluir);

	}

	/****************************************************************************************************/
	//obtiene el estado de venta
	private function prestashop_obtener_cliente ($cliente_id, $ConexionPrestashop) {

		$opt = array();
		$opt['resource'] = 'customers';
		$opt['display'] = '[firstname,lastname,email]';
		$opt['filter[id]'] = '[' .$cliente_id. ']';

		$xml = $ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$json = json_encode($PrestashopResources);
		$cliente = json_decode($json, true);

		$VentaCliente = $this->Venta->VentaCliente->find(
			'first',
			array(
				'conditions' => array(
					'VentaCliente.email' => $cliente['customer']['email']
				),
				'fields' => array(
					'VentaCliente.id'
				)
			)
		);

		if (!empty($VentaCliente)) {
			return $VentaCliente['VentaCliente']['id'];
		}

		//si el cliente no existe, se crea
		$data = array();
		$data['VentaCliente']['nombre'] = $cliente['customer']['firstname'];
		$data['VentaCliente']['apellido'] = $cliente['customer']['lastname'];
		$data['VentaCliente']['email'] = $cliente['customer']['email'];

		$this->Venta->VentaCliente->create();
		$this->Venta->VentaCliente->save($data);

		return $this->Venta->VentaCliente->id;

	}



	/**
	 * Obtiene los mensajes de un pedido dese prestashop
	 * @param  intancia $ConexionPrestashop Instancia del recurso Prestashop
	 * @param  int 		$venta_id_externo  	id venta   
	 * @return array()
	 */
	public static function prestashop_obtener_venta_mensajes($ConexionPrestashop, $venta_id_externo)
	{
		$res = array();

		$opt = array();
		$opt['resource'] = 'customer_threads';
		$opt['display'] = '[id]';
		$opt['filter[id_order]'] = '[' .$venta_id_externo. ']';

		$xml = $ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		if (!empty($PrestashopResources)) {

			$json = json_encode($PrestashopResources);
			$CustomerThread = json_decode($json, true);

			$opt = array();
			$opt['resource'] = 'customer_messages';
			$opt['display'] = '[message,date_add]';
			$opt['filter[id_customer_thread]'] = '[' .$CustomerThread['customer_thread']['id']. ']';

			$xml = $ConexionPrestashop->get($opt);

			$PrestashopResources = $xml->children()->children();

			$json = json_encode($PrestashopResources);
			$mensajes = json_decode($json, true);

			if (!isset($mensajes['customer_message'][0])) {
				$mensajes = array(
					'customer_message' => array(
						'0' => $mensajes['customer_message']
					)
				);
			}

			foreach ($mensajes['customer_message'] as $mensaje) {

				$data = array();
				$data['mensaje'] = $mensaje['message'];
				$data['fecha'] = date_format(date_create($mensaje['date_add']), 'd/m/Y H:i:s');;
				$data['asunto'] = '';

				$res[] = $data;

			}

		}

		return $res;
	}


	/**
	 * Méodo utilizado por los marketplace que tengan como
	 * identificadro externo a la referencia del producto
	 * @param  string $referencia [description]
	 * @return [type]             [description]
	 */
	private function prestashop_obtener_idproducto($referencia = '', $ConexionPrestashop)
	{
		$opt = array();
		$opt['resource'] = 'products';
		$opt['display'] = '[id]';
		$opt['filter[reference]'] = '[' .$referencia. ']';

		$xml = $ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$json = json_encode($PrestashopResources);
		$producto = json_decode($json, true);

		return $producto;
	}

	/****************************************************************************************************/
	//obtiene las órdenes de una marketplace de linio
	private function linio_obtener_ventas ($marketplace, $ConexionLinio, &$finalizarLinio) {

		//se obtiene la última venta registrada para consultar solo las nuevas a linio
		$venta = $this->Venta->find(
			'first',
			array(
				'conditions' => array(
					'Venta.marketplace_id' => $marketplace['id']
				),
				'fields' => array(
					'Venta.fecha_venta'
				),
				'order' => 'Venta.fecha_venta DESC'
			)
		);

		//$venta['Venta']['fecha_venta'] = "2018-07-01 00:00:00";

		if (!empty($venta)) { //ventas a partir de la última registrada

			$fecha = date_create($venta['Venta']['fecha_venta']);

			date_modify($fecha, '+1 second');

			$fecha = date_format($fecha, 'c');

			$response = $ConexionLinio->call(
			    (new GenericRequest(
			        Client::GET,
			        'GetOrders',
			        GenericRequest::V1,
			        ['CreatedAfter' => $fecha, 'Limit' => 10]
			    ))
			);

		}

		else { //todas las ventas

			$response = $ConexionLinio->call(
			    (new GenericRequest(
			        Client::GET,
			        'GetOrders',
			        GenericRequest::V1,
			        ['Limit' => 10]
			    ))
			);

		}

		$ResponseVentas = $response->getBody()['Orders'];

		if (empty($ResponseVentas)) {
			$finalizarLinio = true;
			return $ResponseVentas;
		}

		return $ResponseVentas['Order'];

	}

	/****************************************************************************************************/
	//obtiene el detalle de una orden de Linio
	private function linio_obtener_venta_detalles ($venta_id, $ConexionLinio) {

		$response = $ConexionLinio->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetOrderItems',
		        GenericRequest::V1,
		        ['OrderId' => $venta_id]
		    ))
		);

		return $response->getBody()['OrderItems']['OrderItem'];

	}

	/****************************************************************************************************/
	//guarda un producto si no existe
	private function linio_guardar_producto ($DetalleVenta, $excluir = array()) {

		$producto = $this->Venta->VentaDetalle->VentaDetalleProducto->find(
			'first',
			array(
				'conditions' => array(
					'VentaDetalleProducto.id_externo' => $DetalleVenta['Sku']
				),
				'fields' => array(
					'VentaDetalleProducto.id',
					'VentaDetalleProducto.cantidad_virtual'
				)
			)
		);

		$nuevaCantidad = 0;
		
		if (empty($producto)) {

			$data = array();
			$data['VentaDetalleProducto']['id']         = $DetalleVenta['Sku'];
			$data['VentaDetalleProducto']['id_externo'] = $DetalleVenta['Sku'];
			$data['VentaDetalleProducto']['nombre']     = $DetalleVenta['Name'];
			$data['VentaDetalleProducto']['cantidad_virtual']     	= 11;

			$this->Venta->VentaDetalle->VentaDetalleProducto->create();
			$this->Venta->VentaDetalle->VentaDetalleProducto->save($data);

			$nuevaCantidad = $data['VentaDetalleProducto']['cantidad_virtual'] - 1; # Linio separa los items de una compra aunque su cantidad sea mayor a 1

			
		}else{

			if ($producto['VentaDetalleProducto']['cantidad_virtual'] > 1) {
				$nuevaCantidad = $producto['VentaDetalleProducto']['cantidad_virtual'] - 1;
			}

		}

		# Descontar stock virtual y refrescar canales
		$productosController = new VentaDetalleProductosController();

		//$productosController->descontar_stock_virtual($DetalleVenta['Sku'], $DetalleVenta['Sku'], $nuevaCantidad, $excluir);

		return $DetalleVenta['Sku'];

	}

	/****************************************************************************************************/
	//obtiene el estado de venta
	private function linio_obtener_venta_estado ($estado) {

		$VentaEstado = $this->Venta->VentaEstado->find(
			'first',
			array(
				'conditions' => array(
					'VentaEstado.nombre' => $estado
				),
				'fields' => array(
					'VentaEstado.id'
				)
			)
		);

		if (!empty($VentaEstado)) {
			return $VentaEstado['VentaEstado']['id'];
		}

		//si el estado de venta no existe, se crea
		$data = array();
		$data['VentaEstado']['nombre'] = $estado;

		$this->Venta->VentaEstado->create();
		$this->Venta->VentaEstado->save($data);

		return $this->Venta->VentaEstado->id;

	}

	/****************************************************************************************************/
	//obtiene el id de un medio de pago
	private function linio_obtener_medio_pago ($medio_pago) {

		$MedioPago = $this->Venta->MedioPago->find(
			'first',
			array(
				'conditions' => array(
					'MedioPago.nombre' => $medio_pago
				),
				'fields' => array(
					'MedioPago.id'
				)
			)
		);

		if (!empty($MedioPago)) {
			return $MedioPago['MedioPago']['id'];
		}

		//si el medio de pago no existe, se crea
		$data = array();
		$data['MedioPago']['nombre'] = $medio_pago;

		$this->Venta->MedioPago->create();
		$this->Venta->MedioPago->save($data);

		return $this->Venta->MedioPago->id;

	}

	/****************************************************************************************************/
	//obtiene el estado de venta
	private function linio_obtener_cliente ($DataVenta) {

		$rut = str_replace(".", "", $DataVenta['NationalRegistrationNumber']);

		$VentaCliente = $this->Venta->VentaCliente->find(
			'first',
			array(
				'conditions' => array(
					'VentaCliente.rut' => $rut
				),
				'fields' => array(
					'VentaCliente.id'
				)
			)
		);

		if (!empty($VentaCliente)) {
			return $VentaCliente['VentaCliente']['id'];
		}

		//si el cliente no existe, se crea
		$data = array();
		$data['VentaCliente']['nombre'] = $DataVenta['CustomerFirstName'];
		$data['VentaCliente']['apellido'] = $DataVenta['CustomerLastName'];
		$data['VentaCliente']['rut'] = $rut;

		$this->Venta->VentaCliente->create();
		$this->Venta->VentaCliente->save($data);

		return $this->Venta->VentaCliente->id;

	}

	/****************************************************************************************************/
	//obtiene los comentarios de una venta de linio
	private function linio_obtener_venta_mensajes ($venta_id, $ConexionLinio) {

		$response = $ConexionLinio->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetOrderComments',
		        GenericRequest::V1,
		        ['OrderId' => $venta_id]
		    ))
		);

		return $response->getBody();

	}

	/****************************************************************************************************/
	//sincroniza el stock de productos de una tienda y los marketplaces asociados
	// 1. se buscan los productos en prestashop para ver su stock
	// 2. se restan las cantidades vendidas al stock tomado de prestashop para actualizarlo.
	// 3. el nuevo stock se envía a la tienda y marketplaces asociados
	private function sincronizar_stock_productos ($tienda, $ArrayProductosSincronizacion, $ArrayCantidadesVendidos, $ConexionPrestashop) {

		//para actualizar los productos a cada marketplace Linio si existen
		//esto debido a que Linio va reuniendo los productos y luego actualiza en una sola llamada
		$ArrayLinios = array();

		//----------------------------------------------------------------------------------------------------
		//ciclo para recorrer los productos que estaban en las ventas
		foreach ($ArrayProductosSincronizacion as $producto_key => $producto_id) {

			//se obtiene el stock de prestashop
			$opt = array();
			$opt['resource'] = 'stock_availables';
			$opt['display'] = '[quantity]';
			$opt['filter[id_product]'] = '[' .$producto_id. ']';

			$xml = $ConexionPrestashop->get($opt);

			$PrestashopResources = $xml->children()->children();

			//para cambiar el objeto xml a un array
			$json = json_encode($PrestashopResources);
			$stock = json_decode($json, true);

			$NuevoStock = $stock['stock_available']['quantity'];

			//se resta la cantidad vendida en marketplaces para actualizar el stock
			//si el producto no existe en el array de vendidos (si no se vendió en los marketplaces) no es necesario actualizar el stock de prestashop
			if (!empty($ArrayCantidadesVendidos[$producto_key])) {

				$NuevoStock = $NuevoStock - $ArrayCantidadesVendidos[$producto_key];

				//se actualiza el stock en prestashop
				$resources->stock_available->quantity = $NuevoStock;

				$opt = array('resource' => 'stock_availables');
				$opt['putXml'] = $xml->asXML();
				$opt['id'] = $producto_id;
				$xml = $ConexionPrestashop->edit($opt);

			} //fin resta de cantidad vendida en marketplaces

			//----------------------------------------------------------------------------------------------------
			//si la tienda tiene marketplaces asociados para actualizar
			if (!empty($tienda['Marketplace'])) {

				//recorrido de marketplaces
				foreach ($tienda['Marketplace'] as $marketplace_key => $marketplace) {

					//----------------------------------------------------------------------------------------------------
					//si el marketplace es Linio
					if ($marketplace['marketplace_tipo_id'] == 1) {

						//si no existe la posición del marketplace para ir reuniendo los productos, se agrega
						if (!isset($ArrayLinios[$marketplace_key])) {
							$ArrayLinios[$marketplace_key] = Endpoints::product()->productUpdate();
						}

						$ArrayLinios[$marketplace_key]->updateProduct($producto_id)->setQuantity($NuevoStock);

					} //fin si el marketplace es Linio

					//----------------------------------------------------------------------------------------------------
					//si el marketplace es Mercado Libre

				} //fin recorrido de los marketplaces

			} //fin si la tienda tiene marketplaces asociados

		} //fin ciclo de productos que estaban en las ventas

		//si existen productos para actualizar en cuentas de Linio
		if (!empty($ArrayLinios)) {

			foreach ($ArrayLinios as $linio_key => $linio) {

				$ConexionLinio = Client::create(new Configuration($tienda['Marketplace'][$linio_key]['api_host'], $tienda['Marketplace'][$linio_key]['api_user'], $tienda['Marketplace'][$linio_key]['api_key']));

				$linio->build()->call($ConexionLinio);

			}

		} //fin si existen productos para actualizar en tiendas de linio

	}


	/**
	 * Método encargado de actualizar, obtener el token de MELI. 
	 * Sí no existe Token, crea la url para la conexión
	 * @return array
	 */
	public function admin_verificar_conexion_meli()
	{
		$tiendas     = $this->obtener_tiendas();
		$redirectURI = Router::url( array('controller' => $this->request->controller, 'action' => 'index'), true );
		$siteId      = 'MLC';
		$results     = array();
		$response    = array();

		foreach ($tiendas as $it => $tienda) {
			foreach ($tienda['Marketplace'] as $im => $marketplace) {
				if ($marketplace['marketplace_tipo_id'] == 2) {

					if (!empty($marketplace['api_user']) && !empty($marketplace['api_key'])) {

						$this->Mercadolibre = new Meli($marketplace['api_user'], $marketplace['api_key'], $marketplace['access_token'], $marketplace['refresh_token']);
						$results[$marketplace['id']] = $this->admin_mercadolibre_conectar($marketplace, $redirectURI, $siteId);	
					}else{

						$results[$marketplace['id']]['errors'] = sprintf('%s no tiene configurado su API_USER y API_KEY', $marketplace['nombre']);
					}	
				}

			}
		}
		
		foreach ($results as $ir => $result) {
			if (!empty($result['success']) && !$this->shell) {
				$this->Session->setFlash($result['success'], null, array(), 'success');
			}

			if (!empty($result['errors']) && !$this->shell) {
				$this->Session->setFlash($result['errors'], null, array(), 'danger');
			}

			if (!empty($result['access'])) {
				$response[] = $result['access'];
			}
		}

		return $response;
	}


	/**
	 * Método encargado de conectar MELI con Sistemas
	 * @param  array  $marketplace Arreglo con la información del Marketplace 
	 * @param  string $redirectURI Url de redirección para el login de MEli
	 * @param  string $siteId      Identificador de API (MLC = Mercadolibre Chile)
	 * @return array  			   Información del procedimiento	
	 */
	public function admin_mercadolibre_conectar($marketplace = array(), $redirectURI = '', $siteId = 'MLC') 
	{	
		$m = array();
		$response = array(
			'access' => array(),
			'success' => array(),
			'errors' => array()
		);

		if(isset($_GET['code']) || isset($marketplace['access_token'])) {
			// If code exist and session is empty
			if(isset($_GET['code']) && !isset($marketplace['access_token'])) {
				// //If the code was in get parameter we authorize
				try{
					$user = $this->Mercadolibre->authorize($_GET["code"], $redirectURI);
					
					if ($user['httpCode'] == 200) {
						// Now we save credentials with the authenticated user
						$m['Marketplace']['access_token']  = $user['body']->access_token;
						$m['Marketplace']['expires_token'] = time() + $user['body']->expires_in;
						$m['Marketplace']['refresh_token'] = $user['body']->refresh_token;
						$m['Marketplace']['seller_id']     = $user['body']->user_id;
					}
				}catch(Exception $e){
					$response['errors'] = $e->getMessage();
				}
			} else {
				// We can check if the access token in invalid checking the time
				if($marketplace['expires_token'] < time()) {

					try {
						// Make the refresh proccess
						$refresh = $this->Mercadolibre->refreshAccessToken();
						
						if ($refresh['httpCode'] == 200) {
							// Now we save credentials with the new parameters
							$m['Marketplace']['access_token']  = $refresh['body']->access_token;
							$m['Marketplace']['expires_token'] = time() + $refresh['body']->expires_in;
							$m['Marketplace']['refresh_token'] = $refresh['body']->refresh_token;
							$m['Marketplace']['seller_id']     = $refresh['body']->user_id;
						}
					} catch (Exception $e) {
					  	$response['errors'] = $e->getMessage();
					}
				}
			}
			
			// save in db marketplace tokens
			if (!empty($m) && empty($response[$marketplace['id']]['errors'])) {

				ClassRegistry::init('Marketplace')->id = $marketplace['id'];
				if (!ClassRegistry::init('Marketplace')->save($m)) {
					$response['errors'] = sprintf('%s no se logró conectar a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}else{
					$response['success'] = sprintf('%s se ha conectado con éxito a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}
			}

			return $response;

		} else {

			$response['access']['marketplace'] = $marketplace['nombre'];
			$response['access']['url'] = $this->Mercadolibre->getAuthUrl($redirectURI, Meli::$AUTH_URL[$siteId]);
			
			return $response;
		}
	}


	/**
	 * Obtiene las ventas desde mercadolibre
	 * @param  array   $marketplace Arreglo con la información del Marketplace
	 * @param  integer $offset      salto del puntero en la búsqueda de ventas
	 * @return array                Arreglo con las ventas obtenidas
	 */
	private function mercadolibre_obtener_ventas($marketplace = array(), $offset = 0)
	{	
		$limit = 50; // Máximo número de items por request

        // Se previene el ingreso de un offset mayor a 1000
        if ($offset > 50) {
        	$offset = 50;
        }

        $params = array(
			'access_token' => $marketplace['access_token'],
			'seller'       => $marketplace['seller_id'],
			'limit'        => $limit,
			'offset'       => $offset
		);

		//se obtiene la última venta registrada para consultar solo las nuevas a mercadolibre
		$venta = $this->Venta->find(
			'first',
			array(
				'conditions' => array(
					'Venta.marketplace_id' => $marketplace['id']
				),
				'fields' => array(
					'Venta.fecha_venta'
				),
				'order' => 'Venta.fecha_venta DESC'
			)
		);

		if (!empty($venta)) { //ventas a partir de la última registrada

			$fecha = date_create($venta['Venta']['fecha_venta'] . ' -0400');
			
			$ahora = date_create(date('Y-m-d H:i:s'));

			date_modify($fecha, '+1 second');
			date_modify($fecha, '-1 hour');
			
			$fecha = date_format($fecha, 'c');
			$ahora = date_format($ahora, 'c');
			
			// Filtro
	        $params = array_replace_recursive($params, array(
				"order.date_created.from"   => $fecha,
				"order.date_created.to"     => $ahora
	        	)
	    	);

		}
		

        $ventasArr['ventasMercadolibre'] = array(); // Almacena las ventas para ser retornado
        $ejecutar = true; // Semaforo que determina si continua haciendo request
        $cont = 0; // Iterador para listar las ventas

        // Obtendrá las ventas hasta que $result['body']->results === 0
        while ($ejecutar) {
        	
         	$result = $this->Mercadolibre->get('/orders/search', $params);
         	
        	if ( $result['httpCode'] == 200 && count($result['body']->results) == 0  ) {
        		$ejecutar = false;
        	}elseif($result['httpCode'] == 200){

        		$params['offset'] = $params['offset'] + 50;
        		
        		foreach ($result['body']->results as $ir => $venta) {
        			if (!empty($venta)) {

        				$arrVenta = to_array($venta);

        				$ventaExiste =  $this->Venta->find('count', array('conditions' => array('Venta.id_externo' => $arrVenta['id'])));

        				if (!$ventaExiste) {
        					$ventasArr['ventasMercadolibre'][$cont] = $arrVenta;	
        				}
        			}
        			$cont++;
        		}
        	}else{
        		$ejecutar = false;
        	}
        }
		return $ventasArr;
	}


	/**
	 * Obtiene los mensajes de un pedido dado desde Meli
	 * @param  string $access_token token de acceso para peticiones privadas
	 * @param  string $id           Identificador de la venta de Meli
	 * @return array  				Mensajes del pedido
	 */
	public static function mercadolibre_obtener_mensajes($access_token = '', $id = '')
	{	
		$params = array(
			'access_token' => $access_token,
			'limit' => 20
		);

		try {
			$mensajes = self::$Mercadolibre->get('/messages/orders/' . $id, $params);
			$mensajes = to_array($mensajes);
			
		} catch (Exception $e) {
			// 
		}
		
		if ($mensajes['httpCode'] == 200) {
			return $mensajes['body']['results'];
		}else{
			return array();
		}
	}


	/**
	 * Obtener detalle de una venta desde Meli por su ID 
	 * @param  string $access_token token de acceso para peticiones privadas
	 * @param  string $id           Identificador de la venta de Meli
	 * @param  bool  $todo 			True: Retorna toda la respuesta d el GET False: retorna solo los productos del GET
	 * @return array               	Detalle de la venta
	 */
	private function mercadolibre_obtener_venta_detalles($access_token = '', $id = '', $todo = false)
	{
		$params = array(
			'access_token' => $access_token
		);
		
		try {
			$detallesVenta = $this->Mercadolibre->get('/orders/' . $id, $params);
			$detallesVenta = to_array($detallesVenta);
		} catch (Exception $e) {
			//
		}

		if ($detallesVenta['httpCode'] == 200) {
			if ($todo) {
				return $detallesVenta['body']; # Retornamos toda la venta
			}else{
				return Hash::extract($detallesVenta['body'], 'order_items.{n}'); # Retornamos solo los productos de la venta
			}
		}else{
			return array();
		}

	}


	/**
	 * Método encargado de obtener y crear un cliente registrado en Meli
	 * @param  array  $DataVenta Información de la venta ref: mercadolibre_obtener_venta_detalles
	 * @return bigint	         Identificador del Cliente creado o existente
	 */
	private function mercadolibre_obtener_cliente ($DataVenta = array()) {

		$rut = $DataVenta['buyer']['billing_info']['doc_number'];

		$VentaCliente = array();

		if (!empty($rut)) {
			$VentaCliente = $this->Venta->VentaCliente->find(
				'first',
				array(
					'conditions' => array(
						'VentaCliente.rut' => $rut
					),
					'fields' => array(
						'VentaCliente.id'
					)
				)
			);
		}

		if (!empty($VentaCliente)) {
			return $VentaCliente['VentaCliente']['id'];
		}

		//si el cliente no existe, se crea
		$data = array();
		$data['VentaCliente']['nombre']   = $DataVenta['buyer']['first_name'];
		$data['VentaCliente']['apellido'] = $DataVenta['buyer']['last_name'];
		$data['VentaCliente']['email']    = $DataVenta['buyer']['email'];
		$data['VentaCliente']['telefono'] = $DataVenta['buyer']['phone']['area_code'] . $DataVenta['buyer']['phone']['number'];
		$data['VentaCliente']['rut']      = $rut;

		$this->Venta->VentaCliente->create();
		$this->Venta->VentaCliente->save($data);

		return $this->Venta->VentaCliente->id;

	}

	/****************************************************************************************************/
	//actualización de estatus de ventas marcadas como No Atendidas para marketplaces (mercado libre)
	public function actualizar_ventas_anteriores_mercadolibre ($ventas, $marketplace) {

		$this->Mercadolibre = new Meli($marketplace['Marketplace']['api_user'], $marketplace['Marketplace']['api_key'], $marketplace['Marketplace']['access_token'], $marketplace['Marketplace']['refresh_token']);

		$dataToSave = array();

		foreach ($ventas as $venta) {

			$response = $this->mercadolibre_obtener_venta_detalles($marketplace['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);

			$EstatusMeli = $response['status'];
		
			$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
			
			$venta['Venta']['venta_estado_id'] = $this->linio_obtener_venta_estado($EstatusMeli);

			$dataToSave[] = $venta;

		}

		$this->Venta->saveMany($dataToSave);

	}

	/****************************************************************************************************/
	//actualización de estatus de ventas marcadas como No Atendidas para marketplaces (linio)
	public function actualizar_ventas_anteriores_linio ($ventas, $marketplace) {
		
		$ConexionLinio = Client::create(new Configuration($marketplace['Marketplace']['api_host'], $marketplace['Marketplace']['api_user'], $marketplace['Marketplace']['api_key']));

		$dataToSave = array();

		foreach ($ventas as $venta) {

			$EstatusLinio = $this->linio_obtener_venta($venta['Venta']['id_externo'], $ConexionLinio);

			sleep(1);

			$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
			$venta['Venta']['venta_estado_id'] = $this->linio_obtener_venta_estado($EstatusLinio);

			$dataToSave[] = $venta;

		}

		$this->Venta->saveMany($dataToSave);
		
	}

	public function linio_obtener_venta($id, $ConexionLinio, $todo =  false) 
	{
		$response = $ConexionLinio->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetOrder',
		        GenericRequest::V1,
		        ['OrderId' => $id]
		    ))
		);

		$results = $response->getBody()['Orders'];

		// Productos
		$productos = $this->linio_obtener_venta_detalles($id, $ConexionLinio);

		if ($todo) {
			
			if (!isset($productos[0])) {
				$productosN[0] = $productos;
			}else{
				$productosN = $productos;
			}
			
			// Agregamos productos a la venta
			$results['Order']['Products'] = $productosN;

			return $results['Order'];

		}else{
			return $results['Order']['Statuses']['Status'];
		}
	}

	/****************************************************************************************************/
	//actualización de estatus de ventas marcadas como No Atendidas para tiendas (prestashop)
	public function actualizar_ventas_anteriores_prestashop ($ventas, $tienda) {

		$ConexionPrestashop = new PrestaShopWebservice($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'], false);

		$dataToSave = array();

		foreach ($ventas as $venta) {
			/*
			$opt = array();
			$opt['resource'] = 'orders';
			$opt['display'] = '[current_state,total_paid]';
			$opt['filter[id]'] = '[' .$venta['Venta']['id_externo']. ']';

			$xml = $ConexionPrestashop->get($opt);

			$PrestashopResources = $xml->children()->children();

			$json = json_encode($PrestashopResources);
			$data = json_decode($json, true);
			*/
		
			# Modelos que requieren agregar configuración
			$this->cambiarDatasource(array('Orden'), $tienda);
		
			$data = ClassRegistry::init('Orden')->find('first', array(
				'conditions' => array(
					'Orden.id_order' => $venta['Venta']['id_externo']
				),
				'fields' => array(
					'Orden.current_state',
					'Orden.total_paid'
				)
			));
			
			if (empty($data)) {
				$venta['Venta']['estado_anterior'] = 1;
				$venta['Venta']['venta_estado_id'] = 1; //Sin Estado
			}
			else {
				$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
				$venta['Venta']['venta_estado_id'] = $this->prestashop_obtener_venta_estado($data['Orden']['current_state'], $ConexionPrestashop);
				$venta['Venta']['total']           = $data['Orden']['total_paid'];
			}

			$dataToSave[] = $venta;

		}

		$this->Venta->saveMany($dataToSave);

	}

	/****************************************************************************************************/
	//recorre las tiendas y marketplaces, busca las ventas para actualizar y las envía a los métodos
	//actualizar_ventas_anteriores_prestashop, actualizar_ventas_anteriores_linio y actualizar_ventas_anteriores_mercadolibre, según corresponda
	public function actualizar_ventas_anteriores () {

		$this->layout = 'ajax';

		set_time_limit(0);

		//----------------------------------------------------------------------------------------------------
		//lista de tiendas (prestashop)
		$tiendas = $this->Venta->Tienda->find(
			'all',
			array(
				'conditions' => array(
					'Tienda.activo' => 1,
					'Tienda.apiurl_prestashop <>' => '',
					'Tienda.apikey_prestashop <>' => ''
				),
				'fields' => array(
					'Tienda.id', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.configuracion'
				)
			)
		);

		//recorrido de las tiendas (prestashop)
		foreach ($tiendas as $tienda) {

			$ventas = $this->Venta->find(
				'all',
				array(
					'conditions' => array(
						'Venta.atendida' => 0,
						'Venta.activo' => 1,
						'Venta.tienda_id' => $tienda['Tienda']['id'],
						'Venta.marketplace_id IS NULL'
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.venta_estado_id', 'Venta.estado_anterior'
					)
				)
			);
			
			if (!empty($ventas)) {
				$this->actualizar_ventas_anteriores_prestashop($ventas, $tienda);
			}
			
		}

		//----------------------------------------------------------------------------------------------------
		//lista de marketplaces (linio y mercado libre)
		$marketplaces = $this->Venta->Marketplace->find(
			'all',
			array(
				'conditions' => array(
					'Marketplace.activo' => 1
				),
				'fields' => array(
					'Marketplace.id', 'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token', 'Marketplace.seller_id', 'Marketplace.marketplace_tipo_id'
				)
			)
		);

		//recorrido de los marketplaces (linio y mercado libre)
		foreach ($marketplaces as $marketplace) {

			$ventas = $this->Venta->find(
				'all',
				array(
					'conditions' => array(
						'Venta.atendida' => 0,
						'Venta.activo' => 1,
						'Venta.marketplace_id' => $marketplace['Marketplace']['id']
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.venta_estado_id', 'Venta.estado_anterior'
					)
				)
			);
			
			if (!empty($ventas)) {

				switch ($marketplace['Marketplace']['marketplace_tipo_id']) {

					case 1:
						$this->actualizar_ventas_anteriores_linio($ventas, $marketplace);
					break;

					case 2:
						$this->actualizar_ventas_anteriores_mercadolibre($ventas, $marketplace);
					break;

				}

			}
			
		}

	}


	/**
	 * Devuelve el stock de las ventas con eststados que tanga habilitada la opción de
	 * revertir stock
	 * @param  array  $excluir [description]
	 * @return void
	 */
	public function ventas_estados_revertidas( $excluir = array() )
	{	
		$ventas = $this->Venta->find('all', array(
			'conditions' => array(
				'Venta.activo' => 1,
				'Venta.atendida' => 0
			),
			'contain' => array(
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.revertir_stock'
					)
				),
				'VentaDetalle' => array(
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.cantidad_virtual',
							'VentaDetalleProducto.id_externo',
							'VentaDetalleProducto.id'
						)
					),
					'fields' => array(
						'VentaDetalle.cantidad'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.venta_estado_id',
				'Venta.estado_anterior'
			)
		));

		# agrupa los nuevos stocks de los productos
		$productoStocks = array();
		
		if (!empty($ventas)) {

			# Descontar stock virtual y refrescar canales
			$productosController = new VentaDetalleProductosController();

			foreach ($ventas as $iv => $venta) {

				# solo se procesa si el estado de la venta ha cambiado
				if ($venta['VentaEstado']['revertir_stock'] && $venta['Venta']['venta_estado_id'] != $venta['Venta']['estado_anterior'] ) {
					foreach ($venta['VentaDetalle'] as $ip => $producto) {

						$productoStocks[$producto['VentaDetalleProducto']['id']]['id_externo'] = $producto['VentaDetalleProducto']['id_externo'];
						//$productoStocks[$producto['VentaDetalleProducto']['id']]['descontar']  = $producto['cantidad'];

						if (!isset($productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad'])) {
							$productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad']   = $producto['VentaDetalleProducto']['cantidad_virtual'] + $producto['cantidad'];
						}else{
							$productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad']   = $productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad'] + $producto['cantidad'];
						}
						
					}
				}
			}
			
			# Se refrescan solo una vez cada producto
			foreach ($productoStocks as $id_producto => $data) {
				//$productosController->descontar_stock_virtual($id_producto, $data['id_externo'], $data['nueva_cantidad'], $excluir);
			}

		}

		return;

	}


	public function ventas_estados_atendidos( $excluir = array() )
	{
		$ventas = $this->Venta->find('all', array(
			'conditions' => array(
				'Venta.activo' => 1
			),
			'contain' => array(
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.marcar_atendida'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.venta_estado_id',
				'Venta.estado_anterior',
				'Venta.fecha_venta',
				'Venta.atendida'
			)
		));


		$limite_mes  = mktime(0, 0, 0, date("m")-3, date("d"),   date("Y"));
		
		if (!empty($ventas)) {

			foreach ($ventas as $iv => $venta) {

				if ($venta['VentaEstado']['marcar_atendida'] && !$venta['Venta']['atendida']) {
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 1
						)
					)); 
				}elseif ( strtotime($venta['Venta']['fecha_venta']) < $limite_mes && !$venta['Venta']['atendida']) { // si es una venta muy antigua se cierra
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 1
						)
					)); 
				}elseif ($venta['VentaEstado']['marcar_atendida'] && $venta['Venta']['atendida'] && $venta['Venta']['venta_estado_id'] != $venta['Venta']['estado_anterior']) {
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 1
						)
					)); 
				}elseif ($venta['Venta']['atendida']) {
					continue; // Ya fue atendida
				}else{
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 0
						)
					));
				}
			}

		}

		return;
	}



	/****************************************************************************************************/
	//actualización de ventas
	public function admin_actualizar_ventas () {
		
		$this->layout = 'ajax';

		set_time_limit(0);

		# Mercadolibre conectar
		$this->admin_verificar_conexion_meli();

		//actualización de estatus de ventas marcadas como No Atendidas
		#$this->actualizar_ventas_anteriores();

		//ids de productos para actualizar su stock al finalizar la carga de ventas
		$ArrayProductosSincronizacion = array();
		$ArrayCantidadesVendidos = array();

		//se buscan las tiendas que se deben procesar
		$tiendas = $this->obtener_tiendas();

		//si hay tiendas para procesar
		if (!empty($tiendas)) {

			//ciclo para procesar cada tienda (prestashop)
			foreach ($tiendas as $tienda) {

				//----------------------------------------------------------------------------------------------------
				//conexión a prestashop (para registrar las ventas de la tienda)
				$ConexionPrestashop = new PrestaShopWebservice($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'], false);

				//si se cargaron ventas
				if ($TiendaVentas = $this->prestashop_obtener_ventas($tienda, $ConexionPrestashop)) {

					if (!isset($TiendaVentas['order'][0])) {
						$TiendaVentas = array(
							'order' => array(
								'0' => $TiendaVentas['order']
							)
						);
					}
					
					//ciclo de ventas
					foreach ($TiendaVentas['order'] as $DataVenta) {
						
						//datos de la venta a registrar
						$NuevaVenta = array();
						$NuevaVenta['Venta']['tienda_id'] = $tienda['Tienda']['id'];
						$NuevaVenta['Venta']['id_externo'] = $DataVenta['id'];
						$NuevaVenta['Venta']['referencia'] = $DataVenta['reference'];
						$NuevaVenta['Venta']['fecha_venta'] = $DataVenta['date_add'];
						$NuevaVenta['Venta']['descuento'] = intval(round($DataVenta['total_discounts_tax_incl']));
						$NuevaVenta['Venta']['costo_envio'] = intval(round($DataVenta['total_shipping_tax_incl']));
						$NuevaVenta['Venta']['total'] = intval(round($DataVenta['total_paid']));

						//se obtienen las transacciones de una venta
						//si la venta tiene transacciones asociadas
						if ($VentaTransacciones = $this->prestashop_obtener_venta_transacciones($DataVenta['reference'], $ConexionPrestashop)) {

							if (!isset($VentaTransacciones['order_payment'][0])) {
								$VentaTransacciones = array(
									'order_payment' => array(
										'0' => $VentaTransacciones['order_payment']
									)
								);
							}
							
							foreach ($VentaTransacciones['order_payment'] as $transaccion) {

								$NuevaTransaccion = array();

								if (!empty($transaccion['transaction_id'])) {
									$NuevaTransaccion['nombre'] = $transaccion['transaction_id'];
								}

								$NuevaTransaccion['monto'] = (!empty($transaccion['amount'])) ? $transaccion['amount'] : 0;

								$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;
								
							}

						}

						$direccionEntrega = $this->prestashop_obtener_venta_direccion($DataVenta['id_address_delivery'], $ConexionPrestashop);

						// Dirección de entrega
						if (!isset($DataVenta['address'])) {
							
							$direccion_entrega = '';
							$comuna_entrega    = '';
							$nombre_receptor   = '';
							$fono_receptor     = '';

							if (!empty($direccionEntrega['address']['address1'])) {

								if (is_array($direccionEntrega['address']['address1'])) {
									$direccionEntrega['address']['address1'] = implode(', ', $direccionEntrega['address']['address1']);
								}

								$direccion_entrega .= $direccionEntrega['address']['address1'];
							}

							if (!empty($direccionEntrega['address']['address2'])) {

								if (is_array($direccionEntrega['address']['address2'])) {
									$direccionEntrega['address']['address2'] = implode(', ', $direccionEntrega['address']['address2']);
								}

								$direccion_entrega .= ', ' . $direccionEntrega['address']['address2'];
							}

							if (!empty($direccionEntrega['address']['other'])) {
								if (is_array($direccionEntrega['address']['other'])) {
									$direccionEntrega['address']['other'] = implode(', ', $direccionEntrega['address']['other']);
								}
								$direccion_entrega .= ', ' . $direccionEntrega['address']['other'];
							}

							if (!empty($direccionEntrega['address']['city'])) {
								$comuna_entrega .= $direccionEntrega['address']['city'];
							}

							if (!empty($direccionEntrega['address']['firstname'])) {
								$nombre_receptor .= $direccionEntrega['address']['firstname'] . ' ' . $direccionEntrega['address']['lastname'];
							}

							if (!empty($direccionEntrega['address']['phone'])) {

								if (is_array($direccionEntrega['address']['phone'])) {
									$direccionEntrega['address']['phone'] = implode(' - ', $direccionEntrega['address']['phone']);
								}

								$fono_receptor .= trim($direccionEntrega['address']['phone']);
							}

							if (!empty($direccionEntrega['address']['phone_mobile'])) {
								$fono_receptor .=  ' - ' . trim($direccionEntrega['address']['phone_mobile']);
							}


							$NuevaVenta['Venta']['direccion_entrega'] =  $direccion_entrega;
							$NuevaVenta['Venta']['comuna_entrega']    =  $comuna_entrega;
							$NuevaVenta['Venta']['nombre_receptor']   =  $nombre_receptor;
							$NuevaVenta['Venta']['fono_receptor']     =  $fono_receptor;
						}

						//se obtienen el detalle de la venta
						$VentaDetalles = $this->prestashop_obtener_venta_detalles($DataVenta['id'], $ConexionPrestashop);

						if (isset($VentaDetalles['order_detail']) && !isset($VentaDetalles['order_detail'][0])) {
							$VentaDetalles = array(
								'order_detail' => array(
									'0' => $VentaDetalles['order_detail']
								)
							);
						}

						// Existen ventas sin productos xD
						if (isset($VentaDetalles['order_detail'])) {
							//ciclo para recorrer el detalle de la venta
							foreach ($VentaDetalles['order_detail'] as $DetalleVenta) {
								if (!empty($DetalleVenta['product_id'])) {
									
									$NuevoDetalle = array();
									$NuevoDetalle['venta_detalle_producto_id'] = $DetalleVenta['product_id'];
									$NuevoDetalle['precio'] = round($DetalleVenta['unit_price_tax_excl'], 2);
									$NuevoDetalle['cantidad'] = $DetalleVenta['product_quantity'];

									$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;

									# Evitamos que se vuelva actualizar el stock en prestashop
									$excluirPrestashop = array('Prestashop' => array($tienda['Tienda']['id']));

									//se guarda el producto si no existe
									$this->prestashop_guardar_producto($DetalleVenta, $excluirPrestashop);

									//se toma el id de producto para usarlo luego en la sincronización de stock
									if (!in_array($DetalleVenta['product_id'], $ArrayProductosSincronizacion)) {
										$ArrayProductosSincronizacion[] = $DetalleVenta['product_id'];
									}

								}
								
							} //fin ciclo detalle de venta
						}

						//se obtiene el estado de la venta
						if (empty($DataVenta['current_state']) || $DataVenta['current_state'] == 0) {
							$NuevaVenta['Venta']['venta_estado_id'] = 1; //Sin Estado
						}
						else {
							$NuevaVenta['Venta']['venta_estado_id'] = $this->prestashop_obtener_venta_estado($DataVenta['current_state'], $ConexionPrestashop);
						}

						//se obtiene el medio de pago
						$NuevaVenta['Venta']['medio_pago_id'] = $this->prestashop_obtener_medio_pago($DataVenta['payment']);
						
						//se obtiene el cliente
						$NuevaVenta['Venta']['venta_cliente_id'] = $this->prestashop_obtener_cliente($DataVenta['id_customer'], $ConexionPrestashop);

						//se guarda la venta
						$this->Venta->create();
						$this->Venta->saveAll($NuevaVenta);

					} //fin ciclo de ventas

				} //fin si se cargaron ventas

				//----------------------------------------------------------------------------------------------------
				//registro de las ventas de marketplaces asociados a la tienda
				if (!empty($tienda['Marketplace'])) {

					//recorrido de marketplaces
					foreach ($tienda['Marketplace'] as $marketplace) {

						//----------------------------------------------------------------------------------------------------
						//si el marketplace es Linio
						if ($marketplace['marketplace_tipo_id'] == 1) {

							$ConexionLinio = Client::create(new Configuration($marketplace['api_host'], $marketplace['api_user'], $marketplace['api_key']));
							
							$finalizarLinio = false;

							do {
								
								$LinioVentas = $this->linio_obtener_ventas($marketplace, $ConexionLinio, $finalizarLinio);

								//si se cargaron ventas
								if ($LinioVentas) {

									if (!isset($LinioVentas[0])) {
										$LinioVentas = array(
											'0' => $LinioVentas
										);
									}

									//ciclo de ventas
									foreach ($LinioVentas as $DataVenta) {

										//datos de la venta a registrar
										$NuevaVenta = array();
										$NuevaVenta['Venta']['tienda_id'] = $tienda['Tienda']['id'];
										$NuevaVenta['Venta']['marketplace_id'] = $marketplace['id'];
										$NuevaVenta['Venta']['id_externo'] = $DataVenta['OrderId'];
										$NuevaVenta['Venta']['referencia'] = $DataVenta['OrderNumber'];
										$NuevaVenta['Venta']['fecha_venta'] = $DataVenta['CreatedAt'];
										$NuevaVenta['Venta']['total'] = intval(round($DataVenta['Price']));
										//$NuevaVenta['Venta']['total'] = 0;
										
										// Guardar transacción
										$NuevaTransaccion = array();

										if (!empty($DataVenta['OrderNumber'])) {
											$NuevaTransaccion['nombre'] = $DataVenta['OrderNumber'];
										}

										$NuevaTransaccion['monto'] = (!empty($DataVenta['Price'])) ? $DataVenta['Price'] : 0;
										$NuevaTransaccion['fee']   = ($NuevaTransaccion['monto'] * ($marketplace['fee'] / 100));

										$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;

												
										//se obtienen el detalle de la venta
										$VentaDetalles = $this->linio_obtener_venta_detalles($DataVenta['OrderId'], $ConexionLinio);
										
										if (!isset($VentaDetalles[0])) {
											$VentaDetalles = array(
												'0' => $VentaDetalles
											);
										}
										
										// Direccion despacho
										$NuevaVenta['Venta']['direccion_entrega'] =  $DataVenta['AddressShipping']['Address1'] . ', ' . $DataVenta['AddressShipping']['Address2'];
										$NuevaVenta['Venta']['comuna_entrega']    =  $DataVenta['AddressShipping']['City'];
										$NuevaVenta['Venta']['nombre_receptor']   =  $DataVenta['AddressShipping']['FirstName'] . ' ' . $DataVenta['AddressShipping']['LastName'];
										$NuevaVenta['Venta']['fono_receptor']     =  trim($DataVenta['AddressShipping']['Phone']) . '-' .  trim($DataVenta['AddressShipping']['Phone2']) ;
										
										$totalDespacho = (float) 0;

										//ciclo para recorrer el detalle de la venta
										foreach ($VentaDetalles as $DetalleVenta) {

											$DetalleVenta['Sku'] = intval($DetalleVenta['Sku']);

											# Evitamos que se vuelva actualizar el stock en linio
											$excluirLinio = array('Linio' => array($marketplace['id']));

											//se guarda el producto si no existe
											$idNuevoProducto = $this->linio_guardar_producto($DetalleVenta, $excluirLinio);

											$NuevoDetalle = array();
											$NuevoDetalle['venta_detalle_producto_id'] = $idNuevoProducto;
											$NuevoDetalle['precio'] = $this->precio_neto(round($DetalleVenta['PaidPrice'], 2));

											$totalDespacho = $totalDespacho + round($DetalleVenta['ShippingAmount'], 2);

											// Se agrega el valor de la compra sumanod el precio de los productos
											//$NuevaVenta['Venta']['total'] = $NuevaVenta['Venta']['total'] + $NuevoDetalle['precio'];

											$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;
											
											//se toma el id de producto para usarlo luego en la sincronización de stock
											if (!in_array($DetalleVenta['Sku'], $ArrayProductosSincronizacion)) {
												$ArrayProductosSincronizacion[] = $DetalleVenta['Sku'];
											}

											//se aumenta la cantidad de vendidos
											$pos = array_search($DetalleVenta['Sku'], $ArrayProductosSincronizacion);

											if (!isset($ArrayCantidadesVendidos[$pos])) {
												$ArrayCantidadesVendidos[$pos] = 1;
											}
											else {
												$ArrayCantidadesVendidos[$pos]++;
											}

										} //fin ciclo detalle de venta

										$NuevaVenta['Venta']['costo_envio'] = (float) $totalDespacho;

										//se obtiene el estado de la venta
										$NuevaVenta['Venta']['venta_estado_id'] = $this->linio_obtener_venta_estado($DataVenta['Statuses']['Status']);

										//se obtiene el medio de pago
										$NuevaVenta['Venta']['medio_pago_id'] = $this->linio_obtener_medio_pago($DataVenta['PaymentMethod']);

										//se obtiene el cliente
										$NuevaVenta['Venta']['venta_cliente_id'] = $this->linio_obtener_cliente($DataVenta);

										/*
										obtener mensajes de la venta
										$mensajes = $this->linio_obtener_venta_mensajes ($DataVenta['OrderId'], $ConexionLinio);
										*/
										#prx($DataVenta);
										//se guarda la venta
										$this->Venta->create();
										$this->Venta->saveAll($NuevaVenta);

									} //fin ciclo de ventas

								} //fin si se cargaron ventas

								sleep(1); // Delay de 3 segunos para evitar excepción Too many request

							} while ( !$finalizarLinio );

						} //fin si el marketplace es Linio


						# Es mercadolibre
						if ($marketplace['marketplace_tipo_id'] == 2) {

							self::$Mercadolibre = new Meli($marketplace['api_user'], $marketplace['api_key'], $marketplace['access_token'], $marketplace['refresh_token']);

							$ventasMercadolibre = $this->mercadolibre_obtener_ventas($marketplace);
							
							if (count($ventasMercadolibre['ventasMercadolibre']) > 0) {
								//ciclo de ventas
								foreach ($ventasMercadolibre['ventasMercadolibre'] as $DataVenta) {

									//datos de la venta a registrar
									$NuevaVenta                            = array();
									$NuevaVenta['Venta']['tienda_id']      = $tienda['Tienda']['id'];
									$NuevaVenta['Venta']['marketplace_id'] = $marketplace['id'];
									$NuevaVenta['Venta']['id_externo']     = $DataVenta['id'];
									$NuevaVenta['Venta']['referencia']     = $DataVenta['id'];

									
									$NuevaVenta['Venta']['fecha_venta']    = CakeTime::format($DataVenta['date_created'], '%Y-%m-%d %H:%M:%S');
									$NuevaVenta['Venta']['total']          = intval(round($DataVenta['total_amount']));
									
									//se obtienen el detalle de la venta
									$VentaDetalles = $this->mercadolibre_obtener_venta_detalles($marketplace['access_token'], $DataVenta['id'], true);
									
									// Detalles de envio
									$direccion_entrega = '';
									$comuna_entrega  = '';
									$nombre_receptor = '';
									$fono_receptor   = '';

									if (isset($VentaDetalles['shipping']['receiver_address']['address_line'])
										&& isset($VentaDetalles['shipping']['receiver_address']['city']['name'])) {
										$direccion_entrega = $VentaDetalles['shipping']['receiver_address']['address_line'] . ', ' . $VentaDetalles['shipping']['receiver_address']['city']['name'];
									}

									if (isset($VentaDetalles['shipping']['receiver_address']['city']['name'])) {
										$comuna_entrega = $VentaDetalles['shipping']['receiver_address']['city']['name'];
									}

									if (isset($VentaDetalles['shipping']['receiver_address']['receiver_name'])) {
										$nombre_receptor = $VentaDetalles['shipping']['receiver_address']['receiver_name'];
									}

									if (isset($VentaDetalles['shipping']['receiver_address']['receiver_phone'])) {
										$fono_receptor = $VentaDetalles['shipping']['receiver_address']['receiver_phone'];
									}

									// Direccion despacho
									$NuevaVenta['Venta']['direccion_entrega'] =  $direccion_entrega;
									$NuevaVenta['Venta']['comuna_entrega']    =  $comuna_entrega;
									$NuevaVenta['Venta']['nombre_receptor']   =  $nombre_receptor;
									$NuevaVenta['Venta']['fono_receptor']     =  $fono_receptor;
									
									if (!isset($VentaDetalles['order_items'][0])) {
										$VentaDetalles['order_items'] = array(
											'0' => $VentaDetalles['order_items']
										);
									}
							
									//ciclo para recorrer el detalle de la venta
									foreach ($VentaDetalles['order_items'] as $DetalleVenta) {
										if (!empty($DetalleVenta['item']['seller_custom_field']) ) {

											// Si el seller id es referencia entonces lo reemplazamos por ID
											if (intval($DetalleVenta['item']['seller_custom_field']) == 0) {
												$nuevoSellerID = $this->prestashop_obtener_idproducto($DetalleVenta['item']['seller_custom_field'], $ConexionPrestashop);

												// No se logró encontrar el ID
												if (!isset($nuevoSellerID['product']['id'])) {
													continue; // Se continua con el siguiente
												}

												$DetalleVenta['Sku']  = $nuevoSellerID['product']['id'];
											}else{
												$DetalleVenta['Sku']  = intval($DetalleVenta['item']['seller_custom_field']);
											}

											
											$DetalleVenta['Name'] = $DetalleVenta['item']['title'];

											# Evitamos que se vuelva actualizar el stock en meli
											$excluirMeli = array('Mercadolibre' => array($marketplace['id']));

											//se guarda el producto si no existe
											$idNuevoProducto = $this->linio_guardar_producto($DetalleVenta, $excluirMeli);

											$NuevoDetalle                              = array();
											$NuevoDetalle['venta_detalle_producto_id'] = $idNuevoProducto;
											$NuevoDetalle['precio']                    = $this->precio_neto(round($DetalleVenta['unit_price'], 2));

											$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;


											//se toma el id de producto para usarlo luego en la sincronización de stock
											if (!in_array($DetalleVenta['Sku'], $ArrayProductosSincronizacion)) {
												$ArrayProductosSincronizacion[] = $DetalleVenta['Sku'];
											}

											//se aumenta la cantidad de vendidos
											$pos = array_search($DetalleVenta['Sku'], $ArrayProductosSincronizacion);

											if (!isset($ArrayCantidadesVendidos[$pos])) {
												$ArrayCantidadesVendidos[$pos] = 1;
											}
											else {
												$ArrayCantidadesVendidos[$pos]++;
											}
											
										} // fin no empty
									
									} //fin ciclo detalle de venta

									# Mercado libre puede tener más de 1 pago
									foreach ($DataVenta['payments'] as $venta) {
										//se obtiene el estado de la venta
										$NuevaVenta['Venta']['venta_estado_id'] = $this->linio_obtener_venta_estado($venta['status']);

										//se obtiene el medio de pago
										$NuevaVenta['Venta']['medio_pago_id'] = $this->linio_obtener_medio_pago($venta['payment_type']);

										$NuevaTransaccion = array();

										if (!empty($venta['id'])) {
											$NuevaTransaccion['nombre'] = $venta['id'];
										}

										$NuevaTransaccion['monto'] = (!empty($venta['total_paid_amount'])) ? $venta['total_paid_amount'] : 0;
										$NuevaTransaccion['fee'] = (!empty($venta['marketplace_fee'])) ? $venta['marketplace_fee'] : 0;

										$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;
									}

									//se obtiene el cliente
									$NuevaVenta['Venta']['venta_cliente_id'] = $this->mercadolibre_obtener_cliente($DataVenta);

									
									# Obtener mensajes de la venta
									$mensajes = $this->mercadolibre_obtener_mensajes($marketplace['access_token'], $DataVenta['id']);

									foreach ($mensajes as $im => $mensaje) {
	
										$NuevaVenta['VentaMensaje'][$im]['nombre']   = $mensaje['subject'];
										$NuevaVenta['VentaMensaje'][$im]['fecha']    = CakeTime::format($mensaje['date'], '%Y-%m-%d %H:%M:%S');
										$NuevaVenta['VentaMensaje'][$im]['emisor']   = $mensaje['from']['user_id'];
										$NuevaVenta['VentaMensaje'][$im]['mensaje']  = $this->removeEmoji($mensaje['text']['plain']);

									}
									
									//se guarda la venta
									$this->Venta->create();
									$this->Venta->saveAll($NuevaVenta);

								} //fin ciclo de ventas
							} // Fin agregar mercadopago

						} // Fin mercadolibre

					} //fin ciclo de marketplaces

				} //fin si hay marketplaces para procesar

				//----------------------------------------------------------------------------------------------------
				//sincronizar stock de productos
				/*if (!empty($ArrayProductosSincronizacion)) {

					$this->sincronizar_stock_productos($tienda, $ArrayProductosSincronizacion, $ArrayCantidadesVendidos, $ConexionPrestashop);

				}*/ // fin si hay productos para sincronizar

			} //fin ciclo de tiendas

		} //fin si hay ventas para procesar


		# revertir el stock virtual del producto segun su estado
		$this->ventas_estados_revertidas();

		# Marca las ventas como atendidas segun su estado.
		$this->ventas_estados_atendidos();
		
		if (!$this->shell) {
			$this->Session->setFlash('Actualización realizada correctamente', null, array(), 'success');

			$this->redirect(array('action' => 'index'));
		}

	}

	public function admin_exportar () {

		set_time_limit(0);

		ini_set('memory_limit', '512M');

		$condiciones = array();
		$joins = array();

		//----------------------------------------------------------------------------------------------------
		//info de venta
		$FiltroVenta = "";
		
		if (isset($this->request->data['Venta']['filtroventa'])) {

			$FiltroVenta = trim($this->request->data['Venta']['filtroventa']);

			if ($FiltroVenta != "") {

				$condiciones["OR"] = array(
					"Venta.id LIKE '%" .$FiltroVenta. "%'",
					"Venta.id_externo LIKE '%" .$FiltroVenta. "%'",
					"Venta.referencia LIKE '%" .$FiltroVenta. "%'"
				);
				
			}

		}

		//----------------------------------------------------------------------------------------------------
		//info de cliente
		$FiltroCliente = "";
		
		if (isset($this->request->data['Venta']['filtrocliente'])) {

			$FiltroCliente = trim($this->request->data['Venta']['filtrocliente']);

			if ($FiltroCliente != "") {

				$joins[] = array(
					'table' => 'rp_venta_clientes',
					'alias' => 'clientes',
					'type' => 'INNER',
					'conditions' => array(
						'clientes.id = Venta.venta_cliente_id',
						'OR' => array(
							"clientes.nombre LIKE '%" .$FiltroCliente. "%'",
							"clientes.apellido LIKE '%" .$FiltroCliente. "%'",
							"clientes.rut LIKE '%" .$FiltroCliente. "%'",
							"clientes.email LIKE '%" .$FiltroCliente. "%'",
							"clientes.telefono LIKE '%" .$FiltroCliente. "%'"
						)
					)
				);
				
			}

		}

		//----------------------------------------------------------------------------------------------------
		//tienda
		$FiltroTienda = "";
		
		if (isset($this->request->data['Venta']['tienda_id'])) {

			$FiltroTienda = $this->request->data['Venta']['tienda_id'];

			if ($FiltroTienda != "") {
				$condiciones['Venta.tienda_id'] = $FiltroTienda;
			} 

		}

		//----------------------------------------------------------------------------------------------------
		//marketplace
		$FiltroMarketplace = "";
		
		if (isset($this->request->data['Venta']['marketplace_id'])) {

			$FiltroMarketplace = $this->request->data['Venta']['marketplace_id'];

			if ($FiltroMarketplace != "") {
				$condiciones['Venta.marketplace_id'] = $FiltroMarketplace;
			} 

		}

		//----------------------------------------------------------------------------------------------------
		//medio de pago
		$FiltroMedioPago = "";
		
		if (isset($this->request->data['Venta']['medio_pago_id'])) {

			$FiltroMedioPago = $this->request->data['Venta']['medio_pago_id'];

			if ($FiltroMedioPago != "") {
				$condiciones['Venta.medio_pago_id'] = $FiltroMedioPago;
			} 

		}

		//----------------------------------------------------------------------------------------------------
		//estado de venta
		$FiltroVentaEstadoCategoria = "";
		
		if (isset($this->request->data['Venta']['venta_estado_categoria_id'])) {

			$FiltroVentaEstadoCategoria = $this->request->data['Venta']['venta_estado_categoria_id'];

			if ($FiltroVentaEstadoCategoria != "") {

				$joins[] = array(
					'table' => 'rp_venta_estados',
					'alias' => 'ventas_estados',
					'type' => 'INNER',
					'conditions' => array(
						'ventas_estados.id = Venta.venta_estado_id',
						"ventas_estados.venta_estado_categoria_id = " .$FiltroVentaEstadoCategoria
					)
				);

			} 

		}

		//----------------------------------------------------------------------------------------------------
		//venta atendida
		$FiltroAtendida = "";
		
		if (isset($this->request->data['Venta']['atendida'])) {

			$FiltroAtendida = $this->request->data['Venta']['atendida'];

			if ($FiltroAtendida != "") {
				$condiciones['Venta.atendida'] = $FiltroAtendida;
			} 

		}

		//----------------------------------------------------------------------------------------------------
		//fecha desde
		$FiltroFechaDesde = "";

		if (isset($this->request->data['Venta']['FechaDesde'])) {

			$FiltroFechaDesde = trim($this->request->data['Venta']['FechaDesde']);

			if ($FiltroFechaDesde != "") {

				$ArrayFecha = explode("-", $FiltroFechaDesde);

				$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

				$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 00:00:00"));

				$condiciones["Venta.fecha_venta >="] = $Fecha;

			} 

		}

		//----------------------------------------------------------------------------------------------------
		//fecha hasta
		$FiltroFechaHasta = "";

		if (isset($this->request->data['Venta']['FechaHasta'])) {

			$FiltroFechaHasta = trim($this->request->data['Venta']['FechaHasta']);

			if ($FiltroFechaHasta != "") {

				$ArrayFecha = explode("-", $FiltroFechaHasta);

				$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

				$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 23:59:59"));

				$condiciones["Venta.fecha_venta <="] = $Fecha;

			} 

		}

		//----------------------------------------------------------------------------------------------------
		$datos = $this->Venta->find(
			'all',
			array(
				'contain' => array(
					'VentaEstado' => array(
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
							)
						),
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.venta_estado_categoria_id'
						)
					),
					'Tienda' => array(
						'fields' => array(
							'Tienda.id', 'Tienda.nombre'
						)
					),
					'Marketplace' => array(
						'fields' => array(
							'Marketplace.id', 'Marketplace.nombre'
						)
					),
					'MedioPago' => array(
						'fields' => array(
							'MedioPago.id', 'MedioPago.nombre'
						)
					),
					'VentaCliente' => array(
						'fields' => array(
							'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono',
						)
					),
					'Dte' => array(
						'fields' => array(
							'Dte.estado', 'Dte.folio', 'Dte.tipo_documento'
						)
					)
				),
				'conditions' => $condiciones,
				'joins' => $joins,
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo',
					'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id'
				),
				'order' => 'Venta.fecha_venta DESC'
			)
		);
	
		$this->set(compact('datos'));

	}

	public function admin_view ($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$venta = $this->request->data = $this->Venta->find(
			'first',
			array(
				'conditions' => array(
					'Venta.id' => $id
				),
				'contain' => array(
					'VentaDetalle' => array(
						'VentaDetalleProducto' => array(
							'fields' => array(
								'VentaDetalleProducto.id', 'VentaDetalleProducto.nombre'
							)
						),
						'conditions' => array(
							'VentaDetalle.activo' => 1
						),
						'fields' => array(
							'VentaDetalle.id', 'VentaDetalle.venta_detalle_producto_id', 'VentaDetalle.precio', 'VentaDetalle.cantidad', 'VentaDetalle.venta_id'
						)
					),
					'VentaEstado' => array(
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
							)
						),
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.nombre', 'VentaEstado.venta_estado_categoria_id', 'VentaEstado.permitir_dte'
						)
					),
					'Tienda' => array(
						'fields' => array(
							'Tienda.id', 'Tienda.nombre', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop'
						)
					),
					'Marketplace' => array(
						'fields' => array(
							'Marketplace.id', 'Marketplace.nombre', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id',
							'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key',
							'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token'
						)
					),
					'MedioPago' => array(
						'fields' => array(
							'MedioPago.id', 'MedioPago.nombre'
						)
					),
					'VentaCliente' => array(
						'fields' => array(
							'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono', 'VentaCliente.created'
						)
					),
					'Dte' => array(
						'Administrador' => array(
							'fields' => array(
								'Administrador.email'
							)
						),
						'fields' => array(
							'Dte.id', 'Dte.folio', 'Dte.tipo_documento', 'Dte.rut_receptor', 'Dte.razon_social_receptor', 'Dte.giro_receptor', 'Dte.neto', 'Dte.iva',
							'Dte.total', 'Dte.fecha', 'Dte.estado', 'Dte.pdf'
						),
						'order' => 'Dte.fecha DESC'
					),
					'VentaTransaccion' => array(
						'fields' => array(
							'VentaTransaccion.nombre', 'VentaTransaccion.monto', 'VentaTransaccion.fee', 'VentaTransaccion.created'
						)
					)
				),
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo', 'Venta.descuento', 'Venta.costo_envio',
					'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id', 'Venta.paquete_generado'
				)
			)
		);
		

		$venta['VentaMensaje'] = array(); //carga de mensajes de la venta	
		$venta['VentaExterna']['curriers'] = array(); //curriers para crear paquete linio
		$venta['VentaExterna']['tipodocumentos'] = array(); //tipo documentos
		
		//----------------------------------------------------------------------------------------------------
		//carga de mensajes de prestashop
		if (empty($venta['Marketplace']['id'])) {

			$ConexionPrestashop = new PrestaShopWebservice($venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'], false);

			$venta['VentaMensaje'] = $this->prestashop_obtener_venta_mensajes($ConexionPrestashop, $venta['Venta']['id_externo']);

		}

		else {
			
			//----------------------------------------------------------------------------------------------------
			//carga de mensajes de Linio
			if ($venta['Marketplace']['marketplace_tipo_id'] == 1) {

				$ConexionLinio = Client::create(new Configuration($venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key']));

				//$mensajes =  $this->linio_obtener_venta_mensajes($venta, $ConexionLinio);

				// Obtener detall venta externo
				$venta['VentaExterna'] = $this->linio_obtener_venta($venta['Venta']['id_externo'], $ConexionLinio, true);
				
				// Recorremos los productos
				foreach ($venta['VentaExterna']['Products'] as $ip => $detalle) {

					// Detalles de envio
					$venta['Envio'][$ip] = array(
						'id'                      => $detalle['OrderItemId'],
						'tipo'                    => $detalle['ShipmentProvider'],
						'estado'                  => $detalle['Status'],
						'direccion_envio'         => sprintf('%s, %s, %s', $venta['VentaExterna']['AddressShipping']['Address1'], $venta['VentaExterna']['AddressShipping']['Address2'], $venta['VentaExterna']['AddressShipping']['City']),
						'nombre_receptor'         => sprintf('%s %s', $venta['VentaExterna']['AddressShipping']['FirstName'], $venta['VentaExterna']['AddressShipping']['LastName']),
						'fono_receptor'           => $venta['VentaExterna']['AddressShipping']['Phone'],
						'producto'                => $detalle['Name'],
						'cantidad'                => 1, // No especifica
						'costo'                   => $detalle['ShippingAmount'],
						'fecha_entrega_estimada'  => $detalle['PromisedShippingTime'],
						'comentario'              => '',
						'mostrar_etiqueta'        => false,
						'paquete' 				  => false
					);

				}

				$tipodocumentos = array(
					'invoice', 'exportInvoice', 'shippingLabel', 'shippingParcel', 'carrierManifest', 'serialNumber'
				);

				$documentos = array();

				foreach ($tipodocumentos as $tdocumento) {
					$documentos[] = $this->linio_obtener_documentos($ConexionLinio, Hash::extract($venta['VentaExterna']['Products'], '{n}.OrderItemId'), $tdocumento);
				}
				
				$venta['VentaExterna']['curriers'] = $this->linio_obtener_curriers($ConexionLinio);
				$venta['VentaExterna']['tipodocumentos'] = array(
					'invoice', 'exportInvoice', 'shippingLabel', 'shippingParcel', 'carrierManifest', 'serialNumber'
				);

			}
			
			//----------------------------------------------------------------------------------------------------
			//carga de mensajes de mercado libre
			if ($venta['Marketplace']['marketplace_tipo_id'] == 2) {

				# Mercadolibre conectar
				$meliConexion = $this->admin_verificar_conexion_meli();
				
				self::$Mercadolibre = new Meli($venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token']);

				$mensajes = $this->mercadolibre_obtener_mensajes($venta['Marketplace']['access_token'], $venta['Venta']['id']);

				foreach ($mensajes as $mensaje) {

					$data = array();
					$data['mensaje'] = $this->removeEmoji($mensaje['text']['plain']);
					$data['fecha'] = CakeTime::format($mensaje['date'], '%d-%m-%Y %H:%M:%S');
					$data['asunto'] = $mensaje['subject'];
					$venta['VentaMensaje'][] = $data;
				}

				// Detalles de la venta externa
				$venta['VentaExterna'] = $this->mercadolibre_obtener_venta_detalles($venta['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);
				
				if (isset($venta['VentaExterna']['shipping']['id'])) {

					foreach ($venta['VentaExterna']['order_items'] as $ip => $detalle) { 
						// Detalles de envio
						$direccion_envio = '';
						$nombre_receptor = '';
						$fono_receptor   = '';
						$comentario      = '';

						if (isset($venta['VentaExterna']['shipping']['receiver_address']['address_line'])
							&& isset($venta['VentaExterna']['shipping']['receiver_address']['city']['name'])) {
							$direccion_envio = sprintf('%s, %s', $venta['VentaExterna']['shipping']['receiver_address']['address_line'], $venta['VentaExterna']['shipping']['receiver_address']['city']['name']);
						}

						if (isset($venta['VentaExterna']['shipping']['receiver_address']['receiver_name'])) {
							$nombre_receptor = $venta['VentaExterna']['shipping']['receiver_address']['receiver_name'];
						}

						if (isset($venta['VentaExterna']['shipping']['receiver_address']['receiver_phone'])) {
							$fono_receptor = $venta['VentaExterna']['shipping']['receiver_address']['receiver_phone'];
						}

						if (isset($venta['VentaExterna']['shipping']['receiver_address']['comment'])) {
							$comentario = $venta['VentaExterna']['shipping']['receiver_address']['comment'];
						}

						
						$venta['Envio'][$ip] = array(
							'id'                      => $venta['VentaExterna']['shipping']['id'],
							'tipo'                    => $venta['VentaExterna']['shipping']['shipping_option']['name'],
							'estado'                  => $venta['VentaExterna']['shipping']['status'],
							'direccion_envio'         => $direccion_envio,
							'nombre_receptor'         => $nombre_receptor,
							'fono_receptor'           => $fono_receptor,
							'producto'                => $detalle['item']['title'],
							'cantidad'                => $detalle['quantity'],
							'costo'                   => $venta['VentaExterna']['shipping']['shipping_option']['cost'],
							'fecha_entrega_estimada'  => (isset($venta['VentaExterna']['shipping']['shipping_option']['estimated_delivery_time'])) ? CakeTime::format($venta['VentaExterna']['shipping']['shipping_option']['estimated_delivery_time']['date'], '%d-%m-%Y %H:%M:%S') : __('No especificado') ,
							'comentario'              => $comentario,
							'mostrar_etiqueta'        => ($venta['VentaExterna']['shipping']['status'] == 'ready_to_ship') ? true : false,
							'paquete' 				  => false
						);	
					}	
				}
			}

		}
		
		BreadcrumbComponent::add('Listado de ventas', '/ventas');
		BreadcrumbComponent::add('Detalles de Venta');
		
		$this->set(compact('venta'));

	}


	/**
	 * Permite decontar desde bodega la cantidad de productos solicitadas por una venta.
	 * @return [type] [description]
	 */
	public function admin_procesar_ventas($id = null)
	{	

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}


		if ($this->request->is('post') || $this->request->is('put')) {


			$aceptados = array();
			$erorres   = array();

			foreach ($this->request->data['VentaDetalle'] as $id => $detalle) {

				# vemos la cantidad de existencia que hay en bodega principal.
				$enBodega = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($detalle['venta_detalle_producto_id']);
				$enBodegas = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodegas($detalle['venta_detalle_producto_id']);

				# Guardar resultados del detalle

				if ($enBodega < $detalle['cantidad_entregar']) {
					$errores[] = 'Item ' . ClassRegistry::init('VentaDetalleProducto')->field('nombre', $detalle['venta_detalle_producto_id']) . ' no puede ser retirado: Stock bodega principal ('.$enBodega.') - Stock global ('.$enBodegas.') - Vendidos ('.$detalle['cantidad_entregar'].')';
					continue;
				}elseif ($detalle['cantidad_entregar'] > 0) {

					$this->request->data['VentaDetalle'][$id]['cantidad_pendiente_entrega'] = $detalle['cantidad'] - $detalle['cantidad_entregar'];

					if (ClassRegistry::init('Bodega')->crearSalidaBodega($detalle['venta_detalle_producto_id'], null, $detalle['cantidad_entregar'], 'OC')) {
						$aceptados[] = 'Item ' . ClassRegistry::init('VentaDetalleProducto')->field('nombre', $detalle['venta_detalle_producto_id']) . ': Se descontaron ' . $detalle['cantidad_entregar'] . ' items de bodega principal';

						
						$this->request->data['VentaDetalle'][$id]['cantidad_entregada'] = $detalle['cantidad_entregar'];

						# si no quedan pendientes se marca como completado
						if (!$this->request->data['VentaDetalle'][$id]['cantidad_pendiente_entrega']) {
							$this->request->data['VentaDetalle'][$id]['completo'] = 1;
						}

					}else{
						$errores[] = 'Item ' . ClassRegistry::init('VentaDetalleProducto')->field('nombre', $detalle['venta_detalle_producto_id']) . ' no puede ser retirado: Stock bodega principal ('.$enBodega.') - Stock global ('.$enBodegas.') - Vendidos ('.$detalle['cantidad_entregar'].')';
					}
				}else{
					$this->request->data['VentaDetalle'][$id]['cantidad_pendiente_entrega'] = $detalle['cantidad'];
				}
			}

			
			# Sub estados de la venta
			if (array_sum(Hash::extract($this->request->data['VentaDetalle'], '{n}.cantidad_pendiente_entrega')) > 0 ) {
				$this->request->data['Venta']['subestado_oc'] = 'parcialmente_entregado';
				$this->Session->setFlash('La venta se ha marcado como parcialmente entregado. Se recordará vía email la reposición del/los productos faltantes.', null, array(), 'warning');
			}else{
				$this->request->data['Venta']['subestado_oc'] = 'entregado';
			}


			if (!empty($aceptados)) {
				$this->Session->setFlash($this->crearAlertaUl($aceptados, 'Correcto'), null, array(), 'success');
			}

			if (!empty($errores)) {
				$this->Session->setFlash($this->crearAlertaUl($errores, 'Errores'), null, array(), 'danger');
			}

			
			if ($this->Venta->saveAll($this->request->data, array('callbacks' => false))) {
				$this->redirect(array('action' => 'index'));
			}
			

		}else {

			$this->request->data = $this->Venta->find('first', array(
				'conditions' => array(
					'Venta.id' => $id,
				),
				'contain' => array(
					'VentaDetalle' => array(
						'VentaDetalleProducto' => array(
							'Bodega'
						)
					),
					'VentaEstado' => array(
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.permitir_retiro_oc', 'VentaEstado.nombre'
						)
					)
				),
				'fields' => array(
					'Venta.id'
				)
			));

		}


		BreadcrumbComponent::add('Ventas ', '/ventas');
		BreadcrumbComponent::add('Retirar productos ');

		

	} 


	/**
	 * Obtiene los documentos adjuntos de una venta en mercadolibre
	 * @param  string 	$access_token token de acceso a meli
	 * @param  array 	$detallesVenta Arreglo con la información de la venta
	 * @param  string 	$type 		  formato de retorno         	
	 * @return OBJ
	 */
	public function mercadolibre_obtener_etiqueta_envio($access_token, $detallesVenta, $type = 'zpl2')
	{	
		if (isset($detallesVenta['shipping']['id'])) {
			
			// No hay documentos
			if ($detallesVenta['shipping']['status'] != 'ready_to_ship') {
				return array();
			}

			$shipping_id = $detallesVenta['shipping']['id'];

			$curl = curl_init();

			curl_setopt_array($curl, array(
			  CURLOPT_URL => "https://api.mercadolibre.com/shipment_labels?shipment_ids=".$shipping_id."&response_type=".$type."&access_token=" . $access_token,
			  CURLOPT_RETURNTRANSFER => true,
			  CURLOPT_ENCODING => "",
			  CURLOPT_MAXREDIRS => 10,
			  CURLOPT_TIMEOUT => 30,
			  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
			  CURLOPT_CUSTOMREQUEST => "GET",
			  CURLOPT_HTTPHEADER => array(
			    "Cache-Control: no-cache",
			  ),
			));

			$response = curl_exec($curl);
			$err = curl_error($curl);

			curl_close($curl);

			if ($err) {
			  	echo "cURL Error #:" . $err;
			} else {
				header('Content-type:application/zip');
				header('Content-Disposition:attachment;filename="'.$shipping_id.'.zip"');

			  	echo $response;
			}
		}

		exit;
	}


	/**
	 * Referencia: https://sellerapi.sellercenter.net/docs/setstatustoreadytoship
	 * @param  [type] $conexion [description]
	 * @param  [type] $id       [description]
	 * @return [type]           [description]
	 */
	public function linio_listo_para_envio($conexion, $items = array(), $delivery = '', $currier = '', $tracking = '')
	{
		$orderItemIds     = $items; // Please change the set of Order Item IDs for Your system.
		$deliveryType     = $delivery;
		$shipmentProvider = $currier;
		$trackingNumber   = $tracking;

		$response = Endpoints::order()
		    ->setStatusToReadyToShip($orderItemIds, $deliveryType, $shipmentProvider, $trackingNumber)
		    ->call($conexion);

		if ($response instanceof SuccessResponseInterface) {
			return true;
		} else {
		    return false;
		}
	}


	public function linio_obtener_curriers($conexion )
	{
		$response = $conexion->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetShipmentProviders',
		        GenericRequest::V1
		    ))
		);

		$curriers = $response->getBody()['ShipmentProviders']['ShipmentProvider'];

		if (!isset($curriers[0])) {
			$curriers[0] = $curriers;
		}		

		return $curriers;
	}



	public function admin_linio_generar_paquete($id)
	{	
		if ($this->request->is('POST') || $this->request->is('PUT')) {

			$venta = $this->Venta->find(
				'first',
				array(
					'conditions' => array(
						'Venta.id' => $id,
						'Venta.paquete_generado' => false
					),
					'contain' => array(
						
						'Marketplace' => array(
							'fields' => array(
								'Marketplace.id', 'Marketplace.nombre', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id',
								'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key',
								'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token'
							)
						)
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.paquete_generado'
					)
				)
			);
			
			$ConexionLinio = Client::create(new Configuration($venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key']));

			$orderItemIds     = json_decode($this->request->data['Venta']['OrderItemIds']); // Please change the set of Order Item IDs for Your system.
			$deliveryType     = 'dropship';
			$shipmentProvider = $this->request->data['Venta']['ShippingProvider'];

			$response = Endpoints::order()
			    ->setStatusToPackedByMarketplace($orderItemIds, $deliveryType, $shipmentProvider)
			    ->call($conexion);
			if ($response instanceof SuccessResponseInterface) {
			    
				//$detalleVenta = $this->mercadolibre_obtener_venta_detalles($venta['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);
				$tipodocumentos = array(
					'invoice', 'exportInvoice', 'shippingLabel', 'shippingParcel', 'carrierManifest', 'serialNumber'
				);

				$documentos = array();

				foreach ($tipodocumentos as $tdocumento) {
					$documentos[] = $this->linio_obtener_documentos($ConexionLinio, $orderItemIds, $tdocumento);
				}

				$this->Venta->id = $id;
				
				$this->Venta->saveField('Venta.documento', json_encode($documentos));
				$this->Session->setFlash('Paquete creado con éxito.', null, array(), 'success');
				$this->redirect(array('action' => 'view', $id));


			} else {

			    $this->Session->setFlash('No fue posible cambiar el estado del pedido.', null, array(), 'danger');
				$this->redirect(array('action' => 'view', $id));
			
			}	
		}
	}


	public function linio_obtener_documentos($conexion, $items = array(), $type = '')
	{	
		$orderItemIds = $items; // Please change the set of Order Item IDs for Your system.
		$documentType = $type;

		$response = Endpoints::order()->getDocument($orderItemIds, $documentType)->call($conexion);

		if ($response instanceof ErrorResponse) {
		    return '';
		} else {
		    $doc = $response->getDocument();

		    $res = array(
				'doctype'  => $doc->getType(),
				'mimeType' => $doc->getMimeType(),
				'pdf'      => $doc->getRawFile()
		    );

		    return $res;
		}

	}


	public function linio_generar_pdf($pdf){

	}


	public function admin_obtener_etiqueta($id, $tipo = '') 
	{

		$venta = $this->request->data = $this->Venta->find(
			'first',
			array(
				'conditions' => array(
					'Venta.id' => $id
				),
				'contain' => array(
					'VentaDetalle' => array(
						'VentaDetalleProducto' => array(
							'fields' => array(
								'VentaDetalleProducto.id', 'VentaDetalleProducto.nombre'
							)
						),
						'conditions' => array(
							'VentaDetalle.activo' => 1
						),
						'fields' => array(
							'VentaDetalle.id', 'VentaDetalle.venta_detalle_producto_id', 'VentaDetalle.precio', 'VentaDetalle.cantidad', 'VentaDetalle.venta_id'
						)
					),
					'VentaEstado' => array(
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
							)
						),
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.venta_estado_categoria_id'
						)
					),
					'Tienda' => array(
						'fields' => array(
							'Tienda.id', 'Tienda.nombre', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop'
						)
					),
					'Marketplace' => array(
						'fields' => array(
							'Marketplace.id', 'Marketplace.nombre', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id',
							'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key',
							'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token'
						)
					),
					'MedioPago' => array(
						'fields' => array(
							'MedioPago.id', 'MedioPago.nombre'
						)
					),
					'VentaCliente' => array(
						'fields' => array(
							'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono', 'VentaCliente.created'
						)
					),
					'Dte' => array(
						'fields' => array(
							'Dte.id', 'Dte.folio', 'Dte.tipo_documento', 'Dte.rut_receptor', 'Dte.razon_social_receptor', 'Dte.giro_receptor', 'Dte.neto', 'Dte.iva',
							'Dte.total', 'Dte.fecha', 'Dte.estado'
						),
						'order' => 'Dte.fecha DESC'
					)
				),
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo', 'Venta.descuento', 'Venta.costo_envio',
					'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id'
				)
			)
		);
		

		if ($venta['Marketplace']['marketplace_tipo_id'] == 1) {
			
			$ConexionLinio = Client::create(new Configuration($venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key']));

			$detallesVenta = $this->linio_obtener_venta($venta['Venta']['id_externo'], $ConexionLinio, true);

			$documento = $this->linio_obtener_documentos($ConexionLinio, Hash::extract($detallesVenta['Products'], '{n}.OrderItemId'), $tipo);

			if (!empty($documento)) {
				$this->ver_documento($documento['mimeType'], $documento['pdf']);
			}else{
				exit;
			}
			
		}

		if ($venta['Marketplace']['marketplace_tipo_id'] == 2) {

			$this->Mercadolibre = new Meli($venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token']);

			// Detalles de la venta externa
			$venta['VentaExterna'] = $this->mercadolibre_obtener_venta_detalles($venta['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);

			$this->mercadolibre_obtener_etiqueta_envio($venta['Marketplace']['access_token'], $venta['VentaExterna']);
			
		}
	}


	public function ver_documento($mimetype = '', $cuerpo = '')
	{
		header('Content-type:' . $mimetype);

		switch ($mimetype) {
			case 'application/pdf':
				header('Content-Disposition:inline;filename="'.rand().'.pdf"');
				break;
			case 'application/zip':
				header('Content-Disposition:attachment;filename="'.rand().'.zip"');
				break;
		}

		echo base64_decode($cuerpo);
		exit;
		
	}

}
