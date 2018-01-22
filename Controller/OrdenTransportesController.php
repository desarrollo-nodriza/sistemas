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

		// Filtrado de ordenes por formulario
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
		$estados = Hash::extract($estados, '{n}.Lang.0.OrdenEstadoIdioma.name');

		$paginate = array_replace_recursive($paginate, array(
			'limit' => 20,
			'contain' => array('OrdenEstado' => array('Lang'), 'OrdenTransporte'),
			'conditions' => array('Orden.current_state' => $estadosId),
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
		$this->cambiarDatasource(array('Orden', 'OrdenEstado', 'Lang', 'CustomField' ,'CustomUserdata'));

		$this->paginate = $paginate;

		$ordenes	= $this->paginate();
		$totalMostrados = $this->Orden->find('count');
		
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
					'OrdenDetalle',
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
		
		BreadcrumbComponent::add('Ordenes de transporte', '/ordenTransporte');
		BreadcrumbComponent::add('Ver OT´s', '/ordenTransporte/orden/'.$id_orden);
		BreadcrumbComponent::add('Generar OT ');

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
}
