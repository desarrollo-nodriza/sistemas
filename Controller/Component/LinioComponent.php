<?php
App::uses('Component', 'Controller');

require_once __DIR__ . '/../../Vendor/SellerCenterSDK/vendor/autoload.php';

use RocketLabs\SellerCenterSdk\Core\Client;
use RocketLabs\SellerCenterSdk\Core\Configuration;
use RocketLabs\SellerCenterSdk\Core\Request\GenericRequest;
use RocketLabs\SellerCenterSdk\Core\Response\ErrorResponse;
use RocketLabs\SellerCenterSdk\Core\Response\SuccessResponseInterface;
use RocketLabs\SellerCenterSdk\Endpoint\Endpoints;

//require_once (__DIR__ . '/../../Vendor/PSWebServiceLibrary/PSWebServiceLibrary.php');

class LinioComponent extends Component
{	

	public $LinioConexion;

	public $estados = array(
		'canceled'      => 'Cancelar pedido',
		'ready_to_ship' => 'Pedido listo para envio'
	);


	public function crearCliente($apiurl, $apiuser, $apikey)
	{
		$this->LinioConexion = Client::create(new Configuration($apiurl, $apiuser, $apikey));
	}

	/**
	 * [actualizar_precio_stock_producto description]
	 * @param  string $referencia    Referencia/Sku del producto (id externo)
	 * @param  string $precioFinal   Nuevo precio del producto
	 * @param  string $cantidadFinal Nueva cantidad del producto
	 * @return array                
	 */
	function actualizar_precio_stock_producto($referencia = '', $precioFinal = '', $cantidadFinal = '')
	{	
		$res = array(
			'code'    => 501,
			'message' => 'Un error interno ha ocurrido.'
		);

		if ( empty($referencia)
			|| empty($precioFinal)
			|| empty($cantidadFinal)
			) {

			$res['code']    = 300;
			$res['message'] = 'Existen campos vacios. No se puede realizar la operación.';
			
			return $res;
		}

		$productCollectionRequest = Endpoints::product()->productUpdate();

		$productCollectionRequest->updateProduct($referencia)
		->setPrice($precioFinal)
	    ->setQuantity($cantidadFinal);
		
		$response = $productCollectionRequest->build()->call($this->LinioConexion);

		//si la actualización a linio es correcta
		if ($response instanceof SuccessResponseInterface) {
			$res['code'] = 200;
			$res['message'] = 'Producto Ref: ' . $referencia . ' actualizado con éxito';
		}

		return $res;
	}


	/**
	 * [actualizar_precio_producto description]
	 * @param  array  $tienda        Datos de la conexión hacia linio (APIKEY y USER)
	 * @param  string $referencia    Referencia/Sku del producto (id externo)
	 * @param  string $precioFinal   Nuevo precio del producto
	 * @return array                
	 */
	function actualizar_precio_producto($referencia = '', $precioFinal = '')
	{
		$res = array(
			'code'    => 501,
			'message' => 'Un error interno ha ocurrido.'
		);

		if ( empty($referencia)
			|| empty($precioFinal)
			) {

			$res['code']    = 300;
			$res['message'] = 'Existen campos vacios. No se puede realizar la operación.';
			
			return $res;
		}

		$productCollectionRequest = Endpoints::product()->productUpdate();

		$productCollectionRequest->updateProduct($referencia)
		->setPrice($precioFinal);
		
		$response = $productCollectionRequest->build()->call($this->LinioConexion);

		//si la actualización a linio es correcta
		if ($response instanceof SuccessResponseInterface) {
			$res['code'] = 200;
			$res['message'] = 'Producto Ref: ' . $referencia . ' precio actualizado con éxito';
		}

		return $res;
	}


	/**
	 * [actualizar_stock_producto description]
	 * @param  array  $tienda        Datos de la conexión hacia linio (APIKEY y USER)
	 * @param  string $referencia    Referencia/Sku del producto (id externo)
	 * @param  string $cantidadFinal Nueva cantidad del producto
	 * @return array                
	 */
	function actualizar_stock_producto($tienda = array(), $referencia = '', $cantidadFinal = '')
	{
		$res = array(
			'code'    => 501,
			'message' => 'Un error interno ha ocurrido.'
		);

		if (empty($referencia)
			|| empty($cantidadFinal)
			) {

			$res['code']    = 300;
			$res['message'] = 'Existen campos vacios. No se puede realizar la operación.';
			
			return $res;
		}

		$productCollectionRequest = Endpoints::product()->productUpdate();

		$productCollectionRequest->updateProduct($referencia)
	    ->setQuantity($cantidadFinal);
		
	    $response = '';
	    $error = '';

		try {
			$response = $productCollectionRequest->build()->call($this->LinioConexion);
		} catch (Exception $e) {
			$error = $e->getMessage();
		}
		

		//si la actualización a linio es correcta
		if ($response instanceof SuccessResponseInterface) {
			$res['code'] = 200;
			$res['message'] = 'Producto Ref: ' . $referencia . ' stock actualizado con éxito';
		}else{
			$res['code'] = 300;
			$res['message'] = 'Error Ref: ' . $referencia . '-- Mesnaje: ' . $error;
		}

		return $res;
	}


	/****************************************************************************************************/
	//obtiene las órdenes de una marketplace de linio
	public function linio_obtener_ventas ($marketplace_id, &$finalizarLinio) {

		//se obtiene la última venta registrada para consultar solo las nuevas a linio
		$venta = ClassRegistry::init('Venta')->find(
			'first',
			array(
				'conditions' => array(
					'Venta.marketplace_id' => $marketplace_id
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

			$response = $this->LinioConexion->call(
			    (new GenericRequest(
			        Client::GET,
			        'GetOrders',
			        GenericRequest::V1,
			        ['CreatedAfter' => $fecha, 'Limit' => 10]
			    ))
			);

		}

		else { //todas las ventas

			$response = $this->LinioConexion->call(
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


	public function linio_obtener_venta($id, $todo =  false) 
	{
		$response = $this->LinioConexion->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetOrder',
		        GenericRequest::V1,
		        ['OrderId' => $id]
		    ))
		);

		$results = $response->getBody()['Orders'];

		if ($todo) {

			// Productos
			$productos = $this->linio_obtener_venta_detalles($id);
			
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


	public function obtener_pagos ()
	{
		$response = $this->LinioConexion->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetPayoutStatus',
		        GenericRequest::V1
		    ))
		);

		return $response->getBody()['PayoutStatus'];
	}



	/****************************************************************************************************/
	//obtiene el detalle de una orden de Linio
	public function linio_obtener_venta_detalles ($venta_id) {

		$response = $this->LinioConexion->call(
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
	//obtiene los comentarios de una venta de linio
	public function linio_obtener_venta_mensajes ($venta_id) {

		$response = $this->LinioConexion->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetOrderComments',
		        GenericRequest::V1,
		        ['OrderId' => $venta_id]
		    ))
		);

		return $response->getBody();

	}


	/**
	 * Referencia: https://sellerapi.sellercenter.net/docs/getfailurereasons
	 * @return [type] [description]
	 */
	public function linio_obtener_razones_de_falla()
	{
		$response = $this->LinioConexion->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetFailureReasons',
		        GenericRequest::V1,
		        []
		    ))
		);

		return $response->getBody();
	}


	/**
	 * Referencia https://sellerapi.sellercenter.net/docs/setstatustocanceled
	 * @param  [type] $item_id  id del producto
	 * @return [type]          [description]
	 */
	public function linio_cancelar_pedido($item_id, $razon = '', $detalle_razon = '')
	{
		$response = Endpoints::order()
		    ->SetStatusToCanceled($item_id, $razon, $detalle_razon)
		    ->call($this->LinioConexion);

		if ($response instanceof SuccessResponseInterface) {
			return true;
		} else {
		    return false;
		}
	}


	/**
	 * Referencia: https://sellerapi.sellercenter.net/docs/setstatustopackedbymarketplace
	 * @param  [type] $id       [description]
	 * @return [type]           [description]
	 */
	public function linio_paquete_embalado($items = array(), $delivery = 'dropship', $currier = 'Blue Express')
	{
		$orderItemIds     = $items; // Please change the set of Order Item IDs for Your system.
		$deliveryType     = $delivery;
		$shipmentProvider = $currier;

		$response = Endpoints::order()
		    ->SetStatusToPackedByMarketplace($orderItemIds, $deliveryType, $shipmentProvider)
		    ->call($this->LinioConexion);

		if ($response instanceof SuccessResponseInterface) {
			return true;
		} else {
		    return false;
		}
	}



	/**
	 * Referencia: https://sellerapi.sellercenter.net/docs/setstatustoreadytoship
	 * @param  [type] $id       [description]
	 * @return [type]           [description]
	 */
	public function linio_listo_para_envio($items = array(), $delivery = 'dropship', $currier = 'Blue Express', $tracking = '')
	{
		$orderItemIds     = $items; // Please change the set of Order Item IDs for Your system.
		$deliveryType     = $delivery;
		$shipmentProvider = $currier;
		$trackingNumber   = $tracking;

		$response = Endpoints::order()
		    ->setStatusToReadyToShip($orderItemIds, $deliveryType, $shipmentProvider, $trackingNumber)
		    ->call($this->LinioConexion);

		if ($response instanceof SuccessResponseInterface) {
			return true;
		} else {
		    return false;
		}
	}


	public function linio_obtener_curriers()
	{
		$response = $this->LinioConexion->call(
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



	public function linio_empaquetar($items_id, $proveedor)
	{
		$orderItemIds     = json_decode($items_id); // Please change the set of Order Item IDs for Your system.
		$deliveryType     = 'dropship';
		$shipmentProvider = $proveedor;

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
				$documentos[] = $this->linio_obtener_documentos($orderItemIds, $tdocumento);
			}

			return json_encode($documentos);

		}

		return '';
	}


	public function linio_obtener_documentos($items = array(), $type = '')
	{	
		$orderItemIds = $items; // Please change the set of Order Item IDs for Your system.
		$documentType = $type;

		$response = Endpoints::order()->getDocument($orderItemIds, $documentType)->call($this->LinioConexion);
		
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


	public function linio_producto_existe($id)
	{
		$response = $this->LinioConexion->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetProducts',
		        GenericRequest::V1,
		        ['SkuSellerList' => array($id)]
		    ))
		);

		$detalle = array(
			'existe' => 0,
			'item' => array()
		);

		$producto = $response->getBody()['Products'];
		
		if (!empty($producto)) {

			$producto['Product']['precio']           = round($producto['Product']['Price']);
			$producto['Product']['stock_disponible'] = $producto['Product']['Available'];
			$producto['Product']['estado']           = $producto['Product']['Status'];
			$detalle['item']                         = $producto['Product'];
			$detalle['existe']                       = 1;

		}

		return $detalle;

	}


	function publicar_producto($tienda = array(), $args = array())
	{

	}

}