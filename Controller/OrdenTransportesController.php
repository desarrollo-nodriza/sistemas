<?php
App::uses('AppController', 'Controller');
class OrdenTransportesController extends AppController
{	
	public $uses = array('Orden');
	public $helpers = array('Chilexpress.Chilexpress');

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

		# Estados del DTE
		# OBtener cantidad de folios facturas
		#$this->getFolioInfo(33, 76381142);

		# Informacion del contribuyente
		#$contribuyente = $this->getContribuyenteInfo($this->rutSinDv($this->Session->read('Tienda.rut')));
		
		#$this->GeoReferencia->obtenerRegiones();

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

		$this->set(compact('dtes'));
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
			prx($this->request->data);
			try {
				$resultado = $this->Ot->generarOt(3,
				3,
				'RENCA',
				22106942,
				'123456789',
				'Compra1',
				10000,
				0,
				'Mario Moyano',
				'mmoyano@chilexpress.cl',
				'84642291',
				'Alexis Erazo',
				'aerazo@chilexpress.cl',
				'84642291',
				'PENALOLEN',
				'Camino de las Camelias',
				'7909',
				'Casa 33',
				'PUDAHUEL',
				'Jose Joaquin Perez',
				'1376',
				'Piso 2',
				5,
				1,
				1,
				1);
			} catch (Exception $e) {
				$resultado = $e->getMessage();
			}

			return $resultado;

			
			# Guardamos OT
			if($this->Orden->OrdenTransporte->saveAll($this->request->data)) {

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

				$this->redirect(array('controller' => 'ordenTransporte', 'action' => 'orden', $id_orden));

			}else{
				$this->Session->setFlash('Error al guardar la información en la base de detos local. Intente nuevamente.' , null, array(), 'warning');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'orden', $id_orden));
			}

		}else{

			$opt = array(
				'fields' => array(
					'Orden.*',
					'OrdenEstado.*',
					'OrdenTransportista.*',
					'Transportista.*',
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
					'OrdenDetalle',
					'OrdenTransporte',
					'Cliente',
					'ClienteHilo' => array('ClienteMensaje' => array('Empleado')),
				),
			);


			$this->request->data	= $this->Orden->find('first', $opt);

			#prx($this->request->data);
		}

		# Transportistas	
		$curriers = array(
			'Chilexpress' => 'Chilexpress'
		);

		# Servicios Chilexpress
		$codigosServicio = array(
			3 => 'Chilexpress normal',
			2 => 'Overnight'
		);

		# Productos Chilexpress
		$codigoProductosChilexpress = array(
			3 => 'ENCOMIENDA',
			#2 => 'VALIJA',
			#1 => 'DOCUMENTO'
 		);

 		# TCC
 		$tcc = array(
 			22106942 => 22106942
 		);

 		$comunasCobertura = to_array($this->GeoReferencia->obtenerCoberturas());
 		$comunas = array();

 		if ($comunasCobertura['respObtenerCobertura']['CodEstado'] == 0) {
 			foreach ($comunasCobertura['respObtenerCobertura']['Coberturas'] as $ico => $cobertura) {
 				$comunas[$cobertura['GlsComuna']] = $cobertura['GlsComuna'];	
 			}
 		}

 		# Se agrega id de servicio para usarlo en el front
 		if (isset($this->request->data['Transportista']) && empty($this->request->data['OrdenTransporte']['e_codigo_servicio'])) {
			$this->request->data['OrdenTransporte']['e_codigo_servicio'] = array_search($this->request->data['Transportista']['name'], $codigosServicio);
		}

		# Se agrega comuna de destino
		if (isset($this->request->data['DireccionEntrega']) && empty($this->request->data['OrdenTransporte']['e_direccion_comuna'])) {
			$this->request->data['OrdenTransporte']['e_direccion_comuna'] = array_search($this->request->data['DireccionEntrega']['city'], $comunas);
		}
		

 		/*
 			Definir si es despachoa domicilio o retiro en sucursal
 		 */
		
		BreadcrumbComponent::add('Ordenes de transporte', '/ordenTransporte');
		BreadcrumbComponent::add('Ver OT´s', '/ordenTransporte/orden/'.$id_orden);
		BreadcrumbComponent::add('Generar OT ');

		$this->set(compact('curriers', 'codigosServicio', 'codigoProductosChilexpress', 'comunas', 'tcc'));
	}


	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->OrdenTransporte->create();
			if ( $this->OrdenTransporte->save($this->request->data) )
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
		if ( ! $this->OrdenTransporte->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->OrdenTransporte->save($this->request->data) )
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
			$this->request->data	= $this->OrdenTransporte->find('first', array(
				'conditions'	=> array('Transporte.id' => $id)
			));
		}

		BreadcrumbComponent::add('Transportes', '/transportes');
		BreadcrumbComponent::add('Editar');
	}

	public function admin_delete($id = null)
	{
		$this->OrdenTransporte->id = $id;
		if ( ! $this->OrdenTransporte->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->OrdenTransporte->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->OrdenTransporte->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->OrdenTransporte->_schema);
		$modelo			= $this->OrdenTransporte->alias;

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
		$htmlDiv     = '<div id="comunasGlosario" class="hide">';
		$htmlDiv     .= '<table class="table table-bordered">';
		$htmlDiv     .= '<thead>';
		$htmlDiv     .= '<th>Nombre Oficina</th>';
		$htmlDiv     .= '<th>Nombre Calle</th>';
		$htmlDiv     .= '<th>Número Oficina</th>';
		$htmlDiv     .= '<th>Comuna</th>';
		$htmlDiv     .= '</thead>';

		$oficinas = to_array($this->GeoReferencia->obtenerDireccionOficinasComuna($comuna));
		
		if (!isset($oficinas['respObtenerOficinas']['CodEstado']) || $oficinas['respObtenerOficinas']['CodEstado'] != 0) {
			$res = array(
				'code'    => 300,
				'message' => 'Ocurrió un error al obtener los datos.',
				'lista'   => '',
				'tabla'   => ''
			);
		
			echo json_encode($res);
			exit;
		}


		$htmlDiv .= '<tbody>';

		foreach($oficinas['respObtenerOficinas']['Calles'] as $ic => $calle) {
			# Options
			$htmlOptions .= '<option value="' . $calle['NombreOficina'] . '">' . $calle['NombreOficina'] . '</option>';
			
			# Tabla
			$htmlDiv .= '<tr>';
			$htmlDiv .= '<td>' . $calle['NombreOficina'] . '</td>';
			$htmlDiv .= '<td>' . $calle['NombreCalle'] . '</td>';
			$htmlDiv .= '<td>' . $calle['Numeracion'] . '</td>';
			$htmlDiv .= '<td>' . $calle['NombreComuna'] . '</td>';
			$htmlDiv .= '<tr>';

		}

		$htmlDiv .= '</tbody>';
		$htmlDiv .= '</ul>';
		$htmlDiv .= '</div>';

		$res = array(
			'code'    => 400,
			'message' => 'Solicitud procesada con éxito',
			'lista'   => $htmlOptions,
			'tabla'   => $htmlDiv
		);
		
		echo json_encode($res);
		exit;
	}
}
