<?php
App::uses('AppController', 'Controller');
#App::import('Vendor', 'Meli', array('file' => 'Meli/meli.php'));

class MercadoLibresController extends AppController
{	

	public $components = array('Meli');

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


	public function autorizacionMeli()
	{	
		if ( ! empty($this->request->query['code']) || ($this->Session->check('Meli.access_token') && !empty($this->Session->read('Meli.access_token'))) ) {
			if( isset($this->request->query['code']) && !$this->Session->check('Meli.access_token') ) {
				$this->Meli->login($this->request->query['code'], Router::url(array('controller' => 'mercadoLibres', 'action' => 'index'), true));
			} else {
				$this->Meli->checkTokenAndRefreshIfNeed();
			}
		}else{
			return $this->Meli->getAuthUrl(Router::url(array('controller' => 'mercadoLibres', 'action' => 'index'), true));
		}
	}


	public function admin_obtenerCategorias($json = true)
	{
		$response = $this->Meli->getSiteCategories();
		if ($response['httpCode'] != 200) {
			$response = '';
		}else{
			$response = json_decode(json_encode($response['body']), true);
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

		$response = $this->Meli->getCategoriesByIdentifier($id);

		if ($response['httpCode'] != 200) {
			return;
		}else{
			$response = json_decode(json_encode($response['body']), true);
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


	public function admin_verProducto($id = '')
	{
		if (empty($id)) {
			return '';
		}

		$itemInfo = to_array($this->Meli->viewItem($id));
		if ($itemInfo['httpCode'] != 200) {
			return '';
		}else{
			return $itemInfo['body'];
		}
	}


	public function admin_cambiarEstado($id= '' , $meli_id = '', $estado = '')
	{
		if (empty($id) || empty($estado) || empty($meli_id)) {
			return '';
		}

		$stateResponse = to_array($this->Meli->changeState($meli_id, $estado));
		if ($stateResponse['httpCode'] != 200) {
			
			$this->Session->setFlash('No fue posible actualizar el estado del item.', null, array(), 'danger');
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
		if (!empty($producto)) {
			$errores = '';
			# Verificamos que el producto no esté publicado en mercadolibre
			if (!empty($producto['MercadoLibr']['id_meli'])) {

				$imagenes = array(
					array(
					'source' => sprintf('"%s"', $producto['MercadoLibr']['imagen_meli'])
					)
				);

				# Actualizamos publicación existente en mercado libre
				$meliRespuesta = $this->Meli->update($producto['MercadoLibr']['id_meli'], $producto['MercadoLibr']['producto'], $producto['MercadoLibr']['precio'], $producto['MercadoLibr']['cantidad_disponible'], $producto['MercadoLibr']['id_video'], $imagenes);
			
				$publicarResponse = to_array($meliRespuesta);
				if ($meliRespuesta['httpCode'] == 200) {
					$meliRespuesta = 'updated';

					# actualizar Descripción

					$descriptionResponse = $this->Meli->updateDescription($producto['MercadoLibr']['id_meli'], $producto['MercadoLibr']['html']);

					$desc = to_array($descriptionResponse);

					$publicarResponse = to_array($desc);
					
					if ($publicarResponse['httpCode'] >= 300) { 
						$errores .= sprintf('<p>La descripción no pudo ser actualizada: Error %s</p>',$publicarResponse['body']['error']);
						$errores .= '<ul>';
						foreach ($publicarResponse['body']['cause'] as $causa) {
							$errores .= sprintf('<li>%s</li>', $causa['message']);
						}

						$errores .= '</ul>';
					}

				}else{
					$meliRespuesta = to_array($meliRespuesta);
				}
			}

			if (is_array($meliRespuesta)) {
					
				$errores .= sprintf('<p>%s</p>',$meliRespuesta['body']['error']);
				$errores .= '<p>Causas:</p><ul>';	
				
				foreach ($meliRespuesta['body']['cause'] as $causa) {
					$errores .= sprintf('<li>%s</li>', $causa['message']);
				}

				$errores .= '</ul>';

				$this->Session->setFlash('Producto no pudo ser editado en Mercado libre. Detalles del error:<br>' . $errores, null, array(), 'danger');
				$this->redirect(array('action' => 'edit', $producto['MercadoLibr']['id']));

			}else{

				$this->Session->setFlash('Producto editado correctamente en Mercado libre. <br><p>Recuerde que la actualización en Mercado libre no es automática. Ver producto <a href="' . $producto['MercadoLibr']['url_meli'] . '" target="_blank" class="btn btn-default btn-xs">aquí</a></p> ', null, array(), 'success');
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
					'source' => sprintf('"%s"', $producto['MercadoLibr']['imagen_meli'])
					)
				);

				$meliRespuesta = $this->Meli->publish($producto['MercadoLibr']['producto'], $producto['MercadoLibr']['categoria_hoja'], $producto['MercadoLibr']['precio'], 'CLP', $producto['MercadoLibr']['cantidad_disponible'], 'buy_it_now', $producto['MercadoLibr']['tipo_publicacion'], $producto['MercadoLibr']['condicion'], $producto['MercadoLibr']['html'], $producto['MercadoLibr']['id_video'], $producto['MercadoLibr']['garantia'], $imagenes);
				
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
		if (!empty($this->autorizacionMeli())) {
			$this->Session->setFlash('Error al publicar en Mercado libre. Detalles del error:<br> La sesión de Mercado libre expiró. Conecte nuevamente la aplicación.', null, array(), 'danger');
			$this->redirect(array('action' => 'edit', $id));
		}

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


	public function admin_usuario()
	{
		$miCuenta = array();
		if ($this->Session->check('Meli.access_token') && empty($this->autorizacionMeli())) {
			$miCuenta =  to_array($this->Meli->getMyAccountInfo());

			if ($miCuenta['httpCode'] != 200) {
				$miCuenta = '';
			}else{
				$miCuenta = $miCuenta['body'];
			}

		}

		$url = '';
		
		if (!empty($this->autorizacionMeli())) {
			$url = $this->autorizacionMeli();
		}

		BreadcrumbComponent::add('Mercado Libre Productos', '/mercadoLibres');
		BreadcrumbComponent::add('Mi cuenta ');
		
		$this->set(compact('miCuenta', $url));
	}


	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0,
			'conditions' => array(
				'MercadoLibr.tienda_id' => $this->Session->read('Tienda.id')
				),
			'order' => array('MercadoLibr.id' => 'DESC')
		);

		$url = '';
		
		if (!empty($this->autorizacionMeli())) {
			$url = $this->autorizacionMeli();
		}

		BreadcrumbComponent::add('Mercado Libre Productos ');

		prx($this->Meli->uploadFile('https://www.toolmania.cl/img/p/4/3/8/5/4385.jpg'));
		#prx($this->Meli->linkImageToItem('833366-MLC25840423212_082017', 'MLC447841638'));

		$mercadoLibres	= $this->paginate();
		$this->set(compact('mercadoLibres', 'url'));
	}


	public function admin_add()
	{	
		if ( $this->request->is('post') )
		{	

			$this->request->data['MercadoLibr']['html'] = $this->createHtml();

			for ( $i = 1; $i < 6; $i++ ) { 
				if (!isset($this->request->data['MercadoLibr']['categoria_0' . $i])) {
					$this->request->data['MercadoLibr']['categoria_0' . $i] = '';
				}
			}

			$this->request->data['MercadoLibr']['nombre'] = $this->request->data['MercadoLibr']['producto'];

			$this->MercadoLibr->create();
			if ( $this->MercadoLibr->save($this->request->data) )
			{	
				# Recien creado
				$ultimo = $this->MercadoLibr->find('first', array('order' => array('id' => 'DESC'), 'limit' => 1));
				$this->admin_validar_meli($ultimo['MercadoLibr']['id']);
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$url = '';
		
		if (!empty($this->autorizacionMeli())) {
			$url = $this->autorizacionMeli();
		}

		BreadcrumbComponent::add('Mercado Libre Productos', '/mercadoLibres');
		BreadcrumbComponent::add('Agregar ');

		$plantillas	= $this->MercadoLibr->MercadoLibrePlantilla->find('list', array('conditions' => array('activo' => 1)));
		$categoriasRoot = $this->admin_obtenerCategorias(false);
		$tipoPublicacionesMeli = $this->Meli->listing_types();
		$condicionProducto = array('new' => 'Nuevo');

		$this->set(compact('plantillas', 'url', 'categoriasRoot', 'tipoPublicacionesMeli', 'condicionProducto'));
	}


	public function admin_edit($id = null)
	{
		if ( ! $this->MercadoLibr->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			$this->request->data['MercadoLibr']['html'] = $this->createHtml();

			for ( $i = 1; $i < 6; $i++ ) { 
				if (!isset($this->request->data['MercadoLibr']['categoria_0' . $i])) {
					$this->request->data['MercadoLibr']['categoria_0' . $i] = '';
				}
			}

			$this->request->data['MercadoLibr']['nombre'] = $this->request->data['MercadoLibr']['producto'];


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
				'conditions'	=> array('MercadoLibr.id' => $id)
			));
		}

		BreadcrumbComponent::add('Mercado Libre Productos', '/mercadoLibres');
		BreadcrumbComponent::add('Editar ');

		$plantillas	= $this->MercadoLibr->MercadoLibrePlantilla->find('list', array('conditions' => array('activo' => 1)));
		$producto = ClassRegistry::init('Productotienda')->find('first', array(
			'conditions' => array('Productotienda.id_product' => $this->request->data['MercadoLibr']['id_product']),
			'contain' => array('Lang')
			));
		

		$categoriasRoot = $this->admin_obtenerCategorias(false);
		$categoriasHojas = array();

		# Recoreemos por los 4 nievels de categorias de Mercadolibre
		for ( $i = 1; $i < 6; $i++ ) { 
			if (!empty($this->request->data['MercadoLibr']['categoria_0' . $i])) {

				$categoriasHojasProducto = $this->admin_obtenerCategoriasId($this->request->data['MercadoLibr']['categoria_0' . ($i - 1)], false);

				foreach ($categoriasHojasProducto as $in => $categoria) {
					$categoriasHojas[$i][$categoria['id']] = $categoria['name'];
				}
			}
		}
		
		$url = '';
		
		if (!empty($this->autorizacionMeli())) {
			$url = $this->autorizacionMeli();
		}

		$tipoPublicacionesMeli = $this->Meli->listing_types();
		$condicionProducto = array('new' => 'Nuevo');

		$meliItem = $this->admin_verProducto($this->request->data['MercadoLibr']['id_meli']);

		$this->set(compact('plantillas', 'producto', 'categoriasRoot', 'categoriasHojas', 'url', 'tipoPublicacionesMeli', 'condicionProducto', 'meliItem'));
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


	public function admin_delete($id = null)
	{
		$this->MercadoLibr->id = $id;
		if ( ! $this->MercadoLibr->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
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
    		echo json_encode(array('0' => array('value' => '', 'label' => 'Ingrese referencia')));
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

   		// Buscamos los productos que cumplan con el criterio
		$productos	= $this->MercadoLibr->Productotienda->find('all', array(
			'fields' => array(
				'concat(\'https://' . $tienda['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-home_default.jpg\' ) AS url_image_thumb',
				'concat(\'https://' . $tienda['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-thickbox_default.jpg\' ) AS url_image_large',
				'Productotienda.id_product',
				'Productotienda.id_category_default',
				'pl.name', 
				'pl.description_short',
				'Productotienda.price', 
				'pl.link_rewrite', 
				'Productotienda.reference', 
				'Productotienda.show_price',
				'Productotienda.quantity'
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
		            'table' => sprintf('%scategory_product', $tienda['Tienda']['prefijo']),
		            'alias' => 'CategoriaProducto',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'CategoriaProducto.id_product' => 'Productotienda.id_product'
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
							'OR' => array(
								array('SpecificPrice.from' => '000-00-00 00:00:00'),
								array('SpecificPrice.to' => '000-00-00 00:00:00')
							),
							'AND' => array(
								'SpecificPrice.from <= "' . date('Y-m-d H:i:s') . '"',
								'SpecificPrice.to >= "' . date('Y-m-d H:i:s') . '"'
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
				'Productotienda.reference LIKE' => $palabra . '%'
			),
			'limit' => 3
		));

   		
   		if (empty($productos)) {
    		echo json_encode(array('0' => array('id' => '', 'value' => 'No se encontraron coincidencias')));
    		exit;
    	}
    	
    	foreach ($productos as $index => $producto) {

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


    		$arrayProductos[$index]['id'] = $producto['Productotienda']['id_product'];
			$arrayProductos[$index]['value'] = sprintf('%s', $producto['Lang'][0]['ProductotiendaIdioma']['name']);
			$arrayProductos[$index]['imagen'] = sprintf('%s', $producto[0]['url_image_large']);
			$arrayProductos[$index]['precio'] = sprintf('%s', $producto['Productotienda']['valor_final']);
			//$arrayProductos[$index]['name'] = $producto['Lang'][0]['ProductotiendaIdioma']['name'];
			//$arrayProductos[$index]['image'] = $producto[0]['url_image'];
			//$arrayProductos[$index]['description'] = $producto['Lang'][0]['ProductotiendaIdioma']['description_short'];
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
}
