<?php
App::uses('AppController', 'Controller');

class OrdenTransportesController extends AppController
{	
	public $uses = array('Orden');
	public $helpers = array('Chilexpress.Chilexpress');
	public $components = array('RequestHandler');

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

	public function admin_index()
	{
		$this->verificarTienda();

		$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;
    	$categorias = array();

    	$textoBuscar = null;

		# Filtrado de ordenes por formulario
		if ( $this->request->is('post') ) {

			$this->filtrar('ordenes', 'index');

		}

		# Estados del pedidos
		$estados = $this->Orden->OrdenEstado->find('all', array('contain' => array('Lang')));

		# Se excluyen pedidos no pagados
		foreach ($estados as $ie => $estado) {
			if (!$estado['OrdenEstado']['paid']) {
				unset($estados[$ie]);
			}
		}

		$estadosId = Hash::extract($estados, '{n}.OrdenEstado.id_order_state');
		$estados   = Hash::extract($estados, '{n}.Lang.0.OrdenEstadoIdioma.name');

		$paginate = array_replace_recursive($paginate, array(
			'limit'      => 20,
			'contain'    => array('OrdenEstado' => array('Lang'), 'OrdenTransporte'),
			'conditions' => array('Orden.current_state' => $estadosId),
			'order'      => array('Orden.id_order' => 'DESC')
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
		$this->cambiarDatasource(array('Orden', 'OrdenEstado', 'Lang', 'CustomField' ,'CustomUserdata'));

		$this->paginate  = $paginate;
		
		$ordenes         = $this->paginate();
		$totalMostrados  = $this->Orden->find('count');
		
		# Medios de pago
		$medios_de_pago  = $this->obtenerMediosDePago();
		
		$rangosPagado    = $this->obtenerRangoPrecios('total_paid', 500000);
		$rangosEnvio     = $this->obtenerRangoPrecios('total_shipping', 1000);
		$rangosDescuento = $this->obtenerRangoPrecios('total_discounts', 50000);


		BreadcrumbComponent::add('Ordenes para transporte ');

		$this->set(compact('ordenes', 'totalMostrados', 'estados', 'medios_de_pago', 'rangosPagado', 'rangosEnvio', 'rangosDescuento'));
	}


	public function admin_orden($id)
	{	
		$this->verificarTienda();

		if ( ! $this->Orden->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden', 'CustomField', 'CustomUserdata'));

		$opt = array(
			'conditions'	=> array('Orden.id_order' => $id),
			'contain' => array(
				'OrdenEstado' => array('Lang'),
				'OrdenDetalle',
				'OrdenTransporte'
				)
		);


		$this->request->data	= $this->Orden->find('first', $opt);

		BreadcrumbComponent::add('Ordenes para transporte', '/ordenTransportes');
		BreadcrumbComponent::add('Ver OT´s ');

	}



	public function guardar_pdf_etiqueta($etiqueta = '', $data = array())
	{

		App::uses('CakePdf', 'Plugin/CakePdf/Pdf');

		# Ruta absoluta del pdf
		$absoluta = APP . 'webroot' . DS . 'Pdf' . DS . 'Etiquetas' . DS . $data['OrdenTransporte']['id'] . DS . 'etiqueta_' . $data['OrdenTransporte']['r_numero_ot'] . '.pdf';

		# Ruta para guardar en la Base de datos
		$archivo = Router::url('/', true) . 'Pdf/Etiquetas/' . $data['OrdenTransporte']['id'] . '/etiqueta_' . $data['OrdenTransporte']['r_numero_ot'] . '.pdf';

		if (file_exists($absoluta)) {
			return $archivo;
		}

		$this->pdfConfig = array(
			'download' => true,
			'margin' => array(
				'bottom' => 0,
				'left' => 0,
				'right' => 0,
				'top' => 0
			)
		);
		
		$this->CakePdf = new CakePdf();
		$this->CakePdf->template('guardar_pdf_etiqueta', 'logistica');
		$this->CakePdf->viewVars(compact('etiqueta', 'data'));
		$this->CakePdf->write($absoluta);

		ClassRegistry::init('OrdenTransporte')->id = $data['OrdenTransporte']['id'];
		
		if( ! ClassRegistry::init('OrdenTransporte')->saveField('pdf', $archivo)) {
			return '';
		}

		return $archivo;

	}



	public function admin_imprimir_etiqueta($id = '', $id_orden = '')
	{
		$this->verificarTienda();

		if ( ! ClassRegistry::init('OrdenTransporte')->exists($id) )
		{
			$this->Session->setFlash('No existe la OT consultada.', null, array(), 'danger');
			$this->redirect(array('action' => 'orden', $id_orden));
		}


		$etiquetaRes = ClassRegistry::init('OrdenTransporte')->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'fields' => array(
				'OrdenTransporte.r_imagen_etiqueta',
				'OrdenTransporte.r_numero_ot',
				'OrdenTransporte.r_barcode',
				'OrdenTransporte.id',
				'Ordentransporte.pdf'
			)
		));

		$etiqueta = $this->Ot->verEtiqueta($etiquetaRes['OrdenTransporte']['r_imagen_etiqueta'], $etiquetaRes['OrdenTransporte']['r_numero_ot'], $etiquetaRes['OrdenTransporte']['r_barcode']);

		$pdf = $this->guardar_pdf_etiqueta($etiqueta, $etiquetaRes);
		
		return $pdf;

	}



	public function generarPdf($id = null)
	{

		if ( ! ClassRegistry::init('Orden')->exists($id) )
		{
			$result = array(
				'code' => 400,
				'message' => 'No existe orden para generar Etiqueta.'
			);
		}

		$etiquetaRes = ClassRegistry::init('OrdenTransporte')->find('first', array(
			'conditions' => array(
				'id_order' => $id
			),
			'fields' => array(
				'OrdenTransporte.r_imagen_etiqueta',
				'OrdenTransporte.r_numero_ot',
				'OrdenTransporte.r_barcode',
				'OrdenTransporte.id',
				'OrdenTransporte.pdf'
			)
		));

		$etiqueta = $this->Ot->verEtiqueta($etiquetaRes['OrdenTransporte']['r_imagen_etiqueta'], $etiquetaRes['OrdenTransporte']['r_numero_ot'], $etiquetaRes['OrdenTransporte']['r_barcode']);

		$pdf = $this->guardar_pdf_etiqueta($etiqueta, $etiquetaRes);

		if (empty($pdf)) {
			$result = array(
				'code' => 500,
				'message' => 'No fue posible crear la etiqueta para impresión'
			);
		}else{
			$result = array(
				'code' => 200,
				'message' => 'Etiqueta creada con éxito.'
			);
		}

	}


	/**
	 * Valida que los indices del arreglo tengan un valor
	 * @param  array  $data Lista de elementos
	 * @return bool
	 */
	public function validarCamposOt($data = array())
	{
		if (!empty($data)) {
			foreach ($data as $ida => $campo) {
				if (!empty($campo) && $campo != 'e_direccion_complemento') {
					return true;
				}
			}
		}

		return false;
	}


	public function admin_generar_chilexpress($id_orden = '')
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
		{	unset($this->request->data['Detalle']);
			
			$validarCampos = $this->validarCamposOt($this->request->data['OrdenTransporte']);

			if ($validarCampos) {
				#prx($this->request->data);

				$this->request->data['OrdenTransporte']['tienda_id'] = $this->Session->read('Tienda.id');

				$refEnv = $this->request->data['OrdenTransporte']['e_referencia_envio'];
				$monCob = $this->request->data['OrdenTransporte']['e_monto_cobrar'];
				$codPro = $this->request->data['OrdenTransporte']['e_codigo_producto'];
				$codSer = $this->request->data['OrdenTransporte']['e_codigo_servicio'];
				$eoc    = $this->request->data['OrdenTransporte']['e_eoc'];
				$tcc    = $this->request->data['OrdenTransporte']['e_numero_tcc'];
				$comOri = $this->request->data['OrdenTransporte']['e_comuna_origen'];
				$remNom = $this->request->data['OrdenTransporte']['e_remitente_nombre'];
				$remEma = $this->request->data['OrdenTransporte']['e_remitente_email'];
				$remCel = $this->request->data['OrdenTransporte']['e_remitente_celular'];
				$desNom = $this->request->data['OrdenTransporte']['e_destinatario_nombre'];
				$desEma = $this->request->data['OrdenTransporte']['e_destinatario_email'];
				$desCel = $this->request->data['OrdenTransporte']['e_destinatario_celular'];
				$desCom = $this->request->data['OrdenTransporte']['e_direccion_comuna'];
				$desCal = $this->request->data['OrdenTransporte']['e_direccion_calle'];
				$desNum = $this->request->data['OrdenTransporte']['e_direccion_numero'];
				$desCop = $this->request->data['OrdenTransporte']['e_direccion_complemento'];
				$devCom = $this->request->data['OrdenTransporte']['e_direccion_d_comuna'];
				$devCal = $this->request->data['OrdenTransporte']['e_direccion_d_calle'];
				$devNum = $this->request->data['OrdenTransporte']['e_direccion_d_numero'];
				$devCom = $this->request->data['OrdenTransporte']['e_direccion_d_complemento'];
				$paPeso = $this->request->data['OrdenTransporte']['e_peso'];
				$paLarg = $this->request->data['OrdenTransporte']['e_largo'];
				$paAnch = $this->request->data['OrdenTransporte']['e_ancho'];
				$paAlto = $this->request->data['OrdenTransporte']['e_alto'];
				
				try {
					$resultado = $this->Ot->generarOt(
						$codPro,
						$codSer,
						$comOri,
						$tcc,
						$refEnv,
						'',
						$monCob,
						$eoc,
						$desNom,
						$desEma,
						$desCel,
						$remNom,
						$remEma,
						$remCel,
						$desCom,
						$desCal,
						$desNum,
						$desCop,
						$devCom,
						$devCal,
						$devNum,
						$devCom,
						$paPeso,
						$paLarg,
						$paAlto,
						$paAnch
					);
				} catch (Exception $e) {
					$resultado = $e->getMessage();
				}

				if (isset($resultado->respGenerarIntegracionAsistida->DatosEtiqueta)) {
					
					$this->request->data['OrdenTransporte']['r_numero_ot']                   = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->numeroOT;
					$this->request->data['OrdenTransporte']['r_numero_ot_padre']             = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->numeroOTPadre;
					$this->request->data['OrdenTransporte']['r_glosa_producto']              = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->glosaProductoOT;
					$this->request->data['OrdenTransporte']['r_glosa_servicio']              = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->glosaServicio;
					$this->request->data['OrdenTransporte']['r_nombre_destinatario']         = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->nombreDestinatario;
					$this->request->data['OrdenTransporte']['r_numero_guia']                 = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->numeroGuia;
					$this->request->data['OrdenTransporte']['r_glosa_cobertura']             = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->glosaCobertura;
					$this->request->data['OrdenTransporte']['r_direccion']                   = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->direccion;
					$this->request->data['OrdenTransporte']['r_codigo_region']               = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->codigoRegion;
					$this->request->data['OrdenTransporte']['r_adicionales']                 = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->adicionales;
					$this->request->data['OrdenTransporte']['r_peso']                        = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->peso;
					$this->request->data['OrdenTransporte']['r_alto']                        = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->alto;
					$this->request->data['OrdenTransporte']['r_ancho']                       = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->ancho;
					$this->request->data['OrdenTransporte']['r_largo']                       = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->largo;
					$this->request->data['OrdenTransporte']['r_xml_salida_epl']              = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->xmlSalidaEpl;
					$this->request->data['OrdenTransporte']['r_barcode']                     = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->barcode;
					$this->request->data['OrdenTransporte']['r_referencia2']                 = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->referencia2;
					$this->request->data['OrdenTransporte']['r_informacion_producto']        = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->informacionProducto;
					$this->request->data['OrdenTransporte']['r_glosa_corta_producto']        = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->GlosaCortaProductoOT;
					$this->request->data['OrdenTransporte']['r_fecha_impresion']             = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->fechaImpresion;
					$this->request->data['OrdenTransporte']['r_numero_bulto']                = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->numeroBulto;
					$this->request->data['OrdenTransporte']['r_centro_distribucion_destino'] = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->centroDistribucionDestino;
					$this->request->data['OrdenTransporte']['r_imagen_etiqueta']             = $resultado->respGenerarIntegracionAsistida->DatosEtiqueta->imagenEtiqueta;

					if (ClassRegistry::init('OrdenTransporte')->save($this->request->data)) {
						
						$this->Session->setFlash('OT generada con éxito' , null, array(), 'success');

						# Se envia el mensaje al cliente
						$dataEmail = array(
							'nombre_cliente' => $this->request->data['OrdenTransporte']['e_destinatario_nombre'],
							'to'			 => $this->request->data['OrdenTransporte']['e_destinatario_email'],
							'currier'        => $this->request->data['OrdenTransporte']['transporte'],
							'ot'             => $this->request->data['OrdenTransporte']['r_numero_ot'],
							'tracking_url'   => Configure::read('Chilexpress.seguimiento.tracking_url'),
							'Tienda'         => $this->Session->read('Tienda'),
							'Vendedor' 		 => array(
								'email' => $this->Auth->user('email')
							)
						);

						/*
						$enviar = $this->enviarEmail($dataEmail);

						if ($enviar['code'] == 200) {
							$this->Session->setFlash($enviar['message'] , null, array(), 'success');
						}else{
							$this->Session->setFlash($enviar['message'] , null, array(), 'warning');
						}*/


						# Se genera el PDF con la etiqueta
						$generarPdf = $this->generarPdf($id_orden);

						if ($generarPdf['code'] == 200) {
							$this->Session->setFlash($generarPdf['message'] , null, array(), 'success');
						}else{
							$this->Session->setFlash($generarPdf['message'] , null, array(), 'warning');
						}

						$this->redirect(array('controller' => 'ordenTransportes', 'action' => 'orden', $id_orden));
					}

				}else{
					$this->Session->setFlash($resultado->getMessage(), null, array(), 'danger');
					$this->redirect(array('controller' => 'ordenTransportes', 'action' => 'generar_chilexpress', $id_orden));
				}

			}

			$this->Session->setFlash('Error al generar la OT. Verifique los campos e intente nuevamente' , null, array(), 'danger');
			$this->redirect(array('controller' => 'ordenTransportes', 'action' => 'generar_chilexpress', $id_orden));

		}else{

			$opt = array(
				'fields' => array(
					'Orden.*',
					'OrdenEstado.*',
					'OrdenTransportista.*',
					'Transportista.*',
					'TransportistaIdioma.*',
					'DireccionEntrega.*',
					'RegionEntrega.*',
					'Cliente.*',
					'MensajeInterno.*'
				),
				'conditions'	=> array('Orden.id_order' => $id_orden),
				'joins' => array(
					array(
			            'table' => sprintf('%sorder_carrier', $this->Session->read('Tienda.prefijo')),
			            'alias' => 'OrdenTransportista',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'OrdenTransportista.id_order =' . $id_orden
			            )
		        	),
		        	array(
			            'table' => sprintf('%scarrier', $this->Session->read('Tienda.prefijo')),
			            'alias' => 'Transportista',
			            'type'  => 'INNER',
			            'conditions' => array(
			                'OrdenTransportista.id_carrier = Transportista.id_carrier',
			                'Transportista.external_module_name = "rg_chilexpress"'
			            )
		        	),
		        	array(
			            'table' => sprintf('%scarrier_lang', $this->Session->read('Tienda.prefijo')),
			            'alias' => 'TransportistaIdioma',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'Transportista.id_carrier = TransportistaIdioma.id_carrier'
			            )
		        	),
		        	array(
			            'table' => sprintf('%saddress', $this->Session->read('Tienda.prefijo')),
			            'alias' => 'DireccionEntrega',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'Orden.id_address_delivery = DireccionEntrega.id_address'
			            )
		        	),
		        	array(
			            'table' => sprintf('%sstate', $this->Session->read('Tienda.prefijo')),
			            'alias' => 'RegionEntrega',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'DireccionEntrega.id_state = RegionEntrega.id_state'
			            )
		        	),
		        	array(
			            'table' => sprintf('%smessage', $this->Session->read('Tienda.prefijo')),
			            'alias' => 'MensajeInterno',
			            'type'  => 'LEFT',
			            'conditions' => array(
			                'Orden.id_cart = MensajeInterno.id_cart'
			            )
		        	),
				),
				'contain' => array(
					'OrdenEstado' => array('Lang'),
					'OrdenDetalle' => array('Productotienda'),
					'OrdenTransporte',
					'Cliente',
					'ClienteHilo' => array('ClienteMensaje' => array('Empleado')),
				),
			);


			$this->request->data	= $this->Orden->find('first', $opt);

			if (empty($this->request->data)) {
				$this->Session->setFlash('No es posible generar una OT de Chilexpress para ésta orden de compra.' , null, array(), 'warning');
				$this->redirect(array('controller' => 'ordenTransportes', 'action' => 'index'));
			}

			# Valor por defecto de las medidas
			$this->request->data['OrdenTransporte']['e_alto'] = 0;
			$this->request->data['OrdenTransporte']['e_largo'] = 0;
			$this->request->data['OrdenTransporte']['e_ancho'] = 0;

			if(!empty($this->request->data['OrdenDetalle'])){

				# Se obtienen los tamaños de los productos
				$cajasProductos = $this->Ot->obtenerCajasProductos($this->request->data['OrdenDetalle'], 'Productotienda');

				# Se Arma un paquete de productos con el algoritmo LAFF y se obtienen sus medidas
				$paqueteProductos = $this->Ot->obtenerDimensionesPaquete($cajasProductos);

				$this->request->data['OrdenTransporte']['e_alto']  = (!empty($paqueteProductos['height'])) ? $paqueteProductos['height'] : 0 ;
				$this->request->data['OrdenTransporte']['e_largo'] = (!empty($paqueteProductos['length'])) ? $paqueteProductos['length'] : 0 ;
				$this->request->data['OrdenTransporte']['e_ancho'] = (!empty($paqueteProductos['width'])) ? $paqueteProductos['width'] : 0 ;
			}

			if (!empty($this->request->data['OrdenTransportista'])){
				$this->request->data['OrdenTransporte']['e_peso'] = $this->request->data['OrdenTransportista']['weight'];
			}

			#prx($this->request->data);
		}

		# Transportistas	
		$curriers = array(
			'Chilexpress' => 'Chilexpress'
		);

		# Servicios Chilexpress
		$codigosServicio = $this->Ot->obtenerListaServicios();

		# Productos Chilexpress
		$codigoProductosChilexpress = $this->Ot->obtenerListaProductos();

 		# TCC
 		$tcc = $this->Ot->obtenerListaTCC();

 		# EOC
 		$codigoEoc = $this->Ot->obtenerListaEoc();

 		$comunasCobertura = to_array($this->GeoReferencia->obtenerCoberturas());
 		$comunas = array();

 		if ($comunasCobertura['respObtenerCobertura']['CodEstado'] == 0) {
 			foreach ($comunasCobertura['respObtenerCobertura']['Coberturas'] as $ico => $cobertura) {
 				$comunas[$cobertura['GlsComuna']] = $cobertura['GlsComuna'];	
 			}
 		}

 		# Se agrega id de servicio para usarlo en el front
 		if (isset($this->request->data['Transportista']) && empty($this->request->data['OrdenTransporte']['e_codigo_servicio'])) {
			$this->request->data['OrdenTransporte']['e_codigo_servicio'] = array_search($this->request->data['TransportistaIdioma']['delay'], $codigosServicio);
		}

		# Se agrega comuna de destino
		if (isset($this->request->data['DireccionEntrega']) && empty($this->request->data['OrdenTransporte']['e_direccion_comuna'])) {
			$this->request->data['OrdenTransporte']['e_direccion_comuna'] = array_search($this->request->data['DireccionEntrega']['city'], $comunas);
		}
		
		BreadcrumbComponent::add('Ordenes de transporte', '/ordenTransportes');
		BreadcrumbComponent::add('Ver OT´s', '/ordenTransportes/orden/'.$id_orden);
		BreadcrumbComponent::add('Generar OT ');

		$this->set(compact('curriers', 'codigosServicio', 'codigoProductosChilexpress', 'comunas', 'tcc', 'codigoEoc'));
	}


	public function admin_view_chilexpress($id_orden = '')
	{
		$this->verificarTienda();

		if ( ! $this->Orden->exists($id_orden) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden', 'OrdenEstado', 'OrdenDetalle', 'Lang', 'Cliente', 'ClienteHilo', 'ClienteMensaje', 'Empleado', 'CustomUserdata', 'CustomField', 'CustomFieldLang'));

		$opt = array(
			'fields' => array(
				'Orden.*',
				'OrdenEstado.*',
				'OrdenTransportista.*',
				'Transportista.*',
				'TransportistaIdioma.*',
				'DireccionEntrega.*',
				'RegionEntrega.*',
				'Cliente.*'
			),
			'conditions'	=> array('Orden.id_order' => $id_orden),
			'joins' => array(
				array(
		            'table' => sprintf('%sorder_carrier', $this->Session->read('Tienda.prefijo')),
		            'alias' => 'OrdenTransportista',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'OrdenTransportista.id_order =' . $id_orden
		            )
	        	),
	        	array(
		            'table' => sprintf('%scarrier', $this->Session->read('Tienda.prefijo')),
		            'alias' => 'Transportista',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'OrdenTransportista.id_carrier = Transportista.id_carrier'
		            )
	        	),
	        	array(
		            'table' => sprintf('%scarrier_lang', $this->Session->read('Tienda.prefijo')),
		            'alias' => 'TransportistaIdioma',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Transportista.id_carrier = TransportistaIdioma.id_carrier'
		            )
	        	),
	        	array(
		            'table' => sprintf('%saddress', $this->Session->read('Tienda.prefijo')),
		            'alias' => 'DireccionEntrega',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'Orden.id_address_delivery = DireccionEntrega.id_address'
		            )
	        	),
	        	array(
		            'table' => sprintf('%sstate', $this->Session->read('Tienda.prefijo')),
		            'alias' => 'RegionEntrega',
		            'type'  => 'LEFT',
		            'conditions' => array(
		                'DireccionEntrega.id_state = RegionEntrega.id_state'
		            )
	        	),
			),
			'contain' => array(
				'OrdenEstado' => array('Lang'),
				'OrdenDetalle' => array('Productotienda'),
				'OrdenTransporte' => array(
					'conditions' => array(
						'OrdenTransporte.transporte' => 'Chilexpress'
					)
				),
				'Cliente',
				'ClienteHilo' => array('ClienteMensaje' => array('Empleado')),
			),
		);

		if ($this->request->is('post')) {

 			$dataEmail = array(
				'nombre_cliente' => $this->request->data['OrdenTransporte']['nombre'],
				'to' 			 => $this->request->data['OrdenTransporte']['email'],
				'currier'        => $this->request->data['OrdenTransporte']['transporte'],
				'ot'             => $this->request->data['OrdenTransporte']['ot'],
				'tracking_url'   => Configure::read('Chilexpress.seguimiento.tracking_url'),
				'Tienda'         => $this->Session->read('Tienda'),
				'Vendedor' 		 => array(
					'email' => $this->Auth->user('email')
				)
			);

 			#prx($dataEmail);
			$enviar = $this->enviarEmail($dataEmail);

			if ($enviar['code'] == 200) {
				$this->Session->setFlash($enviar['message'] , null, array(), 'success');
			}else{
				$this->Session->setFlash($enviar['message'] , null, array(), 'warning');
			}

			$this->redirect(array('controller' => 'ordenTransportes', 'action' => 'orden', $id_orden));
 		}


		$this->request->data	= $this->Orden->find('first', $opt);
		
		if ( empty($this->request->data['OrdenTransporte']) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		# Transportistas	
		$curriers = array(
			'Chilexpress' => 'Chilexpress'
		);

		# Servicios Chilexpress
		$codigosServicio = $this->Ot->obtenerListaServicios();

		# Productos Chilexpress
		$codigoProductosChilexpress = $this->Ot->obtenerListaProductos();

 		# TCC
 		$tcc = $this->Ot->obtenerListaTCC();

 		# EOC
 		$codigoEoc = $this->Ot->obtenerListaEoc();

 		$comunasCobertura = to_array($this->GeoReferencia->obtenerCoberturas());
 		$comunas = array();

 		if ($comunasCobertura['respObtenerCobertura']['CodEstado'] == 0) {
 			foreach ($comunasCobertura['respObtenerCobertura']['Coberturas'] as $ico => $cobertura) {
 				$comunas[$cobertura['GlsComuna']] = $cobertura['GlsComuna'];	
 			}
 		}


 		$tracking = array();
 		if (isset($this->request->data['OrdenTransporte'][0]['r_numero_ot'])) {
 			$tracking = $this->trackingChilexpress($this->request->data['OrdenTransporte'][0]['r_numero_ot']);
 		}

 		/*
 			Definir si es despachoa domicilio o retiro en sucursal
 		 */
		
		BreadcrumbComponent::add('Ordenes de transporte', '/ordenTransportes');
		BreadcrumbComponent::add('Ver OT´s', '/ordenTransportes/orden/'.$id_orden);
		BreadcrumbComponent::add('Detalle OT ');

		$this->set(compact('curriers', 'codigosServicio', 'codigoProductosChilexpress', 'comunas', 'tcc', 'codigoEoc', 'tracking'));
	}



	public function trackingChilexpress($ot = '')
	{	
		if (empty($ot)) {
			return array();
		}

		$ruta    = Configure::read('Chilexpress.seguimiento.path');
		$archivo = Configure::read('Chilexpress.seguimiento.filename');
		
		$fullpath = $ruta . $archivo;

		//$arr = $this->Tracking->leer_excel_tracking($fullpath, '99574733764');
		$arr = $this->Tracking->leer_excel_tracking($fullpath, $ot);

		return $arr;
	}


	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			ClassRegistry::init('OrdenTransporte')->create();
			if ( ClassRegistry::init('OrdenTransporte')->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		BreadcrumbComponent::add('Transportes', '/transportes');
		BreadcrumbComponent::add('Agregar');
	}

	public function admin_edit($id = null)
	{
		if ( ! ClassRegistry::init('OrdenTransporte')->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( ClassRegistry::init('OrdenTransporte')->save($this->request->data) )
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
			$this->request->data	= ClassRegistry::init('OrdenTransporte')->find('first', array(
				'conditions'	=> array('Transporte.id' => $id)
			));
		}

		BreadcrumbComponent::add('Transportes', '/transportes');
		BreadcrumbComponent::add('Editar');
	}

	public function admin_delete($id = null)
	{
		ClassRegistry::init('OrdenTransporte')->id = $id;
		if ( ! ClassRegistry::init('OrdenTransporte')->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( ClassRegistry::init('OrdenTransporte')->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= ClassRegistry::init('OrdenTransporte')->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys(ClassRegistry::init('OrdenTransporte')->_schema);
		$modelo			= ClassRegistry::init('OrdenTransporte')->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	public function admin_obtener_sucursales_comuna($comuna = 'SANTIAGO CENTRO')
	{	
		if (empty($comuna)) {
			$res = array(
				'code'    => 300,
				'message' => 'Seleccione comuna destino.',
				'lista'   => '',
				'tabla'   => ''
			);
		
			echo json_encode($res);
			exit;
		}


		$htmlOptions = '';
		$htmlDiv     = '<div class="row">';
		$htmlDiv     .= '<div class="col-xs-12">';
		$htmlDiv     .= '<h3>Sucrusales disponibles en ' . $comuna . '</h3>';
		$htmlDiv     .= '<div id="comunasGlosario" class="table-responsive" style="height: 200px; overflow-y: scroll;">';
		$htmlDiv     .= '<table class="table table-bordered">';
		$htmlDiv     .= '<thead>';
		$htmlDiv     .= '<th>Usar</th>';
		$htmlDiv     .= '<th>Nombre Oficina</th>';
		$htmlDiv     .= '<th>Nombre Calle</th>';
		$htmlDiv     .= '<th>Número Oficina</th>';
		$htmlDiv     .= '<th>Comuna</th>';
		$htmlDiv     .= '<th>Complemento</th>';
		$htmlDiv     .= '</thead>';

		$htmlDivNoResult = '<div class="row">';
		$htmlDivNoResult .= '<div class="col-xs-12">';
		$htmlDivNoResult .= '<h3>No hay oficinas disponibles en '.$comuna.'</h3>';
		$htmlDivNoResult .= '</div>';
		$htmlDivNoResult .= '</div>';

		$oficinas = to_array($this->GeoReferencia->obtenerDireccionOficinasComuna($comuna));
		
		if (!isset($oficinas['respObtenerOficinas']['CodEstado']) || $oficinas['respObtenerOficinas']['CodEstado'] != 0) {

			$res = array(
				'code'    => 404,
				'message' => 'Solicitud procesada con éxito',
				'lista'   => '',
				'tabla'   => $htmlDivNoResult
			);
			
			echo json_encode($res);
			exit;
		}

		$htmlDiv .= '<tbody>';

		if (isset($oficinas['respObtenerOficinas']['Calles'][0])) {

			foreach($oficinas['respObtenerOficinas']['Calles'] as $ic => $calle) {
				# Options
				$htmlOptions .= '<option value="' . $calle['NombreOficina'] . '">' . $calle['NombreOficina'] . '</option>';
				
				# Tabla
				$htmlDiv .= '<tr>';
				$htmlDiv .= '<td><input type="radio" name="usar_sucursal" class="icheckbox js-select-sucursal"></td>';
				$htmlDiv .= '<td>' . $calle['NombreOficina'] . '</td>';
				$htmlDiv .= '<td>' . $calle['NombreCalle'] . '</td>';
				$htmlDiv .= '<td>' . $calle['Numeracion'] . '</td>';
				$htmlDiv .= '<td>' . $calle['NombreComuna'] . '</td>';
				$htmlDiv .= '<td>Chilexpress</td>';
				$htmlDiv .= '<tr>';
			}	

		}else{

			# Options
			$htmlOptions .= '<option value="' . $oficinas['respObtenerOficinas']['Calles']['NombreOficina'] . '">' . $oficinas['respObtenerOficinas']['Calles']['NombreOficina'] . '</option>';
			
			# Tabla
			$htmlDiv .= '<tr>';
			$htmlDiv .= '<td><input type="radio" name="usar_sucursal" class="icheckbox js-select-sucursal"></td>';
			$htmlDiv .= '<td>' . $oficinas['respObtenerOficinas']['Calles']['NombreOficina'] . '</td>';
			$htmlDiv .= '<td>' . $oficinas['respObtenerOficinas']['Calles']['NombreCalle'] . '</td>';
			$htmlDiv .= '<td>' . $oficinas['respObtenerOficinas']['Calles']['Numeracion'] . '</td>';
			$htmlDiv .= '<td>' . $oficinas['respObtenerOficinas']['Calles']['NombreComuna'] . '</td>';
			$htmlDiv .= '<td>Chilexpress</td>';
			$htmlDiv .= '<tr>';

		}

		$htmlDiv .= '</tbody>';
		$htmlDiv .= '</ul>';
		$htmlDiv .= '</div><!-- Endtable div -->';
		$htmlDiv .= '</div><!-- End col -->';
		$htmlDiv .= '</div><!-- End row -->';

		$res = array(
			'code'    => 200,
			'message' => 'Solicitud procesada con éxito',
			'lista'   => $htmlOptions,
			'tabla'   => $htmlDiv
		);
		
		echo json_encode($res);
		exit;
	}



	public function admin_validar_direccion($comuna = '', $calle = '', $numero = '')
	{	
		if (empty($comuna) || empty($calle) || empty($numero)) {
			$res = array(
				'code'    => 300,
				'message' => 'Ingrese Comuna, Calle y número.'
			);
		
			echo json_encode($res);
			exit;
		}	

		$direccion = to_array($this->GeoReferencia->validarDireccion($comuna, $calle, '' , $numero, '', ''));
		
		if (!isset($direccion['respObtenerDireccion']['CodEstado']) || $direccion['respObtenerDireccion']['CodEstado'] != 0) {

			$res = array(
				'code'    => 404,
				'message' => $direccion['respObtenerDireccion']['GlsEstado']
			);
			
			echo json_encode($res);
			exit;
		}


		if (isset($direccion['respObtenerDireccion']['Direcciones']['CodResultado']) && $direccion['respObtenerDireccion']['CodEstado'] == 0 ) {

			$res = array(
				'code'    => 200,
				'message' => $direccion['respObtenerDireccion']['GlsEstado']
			);

		}
		
		echo json_encode($res);
		exit;
	}



	public function enviarEmail($data = array())
	{
		$result = array();


		foreach ($data as $id => $d) {
			if (empty($d)) {

				$result = array(
					'code' => 400,
					'message' => 'No se logró enviar el email al cliente ya que no se completaron todos los campos.'
				);

				return $result;
			}
		}
		
		App::uses('CakeEmail', 'Network/Email');

		$bccArray = array();
		# BCC
		if ( !empty($data['Tienda']['emails_bcc']) ) {
			$bcc = explode( ',', trim($data['Tienda']['emails_bcc']) );
			$bccArray = array();
			foreach ($bcc as $key => $value) {
				$bccArray[$value] = $value;
			}
		}
		
		$Email = new CakeEmail();
		$Email->viewVars('datos', $data);
		$Email->from(array($data['Vendedor']['email'] => sprintf('Ventas %s', $data['Tienda']['nombre']) ));
		$Email->to($data['to']);
		$Email->subject('[NDRZ] Código de seguimiento de su pedido en ' . $data['Tienda']['nombre']);
		$Email->addBcc($bcc);
		$Email->emailFormat('html');
		$Email->template('tracking');	

		if( $Email->send() ) {

			$result = array(
				'code' => 200,
				'message' => "Email enviado con éxito."
			);

			return $result;

		}else{

			$result = array(
				'code' => 500,
				'message' => "Error al enviar la cotización al cliente. Intente enviarla manualmente."
			);

			return $result;
		}

	}	

}
