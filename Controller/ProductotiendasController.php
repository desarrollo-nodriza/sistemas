<?
App::uses('AppController', 'Controller');
 
class ProductotiendasController extends AppController {
 
    public $name = 'Productotiendas';    
    public $uses = array('Productotienda');

    public function admin_index() 
    {
    	$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;
    	$categorias = array();

    	$textoBuscar = null;

		// Filtrado de productos por formulario
		if ( $this->request->is('post') ) {

			if ( ! empty($this->request->data['Filtro']['findby']) && empty($this->request->data['Filtro']['nombre_buscar']) ) {
				$this->Session->setFlash('Ingrese nombre o referencia del producto' , null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}

			if ( ! empty($this->request->data['Filtro']['findby']) && ! empty($this->request->data['Filtro']['nombre_buscar']) ) {
				$this->redirect(array('controller' => 'productotiendas', 'action' => 'index', 'findby' => $this->request->data['Filtro']['findby'], 'nombre_buscar' => $this->request->data['Filtro']['nombre_buscar']));
			}
		}
		//Buscamos el prefijo de la tienda
		$tienda = ClassRegistry::init('Tienda')->find('first', array(
		'conditions' => array(
			'Tienda.id' => $this->Session->read('Tienda.id')
			)
		));

		// Virificar existencia de la tienda
		if (empty($tienda)) {
			$this->Session->setFlash('La tienda seleccionada no existe' , null, array(), 'danger');
			$this->redirect('/');
		}

		// Verificar que la tienda esté configurada
		if (empty($tienda['Tienda']['prefijo']) || empty($tienda['Tienda']['prefijo']) || empty($tienda['Tienda']['configuracion'])) {
			$this->Session->setFlash('La tienda no está configurada completamente. Verifiquela y vuelva a intentarlo' , null, array(), 'danger');
			$this->redirect('/');
		}

		// Opciones de paginación
		$paginate = array_replace_recursive(array(
			'limit' => 10,
			'fields' => array(
				'concat(\'http://' . $tienda['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'.jpg\' ) AS url_image',
				'Productotienda.id_product', 
				'pl.name', 
				'Productotienda.price', 
				'pl.link_rewrite', 
				'Productotienda.reference', 
				'Productotienda.show_price'
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
				'Categoria'
			),
			'conditions' => array(
				'Productotienda.active' => 1,
				'Productotienda.available_for_order' => 1,
				'Productotienda.id_shop_default' => 1,
				'pl.id_lang' => 1
			)
		));

		/*******************************************
		 * 
		 * Aplicar a todos los modelos dinámicos
		 * 
		 ******************************************/
		$this->cambiarConfigDB($tienda['Tienda']['configuracion']);

		/**
		* Buscar por
		*/
		if ( !empty($this->request->params['named']['findby']) && !empty($this->request->params['named']['nombre_buscar']) ) {

			/**
			* Agregar condiciones a la paginación
			* según el criterio de busqueda (código de referencia o nombre del producto)
			*/
			switch ($this->request->params['named']['findby']) {
				case 'code':
					$paginate		= array_replace_recursive($paginate, array(
						'conditions'	=> array(
							'Productotienda.reference' => trim($this->request->params['named']['nombre_buscar'])
						)
					));
					break;
				
				case 'name':
					$paginate		= array_replace_recursive($paginate, array(
						'conditions'	=> array(
							'pl.name LIKE "%' . trim($this->request->params['named']['nombre_buscar']) . '%"'
						)
					));
					break;
			}
			// Texto ingresado en el campo buscar
			$textoBuscar = $this->request->params['named']['nombre_buscar'];
			
		}else if ( ! empty($this->request->params['named']['findby'])) {
			$this->Session->setFlash('No se aceptan campos vacios.' ,  null, array(), 'danger');
		}

		// Total de registros de la tienda
		$total 		= $this->Productotienda->find('count', array(
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
			'conditions' => array(
				'Productotienda.active' => 1,
				'Productotienda.available_for_order' => 1,
				'Productotienda.id_shop_default' => 1,
				'pl.id_lang' => 1
			)
		));

		$categorias = $this->Productotienda->Categoria->find('list', array('conditons' => array('Categoria.activo' => 1)));

		$this->paginate = $paginate;

		$productos	= $this->paginate();
		$totalMostrados = count($productos);

		if (empty($productos)) {
			$this->Session->setFlash(sprintf('No se encontraron resultados para %s', $this->request->params['named']['nombre_buscar']) , null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		BreadcrumbComponent::add('Productos Tiendas');

		$this->set(compact('productos', 'total', 'totalMostrados', 'textoBuscar', 'categorias', 'tienda'));

    }


    public function admin_associate($id = null, $tiendaId = null) {

    	if (is_null($tiendaId) || empty($tiendaId)) {
    		$this->Session->setFlash('No se ubicó la tienda del producto', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
    	}

    	//Buscamos el prefijo de la tienda
		$tienda = ClassRegistry::init('Tienda')->find('first', array(
		'conditions' => array(
			'Tienda.id' => $tiendaId
			)
		));

		// Virificar existencia de la tienda
		if (empty($tienda)) {
			$this->Session->setFlash('La tienda seleccionada no existe' , null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		// Verificar que la tienda esté configurada
		if (empty($tienda['Tienda']['prefijo']) || empty($tienda['Tienda']['prefijo']) || empty($tienda['Tienda']['configuracion'])) {
			$this->Session->setFlash('La tienda no está configurada completamente. Verifiquela y vuelva a intentarlo' , null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}


		/*******************************************
		 * 
		 * Aplicar a todos los modelos dinámicos
		 * 
		 ******************************************/
		$this->cambiarConfigDB($tienda['Tienda']['configuracion']);

    	if ( ! $this->Productotienda->exists($id) ) {
    		$this->Session->setFlash('No se encontraron el producto seleccionado', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
    	}


    	if ($this->request->is('post')) {

    		$this->Productotienda->CategoriasProductotienda->deleteAll(
    			array(
					'CategoriasProductotienda.id_product' => $this->request->data['Productotienda']['id_product']
				)
    		);

    		if ( $this->Productotienda->save($this->request->data) )
    		{

				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));

			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
    	}

    	// Opciones de paginación
		$producto = $this->Productotienda->find('first', array(
			'fields' => array(
				'concat(\'http://' . $tienda['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-large_default.jpg\' ) AS url_image',
				'Productotienda.id_product', 
				'pl.name', 
				'Productotienda.price', 
				'pl.link_rewrite', 
				'Productotienda.reference', 
				'Productotienda.show_price'
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
				'Categoria' => array(
					'conditions' => array(
						'Categoria.activo' => 1,
						'Categoria.tienda_id' => $tiendaId
					)
				),
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
				'Productotienda.id_product' => $id,
				'Productotienda.active' => 1,
				'Productotienda.available_for_order' => 1,
				'Productotienda.id_shop_default' => 1,
				'pl.id_lang' => 1
			)
		));
	
		// Retornar valor con iva;
		$producto['Productotienda']['valor_iva'] = $this->precio($producto['Productotienda']['price'], $producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']);
		

		// Criterio del precio específico
		foreach ($producto['SpecificPricePriority'] as $criterio) {
			$precioEspecificoPrioridad = explode(';', $criterio['priority']);
		}

		$producto['Productotienda']['valor_final'] = $producto['Productotienda']['valor_iva'];

		// Retornar precio espeficico según criterio
		foreach ($producto['SpecificPrice'] as $precio) {

			if ( $precio['reduction'] == 0 ) {
				$producto['Productotienda']['valor_final'] = $producto['Productotienda']['valor_iva'];
			}else{
				$producto['Productotienda']['valor_final'] = $this->precio($producto['Productotienda']['valor_iva'], ($precio['reduction'] * 100 * -1) );
				$producto['Productotienda']['descuento'] = ($precio['reduction'] * 100 * -1 );
			}
		}

		$categorias = $this->Productotienda->Categoria->find('list', array('conditions' => array('Categoria.activo' => 1, 'Categoria.tienda_id' => $tiendaId)));

		BreadcrumbComponent::add('Productos Tiendas', '/productotiendas');
		BreadcrumbComponent::add('Asociar ');

		$this->set(compact('producto', 'categorias', 'tienda'));

    }


    public function admin_obtener_productos( $tienda = '', $palabra = '') {
    	if (empty($tienda) || empty($palabra)) {
    		echo json_encode(array('0' => array('value' => '', 'label' => 'Ingrese referencia')));
    		exit;
    	}
   		
   		/*******************************************
		 * 
		 * Aplicar a todos los modelos dinámicos
		 * 
		 ******************************************/
   		$this->cambiarConfigDB($this->tiendaConf($tienda));

   		$productos = $this->Productotienda->find('all', array(
   			'conditions' => array(
   				'Productotienda.reference LIKE' => $palabra . '%'
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
				)
			),
			'limit' => 3)
   		);

   		if (empty($productos)) {
    		echo json_encode(array('0' => array('id' => '', 'value' => 'No se encontraron coincidencias')));
    		exit;
    	}

    	$tabla = '<tr>';
    	$tabla .= '<td><input type="hidden" name="data[Productotienda][[*ID*]][id_product]" value="[*ID*]" class="js-input-id_product">[*ID*]</td>';
    	$tabla .= '<td>[*REFERENCIA*]</td>';
    	$tabla .= '<td>[*NOMBRE*]</td>';
    	$tabla .= '<td><label class="label label-form label-success">[*PRECIO*]</label></td>';
    	$tabla .= '<td><input type="number" name="data[Productotienda][[*ID*]][cantidad]" class="form-control js-number" min="0" max="100" placeholder="X" style="max-width: 70px;" value="1" /></td>';
    	$tabla .= '<td><input type="text" name="data[Productotienda][[*ID*]][nombre_descuento]" class="form-control" placeholder="Desc Nombre" style="max-width: 200px;"/></td>';
    	$tabla .= '<td><input type="number" name="data[Productotienda][[*ID*]][descuento]" class="form-control js-number" min="0" max="100" placeholder="%" style="max-width: 70px;"/></td>';
    	$tabla .= '<td><button class="quitar btn btn-danger">Quitar</button></td>';
    	$tabla .= '</tr>';
    	
    	foreach ($productos as $index => $producto) {
    		$arrayProductos[$index]['id'] = $producto['Productotienda']['id_product'];
			$arrayProductos[$index]['value'] = sprintf('%s - %s', $producto['Productotienda']['reference'], $producto['Lang'][0]['ProductotiendaIdioma']['name']);

			// Armamos la tabla
			$tabla = str_replace('[*ID*]', $producto['Productotienda']['id_product'] , $tabla);
			$tabla = str_replace('[*REFERENCIA*]', $producto['Productotienda']['reference'] , $tabla);
			$tabla = str_replace('[*NOMBRE*]', $producto['Lang'][0]['ProductotiendaIdioma']['name'] , $tabla);

			$precio_normal 		= $this->precio($producto['Productotienda']['price'], $producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']);
			
			if ( ! empty($producto['SpecificPrice']) ) {
				if ($producto['SpecificPrice'][0]['reduction'] == 0) {
					$tabla = str_replace('[*PRECIO*]', CakeNumber::currency($precio_normal , 'CLP'), $tabla);
				}else {
					$precio_descuento	= $this->precio($precio_normal, ($producto['SpecificPrice'][0]['reduction'] * 100 * -1) );
					$tabla = str_replace('[*PRECIO*]', CakeNumber::currency($precio_descuento , 'CLP') , $tabla);
				}
			}else{
				$tabla = str_replace('[*PRECIO*]', CakeNumber::currency($precio_normal , 'CLP') , $tabla);
			}
			
			$arrayProductos[$index]['todo'] = $tabla;
    	}
    	
    	echo json_encode($arrayProductos, JSON_FORCE_OBJECT);
    	exit;
    }
    
}