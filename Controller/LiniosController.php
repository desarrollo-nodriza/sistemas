<?php

App::uses('AppController', 'Controller');

require_once __DIR__ . '/../Vendor/SellerCenterSDK/vendor/autoload.php';

use RocketLabs\SellerCenterSdk\Core\Client;
use RocketLabs\SellerCenterSdk\Core\Configuration;
use RocketLabs\SellerCenterSdk\Core\Request\GenericRequest;
use RocketLabs\SellerCenterSdk\Core\Response\ErrorResponse;
use RocketLabs\SellerCenterSdk\Core\Response\SuccessResponseInterface;
use RocketLabs\SellerCenterSdk\Endpoint\Endpoints;

require_once (__DIR__ . '/../Vendor/PSWebServiceLibrary/PSWebServiceLibrary.php');

class LiniosController extends AppController {

	private function config_tienda () {

		//info de la tienda que contiene la configuración
		$this->loadModel('Tienda');

		return $tienda = $this->Tienda->find(
			'first',
			array(
				'conditions' => array(
					'Tienda.id' => $this->Session->read('Tienda.id')
				),
				'fields' => array(
					'Tienda.id',
					'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop',
					'Tienda.apiurl_linio', 'Tienda.apiuser_linio', 'Tienda.apikey_linio', 'Tienda.sincronizacion_automatica_linio'
				)
			)
		);

	}

	public function admin_index () {

		//info de la tienda que contiene la configuración
		$tienda = $this->config_tienda();

		BreadcrumbComponent::add('Linio');

		$this->set(compact('tienda'));

	}

	//----------------------------------------------------------------------------------------------------
	//retorna el precio final de un producto con el iva y descuento calculados
	//se reciben el producto y la cofiguración de la tienda para tener los datos de conexión
	private function calcular_precio_final ($DataProducto, $tienda) {

		$webService = new PrestaShopWebservice($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'], false);

		//----------------------------------------------------------------------------------------------------
		//se busca el tax_id a partir del id_tax_rules_group
		$opt = array();
		$opt['resource'] = 'tax_rules';
		$opt['display'] = '[id_tax]';
		$opt['filter[id]'] = '[' .$DataProducto['id_tax_rules_group']. ']';

		$xml = $webService->get($opt);

		$PrestashopResources = $xml->children()->children();

		//para cambiar el objeto xml a un array
		$json = json_encode($PrestashopResources);
		$DataTaxRule = json_decode($json, true);

		//----------------------------------------------------------------------------------------------------
		//se busca el iva a partir del tax_id
		$opt = array();
		$opt['resource'] = 'taxes';
		$opt['display'] = '[rate]';
		$opt['filter[id]'] = '[' .$DataTaxRule['tax_rule']['id_tax']. ']';

		$xml = $webService->get($opt);

		$PrestashopResources = $xml->children()->children();

		//para cambiar el objeto xml a un array
		$json = json_encode($PrestashopResources);
		$DataTax = json_decode($json, true);

		//se calcula el precio con iva
		$PrecioFinal = $DataProducto['price'] + (($DataProducto['price'] * $DataTax['tax']['rate']) / 100);

		//----------------------------------------------------------------------------------------------------
		//se buscan los descuentos asociados al producto por su id
		$opt = array();
		$opt['resource'] = 'specific_prices';
		$opt['display'] = '[id,reduction,reduction_type,from,to]';
		$opt['filter[id_product]'] = '[' .$DataProducto['id']. ']';

		$xml = $webService->get($opt);

		$PrestashopResources = $xml->children()->children();

		//para cambiar el objeto xml a un array
		$json = json_encode($PrestashopResources);
		$DataDescuentos = json_decode($json, true);

		//si existen descuentos para aplicar
		if (!empty($DataDescuentos)) {

			$DescuentoValido = 0; //monto del descuento válido
			$DescuentoValidoId = 0; //id del descuento válido para ir verificando que se tome siempre el de menor id (primer descuento creado)
			$DescuentoValidoTipo = ""; //tipo de descuento "percentage" o "amount" para controlar cómo aplicarlo
			$DescuentoPorFechas = false; //para controlar que se de prioridad al descuento por fechas durante el ciclo

			$ArrayDescuentos = array(); //para evitar problemas con los índices

			if (isset($DataDescuentos['specific_price'][0])) {
				$ArrayDescuentos = $DataDescuentos['specific_price'];
			}
			else {
				$ArrayDescuentos = $DataDescuentos;
			}

			foreach ($ArrayDescuentos as $descuento) {

				//si el descuento aplica por fechas
				if (($descuento['from'] != "0000-00-00 00:00:00") || ($descuento['to'] != "0000-00-00 00:00:00")) {

					//si se aplica desde una fecha (solo se tiene la fecha "from")
					if (($descuento['from'] != "0000-00-00 00:00:00") && ($descuento['to'] == "0000-00-00 00:00:00")) {

						//se verifica si la fecha actual es mayor o igual a la fecha "from" del descuento
						if (date('Y-m-d') >= date_format(date_create($descuento['from']), 'Y-m-d')) {

							//si es el primer descuento que se está considerando
							if ($DescuentoValidoId == 0) {
								$DescuentoValido = $descuento['reduction'];
								$DescuentoValidoId = $descuento['id'];
								$DescuentoValidoTipo = $descuento['reduction_type'];
								$DescuentoPorFechas = true;
							}

							//si no es el primer descuento considerado
							else {

								//se verifica si este descuento tiene prioridad con respecto del que ya se tenía considerado
								//(si este descuento se agregó antes del que se tenía considerado o si tiene prioridad por ser descuento por fechas)
								if (($descuento['id'] < $DescuentoValidoId) || (!$DescuentoPorFechas)) {
									$DescuentoValido = $descuento['reduction'];
									$DescuentoValidoId = $descuento['id'];
									$DescuentoValidoTipo = $descuento['reduction_type'];
									$DescuentoPorFechas = true;
								}

							}

						}

					}

					//si no solo se tiene la fecha "from"
					else {

						//si se aplica hasta una fecha (solo se tiene la fecha "to")
						if (($descuento['from'] == "0000-00-00 00:00:00") && ($descuento['to'] != "0000-00-00 00:00:00")) {

							//se verifica si la fecha actual es menor o igual a la fecha "to" del descuento
							if (date('Y-m-d') <= date_format(date_create($descuento['to']), 'Y-m-d')) {

								//si es el primer descuento que se está considerando
								if ($DescuentoValidoId == 0) {
									$DescuentoValido = $descuento['reduction'];
									$DescuentoValidoId = $descuento['id'];
									$DescuentoValidoTipo = $descuento['reduction_type'];
									$DescuentoPorFechas = true;
								}

								//si no es el primer descuento considerado
								else {

									//se verifica si este descuento tiene prioridad con respecto del que ya se tenía considerado
									//(si este descuento se agregó antes del que se tenía considerado o si tiene prioridad por ser descuento por fechas)
									if (($descuento['id'] < $DescuentoValidoId) || (!$DescuentoPorFechas)) {
										$DescuentoValido = $descuento['reduction'];
										$DescuentoValidoId = $descuento['id'];
										$DescuentoValidoTipo = $descuento['reduction_type'];
										$DescuentoPorFechas = true;
									}

								}

							}

						}

						//si se tienen las fechas "from" y "to"
						else {

							//se verifica si la fecha actual está entre las fechas "from" y "to" del descuento
							if ((date('Y-m-d') >= date_format(date_create($descuento['from']), 'Y-m-d')) && (date('Y-m-d') <= date_format(date_create($descuento['to']), 'Y-m-d'))) {

								//si es el primer descuento que se está considerando
								if ($DescuentoValidoId == 0) {
									$DescuentoValido = $descuento['reduction'];
									$DescuentoValidoId = $descuento['id'];
									$DescuentoValidoTipo = $descuento['reduction_type'];
									$DescuentoPorFechas = true;
								}

								//si no es el primer descuento considerado
								else {

									//se verifica si este descuento tiene prioridad con respecto del que ya se tenía considerado
									//(si este descuento se agregó antes del que se tenía considerado o si tiene prioridad por ser descuento por fechas)
									if (($descuento['id'] < $DescuentoValidoId) || (!$DescuentoPorFechas)) {
										$DescuentoValido = $descuento['reduction'];
										$DescuentoValidoId = $descuento['id'];
										$DescuentoValidoTipo = $descuento['reduction_type'];
										$DescuentoPorFechas = true;
									}

								}

							}

						}

					}

				}

				//si es un descuento fijo (sin fechas)
				else {

					//si es el primer descuento que se está considerando
					if ($DescuentoValidoId == 0) {
						$DescuentoValido = $descuento['reduction'];
						$DescuentoValidoId = $descuento['id'];
						$DescuentoValidoTipo = $descuento['reduction_type'];
						$DescuentoPorFechas = false;
					}

					//si no es el primer descuento considerado
					else {

						//se verifica si este descuento reemplaza al que se tenía considerado
						//(si este descuento se agregó antes del que se tenía considerado y que ese descuento no sea por fechas)
						if (($descuento['id'] < $DescuentoValidoId) && (!$DescuentoPorFechas)) {
							$DescuentoValido = $descuento['reduction'];
							$DescuentoValidoId = $descuento['id'];
							$DescuentoValidoTipo = $descuento['reduction_type'];
							$DescuentoPorFechas = true;
						}

					}

				}

			}

			//se verifica si se aplica un descuento por porcentaje
			if ($DescuentoValidoTipo == "percentage") {
				$PrecioFinal = $PrecioFinal - ($PrecioFinal * $DescuentoValido);
			}
			//si no es descuento por porcentaje se aplica un monto fijo
			else {
				$PrecioFinal = $PrecioFinal - $DescuentoValido;
			}

		}

		return (round($PrecioFinal));

	}

	//----------------------------------------------------------------------------------------------------
	//retorna el stock del producto
	//se reciben el producto y la cofiguración de la tienda para tener los datos de conexión
	private function obtener_stock ($DataProducto, $tienda) {

		$webService = new PrestaShopWebservice($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'], false);

		//----------------------------------------------------------------------------------------------------
		//se busca el stock_available a partir del id de producto
		$opt = array();
		$opt['resource'] = 'stock_availables';
		$opt['display'] = '[quantity]';
		$opt['filter[id_product]'] = '[' .$DataProducto['id']. ']';

		$xml = $webService->get($opt);

		$PrestashopResources = $xml->children()->children();

		//para cambiar el objeto xml a un array
		$json = json_encode($PrestashopResources);
		$stock = json_decode($json, true);

		return $stock['stock_available']['quantity'];

	}

	//----------------------------------------------------------------------------------------------------
	//Sincronización de productos por selección
	public function admin_sincronizar_productos () {

		set_time_limit(0);

		//info de la tienda que contiene la configuración
		$tienda = $this->config_tienda();

		$ResultadoSincronizacion = array(); //para mostrar los resultados de la sincronización
		$ListaProductos = array(); //lista de productos que se obtienen de linio para seleccionar cuáles se van a actualizar
		$ResultadoConsultaLinio = true; //para controlar la respuesta del primer paso (obtener los productos de linio)

		//si se realiza la sincronización
		if ( $this->request->is('post') || $this->request->is('put') ) {

			//----------------------------------------------------------------------------------------------------
			//se buscan los productos de linio en prestashop
			$ArrayReferencias = array(); //para verificar existencia en prestashop
			$ArrayProductoData = array(); //complementa al anterior
			$StrReferencias = ""; //para consultar en prestashop

			$ListaProductos = $this->request->data['Linio'];

			//se preparan las referencias para consultar a prestashop
			foreach ($ListaProductos as $producto) {

				//se considera solo los marcados para sincronizar
				if (isset($producto['seleccionado'])) {

					$ArrayReferencias[] = $producto['Product']['SellerSku'];
					$ArrayProductoData[] = $producto;

					if ($StrReferencias != "") {
						$StrReferencias .= "|";
					}

					$StrReferencias .= $producto['Product']['SellerSku'];

				}

			}

			$ResultadoSincronizacion['total'] = count($ArrayReferencias);

			try {

				$webService = new PrestaShopWebservice($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'], false);
				
				$opt['resource'] = 'products';
				$opt['display'] = '[id,reference,price,id_tax_rules_group]';
				$opt['filter[id]'] = '[' .$StrReferencias. ']';

				$xml = $webService->get($opt);

				$PrestashopResources = $xml->children()->children();

				$ResultadoSincronizacion['resultado'] = true;

			}

			catch (PrestaShopWebserviceException $e) {

				$ResultadoSincronizacion['resultado'] = false;
				
				$trace = $e->getTrace();

				if ($trace[0]['args'][0] == 404) {
					$ResultadoSincronizacion['error'] = "Api User Incorrecto";
				}
				else {
					if ($trace[0]['args'][0] == 401) {
						$ResultadoSincronizacion['error'] = "Api Key Incorrecta";
					}
					else {
						$ResultadoSincronizacion['error'] = $e->getMessage();
					}
				}

			}

			//----------------------------------------------------------------------------------------------------
			//si la búsqueda en prestashop es correcta se hace la actualización a linio
			if ($ResultadoSincronizacion['resultado']) {

				$ResultadoSincronizacion['coincidencias'] = count($PrestashopResources); //cantidad de productos de linio que se encontraron en prestashop

				$ResultadoSincronizacion['actualizados'] = 0; //cantidad de productos que se actualizaron

				//productos para actualizar
				$productCollectionRequest = Endpoints::product()->productUpdate();

				//se preparan los productos para actualizar a linio
				foreach ($PrestashopResources as $producto) {

					//para cambiar el objeto xml a un array
					$json = json_encode($producto);
					$DataProducto = json_decode($json, true);

					$pos = array_search($DataProducto['id'], $ArrayReferencias);

					$PrecioFinal = $this->calcular_precio_final($DataProducto, $tienda);

					$DataProducto['quantity'] = $this->obtener_stock($DataProducto, $tienda);

					//si se actualiza el producto (comparación de precio y cantidad entre prestashop y linio)
					if ((intval($ArrayProductoData[$pos]['Product']['Price']) != $PrecioFinal) || ($ArrayProductoData[$pos]['Product']['Quantity'] != $DataProducto['quantity'])) {

						$productCollectionRequest->updateProduct($ArrayProductoData[$pos]['Product']['SellerSku'])
						->setPrice($PrecioFinal)
					    ->setQuantity($DataProducto['quantity']);

						$ResultadoSincronizacion['actualizados']++;

					}
					
				}

				//se llama a la actualización de linio solo si hay productos para actualizar
				if ($ResultadoSincronizacion['actualizados'] > 0) {

					$client = Client::create(new Configuration($tienda['Tienda']['apiurl_linio'], $tienda['Tienda']['apiuser_linio'], $tienda['Tienda']['apikey_linio']));
				
					$response = $productCollectionRequest->build()->call($client);

					//si la actualización a linio es correcta
					if ($response instanceof SuccessResponseInterface) {
						$ResultadoSincronizacion['resultado'] = true;
					}

					//si hubo un error en la actualización a linio
					else {
						$ResultadoSincronizacion['resultado'] = false;
						$ResultadoSincronizacion['error'] = "Error actualizando los productos en Linio.";
					}

				}

				//si no habían productos para actualizar igual el proceso es correcto
				else {
					$ResultadoSincronizacion['resultado'] = true;
				}
				
			}

		}

		//si se presentan los productos de linio para seleccionar cuáles se van a actualizar
		else {

			$client = Client::create(new Configuration($tienda['Tienda']['apiurl_linio'], $tienda['Tienda']['apiuser_linio'], $tienda['Tienda']['apikey_linio']));

			$response = $client->call(
			    (new GenericRequest(
			        Client::GET,
			        'GetProducts',
			        GenericRequest::V1
			    ))
			);

			//si la consulta a linio fue correcta
			if ($response instanceof SuccessResponseInterface) {

				//productos obtenidos de linio
				$ListaProductos = $response->getBody()['Products']['Product'];

			}

			//si hubo un error leyendo los productos de linio
			else {
				$ResultadoConsultaLinio = false;
			}

		}

		BreadcrumbComponent::add('Linio');
		BreadcrumbComponent::add('Sincronización de Productos');

		$this->set(compact('tienda', 'ResultadoConsultaLinio', 'ListaProductos', 'ResultadoSincronizacion'));

	}

	//----------------------------------------------------------------------------------------------------
	//Sincronización de todos los productos
	public function admin_sincronizar_productos_todos () {

		set_time_limit(0);

		//info de la tienda que contiene la configuración
		$tienda = $this->config_tienda();

		//----------------------------------------------------------------------------------------------------
		//Se obtienen los productos de linio

		$ResultadoSincronizacion = array(); //para mostrar los resultados de la sincronización

		$client = Client::create(new Configuration($tienda['Tienda']['apiurl_linio'], $tienda['Tienda']['apiuser_linio'], $tienda['Tienda']['apikey_linio']));

		$response = $client->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetProducts',
		        GenericRequest::V1
		    ))
		);

		//si la consulta a linio fue correcta
		if ($response instanceof SuccessResponseInterface) {

			//productos obtenidos de linio
			$ListaProductos = $response->getBody()['Products']['Product'];

			$ResultadoSincronizacion['resultado'] = true;

			$ResultadoSincronizacion['total'] = count($ListaProductos);

			//----------------------------------------------------------------------------------------------------
			//se buscan los productos de linio en prestashop
			$ArrayReferencias = array(); //para verificar existencia en prestashop
			$ArrayProductoData = array(); //complementa al anterior
			$StrReferencias = ""; //para consultar en prestashop

			//se preparan las referencias para consultar a prestashop
			foreach ($ListaProductos as $producto) {

				$ArrayReferencias[] = $producto['SellerSku'];
				$ArrayProductoData[] = $producto;

				if ($StrReferencias != "") {
					$StrReferencias .= "|";
				}

				$StrReferencias .= $producto['SellerSku'];

			}

			try {

				$webService = new PrestaShopWebservice($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'], false);
				
				$opt['resource'] = 'products';
				$opt['display'] = '[id,reference,price,id_tax_rules_group]';
				$opt['filter[id]'] = '[' .$StrReferencias. ']';

				$xml = $webService->get($opt);

				$PrestashopResources = $xml->children()->children();

				$ResultadoSincronizacion['resultado'] = true;

			}

			catch (PrestaShopWebserviceException $e) {

				$ResultadoSincronizacion['resultado'] = false;
				
				$trace = $e->getTrace();

				if ($trace[0]['args'][0] == 404) {
					$ResultadoSincronizacion['error'] = "Api User Incorrecto";
				}
				else {
					if ($trace[0]['args'][0] == 401) {
						$ResultadoSincronizacion['error'] = "Api Key Incorrecta";
					}
					else {
						$ResultadoSincronizacion['error'] = $e->getMessage();
					}
				}

			}

			//----------------------------------------------------------------------------------------------------
			//si la búsqueda en prestashop es correcta se hace la actualización a linio
			if ($ResultadoSincronizacion['resultado']) {

				$ResultadoSincronizacion['coincidencias'] = count($PrestashopResources->product); //cantidad de productos de linio que se encontraron en prestashop

				$ResultadoSincronizacion['actualizados'] = 0; //cantidad de productos que se actualizaron

				//productos para actualizar
				$productCollectionRequest = Endpoints::product()->productUpdate();

				//se preparan los productos para actualizar a linio
				foreach ($PrestashopResources as $producto) {

					//para cambiar el objeto xml a un array
					$json = json_encode($producto);
					$DataProducto = json_decode($json, true);

					$pos = array_search($DataProducto['id'], $ArrayReferencias);

					$PrecioFinal = $this->calcular_precio_final($DataProducto, $tienda);

					$DataProducto['quantity'] = $this->obtener_stock($DataProducto, $tienda);

					//si se actualiza el producto (comparación de precio y cantidad entre prestashop y linio)
					if ((intval($ArrayProductoData[$pos]['Price']) != $PrecioFinal) || ($ArrayProductoData[$pos]['Quantity'] != $DataProducto['quantity'])) {

						$productCollectionRequest->updateProduct($ArrayProductoData[$pos]['SellerSku'])
						->setPrice($PrecioFinal)
					    ->setQuantity($DataProducto['quantity']);

						$ResultadoSincronizacion['actualizados']++;

					}
					
				}

				//se llama a la actualización de linio solo si hay productos para actualizar
				if ($ResultadoSincronizacion['actualizados'] > 0) {

					$client = Client::create(new Configuration($tienda['Tienda']['apiurl_linio'], $tienda['Tienda']['apiuser_linio'], $tienda['Tienda']['apikey_linio']));
				
					$response = $productCollectionRequest->build()->call($client);

					//si la actualización a linio es correcta
					if ($response instanceof SuccessResponseInterface) {
						$ResultadoSincronizacion['resultado'] = true;
					}

					//si hubo un error en la actualización a linio
					else {
						$ResultadoSincronizacion['resultado'] = false;
						$ResultadoSincronizacion['error'] = "Error actualizando los productos en Linio.";
					}

				}

				//si no habían productos para actualizar igual el proceso es correcto
				else {
					$ResultadoSincronizacion['resultado'] = true;
				}
				
			}

		}

		//si hubo un error leyendo los productos de linio
		else {
			$ResultadoSincronizacion['resultado'] = false;
			$ResultadoSincronizacion['error'] = "Error obteniendo los productos de Linio.";
		}

		BreadcrumbComponent::add('Linio');
		BreadcrumbComponent::add('Sincronización de Productos');

		$this->set(compact('tienda', 'ResultadoSincronizacion'));

	}


	public function admin_sincronizar_stock_todos()
	{
		set_time_limit(0);

		//info de la tienda que contiene la configuración
		$tienda = $this->config_tienda();

		//----------------------------------------------------------------------------------------------------
		//Se obtienen los productos de linio

		$ResultadoSincronizacion = array(); //para mostrar los resultados de la sincronización

		$client = Client::create(new Configuration($tienda['Tienda']['apiurl_linio'], $tienda['Tienda']['apiuser_linio'], $tienda['Tienda']['apikey_linio']));

		$response = $client->call(
		    (new GenericRequest(
		        Client::GET,
		        'GetProducts',
		        GenericRequest::V1
		    ))
		);

		//si la consulta a linio fue correcta
		if ($response instanceof SuccessResponseInterface) {

			//productos obtenidos de linio
			$ListaProductos = $response->getBody()['Products']['Product'];

			$ResultadoSincronizacion['resultado'] = true;

			$ResultadoSincronizacion['total'] = count($ListaProductos);

			//----------------------------------------------------------------------------------------------------
			//se buscan los productos de linio en prestashop
			$ArrayReferencias = array(); //para verificar existencia en prestashop
			$ArrayProductoData = array(); //complementa al anterior
			$StrReferencias = ""; //para consultar en prestashop

			//se preparan las referencias para consultar a prestashop
			foreach ($ListaProductos as $producto) {

				$ArrayReferencias[] = $producto['SellerSku'];
				$ArrayProductoData[] = $producto;

				if ($StrReferencias != "") {
					$StrReferencias .= "|";
				}

				$StrReferencias .= $producto['SellerSku'];

			}

			try {

				$webService = new PrestaShopWebservice($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'], false);
				
				$opt['resource'] = 'products';
				$opt['display'] = '[id,reference,price,id_tax_rules_group]';
				$opt['filter[id]'] = '[' .$StrReferencias. ']';

				$xml = $webService->get($opt);

				$PrestashopResources = $xml->children()->children();

				$ResultadoSincronizacion['resultado'] = true;

			}

			catch (PrestaShopWebserviceException $e) {

				$ResultadoSincronizacion['resultado'] = false;
				
				$trace = $e->getTrace();

				if ($trace[0]['args'][0] == 404) {
					$ResultadoSincronizacion['error'] = "Api User Incorrecto";
				}
				else {
					if ($trace[0]['args'][0] == 401) {
						$ResultadoSincronizacion['error'] = "Api Key Incorrecta";
					}
					else {
						$ResultadoSincronizacion['error'] = $e->getMessage();
					}
				}

			}

			//----------------------------------------------------------------------------------------------------
			//si la búsqueda en prestashop es correcta se hace la actualización a linio
			if ($ResultadoSincronizacion['resultado']) {

				$ResultadoSincronizacion['coincidencias'] = count($PrestashopResources->product); //cantidad de productos de linio que se encontraron en prestashop

				$ResultadoSincronizacion['actualizados'] = 0; //cantidad de productos que se actualizaron

				//productos para actualizar
				$productCollectionRequest = Endpoints::product()->productUpdate();

				//se preparan los productos para actualizar a linio
				foreach ($PrestashopResources as $producto) {

					//para cambiar el objeto xml a un array
					$json = json_encode($producto);
					$DataProducto = json_decode($json, true);

					$pos = array_search($DataProducto['id'], $ArrayReferencias);

					//$PrecioFinal = $this->calcular_precio_final($DataProducto, $tienda);

					$DataProducto['quantity'] = $this->obtener_stock($DataProducto, $tienda);

					//si se actualiza el producto (comparación de precio y cantidad entre prestashop y linio)
					if (($ArrayProductoData[$pos]['Quantity'] != $DataProducto['quantity'])) {

						$productCollectionRequest->updateProduct($ArrayProductoData[$pos]['SellerSku'])
						//->setPrice($PrecioFinal)
					    ->setQuantity($DataProducto['quantity']);

						$ResultadoSincronizacion['actualizados']++;

					}
					
				}

				//se llama a la actualización de linio solo si hay productos para actualizar
				if ($ResultadoSincronizacion['actualizados'] > 0) {

					$client = Client::create(new Configuration($tienda['Tienda']['apiurl_linio'], $tienda['Tienda']['apiuser_linio'], $tienda['Tienda']['apikey_linio']));
				
					$response = $productCollectionRequest->build()->call($client);

					//si la actualización a linio es correcta
					if ($response instanceof SuccessResponseInterface) {
						$ResultadoSincronizacion['resultado'] = true;
					}

					//si hubo un error en la actualización a linio
					else {
						$ResultadoSincronizacion['resultado'] = false;
						$ResultadoSincronizacion['error'] = "Error actualizando los productos en Linio.";
					}

				}

				//si no habían productos para actualizar igual el proceso es correcto
				else {
					$ResultadoSincronizacion['resultado'] = true;
				}
				
			}

		}

		//si hubo un error leyendo los productos de linio
		else {
			$ResultadoSincronizacion['resultado'] = false;
			$ResultadoSincronizacion['error'] = "Error obteniendo los productos de Linio.";
		}

		BreadcrumbComponent::add('Linio');
		BreadcrumbComponent::add('Sincronización de Productos');

		$this->set(compact('tienda', 'ResultadoSincronizacion'));
	}

}
