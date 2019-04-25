<?php
App::uses('Component', 'Controller');
App::uses('AppController', 'Controller');
App::import('Vendor', 'LibreDTE', array('file' => 'LibreDte/autoload.php'));
App::import('Vendor', 'LibreDTE', array('file' => 'LibreDte/sasco/libredte-sdk-php/sdk/LibreDTE.php'));

class LibreDteComponent extends Component
{	
	/**
	 * Cliente LibreDte
	 */
	public $ConexionLibreDte;
	public $url = 'https://libredte.cl';



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


	public function crearCliente($hash, $ssl = true)
	{
		// crear cliente
		$this->ConexionLibreDte = new \sasco\LibreDTE\SDK\LibreDTE($hash, $this->url);
		$this->ConexionLibreDte->setSSL(false, $ssl); ///< segundo parámetro =false desactiva verificación de SSL	
	}


	/**
	 * Obtiene las comunas de Chile actualiadas desde la API disponible en http://apis.digital.gob.cl
	 * @return 		Array 	Arreglo con las Comunas key:par
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
	public function obtenerPdfDteTemporal($receptor, $tipo , $temporal, $emisor) 
	{
		# Obtenemos el PDFasd
		$pdf = $this->ConexionLibreDte->get('/dte/dte_tmps/pdf/'.$receptor.'/'.$tipo.'/'.$temporal.'/'.$emisor);
		
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
	 * Crea el DTE temporal y modifica el DTE creado localmente.
	 * @param  array 		$dataDte 		Información del DTE a crear     
	 * @param  array  		&$dteInterno 	DTE local a modificar 
	 * @return
	 */
	public function crearDteTemporal($dataDte, &$dteInterno = array())
	{	
		// crear DTE temporal
		$emitir = $this->ConexionLibreDte->post('/dte/documentos/emitir', $dataDte);

		if ($emitir['status']['code'] != 200) {

			# Guardamos el estado
		    $dteInterno['Dte']['estado'] = 'dte_temporal_no_emitido';
		    ClassRegistry::init('Dte')->save($dteInterno);

		    # Mensaje de retorno
		    throw new Exception("Error al generar el DTE temporal: " . $emitir['body'], $emitir['status']['code']);
		    return;

		}else{

			# Guardamos el estado
			$dteInterno['Dte']['estado'] = 'dte_temporal_emitido';
			$dteInterno['Dte']['dte_temporal'] = $emitir['body']['codigo'];
			$dteInterno['Dte']['emisor'] = $emitir['body']['emisor'];
			$dteInterno['Dte']['receptor'] = $emitir['body']['receptor'];
			ClassRegistry::init('Dte')->save($dteInterno);

			return $emitir['body'];
		}
	}


	/**
	 * Crea el DTE real y modifica el DTE creado localmente.
	 * @param  array 		$dte_temporal 		Dte temporal devuelto por LibreDte      
	 * @param  array  		&$dteInterno 		DTE local a modificar 
	 * @return
	 */
	public function crearDteReal($dte_temporal, &$dteInterno = array())
	{
		// crear DTE real
		$generar = $this->ConexionLibreDte->post('/dte/documentos/generar', $dte_temporal);
	
		if ($generar['status']['code']!=200) {

		    # Guardamos el estado
		    $dteInterno['Dte']['estado'] = 'dte_real_no_emitido';
		    ClassRegistry::init('Dte')->save($dteInterno);

		    # Mensaje de retorno
		    throw new Exception("Error al generar el DTE Real: " . $generar['body'], $generar['status']['code']);
		    return;
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

			ClassRegistry::init('Dte')->save($dteInterno);

			// Se marca como atendida
			ClassRegistry::init('Venta')->id = $dteInterno['Dte']['venta_id'];
			ClassRegistry::init('Venta')->saveField('atendida', 1);

			# Mensaje de retorno
			throw new Exception("DTE generado con éxito.", $generar['status']['code']);
			return;
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
		# obtener el PDF del DTE
		$datos = [
		    'emisor' => $rut,
		    'dte' => $dte,
		    'folio' => $folio,
		    'fecha' => $fecha,
		    'total' => $total,
		];

		$consultar = $this->ConexionLibreDte->post('/dte/dte_emitidos/consultar?getXML='.$getXML, $datos);
		
		if ($consultar['status']['code']!=200) {
		    throw new Exception('Ocurrió un error al obtener el DTE desde el SII: ' . $consultar['body'], 400);
		}

		if ($consultar['body']['anulado'] || $consultar['body']['iva_fuera_plazo']) {
			throw new Exception('Este DTE ha sido anulado por el SII o el IVA se encentra fuera de plazo. Estado de la revisión:' . $consultar['body']['revision_estado'] , 200);
		}

		return;
	}

	/**
	 * Obtiene el estado el DTE desde el SII
	 * @param  [type] $dte    [description]
	 * @param  [type] $folio  [description]
	 * @param  [type] $emisor [description]
	 * @return [type]         [description]
	 */
	public function consultarDteSii($dte, $folio, $emisor)
	{
		$res = array(
			'estado' => '',
			'detalle' => ''
			);

		$consultar = $this->ConexionLibreDte->get('/dte/dte_emitidos/actualizar_estado/'.$dte.'/'.$folio.'/'.$emisor);
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

			# Generar PDF
			$generar_pdf = $this->ConexionLibreDte->get('/dte/dte_emitidos/pdf/'.$tipo_dte.'/'.$folio.'/'.$emisor);
			
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



	public function eliminarDteTemporal($rut_receptor, $tipo_documento, $dte_temporal, $rut_emisor)
	{
		$eliminar = $this->ConexionLibreDte->get('/dte/dte_tmps/eliminar/'.$receptor.''.$tipo_documento.''.$dte_temporal.''.$rut_emisor);

		if ($eliminar['status']['code'] == 200) {
			return;
		}else{
			return $eliminar['body'];
		}
	}


	/**
	 * Retorna una lista ordenada de los documentos autorizados en LibreDte
	 * @param 		int 		$rut_contribuyente 		Rut de la empresa
	 * @param 		bool 		$ajax 					Define el formato de la respuesta
	 * @return 		json o array 
	 */
	public function dtePermitidos($rut_contribuyente, $ajax = false)
	{
		# Obtenemos información del contribuyente
		$contribuyente = $this->ConexionLibreDte->get('/dte/contribuyentes/config/'.$rut_contribuyente);
		
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
	 * Retorna los datos del rut del contribuyente consultado
	 * @param 		$rut_contribuyente 		int 		Rut a buscar
	 * @return 		array/json
	 */
	public function obtenerContribuyente($rut_contribuyente) 
	{
		# Obtenemos información del contribuyente
		$contribuyente = $this->ConexionLibreDte->get('/dte/contribuyentes/info/'.$rut_contribuyente);
		
		if ($contribuyente['status']['code'] == 200) {
			return $contribuyente['body'];
		}

		return;
	}



	/**
	 * Retorna la cantidad de folios disponibles que tiene el usuario
	 */
	public function obtenerFoliosDisponibles($tipo_dte, $rut_contribuyente) 
	{
		# Obtenemos información de los folios
		$folios = $this->ConexionLibreDte->get('/dte/admin/dte_folios/info/'.$tipo_dte.'/'.$rut_contribuyente);
		
		if ($folios['status']['code'] == 200) {
			return $folios['body'];
		}

		return;
	}


	public function enviarDteEmail($emails = array(), $dte = '', $folio = '', $emisor = '', $asunto = '', $mensaje = '', $pdf = true, $cedible = true, $papelContinuo = 0)
	{
		# Esquema para datos
		$datos = array(
			'emails' => $emails,
			'asunto' => $asunto,
			'mensaje' => $mensaje,
			'pdf' => $pdf,
			'cedible' => $cedible,
			'papelContinuo' => $papelContinuo
		);
		
		$enviar = $this->ConexionLibreDte->post('/dte/dte_emitidos/enviar_email/'.$dte.'/'.$folio.'/'.$emisor, $datos);

		if ($enviar['status']['code'] == 200) {
			return true;
		}else{
			return $enviar['body'];
		}
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
		$dte_tmp = ClassRegistry::init('Dte')->find('first', array('conditions' => array('Dte.id' => $id_dte)));

		if (!empty($dte_tmp) && !empty($dte_tmp['Dte']['dte_temporal'])) {
			
			if ($dte_tmp['Dte']['estado'] == 'dte_temporal_emitido' || $dte_tmp['Dte']['estado'] == 'dte_real_no_emitido') {
				
				$data = array(
					'emisor' => $dte_tmp['Dte']['emisor'],
					'receptor' => $dte_tmp['Dte']['receptor'],
					'dte' => $dte_tmp['Dte']['tipo_documento'],
					'codigo' => $dte_tmp['Dte']['dte_temporal']
				);

				// crear DTE real
				$generar = $this->ConexionLibreDte->post('/dte/documentos/generar', $data);
				
				if ($generar['status']['code']!=200) {

				    # Guardamos el estado
				    $dte_tmp['Dte']['estado'] = 'dte_real_no_emitido';
				    ClassRegistry::init('Dte')->save($dte_tmp);

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

					ClassRegistry::init('Dte')->save($dte_tmp);

					# Mensaje de retorno
					throw new Exception("DTE generado con éxito.", $generar['status']['code']);
				}
			}
		}

		# Mensaje de retorno
		throw new Exception("Error al generar el DTE Real. No estan todos los campos completos.", 402);
	}

}