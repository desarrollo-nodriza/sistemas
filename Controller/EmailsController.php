<?php

App::uses('AppController', 'Controller');

class EmailsController extends AppController

{

	public function admin_index()

	{

		$conditions = array(
			'tienda_id' => $this->Session->read('Tienda.id')
		);

		// Condicones para super administrador
		if ( $this->Auth->user('Aministrador.rol_id') == 1 ) {
			$conditions[] = array('Categoria.activo' => 1);
		}

		$this->paginate		= array(
			'recursive'			=> 0,
			'limit'	=> 1000,
			'conditions' => $conditions,
			'contain' => array('Tienda', 'Plantilla'),
			'order' => array('Email.created' => 'DESC')
		);

		BreadcrumbComponent::add('Newsletters ');

		$emails	= $this->paginate();

		$this->set(compact('emails'));

	}



	public function admin_add()

	{

		if ( $this->request->is('post') )

		{	
			// Se verifica y formatea la url
			$this->request->data['Email']['sitio_url'] = $this->formatear_url($this->request->data['Email']['sitio_url']);

			// Forzamos tienda id
			$this->request->data['Email']['tienda_id'] = $this->Session->read('Tienda.id');

			$this->Email->create();

			if ( $this->Email->save($this->request->data) )

			{ 	

				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');

				$this->redirect(array('action' => 'index'));

			}

			else

			{

				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');

			}

		}

		BreadcrumbComponent::add('Newsletters ', '/emails');
		BreadcrumbComponent::add('Agregar ');

		$plantillas	= $this->Email->Plantilla->find('list', array('conditions' => array('activo' => 1)));
		$categorias	= $this->Email->Categoria->find('list', array('conditions' => array('activo' => 1, 'tienda_id' => $this->Session->read('Tienda.id'))));

		$this->set(compact('plantillas', 'categorias'));

	}



	public function admin_edit($id = null)

	{

		if ( ! $this->Email->exists($id) )

		{

			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');

			$this->redirect(array('action' => 'index'));

		}


		if ( $this->request->is('post') || $this->request->is('put') )

		{	

			// Se verifica y formatea la url
			$this->request->data['Email']['sitio_url'] = $this->formatear_url($this->request->data['Email']['sitio_url']);

			// Forzamos tienda id
			$this->request->data['Email']['tienda_id'] = $this->Session->read('Tienda.id');

			/**

			* Se eliminan las relaciones categorias-emails para volver a agregarlas 

			*/

			$this->Email->CategoriasEmail->deleteAll(

				array(

					'CategoriasEmail.email_id' => $id,

				)

           	);



			if ( $this->Email->save($this->request->data) )

			{

				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');

				$this->redirect(array('action' => 'generarHtml', $id, true));

			}

			else

			{

				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');

			}

		}

	

			$this->request->data	= $this->Email->find('first', array(
				'contain'		=> array('Categoria', 'Tienda', 'Plantilla'),
				'conditions'	=> array('Email.id' => $id)
			));

	
		BreadcrumbComponent::add('Newsletters ', '/emails');
		BreadcrumbComponent::add('Editar ');

		$plantillas	= $this->Email->Plantilla->find('list', array('conditions' => array('activo' => 1)));
		$categorias	= $this->Email->Categoria->find('list', array('conditions' => array('activo' => 1, 'tienda_id' => $this->Session->read('Tienda.id'))));
		$tiendas	= $this->Email->Tienda->find('list', array('conditions' => array('activo' => 1)));

		$this->set(compact('plantillas', 'categorias', 'tiendas'));

	}



	public function admin_delete($id = null)

	{

		$this->Email->id = $id;

		if ( ! $this->Email->exists() )
		{

			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');

			$this->redirect(array('action' => 'index'));

		}
		$this->request->onlyAllow('post', 'delete');

		if ( $this->Email->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}

		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');

		$this->redirect(array('action' => 'index'));

	}



	public function admin_exportar()

	{

		$datos			= $this->Email->find('all', array(

			'recursive'				=> -1

		));

		$campos			= array_keys($this->Email->_schema);

		$modelo			= $this->Email->alias;



		$this->set(compact('datos', 'campos', 'modelo'));

	}



	/**

	* Función que desactiva un email

	* @param $id 	Integer 	Identificador del email

	*/

	public function admin_desactivar($id = null)

	{

		$this->Email->id = $id;

		if ( ! $this->Email->exists() )

		{

			$this->Session->setFlash('Newslletter no existe.', null, array(), 'danger');

			$this->redirect(array('action' => 'index'));

		}


		if ( $this->Email->saveField('activo', 0, array('callbacks' => false)) )

		{

			$this->Session->setFlash('Newslletter desactivado correctamente.', null, array(), 'success');

			$this->redirect(array('action' => 'index'));

		}

		$this->Session->setFlash('Error al desactivar el Newslletter. Por favor intenta nuevamente.', null, array(), 'danger');

		$this->redirect(array('action' => 'index'));

	}





	/**

	* Función que activa un email

	* @param $id 	Integer 	Identificador del email

	*/

	public function admin_activar($id = null)

	{

		$this->Email->id = $id;

		if ( ! $this->Email->exists() )

		{

			$this->Session->setFlash('Newslletter no existe.', null, array(), 'danger');

			$this->redirect(array('action' => 'index'));

		}

		if ( $this->Email->saveField('activo', 1, array('callbacks' => false)) )

		{

			$this->Session->setFlash('Newslletter activado correctamente.', null, array(), 'success');

			$this->redirect(array('action' => 'index'));

		}

		$this->Session->setFlash('Error al eliminar el Newslletter. Por favor intenta nuevamente.', null, array(), 'danger');

		$this->redirect(array('action' => 'index'));

	}





	public function admin_view($id =  null) {
		$this->redirect(array('action' => 'index'));
		if ( ! $this->Email->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));

		}

		$this->request->data	= $this->Email->find('first', array(
			'conditions'	=> array('Email.id' => $id),
			'contain'		=> array('Categoria')
		));

	}

	/**
	 * Función que permite ver el último html guardado de un newsleter
	 * @param 	bigint 	$id 	Identificador del nesletter
	 */
	public function admin_view_html($id =  null) {
		if ( ! $this->Email->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));

		}

		$this->request->data	= $this->Email->find('first', array(
			'conditions'	=> array('Email.id' => $id)
		));

		if (empty($this->request->data)) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		BreadcrumbComponent::add('Newsletters ', '/emails');
		BreadcrumbComponent::add(sprintf('Último html guardado para %s', $this->request->data['Email']['nombre']));

	}

	/**
	 * Función encargada de crear el contenido HTML del newsletter según su base HTML
	 * @param 	(Int) 	$id 	Identificador del nnewsletter 	
	 */
	public function admin_generarHtml($id = null, $save = false) {

			$htmlEmail = $this->Email->find('first', array(
				'conditions' => array('Email.id' => $id), 
				'fields' => array('html','nombre', 'sitio_url', 'tienda_id', 'mostrar_cuotas', 'cuotas', 'descripcion'),
				'contain'	=> array('Categoria', 'Tienda')	
				)
			);

			if (empty($htmlEmail)) {
				$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}

			// Nombre del Newsletter
			$htmlNombre = $htmlEmail['Email']['nombre'];

			// Url del sitio que corresponde el newsletter
			$SitioUrl = $htmlEmail['Email']['sitio_url'];

			// Cuotas
			$MostrarCuotasEmail = $htmlEmail['Email']['mostrar_cuotas'];
			$CuotasEmail = $htmlEmail['Email']['cuotas'];

			$categoriasId = Hash::extract($htmlEmail['Categoria'], '{n}.id');

			$categorias = ClassRegistry::init('Categoria')->find('all', array(
					'conditions' => array(
						'Categoria.id'	=> $categoriasId,
						'Categoria.activo' => 1, 
						'Categoria.tienda_id' => $htmlEmail['Email']['tienda_id']
					),
					'order'	=> array(
						'Categoria.orden ASC'
					)
				)
			);

			// Obtenemos la información de a tienda
			$tienda = ClassRegistry::init('Tienda')->find('first', array(
				'conditions' => array('Tienda.activo' => 1, 'Tienda.id' => $htmlEmail['Email']['tienda_id'])
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

			// Se genera HTML para el newsletter (ver modelo Email)
			$htmlEmail = $this->Email->armarHtmlEmail($htmlEmail);

			$bloque = array();
			$seccion = array();

			// Orden de productos por defecto
			$ordenProductos = array(
				'Productotienda.id_product DESC'
			);


			// Dos columnas por defecto
			$dosColumnas = true;

			App::uses('CakeNumber', 'Utility');
			App::uses('CakeText', 'Utility');

			foreach ($categorias as $indice => $categoria) {

				/**
				* Condiciones para ordenar productos
				*/
				if (isset($categoria['Categoria']['orden_productos']) && ! empty($categoria['Categoria']['orden_productos'])) {
					
					switch ($categoria['Categoria']['orden_productos']) {
						case 'nombre_asc':
							$ordenProductos = array(
								'pl.name ASC'
							);
							break;
						case 'nombre_desc':
							$ordenProductos = array(
								'pl.name DESC'
							);
							break;
						case 'precio_asc':
							$ordenProductos = array(
								'Productotienda.price ASC'
							);
							break;
						case 'precio_desc':
							$ordenProductos = array(
								'Productotienda.price DESC'
							);
							break;
						case 'referencia_asc':
							$ordenProductos = array(
								'Productotienda.reference ASC'
							);
							break;
						case 'referencia_desc':
							$ordenProductos = array(
								'Productotienda.reference DESC'
							);
							break;
					}
				}

				/**
				* Obtenemos los productos relacionados a la categoría
				*/
				$relacionados = ClassRegistry::init('CategoriasProductotienda')->find('all', array(
					'fields' => array('CategoriasProductotienda.id_product'),
					'conditions' => array('CategoriasProductotienda.categoria_id' => $categoria['Categoria']['id'])
					)
				);

				// Arreglo para alojar los IDs de los productos relacionados
				$arrayRelacionadosId = array();

				// Agregamos al arreglo $arrayRelacionadosId los IDs de los productos
				foreach ($relacionados as $relacionado) {
					$arrayRelacionadosId[] = $relacionado['CategoriasProductotienda']['id_product'];
				}

				// Buscamos los productos que cumplan con el criterio
				$productos	= ClassRegistry::init('Productotienda')->find('all', array(
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
						'TaxRulesGroup' => array(
							'TaxRule' => array(
								'Tax'
							)
						),
						'SpecificPrice' => array(
							'conditions' => array(
								'OR' => array(
									'OR' => array(
										array(
											'SpecificPrice.from' => '000-00-00 00:00:00',
											'SpecificPrice.to' => '000-00-00 00:00:00'
										)
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
						'Productotienda.id_product' => $arrayRelacionadosId,
						'Productotienda.active' => 1,
						'Productotienda.available_for_order' => 1,
						'Productotienda.id_shop_default' => 1,
						'pl.id_lang' => 1 
					),
					'order' => $ordenProductos
				));

				// Agregamos los productos al arreglo final
				$categoria['Producto'] = $productos;


				// Se abre el bloque sección
				$seccion[$indice] = $htmlEmail['seccion_categoria'] . $htmlEmail['categoria'];

				// Agregamos el nombre de la categoría al HTML
				$seccion[$indice] = str_replace('[**categoria_nombre**]',$categoria['Categoria']['nombre'], $seccion[$indice]);

				// 2 o 3 columnas de prodctos
				$dosColumnas = ( $categoria['Categoria']['tres_columnas'] ) ? false : true;

				/**
				* Agregamos los productos al HTML
				*/
				foreach ($categoria['Producto'] as $llave => $producto) {

					// Retornar valor con iva;
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

					// Mostrar cuotas si viene activa
					if ($MostrarCuotasEmail && ! empty($CuotasEmail)) {
						$producto['Productotienda']['valor_cuotas'] = ($producto['Productotienda']['valor_final'] / $CuotasEmail);
					}

					/**
					* Información del producto
					*/
					$urlProducto 			= $producto[0]['url_image'];
					$porcentaje_descuento 	= ( !empty($producto['Productotienda']['descuento']) ) ? $producto['Productotienda']['descuento'] . '%' : '<font size="2">Oferta</font>' ;
					$nombre_producto		= CakeText::truncate($producto['pl']['name'], 40, array('exact' => false));
					$modelo_producto		= $producto['Productotienda']['reference'];
					$valor_producto			= ( !empty($producto['Productotienda']['descuento']) ) ? '<font style="text-decoration: line-through;">' . CakeNumber::currency($producto['Productotienda']['valor_iva'], 'CLP') . '</font>' : '' ;
					$oferta_producto		= CakeNumber::currency($producto['Productotienda']['valor_final'] , 'CLP');
					$url_producto			= sprintf('%s%s-%s.html', $SitioUrl, $producto['pl']['link_rewrite'], $producto['Productotienda']['id_product']);
					$cuotas_producto 		= ( isset($producto['Productotienda']['valor_cuotas']) ) ? sprintf('<font style="background-color:#EA3A3A; display: block;line-height: 25px;color: #fff;margin: 4px 5px;">%s cuotas de %s</font>', $CuotasEmail, CakeNumber::currency($producto['Productotienda']['valor_cuotas'] , 'CLP') ) : '';
					
					/**
					* Agregamos la información al HTML del email
					*/
					$seccion[$indice] .= ( $dosColumnas ) ? $htmlEmail['bloque_2'] : $htmlEmail['bloque_3'] ;
					$seccion[$indice] = str_replace('[**url_imagen_producto**]',$urlProducto, $seccion[$indice]);
					$seccion[$indice] = str_replace('[**porcentaje_producto**]', $porcentaje_descuento, $seccion[$indice]);
					$seccion[$indice] = str_replace('[**nombre_producto**]', $nombre_producto , $seccion[$indice]);
					$seccion[$indice] = str_replace('[**modelo_producto**]', $modelo_producto , $seccion[$indice]);
					$seccion[$indice] = str_replace('[**antes_producto**]', $valor_producto , $seccion[$indice]);
					$seccion[$indice] = str_replace('[*valor_producto*]', $oferta_producto , $seccion[$indice]);
					$seccion[$indice] = str_replace('[*cuotas_producto*]', $cuotas_producto , $seccion[$indice]);
					$seccion[$indice] = str_replace('[**url_producto**]', $url_producto , $seccion[$indice]);
				}


				// Se cierra el bloque sección
				$seccion[$indice] .= $htmlEmail['seccion_bloque'];

			}			

		
			// Unimos los contenidos
			$seccion = implode($seccion, '');

			$htmlFinal = '';

			// Limpiamos espacios en blanco y asignamos HTML de cabecera
			$htmlFinal .= trim($htmlEmail['cabecera']);

			// Agregamos HTML de secciones y productos
			$htmlFinal .= $seccion;

			// Agregamos HTML del footer
			$htmlFinal .= $htmlEmail['footer'];

			
			if ( $save ) {
				// Guardar ultimo html generado
				$data = array(
					'Email' => array(
						'id' => $id,
						'ultimo_html' => $htmlFinal,
						'semaforo' => true
					)
				);	

				$this->Email->save($data);
			}

			BreadcrumbComponent::add('Newsletters ', '/emails');
			BreadcrumbComponent::add('Generar ');

			$this->set(compact('htmlFinal', 'htmlNombre', 'save'));

	}

}

