<?
App::uses('AppController', 'Controller');
 
class ToolmaniasController extends AppController {
 
    public $name = 'Toolmanias';    
    public $uses = array('Toolmania');

    public function admin_index() 
    {

    	$conditions = array();

    	$textoBuscar = null;

    	// Opciones de paginación
		$paginate = array(
			'limit' => 10,
			'fields' => array(
				'concat(\'http://www.toolmania.cl/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'.jpg\' ) AS url_image',
				'Toolmania.id_product', 
				'pl.name', 
				'Toolmania.price', 
				'pl.link_rewrite', 
				'Toolmania.reference', 
				'Toolmania.show_price'
			),
			'joins' => array(
				array(
		            'table' => 'tm_product_lang',
		            'alias' => 'pl',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Toolmania.id_product=pl.id_product'
		            )

	        	),
	        	array(
		            'table' => 'tm_image',
		            'alias' => 'im',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Toolmania.id_product = im.id_product',
		                'im.cover' => 1
		            )
	        	)
			),
			'contain' => array(
				'Categoria'
			),
			'conditions' => array(
				'Toolmania.active' => 1,
				'Toolmania.available_for_order' => 1,
				'Toolmania.id_shop_default' => 1,
				'pl.id_lang' => 1
			)
		);

		// Filtrado de productos por formulario
		if ( $this->request->is('post') ) {
			
			/**
			* Buscar por
			*/
			if ( !empty($this->request->data['Toolmania']['findby']) && !empty($this->request->data['Toolmania']['nombre_buscar']) ) {

				/**
				* Agregar condiciones a la paginación
				* según el criterio de busqueda (código de referencia o nombre del producto)
				*/
				switch ($this->request->data['Toolmania']['findby']) {
					case 'code':
						$paginate		= array_replace_recursive($paginate, array(
							'conditions'	=> array(
								'Toolmania.reference' => trim($this->request->data['Toolmania']['nombre_buscar'])
							)
						));
						break;
					
					case 'name':
						$paginate		= array_replace_recursive($paginate, array(
							'conditions'	=> array(
								'pl.name LIKE "%' . trim($this->request->data['Toolmania']['nombre_buscar']) . '%"'
							)
						));
						break;
				}
				// Texto ingresado en el campo buscar
				$textoBuscar = $this->request->data['Toolmania']['nombre_buscar'];
				
			}else{
				$this->Session->setFlash('No se aceptan campos vacios.' ,  null, array(), 'danger');
			}
		}
		
		$this->paginate = $paginate;

		$total 		= $this->Toolmania->find('count', array(
			'joins' => array(
				array(
		            'table' => 'tm_product_lang',
		            'alias' => 'pl',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Toolmania.id_product=pl.id_product'
		            )

	        	),
	        	array(
		            'table' => 'tm_image',
		            'alias' => 'im',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Toolmania.id_product = im.id_product',
		                'im.cover' => 1
		            )
	        	)
			),
			'conditions' => array(
				'Toolmania.active' => 1,
				'Toolmania.available_for_order' => 1,
				'Toolmania.id_shop_default' => 1,
				'pl.id_lang' => 1
			)
		));

		$productos	= $this->paginate();
		
		$categorias = $this->Toolmania->Categoria->find('list', array('conditons' => array('Categoria.activo' => 1)));
		$totalMostrados = count($productos);

		if (empty($productos)) {
			$this->Session->setFlash(sprintf('No se encontraron resultados para %s', $this->request->data['Toolmania']['nombre_buscar']) , null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		BreadcrumbComponent::add('Productos Toolmania ');

		$this->set(compact('productos', 'total', 'totalMostrados', 'textoBuscar', 'categorias'));
    }


    public function admin_associate($id = null) {

    	if ( ! $this->Toolmania->exists($id) ) {
    		$this->Session->setFlash('No se encontraron el producto seleccionado', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
    	}

    	if ($this->request->is('post')) {

    		$this->Toolmania->CategoriasToolmania->deleteAll(
    			array(
					'CategoriasToolmania.id_product' => $this->request->data['Toolmania']['id_product']
				)
    		);

    		if ( $this->Toolmania->save($this->request->data) )
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
		$producto = $this->Toolmania->find('first', array(
			'fields' => array(
				'concat(\'http://www.toolmania.cl/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-large_default.jpg\' ) AS url_image',
				'Toolmania.id_product', 
				'pl.name', 
				'Toolmania.price', 
				'pl.link_rewrite', 
				'Toolmania.reference', 
				'Toolmania.show_price'
			),
			'joins' => array(
				array(
		            'table' => 'tm_product_lang',
		            'alias' => 'pl',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Toolmania.id_product=pl.id_product'
		            )

	        	),
	        	array(
		            'table' => 'tm_image',
		            'alias' => 'im',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Toolmania.id_product = im.id_product',
		                'im.cover' => 1
		            )
	        	)
			),
			'contain' => array(
				'Categoria' => array(
					'conditions' => array(
						'Categoria.activo' => 1
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
				'Toolmania.id_product' => $id,
				'Toolmania.active' => 1,
				'Toolmania.available_for_order' => 1,
				'Toolmania.id_shop_default' => 1,
				'pl.id_lang' => 1
			)
		));

		/*$all = $this->Toolmania->find('all', array(
			'fields' => array(
				'Toolmania.id_product',
				'pl.name'
			),
			'joins' => array(
				array(
		            'table' => 'tm_product_lang',
		            'alias' => 'pl',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Toolmania.id_product=pl.id_product',
		                'pl.id_product' => $id
		            )
	        	),
	        	array(
		            'table' => 'tm_image',
		            'alias' => 'im',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Toolmania.id_product = im.id_product',
		                'im.id_product' => $id
		            )
	        	),
	        	array(
		            'table' => 'tm_product_shop',
		            'alias' => 'ps',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Toolmania.id_product = ps.id_product',
		                'ps.id_product' => $id
		            )
	        	)
			),
			'contain' => array(
				'TaxRulesGroup' => array(
					'TaxRule' => array(
						'Tax'
					)
				)
			),
			'conditions' => array(
				'Toolmania.active' => 1
			),
			'limit' => 10
		));*/

		// Retornar valor con iva;
		$producto['Toolmania']['valor_iva'] = $this->precio($producto['Toolmania']['price'], $producto['TaxRulesGroup']['TaxRule'][0]['Tax']['rate']);
		

		// Criterio del precio específico
		foreach ($producto['SpecificPricePriority'] as $criterio) {
			$precioEspecificoPrioridad = explode(';', $criterio['priority']);
		}

		$producto['Toolmania']['valor_final'] = $producto['Toolmania']['valor_iva'];

		// Retornar precio espeficico según criterio
		foreach ($producto['SpecificPrice'] as $precio) {

			if ( $precio['reduction'] == 0 ) {
				$producto['Toolmania']['valor_final'] = $producto['Toolmania']['valor_iva'];
			}else{
				$producto['Toolmania']['valor_final'] = $this->precio($producto['Toolmania']['valor_iva'], ($precio['reduction'] * 100 * -1) );
				$producto['Toolmania']['descuento'] = ($precio['reduction'] * 100 * -1 );
			}
		}

		$categorias = $this->Toolmania->Categoria->find('list', array('conditions' => array('Categoria.activo' => 1)));

		BreadcrumbComponent::add('Productos Toolmania', '/toolmania');
		BreadcrumbComponent::add('Asociar ');

		$this->set(compact('producto', 'categorias'));

    }
    
}