<?
App::uses('Component', 'Controller');

// Librería Prestashop
require_once (__DIR__ . '/../../Vendor/PSWebServiceLibrary/PSWebServiceLibrary.php');

class PrestashopComponent extends Component
{	
	public $ConexionPrestashop;


	public function crearCliente($apiurl, $apikey, $opt = false)
	{	
		$this->ConexionPrestashop = new PrestaShopWebservice($apiurl, $apikey, $opt);
	}


	/****************************************************************************************************/
	//obtiene las órdenes de una tienda
	public function prestashop_obtener_ventas ($tienda_id, $ultima_venta = array()) {

		$opt = array();
		$opt['resource'] = 'orders';
		$opt['display'] = '[id,id_customer,id_carrier,current_state,date_add,payment,total_discounts_tax_incl,total_paid,total_products,total_shipping_tax_incl,reference,id_address_delivery]';


		//$venta['Venta']['id_externo'] = 8300;

		if (!empty($ultima_venta)) {
			$opt['filter[id]'] = '>[' .$ultima_venta['Venta']['id_externo']. ']';
		}

		$opt['filter[id_customer]'] = '>[0]';

		$xml                 = $this->ConexionPrestashop->get($opt);
		
		$PrestashopResources = $xml->children()->children();
		
		$DataVentas          = to_array($PrestashopResources);
		
		return $DataVentas;

	}


	/**
	 * Método antiguo para obtener ventas desde prestashop
	 * @param  array  $params [description]
	 * @param  array  $tienda [description]
	 * @return [type]         [description]
	 */
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
				$result['order'][$key]['id_carrier']               = $value['Orden']['id_carrier'];
			}
		}

		return $result;

	}



	/**
	 * Método antiguo para obtener ventas desde prestashop
	 * @param  array  $params [description]
	 * @param  array  $tienda [description]
	 * @return [type]         [description]
	 */
	private function prestashop_obtener_venta_antiguo($params = array(), $tienda = array())
	{	
		App::uses('AppController', 'Controller');
		$app = new AppController();

		# Modelos que requieren agregar configuración
		$app->cambiarDatasource(array('Orden'), $tienda);

		$orden	= ClassRegistry::init('Orden')->find('first', $params);

		$result = array();

		if (!empty($orden)) {

			$result['order']['id']                       = $orden['Orden']['id_order'];
			$result['order']['id_address_delivery']      = $orden['Orden']['id_address_delivery'];
			$result['order']['id_customer']              = $orden['Orden']['id_customer'];
			$result['order']['current_state']            = $orden['Orden']['current_state'];
			$result['order']['date_add']                 = $orden['Orden']['date_add'];
			$result['order']['payment']                  = $orden['Orden']['payment'];
			$result['order']['total_discounts_tax_incl'] = $orden['Orden']['total_discounts_tax_incl'];
			$result['order']['total_paid']               = $orden['Orden']['total_paid'];
			$result['order']['total_products']           = $orden['Orden']['total_products'];
			$result['order']['total_shipping_tax_incl']  = $orden['Orden']['total_shipping_tax_incl'];
			$result['order']['reference']                = $orden['Orden']['reference'];
			$result['order']['id_carrier']               = $orden['Orden']['id_carrier'];
			
		}

		return $result;

	}



	/****************************************************************************************************/
	//obtiene las órdenes por id
	public function prestashop_obtener_venta ($id, $tienda = array()) {

		if (empty($tienda)) {
			$opt = array();
			$opt['resource'] = 'orders';
			$opt['display'] = '[id,id_customer,id_carrier,current_state,date_add,payment,total_discounts_tax_incl,total_paid,total_products,total_shipping_tax_incl,reference,id_address_delivery,id_address_invoice]';
			$opt['filter[id]'] = '[' .$id. ']';

			$xml                 = $this->ConexionPrestashop->get($opt);
			
			$PrestashopResources = $xml->children()->children();
			
			$DataVenta          = to_array($PrestashopResources);
		}else{
			$DataVenta          = $this->prestashop_obtener_venta_antiguo( array('conditions' => array('Orden.id_order' => $id)), $tienda );
		}
		
		
		if (empty($DataVenta)) {
			return array();
		}

		return $DataVenta['order'];

	}


	/**
	 * Obtiene la dirección de entrega de una venta
	 * @param  [type] $id                 id_address
	 * @return [type]                     [description]
	 */
	public function prestashop_obtener_venta_direccion($id)
	{
		$opt = array();
		$opt['resource'] = 'addresses';
		$opt['display'] = '[id,firstname,lastname,address1,address2,city,other,dni,phone,phone_mobile,deleted,id_state]';
		$opt['filter[id]'] = '[' .$id. ']';
		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$Address = to_array($PrestashopResources);

		return $Address;
	}



	public function prestashop_obtener_comuna_por_id($id)
	{	
		$opt = array();
		$opt['resource'] = 'states';
		$opt['display'] = '[id,name]';
		$opt['filter[id]'] = '[' .$id. ']';
		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$commune = to_array($PrestashopResources);

		return $commune;
	}


	/****************************************************************************************************/
	//obtiene el detalle de una venta
	public function prestashop_obtener_venta_detalles ($venta_id) 
	{
		$opt = array();
		$opt['resource'] = 'order_details';
		$opt['display'] = '[product_id,product_name,product_quantity,unit_price_tax_excl,unit_price_tax_incl]';
		$opt['filter[id_order]'] = '[' .$venta_id. ']';

		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$DataVentaDetalle = to_array($PrestashopResources);

		return $DataVentaDetalle;

	}


	/****************************************************************************************************/
	//obtiene las transacciones de una venta
	public function prestashop_obtener_venta_transacciones ($referencia) 
	{
		$opt = array();
		$opt['resource'] = 'order_payments';
		$opt['display'] = '[transaction_id,amount]';
		$opt['filter[order_reference]'] = '[' .$referencia. ']';

		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$DataVentaDetalle = to_array($PrestashopResources);

		return $DataVentaDetalle;

	}


	/**
	 * Obitnen y cre el metodo si no existe
	 * @param  [type]  $id_carrier [description]
	 * @param  boolean $create     [description]
	 * @return [type]              [description]
	 */
	public function prestashop_obtener_transportista($id_carrier)
	{
		$opt = array();
		$opt['resource'] = 'carriers';
		$opt['display'] = '[name,id]';
		$opt['filter[id]'] = '[' .$id_carrier. ']';

		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$DataVentaDetalle = to_array($PrestashopResources);

		$MetodoEnvio = ClassRegistry::init('MetodoEnvio')->find('first',
			array(
				'conditions' => array(
					'MetodoEnvio.nombre' => @trim($DataVentaDetalle['carrier']['name'])
				),
				'fields' => array(
					'MetodoEnvio.id' 
				)
			)
		);

		if (!empty($MetodoEnvio)) {
			return $MetodoEnvio['MetodoEnvio']['id'];
		}

		//si el metodo no existe, se crea
		$data = array();
		$data['MetodoEnvio']['nombre'] = $DataVentaDetalle['carrier']['name'];

		ClassRegistry::init('MetodoEnvio')->create();
		ClassRegistry::init('MetodoEnvio')->save($data);

		return ClassRegistry::init('MetodoEnvio')->id;
	}


	/****************************************************************************************************/
	//obtiene el estado de venta
	public function prestashop_obtener_venta_estado ($estado_id) 
	{
		$opt               = array();
		$opt['resource']   = 'order_states';
		$opt['display']    = '[name]';
		$opt['filter[id]'] = '[' .$estado_id. ']';

		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$estado = to_array($PrestashopResources);

		if (!isset($estado['order_state'])) {
			return 1; // Sin estado
		}
		
		$VentaEstado = ClassRegistry::init('VentaEstado')->find(
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

		ClassRegistry::init('VentaEstado')->create();
		ClassRegistry::init('VentaEstado')->save($data);

		return ClassRegistry::init('VentaEstado')->id;

	}

	public function prestashop_obtener_estado_por_nombre($estado)
	{	
		try {
			$opt                 = array();
			$opt['resource']     = 'order_states';
			$opt['display']      = '[id,name]';
			$opt['limit']        = 1;
			$opt['filter[name]'] = '[' .$estado. ']';

			$xml = $this->ConexionPrestashop->get($opt);

			$PrestashopResources = $xml->children()->children();

			$estado = to_array($PrestashopResources);	
		} catch (PrestaShopWebserviceException $ex) {
			
		}

		if (!isset($estado['order_state'])) {
			return array(); // Sin estado
		}

		return $estado['order_state'];
	}


	public function prestashop_cambiar_estado_venta($id, $estado_id, $apiurl = '')
	{	
		# Cambiamos directamente el estado actual del pedido y hace el envio correspondiente de emails. ¡Wena CTM!
		return $this->prestashop_cambiar_estado_actual_venta($id, $estado_id);

		# Se crea un registro en orderhistory y luego se setea el campo current_state de la orden o venta
		$ordenHistoria = false;
        
        # Obtenemos el esquema del modelo
		try {
			$opt = array('resource' => 'order_histories');			
			$xml = $this->ConexionPrestashop->get(array('url' => $apiurl.'/api/order_histories?schema=blank'));
			$resources = $xml->children()->children();
			
		} catch (PrestaShopWebserviceException $ex) {
			return false;
		}

		foreach ($resources as $nodeKey => $node)
		{	
			$resources->id_employee = 1;
			$resources->id_order_state = $estado_id;
			$resources->id_order = $id;
			$resources->date_add = date('Y-m-d H:i:s');
		}

		try {
			
			$opt['postXml'] = $xml->asXML();
			$xml = $this->ConexionPrestashop->add($opt);
			
			$ordenHistoria = true;

		}
		catch (PrestaShopWebserviceException $ex)
		{	
			#prx($ex->getMessage());
		}

		if ($ordenHistoria) {
			return true;
			return $this->prestashop_cambiar_estado_actual_venta($id, $estado_id);
		}

		return false;
	}



	public function prestashop_cambiar_estado_actual_venta($id_venta, $estado_id, $forzar = true)
	{
		try {

			$opt                       = array();
			$opt['resource']           = 'orders';
			$opt['id'] = $id_venta;

			$xml       = $this->ConexionPrestashop->get($opt);
			$resources = $xml->children()->children();
			
			$resources->current_state = $estado_id;
			
			$opt           = array('resource' => 'orders');
			$opt['putXml'] = $xml->asXML();
			$opt['id'] 	   = $id_venta; 
			$xml           = $this->ConexionPrestashop->edit($opt);
			
		} catch (PrestaShopWebserviceException $ex) {
			//prx($ex->getMessage());
			 // No actualizado
			if ($forzar) { // Algo pasa en prestashop que retorna un error 500 pero de todas maneras actualiza el estado.
				return true;
			}

			return false;
		}

		return true;
	}


	/****************************************************************************************************/
	//obtiene el id de un medio de pago
	public function prestashop_obtener_medio_pago ($medio_pago) 
	{
		$MedioPago = ClassRegistry::init('MedioPago')->find(
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

		ClassRegistry::init('MedioPago')->create();
		ClassRegistry::init('MedioPago')->save($data);

		return ClassRegistry::init('MedioPago')->id;

	}



	/****************************************************************************************************/
	//obtiene el estado de venta
	public function prestashop_obtener_cliente ($cliente_id, $todo = false) 
	{
		$opt = array();
		$opt['resource'] = 'customers';
		$opt['display'] = '[firstname,lastname,email]';
		$opt['filter[id]'] = '[' .$cliente_id. ']';

		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$cliente = to_array($PrestashopResources);


		if ($todo) {
			return $cliente;
		}

		$VentaCliente = ClassRegistry::init('VentaCliente')->find(
			'first',
			array(
				'conditions' => array(
					'VentaCliente.email' => @$cliente['customer']['email']
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

		ClassRegistry::init('VentaCliente')->create();
		ClassRegistry::init('VentaCliente')->save($data);

		return ClassRegistry::init('VentaCliente')->id;

	}


	/**
	 * Obtiene los mensajes de un pedido dese prestashop
	 * @param  int 		$venta_id_externo  	id venta   
	 * @return array()
	 */
	public function prestashop_obtener_venta_mensajes($venta_id_externo, $limit = 2)
	{
		$res = array();

		$opt = array();
		$opt['resource'] = 'customer_threads';
		$opt['display'] = '[id]';
		$opt['filter[id_order]'] = '[' .$venta_id_externo. ']';

		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		if (!empty($PrestashopResources)) {

			$CustomerThread = to_array($PrestashopResources);

			$opt = array();
			$opt['resource']                   = 'customer_messages';
			$opt['display']                    = '[message,date_add]';
			$opt['filter[id_customer_thread]'] = '[' .@$CustomerThread['customer_thread']['id']. ']';
			$opt['sort']                       = '[id_customer_thread_DESC]';
			$opt['limit']                      = $limit;

			$xml = $this->ConexionPrestashop->get($opt);

			$PrestashopResources = $xml->children()->children();

			$mensajes = to_array($PrestashopResources);

			if (empty($mensajes)) {
				return $res;
			}
			
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


	public function prestashop_crear_mensaje($venta_id_externo, $mensaje = '')
	{
		# Obtenemos el esquema del modelo
		try {	
			$xml = $this->ConexionPrestashop->get(array('url' => $this->ConexionPrestashop->get_url().'/api/customer_threads?schema=blank'));
			$resources = $xml->children()->children();
			
		} catch (PrestaShopWebserviceException $ex) {
			return false;
		}

		# Obtenemos hilo de los mensajes
		$opt = array();
		$opt['resource'] = 'customer_threads';
		$opt['display'] = '[id]';
		$opt['filter[id_order]'] = '[' .$venta_id_externo. ']';

		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		debug($venta_id_externo);

		prx($PrestashopResources);

		# Obtenemos el esquema del modelo
		try {		
			$xml2 = $this->ConexionPrestashop->get(array('url' => $this->ConexionPrestashop->get_url().'/api/customer_messages?schema=blank'));
			$resources2 = $xml2->children()->children();
			
		} catch (PrestaShopWebserviceException $ex) {
			return false;
		}

		debug($resources);

		# Obtenemos la venta
		$venta   = $this->prestashop_obtener_venta($venta_id_externo);
		$cliente = $this->prestashop_obtener_cliente($venta['id_customer'], true);
		
		/*
		# Datos
		foreach ($resources as $nodeKey => $node)
		{	
			$resources->id_order   = $venta_id_externo;
			$resources->id_shop    = 1;
			$resources->id_lang    = 1;
			$resources->id_contact = 0;
			$resources->id_product = 0;
			$resources->status     = 'open';
			$resources->token 	   = ClassRegistry::init('Token')->generar_token(12);
			$resources->email      = $cliente['customer']['email'];
			$resources->date_add   = date('Y-m-d H:i:s');
			$resources->date_upd   = date('Y-m-d H:i:s');
		}
		
		try {		
			$opt['postXml']  = $xml->asXML();
			$opt['resource'] = 'customer_threads';
			$xml = $this->ConexionPrestashop->add($opt);
		}
		catch (PrestaShopWebserviceException $ex)
		{	
			//prx($ex->getMessage());
		}*/

		prx($resources2);
		foreach ($resources2 as $nodekey => $node) {
			# code...
		}


		# Crear mensaje



	}


	/**
	 * Méodo utilizado por los marketplace que tengan como
	 * identificadro externo a la referencia del producto
	 * @param  string $referencia [description]
	 * @return [type]             [description]
	 */
	public function prestashop_obtener_idproducto($referencia = '')
	{
		$opt = array();
		$opt['resource'] = 'products';
		$opt['display'] = '[id]';
		$opt['filter[reference]'] = '[' .$referencia. ']';

		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$producto = to_array($PrestashopResources);

		return $producto;
	}


	/**
	 * [prestashop_obtener_stock_producto description]
	 * @param  [type] $producto_id  id prestashop del producto
	 * @return array               resultado operación
	 */
	public function prestashop_obtener_stock_producto($producto_id)
	{	
		//se obtiene el stock de prestashop
		$opt = array();
		$opt['resource'] = 'stock_availables';
		$opt['display'] = '[quantity]';
		$opt['filter[id_product]'] = '[' .$producto_id. ']';

		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$stock = to_array($PrestashopResources);

		return $stock;
	}


	/**
	 * @param  string $id [description]
	 * @return [type]             [description]
	 */
	public function prestashop_producto_existe($id = '')
	{
		$opt             = array();
		$opt['resource'] = 'products';
		$opt['id']       = $id;

		$detalle = array(
			'existe' => 0,
			'item' => array()
		);

		try {
			$xml = $this->ConexionPrestashop->get($opt);
		
			$PrestashopResources = $xml->children()->children();
			
			$producto = to_array($PrestashopResources);
			
			if (!empty($producto)) {

				$stock = $this->prestashop_obtener_stock_producto($id);
				
				$producto['precio']           = round($producto['price'] * 1.19);
				$producto['estado']           = ($producto['active']) ? 'Activo' : 'Inactivo';
				$producto['stock_disponible'] = $stock['stock_available']['quantity'];
				$detalle['item']              = $producto;
				$detalle['existe']            = 1;

			}	
		} catch (Exception $e) {
			// No existe en prestashop
		}

		return $detalle;

	}


	/**
	 * [prestashop_obtener_producto description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function prestashop_obtener_producto($id)
	{
		$opt             = array();
		$opt['resource'] = 'products';
		$opt['id']       = $id;

		$producto = array();

		try {
			$xml = $this->ConexionPrestashop->get($opt);
		
			$PrestashopResources = $xml->children()->children();
			
			$producto = to_array($PrestashopResources);

				
		} catch (Exception $e) {
			// No existe en prestashop
		}

		return $producto;
	}


	public function prestashop_obtener_descuento_producto($id, $monto = 0) 
	{
		$opt                       = array();
		$opt['resource']           = 'specific_prices';
		$opt['filter[id_product]'] = $id;
		$opt['display']            = 'full';

		$descuentos = array();

		try {
			$xml = $this->ConexionPrestashop->get($opt);
		
			$PrestashopResources = $xml->children()->children();
			
			$descuentos = to_array($PrestashopResources);

				
		} catch (Exception $e) {
			// No existe en prestashop
		}

		if (empty($descuentos))
			return 0;


		if (count($descuentos) == 1) {
			$descuentos[0] = $descuentos;
			unset($descuentos['specific_price']);	
		}

		$hoy = date('Y-m-d H:i:s');

		$descuento = array();;

		foreach ($descuentos as $id => $d) {
			
			if ($d['specific_price']['from'] == '0000-00-00 00:00:00' && $d['specific_price']['to'] == '0000-00-00 00:00:00') {
				$descuento['tipo']  = $d['specific_price']['reduction_type'];
				$descuento['valor'] = $d['specific_price']['reduction'];
			}

			if ($d['specific_price']['from'] == '0000-00-00 00:00:00' && $d['specific_price']['to'] >= $hoy) {
				$descuento['tipo']  = $d['specific_price']['reduction_type'];
				$descuento['valor'] = $d['specific_price']['reduction'];
			}

			if ($d['specific_price']['from'] <= $hoy && $d['specific_price']['to'] == '0000-00-00 00:00:00') {
				$descuento['tipo']  = $d['specific_price']['reduction_type'];
				$descuento['valor'] = $d['specific_price']['reduction'];
			}

			if ($d['specific_price']['from'] <= $hoy && $d['specific_price']['to'] <= $hoy ) {
				$descuento['tipo']  = $d['specific_price']['reduction_type'];
				$descuento['valor'] = $d['specific_price']['reduction'];
			}
		}

		$descuento_monto = 0;

		if ($descuento['tipo'] == 'percentage') {
			$descuento_monto = $monto * $descuento['valor'];
		}else{
			$descuento_monto = $descuento['valor'];
		}

		return $descuento_monto;
	}


	/**
	 * Actualiza el stock disponible en prestashop
	 * @param  [type] $stock_id   Identificador del stock (no id de producto)
	 * @param  [type] $NuevoStock Nueva cantidad
	 * @return bool             
	 */
	public function prestashop_actualizar_stock($stock_id, $NuevoStock)
	{	

		return false;

		try {

			$opt                       = array();
			$opt['resource']           = 'stock_availables';
			$opt['id'] = $stock_id;

			$xml       = $this->ConexionPrestashop->get($opt);
			$resources = $xml->children()->children();
			
			$resources->quantity = $NuevoStock;
			
			$opt           = array('resource' => 'stock_availables');
			$opt['putXml'] = $xml->asXML();
			$opt['id'] 	   = $stock_id; 
			$xml           = $this->ConexionPrestashop->edit($opt);
			
		} catch (PrestaShopWebserviceException $ex) {
			//prx($ex->getMessage());
			 // No actualizado
			return false;
		}

		return true;

	}


	/**
	 * [prestashop_activar_desactivar_producto description]
	 * @param  [type]  $id     [description]
	 * @param  integer $activo [description]
	 * @return [type]          [description]
	 */
	public function prestashop_activar_desactivar_producto($id, $activo = 1)
	{	

		return false;

		try {

			$opt                       = array();
			$opt['resource']           = 'products';
			$opt['id'] = $id;

			$xml       = $this->ConexionPrestashop->get($opt);
			$resources = $xml->children()->children();
			
			$resources->active = $activo;
			
			$opt           = array('resource' => 'products');
			$opt['putXml'] = $xml->asXML();
			$opt['id'] 	   = $id; 
			$xml           = $this->ConexionPrestashop->edit($opt);
			
		} catch (PrestaShopWebserviceException $ex) {
			
			 // No actualizado
			return false;
		}

		return true;

	}

	/**
	 * Obtiene todos los productos publicados en prestashop (solo los activos)
	 * @param  array  $filter   arreglo con la condición de búqueda de productos (por defecto trae los roductos activos)
	 * @return array productos
	 */
	public function prestashop_obtener_productos($filter = array('filter[active]' => '[1]'))
	{	
		ini_set('max_execution_time', 0);

		$opt             = array();
		$opt['display'] = '[id,name,price,active,quantity,supplier_reference,id_manufacturer,id_supplier,id_default_image,link_rewrite,width,height,depth,weight]';
		
		$opt['resource'] = 'products';

		foreach ($filter as $field => $value) {
			$opt = array_replace_recursive($opt, array($field => $value));
		}

		$productos = array();

		try {
			$xml = $this->ConexionPrestashop->get($opt);
		
			$PrestashopResources = $xml->children()->children();
			
			$productos = to_array($PrestashopResources);

		} catch (Exception $e) {
			// No existe en prestashop
		}
		
		return $productos;
	}


	/**
	 * [prestashop_obtener_imagenes_producto description]
	 * @param  [type] $id_product [description]
	 * @param  string $host       [description]
	 * @return [type]             [description]
	 */
	public function prestashop_obtener_imagenes_producto($id_product = null, $host = '')
	{
		
		$filtro = array(
			'filter[id]' => '['.$id_product.']',
			'display' => 'full'
		);

		$res = $this->prestashop_obtener_productos($filtro);

		$imagenes = array();

		if (!empty($res['product']['associations']) && !empty($res['product']['associations']['images']) && isset($res['product']['associations']['images']['image'])) {
			foreach ($res['product']['associations']['images']['image'] as $im => $image_id) {

				$id = (isset($image_id['id'])) ? $image_id['id'] : $image_id ; 

				$imagenes[$id]['url'] = $this->ConexionPrestashop->get_url() . $id . '-full_default/' . $res['product']['link_rewrite']['language'] . '.jpg';

				if ($id == $res['product']['id_default_image']) {
					$imagenes[$id]['principal'] = 1;
				}else{
					$imagenes[$id]['principal'] = 0;
				}
			}
		}

		if ($id_product == 8987) {
			//prx($imagenes);
		}

		return $imagenes;
		
	}

	/**
	 * [prestashop_crear_imagen_url description]
	 * @param  string $id_imagen                [description]
	 * @param  string $nombre_amigable_producto [description]
	 * @param  string $host                     [description]
	 * @return [type]                           [description]
	 */
	public function prestashop_crear_imagen_url($id_imagen = '', $nombre_amigable_producto = '' , $host = '')
	{
		return $this->ConexionPrestashop->get_url() . $id_imagen . '-full_default/' . $nombre_amigable_producto . '.jpg';
	}


	/**
	 * [prestashop_obtener_proveedores description]
	 * @return [type] [description]
	 */
	public function prestashop_obtener_categorias( $id_padre = '' )
	{	
		ini_set('max_execution_time', 0);

		$opt             = array();
		$opt['display'] = '[id,name]';
		$opt['filter[active]'] = '[1]';
		$opt['resource'] = 'categories';

		if (!empty($id_padre)) {
			$opt['filter[id_parent]'] = $id_padre;
		}

		$proveedores = array();

		try {
			$xml = $this->ConexionPrestashop->get($opt);
		
			$PrestashopResources = $xml->children()->children();
			
			$proveedores = to_array($PrestashopResources);

		} catch (Exception $e) {
			// No existe en prestashop
		}
		
		return $proveedores;
	}


	/**
	 * [prestashop_obtener_proveedores description]
	 * @return [type] [description]
	 */
	public function prestashop_obtener_proveedores()
	{	
		ini_set('max_execution_time', 0);

		$opt             = array();
		$opt['display'] = '[id,name]';
		$opt['filter[active]'] = '[1]';
		$opt['resource'] = 'suppliers';

		$proveedores = array();

		try {
			$xml = $this->ConexionPrestashop->get($opt);
		
			$PrestashopResources = $xml->children()->children();
			
			$proveedores = to_array($PrestashopResources);

		} catch (Exception $e) {
			// No existe en prestashop
		}
		
		return $proveedores;
	}


	public function prestashop_actualizar_proveedor($id_proveedor, $nuevo_nombre)
	{	
		try {

			$opt                       = array();
			$opt['resource']           = 'suppliers';
			$opt['id'] = $id_proveedor;

			$xml       = $this->ConexionPrestashop->get($opt);
			$resources = $xml->children()->children();
			
			$resources->name = $nuevo_nombre;
			
			$opt           = array('resource' => 'suppliers');
			$opt['putXml'] = $xml->asXML();
			$opt['id'] 	   = $id_proveedor; 
			$xml           = $this->ConexionPrestashop->edit($opt);
			
		} catch (PrestaShopWebserviceException $ex) {
			//prx($ex->getMessage());
			 // No actualizado
			return false;
		}

		return true;

	}


	public function prestashop_obtener_marcas()
	{	
		ini_set('max_execution_time', 0);

		$opt             = array();
		$opt['display'] = '[id,name]';
		#$opt['filter[active]'] = '[1]';
		$opt['resource'] = 'manufacturers';

		$marcas = array();

		try {
			$xml = $this->ConexionPrestashop->get($opt);
		
			$PrestashopResources = $xml->children()->children();
			
			$marcas = to_array($PrestashopResources);

		} catch (Exception $e) {
			// No existe en prestashop
		}
		
		return $marcas;
	}

	public function prestashop_actualizar_marca($id_marca, $nuevo_nombre)
	{
		try {

			$opt                       = array();
			$opt['resource']           = 'manufacturers';
			$opt['id'] = $id_marca;

			$xml       = $this->ConexionPrestashop->get($opt);
			$resources = $xml->children()->children();
			
			$resources->name = $nuevo_nombre;
			
			$opt           = array('resource' => 'manufacturers');
			$opt['putXml'] = $xml->asXML();
			$opt['id'] 	   = $id_marca; 
			$xml           = $this->ConexionPrestashop->edit($opt);
			
		} catch (PrestaShopWebserviceException $ex) {
			//prx($ex->getMessage());
			 // No actualizado
			return false;
		}

		return true;
	}


	/**
	 * [prestashop_obtener_marca description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function prestashop_obtener_marca($id)
	{
		$opt             = array();
		$opt['resource'] = 'manufacturers';
		$opt['id']       = $id;

		$marca = array();

		try {
			$xml = $this->ConexionPrestashop->get($opt);
		
			$PrestashopResources = $xml->children()->children();
			
			$marca = to_array($PrestashopResources);

				
		} catch (Exception $e) {
			// No existe en prestashop
		}

		return $marca;
	}

}