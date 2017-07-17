<?php
App::uses('AppController', 'Controller');
class MercadoLibresController extends AppController
{	

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

			return $this->request->data['MercadoLibr']['html'];
			exit;
		}
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

		BreadcrumbComponent::add('Mercado Libre Productos ');

		$mercadoLibres	= $this->paginate();
		$this->set(compact('mercadoLibres'));
	}

	public function admin_add()
	{	
		if ( $this->request->is('post') )
		{	

			$this->request->data['MercadoLibr']['html'] = $this->createHtml();

			$this->MercadoLibr->create();
			if ( $this->MercadoLibr->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Mercado Libre Productos', '/mercadoLibres');
		BreadcrumbComponent::add('Agregar ');

		$plantillas	= $this->MercadoLibr->MercadoLibrePlantilla->find('list', array('conditions' => array('activo' => 1)));
		$this->set(compact('plantillas'));
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

			if ( $this->MercadoLibr->save($this->request->data) )
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
		
		$this->set(compact('plantillas', 'producto'));
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

   		$productos = $this->MercadoLibr->Productotienda->find('all', array(
   			'fields' => array(
				//'concat(\'http://' . $tienda['Tienda']['url'] . '/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'-large_default.jpg\' ) AS url_image',
				'Productotienda.id_product',
				'Productotienda.reference',
				'pl.name', 
			),
   			'conditions' => array(
   				'Productotienda.reference LIKE' => $palabra . '%'
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
   				/*array(
		            'table' => sprintf('%simage', $tienda['Tienda']['prefijo']),
		            'alias' => 'im',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Productotienda.id_product = im.id_product',
		                'im.cover' => 1
		            )
	        	)*/
   			),
   			'contain' => array(
   				'Lang',
   				/*'Especificacion' => array(
   					'Lang'
   					)*/
			),
			'limit' => 3)
   		);
   		
   		if (empty($productos)) {
    		echo json_encode(array('0' => array('id' => '', 'value' => 'No se encontraron coincidencias')));
    		exit;
    	}
    	
    	foreach ($productos as $index => $producto) {
    		$arrayProductos[$index]['id'] = $producto['Productotienda']['id_product'];
			$arrayProductos[$index]['value'] = sprintf('%s - %s', $producto['Productotienda']['reference'], $producto['Lang'][0]['ProductotiendaIdioma']['name']);
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
