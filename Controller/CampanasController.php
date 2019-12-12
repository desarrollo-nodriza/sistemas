<?php
App::uses('AppController', 'Controller');
App::uses('ProductotiendasController', 'Controller');

App::import('Vendor', 'GoogleShopping', array('file' => 'google-shopping-feed/vendor/autoload.php'));
App::import('Vendor', 'GoogleShopping', array('file' => 'google-shopping-feed/src/LukeSnowden/GoogleShoppingFeed/Containers/GoogleShopping.php'));

use LukeSnowden\GoogleShoppingFeed\Containers\GoogleShopping;

Class CampanasController extends AppController {

	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0,
			'order' => array('id' => 'desc')
		);
		$campanas	= $this->paginate();
		BreadcrumbComponent::add('Campañas ');
		$this->set(compact('campanas'));
	}


	public function obtener_lista_categorias_tienda($id_tienda = '', $id_padre = '')
	{

		if (!ClassRegistry::init('Tienda')->exists($id_tienda)) {
			throw new CakeException('Tienda no existe');
			return;
		}

		# componente on the fly!
		$this->Prestashop = $this->Components->load('Prestashop');

		$api_url = '';
		$api_key = '';

		if ($this->Session->check('Tienda.apiurl_prestashop') && $this->Session->check('Tienda.apikey_prestashop')) {
			$api_url = $this->Session->read('Tienda.apiurl_prestashop');
			$api_key = $this->Session->read('Tienda.apikey_prestashop');
		}else{
			$tienda = ClassRegistry::init('Tienda')->find('first', array(
				'conditions' => array(
					'Tienda.id' => $id_tienda
				),
				'fields' => array(
					'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop'
				)
			));

			if (empty($tienda['Tienda']['apiurl_prestashop']) || empty($tienda['Tienda']['apikey_prestashop'])) {
				throw new CakeException('Tienda no configurada');
				return;
			}

			$api_url = $tienda['Tienda']['apiurl_prestashop'];
			$api_key = $tienda['Tienda']['apikey_prestashop'];
		}

		# Cliente Prestashop
		$this->Prestashop->crearCliente( $api_url, $api_key);

		$categoriasTienda = $this->Prestashop->prestashop_obtener_categorias($id_padre);

		$categorias = array();

		if (empty($categoriasTienda)) {
			return $categorias;
		}

		foreach ($categoriasTienda['category'] as $ic => $c) {
			$categorias[$c['id']] = $c['id'] . ' - ' . $c['name']['language']; 
		}

		return $categorias;
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Campana->create();
			if ( $this->Campana->save($this->request->data) )
			{	
				$this->Session->setFlash('Registro agregado correctamente. Ahora puede continuar.', null, array(), 'success');
				$this->redirect(array('action' => 'edit', $this->Campana->id));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$categorias = $this->obtener_lista_categorias_tienda($this->Session->read('Tienda.id'));

		BreadcrumbComponent::add('Campañas ', '/campanas');
		BreadcrumbComponent::add('Agregar ');

		$this->set(compact('categorias'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Campana->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			$this->Campana->CampanaEtiqueta->deleteAll(array('CampanaEtiqueta.campana_id' => $id), false);
			
			if ( $this->Campana->saveAll($this->request->data) )
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
			$this->request->data	= $this->Campana->find('first', array(
				'conditions'	=> array('Campana.id' => $id),
				'contain' => array('CampanaEtiqueta')
			));
		}
		
		
		$categorias = $this->obtener_lista_categorias_tienda($this->Session->read('Tienda.id'));
		$subCategorias = $this->obtener_lista_categorias_tienda($this->Session->read('Tienda.id'), $this->request->data['Campana']['categoria_id']);
		
		BreadcrumbComponent::add('Campañas ', '/campanas');
		BreadcrumbComponent::add('Editar ');

		$this->set(compact('categorias', 'subCategorias'));
	}

	public function admin_delete($id = null)
	{
		$this->Campana->id = $id;
		if ( ! $this->Campana->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Campana->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Campana->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Campana->_schema);
		$modelo			= $this->Campana->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	public function google_feed($id_tienda, $id_campana)
	{

		if (!ClassRegistry::init('Tienda')->exists($id_tienda) || !$this->Campana->exists($id_campana)) {
			return;
		}

		$tienda = ClassRegistry::init('Tienda')->find('first', array(
			'conditions' => array(
				'Tienda.id' => $id_tienda
			),
			'fields' => array(
				'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.prefijo', 'Tienda.configuracion', 'Tienda.url'
			)
		));

		if (empty($tienda['Tienda']['apiurl_prestashop']) || empty($tienda['Tienda']['apikey_prestashop'])) {
			return;
		}

		$api_url = $tienda['Tienda']['apiurl_prestashop'];
		$api_key = $tienda['Tienda']['apikey_prestashop'];

		$campana = $this->Campana->find('first', array(
			'conditions' => array(
				'Campana.id' => $id_campana
			),
			'contain' => array(
				'CampanaEtiqueta'
			)
		));

		# Almacenará los productos que iran en el feed
		$productostodos = array();

		# usar DB
		$this->cambiarConfigDB($tienda['Tienda']['configuracion']);

		# si no tiene etiqueta, se usa la categoria principal
		if (empty($campana['CampanaEtiqueta'])) {

			$id_productos = ClassRegistry::init('Productotienda')->query(sprintf('SELECT * FROM %scategory_product WHERE id_category = %d', $tienda['Tienda']['prefijo'], $campana['Campana']['categoria_id'])); 
			$id_productos = Hash::extract($id_productos, '{n}.tm_category_product.id_product');
			
			// Buscamos los productos que cumplan con el criterio
			$productos	= ClassRegistry::init('Productotienda')->find('all', array(
				'fields' => array(
					'concat(\'https://' . $tienda['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-home_default.jpg\' ) AS url_image_thumb',
					'concat(\'https://' . $tienda['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'.jpg\' ) AS url_image_large',
					'Productotienda.id_product',
					'Productotienda.id_category_default',
					'pl.name', 
					'pl.description_short',
					'Productotienda.price', 
					'pl.link_rewrite', 
					'Productotienda.reference', 
					'Productotienda.show_price',
					'Productotienda.quantity',
					'Productotienda.id_manufacturer',
					'Productotienda.condition',
					'Productotienda.supplier_reference',
					'Marca.id_manufacturer',
					'Marca.name',
					'Stockdisponible.quantity'
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
			            'table' => sprintf('%smanufacturer', $tienda['Tienda']['prefijo']),
			            'alias' => 'Marca',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'Productotienda.id_manufacturer = Marca.id_manufacturer'
			            )
		        	),
		        	array(
			            'table' => sprintf('%sstock_available', $tienda['Tienda']['prefijo']),
			            'alias' => 'Stockdisponible',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'Productotienda.id_product = Stockdisponible.id_product'
			            )
		        	)
				),
				'contain' => array(
					'TaxRulesGroup' => array(
						'TaxRule' => array(
							'Tax'
						)
					),
					'SpecificPrice' => array(
						'conditions' => array(
							'AND' => array(
								'SpecificPrice.from_quantity > 0' 
							),
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
					'Productotienda.id_product' => $id_productos,
					'Productotienda.active' => 1,
					'Productotienda.available_for_order' => 1,
					'Productotienda.id_shop_default' => 1
				)
			));

			# agregamos a los productos la etiqueta de la campaña
			foreach ($productos as $ip => $p) {
				
				if (!isset($productostodos[$p['Productotienda']['id_product']])) {
					$productostodos[$p['Productotienda']['id_product']] = $p;
				}
				
			}
		}else{

			# Se agregan las etiquetas correspondientes
			foreach ($campana['CampanaEtiqueta'] as $ic => $c) {

				$id_productos = ClassRegistry::init('Productotienda')->query(sprintf('SELECT * FROM %scategory_product WHERE id_category = %d', $tienda['Tienda']['prefijo'], $c['categoria_id'])); 
				$id_productos = Hash::extract($id_productos, '{n}.tm_category_product.id_product');
				
				// Buscamos los productos que cumplan con el criterio
				$productos	= ClassRegistry::init('Productotienda')->find('all', array(
					'fields' => array(
						'concat(\'https://' . $tienda['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-home_default.jpg\' ) AS url_image_thumb',
						'concat(\'https://' . $tienda['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'.jpg\' ) AS url_image_large',
						'Productotienda.id_product',
						'Productotienda.id_category_default',
						'pl.name', 
						'pl.description_short',
						'Productotienda.price', 
						'pl.link_rewrite', 
						'Productotienda.reference', 
						'Productotienda.show_price',
						'Productotienda.quantity',
						'Productotienda.id_manufacturer',
						'Productotienda.condition',
						'Productotienda.supplier_reference',
						'Marca.id_manufacturer',
						'Marca.name',
						'Stockdisponible.quantity'
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
				            'table' => sprintf('%smanufacturer', $tienda['Tienda']['prefijo']),
				            'alias' => 'Marca',
				            'type'  => 'LEFT',
				            'conditions' => array(
				                'Productotienda.id_manufacturer = Marca.id_manufacturer'
				            )
			        	),
			        	array(
				            'table' => sprintf('%sstock_available', $tienda['Tienda']['prefijo']),
				            'alias' => 'Stockdisponible',
				            'type'  => 'LEFT',
				            'conditions' => array(
				                'Productotienda.id_product = Stockdisponible.id_product'
				            )
			        	)
					),
					'contain' => array(
						'TaxRulesGroup' => array(
							'TaxRule' => array(
								'Tax'
							)
						),
						'SpecificPrice' => array(
							'conditions' => array(
								'AND' => array(
									'SpecificPrice.from_quantity > 0' 
								),
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
						'Productotienda.id_product' => $id_productos,
						'Productotienda.active' => 1,
						'Productotienda.available_for_order' => 1,
						'Productotienda.id_shop_default' => 1
					)
				));

				# agregamos a los productos la etiqueta de la campaña
				foreach ($productos as $ip => $p) {
					
					if (!isset($productostodos[$p['Productotienda']['id_product']])) {
						$productostodos[$p['Productotienda']['id_product']] = $p;
					}
					$productostodos[$p['Productotienda']['id_product']]['custom_label_' . $ic] = $c['nombre'];
					
				}
			}

		}

		# Campana de Google
		GoogleShopping::title('Feed Google Shopping');
		GoogleShopping::link(FULL_BASE_URL);
		GoogleShopping::description('Feed generado por Nodriza Spa [cristian.rojas@nodriza.cl]');
		GoogleShopping::setIso4217CountryCode('CLP');

		
		$google = array();

		$productoTienda = new ProductotiendasController();

		$sitioUrl = $this->formatear_url($tienda['Tienda']['url'], true);

		foreach ($productostodos as $ip => $producto) {

			# Se excluyen los productos sin stock
			if ($producto['Stockdisponible']['quantity'] < 1 && $campana['Campana']['excluir_stockout'])
				continue;

			$cate = $productoTienda->getParentCategory($producto['Productotienda']['id_category_default'], $tienda['Tienda']['prefijo']);
			
			if (!empty($cate)) {
				$cate = $productoTienda->categoriesTree($cate);
			}


			if ( !isset($producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']) ) {
				$producto['Productotienda']['valor_iva'] = $producto['Productotienda']['price'];	
			}else{
				$producto['Productotienda']['valor_iva'] = $productoTienda->precio($producto['Productotienda']['price'], $producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']);
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

					$producto['Productotienda']['valor_final'] = $productoTienda->precio($producto['Productotienda']['valor_iva'], ($precio['reduction'] * 100 * -1) );
					$producto['Productotienda']['descuento'] = ($precio['reduction'] * 100 * -1 );

				}
			}

			$google[$ip]['g:id']           = $producto['Productotienda']['id_product'];
			$google[$ip]['g:title']        = $producto['pl']['name'];
			$google[$ip]['g:description']  = strip_tags($producto['pl']['description_short']) . '';
			$google[$ip]['g:link']         = sprintf('%s%s-%s.html', $sitioUrl, $producto['pl']['link_rewrite'], $producto['Productotienda']['id_product']);
			$google[$ip]["g:image_link"]   = $producto[0]['url_image_large'];
			$google[$ip]['g:availability'] = ($producto['Stockdisponible']['quantity'] > 0) ? 'in stock' : 'out of stock';
			$google[$ip]['g:price']        = $producto['Productotienda']['valor_iva'];
			$google[$ip]['g:sale_price']   = $producto['Productotienda']['valor_final'];
			$google[$ip]['g:product_type'] = (!empty($cate)) ? $productoTienda->tree($cate) : 'Sin categoría';
			$google[$ip]['g:brand']        = (empty($producto['Productotienda']['id_manufacturer'])) ? 'No especificado' : $producto['Marca']['name'] ;
			$google[$ip]['g:mpn']          = $producto['Productotienda']['reference'];
			$google[$ip]['g:condition']    = $producto['Productotienda']['condition'];
			$google[$ip]['g:adult']        = 'no';
			$google[$ip]['g:age_group']    = 'adult';


			# Se agrega la info del producto al Campana
			$item = GoogleShopping::createItem();
			$item->id($google[$ip]['g:id']);
			$item->title($google[$ip]['g:title']);
			$item->description($google[$ip]['g:description']);
			$item->price($google[$ip]['g:price']);
			$item->sale_price($google[$ip]['g:sale_price']);
			$item->link($google[$ip]['g:link']);
			$item->image_link($google[$ip]['g:image_link']);
			$item->availability($google[$ip]['g:availability']);
			$item->product_type($google[$ip]['g:product_type']);
			$item->brand($google[$ip]['g:brand']);
			$item->mpn($google[$ip]['g:mpn']);
			$item->condition($google[$ip]['g:condition']);
			$item->adult($google[$ip]['g:adult']);
			$item->age_group($google[$ip]['g:age_group']);

			# Agregamos cutom label a feed
			if (isset($producto['custom_label_0'])) {
				$item->custom_label_0($producto['custom_label_0']);
			}

			if (isset($producto['custom_label_1'])) {
				$item->custom_label_1($producto['custom_label_1']);
			}

			if (isset($producto['custom_label_2'])) {
				$item->custom_label_2($producto['custom_label_2']);
			}

			if (isset($producto['custom_label_3'])) {
				$item->custom_label_3($producto['custom_label_3']);
			}

			if (isset($producto['custom_label_4'])) {
				$item->custom_label_4($producto['custom_label_4']);
			}
			
			
		}

		$out = $google;
		

		GoogleShopping::asRss(true);
		#$salida = GoogleShopping::asRss();
		
		
		#file_put_contents('google_campana2.xml', $salida);
		
		exit;
	}

}