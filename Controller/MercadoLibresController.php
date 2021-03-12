<?php
App::uses('AppController', 'Controller');
App::uses('VentasController', 'Controller');
#App::import('Vendor', 'Meli', array('file' => 'Meli/meli.php'));

class MercadoLibresController extends AppController
{	

	public $components = array('MeliMarketplace', 'Prestashop');

	private $envios = array(
		'me2' => 'Envío por MercadoEnvíos',
		'not_specified' => 'También se puede retirar en persona',
		'custom' => 'Envío Personalizado'
	);

	function beforeFilter() {
	    parent::beforeFilter();

	    if (isset($this->request->params['meli'])) {
	    	$this->Auth->allow('get');	
	    }
	}

	/**
	* MELI Access
	* Método de retorna el html generado para un producto, una tienda y una plantilla definida
	*/
	public function meli_get()
	{	
		if (isset($this->request->params['meli']) 
			&& isset($this->request->params['tienda'])
			&& isset($this->request->params['plantilla']) 
			&& isset($this->request->params['producto'])) 
		{
			$this->request->data['MercadoLibr']['mercado_libre_plantilla_id'] = $this->request->params['plantilla'];
			$this->request->data['MercadoLibr']['id_product'] = $this->request->params['producto'];

			// Set tienda
			$this->Session->write('Tienda.id', $this->request->params['tienda']);

			$this->request->data['MercadoLibr']['html'] = $this->createHtml();

			echo $this->request->data['MercadoLibr']['html'];
			exit;
		}
	}

	public function admin_getOrders() 
	{
		# Contacto
		#$json = json_decode(file_get_contents(APP . 'webroot' . DS . 'ordenes-meli.json'), true);
		
		# DUmma
		$json = json_decode(file_get_contents(APP . 'webroot' . DS . 'ordenes-dum.json'), true);
		
		$newArr = array();
		
		foreach ($json['results'] as $indice => $orden) {
			$newArr[$indice]['id_orden'] = $orden['id'];
			$newArr[$indice]['estado'] = $orden['status'];
			$newArr[$indice]['fecha_creacion'] = $orden['date_created'];
			$newArr[$indice]['total_pagado'] = $orden['total_amount'];
			
			foreach ($orden['order_items'] as $key => $value) {
				$newArr[$indice]['productos'][$key]['nombre'] = $value['item']['title'];
				$newArr[$indice]['productos'][$key]['precio_unitario'] = $value['unit_price'];
				$newArr[$indice]['productos'][$key]['cantidad'] = $value['quantity'];
				$newArr[$indice]['productos'][$key]['para_meli'] = $value['sale_fee'];
			}
		}

		header('Content-Type: application/json; charset=utf-8'); 
		echo json_encode($newArr, JSON_UNESCAPED_UNICODE);
		exit;

	}


	public function autorizacionMeli($callback = '')
	{	
		$token = $this->Session->read('Meli.access_token');
		
		if ( ! empty($this->request->query['code']) || ($this->Session->check('Meli.access_token') && !empty($token)) ) {
			if( isset($this->request->query['code']) && !$this->Session->check('Meli.access_token') ) {
				if (!empty($callback)) {
					$this->Meli->login($this->request->query['code'], $callback, true);
				}else{
					$this->Meli->login($this->request->query['code'], Router::url(array('controller' => 'mercadoLibres', 'action' => 'index'), true));
				}
			} else {
				$this->Meli->checkTokenAndRefreshIfNeed();
			}
		}else{
		
			if (!empty($callback)) {
				return $this->Meli->getAuthUrl($callback, true);
			}
			return $this->Meli->getAuthUrl(Router::url(array('controller' => 'mercadoLibres', 'action' => 'index'), true));
		}
	}


	public function admin_obtenerCategorias($json = true)
	{	
		$response = $this->MeliMarketplace->mercadolibre_obtener_categorias();
		if ($response['httpCode'] != 200) {
			$response = '';
		}else{
			$response = to_array($response['body']);
		}
		
		$new = array();
		foreach ($response as $key => $value) {
			$new[$value['id']] = $value['name'];
		}

		if ($json) {
			header('Content-Type: application/json; charset=utf-8'); 
			echo json_encode($new, JSON_UNESCAPED_UNICODE);
			exit;	
		}else{
			return $new;
		}
	}


	public function admin_obtenerCategoriasId($id, $print = true)
	{	
		if (empty($id)) {
			return;
		}

		# Creamos cliente Meli
		$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );

		$response = $this->MeliMarketplace->mercadolibre_obtener_categoria_por_id($id);

		if ($response['httpCode'] != 200) {
			return;
		}else{
			$response = to_array($response['body']);
		}
		
		$new = array();
		if (!empty($response['children_categories'])) {
			foreach ($response['children_categories'] as $key => $value) {
				$new[$key]['id'] = $value['id'];
				$new[$key]['name'] = $value['name'];
			}	
		}

		if ($print) {
			header('Content-Type: application/json; charset=utf-8'); 
			echo json_encode($new, JSON_UNESCAPED_UNICODE);
			exit;
		}else{
			return $new;
		}
	}


	public function admin_desconectar()
	{
		$this->Session->delete('Meli');
		$this->Session->setFlash('Aplicación desconectada con éxito.', null, array(), 'success');
		$this->redirect(array('action' => 'index'));
	}


	public function admin_verProducto($data = '')
	{
		if (empty($data)) {
			return '';
		}

		if (!$this->Session->check('Marketplace.id')) {
			return '';
		}

		# Creamos cliente Meli
		$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );
			
		return $this->MeliMarketplace->mercadolibre_producto_existe($data['MercadoLibr']['id_product'],$this->Session->read('Marketplace.seller_id'));

	}


	public function admin_envioDisponible($categoria = '', $print = false, $costo = '' )
	{
		if (empty($categoria)) {			
			return '';
		}

		# Creamos cliente Meli
		$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );

		$resultado = $this->MeliMarketplace->mercadolibre_obtener_modo_envio($categoria, $costo, $this->Session->read('Marketplace.seller_id'));

		if ($resultado['httpCode'] != 200) {
			if ($print) {
				echo $resultado['httpCode'];
				exit;
			}
			return '';
		}
		
		$listaModos = Hash::extract($resultado['body'], '{n}.mode');

		$envioLista = array();

		foreach ($resultado['body'] as $indice => $modo) {
			if (in_array('me2', $listaModos) && $modo['mode'] == 'custom') {
				unset($resultado['body'][$indice]);
			}else{
				if ($modo['mode'] == 'custom') {
					$envioLista[$indice] = $modo;
					$envioLista[$indice]['label'] = $this->envios[$modo['mode']];
				}

				if ($modo['mode'] == 'me2') {
					$envioLista[$indice] = $modo;
					$envioLista[$indice]['label'] = $this->envios[$modo['mode']];
				}
			}
		}

		if ($print) {
			header('Content-Type: application/json; charset=utf-8'); 
			echo json_encode(array_reverse($envioLista), JSON_UNESCAPED_UNICODE);
			exit;
		}else{
			return array_reverse($envioLista);
		}
		
	}


	public function admin_cambiarEstado($id= '' , $meli_id = '', $estado = '')
	{
		if (empty($id) || empty($estado) || empty($meli_id)) {
			return '';
		}

		# Creamos cliente Meli
		$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );

		$this->MeliMarketplace->mercadolibre_conectar('', $this->Session->read('Marketplace'));

		$stateResponse = to_array($this->MeliMarketplace->mercadolibre_cambiar_estado($meli_id, $estado));

		if ($stateResponse['httpCode'] != 200) {
			
			$this->Session->setFlash($stateResponse['body']['message'], null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		
		}else{
			if ($meli_id == $stateResponse['body']['id'] && $estado == $stateResponse['body']['status']) {

				$arr = array(
					'MercadoLibr' => array(
						'id' => $id,
						'estado' => $stateResponse['body']['status']
						)
				);

				$this->MercadoLibr->save($arr);

				$this->Session->setFlash('Estado del producto actualizado.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
		}
	}


	public function admin_actualizar($producto = array())
	{	
		$urlMeli = '';

		if (!empty($producto)) {
			
			# Verificamos que el producto no esté publicado en mercadolibre
			if (!empty($producto['MercadoLibr']['id_meli'])) {

				$imagenes = array(
					array(
					'source' => $producto['MercadoLibr']['imagen_meli']
					)
				);


				// We construct the item to POST
				$item = array(
					"title"               => $producto['MercadoLibr']['producto'],
					"official_store_id"   => $producto['MercadoLibr']['tienda_oficial_id'],
					"price"               => $producto['MercadoLibr']['precio'],
					"available_quantity"  => $producto['MercadoLibr']['cantidad_disponible'],
					"buying_mode"         => 'buy_it_now',
					"condition"           => $producto['MercadoLibr']['condicion'],
					"video_id"            => $producto['MercadoLibr']['id_video'],
					"warranty"            => $producto['MercadoLibr']['garantia'],
					"pictures"            => $imagenes,
					"seller_custom_field" => $producto['MercadoLibr']['seller_custom_field'],
				);

				$agregarEnvio = false;
				$margenAdicional = $this->Session->read('Marketplace.porcentaje_adicional');
					
				if ($producto['MercadoLibr']['agregar_costo_envio']) {
					$agregarEnvio = true;
				}

				# Envios
				$envios = array();
				if (isset($this->request->data['Envios'])) {
					
					if (isset($this->request->data['Envios']['me2'])) {

						$shippingMethods = $this->MeliMarketplace->mercadolibre_obtener_modo_envio($producto['MercadoLibr']['categoria_hoja'], $producto['MercadoLibr']['precio'], $this->Session->read('Marketplace.seller_id'));

						if ($shippingMethods['httpCode'] == 200) {
							
							$freeshipping = Hash::extract($shippingMethods['body'], '{n}[mode=me2].shipping_attributes.free')[0];

							$envios['mode'] = 'me2';
							$envios['local_pick_up'] = (isset($this->request->data['Envios']['local_pick_up']) && $this->request->data['Envios']['local_pick_up']) ? true : false;
							$envios['free_shipping'] = true;

							foreach ($freeshipping['accepted_methods'] as $if => $metodos) {
								$envios['free_methods'][$if] = array(
									'id' => $metodos,
									'rule' => array(
										'free_mode' => 'country',
										'value' => null
									)
								);
							}

							/*$envios['tags'] = array(
								'mandatory_free_shipping'
							);*/

							if ($this->request->data['MercadoLibr']['agregar_costo_envio']) {
								$agregarEnvio = true;
							}

						}

					}

					if (isset($this->request->data['Envios']['custom']) && isset($this->request->data['Envios']['costs'][0]) && !empty($this->request->data['Envios']['costs'][0])) {
						$envios['mode'] = 'custom';
						$envios['local_pick_up'] = (isset($this->request->data['Envios']['local_pick_up']) && $this->request->data['Envios']['local_pick_up']) ? true : false;
						$envios['free_shipping'] = false;
						$envios['free_methods'] = array();
						foreach ($this->request->data['Envios']['costs'] as $indice => $costo) {
							$envios['costs'][] = $costo;
						}
						
					}

					if (!empty($envios)) {
						$item['shipping'] = $envios;
					}

				}

				$meliRespuesta = $this->MeliMarketplace->modified_item($producto['MercadoLibr']['id_meli'], $item, $agregarEnvio, $margenAdicional, $producto['MercadoLibr']['description']);
				
				if (!empty($meliRespuesta)) {
					if ($meliRespuesta['httpCode'] >= 300) {

						$meliRespuesta = to_array($meliRespuesta);

					}else{
						
						$meliRespuesta = to_array($meliRespuesta);
						
						# Actualizamos producto con respuesta de Meli
						$productoMeli = array(
							'MercadoLibr' => array(
								'id' => $producto['MercadoLibr']['id'],
								'id_meli' => $meliRespuesta['body']['id'],
								'site_id' => $meliRespuesta['body']['site_id'],
								'url_meli' => $meliRespuesta['body']['permalink'],
								'fecha_finaliza' => date('Y-m-d H:i:s', strtotime($meliRespuesta['body']['stop_time'])),
								'estado' => $meliRespuesta['body']['status'],
								)
						);

						$urlMeli = $meliRespuesta['body']['permalink'];

						$this->MercadoLibr->save($productoMeli);

						$meliRespuesta = 'published';
					}
				}
			}


			if (is_array($meliRespuesta)) {
					
				$errores = sprintf('<p>%s</p>',$meliRespuesta['body']['error']);
				$errores .= '<p>Causas:</p><ul>';
				
				foreach ($meliRespuesta['body']['cause'] as $causa) {
					$errores .= sprintf('<li>%s</li>', $causa['message']);
				}

				$errores .= '</ul>';

				$this->Session->setFlash('Error al publicar en Mercado libre. Detalles del error:<br>' . $errores, null, array(), 'danger');
				$this->redirect(array('action' => 'edit', $producto['MercadoLibr']['id']));

			}else{

				$this->Session->setFlash('Producto publicado correctamente en Mercado libre. Ver producto <a href="' . $urlMeli . '" target="_blank" class="btn btn-default btn-xs">aquí</a>', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
		}
	}


	public function admin_publicar($producto = array())
	{	
		$urlMeli = '';

		if (!empty($producto)) {
			
			# Verificamos que el producto no esté publicado en mercadolibre
			if (empty($producto['MercadoLibr']['id_meli'])) {

				$imagenes = array(
					array(
					'source' => $producto['MercadoLibr']['imagen_meli']
					)
				);


				// We construct the item to POST
				$item = array(
					"title"               => $producto['MercadoLibr']['producto'],
					"official_store_id"   => $producto['MercadoLibr']['tienda_oficial_id'],
					"category_id"         => $producto['MercadoLibr']['categoria_hoja'],
					"price"               => $producto['MercadoLibr']['precio'],
					"currency_id"         => 'CLP',
					"available_quantity"  => $producto['MercadoLibr']['cantidad_disponible'],
					"buying_mode"         => 'buy_it_now',
					"listing_type_id"     => $producto['MercadoLibr']['tipo_publicacion'],
					"condition"           => $producto['MercadoLibr']['condicion'],
					"video_id"            => $producto['MercadoLibr']['id_video'],
					"warranty"            => $producto['MercadoLibr']['garantia'],
					"pictures"            => $imagenes,
					"seller_custom_field" => $producto['MercadoLibr']['seller_custom_field'],
					"tags"                => array("immediate_payment"),
					"description"         => array("plain_text" => $producto['MercadoLibr']['description']),
				);

				$agregarEnvio = false;
				$margenAdicional = $this->Session->read('Marketplace.porcentaje_adicional');

				# Envios
				$envios = array();
				if (isset($this->request->data['Envios'])) {
					
					if (isset($this->request->data['Envios']['me2'])) {

						$shippingMethods = $this->MeliMarketplace->mercadolibre_obtener_modo_envio($producto['MercadoLibr']['categoria_hoja'], $producto['MercadoLibr']['precio'], $this->Session->read('Marketplace.seller_id'));

						if ($shippingMethods['httpCode'] == 200) {
							
							$freeshipping = Hash::extract($shippingMethods['body'], '{n}[mode=me2].shipping_attributes.free')[0];

							$envios['mode'] = 'me2';
							$envios['local_pick_up'] = (isset($this->request->data['Envios']['local_pick_up']) && $this->request->data['Envios']['local_pick_up']) ? true : false;
							$envios['free_shipping'] = true;

							foreach ($freeshipping['accepted_methods'] as $if => $metodos) {
								$envios['free_methods'][$if] = array(
									'id' => $metodos,
									'rule' => array(
										'free_mode' => 'country',
										'value' => null
									)
								);
							}

							$envios['tags'] = array(
								'mandatory_free_shipping'
							);

							if ($this->request->data['MercadoLibr']['agregar_costo_envio']) {
								$agregarEnvio = true;
							}

						}

					}

					if (isset($this->request->data['Envios']['custom']) && isset($this->request->data['Envios']['costs'][0]) && !empty($this->request->data['Envios']['costs'][0])) {
						$envios['mode'] = 'custom';
						$envios['local_pick_up'] = (isset($this->request->data['Envios']['local_pick_up']) && $this->request->data['Envios']['local_pick_up']) ? true : false;
						$envios['free_shipping'] = false;
						$envios['free_methods'] = array();
						foreach ($this->request->data['Envios']['costs'] as $indice => $costo) {
							$envios['costs'][] = $costo;
						}
						
					}

					if (!empty($envios)) {
						$item['shipping'] = $envios;
					}

				}
				
				$meliRespuesta = $this->MeliMarketplace->publish($item, $agregarEnvio, $margenAdicional);
				
				if (!empty($meliRespuesta)) {
					if ($meliRespuesta['httpCode'] >= 300) {

						$meliRespuesta = to_array($meliRespuesta);

					}else{
						
						$meliRespuesta = to_array($meliRespuesta);
						
						# Actualizamos producto con respuesta de Meli
						$productoMeli = array(
							'MercadoLibr' => array(
								'id' => $producto['MercadoLibr']['id'],
								'id_meli' => $meliRespuesta['body']['id'],
								'site_id' => $meliRespuesta['body']['site_id'],
								'url_meli' => $meliRespuesta['body']['permalink'],
								'fecha_finaliza' => date('Y-m-d H:i:s', strtotime($meliRespuesta['body']['stop_time'])),
								'estado' => $meliRespuesta['body']['status'],
								)
						);

						$urlMeli = $meliRespuesta['body']['permalink'];

						$this->MercadoLibr->save($productoMeli);

						$meliRespuesta = 'published';
					}
				}
			}


			if (is_array($meliRespuesta)) {
					
				$errores = sprintf('<p>%s</p>',$meliRespuesta['body']['error']);
				$errores .= '<p>Causas:</p><ul>';
				
				foreach ($meliRespuesta['body']['cause'] as $causa) {
					$errores .= sprintf('<li>%s</li>', $causa['message']);
				}

				$errores .= '</ul>';

				$this->Session->setFlash('Error al publicar en Mercado libre. Detalles del error:<br>' . $errores, null, array(), 'danger');
				$this->redirect(array('action' => 'edit', $producto['MercadoLibr']['id']));

			}else{

				$this->Session->setFlash('Producto publicado correctamente en Mercado libre. Ver producto <a href="' . $urlMeli . '" target="_blank" class="btn btn-default btn-xs">aquí</a>', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
		}
	}


	public function admin_validar_meli($id)
	{	
		# Creamos cliente Meli
		$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );
		
		$this->MeliMarketplace->mercadolibre_conectar('', $this->Session->read('Marketplace'));

		if (!empty($id)) {
			
			$producto = $this->MercadoLibr->find('first', array('conditions' => array('id' => $id)));

			if (!empty($producto) && !empty($producto['MercadoLibr']['id_meli'])) {
				$this->admin_actualizar($producto);
			}

			if (!empty($producto) && empty($producto['MercadoLibr']['id_meli'])) {
				$this->admin_publicar($producto);
			}
		}else{
			$this->Session->setFlash('Error al publicar en Mercado libre. Detalles del error:<br> Identificador del producto no existe.', null, array(), 'danger');
			$this->redirect(array('action' => 'edit', $id));
		}
	}


	public function admin_otenerDetalleItems($items = '')
	{
		$array = array();
		$itemses = to_array($items['body']->results);
		foreach ($itemses as $key => $value) {
			$array[$key] = $this->admin_verProducto($value);
		}

		#prx($array);
	}


	public function admin_totalVisitas($desde = '', $hasta = '', $json = true)
	{
		$visitas = to_array($this->Meli->getMonthlyFlow($desde, $hasta));
		
		if ($visitas['httpCode'] < 300) {
			if ($json){
				echo json_encode($visitas);
				exit;	
			}else{
				return $visitas;
			}
		}else{
			return 0;
		}
	}


	


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

		foreach ($this->request->data['Filtro'] as $campo => $valor) {
			if (!empty($valor)) {
				$redirect[$campo] = $valor;
			}
		}

    	$this->redirect($redirect);

    }


	public function admin_index()
	{	

		$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;

		// Filtrado por formulario
		if ( $this->request->is('post') ) {

			$this->filtrar('mercadoLibres', 'index');

		}

		$paginate = array_replace_recursive($paginate, array(
			'recursive'		=> 0,
			'conditions' => array(
				'MercadoLibr.tienda_id' => $this->Session->read('Tienda.id')
				),
			'contain' => array(
				'Tienda',
				'Marketplace' => array(
					'MarketplaceTipo'
				)
			),
			'order' => array('MercadoLibr.id' => 'DESC'),
			'limit' => 20
			)
		);


		if ($this->Session->check('Marketplace.id')) {
			$paginate = array_replace_recursive($paginate, array(
				'conditions' => array(
					'MercadoLibr.marketplace_id' => $this->Session->read('Marketplace.id')
				)
			));
		}


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'by':
						if ($valor == 'ide' && isset($this->request->params['named']['txt'])) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('MercadoLibr.id LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}

						if ($valor == 'idm' && isset($this->request->params['named']['txt'])) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('MercadoLibr.id_meli LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}

						if ($valor == 'nam' && isset($this->request->params['named']['txt'])) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('MercadoLibr.nombre LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}
						
						break;
					case 'marketplace_id':
						
						$this->Session->write('Marketplace', ClassRegistry::init('Marketplace')->find('first', array('conditions' => array('id' => $valor)))['Marketplace'] );
						
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'MercadoLibr.marketplace_id' => $valor
							)
						));
						break;
				}
			}
		}

		$this->paginate = $paginate;

		# Mercadolibre conectar
		$ventasController = new VentasController(new CakeRequest(), new CakeResponse());
		$meliConexion = $ventasController->admin_verificar_conexion_meli(array('controller' => $this->request->controller, 'action' => $this->request->action));
		
		$marketplaces          = ClassRegistry::init('Marketplace')->find('list', array('conditions' => array('activo' => 1, 'marketplace_tipo_id' => 2)));

		BreadcrumbComponent::add('Mercado Libre Productos ');


		$total =  $this->MercadoLibr->find('count', $paginate);

		$mercadoLibres	= $this->paginate();

		# Se agrega el item de merado libre
		foreach ($mercadoLibres as $im => $itm) {
			if (!empty($itm['MercadoLibr']['id_meli']) && $this->Session->check('Marketplace')) {
				# Creamos cliente Meli
				$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );
				$mercadoLibres[$im]['MeliItem'] = $this->MeliMarketplace->mercadolibre_obtener_producto($itm['MercadoLibr']['id_meli']);	
			}
		}	

		$this->set(compact('mercadoLibres', 'total', 'totalMostrados', 'meliConexion', 'marketplaces'));
	}


	public function admin_add()
	{	
		if (!$this->Session->check('Marketplace.id')) {
			$this->Session->setFlash('¡Error!. Debe seleccionar un Marketplace para continuar.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Creamos cliente Meli
		$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );
		
		$this->MeliMarketplace->mercadolibre_conectar('', $this->Session->read('Marketplace'));


		if ( $this->request->is('post') )
		{	

			$this->request->data['MercadoLibr']['administrador_id'] = $this->Auth->user('id');

			for ( $i = 1; $i < 6; $i++ ) { 
				if (!isset($this->request->data['MercadoLibr']['categoria_0' . $i])) {
					$this->request->data['MercadoLibr']['categoria_0' . $i] = '';
				}
			}

			$this->request->data['MercadoLibr']['nombre']              = $this->request->data['MercadoLibr']['producto'];
			$this->request->data['MercadoLibr']['seller_custom_field'] = $this->request->data['MercadoLibr']['id_product'];
			
			$this->MercadoLibr->create();
			if ( $this->MercadoLibr->save($this->request->data) )
			{	
				$this->admin_validar_meli($this->MercadoLibr->id);
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		

		BreadcrumbComponent::add('Mercado Libre Productos', '/mercadoLibres');
		BreadcrumbComponent::add('Agregar ');

		$categoriasRoot        = $this->admin_obtenerCategorias(false);
		$tipoPublicacionesMeli = $this->MeliMarketplace->mercadolibre_tipo_publicacion(null, true);
		$condicionProducto     = array('new' => 'Nuevo');
		$marketplaces          = ClassRegistry::init('Marketplace')->find('list', array('conditions' => array('activo' => 1, 'marketplace_tipo_id' => 2)));
		$tiendasOficialesRes      = $this->MeliMarketplace->admin_obtener_tiendas_oficiales($this->Session->read('Marketplace.seller_id'));
			
		$tiendasOficiales = array();
		foreach ($tiendasOficialesRes as $i => $tof) {
			$tiendasOficiales[$tof['official_store_id']] = $tof['name'];
		}

		$this->set(compact('plantillas', 'categoriasRoot', 'tipoPublicacionesMeli', 'condicionProducto', 'marketplaces', 'tiendasOficiales'));

	}


	public function admin_edit($id = null)
	{	

		if (!$this->Session->check('Marketplace.id')) {
			$this->Session->setFlash('¡Error!. Debe seleccionar un Marketplace para continuar.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Creamos cliente Meli
		$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );
		
		$this->MeliMarketplace->mercadolibre_conectar('', $this->Session->read('Marketplace'));

		if ( ! $this->MercadoLibr->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			#$this->request->data['MercadoLibr']['description'] = $this->createHtml();

			for ( $i = 1; $i < 6; $i++ ) { 
				if (!isset($this->request->data['MercadoLibr']['categoria_0' . $i])) {
					$this->request->data['MercadoLibr']['categoria_0' . $i] = '';
				}
			}

			$this->request->data['MercadoLibr']['nombre']              = $this->request->data['MercadoLibr']['producto'];
			$this->request->data['MercadoLibr']['seller_custom_field'] = $this->request->data['MercadoLibr']['id_product'];
			
			
			if ( $this->MercadoLibr->save($this->request->data) )
			{	
				$this->admin_validar_meli($id);
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		else
		{
			$this->request->data	= $this->MercadoLibr->find('first', array(
				'conditions'	=> array('MercadoLibr.id' => $id),
				'contain' => array(
					'Marketplace' => array(
						'MarketplaceTipo'
					)
				)
			));
		}

		if (empty($this->request->data['Marketplace'])) {
			$this->Session->setFlash('¡Error! Item no tiene un Marketplace asociado.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}		

		BreadcrumbComponent::add('Mercado Libre Productos', '/mercadoLibres');
		BreadcrumbComponent::add('Editar ');

		
		$producto = ClassRegistry::init('Productotienda')->find('first', array(
			'conditions' => array('Productotienda.id_product' => $this->request->data['MercadoLibr']['id_product']),
			'contain' => array('Lang')
			));
		

		$categoriasRoot = $this->admin_obtenerCategorias(false);
		$categoriasHojas = array();

		# Recoreemos por los 6 nievels de categorias de Mercadolibre
		for ( $i = 1; $i < 6; $i++ ) { 
			if (!empty($this->request->data['MercadoLibr']['categoria_0' . $i])) {

				$categoriasHojasProducto = $this->admin_obtenerCategoriasId($this->request->data['MercadoLibr']['categoria_0' . ($i - 1)], false);

				foreach ($categoriasHojasProducto as $in => $categoria) {
					$categoriasHojas[$i][$categoria['id']] = $categoria['name'];
				}
			}
		}

		$tipoPublicacionesMeli = $this->MeliMarketplace->mercadolibre_tipo_publicacion(null, true);
		$condicionProducto = array('new' => 'Nuevo');

		# Envio
		$envio = $this->admin_envioDisponible($this->request->data['MercadoLibr']['categoria_hoja']);
		
		$meliItem = $this->admin_verProducto($this->request->data);
		$meliItemShipping = $this->MeliMarketplace->mercadolbre_obtener_metodo_envio_item($this->request->data['MercadoLibr']['id_meli']);
		$marketplaces = ClassRegistry::init('Marketplace')->find('list', array('conditions' => array('activo' => 1, 'marketplace_tipo_id' => 2)));
		
		$tiendasOficialesRes      = $this->MeliMarketplace->admin_obtener_tiendas_oficiales($this->Session->read('Marketplace.seller_id'));
			
		$tiendasOficiales = array();
		foreach ($tiendasOficialesRes as $i => $tof) {
			$tiendasOficiales[$tof['official_store_id']] = $tof['name'];
		}

		$this->set(compact('producto', 'categoriasRoot', 'categoriasHojas', 'tipoPublicacionesMeli', 'condicionProducto', 'meliItem', 'envio', 'meliItemShipping', 'marketplaces', 'tiendasOficiales'));
	}


	public function admin_view($id = null)
	{
		if ( ! $this->MercadoLibr->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') )
		{	
			$this->request->data	= $this->MercadoLibr->find('first', array(
				'conditions'	=> array('MercadoLibr.id' => $id)
			));

			$html = $this->createHtml();
			
			BreadcrumbComponent::add('Mercado Libre Productos', '/mercadoLibres');
			BreadcrumbComponent::add('Ver Html ');

			$this->set(compact('html'));
		}else{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}
	}


	public function admin_delete($id = null, $meli = null)
	{
		$this->MercadoLibr->id = $id;
		if ( ! $this->MercadoLibr->exists() )
		{		
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if (!empty($meli)) {
			# Creamos cliente Meli
			$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );

			$this->MeliMarketplace->mercadolibre_conectar('', $this->Session->read('Marketplace'));

			# Se cambia el estado a cerrado y se elimina
			$this->MeliMarketplace->mercadolibre_cambiar_estado($meli, 'closed');
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->MercadoLibr->delete() )
		{	
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}


	public function admin_exportar()
	{
		$datos			= $this->MercadoLibr->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->MercadoLibr->_schema);
		$modelo			= $this->MercadoLibr->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}



	public function admin_obtener_productos( $palabra = '') {
    	if (empty($palabra)) {
    		echo json_encode(array('0' => array('value' => '', 'label' => 'Ingrese id del producto')));
    		exit;
    	}

    	// Obtenemos la información de a tienda
		$tienda = ClassRegistry::init('Tienda')->find('first', array(
			'conditions' => array('Tienda.activo' => 1, 'Tienda.id' => $this->Session->read('Tienda.id'))
			));
		
		// Virificar existencia de la tienda
		if (empty($tienda)) {
			echo json_encode(array('0' => array('value' => '', 'label' => 'Error a obtener datos')));
    		exit;
		}

		// Verificar que la tienda esté configurada
		if (empty($tienda['Tienda']['prefijo']) || empty($tienda['Tienda']['prefijo']) || empty($tienda['Tienda']['configuracion'])) {
			echo json_encode(array('0' => array('value' => '', 'label' => 'Error a obtener datos, verifique la configuración de la tienda')));
    		exit;
		}
   		
   		/*******************************************
		 * 
		 * Aplicar a todos los modelos dinámicos
		 * 
		 ******************************************/
   		$this->cambiarConfigDB($tienda['Tienda']['configuracion']);

   		$hostImagenes = (!empty($tienda['Tienda']['url_almaceamiento_externo'])) ? $tienda['Tienda']['url_almaceamiento_externo'] : $tienda['Tienda']['url'];

   		// Buscamos los productos que cumplan con el criterio
		$productos	= $this->MercadoLibr->Productotienda->find('all', array(
			'fields' => array(
				'concat(\'https://' . $hostImagenes . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-home_default.jpg\' ) AS url_image_thumb',
				'concat(\'https://' . $hostImagenes . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-full_default.jpg\' ) AS url_image_large',
				'Productotienda.id_product',
				'Productotienda.id_category_default',
				'pl.name', 
				'pl.description_short',
				'Productotienda.price', 
				'pl.link_rewrite', 
				'Productotienda.reference', 
				'Productotienda.show_price',
				'Productotienda.quantity',
				'StockDisponible.quantity',
			),
			'joins' => array(
				array(
		            'table' => sprintf('%sproduct_lang', $tienda['Tienda']['prefijo']),
		            'alias' => 'pl',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Productotienda.id_product=pl.id_product'
		            )

	        	),
	        	array(
		            'table' => sprintf('%simage', $tienda['Tienda']['prefijo']),
		            'alias' => 'im',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Productotienda.id_product = im.id_product',
                		'im.cover' => 1
		            )
	        	),
	        	array(
		            'table' => sprintf('%sstock_available', $tienda['Tienda']['prefijo']),
		            'alias' => 'StockDisponible',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Productotienda.id_product = StockDisponible.id_product'
		            )
	        	)
			),
			'contain' => array(
				'Lang',
				'Especificacion' => array('Lang'),
				'EspecificacionValor' => array('Lang'),
				'TaxRulesGroup' => array(
					'TaxRule' => array(
						'Tax'
					)
				),
				'SpecificPrice' => array(
					'conditions' => array(
						'OR' => array(
							array(
								'SpecificPrice.from <= "' . date('Y-m-d H:i:s') . '"',
								'SpecificPrice.to >= "' . date('Y-m-d H:i:s') . '"'
							),
							array(
								'SpecificPrice.from' => '0000-00-00 00:00:00',
								'SpecificPrice.to >= "' . date('Y-m-d H:i:s') . '"'
							),
							array(
								'SpecificPrice.from' => '0000-00-00 00:00:00',
								'SpecificPrice.to' => '0000-00-00 00:00:00'
							),
							array(
								'SpecificPrice.from <= "' . date('Y-m-d H:i:s') . '"',
								'SpecificPrice.to' => '0000-00-00 00:00:00'
							)
						)
					)
				),
				'SpecificPricePriority'
			),
			'conditions' => array(
				'Productotienda.active' => 1,
				'Productotienda.available_for_order' => 1,
				'Productotienda.id_shop_default' => 1,
				'Productotienda.id_product' => $palabra
			),
			'limit' => 3
		));
   		
   		if (empty($productos)) {
    		echo json_encode(array('0' => array('id' => '', 'value' => 'No se encontraron coincidencias')));
    		exit;
    	}
    

    	foreach ($productos as $index => $producto) {

    		$textoDescripcion = 'Descripción del artículo' . "\n". "\n";

    		if ( !isset($producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']) ) {
				$producto['Productotienda']['valor_iva'] = $producto['Productotienda']['price'];	
			}else{
				$producto['Productotienda']['valor_iva'] = $this->precio($producto['Productotienda']['price'], $producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']);
			}
			

			// Criterio del precio específico del producto
			foreach ($producto['SpecificPricePriority'] as $criterio) {
				$precioEspecificoPrioridad = explode(';', $criterio['priority']);
			}

			$producto['Productotienda']['valor_final'] = $producto['Productotienda']['valor_iva'];

			// Retornar último precio espeficico según criterio del producto
			foreach ($producto['SpecificPrice'] as $precio) {
				if ( $precio['reduction'] == 0 ) {
					$producto['Productotienda']['valor_final'] = $producto['Productotienda']['valor_iva'];

				}else{

					$producto['Productotienda']['valor_final'] = $this->precio($producto['Productotienda']['valor_iva'], ($precio['reduction'] * 100 * -1) );
					$producto['Productotienda']['descuento'] = ($precio['reduction'] * 100 * -1 );

				}
			}

			# Stock
			$stock = $producto['Productotienda']['quantity'];
			if (!empty($producto['StockDisponible']['quantity'])) {
				$stock = $producto['StockDisponible']['quantity'];
			}

			$textoDescripcion .= nl2br(strip_tags($producto['Lang'][0]['ProductotiendaIdioma']['description_short'])) . "\n" . "\n";

			# Especificaciones
			if (!empty($producto['Especificacion']) && !empty($producto['EspecificacionValor'])) {
	    		
				$textoDescripcion .= 'Especificaciones del artículo' . "\n". "\n";

	    		foreach ($producto['Especificacion'] as $indice => $especificacion) {
	    			foreach ($producto['EspecificacionValor'] as $key => $especificacionvalor) {
	    				if ($especificacion['id_feature'] == $especificacionvalor['id_feature']) {
	    					$textoDescripcion .= '-' . $especificacion['Lang'][0]['EspecificacionIdioma']['name'] . ': ' . $especificacionvalor['Lang'][0]['EspecificacionValorIdioma']['value'] . "\n";
	    				}
	    			}
	    		}

    		}

    		$arrayProductos[$index]['id'] = $producto['Productotienda']['id_product'];
    		$arrayProductos[$index]['value'] = $producto['Productotienda']['reference'];
			$arrayProductos[$index]['nombre'] = sprintf('%s', $producto['Lang'][0]['ProductotiendaIdioma']['name']);
			$arrayProductos[$index]['imagen'] = sprintf('%s', $producto[0]['url_image_large']);
			$arrayProductos[$index]['precio'] = sprintf('%s', $producto['Productotienda']['valor_final']);
			$arrayProductos[$index]['stock'] = sprintf('%s', $stock);
			//$arrayProductos[$index]['name'] = $producto['Lang'][0]['ProductotiendaIdioma']['name'];
			//$arrayProductos[$index]['image'] = $producto[0]['url_image'];
			$arrayProductos[$index]['description'] = $textoDescripcion;
			//$arrayProductos[$index]['spec'] = $producto['Especificacion'];
    	}

    	echo json_encode($arrayProductos, JSON_FORCE_OBJECT);
    	exit;
    }


	public function createHtml()
	{	
		# Html plantilla a utilizar
		$plantillaHtml = $this->MercadoLibr->MercadoLibrePlantilla->find('first', array('conditions' => array('MercadoLibrePlantilla.id' => $this->request->data['MercadoLibr']['mercado_libre_plantilla_id'])));

		# Producto
		# 
		// Obtenemos la información de a tienda
		$tienda = ClassRegistry::init('Tienda')->find('first', array(
			'conditions' => array('Tienda.activo' => 1, 'Tienda.id' => $this->Session->read('Tienda.id'))
			));
		
		// Virificar existencia de la tienda
		if (empty($tienda)) {
			echo json_encode(array('0' => array('value' => '', 'label' => 'Error a obtener datos')));
    		exit;
		}

		// Verificar que la tienda esté configurada
		if (empty($tienda['Tienda']['prefijo']) || empty($tienda['Tienda']['prefijo']) || empty($tienda['Tienda']['configuracion'])) {
			echo json_encode(array('0' => array('value' => '', 'label' => 'Error a obtener datos, verifique la configuración de la tienda')));
    		exit;
		}
   		
   		/*******************************************
		 * 
		 * Aplicar a todos los modelos dinámicos
		 * 
		 ******************************************/
   		$this->cambiarConfigDB($tienda['Tienda']['configuracion']);

   		$producto = $this->MercadoLibr->Productotienda->find('first', array(
   			'fields' => array(
				'concat(\'http://' . $tienda['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-large_default.jpg\' ) AS url_image',
				'Productotienda.id_product',
				'Productotienda.reference',
				'pl.name', 
			),
   			'conditions' => array(
   				'Productotienda.id_product' => $this->request->data['MercadoLibr']['id_product']
   			),
   			'joins' => array(
   				array(
		            'table' => sprintf('%sproduct_lang', $tienda['Tienda']['prefijo']),
		            'alias' => 'pl',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Productotienda.id_product=pl.id_product'
		            )
	        	),
   				array(
		            'table' => sprintf('%simage', $tienda['Tienda']['prefijo']),
		            'alias' => 'im',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Productotienda.id_product = im.id_product',
		                'im.cover' => 1
		            )
	        	)
   			),
   			'contain' => array(
   				'Lang',
   				'Especificacion' => array('Lang'),
				'EspecificacionValor' => array('Lang')
				)
			)
   		);
   		
   		if (empty($producto)) {
    		echo json_encode(array('0' => array('id' => '', 'value' => 'No se encontraron coincidencias')));
    		exit;
    	}

    	$plantillaPredefinida = $this->MercadoLibr->armarHtml($plantillaHtml);

    	#reemplazar cabecera
    	# Imagen
    	$plantillaPredefinida['cabecera'] = str_replace('[IMG]', $producto[0]['url_image'], $plantillaPredefinida['cabecera']);
    	# Imagen alt
    	$plantillaPredefinida['cabecera'] = str_replace('[ALT]', $producto['Lang'][0]['ProductotiendaIdioma']['name'], $plantillaPredefinida['cabecera']);
    	# Nombre
    	$plantillaPredefinida['cabecera'] = str_replace('[NAME]', $producto['Lang'][0]['ProductotiendaIdioma']['name'], $plantillaPredefinida['cabecera']);
    	# Descripcion corta
    	$plantillaPredefinida['cabecera'] = str_replace('[DESC]', $producto['Lang'][0]['ProductotiendaIdioma']['description_short'], $plantillaPredefinida['cabecera']);

    	# Guarda el html de las especificaciones
    	$especificacionHtml = array();
    
    	if (!empty($producto['Especificacion']) && !empty($producto['EspecificacionValor'])) {
    		
    		# Unimos la especificacion con su valor
    		$arrayEspecificacion = array(
    			'Especificacion' => array()
    			); 
    		
    		foreach ($producto['Especificacion'] as $indice => $especificacion) {
    			foreach ($producto['EspecificacionValor'] as $key => $especificacionvalor) {
    				if ($especificacion['id_feature'] == $especificacionvalor['id_feature']) {
    					$arrayEspecificacion['Especificacion'][$indice]['nombre'] = $especificacion['Lang'][0]['EspecificacionIdioma']['name'];
    					$arrayEspecificacion['Especificacion'][$indice]['valor'] = $especificacionvalor['Lang'][0]['EspecificacionValorIdioma']['value'];
    				}
    			}
    		}

    		# Se unen los valores en el Html
    		foreach ($arrayEspecificacion['Especificacion'] as $key => $valor) {
    			if (isset($plantillaPredefinida['if_uno']) && isset($plantillaPredefinida['if_dos'])) {
    				if($key%2 == 0) {
	    				$especificacionHtml[$key]['fila'] = str_replace('[SPEC_NAME]', $valor['nombre'], $plantillaPredefinida['if_uno']);
	    				$especificacionHtml[$key]['fila'] = str_replace('[SPEC_VAL]', $valor['valor'], $especificacionHtml[$key]['fila']);
	    			}else{
	    				$especificacionHtml[$key]['fila'] = str_replace('[SPEC_NAME]', $valor['nombre'], $plantillaPredefinida['if_dos']);
	    				$especificacionHtml[$key]['fila'] = str_replace('[SPEC_VAL]', $valor['valor'], $especificacionHtml[$key]['fila']);
	    			}
    			}else{
    				$especificacionHtml[$key]['fila'] = str_replace('[SPEC_NAME]', $valor['nombre'], $plantillaPredefinida['if_uno']);
	    			$especificacionHtml[$key]['fila'] = str_replace('[SPEC_VAL]', $valor['valor'], $especificacionHtml[$key]['fila']);
    			}
    		}
    	
    	}

    	
    	# Html final
    	$htmlFinal =  $plantillaPredefinida['cabecera'];
    	foreach ($especificacionHtml as $especificacion) {
    		$htmlFinal .= $especificacion['fila'];
    	}
    	$htmlFinal .=  $plantillaPredefinida['footer'];
    	
		return $htmlFinal;
	}

	/**
	 * Actualización de precios
	 *
	 */

	public function htmlResponse($res = array())
	{
		$html = '<ul>';
		foreach ($res as $key => $value) {
			if (isset($value['errors'])) {
				foreach ($value['errors'] as $k => $v) {
					$html .= '<li>Producto: ' . $v['id'] . ' - ' . $v['producto'] . '<br> Error: ' . $v['mensaje'] . '</li>';
				}
			}
		}
		$html .= '</ul>';

		return $html;
	}

	public function verificarCambiosDePreciosStock($console = false)
	{
		if (!$console && $this->Session->check('Tienda.id')) {
			# Obtenemos las tiendas configuradas
			$tiendas = ClassRegistry::init('Tienda')->find('all', array('conditions' => array('Tienda.id' => $this->Session->read('Tienda.id'))));
		}else{
			$tiendas = ClassRegistry::init('Tienda')->find('all');
		}

		# Variable que almacena los productos
		$t = array();

		# Obtenemos productos por tiendas
		foreach ($tiendas as $indice => $tienda) {
			$t[$tienda['Tienda']['configuracion']] = $this->getProductsMeli($tienda);
		}

		# Comparamos los precios para ver si hay alguna diferencia. En la primera diferencia se detiene y retorna true; 
		foreach ($t as $ix => $productos) {
			foreach ($productos as $producto) {
				if ($producto['MercadoLibr']['precio'] != $producto['Productotienda']['precio'] || $producto['MercadoLibr']['cantidad_disponible'] != $producto['Productotienda']['stock'] ) {
					return true;
				}
			}
		}

		return false;

	}

	/**
	 * Actualiza el precio y stock de los productos publicados en meli según sesión activa
	 */
	public function admin_sincronizar()
	{	

		# Creamos cliente Meli
		$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );
		
		$this->MeliMarketplace->mercadolibre_conectar('', $this->Session->read('Marketplace'));
		
		# Obtener productos meli 
		$items = $this->MeliMarketplace->mercadolibre_obtener_todos_productos($this->Session->read('Marketplace.seller_id'));

		if ( empty($this->Session->read('Tienda.apiurl_prestashop')) || empty($this->Session->read('Tienda.apikey_prestashop')) )
		{
			$this->Session->setFlash('No es posible sincronizar los productos ya que la tienda no está configurada correctamente.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}
			
		# Se crea cliente prestahsop
		$this->Prestashop->crearCliente($this->Session->read('Tienda.apiurl_prestashop'), $this->Session->read('Tienda.apikey_prestashop'));
		
		$margen_adicional = $this->Session->read('Marketplace.porcentaje_adicional');
		$agregar_despacho = $this->Session->read('Marketplace.agregar_despacho_costo');

		foreach ($items as $ip => $producto) 
		{

			# Obtenemos el item de meli
			$itml = $this->MeliMarketplace->mercadolibre_obtener_producto($producto);

			if (empty($itml['seller_custom_field']))
				continue;
			
			
			$precio = $this->Prestashop->prestashop_obtener_precio_producto($itml['seller_custom_field']);
			$stock = $this->Prestashop->prestashop_obtener_stock_producto($itml['seller_custom_field']);
			$sync = $this->sincronizar($producto, $stock['stock_available']['quantity'], $precio['product']['final_price'], $margen_adicional, $agregar_despacho);
		
			if ($sync)
			{
				$productos[] = 'Item <a href="' . $sync['permalink'] . '" target="_blank">' . $producto . '</a> actualizado - Nuevo stock: ' .  $stock['stock_available']['quantity'] . ' - Nuevo precio: ' . $sync['price'];
			}
			else
			{
				$productos[] = 'Item ' . $producto . ' no pudo ser actualizado.';
			}
		}
		
		$urlReponse = $this->crearAlertaUl($productos);	

		$this->Session->setFlash($urlReponse , null, array(), 'flash');
		$this->redirect($this->referer('/', true));
	}


	/**
	 * Sincroniza todos los items publicados en meli de todos los marketplaces configurados
	 * 
	 * @return array  Arreglo de productos procesados
	 */
	public function sincronizar_todo()
	{
		$melis = ClassRegistry::init('Marketplace')->find('all', array('conditions' => array(
			'marketplace_tipo_id' => 2, // Meli
			'activo' => 1,
			'api_user <>' => '',
			'api_key <>' => '' 
		)));

		$productos = array();

		foreach ($melis as $ml) {
			
			$this->MeliMarketplace = $this->Components->load('MeliMarketplace');
			$this->Prestashop = $this->Components->load('Prestashop');

			# Creamos cliente Meli
			$this->MeliMarketplace->crearCliente($ml['Marketplace']['api_user'], $ml['Marketplace']['api_key'], $ml['Marketplace']['access_token'], $ml['Marketplace']['refresh_token'] );
			
			$this->MeliMarketplace->mercadolibre_conectar('', $ml['Marketplace']);
			
			# Obtener productos meli 
			$items = $this->MeliMarketplace->mercadolibre_obtener_todos_productos($ml['Marketplace']['seller_id']);
			
			# Tienda
			$tienda 	= $this->tiendaInfo($ml['Marketplace']['tienda_id']);

			if (empty($tienda['Tienda']['apiurl_prestashop']) || empty($tienda['Tienda']['apikey_prestashop']))
				continue;

			# Se crea cliente prestahsop
			$this->Prestashop->crearCliente($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop']);
			
			$margen_adicional = $ml['Marketplace']['porcentaje_adicional'];
			$agregar_despacho = $ml['Marketplace']['agregar_despacho_costo'];

			foreach ($items as $ip => $producto) {

				# Obtenemos el item de meli
				$itml = $this->MeliMarketplace->mercadolibre_obtener_producto($producto);

				if (empty($itml['seller_custom_field']))
					continue;
				
				
				$precio = $this->Prestashop->prestashop_obtener_precio_producto($itml['seller_custom_field']);
				$stock = $this->Prestashop->prestashop_obtener_stock_producto($itml['seller_custom_field']);
				$sync = $this->sincronizar($producto, $stock['stock_available']['quantity'], $precio['product']['final_price'], $margen_adicional, $agregar_despacho);

				$productos[$ml['Marketplace']['id']][] = array(
					'item' => $itml,
					'precio_venta' => $precio,
					'stock_real' => $stock,
					'actualizado' => $sync
				);

			}
		}

		return $productos;
	}



	public function admin_actualizarPreciosStock($console = false)
	{	
		if ($console) {
			$url = $this->autorizacionMeli(Router::url(array('controller' => 'mercadoLibres', 'action' => 'actualizarPreciosStock', 1)));
			if ( !empty($url) ) {
				$code = $this->Meli->getCode($url);
			}
		}else{
			$auth = $this->autorizacionMeli();
			if (!empty($auth)) {
				$this->Session->setFlash('Imposible actualizar los precios en Mercado libre. Detalles del error:<br> La sesión de Mercado libre expiró. Conecte nuevamente la aplicación.', null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}
		}

		if ($this->Session->check('Tienda.id')) {
			# Obtenemos las tiendas configuradas
			$tiendas = ClassRegistry::init('Tienda')->find('all', array('conditions' => array('Tienda.id' => $this->Session->read('Tienda.id'))));
		}else{
			$tiendas = ClassRegistry::init('Tienda')->find('all');
		}
		
		# Variable que almacena los productos
		$productos = array();

		# Obtenemos productos por tiendas
		foreach ($tiendas as $indice => $tienda) {
			$productos[$tienda['Tienda']['configuracion']] = $this->getProductsMeli($tienda);
		}
		
		# Actualizamos de los productos publicados, tanto interna como en MELI
		$result = $this->sincronizarPreciosStock($productos);

		/*if (!$result['Interno']['res'] && !$result['Meli']['res']) {
			$this->Session->setFlash('Imposible actualizar los precios en Mercado libre. Intente nuevamente.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($result['Interno']['res'] && !$result['Meli']['res']) {
			$this->Session->setFlash('Sólo se logró actualizar el precio de los productos interno y no de Mercado libre. Intente nuevamente.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if (!$result['Interno']['res'] && $result['Meli']['res']) {
			$this->Session->setFlash('Sólo se logró actualizar el precio de Mercado libre y no de los productos internos. Intente nuevamente.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($result['Interno']['res'] && $result['Meli']['res']) {
			$this->Session->setFlash('¡Éxito!<br> Se actualizó el precio de todos los productos.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}*/
		
		$urlReponse = $this->htmlResponse($result);	

		$this->Session->setFlash('Resultados de la operación: <br>' . $urlReponse , null, array(), 'flash');
		$this->redirect(array('action' => 'index'));
	}


	/**
	 * Actualizar precio de un item en meli
	 * 
	 * @param var $id_meli  Identificador del producto en meli
	 * @param int $stock  Nuevo stock del item
	 * @param int $precio_base Precio base del producto
	 * @param float $margen_adicional Porcentaje adicional de aumento al $precio_base
	 * @param bool $agregar_costo_envio Agrega al precio_base el costo de envio del producto
	 * 
	 * @return bool
	 */
	public function sincronizar($id_meli, $stock, $precio_base, $margen_adicional = 0, $agregar_costo_envio = true)
	{
		// We construct the item to POST
		$item = array(
			"price"  => $precio_base,
			"available_quantity" => $stock
		);

		
		# Actualizamos el precio agregandole el costo de envio
		if ($agregar_costo_envio) {
			$costoEnvio = $this->MeliMarketplace->mercadolibre_obtener_costo_envio($id_meli);
			$item['price'] = $item['price'] + $costoEnvio;
		}
		
		if ($margen_adicional > 0) {
			$item['price'] = calcular_sobreprecio($item['price'], $margen_adicional);
		}

		$item['price'] = round($item['price']);
		
		$meliRespuesta = $this->MeliMarketplace->update($id_meli, $item);
				
		if (!empty($meliRespuesta)) 
		{
			if ($meliRespuesta['httpCode'] < 300) 
			{
				return $meliRespuesta['body'];
			}
		}

		return false;
	}


	public function sincronizarPreciosStock($tiendas = array())
	{	
		$out = array();
		foreach ($tiendas as $k => $productos) {
			
			foreach ($productos as $i => $producto) {

				# Actualizamos el precio interno
				$this->MercadoLibr->id = $producto['MercadoLibr']['id'];
				if ( ! $this->MercadoLibr->saveField('precio', $producto['Productotienda']['precio']) || ! $this->MercadoLibr->saveField('cantidad_disponible', $producto['Productotienda']['stock']) ) {
					$out['Interno']['res'] = 0;
					$out['Interno']['errors'][$i]['id'] = $producto['MercadoLibr']['id'];
					$out['Interno']['errors'][$i]['producto'] = $producto['MercadoLibr']['producto'];
					$out['Interno']['errors'][$i]['mensaje'] = 'No fue posible actualizar el item en el sistema.';
				}else{
					$out['Interno']['res'] = 1;
					$out['Interno']['success'][$i]['id'] = $producto['MercadoLibr']['id'];
					$out['Interno']['success'][$i]['producto'] = $producto['MercadoLibr']['producto'];
					$out['Interno']['success'][$i]['precio_actual'] = $producto['MercadoLibr']['precio'];
					$out['Interno']['success'][$i]['precio_nuevo'] = $producto['Productotienda']['precio'];
				}

				# Verificamos que el producto esté publicado en mercadolibre
				if (!empty($producto['MercadoLibr']['id_meli'])) {
					

					$meliRespuesta = $this->MeliMarketplace->modified_item($producto['MercadoLibr']['id_meli'], $producto['Productotienda']['porcentaje_adicional']);

					# Actualizamos publicación existente en mercado libre
					$meliRespuesta = $this->Meli->updatePriceAndStockAndCustomField($producto['MercadoLibr']['id_meli'], $producto['Productotienda']['precio'], $producto['Productotienda']['stock'], $producto['Productotienda']['id']);
					
					$res = to_array($meliRespuesta);

					if ($res['httpCode'] < 300) {
						$out['Meli']['res'] = 1;
						$out['Meli']['success'][$i]['id'] = $producto['MercadoLibr']['id'];
						$out['Meli']['success'][$i]['meli'] = $producto['MercadoLibr']['id_meli'];
						$out['Meli']['success'][$i]['producto'] = $producto['MercadoLibr']['producto'];
						$out['Meli']['success'][$i]['precio_actual'] = $producto['MercadoLibr']['precio'];
						$out['Meli']['success'][$i]['precio_nuevo'] = $producto['Productotienda']['precio'];
					}else{
						$out['Meli']['res'] = 0;
						$out['Meli']['errors'][$i]['id'] = $producto['MercadoLibr']['id'];
						$out['Meli']['errors'][$i]['meli'] = $producto['MercadoLibr']['id_meli'];
						$out['Meli']['errors'][$i]['producto'] = $producto['MercadoLibr']['producto'];
						$out['Meli']['errors'][$i]['mensaje'] = $res['body']['message'];
					}
				}	
			}
		}

		return $out;
	}


	public function admin_actualizarPrecioPorCriterio()
	{	
		$tienda 	= $this->tiendaInfo($this->Session->read('Tienda.id'));

		$resultadosOperacion = array();

		#$opts['limit'] = 5;

		$opts['fields'] = array(
			'MercadoLibr.id', 
			'MercadoLibr.id_product', 
			'MercadoLibr.producto',
			'MercadoLibr.precio', 
			'MercadoLibr.id_meli', 
			'MercadoLibr.cantidad_disponible');

		$opts['conditions'] = array(
			'MercadoLibr.tienda_id' => $tienda['Tienda']['id'],
			'MercadoLibr.id_product !=' => null
		);

		foreach ($this->request->data['criterios'] as $ic => $criterio) {
			
			if (empty($criterio['criterio']) || empty($criterio['condicion']) || empty($criterio['valor'])) {
				$this->Session->setFlash('No es posible ejecutar la operación, ya que hay errores en la instrucción. Verifique que los campos estan completos.', null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}

			if ($criterio['criterio'] == 'OR') {
				/*
				if ($criterio['condicion'] == '=') {
					$opts['conditions'] = array_replace_recursive($opts['conditions'], array(
						sprintf("%s", $criterio['criterio']) => array('MercadoLibr.precio' => $criterio['valor'])
					));
				}else{
					$opts['conditions'] = array_replace_recursive($opts['conditions'], array(
						sprintf("%s", $criterio['criterio']) => array(sprintf('MercadoLibr.precio %s', $criterio['condicion']) => $criterio['valor'])
					));
				}*/
				
			}else{

				if ($criterio['condicion'] == '=') {
					$opts['conditions'] = array_replace_recursive($opts['conditions'], array( 'MercadoLibr.precio' => $criterio['valor'] ));
				}else{
					$opts['conditions'] = array_replace_recursive($opts['conditions'], array( sprintf('MercadoLibr.precio %s', $criterio['condicion']) => $criterio['valor'] ));
				}

			}

			

		}

		# Cambiamos la configuración de los modelos externos según la tienda
		$this->cambiarConfigDB($tienda['Tienda']['configuracion']);

		# Listamos productos de mercadolibre
		$productos = $this->MercadoLibr->find('all', $opts);
		
		#$log = $this->MercadoLibr->getDataSource()->getLog(false, false);

		foreach ($productos as $i => $producto) {
			$productos[$i]['Productotienda'] = $this->getProductPriceFromStore($producto['MercadoLibr']['id_product'], $tienda);

			# Agregamos el valor adicional a los productos
			if (isset($this->request->data['Criterio']['valor']) && !empty($this->request->data['Criterio']['valor'])) {
				$productos[$i]['Productotienda']['precio'] = $productos[$i]['Productotienda']['precio'] + $this->request->data['Criterio']['valor'];				
			}
			
		}


		# Actualizamos de los productos publicados, tanto interna como en MELI
		$result = $this->sincronizarPreciosStock($productos);
		
		$urlReponse = $this->htmlResponse($result);	

		$this->Session->setFlash('Resultados de la operación: <br>' . $urlReponse , null, array(), 'flash');
		$this->redirect(array('action' => 'index'));

	}


	public function getProductsMeli($store = array())
	{
		if (!empty($store)) {
			# Instanciamos el controlador APP
			#$app = new AppController(new CakeRequest(), new CakeResponse());
			
			# Cambiamos la configuración de los modelos externos según la tienda
			$this->cambiarConfigDB($store['Tienda']['configuracion']);

			# Listamos productos de mercadolibre
			$productos = $this->MercadoLibr->find('all', array(
				'fields' => array('id', 'id_product', 'producto' ,'precio', 'id_meli', 'cantidad_disponible'),
				'conditions' => array(
					'MercadoLibr.tienda_id' => $store['Tienda']['id'],
					'MercadoLibr.id_product !=' => null
				)));


			foreach ($productos as $i => $producto) {
				$productos[$i]['Productotienda'] = $this->getProductPriceFromStore($producto['MercadoLibr']['id_product'], $store);
			}
			
			return $productos;
		}
	}


	public function getProductPriceFromStore($idProduct = null, $store = array())
	{	

		if ( !empty($idProduct) && !empty($store)) {
			
			#$app = new AppController(new CakeRequest(), new CakeResponse());
			
			$this->cambiarConfigDB($store['Tienda']['configuracion']);
			
   			// Buscamos los productos que cumplan con el criterio
			$producto	= $this->MercadoLibr->Productotienda->find('first', array(
				'fields' => array(
					'concat(\'https://' . $store['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-home_default.jpg\' ) AS url_image_thumb',
					'concat(\'https://' . $store['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-thickbox_default.jpg\' ) AS url_image_large',
					'Productotienda.id_product',
					'Productotienda.id_category_default',
					'pl.name', 
					'pl.description_short',
					'Productotienda.price', 
					'pl.link_rewrite', 
					'Productotienda.reference', 
					'Productotienda.show_price',
					'Productotienda.quantity',
					'StockDisponible.quantity'
				),
				'joins' => array(
					array(
			            'table' => sprintf('%sproduct_lang', $store['Tienda']['prefijo']),
			            'alias' => 'pl',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'Productotienda.id_product=pl.id_product'
			            )

		        	),
		        	array(
			            'table' => sprintf('%simage', $store['Tienda']['prefijo']),
			            'alias' => 'im',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'Productotienda.id_product = im.id_product',
	                		'im.cover' => 1
			            )
		        	),
		        	array(
			            'table' => sprintf('%scategory_product', $store['Tienda']['prefijo']),
			            'alias' => 'CategoriaProducto',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'Productotienda.id_product = CategoriaProducto.id_product'
			            )
		        	),
		        	array(
			            'table' => sprintf('%sstock_available', $store['Tienda']['prefijo']),
			            'alias' => 'StockDisponible',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'Productotienda.id_product = StockDisponible.id_product'
			            )
		        	)
				),
				'contain' => array(
					'Lang',
					'TaxRulesGroup' => array(
						'TaxRule' => array(
							'Tax'
						)
					),
					'SpecificPrice' => array(
						'conditions' => array(
							'OR' => array(
								array(
									'SpecificPrice.from <= "' . date('Y-m-d H:i:s') . '"',
									'SpecificPrice.to >= "' . date('Y-m-d H:i:s') . '"'
								),
								array(
									'SpecificPrice.from' => '0000-00-00 00:00:00',
									'SpecificPrice.to >= "' . date('Y-m-d H:i:s') . '"'
								),
								array(
									'SpecificPrice.from' => '0000-00-00 00:00:00',
									'SpecificPrice.to' => '0000-00-00 00:00:00'
								),
								array(
									'SpecificPrice.from <= "' . date('Y-m-d H:i:s') . '"',
									'SpecificPrice.to' => '0000-00-00 00:00:00'
								)
							)
						)
					),
					'SpecificPricePriority'
				),
				'conditions' => array(
					'Productotienda.id_product' => $idProduct
				)
			));
			
			$arrayProducto = array();

			if (!empty($producto)) {

				
	    		if ( !isset($producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']) ) {
					$producto['Productotienda']['valor_iva'] = $producto['Productotienda']['price'];	
				}else{
					$producto['Productotienda']['valor_iva'] = $this->precio($producto['Productotienda']['price'], $producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']);
				}
				

				// Criterio del precio específico del producto
				foreach ($producto['SpecificPricePriority'] as $criterio) {
					$precioEspecificoPrioridad = explode(';', $criterio['priority']);
				}

				$producto['Productotienda']['valor_final'] = $producto['Productotienda']['valor_iva'];

				// Retornar último precio espeficico según criterio del producto
				foreach ($producto['SpecificPrice'] as $precio) {
					if ( $precio['reduction'] == 0 ) {
						$producto['Productotienda']['valor_final'] = $producto['Productotienda']['valor_iva'];

					}else{

						$producto['Productotienda']['valor_final'] = $this->precio($producto['Productotienda']['valor_iva'], ($precio['reduction'] * 100 * -1) );
						$producto['Productotienda']['descuento'] = ($precio['reduction'] * 100 * -1 );

					}
				}
				
				# Stock
				$stock = $producto['Productotienda']['quantity'];
				if (!empty($producto['StockDisponible']['quantity'])) {

					$stock = $producto['StockDisponible']['quantity'];
				}

	    		$arrayProducto['id'] = $producto['Productotienda']['id_product'];
				$arrayProducto['nombre'] = sprintf('%s', $producto['Lang'][0]['ProductotiendaIdioma']['name']);
				$arrayProducto['imagen'] = sprintf('%s', $producto[0]['url_image_large']);
				$arrayProducto['precio'] = sprintf('%s', $producto['Productotienda']['valor_final']);
				$arrayProducto['stock'] = sprintf('%s', $stock);
				//$arrayProducto[$index]['name'] = $producto['Lang'][0]['ProductotiendaIdioma']['name'];
				//$arrayProducto[$index]['image'] = $producto[0]['url_image'];
				//$arrayProducto[$index]['description'] = $producto['Lang'][0]['ProductotiendaIdioma']['description_short'];
				//$arrayProducto[$index]['spec'] = $producto['Especificacion'];
	    	}

			return $arrayProducto;
		}
	}


	public function admin_obtener_prediccion_categoria($titulo, $categoria = '', $precio = '')
	{	
		$out = array();

		#Creamos cliente Meli
		$this->MeliMarketplace->crearCliente( $this->Session->read('Marketplace.api_user'), $this->Session->read('Marketplace.api_key'), $this->Session->read('Marketplace.access_token'), $this->Session->read('Marketplace.refresh_token') );
		
		$categoriasResponse = $this->MeliMarketplace->mercadolibre_obtener_categoria_preferida($titulo, $categoria, $precio, $this->Session->read('Marketplace.seller_id'));
		
		$categoria = '';
		
		if ($categoriasResponse['httpCode'] == 200) {
			foreach ($categoriasResponse['body']->path_from_root as $ir => $cat) {
				if (count($categoriasResponse['body']->path_from_root) -1 == $ir) {
					$categoria .= $cat->name;
				}else{
					$categoria .= $cat->name . ' > ';
				}
				
			}
		}
		
		echo $categoria;
		exit;
	}
}
