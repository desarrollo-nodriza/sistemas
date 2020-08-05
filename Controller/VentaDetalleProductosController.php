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


		foreach ($ventadetalleproductos as $iv => $producto) {
			
			$ventadetalleproductos[$iv]['VentaDetalleProducto']['stock'] = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodegas($producto['VentaDetalleProducto']['id']);
			$ventadetalleproductos[$iv]['VentaDetalleProducto']['costo'] = ClassRegistry::init('VentaDetalleProducto')->obtener_precio_costo($producto['VentaDetalleProducto']['id']);

		}


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
						'VentaDetalleProducto.nombre', 'VentaDetalleProducto.id'
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
		ini_set('memory_limit', -1);

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
			$this->request->data['VentaDetalleProducto']['Total'][$ib]['total'] = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($id, $ib, true);	
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

				if (!ClassRegistry::init('Bodega')->permite_ajuste($id, $m['bodega'])) {
					$errores[] = 'Item #' . $id . ' No puede ser ajustado en la bodega seleccionada, ya que la bodega no tiene registros de ingreso.';
					continue;
				}
		
				if (ClassRegistry::init('Bodega')->ajustarInventario($id, $m['bodega'], $m['ajustar'], $m['costo'], $m['glosa'])) {
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

		# Inventario
		$movimientosBodega = Hash::sort(Hash::extract($this->request->data, 'Bodega.{n}.BodegasVentaDetalleProducto'), '{n}.fecha', 'desc');

		if (!empty($errores)) {
			$this->Session->setFlash($this->crearAlertaUl($errores, 'Errores encontrados'), null, array(), 'danger');
		}

		if (!empty($aceptados)) {
			$this->Session->setFlash($this->crearAlertaUl($aceptados, 'Movimientos correcto'), null, array(), 'success');
			$this->redirect(array('action' => 'edit', $id));
		}

		foreach ($bodegas as $ib => $b) {
			$this->request->data['VentaDetalleProducto']['Total'][$ib]['bodega_id'] = $ib;
			$this->request->data['VentaDetalleProducto']['Total'][$ib]['bodega_nombre'] = $b;
			$this->request->data['VentaDetalleProducto']['Total'][$ib]['pmp'] = ClassRegistry::init('Bodega')->obtener_pmp_por_producto_bodega($id, $ib);	
			$this->request->data['VentaDetalleProducto']['Total'][$ib]['total'] = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($id, $ib, true);	
		}
		
		BreadcrumbComponent::add('Listado de Movimientos', '/ventaDetalleProductos/movimientos');
		BreadcrumbComponent::add('Ajuste de inventario');

		$this->set(compact('bodegas', 'movimientosBodega'));
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
				$columna_precio       = array_search('precio', $this->request->data['Indice']);

				if (empty($columna_id_productos) || empty($columna_cantidad) || empty($this->request->data['VentaDetalleProducto']['bodega'])) {
					$this->Session->setFlash('Falta indicar la columna de productos, cantidad y/o bodega.', null, array(), 'danger');
					$this->redirect(array('action' => 'ajustarInventarioMasivo'));
				}

				if (!empty($this->Session->read('ajustarInventarioMasivo.data'))) {
					
					foreach ($this->Session->read('ajustarInventarioMasivo.data') as $indice => $valor) {
						
						if (empty($valor[$columna_id_productos]) || $indice == 1) {
							continue;
						}

						if (!$this->VentaDetalleProducto->exists($valor[$columna_id_productos])) {
							continue;
						}

						# Datos necesarios para reaizar un ingreso
						$dataToSave[$indice]['id_producto'] = $valor[$columna_id_productos];
						$dataToSave[$indice]['cantidad']    = (empty($valor[$columna_cantidad])) ? 0 : $valor[$columna_cantidad];
						$dataToSave[$indice]['bodega_id']   = $this->request->data['VentaDetalleProducto']['bodega']; 
						$dataToSave[$indice]['precio']      = (empty($valor[$columna_precio])) ? 0 : $valor[$columna_precio];

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
		$cabeceras = array('id_producto' => 'Id del producto', 'stock' => 'Stock', 'precio' => 'Precio ingreso (opcional)');
		BreadcrumbComponent::add('Listado de Movimientos', '/ventaDetalleProductos/movimientos');
		BreadcrumbComponent::add('Ajustar inventario masivo');

		$this->set(compact('bodegas', 'cabeceras'));

	}


	public function admin_edicion_masiva()
	{	
		$tipoPermitido = array(
			'xlsx',
			'xls',
			'csv'
		);

		$datos = array();

		$markets = ClassRegistry::init('Marketplace')->find('list', array('conditions' => array('activo' => 1)));

		$tiendas = ClassRegistry::init('Tienda')->find('list', array('conditions' => array('activo' => 1)));

		if ( $this->request->is('post') || $this->request->is('put')) {

			ini_set('max_execution_time', 0);
			ini_set('post_max_size', '1G');
			ini_set('memory_limit', -1);
			ini_set('max_input_vars', 1000000);

			if ($this->request->data['VentaDetalleProducto']['archivo']['error'] == 0 ) {
				# Reconocer cabecera e idenitficador
				if ($this->request->data['VentaDetalleProducto']['archivo']['error'] != 0) {
					$this->Session->setFlash('El archivo contiene errores o está dañado.', null, array(), 'danger');
					$this->redirect(array('action' => 'edicionMasiva'));
				}

				$ext = pathinfo($this->request->data['VentaDetalleProducto']['archivo']['name'], PATHINFO_EXTENSION);

				if (!in_array($ext, $tipoPermitido)) {
					$this->Session->setFlash('El formato '.$ext.' no es válido. Los formatos permitidos son: ' . implode($tipoPermitido, ','), null, array(), 'danger');
					$this->redirect(array('action' => 'edicionMasiva'));
				}

				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->data['VentaDetalleProducto']['archivo']['tmp_name']);
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				
				if (isset($sheetData[1])) {
					foreach ($sheetData[1] as $k => $cabecera) {
						$datos['options'][$k] = $cabecera;
					}						
				}
				
				$datos['data'] = $sheetData;
				
				$this->Session->write('edicionMasiva', $datos);

			}else{

				$dataToSave = array();	

				foreach ($this->request->data['Indice'] as $a => $i) {
					if (empty($i)) {
						unset($this->request->data['Indice'][$a]);
					}
				}

				$columna = array();

				# Se obtienen los índices para cada elemento
				$columna['id_productos']     = array_search('id_producto', $this->request->data['Indice']);
				$columna['id_marca']         = array_search('marca_id', $this->request->data['Indice']);
				$columna['nombre']           = array_search('nombre', $this->request->data['Indice']);
				$columna['precio_costo']     = array_search('precio_costo', $this->request->data['Indice']);
				$columna['cantidad_virtual'] = array_search('cantidad_virtual', $this->request->data['Indice']);
				$columna['cod_proveedor']    = array_search('codigo_proveedor', $this->request->data['Indice']);
				$columna['ancho']            = array_search('ancho', $this->request->data['Indice']);
				$columna['alto']             = array_search('alto', $this->request->data['Indice']);
				$columna['largo']            = array_search('largo', $this->request->data['Indice']);
				$columna['peso']             = array_search('peso', $this->request->data['Indice']);

				foreach ($markets as $im => $m) {
					$columna['mp_precio_' . $im]       = array_search('mp_precio_' . $im, $this->request->data['Indice']);
					$columna['mp_preciooferta_' . $im] = array_search('mp_preciooferta_' . $im, $this->request->data['Indice']);
					$columna['mp_activo_' . $im]       = array_search('mp_activo_' . $im, $this->request->data['Indice']);
				}

				foreach ($tiendas as $im => $m) {
					$columna['tn_precio_' . $im]       = array_search('tn_precio_' . $im, $this->request->data['Indice']);
					$columna['tn_preciooferta_' . $im] = array_search('tn_preciooferta_' . $im, $this->request->data['Indice']);
					$columna['tn_activo_' . $im]       = array_search('tn_activo_' . $im, $this->request->data['Indice']);
				}
				

				if (!empty($this->Session->read('edicionMasiva.data'))) {
					
					foreach ($this->Session->read('edicionMasiva.data') as $indice => $valor) {
						
						if (empty($valor[$columna['id_productos']]) || $indice == 1) {
							continue;
						}

						if (!$this->VentaDetalleProducto->exists($valor[$columna['id_productos']])) {
							continue;
						}

						# Datos necesarios para reaizar un ingreso
						$dataToSave[$indice]['VentaDetalleProducto']['id']      = $valor[$columna['id_productos']];

						if (!empty($valor[$columna['id_marca']])) {
							$dataToSave[$indice]['VentaDetalleProducto']['marca_id']         = $valor[$columna['id_marca']];
						}

						if (!empty($valor[$columna['nombre']])) {
							$dataToSave[$indice]['VentaDetalleProducto']['nombre']           = $valor[$columna['nombre']];
						}

						if (isset($valor[$columna['cantidad_virtual']])) {
							$dataToSave[$indice]['VentaDetalleProducto']['cantidad_virtual'] = $valor[$columna['cantidad_virtual']];
						}
						
						if (!empty($valor[$columna['precio_costo']])) {
							$dataToSave[$indice]['VentaDetalleProducto']['precio_costo']     = $valor[$columna['precio_costo']];
						}
						
						if (!empty($valor[$columna['cod_proveedor']])) {
							$dataToSave[$indice]['VentaDetalleProducto']['codigo_proveedor'] = $valor[$columna['cod_proveedor']];
						}

						if (!empty($valor[$columna['ancho']])) {
							$dataToSave[$indice]['VentaDetalleProducto']['ancho']         	 = round($valor[$columna['ancho']], 2);
						}

						if (!empty($valor[$columna['alto']])) {
							$dataToSave[$indice]['VentaDetalleProducto']['alto']             = round($valor[$columna['alto']], 2);
						}

						if (!empty($valor[$columna['largo']])) {
							$dataToSave[$indice]['VentaDetalleProducto']['largo']            = round($valor[$columna['largo']], 2);
						}

						if (!empty($valor[$columna['peso']])) {
							$dataToSave[$indice]['VentaDetalleProducto']['peso']             = round($valor[$columna['peso']], 2);
						}

						foreach ($tiendas as $im => $m) {

							$im = (int) $im;

							if (!empty($valor[$columna['tn_precio_' . $im]])) {
								$dataToSave[$indice]['Tienda'][$im]['id']     = $im;
								$dataToSave[$indice]['Tienda'][$im]['precio'] = $valor[$columna['tn_precio_' . $im]];
							}

							if (!empty($valor[$columna['tn_preciooferta_' . $im]])) {
								$dataToSave[$indice]['Tienda'][$im]['id']        = $im;
								$dataToSave[$indice]['Tienda'][$im]['precio_oferta'] = $valor[$columna['tn_preciooferta_' . $im]];
							}

							if (isset($valor[$columna['tn_activo_' . $im]])) {
								$dataToSave[$indice]['Tienda'][$im]['id'] = $im;
								$dataToSave[$indice]['Tienda'][$im]['activo'] = $valor[$columna['tn_activo_' . $im]];
							}

							if (isset($valor[$columna['cantidad_virtual']])) {
								$dataToSave[$indice]['Tienda'][$im]['id'] = $im;
								$dataToSave[$indice]['Tienda'][$im]['cantidad_virtual'] = $valor[$columna['cantidad_virtual']];
							}
							
						}

						foreach ($markets as $im => $m) {

							if (!empty($valor[$columna['mp_precio_' . $im]])) {
								$dataToSave[$indice]['Marketplace'][$im]['id']     = $im;
								$dataToSave[$indice]['Marketplace'][$im]['precio'] = $valor[$columna['mp_precio_' . $im]];
							}

							if (!empty($valor[$columna['mp_preciooferta_' . $im]])) {
								$dataToSave[$indice]['Marketplace'][$im]['id']            = $im;
								$dataToSave[$indice]['Marketplace'][$im]['precio_oferta'] = $valor[$columna['mp_preciooferta_' . $im]];
							}

							if (isset($valor[$columna['mp_activo_' . $im]])) {
								$dataToSave[$indice]['Marketplace'][$im]['id']     = $im;
								$dataToSave[$indice]['Marketplace'][$im]['activo'] = $valor[$columna['mp_activo_' . $im]];
							}

							if (isset($valor[$columna['cantidad_virtual']])) {
								$dataToSave[$indice]['Marketplace'][$im]['id']     = $im;
								$dataToSave[$indice]['Marketplace'][$im]['cantidad_virtual'] = $valor[$columna['cantidad_virtual']];
							}
							
						}

					}

				}
				
				if (empty($dataToSave)) {
					$this->Session->setFlash('No se encontraron valores para actualizar.', null, array(), 'warning');
					$this->redirect(array('action' => 'edicion_masiva'));
				}
				
				$result = $this->actualizar_producto_masivo($dataToSave);
				
				if (!empty($result['errores'])) {
					$this->Session->setFlash($this->crearAlertaUl($result['errores'], 'Errores encontrados'), null, array(), 'danger');
				}

				if (!empty($result['procesados'])) {
					$this->Session->setFlash($this->crearAlertaUl($result['procesados'], 'Procesados con éxito'), null, array(), 'success');
				}

				$this->Session->delete('edicionMasiva');

			}

		}

		$columnas = array(
			'id_producto'      => 'Id del producto',
			'marca_id'         => 'Marca (ID)',
			'nombre'           => 'Nombre',
			'cantidad_virtual' => 'Cantidad virtual',
			'precio_costo'     => 'Precio costo',
			'codigo_proveedor' => 'Código Proveedor',
			'ancho'            => 'Ancho bulto (2 decimales)',
			'alto'             => 'Alto bulto (2 decimales)',
			'largo'            => 'Largo/profundidad bulto (2 decimales)',
			'peso'             => 'Peso bulto (2 decimales)'
		);		

		foreach ($tiendas as $im => $m) {
			#$columnas['tn_precio_' . $im] = 'Precio normal (' . $m . ')';
			#$columnas['tn_preciooferta_' . $im] = 'Precio oferta (' . $m . ')';
			$columnas['tn_activo_' . $im] = 'Activar/Desactivar en ' . $m . ' (1/0)';
		}

		foreach ($markets as $im => $m) {
			$columnas['mp_precio_' . $im] = 'Precio normal (' . $m . ')';
			$columnas['mp_preciooferta_' . $im] = 'Precio oferta (' . $m . ')';
			$columnas['mp_activo_' . $im] = 'Activar/Desactivar en ' . $m . ' (1/0)';
		}

		$marcas = ClassRegistry::init('Marca')->find('list', array('conditions' => array('activo' => 1)));

		BreadcrumbComponent::add('Listado de productos', '/ventaDetalleProductos/index');
		BreadcrumbComponent::add('Edición masiva');

		$this->set(compact('marcas', 'columnas'));

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

						if (!$this->VentaDetalleProducto->exists($valor[$columna_id_productos])) {
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
						
						if (!$this->VentaDetalleProducto->exists($valor[$columna_id_productos])) {
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

	/**
	 * [actualizar_producto_masivo description]
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	public function actualizar_producto_masivo($data = array()) 
	{
		$resultado = array(
			'errores' => array(),
			'procesados' => array()
		);

		foreach ($data as $id => $p) {
			
			# No existe
			if (!$this->VentaDetalleProducto->exists($p['VentaDetalleProducto']['id'])) {
				$resultado['errores'][] = '404 - Item id #' . $p['VentaDetalleProducto']['id'] . ' no existe en los registros.';
				continue;
			}

			# No se pudo guardar
			if (!$this->VentaDetalleProducto->save($p)) {
				$resultado['errores'][] = 'Imposible guardar Item id #' . $p['VentaDetalleProducto']['id'] . '.';
				continue;
			}

			$subProcesados['success'][] = 'Campos locaes item #' . $p['VentaDetalleProducto']['id'] . ' actualizados con exitos.';

			$id_externo = $this->VentaDetalleProducto->field('id_externo', array('id' => $p['VentaDetalleProducto']['id']));

			$subProcesados = array('success' => array(), 'errors' => array());

			$this->Prestashop      = $this->Components->load('Prestashop');
			$this->Linio           = $this->Components->load('Linio');
			$this->MeliMarketplace = $this->Components->load('MeliMarketplace');

			
			# Cambios en la tienda
			if (isset($p['Tienda'])) {
				foreach ($p['Tienda'] as $it => $t) {
					
					$tienda = ClassRegistry::init('Tienda')->find('first', array(
						'conditions' => array(
							'Tienda.id' => $t['id']
						),
						'fields' => array(
							'Tienda.nombre', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop'
						)
					));

					if (is_null($this->Prestashop->ConexionPrestashop)) {
						$this->Prestashop->crearCliente($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop']);
					};
					
					if (isset($t['activo'])) { 
						$cambioActivo = $this->Prestashop->prestashop_activar_desactivar_producto($id_externo, $t['activo']); 
						$subProcesados['success'][] = ($t['activo']) ? sprintf('%s: Item #%d activado con éxito.', $tienda['Tienda']['nombre'], $p['VentaDetalleProducto']['id']) : sprintf('%s: Item #%d desactivado con éxito.',$tienda['Tienda']['nombre'], $p['VentaDetalleProducto']['id']) ; 
					}

					# Stock en toolmania
					if (isset($t['cantidad_virtual'])) {
						$item 		= $this->Prestashop->prestashop_producto_existe($id_externo);
						$actualizar = $this->Prestashop->prestashop_actualizar_stock($item['item']['associations']['stock_availables']['stock_available']['id'], $t['cantidad_virtual']);
						
						if ($actualizar) {
							$subProcesados['success'][] = sprintf('%s: Item #%d - Stock actualizado con éxito.', $tienda['Tienda']['nombre'], $p['VentaDetalleProducto']['id']);			
						}else{
							$subProcesados['errors'][] = sprintf('%s: Item #%d - Imposible actualizar stock.', $tienda['Tienda']['nombre'], $p['VentaDetalleProducto']['id']);
						}

					}

				}
			}


			# Cambios en MP
			if (isset($p['Marketplace'])) {
				
				foreach ($p['Marketplace'] as $im => $m) {
					
					$market = ClassRegistry::init('Marketplace')->find('first', array(
						'conditions' => array(
							'Marketplace.id' => $m['id']
						),
						'fields' => array(
							'Marketplace.id', 'Marketplace.nombre', 'Marketplace.porcentaje_adicional', 'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.marketplace_tipo_id', 'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token', 'Marketplace.seller_id'
						)
					));

					# Linio
					if ($market['Marketplace']['marketplace_tipo_id'] == 1) {
						
						if (is_null($this->Linio->LinioConexion)) {
							$this->Linio->crearCliente($market['Marketplace']['api_host'], $market['Marketplace']['api_user'], $market['Marketplace']['api_key']);
						};

						# Cambiar estado producto (activar desactivar)
						if (isset($m['activo'])) {
							
							$estado = ($m['activo']) ? 'active' : 'inactive';
							$cambioEstado = $this->Linio->actualizar_estado_producto($id_externo, $estado);

							if ($cambioEstado['code'] == 200) {
								$subProcesados['success'][] = ($m['activo']) ? sprintf('%s: Item #%d - Activado con éxito.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id']) : sprintf('%s: Item #%d - Desactivado con éxito.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id']) ; 
							}else{

								$subProcesados['errors'][] = sprintf('%s: Item #%d - %s.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id'], $cambioEstado['message']); 
							}
						}

						# Cambio de stock
						if (isset($m['cantidad_virtual'])) {

							$actualizar = $this->Linio->actualizar_stock_producto(array(), $id_externo, $m['cantidad_virtual']);
					
							if ($actualizar['code'] == 200) {
								$subProcesados['success'][] = sprintf('%s: Item #%d - Stock actualizado con éxito.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id']);					
							}else{
								$subProcesados['errors'][] = sprintf('%s: Item #%d - Imposible actualizar stock.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id']);
							}

						}

						# Cambiar precio producto (precio normal)
						if (isset($m['precio'])) {

							$aumento = (float) ($market['Marketplace']['porcentaje_adicional'] > 0) ? (100-$market['Marketplace']['porcentaje_adicional']) / 100 : 0;

							$precio = ($aumento > 0) ? ($m['precio']/$aumento) : $m['precio'] ;

							$cambioPrecio = $this->Linio->actualizar_precio_producto($id_externo, $precio);

							if ($cambioPrecio['code'] == 200) {
								$subProcesados['success'][] =  sprintf('%s: Item #%d - %s.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id'], $cambioPrecio['message']); 
							}else{
								$subProcesados['errors'][] = sprintf('%s: Item #%d - %s.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id'], $cambioPrecio['message']);
							}
						}

						# Cambiar precio producto (precio normal)
						if (isset($m['precio_oferta'])) {

							$aumento = (float) ($market['Marketplace']['porcentaje_adicional'] > 0) ? (100-$market['Marketplace']['porcentaje_adicional']) / 100 : 0;

							$precio = ($aumento > 0) ? ($m['precio_oferta']/$aumento) : $m['precio_oferta'] ;
							
							$cambioPrecioOferta = $this->Linio->actualizar_precio_oferta_producto($id_externo, $precio);

							if ($cambioPrecioOferta['code'] == 200) {
								$subProcesados['success'][] = sprintf('%s: Item #%d - %s.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id'], $cambioPrecioOferta['message']); 
							}else{
								$subProcesados['errors'][] = sprintf('%s: Item #%d - %s.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id'], $cambioPrecioOferta['message']);; 
							}
						}

					}

					# Meli
					if ($market['Marketplace']['marketplace_tipo_id'] == 2) {
						
						if (is_null($this->MeliMarketplace::$MeliConexion)) {
							$this->MeliMarketplace->crearCliente( $market['Marketplace']['api_user'], $market['Marketplace']['api_key'], $market['Marketplace']['access_token'], $market['Marketplace']['refresh_token'] );
							$this->MeliMarketplace->mercadolibre_conectar('', $market['Marketplace']);
						};

						$itemMeli = $this->MeliMarketplace->mercadolibre_producto_existe($id_externo, $market['Marketplace']['seller_id']);
						
						if (!$itemMeli['existe']) {
							continue;
						}

						# Cambiar estado producto (activar desactivar)
						if (isset($m['activo'])) {
							
							$estado = ($m['activo']) ? 'active' : 'paused';
							$cambioEstado = $this->MeliMarketplace->mercadolibre_cambiar_estado($itemMeli['item']['id'], $estado);

							if ($cambioEstado['httpCode'] == 200) {
								$subProcesados['success'][] = ($m['activo']) ? sprintf('%s: Item #%d - Activado con éxito.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id']) : sprintf('%s: Item #%d - Desactivado con éxito.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id']) ;  
							}else{
								$subProcesados['errors'][] = sprintf('%s: Item #%d - %s.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id'], $cambioEstado['body']['message']); 
							}
						}

						# Cambios en stock
						if (isset($m['cantidad_virtual'])) {

							$actualizar = $this->MeliMarketplace->mercadolibre_actualizar_stock($itemMeli['item']['id'], $m['cantidad_virtual']);
					
							if ($actualizar['httpCode'] == 200) {
								$subProcesados['success'][] = sprintf('%s: Item #%d - Stock actualizado con éxito.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id']);					
							}else{
								$subProcesados['errors'][] = sprintf('%s: Item #%d - Imposible actualizar stock.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id']);
							}
						}

						# Cambiar precio producto (precio normal)
						if (isset($m['precio'])) {

							$aumento = (float) ($market['Marketplace']['porcentaje_adicional'] > 0) ? (100-$market['Marketplace']['porcentaje_adicional']) / 100 : 0;

							$costo_tranporte = $this->MeliMarketplace->mercadolibre_obtener_costo_envio($itemMeli['item']['id']);

							# Se agrega el porcentaje adicional + el costo de envio
							$precio = ($aumento > 0) ? ($m['precio']/$aumento) + $costo_tranporte : $m['precio'] + $costo_tranporte;

							$cambioPrecio = $this->MeliMarketplace->mercadolibre_cambiar_precio($itemMeli['item']['id'], $precio);

							if ($cambioPrecio['httpCode'] == 200) {
								$subProcesados['success'][] = sprintf('%s: Item #%d - Precio actualizado con éxito.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id']); 
							}else{
								$subProcesados['errors'][] = sprintf('%s: Item #%d - %s.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id'], $cambioPrecio['body']['message']); 
							}
						}

						# Cambiar precio producto (precio normal)
						if (isset($m['precio_oferta'])) {

							$aumento = (float) ($market['Marketplace']['porcentaje_adicional'] > 0) ? (100-$market['Marketplace']['porcentaje_adicional']) / 100 : 0;

							$costo_tranporte = $this->MeliMarketplace->mercadolibre_obtener_costo_envio($itemMeli['item']['id']);

							# Se agrega el porcentaje adicional + el costo de envio
							$precio_oferta = ($aumento > 0) ? ($m['precio_oferta']/$aumento) + $costo_tranporte : $m['precio_oferta'] + $costo_tranporte;

							$cambioPrecioOferta = $this->MeliMarketplace->mercadolibre_cambiar_precio_oferta($itemMeli['item']['id'], $precio_oferta);

							if ($cambioPrecioOferta['httpCode'] == 200) {
								$subProcesados['success'][] = sprintf('%s: Item #%d - Precio actualizado con éxito.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id']); 
							}else{
								$subProcesados['errors'][] = sprintf('%s: Item #%d - %s.', $market['Marketplace']['nombre'], $p['VentaDetalleProducto']['id'], $cambioPrecio['body']['message']);
							}
						}

					}
				}

			}
			
			$resultado['procesados'] = $subProcesados['success'];
			$resultado['errores']    = $subProcesados['errors'];
			
		}

		return $resultado;
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


	/**
	 * [admin_edit description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
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
					'VentaDetalle' => array(
						'Venta' => array(
							'fields' => array(
								'Venta.id', 'Venta.referencia', 'Venta.venta_estado_id', 'Venta.fecha_venta', 'Venta.picking_estado'
							),
							'VentaEstado' => array(
								'fields' => array('VentaEstado.id', 'VentaEstado.nombre'),
								'VentaEstadoCategoria' => array(
									'fields' => array('VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo', 'VentaEstadoCategoria.venta', 'VentaEstadoCategoria.final')
								)
							)
						),
						'limit' => 80,
						'order' => array('VentaDetalle.id' => 'DESC')
					),
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

		$this->request->data['VentaDetalleProducto']['cantidad_real_fisica']      = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodegas($id, true);
		$this->request->data['VentaDetalleProducto']['cantidad_real']    = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodegas($id);
		$this->request->data['VentaDetalleProducto']['cantidad_reservada']    = $this->VentaDetalleProducto->obtener_cantidad_reservada($id);

		# PMP
		$this->request->data['VentaDetalleProducto']['pmp_global'] = ClassRegistry::init('Bodega')->obtener_pmp_por_id($id);

		foreach ($bodegas as $ib => $b) {
			$this->request->data['VentaDetalleProducto']['Inventario'][$ib]['bodega_id'] = $ib;
			$this->request->data['VentaDetalleProducto']['Inventario'][$ib]['bodega_nombre'] = $b;
			$this->request->data['VentaDetalleProducto']['Inventario'][$ib]['total'] = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($id, $ib, true);
			$this->request->data['VentaDetalleProducto']['Inventario'][$ib]['pmp'] = ClassRegistry::init('Bodega')->obtener_pmp_por_producto_bodega($id, $ib);
		}

		if (!in_array(1, Hash::extract($canales, '{s}.{n}.existe'))) {
			$this->Session->setFlash('El producto no se encontró en ningún canal de venta.', null, array(), 'warning');
		}

		# Imagenes publicadas en la tienda de prestashop
		$this->Prestashop->crearCliente($this->Session->read('Tienda.apiurl_prestashop'), $this->Session->read('Tienda.apikey_prestashop'));
		$imagenes = $this->Prestashop->prestashop_obtener_imagenes_producto($id);

		
		BreadcrumbComponent::add('Listado de productos', '/ventaDetalleProductos');
		BreadcrumbComponent::add('Editar');

		$this->set(compact('bodegas', 'proveedores', 'precioEspecificoProductos', 'tipoDescuento', 'canales', 'marcas', 'movimientosBodega', 'precio_costo_final', 'imaganes'));
	}


	public function admin_reservar_stock($id_detalle)
	{	

		$cant = ClassRegistry::init('Venta')->reservar_stock_producto($id_detalle);

		if ($cant == 1) {
			$this->Session->setFlash('Cantidad reservada: ' . $cant . ' unidad.', null, array(), 'success');
		}elseif($cant > 1) {
			$this->Session->setFlash('Cantidad reservada: ' . $cant . ' unidades.', null, array(), 'success');
		}elseif ($cant == 0) {
			$this->Session->setFlash('No fue posible reservar el stock. Cantidad reservada: ' . $cant . '.', null, array(), 'warning');
		}

		$this->redirect($this->referer('/', true));
	}

	
	/**
	 * Modificar los precios especificos de cada prodcuto via ajax
	 * @param 	$id 	ID del producto
	 */
	public function admin_modificar_precio_lista_especifico($id)
	{	
		$res = array(
			'code' => 504,
			'message' => 'Error inexplicable'
		);
		
		if ( !$this->VentaDetalleProducto->exists($id) )
		{
			echo json_encode($res);
			exit;
		}

		if ($this->request->is('post')) {
			
			$dataToSave = array(
				'VentaDetalleProducto' => array(
					'id' => $id
				),
				'PrecioEspecificoProducto' => $this->request->data['PrecioEspecificoProducto']
			);
			
			$this->VentaDetalleProducto->PrecioEspecificoProducto->deleteAll(array('venta_detalle_producto_id' => $id));

			if ($this->VentaDetalleProducto->saveAll($dataToSave)) {

				$precios = $this->VentaDetalleProducto->PrecioEspecificoProducto->find('all', array(
					'conditions' => array(
						'PrecioEspecificoProducto.venta_detalle_producto_id' => $id
					)
				));

				$precios_especificos = Hash::extract($precios, '{n}.PrecioEspecificoProducto');

				$this->set(compact('precios_especificos'));

				$vista = $this->render('../Elements/ordenCompras/crear_precio_costo_especifico_producto');
				$html = $vista->body();

				$res['code'] = 200;
				$res['message'] = $html;
			}
		}

		echo json_encode($res);
		exit;
	}


	/**
	 * Obtiene los descuentos de un producto
	 * @param 	$id ID del producto
	 */
	public function admin_obtener_descuento_producto($id)
	{
		$res = array(
			'code' => 504,
			'message' => 'Error inexplicable',
			'data' => array()
		);
		
		if ( !$this->VentaDetalleProducto->exists($id) )
		{
			echo json_encode($res);
			exit;
		}

		$producto = $this->VentaDetalleProducto->obtener_producto_por_id($id);
		
		$descuentos = ClassRegistry::init('VentaDetalleProducto')::obtener_descuento_por_producto($producto, true);
		
		$producto = array();

		$producto['total_descuento']  = $descuentos['total_descuento'];
		$producto['nombre_descuento'] = $descuentos['nombre_descuento'];
		$producto['valor_descuento']  = $descuentos['valor_descuento'];

		$res['code'] = 200;
		$res['message'] = 'Descuento obtenidos con éxito';
		$res['data'] = $producto;

		echo json_encode($res);
		exit;
	}


	public function admin_guardar_proveedores_producto()
	{	
		$noGuardados = 0;

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
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

		$this->redirect($this->referer('/', true));

	}

	public function descontar_stock_virtual($id, $id_externo, $nuevaCantidad)
	{	

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

	public function admin_exportar($canales = false, $limite = 100000, $offset = 0)
	{	
		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
		set_time_limit(-1);
		ini_set('memory_limit', -1);

		$qry = array(
			'recursive'	=> -1,
			'limit' => $limite,
			'offset' => $offset
		);

		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'id':
						$qry = array_replace_recursive($qry, array(
							'conditions' => array('VentaDetalleProducto.id_externo' => str_replace('%2F', '/', urldecode($valor) ) )));
						break;
					case 'nombre':
						$qry = array_replace_recursive($qry, array(
							'conditions' => array('VentaDetalleProducto.nombre LIKE' => '%'.trim(str_replace('%2F', '/', urldecode($valor) )).'%')));
						break;
					case 'marca':
						$qry = array_replace_recursive($qry, array(
							'conditions' => array('VentaDetalleProducto.marca_id' => $valor)));
						break;
				}
			}
		}
		
		$datos			= $this->VentaDetalleProducto->find('all', $qry);

		$bodegas = ClassRegistry::init('Bodega')->find('list', array('conditions' => array('Bodega.activo' => 1)));
			
		$meliConexion = array();

		$campos			= array_keys($this->VentaDetalleProducto->_schema);
		$modelo			= $this->VentaDetalleProducto->alias;

		$marketplaces = ClassRegistry::init('Marketplace')->find('all', array(
			'conditions' => array(
				'Marketplace.tienda_id' => $this->Session->read('Tienda.id')
			)
		));

		foreach ($datos as $id => $p) {
			
			foreach ($bodegas as $ib => $b) {
				$datos[$id]['VentaDetalleProducto']['stock_fisico_' . strtolower(Inflector::slug($b))] = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($p['VentaDetalleProducto']['id'], $ib, true);		
			}

			$datos[$id]['VentaDetalleProducto']['stock_fisico_total'] = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodegas($p['VentaDetalleProducto']['id'], true);
			$datos[$id]['VentaDetalleProducto']['stock_reservado']    = $this->VentaDetalleProducto->obtener_cantidad_reservada($p['VentaDetalleProducto']['id']);
			$datos[$id]['VentaDetalleProducto']['ultimo_precio_compra'] = ClassRegistry::init('Bodega')->ultimo_precio_compra($p['VentaDetalleProducto']['id']);
			$datos[$id]['VentaDetalleProducto']['precio_costo'] = ClassRegistry::init('VentaDetalleProducto')->obtener_precio_costo($p['VentaDetalleProducto']['id']);
			


			# Vemos el detalle en los canales
			if ($canales) {

				foreach ($marketplaces as $im => $m) {
					
					if ($m['Marketplace']['marketplace_tipo_id'] == 1)
						continue;

					if (!isset($meliConexion[$m['Marketplace']['id']])) {
						# Para la consola se carga el componente on the fly!
						$meliConexion[$m['Marketplace']['id']] = $this->Components->load('MeliMarketplace');

						# cliente Meli
						$meliConexion[$m['Marketplace']['id']]->crearCliente( $m['Marketplace']['api_user'], $m['Marketplace']['api_key'], $m['Marketplace']['access_token'], $m['Marketplace']['refresh_token'] );
					}
					
					$result = $meliConexion[$m['Marketplace']['id']]->mercadolibre_conectar('', $m['Marketplace']);									

					if ($result['success']) {
						
						$meli           = $meliConexion[$m['Marketplace']['id']]->mercadolibre_producto_existe($p['VentaDetalleProducto']['id_externo'], $m['Marketplace']['seller_id']);
						
						if (!$meli['existe']) {
							continue;
						}
						
						$datos[$id]['VentaDetalleProducto']['precio_' . strtolower(Inflector::slug($m['Marketplace']['nombre']))] = $meli['item']['precio'];
						
						$datos[$id]['VentaDetalleProducto']['envio_'  . strtolower(Inflector::slug($m['Marketplace']['nombre']))] = $meliConexion[$m['Marketplace']['id']]->mercadolibre_obtener_costo_envio($meli['item']['id']);

					}

				}
				
			}

		}
		
		$this->set(compact('datos', 'campos', 'modelo', 'bodegas', 'marketplaces'));
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
							),
							'AND' => array(
								'PrecioEspecificoProducto.activo' => 1,
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
				'limit' => 3
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
				$respuesta[$key]['minimo_compra'] 	= (int) $value['VentaDetalleProducto']['cant_minima_compra'];
				$respuesta[$key]['descuento'] 	 	= round($value['VentaDetalleProducto']['total_descuento']);
				$respuesta[$key]['tipo_descuento'] 	= (int)0;
				$respuesta[$key]['nombre_descuento'] = $value['VentaDetalleProducto']['nombre_descuento'];

				$producto = $value['VentaDetalleProducto'];
				$producto['PrecioEspecificoProducto'] = $value['PrecioEspecificoProducto'];
				
				$this->set(compact('producto'));

				$vista = $this->render('../Elements/ordenCompras/modal-precio-especifico');
				$html = $vista->body();
				
				$respuesta[$key]['html_modal'] = $html;

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
					'conditions' => array(
						'Marketplace.activo' => 1
					),
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

		$log = array();

		foreach ($canales as $ic => $canal) {

			if ($ic === 'Prestashop' ) {
				foreach ($canal as $i => $c) {

					# si se excluye se termina
					if (isset($excluir['Prestashop']) && in_array($i, $excluir['Prestashop'])) {
						break;
					}

					if (!$c['existe']) {
						continue;
					}

					if (Configure::read('ambiente') == 'dev') {
			        	$actualizar = true;
			      	}else{
			      		$actualizar = $this->Prestashop->prestashop_actualizar_stock($c['item']['associations']['stock_availables']['stock_available']['id'], $nuevo_stock);
			      	}

					if ($actualizar && $c['existe']) {
						$result['successes'][] = sprintf('Item %d actualizado con éxto en %s', $id_externo, $c['nombre']);					
					}else{
						$result['errors'][] = sprintf('Error al actualizar el item %d en %s o no existe en el canal', $id_externo, $c['nombre']);
					}
				}

				$log[] = array(
					'Log' => array(
						'administrador' => 'Prestashop actualizar canal',
						'modulo' => 'VentaDetalleProductos',
						'modulo_accion' => json_encode($result)
					)
				);
			}

			if ($ic === 'Linio') {

				foreach ($canal as $i => $c) {
					
					# si se excluye se termina
					if (isset($excluir['Linio']) && in_array($i, $excluir['Linio'])) {
						break;
					}
					
					if (!$c['existe']) {
						continue;
					}

					if (Configure::read('ambiente') == 'dev') {
						$actualizar = array('code' => 200);
					}else{
						$actualizar = $this->Linio->actualizar_stock_producto(array(), $id_externo, $nuevo_stock);
					}
					
					
					if ($actualizar['code'] == 200 && $c['existe']) {
						$result['successes'][] = sprintf('Item %d actualizado con éxto en %s', $id_externo, $c['nombre']);					
					}else{
						$result['errors'][] = sprintf('Error al actualizar el item %d en %s o no existe en el canal', $id_externo, $c['nombre']);
					}

					sleep(3);

					$log[] = array(
						'Log' => array(
							'administrador' => 'Linio actualizar canal',
							'modulo' => 'VentaDetalleProductos',
							'modulo_accion' => json_encode($result)
						)
					);
				}
			}

			if ($ic === 'Mercadolibre' ) {
				foreach ($canal as $i => $c) {

					# si se excluye se termina
					if (isset($excluir['Mercadolibre']) && in_array($i, $excluir['Mercadolibre'])) {
						break;
					}

					if (!$c['existe']) {
						continue;
					}
					
					if (Configure::read('ambiente') == 'dev') {
						$actualizar = array('httpCode' => 200);
					}else{
						$actualizar = $this->MeliMarketplace->mercadolibre_actualizar_stock($c['item']['id'], $nuevo_stock);
					}

					if ($actualizar['httpCode'] == 200) {
						$result['successes'][] = sprintf('Item %d actualizado con éxto en %s', $id_externo, $c['nombre']);					
					}else{
						$result['errors'][] = sprintf('Error al actualizar el item %d en %s o no existe en el canal', $id_externo, $c['nombre']);
					}

					$log[] = array(
						'Log' => array(
							'administrador' => 'Mercadolibre actualizar canal',
							'modulo' => 'VentaDetalleProductos',
							'modulo_accion' => json_encode($result)
						)
					);
				}
			}
		
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

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
					$productosLocales[$ip]['VentaDetalleProducto']['cantidad_virtual'] = 0;
				}else{
					$stock = $this->Prestashop->prestashop_obtener_stock_producto($p['id']);
			
					$productosLocales[$ip]['VentaDetalleProducto']['id'] = $p['id'];
					$productosLocales[$ip]['VentaDetalleProducto']['cantidad_virtual'] = $stock['stock_available']['quantity'];
				}

				if (is_array($p['name']['language']) || empty($p['name']['language'])) {
					unset($productosLocales[$ip]);
					continue;
				}

				if (!empty($p['id_supplier'])) {
					$productosLocales[$ip]['Proveedor'] = array(
						'Proveedor' => $p['id_supplier']
					);
				}

				$productosLocales[$ip]['VentaDetalleProducto']['id_externo']       = $p['id'];
				$productosLocales[$ip]['VentaDetalleProducto']['codigo_proveedor'] = $p['supplier_reference'];
				$productosLocales[$ip]['VentaDetalleProducto']['marca_id'] 		   = $p['id_manufacturer'];
				$productosLocales[$ip]['VentaDetalleProducto']['nombre']           = $p['name']['language'];
				$productosLocales[$ip]['VentaDetalleProducto']['ancho']			   = round($p['width'], 2);
				$productosLocales[$ip]['VentaDetalleProducto']['alto']			   = round($p['height'], 2);
				$productosLocales[$ip]['VentaDetalleProducto']['largo']			   = round($p['depth'], 2);
				$productosLocales[$ip]['VentaDetalleProducto']['peso']			   = round($p['weight'], 2);
			}

		}

		if (!empty($productosLocales)) {
				
			if ($this->VentaDetalleProducto->saveMany($productosLocales, array('deep' => true)))
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


	public function admin_obtenerqrsec()
    {	

    	$qr_obtenidos = array();

    	if ($this->request->is('post')) {
    			
			$url_base = $this->request->data['Sec']['url'];
			$nombre = $this->request->data['Sec']['nombre'];
			$marcas = $this->request->data['Sec']['marca'];

			if (empty($url_base) || empty($nombre) || empty($marcas)) {
				$this->Session->setFlash('Todos los campos son obligatorios.', null, array(), 'danger');
				$this->redirect(array('action' => 'obtenerqrsec'));
			}

			$urlContenedor = APP . 'webroot' . DS . 'img' . DS . 'VentaDetalleProducto' . DS;
			
			$productos = $this->VentaDetalleProducto->find('all', array('conditions' => array('VentaDetalleProducto.marca_id' => $marcas), 'fields' => array('VentaDetalleProducto.id', 'VentaDetalleProducto.codigo_proveedor', 'VentaDetalleProducto.nombre')));

			$guardar = array();

			foreach ($productos as $ip => $p) {

				$url_sec = $url_base . str_replace('{ref}', $p['VentaDetalleProducto']['codigo_proveedor'], $nombre);

				$init = curl_init();
				curl_setopt($init, CURLOPT_URL, $url_sec);
				curl_setopt($init, CURLOPT_RETURNTRANSFER, 1);
				curl_setopt($init, CURLOPT_SSLVERSION, 3);

				$resCurl = curl_exec($init);

				// Obtener el código de respuesta
    			$httpcode = curl_getinfo( $init, CURLINFO_HTTP_CODE );

				curl_close($init);

				if (empty($resCurl) || $httpcode == 400)
					continue;

				$sec_nombre = 'sec-' . strtolower(Inflector::slug($p['VentaDetalleProducto']['codigo_proveedor'])) . '.jpg';

				$rutadescarga = $urlContenedor . $sec_nombre;

				if (!is_dir($urlContenedor)) {
					mkdir($urlContenedor, 0775);
				}

				if (!file_exists($rutadescarga)) {

					$qr_sec = fopen($rutadescarga, "w+");

					fputs($qr_sec, $resCurl);

					fclose($qr_sec);
				}
				
				$guardar[$ip]['VentaDetalleProducto']['id']     = $p['VentaDetalleProducto']['id'];
				$guardar[$ip]['VentaDetalleProducto']['qr_sec'] = $sec_nombre;
				
				$qr_obtenidos[] = array(
					'item' => $p['VentaDetalleProducto']['nombre'],
					'url'  => $url_sec,
					'qr'   => obtener_url_base() . 'webroot/img/VentaDetalleProducto/' . $sec_nombre
				);
				
			}
			
			if (!empty($guardar)) {
				if ($this->VentaDetalleProducto->saveMany($guardar)){
					$this->Session->setFlash(sprintf('%d items actualizados con éxito.', count($guardar)), null, array(), 'success');
				}else{
					$this->Session->setFlash('No fue posible guardar la información.', null, array(), 'danger');
				}
			}else{
				$this->Session->setFlash('No Se encontraron coincidencias en la url especificada.', null, array(), 'danger');
			}

    	}

    	$marcas = ClassRegistry::init('Marca')->find('list');

    	$this->set(compact('qr_obtenidos', 'marcas'));

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

    	$token = '';

    	if (isset($this->request->query['token'])) {
    		$token = $this->request->query['token'];
    	}

    	# Existe token
		if (!isset($token)) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($token)) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

		$paginacion = array(
        	'limit' => 0,
        	'offset' => 0,
        	'total' => 0
        );

    	$qry = array(
    		'order' => array('id' => 'desc')
    	);

    	if (isset($this->request->query['id'])) {
    		if (!empty($this->request->query['id'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'VentaDetalleProducto.id' => $this->request->query['id'])));
    		}
    	}

    	if (isset($this->request->query['limit'])) {
    		if (!empty($this->request->query['limit'])) {
    			$qry = array_replace_recursive($qry, array('limit' => $this->request->query['limit']));
    			$paginacion['limit'] = $this->request->query['limit'];
    		}
    	}

    	if (isset($this->request->query['offset'])) {
    		if (!empty($this->request->query['offset'])) {
    			$qry = array_replace_recursive($qry, array('offset' => $this->request->query['offset']));
    			$paginacion['offset'] = $this->request->query['offset'];
    		}
    	}

    	if (isset($this->request->query['s'])) {
    		if (!empty($this->request->query['s'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'VentaDetalleProducto.nombre LIKE' => '%' . $this->request->query['s'] . '%' )));
    		}
    	}
   
        $productos = $this->VentaDetalleProducto->find('all', $qry);

        $paginacion['total'] = count($productos);

        # Si existe el campo external, se consulta el precio del producto en prestashop
    	if (isset($this->request->query['external'])) {
    		
    		# Iniciamos prestashop
    		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array('Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop'));

			$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

    		foreach ($productos as $ip => $producto) {
				
    			$p = $this->Prestashop->prestashop_obtener_producto($producto['VentaDetalleProducto']['id_externo']);
				
				if (empty($p))
					continue;

				$descuento = $this->Prestashop->prestashop_obtener_descuento_producto($producto['VentaDetalleProducto']['id_externo'], $p['price']);

    			$productos[$ip]['VentaDetalleProducto']['external'] = array(
    				'precio_normal' => round($p['price']* 1.19),
    				'precio_venta' => round( ($p['price'] - $descuento) * 1.19)
    			);

			}

		}

		$html_tr = '';

		# Si existe el campo tr, devolvemos las vistas html disponibles
        if (isset($this->request->query['tr'])) {
    		if ($this->request->query['tr'] == 1) {
    			foreach ($productos as $ip => $producto) {
    				
    				$v             =  new View();
					$v->autoRender = false;
					$v->output     = '';
					$v->layoutPath = '';
					$v->layout     = '';
					$v->set(compact('producto'));	

					$productos[$ip]['VentaDetalleProducto']['tr'] = $v->render('/Elements/ventas/tr-producto-crear-venta');

					$v2             =  new View();
					$v2->autoRender = false;
					$v2->output     = '';
					$v2->layoutPath = '';
					$v2->layout     = '';
					$v2->set(compact('producto'));

					$productos[$ip]['VentaDetalleProducto']['tr_prospecto'] = $v2->render('/Elements/prospectos/tr-producto');

    			}
    		}
    	}

    	# Verificamos la existencia del item en bodegas
    	foreach ($productos as $ip => $producto) {
    		$productos[$ip]['VentaDetalleProducto']['stock_fisico_total'] = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodegas($producto['VentaDetalleProducto']['id']);
    	}

        $this->set(array(
            'productos' => $productos,
            'paginacion' => $paginacion,
            '_serialize' => array('productos', 'paginacion')
        ));
    }


    /**
     * Visualiza un producto
     * Endpoint: /api/producto/view/:id_externo.json
     * @param  [type] $id id externo del producto
     */
    public function api_view($id) {
    	
    	$token = '';

    	if (isset($this->request->query['token'])) {
    		$token = $this->request->query['token'];
    	}

    	# Existe token
		if (!isset($token)) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($token)) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

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

		if (isset($this->request->query['external'])) {

			$canales = $this->verificar_canales($producto['VentaDetalleProducto']['id_externo']);

			foreach ($canales as $ic => $canal) {
				foreach ($canal as $i => $c) {

					if (!$c['existe'])
						continue;

					$producto['VentaDetalleProducto'][$ic][$c['nombre']] = array(
						'precio_venta'     => $c['item']['precio'],
						'stock_disponible' => $c['item']['stock_disponible'],
						'estado'           => $c['item']['estado']
					);

				}
			}

		}

		# Etiqueta sec
		if (!empty($producto['VentaDetalleProducto']['qr_sec'])) {
			
			$url_sec = obtener_url_base() . 'webroot/img/VentaDetalleProducto/' . $producto['VentaDetalleProducto']['qr_sec'];
			$producto['VentaDetalleProducto']['qr_sec'] = $url_sec;

		}

		$producto['VentaDetalleProducto']['tiempo_entrega'] = $this->VentaDetalleProducto->obtener_tiempo_entrega($id);


        $this->set(array(
            'producto' => $producto['VentaDetalleProducto'],
            '_serialize' => array('producto')
        ));
			
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


    /**
     * Crea un producto desde Prestashop
     * @param  [type] $tienda_id [description]
     * @return [type]            [description]
     */
    public function api_crear() {

		# Solo método POST
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501,
				'name' => 'error',
				'message' => 'Método no permitido'
			);

			throw new CakeException($response);
		}

		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}


		if (empty($this->request->data['id_externo']) || empty($this->request->data['nombre'])) {
			$response = array(
				'code' => 504,
				'created' => false,
				'message' => 'Id y nombre requerido'
			);

			throw new CakeException($response);
		}

		$resultado = array(
			'code' => 201,
			'created' => false,
			'updated' => false
		);

		$log = array();


		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop rest',
				'modulo' => 'Productos',
				'modulo_accion' => json_encode($this->request->data)
			)
		);
			
		$data = array(
			'VentaDetalleProducto' => array(
				'id'               => $this->request->data['id_externo'],
				'id_externo'       => $this->request->data['id_externo'],
				'marca_id'         => $this->request->data['marca_id'],
				'nombre'           => $this->request->data['nombre'],
				'codigo_proveedor' => $this->request->data['codigo_proveedor'],
				'peso'			   => $this->request->data['peso'],
				'ancho'			   => $this->request->data['ancho'],
				'alto'			   => $this->request->data['alto'],
				'largo'			   => $this->request->data['largo'],
			)
		);

		$existe = true;

		# Si la marca no existe se intenta crear
		if (!ClassRegistry::init('Marca')->exists($this->request->data['marca_id'])) {

			$tienda = ClassRegistry::init('Tienda')->tienda_principal(array('Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop'));

			$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );
			
			$marcaPrestashop = $this->Prestashop->prestashop_obtener_marca($this->request->data['marca_id']);

			if (!empty($marcaPrestashop)) {
				$marcaSave = array(
					'Marca' => array(
						'id' => $marcaPrestashop['id'],
						'nombre' => $marcaPrestashop['name']
					)
				);

				ClassRegistry::init('Marca')->create();
				ClassRegistry::init('Marca')->save($marcaSave);

				$log[] = array(
					'Log' => array(
						'administrador' => 'Prestashop rest',
						'modulo' => 'Marca',
						'modulo_accion' => json_encode($marcaSave)
					)
				);
			}

		}


		if (!$this->VentaDetalleProducto->exists($this->request->data['id_externo'])) {
			$this->VentaDetalleProducto->create();
			$existe = false;	
		}
		
		if ($this->VentaDetalleProducto->save($data)){

			if ($existe) {
				
				$log[] = array(
					'Log' => array(
						'administrador' => 'Prestashop rest',
						'modulo' => 'Productos',
						'modulo_accion' => sprintf('Producto #%d actualizado con éxito', $this->request->data['id_externo'])
					)
				);

				$resultado = array(
					'code' => 200,
					'updated' => true
				);

			}else{

				$log[] = array(
					'Log' => array(
						'administrador' => 'Prestashop rest',
						'modulo' => 'Productos',
						'modulo_accion' => sprintf('Producto #%d creado con éxito', $this->request->data['id_externo'])
					)
				);

				$resultado = array(
					'code' => 200,
					'created' => true
				);
			}
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		$this->set(array(
			'response'   => $resultado,
			'_serialize' => array('response')
	    ));
	}

}
