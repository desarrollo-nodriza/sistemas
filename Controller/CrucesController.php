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
				&& isset($this->request->data['Cruzar']['cabecera']) 
				&& !empty($this->Session->read('Cruxe.data'))) {
				
				// Obtenemos los valores de la columna seleccionada
				$columnaValores = Hash::extract($this->Session->read('Cruxe.data'), '{n}.' . $this->request->data['Cruzar']['cabecera']);

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

				// Se buscan las ventas con dichos identificadores

				$qry = array(
					'conditions' => array(),
					'contain' => array(),
					'fields' => array()
				);

				foreach ($columnaValores as $cd) {
					if (empty($cd))
						continue;

					$qry['conditions']['OR'][] = 'VentaTransaccion.nombre LIKE "' . $cd . '"';
				}

				$qry['contain'] = array(
					'Venta' => array(
						'Dte' => array(
							'conditions' => array(
								'Dte.estado' => 'dte_real_emitido'
							),
							'fields' => array(
								'Dte.folio', 'Dte.estado', 'Dte.tipo_documento', 'Dte.total', 'Dte.rut_receptor'
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
				foreach ($this->Session->read('Cruxe.data') as $indice => $valor) {
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
					->setCellValueByColumnAndRow($ultimaColumna+2, 1, 'Tipo documento')
					->setCellValueByColumnAndRow($ultimaColumna+3, 1, 'Total documento');

					foreach ($this->Session->read('Cruxe.options') as $i => $nombre) {

						$dt = (string) $valor[$i];
						
						if (empty($dt))
							continue;
						
						$transaccion = array_unique(Hash::extract($transacciones, '{n}.VentaTransaccion[nombre='.$dt.']'));
						
						if (empty($transaccion))
							continue;
						
						$dtes = Hash::extract($transacciones, '{n}.Venta[id='.(string) $transaccion[0]['venta_id'].'].Dte.{n}');

						if (empty($dtes)) 
							continue;

						$DtesController = new DtesController(new CakeRequest(), new CakeResponse());

						$folio = '';
						$rut_receptor = '';
						$tipo_documento = '';
						$monto_documento = '';
						foreach ($dtes as $ia => $d) {
							// Sólo boletas y facturas
							if ($d['tipo_documento'] == 33 || $d['tipo_documento'] == 39) {
								$folio           = $d['folio'];
								$rut_receptor    = formato_rut($d['rut_receptor']);
								$tipo_documento  = $DtesController->tipoDocumento[$d['tipo_documento']];
								$monto_documento = CakeNumber::currency($d['total'], 'CLP');
							}
						}

						// Agreamos los valores a la columna y fila correspondiente
						$spreadsheet->setActiveSheetIndex(0)
						->setCellValueByColumnAndRow( $ultimaColumna, $indice, $folio )
						->setCellValueByColumnAndRow( $ultimaColumna+1, $indice, $rut_receptor )
						->setCellValueByColumnAndRow( $ultimaColumna+2, $indice, $tipo_documento )
						->setCellValueByColumnAndRow( $ultimaColumna+3, $indice, $monto_documento );
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

		BreadcrumbComponent::add('Listado de dte´s', '/dtes');
		BreadcrumbComponent::add('Cruzar datos');

		$this->set(compact('datos'));
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

}