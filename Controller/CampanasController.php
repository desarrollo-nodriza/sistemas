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
		$subCategorias[1000000000] = 'Mejor precio del mercado';
		
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

		
	/**
	 * google_feed
	 * *Retorna campaña a google en formato xml
	 * @param  mixed $id_tienda
	 * @param  mixed $id_campana
	 * @return xml
	 */
	public function google_feed(int $id_tienda, int $id_campana)
	{

		if (!ClassRegistry::init('Tienda')->exists($id_tienda) || !$this->Campana->exists($id_campana)) {
			return;
		}

		$campana = $this->Campana->find('first', array(
			'conditions' => array(
				'Campana.id' => $id_campana
			),
			'contain' => array(
				'CampanaEtiqueta'
			)
		));

		if (!$campana) {
			return;
		}

		$ruta = $campana['Campana']['xml_generado'];
		header('Content-Type: application/xml; charset=utf-8');
		// * Si ya se genero xml lo va a buscar y lo retorna en pantalla
		if (file_exists($ruta)) {
			$xml 	= simplexml_load_file($ruta);
			$data 	= html_entity_decode($xml->asXML());
			die($data);
		}else{
			// * De lo contrario tendra que crearlo y mostrarlo en pantalla
			$ruta 	= $this->google_generar_xml_feed($id_tienda, $id_campana, true);
			$xml 	= simplexml_load_file($ruta);
			$data 	= html_entity_decode($xml->asXML());
			die($data);
		}

		exit;
	}
	
	/**
	 * google_generar_xml_feed
	 * Crea los xml de las campañas para el uso en google. Estas se guardan en la campaña para ser usadas posteriormente
	 * @param  mixed $id_tienda
	 * @param  mixed $id_campana
	 * @param  mixed $retornar
	 * @return String
	 */
	public function google_generar_xml_feed(int $id_tienda, int $id_campana, bool $retornar = false)
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

		$this->Prestashop = $this->Components->load('Prestashop');
		$this->Prestashop->crearCliente($api_url, $api_key);


		$campana = $this->Campana->find('first', array(
			'conditions' => array(
				'Campana.id' => $id_campana
			),
			'contain' => array(
				'CampanaEtiqueta'
			)
		));

		if (!$campana) {
			return;
		}

		# Almacenará los productos que iran en el feed
		$productostodos 	= [];
		$producto_ids 		= [];
		$categoria_id 		= "";
		$arbol_categoria    = null;
		$producosProcesados = [];
		
		# si no tiene etiqueta, se usa la categoria principal
		if (empty($campana['CampanaEtiqueta'])) {

			$categoria_id 	= $campana['Campana']['categoria_id'];
			$categoria 		= $this->Prestashop->prestashop_obtener_categorias_v2(
				array(
					'filter[id]' 		=> "[{$categoria_id}]",
					'filter[active]'	=> "[1]",
				)
			);

			$producto_ids 	= Hash::extract($categoria['category']['associations']['products'] ?? [], 'product.{*}.id');
			$imagenes			= $this->Prestashop->prestashop_obtener_imagenes_de_productos($producto_ids);	
			$producto_ids 	= (implode("|", $producto_ids));

			$productostodos = $this->Prestashop->prestashop_obtener_productos_v2(
				array(
					'filter[id]' 					=> "[$producto_ids]",
					'filter[active]' 				=> "[1]",
					'filter[available_for_order]' 	=> "[1]",
					'filter[id_shop_default]' 		=> "[1]",
				)
			)['product'] ?? [];

			$stocks				= $this->Prestashop->prestashop_obtener_stock_productos($producto_ids);
			$categorias_ids 	= array_unique(Hash::extract($productostodos,'{*}.id_category_default'));
			$arbol_categoria	= $this->Prestashop->prestashop_arbol_categorias_muchas_categorias($categorias_ids);

			foreach ($productostodos as $producto) {
				$producto['quantity']		= $stocks[$producto['id']] ?? 0;
				$producto['product_type']	= $arbol_categoria[$producto['id_category_default']];
				$producto['image_link']		= $imagenes[$producto['id']] ?? "";
				$producosProcesados[]		= $producto;
			}

		} else {

			# Se agregan las etiquetas correspondientes
			foreach ($campana['CampanaEtiqueta'] as $ic => $c) {
				
				$categoria_id 	= $c['categoria_id'];
				$categoria 		= $this->Prestashop->prestashop_obtener_categorias_v2(
					array(
						'filter[id]' 		=> "[{$categoria_id}]",
						'filter[active]'	=> "[1]",
					)
				);
				$producto_ids 	= Hash::extract($categoria['category']['associations']['products'], 'product.{*}.id');
				$imagenes			= $this->Prestashop->prestashop_obtener_imagenes_de_productos($producto_ids);
				$producto_ids 	= (implode("|", $producto_ids));
				$productostodos = $this->Prestashop->prestashop_obtener_productos_v2(
					array(
						'filter[id]' 					=> "[$producto_ids]",
						'filter[active]' 				=> "[1]",
						'filter[available_for_order]' 	=> "[1]",
						'filter[id_shop_default]' 		=> "[1]",
					)
				)['product'] ?? [];

				$stocks				= $this->Prestashop->prestashop_obtener_stock_productos($producto_ids);
				$categorias_ids 	= array_unique(Hash::extract($productostodos,'{*}.id_category_default'));
				$arbol_categoria	= $this->Prestashop->prestashop_arbol_categorias_muchas_categorias($categorias_ids);

				# agregamos a los productos la etiqueta de la campaña

				foreach ($productostodos as $producto) {

					$producto['quantity']		= $stocks[$producto['id']] ?? 0;
					$producto['product_type']	= $arbol_categoria[$producto['id_category_default']];
					$producto['image_link']		= $imagenes[$producto['id']] ?? "";
					if ($c['categoria_id'] == 1000000000 && !empty($_producto['reference'])) {

						$prisync = $this->obtener_productos_mejor_precio($_producto['reference']);

						if (!empty($prisync)) {

							if (!isset($prisync['PrisyncProducto'])) {
								continue;
							}

							if ($prisync['PrisyncProducto']['mejor_precio']) {
								$productostodos[$_producto['id']]['custom_label_' . $ic] = 'Mejor precio mercado';
							}
						}
					} else {
						$producto['custom_label_' . $ic] = $c['nombre'];
					}

					$producosProcesados[]		= $producto;
				}
			}
		}

		# Campana de Google
		GoogleShopping::title('Feed Google Shopping');
		GoogleShopping::link(FULL_BASE_URL);
		GoogleShopping::description('Feed generado por Nodriza Spa [desarrollo@nodriza.cl]');
		GoogleShopping::setIso4217CountryCode('CLP');


		$google = array();

		$productoTienda = new ProductotiendasController();

		foreach ($producosProcesados as $ip => $producto) {
			# Se excluyen los productos sin stock
			if ($producto['quantity'] < 1 && $campana['Campana']['excluir_stockout'])
				continue;

			$producto['valor_iva'] 			= is_null($producto['id_tax_rules_group']) ? $producto['price'] : $productoTienda->precio($producto['price'], Configure::read('iva_clp'));
			$producto['valor_final'] 		= $producto['valor_iva'];
			$producto['valor_final'] 		= $producto['valor_final'] - $this->Prestashop->prestashop_obtener_descuento_producto($producto['id'], $producto['valor_final']);

			$google[$ip]['g:id']           	= $producto['id'];
			$google[$ip]['g:title']        	= $producto['name']['language'];
			$google[$ip]['g:description']  	= strip_tags(!is_array($producto['description_short']['language']) ? $producto['description_short']['language'] : "") . '';
			$google[$ip]['g:link']         	= sprintf('%s%s-%s.html', $api_url, $producto['link_rewrite']['language'], $producto['id']);
			$google[$ip]["g:image_link"]   	= $producto['image_link'];
			$google[$ip]['g:availability'] 	= ($producto['quantity'] > 0) ? 'in stock' : 'out of stock';
			$google[$ip]['g:price']        	= $producto['valor_iva'];
			$google[$ip]['g:sale_price']   	= round($producto['valor_final']);
			$google[$ip]['g:product_type'] 	= $producto['product_type'] ?? 'Sin categoría';
			$google[$ip]['g:brand']        	= (empty($producto['id_manufacturer'])) ? 'No especificado' : $producto['manufacturer_name'];
			$google[$ip]['g:mpn']          	= $producto['reference'];
			$google[$ip]['g:condition']    	= $producto['condition'];
			$google[$ip]['g:adult']        	= 'no';
			$google[$ip]['g:age_group']    	= 'adult';


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

		$salida = GoogleShopping::asRss();
		if (!is_dir("FeedGoogle")) mkdir("FeedGoogle");
		$ruta = "FeedGoogle/{$campana['Campana']['id']}.xml";

		file_put_contents($ruta, $salida);

		if (file_exists($ruta)) {
			ClassRegistry::init('Campana')->create();
			ClassRegistry::init('Campana')->save([
				'Campana' => [
					'id'			=> $campana['Campana']['id'],
					'xml_generado'	=> $ruta,
				]
			]);
		}

		// * según se requiere se retorna el valor de la variable o se muestra en pantalla
		if ($retornar) {
			return $ruta;
		} else {
			die($ruta);
		}
		
	}

	
	/**
	 * obtener_productos_mejor_precio
	 * 
	 * Obtener los productos con mejor precio de mercado según prisync
	 *
	 * @param  mixed $ref
	 * @param  mixed $micompania
	 * @return array 
	 */
	public function obtener_productos_mejor_precio($ref = '', $micompania = 'toolmania')
	{	

		$qry = array(
			'contain' => array(
				'PrisyncRuta'
			)
		);

		if (!empty($ref)) {
			$qry = array_replace_recursive($qry, array(
				'conditions' => array(
					'PrisyncProducto.internal_code' => $ref
				),
				'order' => array('PrisyncProducto.modified' => 'DESC')
			));
		}

		$productos = ClassRegistry::init('PrisyncProducto')->find('all', $qry);

		foreach ($productos as $ip => $p) {
			$mejor_precio = false;
			
			$competidores = Hash::sort(Hash::extract($p['PrisyncRuta'], '{n}[price>0]'), '{n}.price', 'asc', 'numeric');
			
			if (!empty($competidores) && count($competidores) > 1) {
				$url      = parse_url($competidores[0]['url']);
				$compania = explode('.', str_replace('www.', '', $url['host']));

				if ($compania[0] == $micompania) {
					$mejor_precio = true;
				}
			}

			$productos[$ip]['PrisyncProducto']['mejor_precio'] = $mejor_precio;
			
		}

		$nwProductos = array();
		foreach ($productos as $ip => $p) {
			if (!isset( $nwProductos[$p['PrisyncProducto']['id']] )) {
				$nwProductos[$p['PrisyncProducto']['id']]['PrisyncProducto'] = $p['PrisyncProducto'];
				$nwProductos[$p['PrisyncProducto']['id']]['PrisyncRuta'] = $p['PrisyncRuta'];
			}
		}

		if (!empty($ref)) {
			return reset($nwProductos); // retornamos solo el producto pedido
		}else{
			return $nwProductos;
		}
	}
}