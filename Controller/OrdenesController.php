<?php

App::uses('AppController', 'Controller', 'Chilexpress');

App::import('Vendor', 'LibreDTE', array('file' => 'LibreDte/autoload.php'));
App::import('Vendor', 'LibreDTE', array('file' => 'LibreDte/sasco/libredte-sdk-php/sdk/LibreDTE.php'));

App::import('Vendor', 'Mercadolibre', array('file' => 'Meli/meli.php'));
App::import('Controller', 'Ventas');

require_once (__DIR__ . '/../Vendor/PSWebServiceLibrary/PSWebServiceLibrary.php');

class OrdenesController extends AppController
{		
	public $name = 'Ordenes';    
    public $uses = array('Orden');

    public $components = array(
    	'Chilexpress.GeoReferencia',
    	'Toolmania',
    	'MeliMarketplace',
    	'LibreDte'
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
			$this->Session->setFlash('DTE invalidado con éxito.', null, array(), 'success');
		}else{
			$this->Session->setFlash('No fue posible invalidar el DTE.', null, array(), 'danger');
		}

		$this->redirect(array('controller' => 'ventas', 'action' => 'view', $id_orden));
	}


	/**
	 * Verifica si una orden tiene DTE emitido correctamente y que no esté anulado
	 * @return bool 
	 */
	public function unico($tipo = '')
	{
		$dts = ClassRegistry::init('Dte')->find('count', array(
			'conditions' => array(
				'Dte.venta_id' => $this->request->data['Dte']['venta_id'],
				'Dte.estado' => 'dte_real_emitido',
				'Dte.tipo_documento' => $tipo,
				'Dte.invalidado' => 0
			)
		));
		
		if ($dts > 0) {
			return false;
		}

		return true;
		
	}


	public function unicoDteValido($id_venta)
	{
		$dts = ClassRegistry::init('Dte')->find('count', array(
			'conditions' => array(
				'Dte.venta_id' => $id_venta,
				'Dte.estado' => 'dte_real_emitido',
				'Dte.tipo_documento' => array(33, 39), // Boletas o facturas
				'Dte.invalidado' => 0
			)
		));
		
		if ($dts > 0) {
			return false;
		}

		return true;
	}


	public function admin_delete_dte($id_dte = '', $id_orden = '')
	{	

		$this->Orden->Dte->id = $id_dte;

		if ($this->Orden->Dte->delete()) {

			$this->admin_eliminarDteTemporal($id_dte, $id_orden);

			$this->Session->setFlash('DTE eliminado con éxito.', null, array(), 'success');
		}else{
			$this->Session->setFlash('No fue posible elimnar el DTE.', null, array(), 'danger');
		}

		$this->redirect(array('controller' => 'ventas', 'action' => 'view', $id_orden));
	}


	public function admin_generar($id_orden = '', $id_dte = '')
	{
		$this->verificarTienda();

		$this->loadModel('Venta');

		if ( ! $this->Venta->exists($id_orden) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Modelos que requieren agregar configuración
		#$this->cambiarDatasource(array('Orden', 'OrdenEstado', 'OrdenDetalle', 'Lang', 'Cliente', 'ClienteHilo', 'ClienteMensaje', 'Empleado', 'CustomUserdata', 'CustomField', 'CustomFieldLang'));

		if ( $this->request->is('post') || $this->request->is('put') )
		{	

			if (!$this->unicoDteValido($id_orden)) {
				$this->Session->setFlash('¡ERROR! No puedes generar 2 documentos válidos de venta. Debes solicitar una Nota de crédito.' , null, array(), 'danger');
				$this->redirect(array('controller' => 'ventas', 'action' => 'view', $id_orden));
			}

			# Administrador
			$this->request->data['Dte']['administrador_id'] = $this->Session->read('Auth.Administrador.id');

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
				$this->request->data['DteDetalle'][$cantidadItem]['VlrCodigo'] = "COD-Trns";
				$this->request->data['DteDetalle'][$cantidadItem]['NmbItem'] = "Transporte";

				# Para boleta se envia el valor bruto y así evitar que el monto aumente o disminuya por el calculo de iva
				if ($this->request->data['Dte']['tipo_documento'] == 39) {
					$this->request->data['Detalle'][$cantidadItem]['PrcItem'] = round($this->request->data['Dte']['Transporte']);
					$this->request->data['DteDetalle'][$cantidadItem]['PrcItem'] = round($this->request->data['Dte']['Transporte']);
				}else{
					$this->request->data['Detalle'][$cantidadItem]['PrcItem'] = $this->precio_neto($this->request->data['Dte']['Transporte']);
					$this->request->data['DteDetalle'][$cantidadItem]['PrcItem'] = $this->precio_neto($this->request->data['Dte']['Transporte']);
				}
				$this->request->data['Detalle'][$cantidadItem]['QtyItem'] = 1;
				$this->request->data['DteDetalle'][$cantidadItem]['QtyItem'] = 1;
			}
				
			# Si el DTE es boleta enviamos los precios Brutos de los items
			if ($this->request->data['Dte']['tipo_documento'] == 39) {

				# Se agrega un rut por defecto
				$this->request->data['Dte']['rut_receptor'] = '66666666-6';

				foreach ($this->request->data['Detalle'] as $k => $item) {

					# Precio de transporte viene Bruto
					if ($item['NmbItem'] != 'Transporte') {
						$this->request->data['Detalle'][$k]['PrcItem'] = $this->precio_bruto($item['PrcItem']);
					}
					
				}

				// Descuento Bruto en boletas
				if ($this->request->data['DscRcgGlobal']['ValorDR'] > 0) {
					$this->request->data['DscRcgGlobal']['ValorDR'] = $this->request->data['editDiscount'];	
				}
			}

			// Se quitan las referencia que no tienen folio.
			/*if (isset($this->request->data['DteReferencia'])) {
				$this->request->data['DteReferencia'] = $this->clear($this->request->data['DteReferencia'], 'folio');	
			}*/
			
			# Limpiar detalle Dte si corresponde
			if (isset($this->request->data['Dte']['id'])) {
				ClassRegistry::init('DteDetalle')->deleteAll(array(
					'DteDetalle.dte_id' => $this->request->data['Dte']['id']
				), false);
			}
			
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

				if (!isset($this->request->data['Dte']['id'])) {
					$id_dte = $this->Orden->Dte->find('first', array(
						'conditions' => array('Dte.venta_id' => $id_orden),
						'order' => array('Dte.id' => 'DESC')
						)
					);	
				}else{
					$id_dte = $this->Orden->Dte->find('first', array(
						'conditions' => array('Dte.id' => $this->request->data['Dte']['id']),
						'order' => array('Dte.id' => 'DESC')
						)
					);
				}

				if (!empty($id_dte)) {
					$this->redirect(array('controller' => 'ordenes', 'action' => 'editar', $id_dte['Dte']['id'], $id_orden));
				}

				$this->redirect(array('controller' => 'ventas', 'action' => 'view', $id_orden));

			}else{
				$this->Session->setFlash('Error al guardar la información en la base de detos local. Intente nuevamente.' , null, array(), 'warning');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_orden));
			}

		}else{

			$venta = $this->request->data = $this->Venta->find(
				'first',
				array(
					'conditions' => array(
						'Venta.id' => $id_orden
					),
					'contain' => array(
						'VentaDetalle' => array(
							'VentaDetalleProducto' => array(
								'fields' => array(
									'VentaDetalleProducto.id', 'VentaDetalleProducto.nombre'
								)
							),
							'conditions' => array(
								'VentaDetalle.activo' => 1
							),
							'fields' => array(
								'VentaDetalle.id', 'VentaDetalle.venta_detalle_producto_id', 'VentaDetalle.precio', 'VentaDetalle.cantidad', 'VentaDetalle.venta_id'
							)
						),
						'VentaEstado' => array(
							'VentaEstadoCategoria' => array(
								'fields' => array(
									'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
								)
							),
							'fields' => array(
								'VentaEstado.id', 'VentaEstado.nombre', 'VentaEstado.venta_estado_categoria_id'
							)
						),
						'Tienda' => array(
							'fields' => array(
								'Tienda.id', 'Tienda.nombre', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop'
							)
						),
						'Marketplace' => array(
							'fields' => array(
								'Marketplace.id', 'Marketplace.nombre', 'Marketplace.marketplace_tipo_id',
								'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key',
								'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token'
							)
						),
						'MedioPago' => array(
							'fields' => array(
								'MedioPago.id', 'MedioPago.nombre'
							)
						),
						'VentaCliente' => array(
							'fields' => array(
								'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono', 'VentaCliente.created'
							)
						),
						'Dte' => array(
							'conditions' => array(
								'Dte.id' => $id_dte
							),
							'DteReferencia' => array(
								'fields' => array(
									'DteReferencia.id', 'DteReferencia.dte_id', 'DteReferencia.dte_referencia', 'DteReferencia.folio', 'DteReferencia.fecha',
									'DteReferencia.tipo_documento', 'DteReferencia.razon'
								)
							),
							'DteDetalle' => array(
								'fields' => array(
									'DteDetalle.*'
								)
							),
							'fields' => array(
								'Dte.id', 'Dte.folio', 'Dte.tipo_documento', 'Dte.rut_receptor', 'Dte.razon_social_receptor', 'Dte.giro_receptor', 'Dte.neto', 'Dte.iva',
								'Dte.total', 'Dte.fecha', 'Dte.estado'
							),
							'order' => 'Dte.fecha DESC'
						)
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo', 'Venta.descuento', 'Venta.costo_envio',
						'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id'
					)
				)
			);

			//carga de mensajes de la venta
			$venta['VentaMensaje'] = array();

			# si no tiene items se crea uno vacio para ser mostrado en el front
			if (empty($venta['VentaDetalle'])) {
				$venta['VentaDetalle'][] = array(
					'VentaDetalleProducto' => array(
						'id'     => null,
						'nombre' => null
					),
					'id'                        => null,
					'venta_detalle_producto_id' => null,
					'precio'                    => null, 
					'cantidad'                  => null,
					'venta_id'                  => $id_orden
				);
			}
			
			//----------------------------------------------------------------------------------------------------
			//carga de mensajes de prestashop
			if (empty($venta['Marketplace']['id'])) {

				$ConexionPrestashop = new PrestaShopWebservice($venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'], false);

				$venta['VentaMensaje'] = VentasController::prestashop_obtener_venta_mensajes($ConexionPrestashop, $venta['Venta']['id_externo']);

			}

			else {

				//----------------------------------------------------------------------------------------------------
				//carga de mensajes de mercado libre
				if ($venta['Marketplace']['marketplace_tipo_id'] == 2) {
					
					$this->MeliMarketplace->crearCliente( $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token'] );

					$mensajes = $this->MeliMarketplace->mercadolibre_obtener_mensajes($venta['Marketplace']['access_token'], $venta['Venta']['id']);

					foreach ($mensajes as $mensaje) {

						$data = array();
						$data['mensaje'] = $mensaje['text']['plain'];
						$data['fecha'] = CakeTime::format($mensaje['date'], '%d-%m-%Y %H:%M:%S');
						$data['asunto'] = $mensaje['subject'];

						$venta['VentaMensaje'][] = $data;
					}

				}

			}

		}

		$documentos = array();

		if (empty($venta['Marketplace']['id'])) {

			# Datos de facturación para compras por Prestashop
			ToolmaniaComponent::$api_url = $this->Session->read('Tienda.apiurl_prestashop');
			//$webpay                      = $this->Toolmania->obtenerWebpayInfo($this->request->data['Orden']['id_cart'], $this->Session->read('Tienda.apikey_prestashop'));
			$documentos                  = $this->Toolmania->obtenerDocumento($venta['Venta']['id_externo'], null, $this->Session->read('Tienda.apikey_prestashop'));
			
			$this->request->data['Dte'] = array(
				'tipo_documento' => 39, # Boleta por defecto
			);
			
			if (!empty($documentos['content'])) {
				$this->request->data['Dte'] = array(
					'tipo_documento'        => ($documentos['content'][0]['boleta']) ? 39 : 33,
					'rut_receptor'          => $documentos['content'][0]['rut'],
					'razon_social_receptor' => $documentos['content'][0]['empresa'],
					'giro_receptor'         => $documentos['content'][0]['giro'],
					'direccion_receptor'    => $documentos['content'][0]['calle']
				);

				if ($this->request->data['Dte']['tipo_documento'] == 33) {
					// Obtenemos la información del contribuyente desde el SII
					$info = $this->admin_getContribuyenteInfo($this->rutSinDv($documentos['content'][0]['rut']));
					
					// Agregamos comuna
					if (isset($info['comuna_glosa'])) {
						$this->request->data['Dte']['comuna_receptor'] = $info['comuna_glosa'];
					}

					if (empty($documentos['content'][0]['empresa']) && isset($info['razon_social'])) {
						$this->request->data['Dte']['razon_social_receptor'] = $info['razon_social'];
					}

					if (empty($documentos['content'][0]['giro']) && isset($info['giro'])) {
						$this->request->data['Dte']['giro_receptor'] = $info['giro'];
					}	
				}elseif (isset($documentos['content'][0]['rut'])){
					
					# Guardamos el rut de la persona
					ClassRegistry::init('VentaCliente')->id = $this->request->data['VentaCliente']['id'];
					ClassRegistry::init('VentaCliente')->saveField('rut', $documentos['content'][0]['rut']);

					$this->request->data['VentaCliente']['rut'] = $documentos['content'][0]['rut'];
				}
			}
		}
		
		// cliente para hacer consultas a la api de libredte
		$this->LibreDte->crearCliente($this->Session->read('Tienda.facturacion_apikey'));

		# Array de tipos de documentos
		$tipoDocumento = $this->LibreDte->dtePermitidos($this->rutSinDv($this->Session->read('Tienda.rut')));

		# Mostramos solo los permitidos para el usuario
		$rol = ClassRegistry::init('Rol')->find('first', array('conditions' => array('Rol.id' => $this->Auth->user('rol_id')), 'fields' => array('permitir_boleta', 'permitir_factura', 'permitir_ndc', 'permitir_ndd', 'permitir_gdd', 'permitir_fc')));

		# Quitamos boleta
		if (!$rol['Rol']['permitir_boleta'])
			unset($tipoDocumento['39']);

		# Quitamos factura
		if (!$rol['Rol']['permitir_factura'])
			unset($tipoDocumento['33']);

		# Quitamos nota de crédito
		if (!$rol['Rol']['permitir_ndc'])
			unset($tipoDocumento['61']);

		# Quitamos nota de débito
		if (!$rol['Rol']['permitir_ndd'])
			unset($tipoDocumento['56']);

		# Quitamos guia de despacho
		if (!$rol['Rol']['permitir_gdd'])
			unset($tipoDocumento['52']);

		# Quitamos factura de compra
		if (!$rol['Rol']['permitir_fc'])
			unset($tipoDocumento['46']);

		# Array de comunas actualizadas
		$comunasResult = ClassRegistry::init('Comuna')->find('list', array('order' => array('nombre' => 'ASC')));

		foreach ($comunasResult as $id => $comuna) {
			$comunas[$comuna] = $comuna;
		}

		# Tipos de traslados
		$traslados = $this->LibreDte->tipoTraslado;

		# Códigos d referencia Libre DTE
		$codigoReferencia = $this->LibreDte->codigoReferencia;

		# Medio de pago
		$medioDePago = $this->LibreDte->medioDePago;

		# DTE´s para referenciar
		$dteEmitidos = $this->Venta->Dte->find(
			'list',
			array(
				'conditions' => array(
					'Dte.venta_id' => $id_orden,
					'Dte.estado' => 'dte_real_emitido'
				)
			)
		);
		
		BreadcrumbComponent::add('Listado de ventas', '/ventas');
		BreadcrumbComponent::add('Venta #' . $id_orden, '/ventas/view/'.$id_orden);
		BreadcrumbComponent::add('Generar Dte ');
		
		$this->set(compact('venta', 'comunas', 'tipoDocumento', 'traslados', 'dteEmitidos', 'codigoReferencia', 'medioDePago', 'documentos'));

	}

	public function admin_editar($id_dte = '', $id_orden = '')
	{
		$this->verificarTienda();
		
		if ( ! $this->Orden->Dte->exists($id_dte) )
		{
			$this->Session->setFlash('No existe el dte seleccionado.', null, array(), 'danger');
			$this->redirect(array('controller' => 'ventas', 'action' => 'index'));
		}

		$dte = $this->request->data = $this->Orden->Dte->find(
			'first',
			array(
				'conditions' => array(
					'Dte.id' => $id_dte
				),
				'fields' => array(
					'Dte.*'
				)
			)
		);
	
		$this->LibreDte->crearCliente($this->Session->read('Tienda.facturacion_apikey'));

		# Consultar por DTE Emitido
		if (!empty($this->request->data['Dte']) && $this->request->data['Dte']['estado'] == 'dte_real_emitido' ) {
			try {
				$this->LibreDte->consultarDteLibreDte($this->request->data['Dte']['emisor'], $this->request->data['Dte']['tipo_documento'], $this->request->data['Dte']['folio'], $this->request->data['Dte']['fecha'], $this->request->data['Dte']['total']);
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
				$this->LibreDte->generarPDFDteEmitido($id_orden, $id_dte, $this->request->data['Dte']['tipo_documento'], $this->request->data['Dte']['folio'], $this->request->data['Dte']['emisor'] );
			} catch (Exception $e) {
				if($e->getCode() < 300) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'success');
				}

				if ($e->getCode() > 300) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'danger');
				}
			}

			# Se redirecciona a vista
			$this->redirect(array('controller' => 'ordenes', 'action' => 'view', $id_dte, $id_orden));

		}else if(!empty($this->request->data['Dte']['pdf']) && $this->request->data['Dte']['estado'] == 'dte_real_emitido'){
			$this->redirect(array('controller' => 'ordenes', 'action' => 'view', $id_dte, $id_orden));
		}

		// Eliminamos DTE
		if ($this->request->data['Dte']['estado'] != 'dte_real_emitido') {
			$this->admin_eliminarDteTemporal($id_dte, $id_orden);
		}
		
		$this->Session->setFlash('El DTE no ha sido emitido correctamente. vuelva a intentarlo.', null, array(), 'warning');
		$this->redirect(array('controller' => 'ordenes', 'action' => 'generar', $id_orden, $id_dte));

	}


	public function admin_view($id_dte = '', $id_venta = '') {

		$this->verificarTienda();

		if ( ! ClassRegistry::init('Venta')->exists($id_venta) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('controller' => 'ventas', 'action' => 'index', $id_venta));
		}

		# Modelos que requieren agregar configuración
		#$this->cambiarDatasource(array('Orden', 'OrdenEstado', 'OrdenDetalle', 'Lang', 'Cliente', 'ClienteHilo', 'ClienteMensaje', 'Empleado'));

		if ( $this->request->is('post') || $this->request->is('put') )
		{	

			
		}
		else
		{
			$venta = $this->request->data = ClassRegistry::init('Venta')->find(
				'first',
				array(
					'conditions' => array(
						'Venta.id' => $id_venta
					),
					'contain' => array(
						'VentaDetalle' => array(
							'VentaDetalleProducto' => array(
								'fields' => array(
									'VentaDetalleProducto.id', 'VentaDetalleProducto.nombre'
								)
							),
							'conditions' => array(
								'VentaDetalle.activo' => 1
							),
							'fields' => array(
								'VentaDetalle.id', 'VentaDetalle.venta_detalle_producto_id', 'VentaDetalle.precio', 'VentaDetalle.cantidad', 'VentaDetalle.venta_id'
							)
						),
						'VentaEstado' => array(
							'VentaEstadoCategoria' => array(
								'fields' => array(
									'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
								)
							),
							'fields' => array(
								'VentaEstado.id', 'VentaEstado.venta_estado_categoria_id'
							)
						),
						'Tienda' => array(
							'fields' => array(
								'Tienda.id', 'Tienda.nombre', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop'
							)
						),
						'Marketplace' => array(
							'fields' => array(
								'Marketplace.id', 'Marketplace.nombre', 'Marketplace.marketplace_tipo_id',
								'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key',
								'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token'
							)
						),
						'MedioPago' => array(
							'fields' => array(
								'MedioPago.id', 'MedioPago.nombre'
							)
						),
						'VentaCliente' => array(
							'fields' => array(
								'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono', 'VentaCliente.created'
							)
						),
						'Dte' => array(
							'conditions' => array(
								'Dte.id' => $id_dte
							),
							'DteReferencia' => array(
								'fields' => array(
									'DteReferencia.id', 'DteReferencia.dte_id', 'DteReferencia.dte_referencia', 'DteReferencia.folio', 'DteReferencia.fecha',
									'DteReferencia.tipo_documento', 'DteReferencia.razon'
								)
							),
							'DteDetalle' => array(
								'fields' => array(
									'DteDetalle.*'
								)
							),
							'fields' => array(
								'Dte.*'
							),
							'order' => 'Dte.fecha DESC'
						)
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo', 'Venta.descuento', 'Venta.costo_envio',
						'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id'
					)
				)
			);

			//carga de mensajes de la venta
			$venta['VentaMensaje'] = array();
			
			//----------------------------------------------------------------------------------------------------
			//carga de mensajes de prestashop
			if (empty($venta['Marketplace']['id'])) {

				$ConexionPrestashop = new PrestaShopWebservice($venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'], false);

				$venta['VentaMensaje'] = VentasController::prestashop_obtener_venta_mensajes($ConexionPrestashop, $venta['Venta']['id_externo']);

			}

			else {

				//----------------------------------------------------------------------------------------------------
				//carga de mensajes de mercado libre
				if ($venta['Marketplace']['marketplace_tipo_id'] == 2) {
					
					$this->MeliMarketplace->crearCliente( $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token'] );

					$mensajes = $this->MeliMarketplace->mercadolibre_obtener_mensajes($venta['Marketplace']['access_token'], $venta['Venta']['id']);

					foreach ($mensajes as $mensaje) {

						$data = array();
						$data['mensaje'] = $mensaje['text']['plain'];
						$data['fecha'] = CakeTime::format($mensaje['date'], '%d-%m-%Y %H:%M:%S');
						$data['asunto'] = $mensaje['subject'];

						$venta['VentaMensaje'][] = $data;
					}

				}

			}
		}

		// Cliente para obtener información desde libredte
		$this->LibreDte->crearCliente($this->Session->read('Tienda.facturacion_apikey'));

		# Estado del dte Emitido en el SII
		if ($this->request->data['Dte'][0]['estado'] == 'dte_real_emitido' ) {
			$venta['Dte'][0]['estado_sii'] = $this->LibreDte->consultarDteSii($this->request->data['Dte'][0]['tipo_documento'], $this->request->data['Dte'][0]['folio'], $this->request->data['Dte'][0]['emisor']);
		}

		# Consultar por DTE Emitido
		if (!empty($this->request->data['Dte'][0]) && $this->request->data['Dte'][0]['estado'] == 'dte_real_emitido' ) {
			try {
				$this->LibreDte->consultarDteLibreDte($this->request->data['Dte'][0]['emisor'], $this->request->data['Dte'][0]['tipo_documento'], $this->request->data['Dte'][0]['folio'], $this->request->data['Dte'][0]['fecha'], $this->request->data['Dte'][0]['total']);
			} catch (Exception $e) {
				if ($e->getCode() == 400) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'danger');
				}else{
					$this->Session->setFlash($e->getMessage() , null, array(), 'warning');
				}
			}
		}	

		# Si no se ha generado el PDF de un documento emitido se intenta generar
		if (!empty($this->request->data['Dte']) && empty($this->request->data['Dte'][0]['pdf']) && !empty($this->request->data['Dte'][0]['folio']) ) {
			try {
				$this->LibreDte->generarPDFDteEmitido($id, $this->request->data['Dte'][0]['id'], $this->request->data['Dte'][0]['tipo_documento'], $this->request->data['Dte'][0]['folio'], $this->request->data['Dte'][0]['emisor'] );
			} catch (Exception $e) {
				if($e->getCode() < 300) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'success');
				}

				if ($e->getCode() > 300) {
					$this->Session->setFlash($e->getMessage() , null, array(), 'danger');
				}
			}
		}

		BreadcrumbComponent::add('Listado de ventas', '/ventas');
		BreadcrumbComponent::add('Venta #' . $id_venta, '/ventas/view/'.$id_venta);
		BreadcrumbComponent::add('Ver Dte ');
		
		$this->set(compact('venta'));
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
	 * Retorna los datos del rut del contribuyente consultado
	 * @param 		$rut_contribuyente 		int 		Rut a buscar sin dv
	 * @param 		$ajax 					bool 		Determina el tipo de retorno del la información
	 * @return 		array/json
	 */
	public function admin_getContribuyenteInfo($rut_contribuyente, $ajax = false) 
	{
		$this->LibreDte->crearCliente($this->Session->read('Tienda.facturacion_apikey'));
		
		$contribuyente = $this->LibreDte->obtenerContribuyente($rut_contribuyente);
		
		if ($ajax) {
			echo json_encode($contribuyente);
			exit;
		}

		return $contribuyente;

	}


	/**
	 * Generar un DTE desde un documento vacio
	 * @param 		int 	$id_dte 	Idntificador del DTE ya emitido
	 */
	public function generarDte($id_dte = '')
	{	
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
		if (!empty($this->request->data['Dte']['rut_receptor']) && $this->request->data['Dte']['rut_receptor'] != '66666666-6' ) {

			$rut = @number_format( substr ( $this->request->data['Dte']['rut_receptor'], 0 , -1 ) , 0, "", "") . '-' . substr ( $this->request->data['Dte']['rut_receptor'], strlen($this->request->data['Dte']['rut_receptor']) -1 , 1 );
			
			$dte['Encabezado'] = array_replace_recursive($dte['Encabezado'], array(
				'Receptor' => array(
					'RUTRecep' => $rut
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

			# si el descuento es 0 se elimina
			if ($this->request->data['DscRcgGlobal']['ValorDR'] == 0) {
				unset($dte['DscRcgGlobal']);
			}
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
			$count = 1;
			foreach ($this->request->data['DteReferencia'] as $i => $ref) {
				
				if (empty($ref['razon'])) {
					continue;
				}

				$DteReferencia[$count] = array(
					'NroLinRef' => $count,
					'TpoDocRef' => $ref['tipo_documento'],
					'FolioRef' => $ref['folio'],
					'FchRef' => $ref['fecha'],
					'CodRef' => $ref['codigo_referencia'],
					'RazonRef' => $ref['razon']
				);

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
		$this->LibreDte->crearCliente($this->Session->read('Tienda.facturacion_apikey'));

		// crear DTE temporal
		$dte_temporal = $this->LibreDte->crearDteTemporal($dte, $dteInterno);

		if (empty($dte_temporal)) {
			return;
		}

		// crear DTE real
		$generar = $this->LibreDte->crearDteReal($dte_temporal, $dteInterno);

		return;
	}


	/**
	 * Método encargado de enviar el DTE a el o los email asignados.
	 */
	public function admin_enviarDteViaEmail()
	{	
		if ($this->request->is('post')
			&& !empty($this->request->data['Orden']['venta_id']) 
			&& !empty($this->request->data['Orden']['dte'])
			&& !empty($this->request->data['Orden']['folio'])
			&& !empty($this->request->data['Orden']['emisor'])
			&& !empty($this->request->data['Orden']['asunto'])
			&& !empty($this->request->data['Orden']['mensaje'])
			&& !empty($this->request->data['Orden']['emails'])) {

			$emails = explode(',', trim($this->request->data['Orden']['emails']));

			$this->LibreDte->crearCliente($this->Session->read('Tienda.facturacion_apikey'));

			$enviar = $this->LibreDte->enviarDteEmail(
				$emails, 
				$this->request->data['Orden']['dte'], 
				$this->request->data['Orden']['folio'], 
				$this->request->data['Orden']['emisor'],
				$this->request->data['Orden']['asunto'],
				$this->request->data['Orden']['mensaje']);
			
			if ($enviar) {
				$this->Session->setFlash('Correo enviado existosamente al cliente.', null, array(), 'success');
				$this->redirect(array('controller' => 'ventas', 'action' => 'view', $this->request->data['Orden']['venta_id']));
			}else{
				$this->Session->setFlash('Error al enviar el email al cliente. Error:' . $enviar, null, array(), 'danger');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'view', $this->request->data['Orden']['id_dte'], $this->request->data['Orden']['venta_id']));
			}	
		}else{
			$this->Session->setFlash('Error al enviar el email. Existen campos no válidos.' , null, array(), 'warning');
			$this->redirect(array('controller' => 'ordenes', 'action' => 'view', $this->request->data['Orden']['id_dte'], $this->request->data['Orden']['venta_id']));
		}
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
			$this->Session->setFlash('Dte Temporal no existe.' , null, array(), 'warning');
			$this->redirect(array('controller' => 'ventas', 'action' => 'view', 
				$id_order));
		}

		if ($this->request->is('GET')) {
			
			$dte = $this->Orden->Dte->find('first', array('conditions' => array('Dte.id' => $id_dte)));
			
			if ( !empty($dte['Dte']['receptor']) && !empty($dte['Dte']['tipo_documento']) && !empty($dte['Dte']['dte_temporal']) && !empty($dte['Dte']['emisor']) ) {
				
				$this->LibreDte->crearCliente($this->Session->read('Tienda.facturacion_apikey'));
				$eliminar = $this->LibreDte->eliminarDteTemporal($dte['Dte']['receptor'], $dte['Dte']['tipo_documento'], $dte['Dte']['dte_temporal'], $dte['Dte']['emisor']);

				if (empty($eliminar)) {
					# Borramos el DTE
					if ( $this->Orden->Dte->delete($id_dte) )
					{
						$this->Session->setFlash('DTE eliminado correctamente de Libre DTE.', null, array(), 'success');
						$this->redirect(array('controller' => 'ventas', 'action' => 'view', $id_order));
					}else{
						$this->Session->setFlash('No se logró eliminar el DTE. Intentelo nuevamente.', null, array(), 'danger');
						$this->redirect(array('controller' => 'ventas', 'action' => 'editar', $id_dte, $id_order));
					}
				}else{
					if ($eliminar == 'No existe el DTE temporal solicitado' && $this->Orden->Dte->delete($id_dte)) {
						$this->Session->setFlash('DTE eliminado correctamente de Libre DTE.', null, array(), 'success');
						$this->redirect(array('controller' => 'ventas', 'action' => 'view', $id_order));
					}else{
						$this->Session->setFlash($eliminar . '. Intentelo nuevamente.', null, array(), 'danger');
						$this->redirect(array('controller' => 'ventas', 'action' => 'view', $id_order));
					}
					
				}
			}else{
				# Borramos el DTE
				if ( $this->Orden->Dte->delete($id_dte) )
				{
					$this->Session->setFlash('DTE no ha sido generado. Vuelva a intentarlo.', null, array(), 'danger');
					$this->redirect(array('controller' => 'ordenes', 'action' => 'generar', $id_order));
				}else{
					$this->Session->setFlash('No se logró eliminar el DTE. Intentelo nuevamente.', null, array(), 'warning');
					$this->redirect(array('controller' => 'ventas', 'action' => 'view', $id_order));
				}
				return;
			}

		}	
	}


}
