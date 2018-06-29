<?php

App::uses('AppController', 'Controller', 'Chilexpress');

App::import('Vendor', 'LibreDTE', array('file' => 'LibreDte/autoload.php'));
App::import('Vendor', 'LibreDTE', array('file' => 'LibreDte/sasco/libredte-sdk-php/sdk/LibreDTE.php'));

class OrdenesController extends AppController
{

	public $name = 'Ordenes';    
    public $uses = array('Orden');

    public $components = array(
    	'Chilexpress.GeoReferencia'
    );
    /**
     * Obtiene y lista los medios de pago disponibles en u array único
     * @return 	array 	Array unidimensional que contiene e nombre del medio de pago
     */
    public function obtenerMediosDePago()
    {
    	$medios_de_pago = array_unique(Hash::extract($this->Orden->find('all', array('fields' => array('payment'))), '{n}.Orden.payment'));
		
		foreach ($medios_de_pago as $k => $medio) {
			unset($medios_de_pago[$k]);
			$medios_de_pago[$medio] = $medio;
		}

		return $medios_de_pago;
    }


    /**
     * Método que crea un arreglo con los valores rangoSinFormato : rangoFormateado
     * Arma una lista de rangos segun el menor y el mayor valor del parámetro $campo 
     * y su rango será definido por el parámetro $rango
     * @param 		$campo 		String 		Nombre del campo que se obtendrán los precios
     * @param 		$rango 		Int 		Intervalo entre los rangos de precios 
     * @return 		Array
     */
    public function obtenerRangoPrecios($campo = 'total_paid', $rango = 100000)
    {
    	$precios = array_unique(Hash::extract($this->Orden->find('all', array('fields' => array($campo))), sprintf('{n}.Orden.%s', $campo)));

    	# Se quitan los decimales
		foreach ($precios as $k => $precio) {
			$precios[$k] = round($precio, 0);	
		}

		# Se ordena de menor a mayor
		sort($precios);

		# Variables para definir el rango
		$primerValor = array_shift($precios);
		$ultimoValor = array_pop($precios);

		# Arreglo de rangos obtenidos 
		$rangosArr = range($primerValor, $ultimoValor, $rango);
		
		$rangos = array();

		foreach ($rangosArr as $k => $valor) {
			if ($k == 0) {
				$rangos[$k]['valor1'] = $valor;
			}else{
				$rangos[$k]['valor1'] = $valor+1;
			}
			
			if (isset($rangosArr[$k+1])) {
				$rangos[$k]['valor2'] = $rangosArr[$k+1];
			}else{
				$rangos[$k]['valor2'] = '+ más';
			}
		    
		}

		$nwRangos = array();
		foreach ($rangos as $i => $rango) {
			if (is_string($rango['valor2'])) {
				$rangoVal = sprintf('%d-%d', $rango['valor1'], 10000000000);
				$rangoTxt = sprintf('%s - %s', CakeNumber::currency($rango['valor1'], 'CLP'), $rango['valor2']);
			}else{
				$rangoVal = sprintf('%d-%d', $rango['valor1'], $rango['valor2']);
				$rangoTxt = sprintf('%s - %s', CakeNumber::currency($rango['valor1'], 'CLP'), CakeNumber::currency($rango['valor2'], 'CLP'));
			}

			$nwRangos[$rangoVal] = $rangoTxt;
		}
		
		return $nwRangos;
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
		$this->verificarTienda();

		$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;
    	$categorias = array();

    	$textoBuscar = null;

		// Filtrado de ordenes por formulario
		if ( $this->request->is('post') ) {

			$this->filtrar('ordenes', 'index');

		}

		$paginate = array_replace_recursive($paginate, array(
			'limit' => 20,
			'contain' => array('OrdenEstado' => array('Lang'), 'Dte'),
			'conditions' => array(),
			'order' => array('Orden.id_order' => 'DESC')
			));


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'id':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Orden.id_order' => $valor)));
						break;
					case 'ref':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Orden.reference' => $valor)));
						break;
					case 'sta':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Orden.current_state' => $valor)));
						break;
					case 'mdp':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Orden.payment' => $valor)));
						break;
					case 'mpa':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Orden.total_paid BETWEEN ? AND ? ' => explode('-', $valor))));
						break;
					case 'men':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Orden.total_shipping BETWEEN ? AND ? ' => explode('-', $valor))));
						break;
					case 'mde':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Orden.total_discounts BETWEEN ? AND ? ' => explode('-', $valor))));
						break;
					case 'dte':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.estado' => $valor)));
						break;
				}
			}
		}

		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden', 'OrdenEstado', 'Lang', 'CustomUserdata'));

		$this->paginate = $paginate;

		$ordenes	= $this->paginate();
		$totalMostrados = $this->Orden->find('count');
		
		# Estados del pedidos
		$estados = $this->Orden->OrdenEstado->find('all', array('contain' => array('Lang')));
		$estados = Hash::extract($estados, '{n}.Lang.0.OrdenEstadoIdioma.name');
		
		# Medios de pago
		$medios_de_pago = $this->obtenerMediosDePago();
		
		$rangosPagado = $this->obtenerRangoPrecios('total_paid', 500000);
		$rangosEnvio = $this->obtenerRangoPrecios('total_shipping', 1000);
		$rangosDescuento = $this->obtenerRangoPrecios('total_discounts', 50000);

		# Estados del DTE
		# OBtener cantidad de folios facturas
		#$this->getFolioInfo(33, 76381142);

		# Informacion del contribuyente
		#$contribuyente = $this->getContribuyenteInfo($this->rutSinDv($this->Session->read('Tienda.rut')));
		
		#$this->GeoReferencia->obtenerRegiones();

		BreadcrumbComponent::add('Ordenes de compra ');

		$this->set(compact('ordenes', 'totalMostrados', 'estados', 'medios_de_pago', 'rangosPagado', 'rangosEnvio', 'rangosDescuento'));
	}


	public function admin_orden($id = '') {

		$this->verificarTienda();

		if ( ! $this->Orden->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden','CustomUserdata', 'CustomField', 'CustomFieldLang'));

		$opt = array(
			'conditions'	=> array('Orden.id_order' => $id),
			'contain' => array(
				'OrdenEstado' => array('Lang'),
				'OrdenDetalle',
				'Dte'
				)
		);


		$this->request->data	= $this->Orden->find('first', $opt);

		BreadcrumbComponent::add('Ordenes de compra', '/ordenes');
		BreadcrumbComponent::add('Ver Dte´s ');

		$this->set(compact('dtes'));

	}


	public function admin_invalidar($id_dte = '', $id_orden = '')
	{	

		$this->Orden->Dte->id = $id_dte;

		if ($this->Orden->Dte->saveField('estado', '')) {
			$this->Session->setFlash('DTE invalidado con éxito.', null, array(), 'info');
		}else{
			$this->Session->setFlash('No fue posible invalidar el DTE.', null, array(), 'danger');
		}

		$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_orden));
	}


	/**
	 * Verifica si una orden tiene DTE emitido correctamente
	 * @return bool 
	 */
	public function unico()
	{
		$dts = ClassRegistry::init('Dte')->find('count', array(
			'conditions' => array(
				'Dte.id_order' => $this->request->data['Dte']['id_order'],
				'Dte.estado' => 'dte_real_emitido'
			)
		));
		
		if ($dts > 0) {
			return false;
		}

		return true;
		
	}


	public function admin_generar($id_orden = '')
	{
		$this->verificarTienda();

		if ( ! $this->Orden->exists($id_orden) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden', 'OrdenEstado', 'OrdenDetalle', 'Lang', 'Cliente', 'ClienteHilo', 'ClienteMensaje', 'Empleado', 'CustomUserdata', 'CustomField', 'CustomFieldLang'));

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			if(!$this->unico())
			{
				$this->Session->setFlash('Ya ha generado un DTE válido para ésta orden de compra.' , null, array(), 'warning');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_orden));
			}

			# Rut sin puntos
			if (!empty($this->request->data['Dte']['rut_receptor'])) {
				$this->request->data['Dte']['rut_receptor'] = str_replace('.', '', $this->request->data['Dte']['rut_receptor']);
			}

			# Rut Transportista sin puntos
			if (isset($this->request->data['Dte']['rut_transportista']) && !empty($this->request->data['Dte']['rut_transportista'])) {
				$this->request->data['Dte']['rut_transportista'] = str_replace('.', '', $this->request->data['Dte']['rut_transportista']);
			}

			# Rut chofer sin puntos
			if (isset($this->request->data['Dte']['rut_chofer']) && !empty($this->request->data['Dte']['rut_chofer'])) {
				$this->request->data['Dte']['rut_chofer'] = str_replace('.', '', $this->request->data['Dte']['rut_chofer']);
			}
		
			# Si existe costo de transporte se agrega como ITEM
			if (intval($this->request->data['Dte']['Transporte']) > 0) {
				$cantidadItem = (count($this->request->data['Detalle']) + 1);
				$this->request->data['Detalle'][$cantidadItem]['VlrCodigo'] = "COD-Trns";
				$this->request->data['Detalle'][$cantidadItem]['NmbItem'] = "Transporte";

				# Para boleta se envia el valor bruto y así evitar que el monto aumente o disminuya por el calculo de iva
				if ($this->request->data['Dte']['tipo_documento'] == 39) {
					$this->request->data['Detalle'][$cantidadItem]['PrcItem'] = round($this->request->data['Dte']['Transporte']);
				}else{
					$this->request->data['Detalle'][$cantidadItem]['PrcItem'] = $this->precio_neto($this->request->data['Dte']['Transporte']);
				}
				$this->request->data['Detalle'][$cantidadItem]['QtyItem'] = 1;
			}
				
			# Si el DTE es boleta enviamos los precios Brutos de los items
			if ($this->request->data['Dte']['tipo_documento'] == 39) {
				foreach ($this->request->data['Detalle'] as $k => $item) {

					# Precio de transporte viene Bruto
					if ($item['NmbItem'] != 'Transporte') {
						$this->request->data['Detalle'][$k]['PrcItem'] = $this->precio_bruto($item['PrcItem']);
					}
					
				}
			}else{
				# se envia el descuento en Bruto
				if (isset($this->request->data['DscRcgGlobal']['ValorDR']) && $this->request->data['DscRcgGlobal']['ValorDR'] > 0) {
					$this->request->data['DscRcgGlobal']['ValorDR'] = $this->precio_neto($this->request->data['DscRcgGlobal']['ValorDR']);	
				}
			}

			$this->request->data['DteReferencia'] = $this->clear($this->request->data['DteReferencia'], 'folio');

			# Guardar información del DTE en base de datos local
			if($this->Orden->Dte->saveAll($this->request->data)) {

				try {
					# Enviar DTE a LibreDTE
					$this->generarDte();
				} catch (Exception $e) {

					if($e->getCode() == 200) {
						$this->Session->setFlash($e->getMessage() , null, array(), 'success');
					}else{
						$this->Session->setFlash($e->getMessage() , null, array(), 'warning');
					}
				}

				$id_dte = $this->Orden->Dte->find('first', array(
					'conditions' => array('Dte.id_order' => $id_orden),
					'order' => array('Dte.id' => 'DESC')
					)
				);

				if (!empty($id_dte)) {
					$this->redirect(array('controller' => 'ordenes', 'action' => 'editar', $id_dte['Dte']['id'], $id_orden));
				}

				$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_orden));

			}else{
				$this->Session->setFlash('Error al guardar la información en la base de detos local. Intente nuevamente.' , null, array(), 'warning');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_orden));
			}

		}else{

			$opt = array(
				'conditions'	=> array('Orden.id_order' => $id_orden),
				'contain' => array(
					'OrdenEstado' => array('Lang'),
					'OrdenDetalle' => array(
						'conditions' => array(
							'OrdenDetalle.product_quantity_refunded' => 0
						)
					),
					'Dte',
					'Cliente',
					'ClienteHilo' => array('ClienteMensaje' => array('Empleado')),
				),
			);

			$modulosExternos = $this->Orden->validarModulosExternos();
			if ($modulosExternos) {
				$opt = array_replace_recursive($opt, array(
					'contain' => array(
						'CustomUserdata' => array('CustomField' => array('Lang'))
					)
				));
			}

			$this->request->data	= $this->Orden->find('first', $opt);

		}
		
		# Array de tipos de documentos
		$tipoDocumento = $this->dtePermitidos($this->rutSinDv($this->Session->read('Tienda.rut')));

		# Array de comunas actualizadas
		$comunas = $this->obtener_comunas_actualizadas();

		# Tipos de traslados
		$traslados = $this->tipoTraslado;

		# Códigos d referencia Libre DTE
		$codigoReferencia = $this->codigoReferencia;

		# Medio de pago
		$medioDePago = $this->medioDePago;

		# DTE´s para referenciar
		$dteEmitidos = $this->Orden->Dte->find('list', array('conditions' => array(
			'Dte.id_order' => $id_orden,
			'Dte.estado' => 'dte_real_emitido'
			)
		));
		
		BreadcrumbComponent::add('Ordenes de compra', '/ordenes');
		BreadcrumbComponent::add('Ver Dte´s', '/ordenes/orden/'.$id_orden);
		BreadcrumbComponent::add('Generar Dte ');
		
		$this->set(compact('comunas', 'tipoDocumento', 'traslados', 'dteEmitidos', 'codigoReferencia', 'medioDePago'));

	}

	public function admin_editar($id_dte = '', $id_orden = '')
	{
		$this->verificarTienda();
		
		if ( ! $this->Orden->exists($id_orden) )
		{
			$this->Session->setFlash('No existe la orden seleccionada.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( ! $this->Orden->Dte->exists($id_dte) )
		{
			$this->Session->setFlash('No existe el dte seleccionado.', null, array(), 'danger');
			$this->redirect(array('action' => 'orden', $id_orden));
		}

		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden', 'OrdenEstado', 'OrdenDetalle', 'Lang', 'Cliente', 'ClienteHilo', 'ClienteMensaje', 'Empleado', 'CustomUserdata', 'CustomField', 'CustomFieldLang'));

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			if(!$this->unico())
			{
				$this->Session->setFlash('Ya ha generado un DTE válido para ésta orden de compra.' , null, array(), 'warning');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_orden));
			}

			# Rut sin puntos
			if (!empty($this->request->data['Dte']['rut_receptor'])) {
				$this->request->data['Dte']['rut_receptor'] = str_replace('.', '', $this->request->data['Dte']['rut_receptor']);
			}
		
			# Si existe costo de transporte se agrega como ITEM
			if (intval($this->request->data['Dte']['Transporte']) > 0) {
				$cantidadItem = (count($this->request->data['Detalle']) + 1);
				$this->request->data['Detalle'][$cantidadItem]['NmbItem'] = "Transporte";

				# Para boleta se envia el valor bruto y así evitar que el monto aumente o disminuya por el calculo de iva
				if ($this->request->data['Dte']['tipo_documento'] == 39) {
					$this->request->data['Detalle'][$cantidadItem]['PrcItem'] = round($this->request->data['Dte']['Transporte']);
				}else{
					$this->request->data['Detalle'][$cantidadItem]['PrcItem'] = $this->precio_neto($this->request->data['Dte']['Transporte']);
				}
				$this->request->data['Detalle'][$cantidadItem]['QtyItem'] = 1;
			}
				
			# Si el DTE es boleta enviamos los precios Brutos de los items
			if ($this->request->data['Dte']['tipo_documento'] == 39) {
				foreach ($this->request->data['Detalle'] as $k => $item) {

					# Precio de transporte viene Bruto
					if ($item['NmbItem'] != 'Transporte') {
						$this->request->data['Detalle'][$k]['PrcItem'] = $this->precio_bruto($item['PrcItem']);
					}
					
				}
			}else{
				# se envia el descuento en Bruto
				if (isset($this->request->data['DscRcgGlobal']['ValorDR']) && $this->request->data['DscRcgGlobal']['ValorDR'] > 0) {
					$this->request->data['DscRcgGlobal']['ValorDR'] = $this->precio_neto($this->request->data['DscRcgGlobal']['ValorDR']);	
				}
			}

			$this->request->data['DteReferencia'] = $this->clear($this->request->data['DteReferencia'], 'folio');

			# Se eliminan las referencias
			$this->Orden->Dte->DteReferencia->deleteAll(array('DteReferencia.dte_id' => $id_dte));

			
			# Guardar información del DTE en base de datos local
			if($this->Orden->Dte->saveAll($this->request->data)) {
				
				# Se genera DTE Real desde uno temporal
				if ( $this->request->data['Dte']['estado'] == 'dte_temporal_emitido' || $this->request->data['Dte']['estado'] == 'dte_real_no_emitido' ) {
					try {
						# Enviar DTE a LibreDTE
						$this->generarDteRealDesdeTemporal($this->request->data['Dte']['id']);

					} catch (Exception $e) {

						if($e->getCode() == 200) {
							$this->Session->setFlash($e->getMessage() , null, array(), 'success');
						}else{
							$this->Session->setFlash($e->getMessage() , null, array(), 'warning');
						}
					}	
				}

				# Se genera DTE desde el cominezo
				if ( $this->request->data['Dte']['estado'] == 'no_generado' || $this->request->data['Dte']['estado'] == 'dte_temporal_no_emitido' ) {
					try {
						# Enviar DTE a LibreDTE
						if (isset($this->request->data['Dte']['id']) && !empty($this->request->data['Dte']['id'])) {
							$this->generarDte($this->request->data['Dte']['id']);
						}else{
							$this->generarDte();
						}

					} catch (Exception $e) {

						if($e->getCode() == 200) {
							$this->Session->setFlash($e->getMessage() , null, array(), 'success');
						}else{
							$this->Session->setFlash($e->getMessage() , null, array(), 'warning');
						}
					}	
				}

				$this->redirect(array('action' => 'orden', $id_orden));

			}else{
				$this->Session->setFlash('Error al guardar la información en la base de detos local. Intente nuevamente.' , null, array(), 'warning');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'editar', $id_dte));
			}

		}
		else
		{

			$opt = array(
				'conditions'	=> array('Dte.id' => $id_dte),
				'contain' => array(
					'DteReferencia',
					'Orden' => array(
						'OrdenEstado' => array('Lang'),
						'OrdenDetalle' => array(
							'conditions' => array(
								'OrdenDetalle.product_quantity_refunded' => 0
							)
						),
						'Dte',
						'Cliente',
						'ClienteHilo' => array('ClienteMensaje' => array('Empleado')),
					)
				)
			);

			$modulosExternos = $this->Orden->validarModulosExternos();
			if ($modulosExternos) {
				$opt = array_replace_recursive($opt, array(
					'contain' => array(
						'Orden' => array(
							'CustomUserdata' => array('CustomField' => array('Lang'))
						)
					)
				));
			}

			$this->request->data	= $this->Orden->Dte->find('first', $opt);

		}
		
		$estadoSii = '';

		# Estado del dte Emitido en el SII
		if ($this->request->data['Dte']['estado'] == 'dte_real_emitido' && $this->request->data['Dte']['tipo_documento'] == 33) {
			$estadoSii = $this->consultarDteSii($this->request->data['Dte']['tipo_documento'], $this->request->data['Dte']['folio'], $this->request->data['Dte']['emisor']);
		}

		# Consultar por DTE Emitido
		if (!empty($this->request->data['Dte']) && $this->request->data['Dte']['estado'] == 'dte_real_emitido' ) {
			try {
				$this->consultarDteLibreDte($this->request->data['Dte']['emisor'], $this->request->data['Dte']['tipo_documento'], $this->request->data['Dte']['folio'], $this->request->data['Dte']['fecha'], $this->request->data['Dte']['total']);
			} catch (Exception $e) {
				if ($e->getCode() == 400) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'danger');
				}else{
					$this->Session->setFlash($e->getMessage() , null, array(), 'warning');
				}
			}
		}

		# Si no se ha generado el PDF de un documento emitido se intenta generar
		if (empty($this->request->data['Dte']['pdf']) && $this->request->data['Dte']['estado'] == 'dte_real_emitido' ) {
			try {
				$this->generarPDFDteEmitido($id_orden, $id_dte, $this->request->data['Dte']['tipo_documento'], $this->request->data['Dte']['folio'], $this->request->data['Dte']['emisor'] );
			} catch (Exception $e) {
				if($e->getCode() < 300) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'success');
				}

				if ($e->getCode() > 300) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'danger');
				}
			}

			# Se redirecciona a si mismo
			$this->redirect(array('controller' => 'ordenes', 'action' => 'editar', $id_dte, $id_orden));
		}

		# Array de tipos de documentos
		$tipoDocumento = $this->dtePermitidos($this->rutSinDv($this->Session->read('Tienda.rut')));

		# Array de comunas actualizadas
		$comunas = $this->obtener_comunas_actualizadas();

		# Tipos de traslados
		$traslados = $this->tipoTraslado;

		# Códigos d referencia Libre DTE
		$codigoReferencia = $this->codigoReferencia;

		# Medio de pago
		$medioDePago = $this->medioDePago;

		# DTE´s para referenciar
		$dteEmitidos = $this->Orden->Dte->find('list', array('conditions' => array(
			'Dte.id_order' => $id_orden,
			'Dte.estado' => 'dte_real_emitido'
			)
		));
		
		BreadcrumbComponent::add('Ordenes de compra', '/ordenes');
		BreadcrumbComponent::add('Ver Dte´s', '/ordenes/orden/'.$id_orden);
		BreadcrumbComponent::add('Editar Dte ');
		
		$this->set(compact('comunas', 'tipoDocumento', 'traslados', 'dteEmitidos', 'codigoReferencia', 'medioDePago', 'estadoSii'));
	}


	public function admin_view($id = '') {

		$this->verificarTienda();

		if ( ! $this->Orden->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden', 'OrdenEstado', 'OrdenDetalle', 'Lang', 'Cliente', 'ClienteHilo', 'ClienteMensaje', 'Empleado'));

		if ( $this->request->is('post') || $this->request->is('put') )
		{	

			if(!$this->unico())
			{
				$this->Session->setFlash('Ya ha generado un DTE válido para ésta orden de compra.' , null, array(), 'warning');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_orden));
			}

			# Rut sin puntos
			if (!empty($this->request->data['Dte']['rut_receptor'])) {
				$this->request->data['Dte']['rut_receptor'] = str_replace('.', '', $this->request->data['Dte']['rut_receptor']);
			}
		
			# Si existe costo de transporte se agrega como ITEM
			if (intval($this->request->data['Dte']['Transporte']) > 0) {
				$cantidadItem = (count($this->request->data['Detalle']) + 1);
				$this->request->data['Detalle'][$cantidadItem]['NmbItem'] = "Transporte";

				# Para boleta se envia el valor bruto y así evitar que el monto aumente o disminuya por el calculo de iva
				if ($this->request->data['Dte']['tipo_documento'] == 39) {
					$this->request->data['Detalle'][$cantidadItem]['PrcItem'] = round($this->request->data['Dte']['Transporte']);
				}else{
					$this->request->data['Detalle'][$cantidadItem]['PrcItem'] = $this->precio_neto($this->request->data['Dte']['Transporte']);
				}
				$this->request->data['Detalle'][$cantidadItem]['QtyItem'] = 1;
			}
				
			# Si el DTE es boleta enviamos los precios Brutos de los items
			if ($this->request->data['Dte']['tipo_documento'] == 39) {
				foreach ($this->request->data['Detalle'] as $k => $item) {

					# Precio de transporte viene Bruto
					if ($item['NmbItem'] != 'Transporte') {
						$this->request->data['Detalle'][$k]['PrcItem'] = $this->precio_bruto($item['PrcItem']);
					}
					
				}
			}else{
				# se envia el descuento en Bruto
				if (isset($this->request->data['DscRcgGlobal']['ValorDR']) && $this->request->data['DscRcgGlobal']['ValorDR'] > 0) {
					$this->request->data['DscRcgGlobal']['ValorDR'] = $this->precio_neto($this->request->data['DscRcgGlobal']['ValorDR']);	
				}
			}

			
			# Guardar información del DTE en base de datos local
			if($this->Orden->Dte->save($this->request->data)) {
				
				# Se genera DTE Real desde uno temporal
				if ( $this->request->data['Dte']['estado'] == 'dte_temporal_emitido' || $this->request->data['Dte']['estado'] == 'dte_real_no_emitido' ) {
					try {
						# Enviar DTE a LibreDTE
						$this->generarDteRealDesdeTemporal($this->request->data['Dte']['id']);

					} catch (Exception $e) {

						if($e->getCode() == 200) {
							$this->Session->setFlash($e->getMessage() , null, array(), 'success');
						}else{
							$this->Session->setFlash($e->getMessage() , null, array(), 'warning');
						}
					}	
				}

				# Se genera DTE desde el cominezo
				if ( $this->request->data['Dte']['estado'] == 'no_generado' || $this->request->data['Dte']['estado'] == 'dte_temporal_no_emitido' ) {
					try {
						# Enviar DTE a LibreDTE
						if (isset($this->request->data['Dte']['id']) && !empty($this->request->data['Dte']['id'])) {
							$this->generarDte($this->request->data['Dte']['id']);
						}else{
							$this->generarDte();
						}

					} catch (Exception $e) {

						if($e->getCode() == 200) {
							$this->Session->setFlash($e->getMessage() , null, array(), 'success');
						}else{
							$this->Session->setFlash($e->getMessage() , null, array(), 'warning');
						}
					}	
				}

				$this->redirect(array('controller' => 'ordenes', 'action' => 'view', $id));

			}else{
				$this->Session->setFlash('Error al guardar la información en la base de detos local. Intente nuevamente.' , null, array(), 'warning');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'view', $id));
			}

		}
		else
		{
			$this->request->data	= $this->Orden->find('first', array(
				'conditions'	=> array('Orden.id_order' => $id),
				'contain' => array(
					'OrdenEstado' => array('Lang'),
					'OrdenDetalle' => array(
						'conditions' => array(
							'OrdenDetalle.product_quantity_refunded' => 0
						)
					),
					'Dte',
					'Cliente',
					'ClienteHilo' => array('ClienteMensaje' => array('Empleado')))
			));
		}
		
		# DTE no creado
		if (empty($this->request->data['Dte']) || $this->request->data['Dte'][0]['estado'] == 'no_generado') {
			$this->Session->setFlash('Aún no se ha emitido del DTE para esta orden de compra.' , null, array(), 'danger');
		}
		
		# Array de tipos de documentos
		$tipoDocumento = $this->dtePermitidos($this->rutSinDv($this->Session->read('Tienda.rut')));

		# Array de comunas actualizadas
		$comunas = $this->obtener_comunas_actualizadas();
		
		BreadcrumbComponent::add('Ordenes de compra', '/ordenes');
		BreadcrumbComponent::add('Ver ');

		# Si no se ha generado el PDF de un documento emitido se intenta generar
		if (!empty($this->request->data['Dte']) && empty($this->request->data['Dte'][0]['pdf']) && !empty($this->request->data['Dte'][0]['folio']) ) {
			try {
				$this->generarPDFDteEmitido($id, $this->request->data['Dte'][0]['id'], $this->request->data['Dte'][0]['tipo_documento'], $this->request->data['Dte'][0]['folio'], $this->request->data['Dte'][0]['emisor'] );
			} catch (Exception $e) {
				if($e->getCode() < 300) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'success');
				}

				if ($e->getCode() > 300) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'danger');
				}
			}
		}


		# Consultar por DTE Emitido
		if (!empty($this->request->data['Dte']) && $this->request->data['Dte'][0]['estado'] == 'dte_real_emitido' ) {
			try {
				$this->consultarDteLibreDte($this->request->data['Dte'][0]['emisor'], $this->request->data['Dte'][0]['tipo_documento'], $this->request->data['Dte'][0]['folio'], $this->request->data['Dte'][0]['fecha'], $this->request->data['Dte'][0]['total']);
			} catch (Exception $e) {
				if ($e->getCode() == 400) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'danger');
				}else{
					$this->Session->setFlash($e->getMessage() , null, array(), 'warning');
				}
			}
		}
		
		$this->set(compact('comunas', 'tipoDocumento'));
	}

	/**
	 * Función que limpia un array según su índice
	 * @param 	$arr 		array 		Arreglo a limpiar
	 * @param 	$validar 	string 		Indice con el cual evaluaremos si está vacio
	 * @return 	$arr 
	 */
	public function clear($arr = array(), $validar = '') {
		if ( is_array($arr) && !empty($arr) && !empty($validar) ) {
			foreach ($arr as $indice => $valor) {
				if ( isset($valor[$validar]) && empty($valor[$validar]) ) {
					unset($arr[$indice]);
				}
			}
		}

		return $arr;
	}


	/**
	 * Tipos de documentos permitidios por el SII
	 */
	public $tipoDocumento = array(
		30 => 'factura',
		32 => 'factura de venta bienes y servicios no afectos o exentos de IVA',
		35 => 'Boleta',
		38 => 'Boleta exenta',
		45 => 'factura de compra',
		55 => 'nota de débito',
		60 => 'nota de crédito',
		103 => 'Liquidación',
		40 => 'Liquidación Factura',
		43 => 'Liquidación - Factura Electrónica',
		33 => 'Factura Electrónica',
		34 => 'Factura No Afecta o Exenta Electrónica',
		39 => 'Boleta Electrónica',
		41 => 'Boleta Exenta Electrónica',
		46 => 'Factura de Compra Electrónica',
		56 => 'Nota de Débito Electrónica',
		61 => 'Nota de Crédito Electrónica',
		50 => 'Guía de Despacho',
		52 => 'Guía de Despacho Electrónica',
		110 => 'Factura de Exportación Electrónica',
		111 => 'Nota de Débito de Exportación Electrónica',
		112 => 'Nota de Crédito de Exportación Electrónica',
		801 => 'Orden de Compra', 
		802 => 'Nota de pedido',
		803 => 'Contrato',
		804 => 'Resolución',
		805 => 'Proceso ChileCompra',
		806 => 'Ficha ChileCompra',
		807 => 'DUS',
		808 => 'B/L (Conocimiento de embarque)',
		809 => 'AWB (Air Will Bill)',
		810 => 'MIC/DTA',
		811 => 'Carta de Porte',
		812 => 'Resolución del SNA donde califica Servicios de Exportación',
		813 => 'Pasaporte',
		814 => 'Certificado de Depósito Bolsa Prod. Chile',
		815 => 'Vale de Prenda Bolsa Prod. Chile'
	);


	/**
	 * Tipos de traslado permitidios por el SII
	 */
	public $tipoTraslado = array(
		1 => 'Operación constituye venta',
		2 => 'Ventas por efectuar',
		3 => 'Consignaciones',
		4 => 'Entrega gratuita',
		5 => 'Traslados internos',
		6 => 'Otros traslados no venta',
		7 => 'Guía de devolución',
		8 => 'Traslado para exportación. (no venta)',
		9 => 'Venta para exportación'
	);


	/**
	 * Tipos de códigos de referencia
	 */
	public $codigoReferencia = array(
		1 => 'Anula documento',
		2 => 'Corrige montos',
		3 => 'Corrige texto'
	);


	/**
	 * Tipos de medios de pago
	 */
	public $medioDePago = array(
		1 => 'Contado',
		2 => 'Crédito',
		3 => 'Sin costo (entrega gratuita)'
 	);


	/**
	 * Método que imprime un json del dte por id
	 * @param 		$id 	int 	Idetificador del DTE
	 * @return 		json
	 */
	public function admin_obtenerDte($id = '')
	{
		if (!empty($id)) {
			$dte = $this->Orden->Dte->find('first', array('conditions' => array('id' => $id)));
			echo json_encode($dte);
		}
		exit;
	}



	/**
	 * Obtiene las comunas de Chile actualiadas desde la API disponible en http://apis.digital.gob.cl
	 * @return 		Array 	Arreglo con las Comunas
	 */
	public function obtener_comunas_actualizadas()
	{	
		$comunas = json_decode(file_get_contents('http://apis.digital.gob.cl/dpa/comunas'));
		$nwComunas = array();	
		foreach ($comunas as $k => $comuna) {
			$nwComunas[$comuna->nombre] = $comuna->nombre;
		}

		return $nwComunas;
	}


	/**
	 * Método encragado de obtener el PDF de un DTE temporal
	 */
	public function admin_getPdfDteTemporal($receptor, $tipo , $temporal, $emisor) 
	{
		// datos a utilizar
		$url = 'https://libredte.cl';
		$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

		// crear cliente
		$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
		$LibreDTE->setSSL(false, false); ///< segundo parámetro =false desactiva verificación de SSL

		# Obtenemos el PDFasd
		$pdf = $LibreDTE->get('/dte/dte_tmps/pdf/'.$receptor.'/'.$tipo.'/'.$temporal.'/'.$emisor);
		
		if ($pdf['status']['code'] == 200) {
			header($pdf['header'][0]);
			header( sprintf('Date: %s', $pdf['header']['Date']) );
			header( sprintf('Content-Type: %s', $pdf['header']['Content-Type']) );
			header( sprintf('Content-Disposition: %s', $pdf['header']['Content-Disposition']) );
			header( sprintf('Vary: %s', $pdf['header']['Vary']) );
			header( sprintf('Date: %s', $pdf['header']['Date']) );
			echo $pdf['body'];
			exit;
		}
	}


	/**
	 * Retorna la cantidad de folios disponibles que tiene el usuario
	 */
	public function getFolioInfo($tipo_dte, $rut_contribuyente ,$ajax = false) 
	{
		// datos a utilizar
		$url = 'https://libredte.cl';
		$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

		// crear cliente
		$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
		$LibreDTE->setSSL(false, false); ///< segundo parámetro =false desactiva verificación de SSL

		# Obtenemos información de los folios
		$folios = $LibreDTE->get('/dte/admin/dte_folios/info/'.$tipo_dte.'/'.$rut_contribuyente);
		
		if ($folios['status']['code'] == 200) {
			if ($ajax) {
				return json_encode($folios['body']);
			}
			
			return $folios['body'];
		}

		return;
	}


	/**
	 * Retorna los datos del rut del contribuyente consultado
	 * @param 		$rut_contribuyente 		int 		Rut a buscar
	 * @param 		$ajax 					bool 		Determina el tipo de retorno del la información
	 * @return 		array/json
	 */
	public function admin_getContribuyenteInfo($rut_contribuyente, $ajax = false) 
	{
		// datos a utilizar
		$url = 'https://libredte.cl';
		$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

		// crear cliente
		$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
		$LibreDTE->setSSL(false, false); ///< segundo parámetro =false desactiva verificación de SSL

		# Obtenemos información del contribuyente
		$contribuyente = $LibreDTE->get('/dte/contribuyentes/info/'.$rut_contribuyente);
		
		if ($contribuyente['status']['code'] == 200) {
			if ($ajax) {
				echo json_encode($contribuyente['body']);
				exit;
			}

			return $contribuyente['body'];
		}

		return;
	}


	/**
	 * Generar un DTE desde un documento vacio
	 * @param 		int 	$id_dte 	Idntificador del DTE ya emitido
	 */
	public function generarDte($id_dte = '')
	{	
		// datos a utilizar
		$url = 'https://libredte.cl';
		$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';
		
		# Arreglo Base
		$dte = array(
		    'Encabezado' => array(
		        'IdDoc' => array(
		            'TipoDTE' => $this->request->data['Dte']['tipo_documento'],
		        ),
		        'Emisor' => array(
		            'RUTEmisor' => '76381142-5',
		        )
		    ),
		      
		);

		# Glosa
		if (!empty($this->request->data['Dte']['glosa'])) {
			$dte = array_replace_recursive($dte, array(
				'Encabezado' => array(
					'IdDoc' => array(
						'TermPagoGlosa' => $this->request->data['Dte']['glosa']
					)
				)
			));
		}

		# Fecha
		if (!empty($this->request->data['Dte']['fecha'])) {
			$dte = array_replace_recursive($dte, array(
				'Encabezado' => array(
					'IdDoc' => array(
						'FchEmis' => $this->request->data['Dte']['fecha']
					)
				)
			));
		}

		# Items
		if (!empty($this->request->data['Detalle'])) {

			$detalle = array();
			foreach ($this->request->data['Detalle'] as $i => $item) {
				$detalle[] = array(
					'CdgItem' => array(
			    		'TpoCodigo' => 'INT1',
			            'VlrCodigo' => $item['VlrCodigo']
			    	),
			        'NmbItem' => $item['NmbItem'],
			        'QtyItem' => $item['QtyItem'],
			        'PrcItem' => $item['PrcItem']
			       );
			}

			$dte = array_replace_recursive($dte, array(
				'Detalle' => $detalle
				)
			);
		}


		# Incluye Receptor
		if (!empty($this->request->data['Dte']['rut_receptor'])) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Receptor' => array(
					'RUTRecep' => $this->request->data['Dte']['rut_receptor']
					)
			));
		}else{
			# Boleta nominativa
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Receptor' => array(
					'RUTRecep' => '66666666-6'
					)
			));
		}

		# Incluye Razón Social
		if (!empty($this->request->data['Dte']['razon_social_receptor'])) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Receptor' => array(
					'RznSocRecep' => $this->request->data['Dte']['razon_social_receptor']
					)
			));
		}
		# Incluye Giro Receptor
		if (!empty($this->request->data['Dte']['giro_receptor'])) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Receptor' => array(
					'GiroRecep' => $this->request->data['Dte']['giro_receptor']
					)
			));
		}
		# Incluye Dirección Receptor
		if (!empty($this->request->data['Dte']['direccion_receptor'])) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Receptor' => array(
					'DirRecep' => $this->request->data['Dte']['direccion_receptor']
					)
			));
		}
		# Incluye Comuna Receptor
		if (!empty($this->request->data['Dte']['comuna_receptor'])) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Receptor' => array(
					'CmnaRecep' => $this->request->data['Dte']['comuna_receptor']
					)
			));
		}

		# Incluye medio de pago
		if (!empty($this->request->data['medio_de_pago'])) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'FmaPago' => $this->request->data['medio_de_pago']
			));
		}

		# Inluye Descuento Global
		if (isset($this->request->data['DscRcgGlobal']) && $this->request->data['Dte']['tipo_documento'] != 52 ) {
			$dte = array_replace_recursive($dte, array(
				"DscRcgGlobal" => array(
			        "TpoMov" => "D",
			        "TpoValor" => "$",
			        'ValorDR' => $this->request->data['DscRcgGlobal']['ValorDR']
				)
			));
		}
		
		# Incluye Tipo de transporte
		/*if ( isset($this->request->data['Dte']['tipo_traslado']) && !empty($this->request->data['Dte']['tipo_traslado']) ) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'IdDoc' => array(
					'IndTraslado' => $this->request->data['Dte']['tipo_traslado']
					)	
				)
			);
		}*/			

		# Incluye patente
		if ( isset($this->request->data['Dte']['patente']) && !empty($this->request->data['Dte']['patente']) ) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Transporte' => array(
					'Patente' => $this->request->data['Dte']['patente']
					)
				)
			);
		}

		# Incluye rut transportista
		if ( isset($this->request->data['Dte']['rut_transportista']) && !empty($this->request->data['Dte']['rut_transportista']) ) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Transporte' => array(
					'RUTTrans' => $this->request->data['Dte']['rut_transportista']
					)
				)
			);
		}

		# Incluye rut chofer
		if ( isset($this->request->data['Dte']['rut_chofer']) && !empty($this->request->data['Dte']['rut_chofer']) ) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Transporte' => array(
					'Chofer' => array(
						'RUTChofer' => $this->request->data['Dte']['rut_chofer'],
						)
					)
				)
			);
		}

		# Incluye nombre chofer
		if ( isset($this->request->data['Dte']['nombre_chofer']) && !empty($this->request->data['Dte']['nombre_chofer']) ) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Transporte' => array(
					'Chofer' => array(
						'NombreChofer' => $this->request->data['Dte']['nombre_chofer']
						)
					)
				)
			);
		}

		# Incluye dirección destino
		if ( isset($this->request->data['Dte']['direccion_traslado']) && !empty($this->request->data['Dte']['direccion_traslado']) ) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Transporte' => array(
					'DirDest' => $this->request->data['Dte']['direccion_traslado']
					)
				)
			);
		}

		# Incluye comuna destino
		if ( isset($this->request->data['Dte']['comuna_traslado']) && !empty($this->request->data['Dte']['comuna_traslado']) ) {
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Transporte' => array(
					'CmnaDest' => $this->request->data['Dte']['comuna_traslado']
					)
				)
			);
		}
		
		# Incluye referencia
		if ( isset($this->request->data['DteReferencia']) && !empty($this->request->data['DteReferencia']) ) {

			$DteReferencia = array();
			$count = 0;
			foreach ($this->request->data['DteReferencia'] as $i => $ref) {
				if ($count == 0) {
					$DteReferencia = array(
						'TpoDocRef' => $ref['tipo_documento'],
						'FolioRef' => $ref['folio'],
						'FchRef' => $ref['fecha'],
						'CodRef' => $ref['codigo_referencia'],
						'RazonRef' => $ref['razon']
					);
				}
				$count++;
			}

			$dte = array_replace_recursive($dte, array(
				'Referencia' => $DteReferencia
				)
			);
		}
		
		if (!empty($id_dte)) {
			# Obtener DTE interno por id
			$dteInterno = $this->Orden->Dte->find('first', array('conditions' => array('id' => $id_dte)));
		}else{
			# Obtener último DTE guardado
			$dteInterno = $this->Orden->Dte->find('first', array('order' => array('id' => 'DESC')));
		}
		
		// crear cliente
		$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
		$LibreDTE->setSSL(false, false); ///< segundo parámetro =false desactiva verificación de SSL

		// crear DTE temporal
		$emitir = $LibreDTE->post('/dte/documentos/emitir', $dte);
		if ($emitir['status']['code'] != 200) {

			# Guardamos el estado
		    $dteInterno['Dte']['estado'] = 'dte_temporal_no_emitido';
		    $this->Orden->Dte->save($dteInterno);

		    # Mensaje de retorno
		    throw new Exception("Error al generar el DTE temporal: " . $emitir['body'], $emitir['status']['code']);
		    
		}else{

			# Guardamos el estado
			$dteInterno['Dte']['estado'] = 'dte_temporal_emitido';
			$dteInterno['Dte']['dte_temporal'] = $emitir['body']['codigo'];
			$dteInterno['Dte']['emisor'] = $emitir['body']['emisor'];
			$dteInterno['Dte']['receptor'] = $emitir['body']['receptor'];
			$this->Orden->Dte->save($dteInterno);

			# Mensaje de retorno
			# throw new Exception("DTE temporal Emitido.", $emitir['status']['code']);
		}

		// crear DTE real
		$generar = $LibreDTE->post('/dte/documentos/generar', $emitir['body']);
		
		if ($generar['status']['code']!=200) {

		    # Guardamos el estado
		    $dteInterno['Dte']['estado'] = 'dte_real_no_emitido';
		    $this->Orden->Dte->save($dteInterno);

		    # Mensaje de retorno
		    throw new Exception("Error al generar el DTE Real: " . $generar['body'], $generar['status']['code']);
		    
		}else{

			# Registramos los datos retornados por Libre DTE
			$dteInterno['Dte']['estado'] 			= 'dte_real_emitido';
			$dteInterno['Dte']['emisor'] 			= $generar['body']['emisor'];
			$dteInterno['Dte']['folio'] 			= $generar['body']['folio'];
			$dteInterno['Dte']['certificacion'] 	= $generar['body']['certificacion'];
			$dteInterno['Dte']['tasa'] 				= !empty($generar['body']['tasa']) ? $generar['body']['tasa'] : '';;
			$dteInterno['Dte']['fecha'] 			= $generar['body']['fecha'];
			$dteInterno['Dte']['sucursal_sii'] 		= !empty($generar['body']['sucursal_sii']) ? $generar['body']['sucursal_sii'] : '';
			$dteInterno['Dte']['receptor'] 			= $generar['body']['receptor'];
			$dteInterno['Dte']['exento'] 			= !empty($generar['body']['exento']) ? $generar['body']['exento'] : '';
			$dteInterno['Dte']['neto'] 				= !empty($generar['body']['neto']) ? $generar['body']['neto'] : '';
			$dteInterno['Dte']['iva'] 				= !empty($generar['body']['iva']) ? $generar['body']['iva'] : '';
			$dteInterno['Dte']['total'] 			= $generar['body']['total'];
			$dteInterno['Dte']['usuario'] 			= $generar['body']['usuario'];
			$dteInterno['Dte']['track_id'] 			= !empty($generar['body']['track_id']) ? $generar['body']['track_id'] : '';
			$dteInterno['Dte']['revision_estado'] 	= !empty($generar['body']['revision_estado']) ? $generar['body']['revision_estado'] : '';
			$dteInterno['Dte']['revision_detalle'] 	= !empty($generar['body']['revision_detalle']) ? $generar['body']['revision_detalle'] : '';

			$this->Orden->Dte->save($dteInterno);

			# Mensaje de retorno
			throw new Exception("DTE generado con éxito.", $emitir['status']['code']);
		}

		return;
	}


	/**
	 * Generar un DTE real desde un DTE temporal
	 * @param 		int 	$id_dte 	Identificador del DTE interno
	 */
	public function generarDteRealDesdeTemporal($id_dte)
	{
		// datos a utilizar
		$url = 'https://libredte.cl';
		$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

		$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
		$LibreDTE->setSSL(false, false);

		# Dte temporal
		$dte_tmp = $this->Orden->Dte->find('first', array('conditions' => array('Dte.id' => $id_dte)));

		if (!empty($dte_tmp) && !empty($dte_tmp['Dte']['dte_temporal'])) {
			
			if ($dte_tmp['Dte']['estado'] == 'dte_temporal_emitido' || $dte_tmp['Dte']['estado'] == 'dte_real_no_emitido') {
				
				$data = array(
					'emisor' => $dte_tmp['Dte']['emisor'],
					'receptor' => $dte_tmp['Dte']['receptor'],
					'dte' => $dte_tmp['Dte']['tipo_documento'],
					'codigo' => $dte_tmp['Dte']['dte_temporal']
				);

				// crear DTE real
				$generar = $LibreDTE->post('/dte/documentos/generar', $data);
				
				if ($generar['status']['code']!=200) {

				    # Guardamos el estado
				    $dte_tmp['Dte']['estado'] = 'dte_real_no_emitido';
				    $this->Orden->Dte->save($dte_tmp);

				    # Mensaje de retorno
				    throw new Exception("Error al generar el DTE Real: " . $generar['body'], $generar['status']['code']);
				    
				}else{

					# Registramos los datos retornados por Libre DTE
					$dte_tmp['Dte']['estado'] 			= 'dte_real_emitido';
					$dte_tmp['Dte']['emisor'] 			= $generar['body']['emisor'];
					$dte_tmp['Dte']['folio'] 			= $generar['body']['folio'];
					$dte_tmp['Dte']['certificacion'] 	= $generar['body']['certificacion'];
					$dte_tmp['Dte']['tasa'] 				= !empty($generar['body']['tasa']) ? $generar['body']['tasa'] : '';;
					$dte_tmp['Dte']['fecha'] 			= $generar['body']['fecha'];
					$dte_tmp['Dte']['sucursal_sii'] 		= !empty($generar['body']['sucursal_sii']) ? $generar['body']['sucursal_sii'] : '';
					$dte_tmp['Dte']['receptor'] 			= $generar['body']['receptor'];
					$dte_tmp['Dte']['exento'] 			= !empty($generar['body']['exento']) ? $generar['body']['exento'] : '';
					$dte_tmp['Dte']['neto'] 				= !empty($generar['body']['neto']) ? $generar['body']['neto'] : '';
					$dte_tmp['Dte']['iva'] 				= !empty($generar['body']['iva']) ? $generar['body']['iva'] : '';
					$dte_tmp['Dte']['total'] 			= $generar['body']['total'];
					$dte_tmp['Dte']['usuario'] 			= $generar['body']['usuario'];
					$dte_tmp['Dte']['track_id'] 			= !empty($generar['body']['track_id']) ? $generar['body']['track_id'] : '';
					$dte_tmp['Dte']['revision_estado'] 	= !empty($generar['body']['revision_estado']) ? $generar['body']['revision_estado'] : '';
					$dte_tmp['Dte']['revision_detalle'] 	= !empty($generar['body']['revision_detalle']) ? $generar['body']['revision_detalle'] : '';

					$this->Orden->Dte->save($dte_tmp);

					# Mensaje de retorno
					throw new Exception("DTE generado con éxito.", $generar['status']['code']);
				}
			}
		}

		# Mensaje de retorno
		throw new Exception("Error al generar el DTE Real. No estan todos los campos completos.", 402);
	}


	/**
	 * Generar el PDF desde un DTE real emitido
	 * @param 		int 		$id_orden 	Identificador de la Orden de compra
	 * @param 		int 		$id_dte 	Identificador del DTE interno
	 * @param 		int 		$tipo_dte 	Tipo de DTE
	 * @param 		string 		$folio 		Folio del DTE real retornado desde el SII
	 * @param 		string 		$emisor 	Rut del emisor sin digito verificador
	 */
	public function generarPDFDteEmitido($id_orden = '', $id_dte = '', $tipo_dte = '', $folio = '', $emisor = '')
	{
		if (!empty($tipo_dte) && !empty($folio) && !empty($emisor)) {

			$url = 'https://libredte.cl';
			$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

			$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
			$LibreDTE->setSSL(false, false);

			# Generar PDF
			$generar_pdf = $LibreDTE->get('/dte/dte_emitidos/pdf/'.$tipo_dte.'/'.$folio.'/'.$emisor);
			
			if ($generar_pdf['status']['code'] != 200) {
			    throw new Exception("No se pudo generar el PDF.");
			}

		 	if (!empty($id_orden) && !empty($id_dte)) {
		 		# Ruta para el nuevo PDF
		 		$rutaAbsoluta = APP . 'webroot' . DS. 'Dte' . DS . $id_orden . DS . $id_dte . DS;

		 		# Creamos la ruta absoluta
		 		if( !mkdir($rutaAbsoluta, 0777, true) ) {
		 			throw new Exception("El PDF ya fue generado.", 201);
		 		}

		 		$rutaPdf = 'Dte' . DS . $id_orden . DS . $id_dte . DS;
		 		$archivoPdf = 'documento-' . date('Y-m-d') . '.pdf';

		 		$rutaCompleta = $rutaAbsoluta . $archivoPdf;

		 		# Guardar PDF
				if (file_put_contents($rutaCompleta, $generar_pdf['body']) == E_WARNING) {
					throw new Exception("El PDF ya fue generado.", 201);
				}else{
					# Guardamos en DB
					ClassRegistry::init('Dte')->id = $id_dte;
					if (!ClassRegistry::init('Dte')->saveField('pdf', $archivoPdf)) {
						throw new Exception("No se logró guardar de Pdf en nuestros registros.", 401);
					}
				}

		 	}
			
		}else{
			throw new Exception("No es posible generar el PDF. El DTE Real no ha sido creado.", 402);
		}
	}


	/**
	 * Consultar estado de un DTE emitido a libredte
	 * @param 		int 		$rut 		Rut sin punto ni dv
	 * @param 		int 		$dte 		Tipo de documento
	 * @param 		int 		$folio 		Folio del DTE
	 * @param 		string 		$fecha 		Fecha de emisión del DTE
	 * @param 		int 		$total 		Monto total del DTE
	 * @param 		bool 		$getXML 	Semáforo que define si necesitamos el XML o no 	
	 */
	public function consultarDteLibreDte($rut = 0, $dte = 0, $folio = 0, $fecha = '', $total = 0, $getXML = 0)
	{
		# datos a utilizar
		$url = 'https://libredte.cl';
		$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

		# crear cliente
		$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
		$LibreDTE->setSSL(false, false);
		
		# obtener el PDF del DTE
		$datos = [
		    'emisor' => $rut,
		    'dte' => $dte,
		    'folio' => $folio,
		    'fecha' => $fecha,
		    'total' => $total,
		];

		$consultar = $LibreDTE->post('/dte/dte_emitidos/consultar?getXML='.$getXML, $datos);
		if ($consultar['status']['code']!=200) {
		    throw new Exception('Ocurrió un error al obtener el DTE desde el SII', 400);
		}

		if ($consultar['body']['anulado'] || $consultar['body']['iva_fuera_plazo']) {
			throw new Exception('Este DTE ha sido anulado por el SII o el IVA se encentra fuera de plazo. Estado de la revisión:' . $consultar['body']['revision_estado'] , 200);
		}

		return;
	}


	public function consultarDteSii($dte, $folio, $emisor)
	{
		# datos a utilizar
		$url = 'https://libredte.cl';
		$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

		# crear cliente
		$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
		$LibreDTE->setSSL(false, false);

		$res = array(
			'estado' => '',
			'detalle' => ''
			);

		$consultar = $LibreDTE->get('/dte/dte_emitidos/actualizar_estado/'.$dte.'/'.$folio.'/'.$emisor);
		if ($consultar['status']['code']!=200) {
			$res = array(
				'estado' => 'Sin información',
				'detalle' => 'No se obtuvo información desde el SII para este DTE'
				);
		}else{
			$res = array(
				'estado' => $consultar['body']['revision_estado'],
				'detalle' => $consultar['body']['revision_detalle']
				);
		}

		return $res;
	}


	/**
	 * Retorna una lista ordenada de los documentos autorizados en LibreDte
	 * @param 		int 		$rut_contribuyente 		Rut de la empresa
	 * @param 		bool 		$ajax 					Define el formato de la respuesta
	 * @return 		json o array 
	 */
	public function dtePermitidos($rut_contribuyente, $ajax = false)
	{
		// datos a utilizar
		$url = 'https://libredte.cl';
		$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

		// crear cliente
		$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
		$LibreDTE->setSSL(false, false); ///< segundo parámetro =false desactiva verificación de SSL

		# Obtenemos información del contribuyente
		$contribuyente = $LibreDTE->get('/dte/contribuyentes/config/'.$rut_contribuyente);
		
		if ($contribuyente['status']['code'] == 200) {
			
			$newArray = array();	

			foreach ($contribuyente['body']['documentos_autorizados'] as $k => $documento) {
				$newArray[$documento['codigo']] = $documento['tipo'];
			}

			if ($ajax) {
				echo json_encode($newArray['body']);
				exit;
			}
			
			return $newArray;
		}

		return;
	}


	/**
	 * Método encargado de enviar el DTE a el o los email asignados.
	 */
	public function admin_enviarDteViaEmail()
	{	
		if ($this->request->is('put')
			&& !empty($this->request->data['Orden']['id_orden']) 
			&& !empty($this->request->data['Orden']['dte'])
			&& !empty($this->request->data['Orden']['folio'])
			&& !empty($this->request->data['Orden']['emisor'])
			&& !empty($this->request->data['Orden']['asunto'])
			&& !empty($this->request->data['Orden']['mensaje'])
			&& !empty($this->request->data['Orden']['emails'])) {

			$emails = explode(',', trim($this->request->data['Orden']['emails']));

			# Esquema para datos
			$datos = array(
				'emails' => $emails,
				'asunto' => $this->request->data['Orden']['asunto'],
				'mensaje' => $this->request->data['Orden']['mensaje'],
				'pdf' => true,
				'cedible' => true,
				'papelContinuo' => false
			);

			// datos a utilizar
			$url = 'https://libredte.cl';
			$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

			// crear cliente
			$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
			$LibreDTE->setSSL(false, false); ///< segundo parámetro =false desactiva verificación de SSL
			
			$enviar = $LibreDTE->post('/dte/dte_emitidos/enviar_email/'.$this->request->data['Orden']['dte'].'/'.$this->request->data['Orden']['folio'].'/'.$this->request->data['Orden']['emisor'], $datos);
			
			if ($enviar['status']['code'] == 200) {
				$this->Session->setFlash('Correo enviado existosamente al cliente.', null, array(), 'success');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'index'));
			}else{
				$this->Session->setFlash('Error al enviar el email al cliente. Error:' . $enviar['body'], null, array(), 'danger');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $this->request->data['Orden']['id_orden']));
			}	
		}else{
			$this->Session->setFlash('Error al enviar el email. Existen campos no válidos.' , null, array(), 'warning');
			$this->redirect(array('controller' => 'ordenes', 'action' => 'editar', $this->request->data['Orden']['id_dte'], $this->request->data['Orden']['id_orden']));
		}
	} 


	public function otroEnvio(){
		// datos a utilizar
		$url = 'https://libredte.cl';
		$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

		$emisor = $this->request->data['Orden']['emisor'];
		$dte = $this->request->data['Orden']['dte'];
		$folio = $this->request->data['Orden']['folio'];
		$datos = [
		    'emails' => ['cristian.rojas@nodriza.cl'],
		    'asunto' => 'Envío de factura',
		    'mensaje' => 'Esta es su factura',
		    'pdf' => true,
		    'cedible' => true,
		    'papelContinuo' => false,
		];
		debug($emisor);
		debug($dte);
		debug($folio);
		print_r($datos);
		exit;
		// crear cliente
		$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
		$LibreDTE->setSSL(false, false);
		// enviar email
		$envio = $LibreDTE->post('/dte/dte_emitidos/enviar_email/'.$dte.'/'.$folio.'/'.$emisor, $datos);
		if ($envio['status']['code']!=200) {
		    die('Error al enviar el correo del DTE emitido: '.$envio['body']."\n");
		}
		echo $envio['body']."\n";
	} 



	/**
	 * Método encargado de eliminar un dte temporal desde Libre DTE
	 * Además de enviar el DTE de la BD interna.
	 * @param 	$id_dte 	int 	Identificador del DTE
	 * @param 	$id_order 	int 	Identificador de la orden
	 */
	public function admin_eliminarDteTemporal($id_dte, $id_order)
	{	
		if ( ! $this->Orden->Dte->exists($id_dte) )
		{
			$this->Session->setFlash('Dte no existe.' , null, array(), 'warning');
			$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', 
				$id_order));
		}

		if ($this->request->is('post')) {
			
			$dte = $this->Orden->Dte->find('first', array('conditions' => array('Dte.id' => $id_dte)));
			
			if ( !empty($dte['Dte']['receptor']) && !empty($dte['Dte']['tipo_documento']) && !empty($dte['Dte']['dte_temporal']) && !empty($dte['Dte']['emisor']) ) {
				
				// datos a utilizar
				$url = 'https://libredte.cl';
				$hash = '62hoFgnBkcOllRuV2FxtR2Mqd6m9EII0';

				// crear cliente
				$LibreDTE = new \sasco\LibreDTE\SDK\LibreDTE($hash, $url);
				$LibreDTE->setSSL(false, false); ///< segundo parámetro =false desactiva verificación de SSL

				$eliminar = $LibreDTE->get('/dte/dte_tmps/eliminar/'.$dte['Dte']['receptor'].'/'.$dte['Dte']['tipo_documento'].'/'.$dte['Dte']['dte_temporal'].'/'.$dte['Dte']['emisor']);

				if ($eliminar['status']['code'] == 200) {
					# Borramos el DTE
					if ( $this->Orden->Dte->delete($id_dte) )
					{
						$this->Session->setFlash('DTE eliminado correctamente de Libre DTE.', null, array(), 'success');
						$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_order));
					}else{
						$this->Session->setFlash('No se logró eliminar el DTE. Intentelo nuevamente.', null, array(), 'danger');
						$this->redirect(array('controller' => 'ordenes', 'action' => 'editar', $id_dte, $id_order));
					}
				}else{
					if ($eliminar['body'] == 'No existe el DTE temporal solicitado' && $this->Orden->Dte->delete($id_dte)) {
						$this->Session->setFlash('DTE eliminado correctamente de Libre DTE.', null, array(), 'success');
						$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_order));
					}else{
						$this->Session->setFlash($eliminar['body'] . '. Intentelo nuevamente.', null, array(), 'danger');
						$this->redirect(array('controller' => 'ordenes', 'action' => 'editar', $id_dte, $id_order));
					}
					
				}
			}else{
				# Borramos el DTE
				if ( $this->Orden->Dte->delete($id_dte) )
				{
					$this->Session->setFlash('DTE eliminado correctamente.', null, array(), 'success');
					$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_order));
				}else{
					$this->Session->setFlash('No se logró eliminar el DTE. Intentelo nuevamente.', null, array(), 'danger');
					$this->redirect(array('controller' => 'ordenes', 'action' => 'editar', $id_dte, $id_order));
				}
			}

		}	
	}


}
