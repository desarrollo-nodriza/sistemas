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
	public function prestashop_obtener_ventas ($tienda) {

		$opt = array();
		$opt['resource'] = 'orders';
		$opt['display'] = '[id,id_customer,current_state,date_add,payment,total_discounts_tax_incl,total_paid,total_products,total_shipping_tax_incl,reference,id_address_delivery]';

		//se obtiene la última venta registrada para consultar solo las nuevas a prestashop
		$venta = ClassRegistry::init('Venta')->find(
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

		$opt['filter[id_customer]'] = '>[0]';

		$xml                 = $this->ConexionPrestashop->get($opt);
		
		$PrestashopResources = $xml->children()->children();
		
		$DataVentas          = to_array($PrestashopResources);
		
		return $DataVentas;

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
		$opt['display'] = '[id,firstname,lastname,address1,address2,city,other, phone, phone_mobile]';
		$opt['filter[id]'] = '[' .$id. ']';
		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$Address = to_array($PrestashopResources);

		return $Address;
	}


	/****************************************************************************************************/
	//obtiene el detalle de una venta
	public function prestashop_obtener_venta_detalles ($venta_id) 
	{
		$opt = array();
		$opt['resource'] = 'order_details';
		$opt['display'] = '[product_id,product_name,product_quantity,unit_price_tax_excl]';
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
	public function prestashop_obtener_cliente ($cliente_id) 
	{
		$opt = array();
		$opt['resource'] = 'customers';
		$opt['display'] = '[firstname,lastname,email]';
		$opt['filter[id]'] = '[' .$cliente_id. ']';

		$xml = $this->ConexionPrestashop->get($opt);

		$PrestashopResources = $xml->children()->children();

		$cliente = to_array($PrestashopResources);

		$VentaCliente = ClassRegistry::init('VentaCliente')->find(
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

		ClassRegistry::init('VentaCliente')->create();
		ClassRegistry::init('VentaCliente')->save($data);

		return ClassRegistry::init('VentaCliente')->id;

	}


	/**
	 * Obtiene los mensajes de un pedido dese prestashop
	 * @param  int 		$venta_id_externo  	id venta   
	 * @return array()
	 */
	public function prestashop_obtener_venta_mensajes($venta_id_externo)
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
			$opt['resource'] = 'customer_messages';
			$opt['display'] = '[message,date_add]';
			$opt['filter[id_customer_thread]'] = '[' .$CustomerThread['customer_thread']['id']. ']';

			$xml = $this->ConexionPrestashop->get($opt);

			$PrestashopResources = $xml->children()->children();

			$mensajes = to_array($PrestashopResources);

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
	 * Actualiza el stock disponible en prestashop
	 * @param  [type] $stock_id   Identificador del stock (no id de producto)
	 * @param  [type] $NuevoStock Nueva cantidad
	 * @return bool             
	 */
	public function prestashop_actualizar_stock($stock_id, $NuevoStock)
	{	
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
	 * Obtiene todos los productos publicados en prestashop (solo los activos)
	 * @param  array  $filter   arreglo con la condición de búqueda de productos (por defecto trae los roductos activos)
	 * @return array productos
	 */
	public function prestashop_obtener_productos($filter = array('filter[active]' => '[1]'))
	{	
		ini_set('max_execution_time', 0);

		$opt             = array();
		$opt['display'] = '[id,name,price,active,quantity,supplier_reference,id_manufacturer]';
		
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
}