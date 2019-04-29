<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'PhpSpreadsheet', array('file' => 'PhpSpreadsheet/vendor/autoload.php'));

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;


class VentaDetalleProductosController extends AppController
{	
	public $components = array(
		'Linio',
		'Prestashop',
		'MeliMarketplace',
		'RequestHandler'
	);

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
				$redirect[$campo] = urlencode($valor);
			}
		}
		
    	$this->redirect($redirect);

    }


	public function admin_index()
	{		
		$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;

		// Filtrado de dtes por formulario
		if ( $this->request->is('post') ) {

			$this->filtrar('ventaDetalleProductos', 'index');

		}

		$paginate = array_replace_recursive($paginate, array(
			'limit' => 20,
			'order' => array('VentaDetalleProducto.id_externo' => 'DESC'),
			'contain' => array('Marca')
			));


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'id':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('VentaDetalleProducto.id_externo' => str_replace('%2F', '/', urldecode($valor) ) )));
						break;
					case 'nombre':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('VentaDetalleProducto.nombre LIKE' => '%'.trim(str_replace('%2F', '/', urldecode($valor) )).'%')));
						break;
					case 'marca':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('VentaDetalleProducto.marca_id' => $valor)));
						break;
				}
			}
		}


		$this->paginate		= $paginate;
		$ventadetalleproductos	= $this->paginate();

		$marcas = ClassRegistry::init('Marca')->find('list');

		BreadcrumbComponent::add('Productos');

		$this->set(compact('ventadetalleproductos', 'marcas'));
	}


	public function admin_movimientos()
	{
		$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;

		// Filtrado de dtes por formulario
		if ( $this->request->is('post') ) {

			$this->filtrar('ventaDetalleProductos', 'movimientos');

		}

		$paginate = array_replace_recursive($paginate, array(
			'limit' => 20,
			'order' => array('BodegasVentaDetalleProducto.fecha' => 'DESC'),
			'contain' => array(
				'VentaDetalleProducto' => array(
					'fields' => array(
						'VentaDetalleProducto.nombre'
					)
				)			
			)
			));


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'producto':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('BodegasVentaDetalleProducto.venta_detalle_producto_id' => $valor )));
						break;
					case 'bodega':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('BodegasVentaDetalleProducto.bodega_id' => $valor )));
						break;
					case 'io':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('BodegasVentaDetalleProducto.io' => $valor)));
						break;
					case 'tipo':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('BodegasVentaDetalleProducto.tipo' => $valor)));
						break;
				}
			}
		}


		$this->paginate		= $paginate;
		$movimientos	= $this->paginate('BodegasVentaDetalleProducto');

		$bodegas 		= ClassRegistry::init('Bodega')->find('list');
		$productos 		= ClassRegistry::init('VentaDetalleProducto')->find('list');
		$ios 			= array('IN' => 'IN', 'ED' => 'ED');
		$tiposM 		= ClassRegistry::init('Bodega')->tipoMovimientos;
		$tipos 			= array();

		foreach ($tiposM as $nombre => $arreglo) {
			$tipos[$nombre] = $arreglo['NOMBRE'];
		}

		BreadcrumbComponent::add('Movimientos de productos');

		$this->set(compact('movimientos', 'bodegas', 'productos', 'ios', 'tipos'));
	}


	public function admin_exportar_movimientos()
	{	
		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
		set_time_limit(-1);

		$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;

		$paginate = array_replace_recursive($paginate, array(
			'order' => array('BodegasVentaDetalleProducto.fecha' => 'DESC'),
			'contain' => array(
				'VentaDetalleProducto' => array(
					'fields' => array(
						'VentaDetalleProducto.nombre'
					)
				)			
			)
			));


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'producto':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('BodegasVentaDetalleProducto.venta_detalle_producto_id' => $valor )));
						break;
					case 'bodega':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('BodegasVentaDetalleProducto.bodega_id' => $valor )));
						break;
					case 'io':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('BodegasVentaDetalleProducto.io' => $valor)));
						break;
					case 'tipo':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('BodegasVentaDetalleProducto.tipo' => $valor)));
						break;
				}
			}
		}


		$datos = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', $paginate);

		$campos			= array_keys(ClassRegistry::init('BodegasVentaDetalleProducto')->_schema);
		$modelo			= ClassRegistry::init('BodegasVentaDetalleProducto')->alias;
		
		$this->set(compact('datos', 'campos', 'modelo'));
	}


	public function admin_moverInventario($id = null)
	{
		if ( ! $this->VentaDetalleProducto->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$errores = array();
		$aceptados = array();

		$bodegas = ClassRegistry::init('Bodega')->find('list', array('conditions' => array('activo' => 1)));

		if ( $this->request->is('post') || $this->request->is('put')) {

			foreach ($this->request->data['VentaDetalleProducto'] as $im => $m) {
				
				if ($m['bodega_origen'] == $m['bodega_destino']) {
					$errores[] = 'No se puede mover a su misma bodega.';
					continue;
				}

				if (empty($m['bodega_destino']) || empty($m['cantidad'])) {
					continue;
				}

				if (ClassRegistry::init('Bodega')->moverProductoBodega($id, $m['bodega_origen'], $m['bodega_destino'], $m['cantidad'])) {
					$aceptados[] = $m['cantidad'] . ' item movido desde ' . $bodegas[$m['bodega_origen']] . ' hacia ' . $bodegas[$m['bodega_destino']];
				}

			}

		}

		$this->request->data = $this->VentaDetalleProducto->find('first', array(
			'conditions' => array(
				'VentaDetalleProducto.id' => $id
			),
			'contain' => array(
				'Bodega' => array(
					'fields' => array(
						'Bodega.id', 'Bodega.nombre', 'Bodega.principal'
					)
				)
			),
			'fields' => array(
				'VentaDetalleProducto.id', 'VentaDetalleProducto.nombre', 'VentaDetalleProducto.precio_costo'
			)
		));

		if (!empty($errores)) {
			$this->Session->setFlash($this->crearAlertaUl($errores, 'Errores encontrados'), null, array(), 'danger');
		}

		if (!empty($aceptados)) {
			$this->Session->setFlash($this->crearAlertaUl($aceptados, 'Movimientos correcto'), null, array(), 'success');
			$this->redirect(array('action' => 'moverInventario', $id));
		}

		foreach ($bodegas as $ib => $b) {
			$this->request->data['VentaDetalleProducto']['Total'][$ib]['bodega_id'] = $ib;
			$this->request->data['VentaDetalleProducto']['Total'][$ib]['bodega_nombre'] = $b;

			$iosBodega = Hash::extract($this->request->data['Bodega'], '{n}.BodegasVentaDetalleProducto[bodega_id='.$ib.']');

			$this->request->data['VentaDetalleProducto']['Total'][$ib]['total'] = array_sum(Hash::extract($iosBodega, '{n}[io=IN].cantidad')) - array_sum(Hash::extract($iosBodega, '{n}[io=ED].cantidad'));	
		}

		BreadcrumbComponent::add('Listado de Movimientos', '/ventaDetalleProductos/movimientos');
		BreadcrumbComponent::add('Movimientos de bodega');

		$this->set(compact('bodegas'));

	}


	public function admin_ajustarInventario($id = null)
	{
		if ( ! $this->VentaDetalleProducto->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$errores = array();
		$aceptados = array();

		$bodegas = ClassRegistry::init('Bodega')->find('list', array('conditions' => array('activo' => 1)));

		if ( $this->request->is('post') || $this->request->is('put')) {

			foreach ($this->request->data['VentaDetalleProducto'] as $im => $m) {

				if ($m['ajustar']=='') {
					continue;
				}

				if (ClassRegistry::init('Bodega')->ajustarInventario($id, $m['bodega'], $m['ajustar'])) {
					$aceptados[] = $m['ajustar'] . ' items ajustados en bodega ' . $bodegas[$m['bodega']];
				}

			}

		}

		$this->request->data = $this->VentaDetalleProducto->find('first', array(
			'conditions' => array(
				'VentaDetalleProducto.id' => $id
			),
			'contain' => array(
				'Bodega' => array(
					'fields' => array(
						'Bodega.id', 'Bodega.nombre', 'Bodega.principal'
					)
				)
			),
			'fields' => array(
				'VentaDetalleProducto.id', 'VentaDetalleProducto.nombre', 'VentaDetalleProducto.precio_costo'
			)
		));

		if (!empty($errores)) {
			$this->Session->setFlash($this->crearAlertaUl($errores, 'Errores encontrados'), null, array(), 'danger');
		}

		if (!empty($aceptados)) {
			$this->Session->setFlash($this->crearAlertaUl($aceptados, 'Movimientos correcto'), null, array(), 'success');
			$this->redirect(array('action' => 'ajustarInventario', $id));
		}

		foreach ($bodegas as $ib => $b) {
			$this->request->data['VentaDetalleProducto']['Total'][$ib]['bodega_id'] = $ib;
			$this->request->data['VentaDetalleProducto']['Total'][$ib]['bodega_nombre'] = $b;

			$iosBodega = Hash::extract($this->request->data['Bodega'], '{n}.BodegasVentaDetalleProducto[bodega_id='.$ib.']');

			$this->request->data['VentaDetalleProducto']['Total'][$ib]['total'] = array_sum(Hash::extract($iosBodega, '{n}[io=IN].cantidad')) - array_sum(Hash::extract($iosBodega, '{n}[io=ED].cantidad'));	
		}

		BreadcrumbComponent::add('Listado de Movimientos', '/ventaDetalleProductos/movimientos');
		BreadcrumbComponent::add('Ajuste de inventario');

		$this->set(compact('bodegas'));
	}


	public function admin_ajustarInventarioMasivo()
	{	
		$tipoPermitido = array(
			'xlsx',
			'xls',
			'csv'
		);

		$datos = array();

		if ( $this->request->is('post') || $this->request->is('put')) {

			ini_set('max_execution_time', 0);

			if ($this->request->data['VentaDetalleProducto']['archivo']['error'] == 0 ) {
				# Reconocer cabecera e idenitficador
				if ($this->request->data['VentaDetalleProducto']['archivo']['error'] != 0) {
					$this->Session->setFlash('El archivo contiene errores o está dañado.', null, array(), 'danger');
					$this->redirect(array('action' => 'ajustarInventarioMasivo'));
				}

				$ext = pathinfo($this->request->data['VentaDetalleProducto']['archivo']['name'], PATHINFO_EXTENSION);

				if (!in_array($ext, $tipoPermitido)) {
					$this->Session->setFlash('El formato '.$ext.' no es válido. Los formatos permitidos son: ' . implode($tipoPermitido, ','), null, array(), 'danger');
					$this->redirect(array('action' => 'ajustarInventarioMasivo'));
				}


				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->data['VentaDetalleProducto']['archivo']['tmp_name']);
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				
				if (isset($sheetData[1])) {
					foreach ($sheetData[1] as $k => $cabecera) {
						$datos['options'][$k] = $cabecera;
					}						
				}
				
				$datos['data'] = $sheetData;
				
				$this->Session->write('ajustarInventarioMasivo', $datos);

			}else{

				$dataToSave = array();	

				foreach ($this->request->data['Indice'] as $a => $i) {
					if (empty($i)) {
						unset($this->request->data['Indice'][$a]);
					}
				}

				# Se obtienen los índices para cada elemento
				$columna_id_productos = array_search('id_producto', $this->request->data['Indice']); 
				$columna_cantidad     = array_search('stock', $this->request->data['Indice']);

				if (empty($columna_id_productos) || empty($columna_cantidad) || empty($this->request->data['VentaDetalleProducto']['bodega'])) {
					$this->Session->setFlash('Falta indicar la columna de productos, cantidad y/o bodega.', null, array(), 'danger');
					$this->redirect(array('action' => 'ajustarInventarioMasivo'));
				}

				if (!empty($this->Session->read('ajustarInventarioMasivo.data'))) {
					
					foreach ($this->Session->read('ajustarInventarioMasivo.data') as $indice => $valor) {
						if (empty($valor[$columna_id_productos]) || empty($valor[$columna_cantidad]) || $indice == 1) {
							continue;
						}

						# Datos necesarios para reaizar un ingreso
						$dataToSave[$indice]['id_producto'] = $valor[$columna_id_productos];
						$dataToSave[$indice]['cantidad']    = $valor[$columna_cantidad];
						$dataToSave[$indice]['bodega_id']   = $this->request->data['VentaDetalleProducto']['bodega']; 

					}

				}

				if (empty($dataToSave)) {
					$this->Session->setFlash('No se encontraron valores para actualizar.', null, array(), 'warning');
					$this->redirect(array('action' => 'ajustarInventarioMasivo'));
				}
				
				# Guardamos el II
				$result = ClassRegistry::init('Bodega')->ajustarInventarioMasivo($dataToSave);
				
				if (isset($result['errores'])) {
					$this->Session->setFlash($this->crearAlertaUl($result['errores'], 'Errores encontrados'), null, array(), 'danger');
				}

				if (isset($result['procesados'])) {
					$this->Session->setFlash(sprintf('%d items procesados con éxito.', $result['procesados']), null, array(), 'success');
				}

				$this->Session->delete('ajustarInventarioMasivo');

			}

		}

		$bodegas = ClassRegistry::init('Bodega')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Listado de Movimientos', '/ventaDetalleProductos/movimientos');
		BreadcrumbComponent::add('Ajustar inventario masivo');

		$this->set(compact('bodegas'));

	}


	public function admin_moverInventarioMasivo()
	{	
		$tipoPermitido = array(
			'xlsx',
			'xls',
			'csv'
		);

		$datos = array();

		if ( $this->request->is('post') || $this->request->is('put')) {

			ini_set('max_execution_time', 0);

			if ($this->request->data['VentaDetalleProducto']['archivo']['error'] == 0 ) {
				# Reconocer cabecera e idenitficador
				if ($this->request->data['VentaDetalleProducto']['archivo']['error'] != 0) {
					$this->Session->setFlash('El archivo contiene errores o está dañado.', null, array(), 'danger');
					$this->redirect(array('action' => 'moverInventarioMasivo'));
				}

				$ext = pathinfo($this->request->data['VentaDetalleProducto']['archivo']['name'], PATHINFO_EXTENSION);

				if (!in_array($ext, $tipoPermitido)) {
					$this->Session->setFlash('El formato '.$ext.' no es válido. Los formatos permitidos son: ' . implode($tipoPermitido, ','), null, array(), 'danger');
					$this->redirect(array('action' => 'moverInventarioMasivo'));
				}


				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->data['VentaDetalleProducto']['archivo']['tmp_name']);
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				
				if (isset($sheetData[1])) {
					foreach ($sheetData[1] as $k => $cabecera) {
						$datos['options'][$k] = $cabecera;
					}						
				}
				
				$datos['data'] = $sheetData;
				
				$this->Session->write('moverInventarioMasivo', $datos);

			}else{

				$dataToSave = array();	

				foreach ($this->request->data['Indice'] as $a => $i) {
					if (empty($i)) {
						unset($this->request->data['Indice'][$a]);
					}
				}

				# Se obtienen los índices para cada elemento
				$columna_id_productos   = array_search('id_producto', $this->request->data['Indice']); 
				$columna_cantidad       = array_search('stock', $this->request->data['Indice']);
				$columna_bodega_origen  = array_search('bodega_origen', $this->request->data['Indice']);
				$columna_bodega_destino = array_search('bodega_destino', $this->request->data['Indice']);

				if (empty($columna_id_productos) || empty($columna_cantidad) || empty($columna_bodega_destino) || empty($columna_bodega_origen)) {
					$this->Session->setFlash('Falta indicar la columna de productos, bodega origen, bodega destino y/ cantidad.', null, array(), 'danger');
					$this->redirect(array('action' => 'moverInventarioMasivo'));
				}

				if (!empty($this->Session->read('moverInventarioMasivo.data'))) {
					
					foreach ($this->Session->read('moverInventarioMasivo.data') as $indice => $valor) {
						if (empty($valor[$columna_id_productos]) || empty($valor[$columna_cantidad]) || empty($valor[$columna_bodega_destino]) || empty($valor[$columna_bodega_origen]) || $indice == 1) {
							continue;
						}

						# Datos necesarios para reaizar un ingreso
						$dataToSave[$indice]['id_producto'] = $valor[$columna_id_productos];
						$dataToSave[$indice]['cantidad']    = $valor[$columna_cantidad];
						$dataToSave[$indice]['bodega_id_origen']   = $valor[$columna_bodega_origen];
						$dataToSave[$indice]['bodega_id_destino']   = $valor[$columna_bodega_destino];

					}

				}

				if (empty($dataToSave)) {
					$this->Session->setFlash('No se encontraron valores para actualizar.', null, array(), 'warning');
					$this->redirect(array('action' => 'moverInventarioMasivo'));
				}
				
				# Guardamos el II
				$result = ClassRegistry::init('Bodega')->moverProductoBodegaMasivo($dataToSave);
				
				if (isset($result['errores'])) {
					$this->Session->setFlash($this->crearAlertaUl($result['errores'], 'Errores encontrados'), null, array(), 'danger');
				}

				if (isset($result['procesados'])) {
					$this->Session->setFlash(sprintf('%d items procesados con éxito.', $result['procesados']), null, array(), 'success');
				}

				$this->Session->delete('moverInventarioMasivo');

			}

		}

		$columnas = array(
			'id_producto' => 'Id del producto',
			'stock' => 'Cantidad a mover',
			'bodega_origen' => 'Bodega origen',
			'bodega_destino' => 'Bodega destino'
		);

		$bodegas = ClassRegistry::init('Bodega')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Listado de Movimientos', '/ventaDetalleProductos/movimientos');
		BreadcrumbComponent::add('Mover productos bodegas');

		$this->set(compact('bodegas', 'columnas'));

	}


	public function admin_inventarioInicial()
	{	
		$tipoPermitido = array(
			'xlsx',
			'xls',
			'csv'
		);

		$datos = array();

		if ( $this->request->is('post') || $this->request->is('put')) {

			ini_set('max_execution_time', 0);

			if ($this->request->data['VentaDetalleProducto']['archivo']['error'] == 0 ) {
				# Reconocer cabecera e idenitficador
				if ($this->request->data['VentaDetalleProducto']['archivo']['error'] != 0) {
					$this->Session->setFlash('El archivo contiene errores o está dañado.', null, array(), 'danger');
					$this->redirect(array('action' => 'inventarioInicial'));
				}

				$ext = pathinfo($this->request->data['VentaDetalleProducto']['archivo']['name'], PATHINFO_EXTENSION);

				if (!in_array($ext, $tipoPermitido)) {
					$this->Session->setFlash('El formato '.$ext.' no es válido. Los formatos permitidos son: ' . implode($tipoPermitido, ','), null, array(), 'danger');
					$this->redirect(array('action' => 'inventarioInicial'));
				}


				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->data['VentaDetalleProducto']['archivo']['tmp_name']);
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				
				if (isset($sheetData[1])) {
					foreach ($sheetData[1] as $k => $cabecera) {
						$datos['options'][$k] = $cabecera;
					}						
				}
				
				$datos['data'] = $sheetData;
				
				$this->Session->write('InventarioInicial', $datos);

			}else{

				$dataToSave = array();	

				foreach ($this->request->data['Indice'] as $a => $i) {
					if (empty($i)) {
						unset($this->request->data['Indice'][$a]);
					}
				}

				# Se obotienen los índices para cada elemento
				$columna_id_productos = array_search('id_producto', $this->request->data['Indice']); 
				$columna_cantidad     = array_search('stock', $this->request->data['Indice']);
				$columna_costo        = array_search('costo', $this->request->data['Indice']);

				if (empty($columna_id_productos) || empty($columna_cantidad) || empty($columna_costo) || empty($this->request->data['VentaDetalleProducto']['bodega'])) {
					$this->Session->setFlash('Falta indicar la columna de productos y/o cantidades.', null, array(), 'danger');
					$this->redirect(array('action' => 'inventarioInicial'));
				}

				if (!empty($this->Session->read('InventarioInicial.data'))) {
					
					foreach ($this->Session->read('InventarioInicial.data') as $indice => $valor) {
						if (empty($valor[$columna_id_productos]) || empty($valor[$columna_cantidad]) || $indice == 1) {
							continue;
						}

						# Datos necesarios para reaizar un ingreso
						$dataToSave[$indice]['id_producto'] = $valor[$columna_id_productos];
						$dataToSave[$indice]['cantidad']    = $valor[$columna_cantidad];
						$dataToSave[$indice]['precio_costo']= $valor[$columna_costo];
						$dataToSave[$indice]['bodega_id']   = $this->request->data['VentaDetalleProducto']['bodega']; 

					}

				}

				if (empty($dataToSave)) {
					$this->Session->setFlash('No se encontraron valores para actualizar.', null, array(), 'warning');
					$this->redirect(array('action' => 'inventarioInicial'));
				}
				
				# Guardamos el II
				$result = ClassRegistry::init('Bodega')->cargaInicialBodega($dataToSave);
				
				if (isset($result['errores'])) {
					$this->Session->setFlash($this->crearAlertaUl($result['errores'], 'Errores encontrados'), null, array(), 'danger');
				}

				if (isset($result['procesados'])) {
					$this->Session->setFlash(sprintf('%d items procesados con éxito.', $result['procesados']), null, array(), 'success');
				}

				$this->Session->delete('InventarioInicial');

			}

		}

		$bodegas = ClassRegistry::init('Bodega')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Listado de Movimientos', '/ventaDetalleProductos/movimientos');
		BreadcrumbComponent::add('Inventario inicial');

		$this->set(compact('bodegas'));

	}


	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->VentaDetalleProducto->create();
			if ( $this->VentaDetalleProducto->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$bodegas                   = $this->VentaDetalleProducto->Bodega->find('list', array('conditions' => array('activo' => 1)));
		$proveedores               = $this->VentaDetalleProducto->Proveedor->find('list', array('conditions' => array('activo' => 1))); 
		$precioEspecificoProductos = $this->VentaDetalleProducto->PrecioEspecificoProducto->find('list', array('conditions' => array('activo' => 1)));
		$tipoDescuento 				= array(1 => '%', 0 => '$');
		$marcas 					= ClassRegistry::init('Marca')->find('list');

		BreadcrumbComponent::add('Productos', '/ventaDetalleProductos');
		BreadcrumbComponent::add('Agregar');

		$this->set(compact('bodegas', 'proveedores', 'precioEspecificoProductos', 'tipoDescuento', 'marcas'));
	}

	public function admin_edit($id = null)
	{	
		if ( ! $this->VentaDetalleProducto->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			
			// Limpiar
			//$this->VentaDetalleProducto->BodegasVentaDetalleProducto->deleteAll(array('venta_detalle_producto_id' => $id));
			$this->VentaDetalleProducto->PrecioEspecificoProducto->deleteAll(array('venta_detalle_producto_id' => $id));

			if ( $this->VentaDetalleProducto->saveAll($this->request->data) )
			{		

				if ($this->request->data['VentaDetalleProducto']['actualizar_canales']) {

					$resultadoStock = $this->actualizar_canales_stock($this->request->data['VentaDetalleProducto']['id_externo'], $this->request->data['VentaDetalleProducto']['cantidad_virtual']);

					if (!empty($resultadoStock['errors'])) {
						$this->Session->setFlash($this->crearAlertaUl($resultadoStock['errors']), null, array(), 'danger');
					}

					if (!empty($resultadoStock['successes'])) {
						$this->Session->setFlash($this->crearAlertaUl($resultadoStock['successes']), null, array(), 'success');
					}


				}

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
			$this->request->data	= $this->VentaDetalleProducto->find('first', array(
				'conditions'	=> array('VentaDetalleProducto.id' => $id),
				'contain' => array(
					'VentaDetalle',
					'Bodega' => array(
						'fields' => array(
							'Bodega.id',
							'Bodega.nombre'
						)
					),
					'Proveedor' => array(
						'fields' => array(
							'Proveedor.id',
							'Proveedor.nombre'
						)
					),
					'Marca' => array(
						'fields' => array(
							'Marca.id',
							'Marca.nombre'
						)
					),
					'PrecioEspecificoProducto'
				),
				'fields' => array(
					'VentaDetalleProducto.id',
					'VentaDetalleProducto.id_externo',
					'VentaDetalleProducto.nombre',
					'VentaDetalleProducto.precio_costo',
					'VentaDetalleProducto.cantidad_virtual',
					'VentaDetalleProducto.codigo_proveedor'
				)
			));
		}
		
		$precio_costo_final = ClassRegistry::init('VentaDetalleProducto')->obtener_precio_costo($id);
		
		$canales 				   = $this->verificar_canales($this->request->data['VentaDetalleProducto']['id_externo']);
		$bodegas                   = $this->VentaDetalleProducto->Bodega->find('list', array('conditions' => array('activo' => 1)));
		$proveedores               = $this->VentaDetalleProducto->Proveedor->find('list', array('conditions' => array('activo' => 1))); 
		$precioEspecificoProductos = $this->VentaDetalleProducto->PrecioEspecificoProducto->find('list', array('conditions' => array('activo' => 1)));
		$tipoDescuento 			   = array(1 => '%', 0 => '$');
		$marcas 				   = ClassRegistry::init('Marca')->find('list');

		# ventas
		$this->request->data['VentaDetalleProducto']['total_vendidos'] 	= array_sum(Hash::extract($this->request->data['VentaDetalle'], '{n}.cantidad'));
		$this->request->data['VentaDetalleProducto']['pvp'] 			= @(array_sum(Hash::extract($this->request->data['VentaDetalle'], '{n}.precio')) / array_sum(Hash::extract($this->request->data['VentaDetalle'], '{n}.cantidad')));

		# Inventario
		$movimientosBodega 		   = $this->VentaDetalleProducto->Bodega->BodegasVentaDetalleProducto->find('all', array(
			'conditions' => array(
				'venta_detalle_producto_id' => $id
			),
			'order' => array('BodegasVentaDetalleProducto.fecha' => 'desc'),
		));

		# PMP
		$this->request->data['VentaDetalleProducto']['pmp_global'] = ClassRegistry::init('Bodega')->obtener_pmp_por_id($id);

		foreach ($bodegas as $ib => $b) {
			$this->request->data['VentaDetalleProducto']['Inventario'][$ib]['bodega_id'] = $ib;
			$this->request->data['VentaDetalleProducto']['Inventario'][$ib]['bodega_nombre'] = $b;

			$iosBodega = Hash::extract($this->request->data['Bodega'], '{n}.BodegasVentaDetalleProducto[bodega_id='.$ib.']');

			$this->request->data['VentaDetalleProducto']['Inventario'][$ib]['total'] = array_sum(Hash::extract($iosBodega, '{n}[io=IN].cantidad')) - array_sum(Hash::extract($iosBodega, '{n}[io=ED].cantidad'));
			$this->request->data['VentaDetalleProducto']['Inventario'][$ib]['pmp'] = ClassRegistry::init('Bodega')->obtener_pmp_por_id($id, $ib);
		}

		if (!in_array(1, Hash::extract($canales, '{s}.{n}.existe'))) {
			$this->Session->setFlash('El producto no se encontró en ningún canal de venta.', null, array(), 'warning');
		}
		
		BreadcrumbComponent::add('Listado de productos', '/ventaDetalleProductos');
		BreadcrumbComponent::add('Editar');

		$this->set(compact('bodegas', 'proveedores', 'precioEspecificoProductos', 'tipoDescuento', 'canales', 'marcas', 'movimientosBodega', 'precio_costo_final'));
	}


	public function admin_guardar_proveedores_producto()
	{	
		$noGuardados = 0;

		$redirect = '/';

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			$redirect = $this->request->data['Form']['redirect_url'];

			unset($this->request->data['Form']['redirect_url']);

			foreach ($this->request->data['Form'] as $key => $value) {
				if(!$this->VentaDetalleProducto->saveAll($value)){
					$noGuardados++;
				}
			}
		}

		if ($noGuardados > 0) {
			$this->Session->setFlash(sprintf('No fue posible guardar la relación en %d items', $noGuardados), null, array(), 'warning');	
		}else{
			$this->Session->setFlash('¡Excelente! Todos los productos tiene su proveedor y marca relacionado.', null, array(), 'success');
		}

		$this->redirect($redirect);

	}

	public function descontar_stock_virtual($id, $id_externo, $nuevaCantidad, $excluir = array(), $devueltos = null)
	{	

		$bodega = ClassRegistry::init('Bodega')->find('first');

		$this->VentaDetalleProducto->id = $id;

		if ($this->VentaDetalleProducto->saveField('cantidad_virtual', $nuevaCantidad)) {
			
			$res = $this->actualizar_canales_stock($id_externo, $nuevaCantidad, $excluir);

			if (!empty($res['errors'])) {
				return false;
			}else{
				return true;
			}
		}

		return false;
	}


	public function admin_delete($id = null)
	{
		$this->VentaDetalleProducto->id = $id;
		if ( ! $this->VentaDetalleProducto->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->VentaDetalleProducto->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{	
		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
		set_time_limit(-1);

		$datos			= $this->VentaDetalleProducto->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->VentaDetalleProducto->_schema);
		$modelo			= $this->VentaDetalleProducto->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	public function admin_buscar($palabra = '')
	{	
		$respuesta = array();

		if ($this->request->is('get') && !empty($palabra)) {
			$productos = $this->VentaDetalleProducto->find('all', array(
				'conditions' => array(
					'OR' => array(
						'VentaDetalleProducto.nombre LIKE "%'.$palabra.'%"',
						'VentaDetalleProducto.codigo_proveedor LIKE "%'.$palabra.'%"'
					)
				),
				'contain' => array(
					'PrecioEspecificoProducto' => array(
						'conditions' => array(
							'OR' => array(
								'PrecioEspecificoProducto.descuento_infinito' => 1,
								'AND' => array(
									array('PrecioEspecificoProducto.fecha_inicio <=' => date('Y-m-d')),
									array('PrecioEspecificoProducto.fecha_termino >=' => date('Y-m-d')),
								)
							)
						),
						'order' => array(
							'PrecioEspecificoProducto.id' => 'DESC'
						)
					),
					'Marca' => array(
						'PrecioEspecificoMarca' => array(
							'conditions' => array(
								'OR' => array(
									'PrecioEspecificoMarca.descuento_infinito' => 1,
									'AND' => array(
										array('PrecioEspecificoMarca.fecha_inicio <=' => date('Y-m-d')),
										array('PrecioEspecificoMarca.fecha_termino >=' => date('Y-m-d')),
									)
								)
							),
							'order' => array(
								'PrecioEspecificoMarca.id' => 'DESC'
							)
						)
					)
				),
				'limit' => 10
			));

			foreach ($productos as $i => $p) {
				$descuentos = ClassRegistry::init('VentaDetalleProducto')::obtener_descuento_por_producto($p, true);

				$productos[$i]['VentaDetalleProducto']['total_descuento']  = $descuentos['total_descuento'];
				$productos[$i]['VentaDetalleProducto']['nombre_descuento'] = $descuentos['nombre_descuento'];
				$productos[$i]['VentaDetalleProducto']['valor_descuento']  = $descuentos['valor_descuento']; 
			}
			

			foreach ($productos as $key => $value) {
				$respuesta[$key]['id']           	= $value['VentaDetalleProducto']['id'];
				$respuesta[$key]['value']        	= $value['VentaDetalleProducto']['nombre'];
				$respuesta[$key]['codigo']       	= $value['VentaDetalleProducto']['codigo_proveedor'];
				$respuesta[$key]['precio_costo'] 	= $value['VentaDetalleProducto']['precio_costo'];
				$respuesta[$key]['descuento'] 	 	= round($value['VentaDetalleProducto']['total_descuento']);
				$respuesta[$key]['tipo_descuento'] 	= (int)0;
				$respuesta[$key]['nombre_descuento'] = $value['VentaDetalleProducto']['nombre_descuento'];

			}
		}

		if (empty($palabra) || empty($respuesta)) {
			echo json_encode(array('0' => array('id' => '', 'value' => 'No se encontraron coincidencias')));
    		exit;
		}
	
		echo json_encode($respuesta);
		exit;
	}


	private function validar_csv_masivo($cabeceras = array())
	{	
		$_cabeceras = array(
			'id',
			'nombre',
			'codigo proveedor',
			'precio costo',
			'stock virtual',
			'tipo de descuento',
			'nombre descuento',
			'descuento',
			'fecha inicio',
			'fecha termino',
			'activo'
		);

		$res = array();

		foreach ($cabeceras as $ic => $c) {
			if (in_array($c, $_cabeceras)) {
				$res['found'][] = $c;
			}else{
				$res['notfound'][] = $c;
			}
		}

		return $res;
	}


	public function admin_carga_masiva()
	{	

		$resultadoCabeceras     = array();
		$productos              = array();
		$resultadoActualizacion = array();

		if ($this->request->is('post')) {

			set_time_limit(0);
			
			if (isset($this->request->data['CargaMasiva']) && empty($this->request->data['CargaMasiva']['csv']) && !isset($this->request->data['ConfirmarCargaMasiva'])) {
				$this->Session->setFlash('No se cargó CSV.', null, array(), 'danger');
				$this->redirect(array('action' => 'carga_masiva'));
			}elseif ( isset($this->request->data['CargaMasiva']) ) {

				$delimitador = $this->request->data['CargaMasiva']['delimitador'];

				$data = $this->csv_to_array($this->request->data['CargaMasiva']['csv']['tmp_name'], $delimitador);
				
				if (!empty($data)) {
					foreach ($data as $i => $item) {
						$resultadoCabeceras 	= $this->validar_csv_masivo(array_keys($item));
						$productos[$i]          = $item; 
					}
				}

			}elseif (  isset($this->request->data['ConfirmarCargaMasiva']) ){

				$contSuccess  = 0;
				$contFailures = 0;

				foreach ($this->request->data['ConfirmarCargaMasiva'] as $ic => $campo) {

					$dataTosave = array();

					if (is_integer($ic) && isset($campo['PrecioEspecificoProducto'])) {
						$dataTosave['PrecioEspecificoProducto'] = $campo['PrecioEspecificoProducto'];
					}

					if (is_integer($ic) && isset($campo['VentaDetalleProducto'])) {
						$dataTosave['VentaDetalleProducto'] = $campo['VentaDetalleProducto'];
					}

					// Si el campo no existe no se intenta guardar
					if ( is_integer($ic) && !$this->VentaDetalleProducto->exists($dataTosave['VentaDetalleProducto']['id']) ) {
						$dataTosave = array();
					}

					if (!empty($dataTosave)) {
						
						// Limpiar
						//$this->VentaDetalleProducto->BodegasVentaDetalleProducto->deleteAll(array('venta_detalle_producto_id' => $campo['VentaDetalleProducto']['id']));
						$this->VentaDetalleProducto->PrecioEspecificoProducto->deleteAll(array('venta_detalle_producto_id' => $campo['VentaDetalleProducto']['id']));

						if ($this->VentaDetalleProducto->saveAll($dataTosave)) {
							$contSuccess++;

							$resultadoActualizacion['success']['total'] =  $contSuccess;

							if (isset($this->request->data['ConfirmarCargaMasiva']['actualizar_canales']) && isset($campo['VentaDetalleProducto']['cantidad_virtual'])) {

								$producto = $this->VentaDetalleProducto->find('first', array('conditions' => array('id' => $campo['VentaDetalleProducto']['id']), 'fields' => array('id_externo') ));

								$resultadoStock = $this->actualizar_canales_stock($producto['VentaDetalleProducto']['id_externo'], $campo['VentaDetalleProducto']['cantidad_virtual']);

								if (!empty($resultadoStock['errors'])) {
									$this->Session->setFlash($this->crearAlertaUl($resultadoStock['errors']), null, array(), 'danger');
								}

								if (!empty($resultadoStock['successes'])) {
									$this->Session->setFlash($this->crearAlertaUl($resultadoStock['successes']), null, array(), 'success');
								}
							}

						}else{
							$contFailures++;
							$resultadoActualizacion['errors']['total']  =  $contFailures;
							$resultadoActualizacion['errors']['message'][] =  '#' . $campo['VentaDetalleProducto']['id'] . ' ' . $campo['VentaDetalleProducto']['nombre'];
						}
					}
				}

			}
		}

		if (isset($resultadoCabeceras['notfound']) && !empty($resultadoCabeceras['notfound'])) :
			$this->Session->setFlash($this->crearAlertaUl($resultadoCabeceras['notfound'], 'Cabeceras no encontradas'), null, array(), 'warning');
		endif;

		if (isset($resultadoActualizacion['success']) && !empty($resultadoActualizacion['success'])) :
			$this->Session->setFlash(sprintf('%d producto/s actualizado/s con éxito.', $resultadoActualizacion['success']['total']), null, array(), 'success');
		endif;

		if (isset($resultadoActualizacion['errors']) && !empty($resultadoActualizacion['errors']) ) :
			$this->Session->setFlash($this->crearAlertaUl( $resultadoActualizacion['errors']['message'], sprintf('%d producto/s no actualizado/s: ', $resultadoActualizacion['errors']['total'] ) ), null, array(), 'danger');
		endif;

		$tipoDescuento 			   = array(1 => '%', 0 => '$');

		BreadcrumbComponent::add('Productos', '/ventaDetalleProductos');
		BreadcrumbComponent::add('Actualización Masiva');

		$this->set(	compact('resultadoCabeceras', 'productos', 'tipoDescuento') );

	}


	/**
	 * Verifica la existencia de un producto en los distintos canales e ventas
	 * @param  int $id_externo Identificador del producto
	 * @return array             detalle de los canales
	 */
	public function verificar_canales ($id_externo)
	{	

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array(
				'Tienda.activo' => 1
			),
			'contain' => array(
				'Marketplace' => array(
					'MarketplaceTipo'
				)
			),
			'fields' => array(
				'Tienda.apiurl_prestashop',
				'Tienda.apiurl_prestashop',
				'Tienda.apikey_prestashop',
				'Tienda.nombre'
			)
		));
		
		$existeCanales = array();

		foreach ($tiendas as $it => $tienda) {

			# Cliente Prestashop
			$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

			$existeCanales['Prestashop'][$tienda['Tienda']['id']]           = $this->Prestashop->prestashop_producto_existe($id_externo);
			$existeCanales['Prestashop'][$tienda['Tienda']['id']]['nombre'] = $tienda['Tienda']['nombre'];
			$existeCanales['Prestashop'][$tienda['Tienda']['id']]['canal']  = $tienda['Tienda'];

			foreach ($tienda['Marketplace'] as $im => $marketplace) {
				
				switch ($marketplace['marketplace_tipo_id']) {

					case 1: # Linio
						
						# cliente Linio
						$this->Linio->crearCliente( $marketplace['api_host'], $marketplace['api_user'], $marketplace['api_key'] );

						$existeCanales['Linio'][$marketplace['id']]           = $this->Linio->linio_producto_existe($id_externo);
						$existeCanales['Linio'][$marketplace['id']]['nombre'] = $marketplace['nombre'];
						$existeCanales['Linio'][$marketplace['id']]['canal']  = $marketplace;

						sleep(3);

						break;
					
					case 2: # Mercado libre
						
						# cliente Meli
						$this->MeliMarketplace->crearCliente( $marketplace['api_user'], $marketplace['api_key'], $marketplace['access_token'], $marketplace['refresh_token'] );
						$result = $this->MeliMarketplace->mercadolibre_conectar('', $marketplace);
						
						if ($result['success']) {
							$existeCanales['Mercadolibre'][$marketplace['id']]           = $this->MeliMarketplace->mercadolibre_producto_existe($id_externo,$marketplace['seller_id']);
							$existeCanales['Mercadolibre'][$marketplace['id']]['nombre'] = $marketplace['nombre'];
							$existeCanales['Mercadolibre'][$marketplace['id']]['canal']  = $marketplace;	
						}			

						break;
				}

			}

		}

		return $existeCanales;
	
	}


	/**
	 * Atualiza el stock de un producto en los distintos canales de ventas
	 * @param  int $id_externo  ID del producto / SKU / CUSTOM_FIELD_ID
	 * @param  int $nuevo_stock Nueva cantidad
	 * @param  array  $excluir     Permite excluir canales. PRestashop-Linio-Mercadolibre
	 * @return string 		Resultado de la operación
	 */
	public function actualizar_canales_stock($id_externo, $nuevo_stock, $excluir = array())
	{	
		$this->Prestashop      = $this->Components->load('Prestashop');
		$this->Linio           = $this->Components->load('Linio');
		$this->MeliMarketplace = $this->Components->load('MeliMarketplace');

		$canales = $this->verificar_canales($id_externo);

		$result = array(
			'errors' => array(),
			'successes' => array()
		);

		foreach ($canales as $ic => $canal) {

			if ($ic === 'Prestashop' ) {
				foreach ($canal as $i => $c) {

					# si se excluye se termina
					if (isset($excluir['Prestashop'][$i])) {
						break;
					}

					if (!$c['existe']) {
						continue;
					}

					$actualizar = $this->Prestashop->prestashop_actualizar_stock($c['item']['associations']['stock_availables']['stock_available']['id'], $nuevo_stock);

					if ($actualizar && $c['existe']) {
						$result['successes'][] = sprintf('Item %d actualizado con éxto en %s', $id_externo, $c['nombre']);					
					}else{
						$result['errors'][] = sprintf('Error al actualizar el item %d en %s o no existe en el canal', $id_externo, $c['nombre']);
					}
 
				}
			}

			if ($ic === 'Linio') {

				foreach ($canal as $i => $c) {
					
					# si se excluye se termina
					if (isset($excluir['Linio'][$i])) {
						break;
					}
					
					if (!$c['existe']) {
						continue;
					}

					$actualizar = $this->Linio->actualizar_stock_producto(array(), $id_externo, $nuevo_stock);

					if ($actualizar['code'] == 200 && $c['existe']) {
						$result['successes'][] = sprintf('Item %d actualizado con éxto en %s', $id_externo, $c['nombre']);					
					}else{
						$result['errors'][] = sprintf('Error al actualizar el item %d en %s o no existe en el canal', $id_externo, $c['nombre']);
					}

					sleep(3);

				}
			}

			if ($ic === 'Mercadolibre' ) {
				foreach ($canal as $i => $c) {

					# si se excluye se termina
					if (isset($excluir['Mercadolibre'][$i])) {
						break;
					}

					if (!$c['existe']) {
						continue;
					}

					$actualizar = $this->MeliMarketplace->mercadolibre_actualizar_stock($c['item']['id'], $nuevo_stock);
					
					if ($actualizar['httpCode'] == 200) {
						$result['successes'][] = sprintf('Item %d actualizado con éxto en %s', $id_externo, $c['nombre']);					
					}else{
						$result['errors'][] = sprintf('Error al actualizar el item %d en %s o no existe en el canal', $id_externo, $c['nombre']);
					}
				}
			}
		
		}

		return $result;
	}

	
	/**
	 * 	Obitne los productos desde prestashop
	 * @return [type] [description]
	 */
	public function admin_obtener_productos_base()
	{	
		# Se carga el componente directamente para ser usado por la consola
		$this->Prestashop = $this->Components->load('Prestashop');

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array(
				'Tienda.activo' => 1
			),
			'contain' => array(
				'Marketplace' => array(
					'MarketplaceTipo'
				)
			),
			'fields' => array(
				'Tienda.apiurl_prestashop',
				'Tienda.apiurl_prestashop',
				'Tienda.apikey_prestashop',
				'Tienda.nombre'
			)
		));


		foreach ($tiendas as $it => $tienda) {

			# Cliente Prestashop
			$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

			$productos = $this->Prestashop->prestashop_obtener_productos();

			$productosLocales = array();
			$arrMessage 	  = array( 'No se logró guardar los productos en el sistema.' );

			foreach ($productos['product'] as $ip => $p) {
				
				# Verificamos que exista en la BD local
				$local = $this->VentaDetalleProducto->find('first', array('conditions' => array('id_externo' => $p['id']), 'fields' => array('id')));

				# Editar existente
				if (!empty($local)) {
					$productosLocales[$ip]['VentaDetalleProducto']['id'] = $local['VentaDetalleProducto']['id'];
				}else{
					$productosLocales[$ip]['VentaDetalleProducto']['id'] = $p['id'];
				}

				$stock = $this->Prestashop->prestashop_obtener_stock_producto($p['id']);

				$productosLocales[$ip]['VentaDetalleProducto']['id_externo']       = $p['id'];
				$productosLocales[$ip]['VentaDetalleProducto']['codigo_proveedor'] = $p['supplier_reference'];
				$productosLocales[$ip]['VentaDetalleProducto']['marca_id'] 		   = $p['id_manufacturer'];
				$productosLocales[$ip]['VentaDetalleProducto']['nombre']           = $p['name']['language'];
				$productosLocales[$ip]['VentaDetalleProducto']['cantidad_virtual'] = 0;

			}

		}

		if (!empty($productosLocales)) {
				
			if ($this->VentaDetalleProducto->saveMany($productosLocales))
			{
				$arrMessage = array( sprintf('Se han creado/modificado %d productos', count($productosLocales)) );
			}
		}
		

		return $arrMessage;
	}


	public function admin_obtenerVentas($id = null, $f_inicio = null, $f_final = null) 
	{	
		$query = array(
			'contain' => array(
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.permitir_dte'
					)
				),
				'VentaDetalle' => array(
					'conditions' => array(
						'VentaDetalle.venta_detalle_producto_id' => $id
					),
					'fields' => array(
						'VentaDetalle.cantidad'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.tienda_id',
				'Venta.marketplace_id',
				'DATE_FORMAT(Venta.fecha_venta, "%Y-%m") AS fecha'
			)
		);

		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-01-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{

			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		# Fechas
		$query = array_replace_recursive($query, array(
			'conditions' => array(
				'Venta.fecha_venta BETWEEN ? AND ?' => array($f_inicio, $f_final)
			)
		));

		# Ventas
		$ventas = $this->VentaDetalleProducto->VentaDetalle->Venta->find('all', $query);

		$ventasCanales = array();

		foreach ($ventas as $iv => $venta) {

			if (!isset($venta['VentaEstado']['permitir_dte'])) {
				continue;
			}

			# Descartar ventas no pagadas
			if (!$venta['VentaEstado']['permitir_dte']) {
				continue;
			}

			# Verificamos el canala de las ventas
			if (!empty($venta['Venta']['marketplace_id'])) {

				$marketplace = ClassRegistry::init('Marketplace')->field('nombre', $venta['Venta']['marketplace_id']);

				if (!isset($ventasCanales[$iv][$venta[0]['fecha']][$marketplace])) {
					$ventasCanales[$venta[0]['fecha']][$marketplace] = array_sum(Hash::extract($venta, 'VentaDetalle.{n}.cantidad'));	
				}else{
					$ventasCanales[$venta[0]['fecha']][$marketplace] = $ventasCanales[$venta[0]['fecha']][$marketplace] + array_sum(Hash::extract($venta, 'VentaDetalle.{n}.cantidad'));
				}				
			}else{

				$tienda = ClassRegistry::init('Tienda')->field('nombre', $venta['Venta']['tienda_id']);

				if (!isset($ventasCanales[$venta[0]['fecha']][$tienda])) {
					$ventasCanales[$venta[0]['fecha']][$tienda] = array_sum(Hash::extract($venta, 'VentaDetalle.{n}.cantidad'));	
				}else{
					$ventasCanales[$venta[0]['fecha']][$tienda] = $ventasCanales[$venta[0]['fecha']][$tienda] + array_sum(Hash::extract($venta, 'VentaDetalle.{n}.cantidad'));
				}	

			}
		}
		

		# Armamos el json para la gráfica

		$res = array(
			'xkeys' => 'y'
		);
		if (!empty($ventasCanales)) {
			
			$pos = 0;
			foreach ($ventasCanales as $fecha => $canal) {
				if (empty($canal))
					continue;

				$res['data'][$pos]['y'] = $fecha;

				$posCanal = 0;
				foreach ($canal as $nombre => $cantidad) {
					$res['data'][$pos][abecedario($posCanal, true)] = $cantidad;
					$res['labels'][$posCanal] = $nombre;
					$res['ykeys'][$posCanal] = abecedario($posCanal, true);
					$res['lineColors'][$posCanal] = '#'.random_color();
					$posCanal++;		
				}	
				$pos++;
			}


		}

		echo json_encode($res);
		exit;
	}


	/**
	 * REST methods
	 */

	public function api_test() {
		App::uses('HttpSocket', 'Network/Http');
		$socket			= new HttpSocket();
		$request		= $socket->post(
			Router::url('/api/producto/view/2.json', true),
			array(
				'Auth' => array(
					'email'		=> 'cristian.rojas@nodriza.cl',
					'secreto'	=> 'FE3n6bYpWDp170gKnsime08Cs'
				),
				'Form' => array(
					'id_externo' => 202010,
					'cantidad_virtual' => 20,
					'precio_costo' => 25990,
					'nombre' => 'Producto de prueba creado mediante curl'
				)
			)
		);

		pr('Response:');
		prx( $request->body );
	}


	/**
	 * Lista todos los productos
	 * Endpoint :  /api/productos.json
	 */
    public function api_index() {

        $productos = $this->VentaDetalleProducto->find('all');

        $this->set(array(
            'productos' => $productos,
            '_serialize' => array('productos')
        ));
    }


    /**
     * Visualiza un producto
     * Endpoint: /api/producto/view/:id_externo.json
     * @param  [type] $id id externo del producto
     */
    public function api_view($id) {
    	
    	if ($this->request->is('post')) {
    		if ( ! $this->VentaDetalleProducto->apiAuth($this->request->data['Auth']) )
			{	
				$response		= array(
					'code'    => $this->VentaDetalleProducto->apiCode, 
					'message' => $this->VentaDetalleProducto->apiCodeMessage
				);

				throw new CakeException($response);
			}else{
				$producto = $this->VentaDetalleProducto->find('first', array(
					'conditions' => array(
						'VentaDetalleProducto.id_externo' => $id
					)
				));

				if (!empty($producto)) {
					$producto['VentaDetalleProducto']['stock_enbodega'] = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodegas($producto['VentaDetalleProducto']['id']);
				}else{
					$producto['VentaDetalleProducto'] = array();
				}

		        $this->set(array(
		            'producto' => $producto['VentaDetalleProducto'],
		            '_serialize' => array('producto')
		        ));
			}	
    	}else{
    		$response		= array(
				'code'    => 300, 
				'message' => 'Request no válido'
			);

			throw new CakeException($response);
    	}
    }


    /**
     * Crear un producto
     * Endpoint: /api/producto/add.json
     */
    public function api_add() {

    	if ($this->request->is('post')) {

    		if ( ! $this->VentaDetalleProducto->apiAuth($this->request->data['Auth']) )
			{	
				$response		= array(
					'code'    => $this->VentaDetalleProducto->apiCode, 
					'message' => $this->VentaDetalleProducto->apiCodeMessage
				);

				throw new CakeException($response);
			}else{

				$data_to_save['VentaDetalleProducto'] = $this->request->data['Form'];
				
				$response = array(
					'code'       => 200,
					'message'    => 'Registro creado con éxito',
					'_serialize' => array('code', 'message')
				);

				$this->VentaDetalleProducto->create();
        		if (!$this->VentaDetalleProducto->save($data_to_save)) {
        			$response = array_replace_recursive($response, array(
						'code' => 330,
						'message' => 'No es posible crear el registro'
						)
		        	);   
        		}

		        $this->set($response);
			}
    	}else{
    		$response		= array(
				'code'    => 300, 
				'message' => 'Request no válido'
			);

			throw new CakeException($response);
    	}

    }


    /**
     * Editar un producto
     * Endpoint: /api/producto/edit/:id_externo.json
     * @param  [type] $id id externo
     */
    public function api_edit($id) {

    	if ($this->request->is('post')) {

    		if ( ! $this->VentaDetalleProducto->apiAuth($this->request->data['Auth']) )
			{	
				$response		= array(
					'code'    => $this->VentaDetalleProducto->apiCode, 
					'message' => $this->VentaDetalleProducto->apiCodeMessage
				);

				throw new CakeException($response);

			}else{

				$data_to_save['VentaDetalleProducto'] = $this->request->data['Form'];
				
				$response = array(
					'code'       => 200,
					'message'    => 'Registro actualizado con éxito',
					'_serialize' => array('code', 'message')
				);

				$producto = $this->VentaDetalleProducto->find('first', array('conditions' => array('VentaDetalleProducto.id_externo' => $id)));

				if (empty($producto)) {

					$response		= array(
						'code'    => 404, 
						'message' => 'Producto no encontrado'
					);

					throw new CakeException($response);
				}

				$this->VentaDetalleProducto->id = $producto['VentaDetalleProducto']['id'];
		        
		        if (!$this->VentaDetalleProducto->save($data_to_save)) {
		        	$response = array_replace_recursive($response, array(
						'code' => 320,
						'message' => 'No es posible actualizar el registro'
						)
		        	);  
		        }else{

		        	if (isset($data_to_save['VentaDetalleProducto']['cantidad_virtual'])) {

		        		$excluir = array();

		        		# Recibe un arreglo con los índices de los canales a excluir ej: Prestashop, Linio, Mercadolibre
		        		if (isset($this->request->data['Excluir'])) {
							$excluir = $this->request->data['Excluir'];
		        		}


		        		$resultadoStock = $this->actualizar_canales_stock($id, $data_to_save['VentaDetalleProducto']['cantidad_virtual'], $excluir);

						if (!empty($resultadoStock['errors'])) {
							$response = array_replace_recursive($response, array(
								'code' => 325,
								'message' => $this->crearAlertaUl($resultadoStock['errors'])
								)
				        	); 
						}
		        	}

		        }

		        $this->set($response);
			}
    	}else{
    		$response		= array(
				'code'    => 300, 
				'message' => 'Request no válido'
			);

			throw new CakeException($response);
    	}
       
    }


    /**
     * Elimina un producto
     * Endpoint: /api/producto/delete/:id_externo.json
     * @param  [type] $id id externo del producto
     */
    public function api_delete($id) {

    	if ($this->request->is('post')) {
    		
    		if ( ! $this->VentaDetalleProducto->apiAuth($this->request->data['Auth']) )
			{	
				$response		= array(
					'code'    => $this->VentaDetalleProducto->apiCode, 
					'message' => $this->VentaDetalleProducto->apiCodeMessage
				);

				throw new CakeException($response);
			}else{
				
				$response = array(
					'code'       => 200,
					'message'    => 'Registro actualizado con éxito',
					'_serialize' => array('code', 'message')
				);

				$producto = $this->VentaDetalleProducto->find('first', array('conditions' => array('VentaDetalleProducto.id_externo' => $id)));

				if (empty($producto)) {

					$response		= array(
						'code'    => 404, 
						'message' => 'Producto no encontrado'
					);

					throw new CakeException($response);
				}

				if (!$this->VentaDetalleProducto->delete($producto['VentaDetalleProducto']['id'])) {
		        	$response = array_replace_recursive($response, array(
						'code' => 340,
						'message' => 'No es posible eliminar el registro'
						)
		        	); 
		        }

		        $this->set($response);

			}
    	}else{
    		$response		= array(
				'code'    => 300, 
				'message' => 'Request no válido'
			);

			throw new CakeException($response);
    	}

    }

}
