<?php
App::uses('AppController', 'Controller');

class DtesController extends AppController
{	
	public $tipoDocumento = array(
		#30 => 'factura',
		#32 => 'factura de venta bienes y servicios no afectos o exentos de IVA',
		#35 => 'Boleta',
		#38 => 'Boleta exenta',
		#45 => 'factura de compra',
		#55 => 'nota de débito',
		#60 => 'nota de crédito',
		#103 => 'Liquidación',
		#40 => 'Liquidación Factura',
		#43 => 'Liquidación - Factura Electrónica',
		33 => 'Factura Electrónica',
		#34 => 'Factura No Afecta o Exenta Electrónica',
		39 => 'Boleta Electrónica',
		#41 => 'Boleta Exenta Electrónica',
		#46 => 'Factura de Compra Electrónica',
		56 => 'Nota de Débito Electrónica',
		61 => 'Nota de Crédito Electrónica',
		#50 => 'Guía de Despacho',
		52 => 'Guía de Despacho Electrónica',
		#110 => 'Factura de Exportación Electrónica',
		#111 => 'Nota de Débito de Exportación Electrónica',
		#112 => 'Nota de Crédito de Exportación Electrónica',
		#801 => 'Orden de Compra', 
		#802 => 'Nota de pedido',
		#803 => 'Contrato',
		#804 => 'Resolución',
		#805 => 'Proceso ChileCompra',
		#806 => 'Ficha ChileCompra',
		#807 => 'DUS',
		#808 => 'B/L (Conocimiento de embarque)',
		#809 => 'AWB (Air Will Bill)',
		#810 => 'MIC/DTA',
		#811 => 'Carta de Porte',
		#812 => 'Resolución del SNA donde califica Servicios de Exportación',
		#813 => 'Pasaporte',
		#814 => 'Certificado de Depósito Bolsa Prod. Chile',
		#815 => 'Vale de Prenda Bolsa Prod. Chile'
	);


	/**
	 * Códigos de Webpay
	 * @var array
	 */
	public $paymentTypeCodearray = array(
        "VD" => "Venta Debito",
        "VN" => "Venta Normal", 
        "VC" => "Venta en cuotas", 
        "SI" => "3 cuotas sin interés", 
        "S2" => "2 cuotas sin interés", 
        "NC" => "N cuotas sin interés", 
    );


	/**
	 * Retorna el estado según su código
	 * @param  string  $slug  código del estado
	 * @return string         Valor humanizado del código
	 */
	public function dteEstado($slug = '')
    {
    	if (!empty($slug)) {
    		$estados = array(
    			'no_generado' => 'DTE no emitido',
    			'dte_temporal_no_emitido' => 'DTE Temporal no emitido',
    			'dte_real_no_emitido' => 'DTE Real no emitido',
    			'dte_real_emitido' => 'DTE Emitido'
    		);

    		return $estados[$slug];
    	}

    	return 'DTE no emitido';
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

		// Filtrado de dtes por formulario
		if ( $this->request->is('post') ) {

			$this->filtrar('dtes', 'index');

		}

		$paginate = array_replace_recursive($paginate, array(
			'limit' => 20,
			'conditions' => array(
				'Dte.tienda_id' => $this->Session->read('Tienda.id')
			),
			'order' => array('Dte.fecha' => 'DESC'),
			'contain' => array(
				'Venta' => array(
					'MedioPago'
				)
			)
			));


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'by':
						if ($valor == 'fol' && isset($this->request->params['named']['txt'])) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.folio LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}

						if ($valor == 'ord' && isset($this->request->params['named']['txt'])) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.id_order LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}

						if ($valor == 'rut' && isset($this->request->params['named']['txt'])) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.rut_receptor LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}
						
						break;
					case 'tyd':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.tipo_documento' => $valor)));
						break;
					case 'sta':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.estado' => $valor)));
						break;
					case 'dtf':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.fecha >=' => trim($valor))));
						break;
					case 'dtt':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.fecha <=' => trim($valor))));
						break;
				}
			}
		}


		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden', 'WebpayStore'));
		
		$this->paginate = $paginate;

		$dtes	= $this->paginate();
		$totalMostrados = $this->Dte->find('count');

		BreadcrumbComponent::add('DTE´s ');


		$estados = Hash::extract($dtes, '{n}.Dte.estado');

		$this->set(compact('dtes', 'estados'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Dte->create();
			if ( $this->Dte->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		$ordenes	= $this->Dte->Orden->find('list');
		$this->set(compact('ordenes'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Dte->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Dte->save($this->request->data) )
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
			$this->request->data	= $this->Dte->find('first', array(
				'conditions'	=> array('Dte.id' => $id)
			));
		}
		$ordenes	= $this->Dte->Orden->find('list');
		$this->set(compact('ordenes'));
	}

	public function admin_delete($id = null)
	{
		$this->Dte->id = $id;
		if ( ! $this->Dte->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Dte->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}


	/**
	 * Método encargado de ajustar los datos al excel.
	 * @param  array  $data DTES
	 * @return array       datos del excel
	 */
	public function prepararExcel($data = array())
	{	
		$newData = array();

		foreach ($data as $indice => $valor) {
				
			$newData[$indice]['Dte']['id_order']                  = $valor['Dte']['id_order'];
			$newData[$indice]['Dte']['referencia']                = (!empty($valor['Orden'])) ? $valor['Orden']['reference'] : 'Nulo';

			# Identificador de la/s transacciones
			$newData[$indice]['Dte']['id_transaccion']            = (!empty($valor['Orden'])) ? implode(Hash::extract($valor['Orden'], 'OrdenPago.{n}.transaction_id')) : 'Nulo';

			# Webpay
			$newData[$indice]['Dte']['authorization_code_webpay'] = (!empty($valor['Orden'])) ? implode(Hash::extract($valor['Orden'], 'Carro.WebpayStore.{n}.authorization_code')) : 'Nuelo';

			$newData[$indice]['Dte']['metodo']                    = (!empty($valor['Orden'])) ? $valor['Orden']['payment'] : 'Nulo';
			$newData[$indice]['Dte']['total']                     = (!empty($valor['Orden'])) ? CakeNumber::currency($valor['Orden']['total_paid'], 'CLP') : 'Nuelo';
			$newData[$indice]['Dte']['envio']                     = (!empty($valor['Orden'])) ? CakeNumber::currency($valor['Orden']['total_shipping'], 'CLP') : 'Nulo';
			$newData[$indice]['Dte']['folio']                     = (!empty($valor['Dte']['folio'])) ? $valor['Dte']['folio'] : 'No aplica';
			$newData[$indice]['Dte']['tipo_documento']            = $this->tipoDocumento[$valor['Dte']['tipo_documento']];
			$newData[$indice]['Dte']['rut_receptor']              = (!empty($valor['Dte']['rut_receptor'])) ? $valor['Dte']['rut_receptor'] : 'No aplica';
			$newData[$indice]['Dte']['estado']                    = $this->dteEstado($valor['Dte']['estado']);
			$newData[$indice]['Dte']['fecha']                     = $valor['Dte']['fecha'];
			
			# Webpay
			$newData[$indice]['Dte']['amount_webpay']             = (!empty($valor['Orden'])) ? implode(Hash::extract($valor['Orden'], 'Carro.WebpayStore.{n}.amount')) : 'Nulo';
			$newData[$indice]['Dte']['payment_type_webpay']       = (!empty($valor['Orden'])) ? implode(Hash::extract($valor['Orden'], 'Carro.WebpayStore.{n}.payment_type')) : 'Nulo';
			$newData[$indice]['Dte']['create_webpay']             = (!empty($valor['Orden'])) ? implode(Hash::extract($valor['Orden'], 'Carro.WebpayStore.{n}.create')) : 'Nulo';
			$newData[$indice]['Dte']['reponse_code']              = (!empty($valor['Orden'])) ? implode(Hash::extract($valor['Orden'], 'Carro.WebpayStore.{n}.reponse_code')) : 'Nulo';

		}

		return $newData;
	}


	public function admin_exportar()
	{	
		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
		set_time_limit(600);

		$this->verificarTienda();

		$query = array(
			'conditions' => array(),
			'contain' => array(
				'Venta' => array(
					'MedioPago' => array(
						'fields' => array(
							'MedioPago.id', 'MedioPago.nombre'
						)
					),
					'VentaTransaccion' => array(
						'fields' => array(
							'VentaTransaccion.id', 'VentaTransaccion.nombre'
						)
					),
					'WebpayStore' => array(
						'fields' => array(
							'WebpayStore.id_webpay_detail_order', 'WebpayStore.authorization_code', 'WebpayStore.amount', 'WebpayStore.payment_type', 'WebpayStore.create', 'WebpayStore.reponse_code'
						)
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.total', 'Venta.costo_envio'
					)
				)
			),
			'fields' => array(
				'Dte.venta_id', 'Dte.folio', 'Dte.tipo_documento', 'Dte.rut_receptor', 'Dte.estado', 'Dte.fecha'
			),
			'order' => array('Dte.folio' => 'DESC')
		);
		
		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'by':
						if ($valor == 'fol' && isset($this->request->params['named']['txt'])) {
							$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.folio LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}

						if ($valor == 'ord' && isset($this->request->params['named']['txt'])) {
							$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.venta_id LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}

						if ($valor == 'rut' && isset($this->request->params['named']['txt'])) {
							$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.rut_receptor LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}
						
						break;
					case 'tyd':
						$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.tipo_documento' => $valor)));
						break;
					case 'sta':
						$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.estado' => $valor)));
						break;
					case 'dtf':
						$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.fecha >=' => trim($valor))));
						break;
					case 'dtt':
						$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.fecha <=' => trim($valor))));
						break;
				}
			}
		}

		$datos = $this->Dte->find('all', $query);

		$TiposDocs = $this->tipoDocumento;
		
		$this->set(compact('datos', 'TiposDocs'));

	}


	public function admin_generarpdf()
	{	

		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
		set_time_limit(0);
		ini_set('memory_limit', '5120M');
		$this->verificarTienda();

		$resultados = array(
			'success' => array(
				'total' => 0,
				'messages' => array(
				)
			),
			'errors' => array(
				'total' => 0,
				'messages' => array(
				)
			)
		);

		// Filtrado de dtes por formulario
		if ( $this->request->is('post') ) {

			$this->filtrar('dtes', 'generarpdf');

		}

		if (isset($this->request->params['named']['generarpdf'])) {

			$query = array(
				'conditions' => array(),
				'order' => array('Dte.folio' => 'DESC'),
				'fields' => array(
					'Dte.id',
					'Dte.id_order',
					'Dte.folio',
					'Dte.tipo_documento',
					'Dte.rut_receptor',
					'Dte.estado',
					'Dte.fecha',
					'Dte.pdf'
				)
			);

			# Filtrar
			if ( isset($this->request->params['named']) ) {
				foreach ($this->request->params['named'] as $campo => $valor) {
					switch ($campo) {
						case 'by':
							if ($valor == 'fol' && isset($this->request->params['named']['txt'])) {
								$query = array_replace_recursive($query, array(
								'conditions' => array('Dte.folio LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
							}

							if ($valor == 'ord' && isset($this->request->params['named']['txt'])) {
								$query = array_replace_recursive($query, array(
								'conditions' => array('Dte.id_order LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
							}

							if ($valor == 'rut' && isset($this->request->params['named']['txt'])) {
								$query = array_replace_recursive($query, array(
								'conditions' => array('Dte.rut_receptor LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
							}
							
							break;
						case 'tyd':
							$query = array_replace_recursive($query, array(
								'conditions' => array('Dte.tipo_documento' => $valor)));
							break;
						case 'sta':
							$query = array_replace_recursive($query, array(
								'conditions' => array('Dte.estado' => $valor)));
							break;
						case 'dtf':
							$query = array_replace_recursive($query, array(
								'conditions' => array('Dte.fecha >=' => trim($valor))));
							break;
						case 'dtt':
							$query = array_replace_recursive($query, array(
								'conditions' => array('Dte.fecha <=' => trim($valor))));
							break;
					}
				}
			}


			$datos = $this->Dte->find('all', $query);

			$pdfs       = array();
			$limite     = 500;
			$lote = 0;
			$ii = 1;

			foreach ($datos as $i => $dato) {
				if (!empty($dato['Dte']['pdf']) && !empty($dato['Dte']['id_order']) ) {

					$pdfFile = APP . 'webroot' . DS. 'Dte' . DS . $dato['Dte']['id_order'] . DS . $dato['Dte']['id'] . DS . $dato['Dte']['pdf']; 

					if (file_exists($pdfFile)) {
						
						$pdfs[$lote][$ii] = $pdfFile;

						if ($ii%$limite == 0) {
							$lote++;
						}	
						
						$resultados['success']['total'] = $resultados['success']['total'] + 1;
						$resultados['success']['messages'][] = 'DTE folio n° '.$dato['Dte']['folio'].' listo para procesar.';
					}else{
						$resultados['errors']['total'] = $resultados['errors']['total'] + 1;
						$resultados['errors']['messages'][] = 'No se encontró pdf para el DTE folio n°' . $dato['Dte']['folio'] . '. Debe ser generado manualmente.';
					}

				}else{
					$resultados['errors']['total'] = $resultados['errors']['total'] + 1;
					if (!empty($dato['Dte']['folio'])) {
						$resultados['errors']['messages'][] = 'El DTE folio n°' . $dato['Dte']['folio'] . ' no tiene PDF generado.';
					}else{
						$resultados['errors']['messages'][] = 'El DTE identificador n°' . $dato['Dte']['id'] . ' no tiene PDF generado.';
					}
					
				}

				$ii++;
			}
			
			include '../Vendor/PDFMerger/PDFMerger.php';

			# Se procesan por Lotes de 500 documentos para no volcar la memoria
			foreach ($pdfs as $ip => $lote) {
				$pdf = new PDFMerger;
				foreach ($lote as $id => $document) {
					$pdf->addPDF($document, 'all');	
				}
				try {
					
					$pdfname = 'maestro-' . date('YmdHis') .'.pdf';

					$res = $pdf->merge('file', APP . 'webroot' . DS. 'DteMaestros' . DS . $pdfname);
					if ($res) {
						$resultados['pdf']['result']['lote_'.$ip]['document'] = Router::url('/', true) . 'DteMaestros/' . $pdfname;
					}

				} catch (Exception $e) {
					$resultados['errors']['messages'][] = $e->getMessage();
				}
			}
		} // End generar pdf request


		// Listar todos los PDFS
		$archivos = $this->obtenerListadoDeArchivos(APP . 'webroot' . DS. 'DteMaestros' . DS);
		
		$archivos = Hash::sort($archivos, '{n}.Modificado', 'DESC');


		BreadcrumbComponent::add('DTE´s', '/dtes');
		BreadcrumbComponent::add('Ver PDF Maestros ');


		$this->set(compact('resultados', 'archivos'));

	}


	public function obtenerListadoDeArchivos($directorio){
 
	  // Array en el que obtendremos los resultados
	  $res = array();
	 
	  // Agregamos la barra invertida al final en caso de que no exista
	  if(substr($directorio, -1) != DS) $directorio .= DS;
	 
	  // Creamos un puntero al directorio y obtenemos el listado de archivos
	  $dir = @dir($directorio) or die("getFileList: Error abriendo el directorio $directorio para leerlo");
	  while(($archivo = $dir->read()) !== false) {
	      // Obviamos los archivos ocultos
	      if($archivo[0] == ".") continue;
	      if(is_dir($directorio . $archivo)) {
	          $res[] = array(
	            "Nombre" => $archivo . DS,
	            "Directorio" => $directorio,
	            "Tamaño" => 0,
	            "Modificado" => filemtime($directorio . $archivo)
	          );
	      } else if (is_readable($directorio . $archivo)) {
	          $res[] = array(
	            "Nombre" => $archivo,
	            "Ruta_completa" => Router::url('/', true) . 'DteMaestros/' . $archivo,
	            "Directorio" => $directorio,
	            "Tamaño" => $this->formatBytes(filesize($directorio . $archivo), 2),
	            "Modificado" => date('Y-m-d H:i:s',filemtime($directorio . $archivo))
	          );
	      }
	  }
	  $dir->close();
	  return $res;
	}


	public function formatBytes($bytes, $precision = 2) { 
	    $units = array('B', 'KB', 'MB', 'GB', 'TB'); 

	    $bytes = max($bytes, 0); 
	    $pow = floor(($bytes ? log($bytes) : 0) / log(1024)); 
	    $pow = min($pow, count($units) - 1); 

	    // Uncomment one of the following alternatives
	     $bytes /= pow(1024, $pow);
	    // $bytes /= (1 << (10 * $pow)); 

	    return round($bytes, $precision) . ' ' . $units[$pow]; 
	}
}