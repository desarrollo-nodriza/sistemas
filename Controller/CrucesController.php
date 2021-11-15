<?php
App::uses('AppController', 'Controller');
App::uses('DtesController', 'Controller');

App::import('Vendor', 'PhpSpreadsheet', array('file' => 'PhpSpreadsheet/vendor/autoload.php'));

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

Class CrucesController extends AppController {

	/**
	 * Permite cruzar las ventas con sus respectivos DTES. 
	 * @return [type] [description]
	 */
	public function admin_cruces()
	{	

		$tipoPermitido = array(
			'xlsx',
			'xls',
			'csv'
		);

		$respuesta = array();

		$datos = array();

		if ($this->request->is('post') || $this->request->is('put')) {

			ini_set('max_execution_time', 0);
			
			if ($this->request->data['Cruzar']['archivo']['error'] != 0  
				&& !empty($this->Session->read('Cruxe.data'))) 
			{	
				# Obtenemos las cabeceras seleccionadas
				foreach ($this->request->data['Cruzar']['cabecera'] as $a => $i) {
					if (empty($i)) {
						unset($this->request->data['Cruzar']['cabecera'][$a]);
					}
				}
				
				# Se obtienen los índices para cada elemento
				$columna_id_transaccion = array_search('id_transaccion', $this->request->data['Cruzar']['cabecera']); 
				$columna_tipo_transaccion     = array_search('tipo_transaccion', $this->request->data['Cruzar']['cabecera']);
				

				// Obtenemos los valores de la columna seleccionada
				$columnaValores = Hash::extract($this->Session->read('Cruxe.data'), '{n}.' . $columna_id_transaccion);
				$columnaTipo = Hash::extract($this->Session->read('Cruxe.data'), '{n}.' . $columna_tipo_transaccion);
				
				if (count($columnaValores) < 2) {
					$this->Session->setFlash('No se encontraron valores en ésta columna. Seleccione otra.', null, array(), 'warning');
					$this->redirect(array('action' => 'cruces'));
				}
				
				// Se quita la cabecera
				foreach ($columnaValores as $i => $valor) {
					if ($i == 0) {
						unset($columnaValores[$i]);
					}else{
						$columnaValores[$i] = (string) $valor;
					}
				}

				// Se quita la cabecera
				foreach ($columnaTipo as $i => $tt) {
					if ($i == 0) {
						unset($columnaTipo[$i]);
					}else{
						$columnaTipo[$i] = (string) $tt;
					}
				}

				// Se buscan las ventas con dichos identificadores
				$ids_transacciones = [];

				$qry = array(
					'conditions' => array(),
					'contain' => array(),
					'fields' => array()
				);

				foreach ($columnaValores as $cd) {
					if (empty($cd))
						continue;
					
					$ids_transacciones[] = trim($cd);

					$qry['conditions']['OR'][] = 'VentaTransaccion.nombre LIKE "' . $cd . '"';

				}

				if (empty($ids_transacciones))
				{
					$this->Session->setFlash('No se encontraron coincidencia ids de transacciones en el documento.', null, array(), 'warning');
					$this->redirect(array('action' => 'cruces'));
				}	

				$datosExcel = $this->Session->read('Cruxe.data');

				# Actualizamos los ids de transaccion para verificar que existan todos
				$this->preparar_transacciones($ids_transacciones, $qry);
				
				$qry['contain'] = array(
					'Venta' => array(
						'Dte' => array(
							'conditions' => array(
								'Dte.estado' => 'dte_real_emitido'
							),
							'fields' => array(
								'Dte.folio', 'Dte.estado', 'Dte.tipo_documento', 'Dte.total', 'Dte.rut_receptor', 'Dte.razon_social_receptor'
							)
						),
						'fields' => array(
							'Venta.id', 'Venta.total', 'Venta.fecha_venta'
						)
					)
				);
				
				$qry['fields'] = array(
					'VentaTransaccion.venta_id', 'VentaTransaccion.nombre', 'VentaTransaccion.monto', 'VentaTransaccion.fee'
				);

				// Activar en versión 2
				$transacciones = ClassRegistry::init('VentaTransaccion')->find('all', $qry);
				//$transacciones = $this->metodoAntiguo($columnaValores);
				
				if (empty($transacciones)) {
					$this->Session->setFlash('No se encontraron coincidencia.', null, array(), 'warning');
					$this->redirect(array('action' => 'cruces'));
				}
				
				
				$spreadsheet = new Spreadsheet();

				// Crear cabeceras excel
				foreach ($this->Session->read('Cruxe.options') as $indice => $nombre) {
					$spreadsheet->setActiveSheetIndex(0)
				    ->setCellValue(sprintf('%s1', $indice), $nombre);
				}
				
				$ultimaFila = 1;
				// Agregamos la data
				foreach ($datosExcel as $indice => $valor) {
					$ultimaColumna = 1;
					foreach ($this->Session->read('Cruxe.options') as $i => $nombre) {
						$spreadsheet->setActiveSheetIndex(0)
						->setCellValue(sprintf('%s%d', $i, $indice), (string) $valor[$i]);
						$ultimaFila = $indice;
						$ultimaColumna++;
					}

					// Se agregas las nuevas cabeceras
					$spreadsheet->setActiveSheetIndex(0)
					->setCellValueByColumnAndRow($ultimaColumna, 1, 'Folio')
					->setCellValueByColumnAndRow($ultimaColumna+1, 1, 'Rut receptor')
					->setCellValueByColumnAndRow($ultimaColumna+2, 1, 'Razon social receptor')
					->setCellValueByColumnAndRow($ultimaColumna+3, 1, 'Tipo documento')
					->setCellValueByColumnAndRow($ultimaColumna+4, 1, 'Total documento');

					foreach ($this->Session->read('Cruxe.options') as $i => $nombre) {
						
						$dt = (string) $valor[$i];
						
						if (empty($dt))
							continue;

						$transaccion = array_unique(Hash::extract($transacciones, '{n}.VentaTransaccion[nombre='.$dt.']'));
						
						if (empty($transaccion))
							continue;

						$tipo_transaccion = $valor[$columna_tipo_transaccion];

						if (empty($tipo_transaccion))
							continue;

						$dtes = Hash::extract($transacciones, '{n}.Venta[id='.(string) $transaccion[0]['venta_id'].'].Dte.{n}');

						if (empty($dtes)) 
							continue;

						$DtesController = new DtesController(new CakeRequest(), new CakeResponse());

						$folio = '';
						$rut_receptor = '';
						$nombre_receptor = '';
						$tipo_documento = '';
						$monto_documento = '';
						foreach ($dtes as $ia => $d) {

							# Si el tipo de movimiento es refund adjuntamos la nota de crédito de la venta
							if ($tipo_transaccion == 'refund' && $d['tipo_documento'] == '61')
							{
								$folio           = $d['folio'];
								$rut_receptor    = formato_rut($d['rut_receptor']);
								$nombre_receptor = $d['razon_social_receptor'];
								$tipo_documento  = $DtesController->tipoDocumento[$d['tipo_documento']];
								$monto_documento = CakeNumber::currency($d['total'], 'CLP');
							}else if ($d['tipo_documento'] == 33 || $d['tipo_documento'] == 39) {
								$folio           = $d['folio'];
								$rut_receptor    = formato_rut($d['rut_receptor']);
								$nombre_receptor = $d['razon_social_receptor'];
								$tipo_documento  = $DtesController->tipoDocumento[$d['tipo_documento']];
								$monto_documento = CakeNumber::currency($d['total'], 'CLP');
							}
						}

						// Agreamos los valores a la columna y fila correspondiente
						$spreadsheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow( $ultimaColumna, $indice, $folio )
						->setCellValueByColumnAndRow( $ultimaColumna+1, $indice, $rut_receptor )
						->setCellValueByColumnAndRow( $ultimaColumna+2, $indice, $nombre_receptor )
						->setCellValueByColumnAndRow( $ultimaColumna+3, $indice, $tipo_documento )
						->setCellValueByColumnAndRow( $ultimaColumna+4, $indice, $monto_documento );
					}
				}
				
				

				// Redirect output to a client’s web browser (Xlsx)
				header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
				header('Content-Disposition: attachment;filename="01simple.xlsx"');
				header('Cache-Control: max-age=0');
				// If you're serving to IE 9, then the following may be needed
				header('Cache-Control: max-age=1');

				// If you're serving to IE over SSL, then the following may be needed
				header('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
				header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT'); // always modified
				header('Cache-Control: cache, must-revalidate'); // HTTP/1.1
				header('Pragma: public'); // HTTP/1.0

				$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
				$writer->save('php://output');
				exit;
				

			}else{
			
				# Reconocer cabecera e idenitficador
				if ($this->request->data['Cruzar']['archivo']['error'] != 0) {
					$this->Session->setFlash('El archivo contiene errores o está dañado.', null, array(), 'danger');
					$this->redirect(array('action' => 'cruces'));
				}

				$ext = pathinfo($this->request->data['Cruzar']['archivo']['name'], PATHINFO_EXTENSION);

				if (!in_array($ext, $tipoPermitido)) {
					$this->Session->setFlash('El formato '.$ext.' no es válido. Los formatos permitidos son: ' . implode($tipoPermitido, ','), null, array(), 'danger');
					$this->redirect(array('action' => 'cruces'));
				}


				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->data['Cruzar']['archivo']['tmp_name']);
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				
				if (isset($sheetData[1])) {
					foreach ($sheetData[1] as $k => $cabecera) {
						$datos['options'][$k] = $cabecera;
					}						
				}
				
				$datos['data'] = $sheetData;

			}

		}

		$this->Session->write('Cruxe', $datos);

		$opciones = array(
			'id_transaccion' => 'ID transacción',
			'tipo_transaccion' => 'Tipo de transacción'
		);

		BreadcrumbComponent::add('Listado de dte´s', '/dtes');
		BreadcrumbComponent::add('Cruzar datos');

		$this->set(compact('datos', 'opciones'));
	}


	/**
	 * Obtiene las ventas con el métdo antiguo (directo desde la BD de la tienda)
	 * @param  array  $datos listado de ids de transaccion
	 * @return array
	 */
	private function metodoAntiguo($datos = array())
	{
		$qry = array(
			'conditions' => array(),
			'fields' => array()
		);

		
		$qry['joins'] = array(
		    array('table' => sprintf('%sorders', $this->Session->read('Tienda.prefijo')),
		        'alias' => 'Orden',
		        'type' => 'LEFT',
		        'conditions' => array(
		        	'OrdenPago.order_reference=Orden.reference'
		        )
		    )
		);

		foreach ($datos as $cd) {
			if (empty($cd)) 
				continue;

			$qry['conditions']['OR'][] = 'OrdenPago.transaction_id = "' . $cd . '"';

		}

		$qry['fields'] = array(
			'OrdenPago.order_reference', 'OrdenPago.transaction_id', 'OrdenPago.amount',
			'Orden.id_order', 'Orden.total_paid_real', 'Orden.date_add'
		);
		
		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden', 'OrdenPago'));

		$transacciones = ClassRegistry::init('OrdenPago')->find('all', $qry);

		if (empty($transacciones)) {
			return array();
		}

		// Obtenemos los dtes relacionados
		$dtes =  ClassRegistry::init('Dte')->find('all', array(
			'conditions' => array(
				'Dte.id_order' => Hash::extract($transacciones, '{n}.Orden.id_order')
			),
			'fields' => array(
				'Dte.folio', 'Dte.estado', 'Dte.tipo_documento', 'Dte.total', 'Dte.id_order'
			)
		));

		// Normalizamos los nombres para que no tengo problemas con la versión anterior
		$final = array();
		
		foreach ($transacciones as $it => $transaccion) {
			$final[$it]['VentaTransaccion']['venta_id'] = $transaccion['Orden']['id_order'];
			$final[$it]['VentaTransaccion']['nombre']   = $transaccion['OrdenPago']['transaction_id'];
			$final[$it]['VentaTransaccion']['monto']    = $transaccion['OrdenPago']['amount'];
			$final[$it]['VentaTransaccion']['fee']      = 0;


			$final[$it]['Venta']['id']          = $transaccion['Orden']['id_order'];
			$final[$it]['Venta']['total']       = $transaccion['Orden']['total_paid_real'];
			$final[$it]['Venta']['fecha_venta'] = $transaccion['Orden']['date_add'];

			$final[$it]['Venta']['Dte']			= Hash::extract($dtes, '{n}.Dte[id_order='.$transaccion['Orden']['id_order'].']');
		}


		return $final;
	}

	public function preparar_transacciones($ids_transacciones, &$qry)
	{
		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
			'Tienda.apiurl_prestashop', 
			'Tienda.apikey_prestashop'
		));

		if (empty($tienda['Tienda']['apiurl_prestashop']) || empty($tienda['Tienda']['apikey_prestashop']))
		{
			return false;
		}

		# componente on the fly!
		$this->Prestashop = $this->Components->load('Prestashop');

		# Cliente Prestashop
		$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

		$transacciones = $this->Prestashop->prestashop_obtener_venta_transaccionesv2($ids_transacciones);
		
		$carritos = $this->Prestashop->prestashop_obtener_ventas_por_carros($ids_transacciones);
		
		$transacciones2 = [];

		if (!empty($carritos))
		{
			foreach($carritos['order'] as $ic => $c)
			{	
				$tr = $this->Prestashop->prestashop_obtener_venta_transacciones_por_referencia([$c['reference']]);	
				$transacciones2['order_payment'][$ic] = $tr['order_payment'][0];
				$transacciones2['order_payment'][$ic]['transaction_id'] = $c['id_cart'];
			}
		}		
	
		$trx = Hash::merge($transacciones, $transacciones2);

		if (empty($trx))
		{
			return false;	
		}
		
		$ventas = ClassRegistry::init('Venta')->find('all', array(
			'conditions' => array(
				'Venta.referencia IN' => Hash::extract($trx, 'order_payment.{n}.order_reference')
			),
			'contain' => array(
				'VentaTransaccion'
			),
			'fields' => array(
				'Venta.id', 'Venta.referencia'
			)
		));

		if (empty($ventas))
			return false;

		$VentaTransaccion = [];

		foreach ($ventas as $v)
		{	
			$nwTransacciones = Hash::extract($trx, 'order_payment.{n}[order_reference='.$v['Venta']['referencia'].']');

			if (empty($nwTransacciones))
				continue;

			foreach ($nwTransacciones as $nt)
			{	

				# Actualizamos la query
				$qry['conditions']['OR'][] = 'VentaTransaccion.nombre LIKE "' . $nt['transaction_id'] . '"';

				# Verificamos si la transaccion no existe
				if (!Hash::check($v['VentaTransaccion'], '{n}[nombre='.$nt['transaction_id'].']'))
				{	
					# La creamos
					$VentaTransaccion[] = array(
						'VentaTransaccion' => array(
							'venta_id' => $v['Venta']['id'],
							'monto' => (!empty($nt['amount'])) ? $nt['amount'] : 0,
							'nombre' => $nt['transaction_id'],
							'created' => $nt['date_add']
						)
					);
				}
			}
		}
		
		if (empty($VentaTransaccion))
			return false;

		if (!ClassRegistry::init('VentaTransaccion')->saveMany($VentaTransaccion))
		{
			return false;
		}

		return true;

	}	

}