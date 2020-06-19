<?php 
App::uses('Component', 'Controller');
App::import('Vendor', 'Starken', array('file' => 'Starken/starken-ws.class.php'));
App::import('Vendor', 'LAFFPack', array('file' => 'Starken/LAFFPack.php'));
App::import('Vendor', 'PDFMerger', array('file' => 'PDFMerger/PDFMerger.php'));

class StarkenComponent extends Component
{
	
	private $StarkenConexion;

	private $tipoEntrega = array(
		1 => 'AGENCIA',
		2 => 'DOMICILIO'
	);

	private $tipoPago = array(
		2 => 'Con Cta Corriente',
		3 => 'Por Pagar en Destino'
	);

	private $tipoServicio = array(
		1 => 'EXPRESS'
	);

	public function crearCliente($rutApiRest, $claveApiRest, $rutEmpresaEmisora, $rutUsuarioEmisor, $claveUsuarioEmisor, $numeroCtaCte = null, $dvNumeroCtaCte = null, $centroCostoCtaCte = null)
	{	
		$this->StarkenConexion = new StarkenWebServices($rutApiRest, $claveApiRest, $rutEmpresaEmisora, $rutUsuarioEmisor, $claveUsuarioEmisor, $numeroCtaCte, $dvNumeroCtaCte, $centroCostoCtaCte);
	}


	public function generar_ot($venta)
	{	
		$volumenMaximo = (float) 60;
		$pesoTotal     = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.VentaDetalleProducto.peso')) * array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_reservada'));
		$pesoMaximo    = $venta['MetodoEnvio']['peso_maximo'];
		
		$log = array();

		# Limite de peso para usar starken
		if ($pesoTotal >= $pesoMaximo) {
			return false;
		}	

		# No se permite ot con productos sin peso
		if ($pesoTotal == 0) {
			return false;
		}	
		
		$paquetes = $this->obtener_bultos_venta($venta, $volumenMaximo);
		
		if (empty($paquetes)) {
			return;
		}

		# Si los paquetes no tienen dimensiones se setean con el valor default
		foreach ($paquetes as $ip => $paquete) {
			
			if($paquete['paquete']['length'] == 0 ||
				$paquete['paquete']['width'] == 0 ||
				$paquete['paquete']['height'] == 0) {
					
				$paquetes[$ip]['paquete']['length'] = $venta['MetodoEnvio']['largo_default'];
				$paquetes[$ip]['paquete']['width']  = $venta['MetodoEnvio']['ancho_default'];
				$paquetes[$ip]['paquete']['height'] = $venta['MetodoEnvio']['alto_default'];

			}

		}


		$transportes = array();

		# Mantenemos las ot ya generadas
		foreach ($venta['Transporte'] as $key => $t) {
			$transportes[] = array(
				'id'              => $t['TransportesVenta']['id'],
				'transporte_id'   => $t['id'],
				'cod_seguimiento' => $t['TransportesVenta']['cod_seguimiento'],
				'etiqueta'        => $t['TransportesVenta']['etiqueta'],
				'entrega_aprox'   => $t['TransportesVenta']['entrega_aprox']
			);
		}
		
		$ruta_pdfs = array();

		foreach ($paquetes as $id_venta => $paquete) {

			# dimensiones de todos los paquetes unificado
			$largoTotal = $paquete['paquete']['length'];
			$anchoTotal = $paquete['paquete']['width'];
			$altoTotal  = $paquete['paquete']['height'];
			
			# Normalizamos el rut
			$venta['Venta']['rut_receptor'] = str_replace('-', '', $venta['Venta']['rut_receptor']);
			$venta['Venta']['rut_receptor'] = trim(str_replace('.', '', $venta['Venta']['rut_receptor']));

			# separamos el rut
			$rut_destinatario = substr($venta['Venta']['rut_receptor'], 0, (strlen($venta['Venta']['rut_receptor']) - 1));	
			$dv_destinatario  = substr($venta['Venta']['rut_receptor'], -1);

			# creamos el arreglo para generar la OT
			$data = array(
				'rutEmpresaEmisora' 				=> $venta['MetodoEnvio']['rut_empresa_emisor'],
				'rutUsuarioEmisor'					=> $venta['MetodoEnvio']['rut_usuario_emisor'],
				'claveUsuarioEmisor'				=> $venta['MetodoEnvio']['clave_usuario_emisor'],
				'rutDestinatario'                   => $rut_destinatario,
				'dvRutDestinatario'                 => $dv_destinatario,
				'nombreRazonSocialDestinatario'     => $venta['VentaCliente']['nombre'],
				'apellidoPaternoDestinatario'       => $venta['VentaCliente']['apellido'],
				'apellidoMaternoDestinatario'       => '.',
				'direccionDestinatario'             => trim($venta['Venta']['direccion_entrega']),
				'numeracionDireccionDestinatario'   => trim($venta['Venta']['numero_entrega']),
				'departamentoDireccionDestinatario' => trim($venta['Venta']['otro_entrega']),
				'comunaDestino'                     => trim($venta['Venta']['comuna_entrega']),
				'telefonoDestinatario'              => trim($venta['Venta']['fono_receptor']),
				'emailDestinatario'                 => trim($venta['VentaCliente']['email']),
				'nombreContactoDestinatario'        => trim($venta['VentaCliente']['nombre']) . ' ' . trim($venta['VentaCliente']['apellido']),
				'tipoEntrega'                       => $venta['MetodoEnvio']['tipo_entrega'],
				'tipoPago'                       	=> $venta['MetodoEnvio']['tipo_pago'],
				'numeroCtaCte'                      => $venta['MetodoEnvio']['numero_cuenta_corriente'],
				'dvNumeroCtaCte'                    => $venta['MetodoEnvio']['dv_numero_cuenta_corriente'],
				'centroCostoCtaCte'                 => $venta['MetodoEnvio']['centro_costo_cuenta_corriente'],
				'valorDeclarado'                    => round($venta['Venta']['total']),
				'contenido'                         => substr(implode(' | ', Hash::extract($venta['VentaDetalle'], '{n}[venta_id='.$paquete['paquete']['venta_id'].'].VentaDetalleProducto.id')), 0, 40 ),
				'kilosTotal'                        => round($pesoTotal, 0),
				'alto'                              => $altoTotal,
				'ancho'                             => $anchoTotal,
				'largo'                             => $largoTotal,
				'tipoServicio'                      => (empty($venta['MetodoEnvio']['tipo_servicio'])) ? '0' : $venta['MetodoEnvio']['tipo_servicio'],
				'ciudadOrigenNom'                   => trim($venta['MetodoEnvio']['ciudad_origen']),
				'observacion'                       => 'OT generada automáticamente por ' . $venta['Tienda']['nombre'] . ' - Venta Ref: ' . $venta['Venta']['referencia'],
				'encargos' => array(
					0 => array(
						'tipoEncargo' => 29, // bultos,
						'cantidadEncargo' => 1 // simepre 1 bulto
					)
				)
			);


			# Se agregan documentos de referencia
			if (!empty($venta['Dte'])) {
				foreach ($venta['Dte'] as $id => $dte) {

					# Factura
					if ($dte['tipo_documento'] == 33 && !$dte['invalidado']) {
						$data = array_replace_recursive($data, array(
							'documentos' => array(
								$id => array(
									'tipoDocumento' => 26,
									'numeroDocumento' => $dte['folio'],
									'generaEtiquetaDocumento' => 'N'
								)
							)
						));
					}

					# Boleta
					if ($dte['tipo_documento'] == 39 && !$dte['invalidado']) {
						$data = array_replace_recursive($data, array(
							'documentos' => array(
								$id => array(
									'tipoDocumento' => 28,
									'numeroDocumento' => $dte['folio'],
									'generaEtiquetaDocumento' => 'N'
								)
							)
						));
					}
				}
			}
			
			$log[] = array(
				'Log' => array(
					'administrador' => 'Straken',
					'modulo' => 'Ventas',
					'modulo_accion' => 'Request: ' . json_encode($data)
				)
			);
	
			$response = json_decode($this->StarkenConexion->generarOrden(json_encode($data)), true);

			$log[] = array(
				'Log' => array(
					'administrador' => 'Straken',
					'modulo' => 'Ventas',
					'modulo_accion' => 'Response: ' . json_encode($response)
				)
			);

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);
			
			if ($response['code'] != 'success') {
				return false;
			}

			if ($response['body']['codigoError'] != 0) {
				return false;
			}

			#Generamos la etiqueta
			$etiquetaZpl = $this->getEtiquetaEmision($response, $venta);	
		
			$etiquetaPdf = '';

			$pathEtiquetas  = APP . 'webroot' . DS . 'img' . DS . 'ModuloStarken' . DS . $venta['Venta']['id'] . DS;
			$nombreEtiqueta = $response['body']['nroOrdenFlete'] . '.pdf';
			

			$curl = curl_init();
			// adjust print density (8dpmm), label width (4 inches), label height (6 inches), and label index (0) as necessary
			curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/4x4/0/");
			curl_setopt($curl, CURLOPT_POST, TRUE);
			curl_setopt($curl, CURLOPT_POSTFIELDS, $etiquetaZpl);
			curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
			curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/pdf")); // omit this line to get PNG images back
			$etiquetaPdf = curl_exec($curl);

			if (!is_dir($pathEtiquetas)) {
				@mkdir($pathEtiquetas, 0775, true);
			}

			if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200) {
			    $file = fopen($pathEtiquetas . $nombreEtiqueta, "w"); // change file name for PNG images
			    fwrite($file, $etiquetaPdf);
			    fclose($file);

			    $rutaPublica = obtener_url_base() . 'img/ModuloStarken/' . $venta['Venta']['id'] . '/' . $nombreEtiqueta;
			    $ruta_pdfs[] = $pathEtiquetas . $nombreEtiqueta;

			}else{
				$rutaPublica = '';
			}

			curl_close($curl);
			
			
			# Guardamos el transportista y el/los numeros de seguimiento
			$carrier_name = 'STARKEN/TURBUS';
			$carrier_opt = array(
				'Transporte' => array(
					'codigo' => 'STARKEN-TURBUS',
					'url_seguimiento' => 'https://www.starken.cl/seguimiento' // Url de seguimiento starken
				)
			);

			if (!empty($rutaPublica)) {
				$carrier_opt = array_replace_recursive($carrier_opt, array(
					'Transporte' => array(
						'etiqueta' => $rutaPublica
					)
				));	
			}

			$transportes[] = array(
				'transporte_id'   => ClassRegistry::init('Transporte')->obtener_transporte_por_nombre($carrier_name, true, $carrier_opt),
				'cod_seguimiento' => $response['body']['nroOrdenFlete'],
				'etiqueta'        => $rutaPublica,
				'entrega_aprox'   => $response['body']['fechaEstimadaEntrega']
			);
		}

		if (empty($transportes)) {
			return false;
		}

		
		# Se guarda la información del tracking en la venta
		$nwVenta = array(
			'Venta' => array(
				'id' => $venta['Venta']['id'],
				'paquete_generado' => 1
			),
			'Transporte' => $transportes
		);

		# unificar pdfs en 1 solo
		if (!empty($ruta_pdfs)) {
			$union = $this->unir_documentos($ruta_pdfs, $venta['Venta']['id']);
			
			if (!empty($union['result'])) {			
				# Tomamos el primer indice ya que jamás tendremos más de 500 etiquetas unidas pra una venta
				$nwVenta = array_replace_recursive($nwVenta, array(
					'Venta' => array(
						'etiqueta_envio_externa' => $union['result'][0]['document'],
					)
				));
			}
		}

		return ClassRegistry::init('Venta')->saveAll($nwVenta);

	}

	/**
	 * [unir_documentos description]
	 * @param  array  $archivos [description]
	 * @param  string $venta_id [description]
	 * @return [type]           [description]
	 */
	public function unir_documentos($archivos = array(), $venta_id = '')
	{
		$pdfs       = array();
		$limite     = 500;
		$lote = 0;
		$ii = 1;

		foreach ($archivos as $i => $archivo) {

			if (file_exists($archivo)) {
				$pdfs[$lote][$ii] = $archivo;

				if ($ii%$limite == 0) {
					$lote++;
				}	
			}

			$ii++;
		}

		if (!is_dir(APP . 'webroot' . DS. 'Venta' . DS . $venta_id)) {
			@mkdir(APP . 'webroot' . DS. 'Venta' . DS . $venta_id, 0775);
		}

		# Se procesan por Lotes de 500 documentos para no volcar la memoria
		foreach ($pdfs as $ip => $lote) {
			$pdf = new PDFMerger;
			foreach ($lote as $id => $document) {
				$pdf->addPDF($document, 'all');	
			}
			try {
				
				$pdfname = 'etiqueta-envio-' . date('YmdHis') .'.pdf';

				$res = $pdf->merge('file', APP . 'webroot' . DS. 'Venta' . DS . $venta_id . DS . $pdfname);
				if ($res) {
					$resultados['result'][]['document'] = Router::url('/', true) . 'Venta/' . $venta_id . '/' . $pdfname;
				}

			} catch (Exception $e) {
				$resultados['errors']['messages'][] = $e->getMessage();
			}
		}

		return $resultados;
	}

	public function getTipoEntregas()
	{
		return $this->tipoEntrega;
	}

	public function getTipoPagos()
	{
		return $this->tipoPago;
	}

	public function getTipoServicios()
	{
		return $this->tipoServicio;
	}


	/**
	 * Calcula a aproximacion de bltos que se deberían armar en base a los itemes
	 * @param  array $venta         Detalle de la venta
	 * @param  float $volumenMaximo volumen máximo para cada paquete
	 * @return array
	 */
	public function obtener_bultos_venta($venta, $volumenMaximo)
	{	
		$bultos = array();

		foreach ($venta['VentaDetalle'] as $ivd => $d) {

			if ($d['cantidad_reservada'] == 0) {
				continue;
			}

			for ($i=0; $i < $d['cantidad_reservada']; $i++) {

				$alto  = $d['VentaDetalleProducto']['alto'];
				$ancho = $d['VentaDetalleProducto']['ancho'];
				$largo = $d['VentaDetalleProducto']['largo'];
				$peso  = $d['VentaDetalleProducto']['peso'];

				$volumen = $this->calcular_volumen($alto, $ancho, $largo);

				$caja = array(
					'id'     => $d['VentaDetalleProducto']['id'],
					'width'  => $ancho,
					'height' => $alto,
					'length' => $largo,
					'weight' => $peso
				);

				$unico = rand(1000, 100000);
				
				if ($volumen > $volumenMaximo) {
					$bultos[$d['venta_id'] . $unico]['venta_id']    = $d['venta_id'];
					$bultos[$d['venta_id'] . $unico]['cajas'][]     = $caja;
				}else{
					$bultos[$d['venta_id']]['venta_id']    = $d['venta_id'];
					$bultos[$d['venta_id']]['cajas'][]     = $caja;
				}	
			}

		}
		
		$resultado = array();
		
		foreach ($bultos as $ib => $b) {
			$resultado[$ib]['paquete']             = $this->obtenerDimensionesPaquete($b['cajas']);
			$resultado[$ib]['paquete']['weight']   = array_sum(Hash::extract($b['cajas'], '{n}.weight'));
			$resultado[$ib]['paquete']['venta_id'] = $b['venta_id'];
			$resultado[$ib]['items']               = $b['cajas'];
		}

		return $resultado;
	}


	/**
	 * [calcular_volumen description]
	 * @param  float $largo cm
	 * @param  float $ancho cm
	 * @param  float $alto  cm
	 * @return float
	 */
	public function calcular_volumen($alto, $ancho, $largo)
	{	
		return (float) round( ($largo/100) * ($ancho/100) * ($alto/100), 2);
	}


	/**
	 * [obtenerDimensionesPaquete description]
	 * @param  array  $cajas [description]
	 * @return [type]        [description]
	 */
	public function obtenerDimensionesPaquete($cajas = array())
	{	
		$laftpack = new LAFFPack();
		$laftpack->pack($cajas);
        
        # Se obtienen las dimensiones del paquete
        $paquete = $laftpack->get_container_dimensions();

        return $paquete;
        
	}


	public function seguimiento($data)
	{	
		return json_decode($this->StarkenConexion->getSeguimiento(json_encode($data)), true);
	}


	public function listarCiudadesDestino()
	{	
		$this->StarkenConexion = new StarkenWebServices();
		return json_decode($this->StarkenConexion->listarCiudadesDestino(false, false), true);
	}


	public function listarCiudadesOrigen()
	{	
		$this->StarkenConexion = new StarkenWebServices();
		return json_decode($this->StarkenConexion->listarCiudadesOrigen(false, false), true);
	}

	public function getEtiquetaEmision($response, $venta) {
        	
		$etiqueta              = "";
		$remitenteNombre       = "";
		$direccionRemitente    = "";
		$direccionDestinatario = "";

        # Remitente
        if (($response['body']['direccionRemitente'] != null) && (!empty($response['body']['direccionRemitente']))) {
            $direccionRemitente = $direccionRemitente . $response['body']['direccionRemitente'];
        }
        if (($response['body']['comunaOrigen'] != null) && (!empty($response['body']['comunaOrigen']))) {
            $direccionRemitente = $direccionRemitente . "- " . $response['body']['comunaOrigen'];
        }
        if (($response['body']['ciudadOrigen'] != null) && (!empty($response['body']['ciudadOrigen']))) {
            $direccionRemitente = $direccionRemitente . "- " . $response['body']['ciudadOrigen'];
        }
        if (($response['body']['nombreEmpresaRemitente'] != null) && (!empty($response['body']['nombreEmpresaRemitente']))) {
            $remitenteNombre = $remitenteNombre . $response['body']['nombreEmpresaRemitente'];
        }

        # Detinatario
        if (($response['body']['direccionDestinatario'] != null) && (!empty($response['body']['direccionDestinatario']))) {
            $direccionDestinatario = $direccionDestinatario . $response['body']['direccionDestinatario'];
        }
        if (($response['body']['numeroDireccionDestinatario'] != null) && (!empty($response['body']['numeroDireccionDestinatario']))) {
            $direccionDestinatario = $direccionDestinatario . ' ' . $response['body']['numeroDireccionDestinatario'];
        }
        if (($response['body']['departamentoDireccionDestinatario'] != null) && (!empty($response['body']['departamentoDireccionDestinatario']))) {
            $direccionDestinatario = $direccionDestinatario . ' ' .  $response['body']['departamentoDireccionDestinatario'];
        }
        if (($response['body']['ciudadDestino'] != null) && (!empty($response['body']['ciudadDestino']))) {
            $direccionDestinatario = $direccionDestinatario . ' - ' . $response['body']['ciudadDestino'];
        }

        $tipo_servicio = (isset($this->tipoServicio[$venta['MetodoEnvio']['tipo_servicio']])) ? $this->tipoServicio[$venta['MetodoEnvio']['tipo_servicio']] : 'NORMAL';
        $tipo_entrega = $this->tipoEntrega[$venta['MetodoEnvio']['tipo_entrega']];	

        $etiqueta .= "\020CT~~CD,~CC^~CT~";
        $etiqueta .= "^XA~TA000~JSN^LT0^MNW^MTT^PON^PMN^LH0,0^JMA^PR5,5~SD30^JUS^LRN^CI0^XZ";
        $etiqueta .= "~DG000.GRF,02688,028,";
        $etiqueta .= ",:::::H0hMFE,H080hK02,::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::H0hMFE,,::::::::::::::::::::::::::~DG001.GRF,03584,028,";
        $etiqueta .= ",::::::H0hMFE,H080hK02,::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::H0hMFE,,:::::~DG002.GRF,02560,020,";
        $etiqueta .= ",L03FgKFE,L020gK02,::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::L03FgKFE,,:::::::::::::~DG003.GRF,09216,096,";
        $etiqueta .= ",:::::::::::::::::7FoIFE,40oI02,::::::::::::::::::::::::::::::::::::::::::::::::::::::7FoIFE,,::::::::::::::::::::~DG004.GRF,01536,024,";
        $etiqueta .= ",:::::::::H03FhF,H020gY01,:::::::::::::::::::::::::::::::::::H03FhF,,:::::::::::::::~DG005.GRF,20736,072,";
        $etiqueta .= ",::::::::::::mF,80lW01,::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::mF,,:::::::::::::::::::::::::::::~DG006.GRF,06144,048,";
        $etiqueta .= ",:::::::::H03FjKFE,H020jK02,:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::H03FjKFE,,:::::::::::::::::::::::::::::~DG007.GRF,02688,028,";
        $etiqueta .= ",::::::::::::I01FhJFE,I010hJ02,:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::I01FhJFE,,::::::::::::::::::~DG008.GRF,02560,040,";
        $etiqueta .= ",:::::I01FiOF80,I010iO080,::::::::::::::::::::::::::::::::::::I01FiOF80,,::::::::::::::::::~DG009.GRF,04608,072,";
        $etiqueta .= ",:::::7FlVF80,40lV080,::::::::::::::::::::::::::::::::::::7FlVF80,,::::::::::::::::::~DG010.GRF,12288,096,";
        $etiqueta .= ",oKFE,:C0oI06,::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::oKFE,:,:::::::::::::~DG011.GRF,36864,096,";
        $etiqueta .= ",:::::::::::oKFE,:C0oI06,:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::oKFE,:,:::::::::::~DG012.GRF,18432,096,";
        $etiqueta .= ",:::::::::::::oKFE,:C0oI06,:::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::::oKFE,:,:::::~DG013.GRF,02688,028,";
        $etiqueta .= ",::::::::::::::::::P0JFDFC1F9FHF8003FHF83F81F8FIF0,O01FIF9FC1F9FHFC003FHFC1F81F9FIF0,O01FIFBF83FBFHFE003FHFE3F83FBFIF0,O01FIFBF81F3FHFE003FHFE3F83F7FIF0,Q0HF03F83FHF87E003F8FE3F83FHF80,Q07F07F83F7F07E367F07E7F07F7F80,Q0FE07F07F7F8FE7E7F0FC7F07F7FHF80,Q0FC05F07D7DFDC5C5FDF87D05F1FDFC0,P01FE0FF07FJF87EFIFCFF07E3FHFE0,P01FE07F07E7FFC054FF7FCFE07E07FFE0,P01FC0FE0FEFHFE0H0FE1FEFE0FE001FE0,P03DC0FD1FCDEFE001FC1FCDE0FC001DE0,P03FC0FKFEFF001FNFDFIFE0,P03F80FIFDFC7F001FIFDFIFDFIFC0,P03F80FIF9FC7F803FIFCFIFBFIFC0,P05F005FDF1F03D801DFDF07DFD1DFDF,hL020,,Y0FC0,Q0KF801FC0I03FJFJ07FIFC00FIFE,P07FJFH03FF0I03FJFC003FJF80FKFC0,O01FKFH07FF80H03FJFE00FKF03FLF0,O03FJFE00FHF80H07FKF03FKF07FLF8,O07DFDFDE01DFDC0H07DFDFDF07DFDFDF1FDFDFDFDC,O0LFE01FHFC0H07FKF8FLF1FMFE,N01FKFC03FHFC0H07FKF9FKFE3FMFE,N03FFE0J07FHFE0H0HFE03FF9FHF802E7FFE00FHFE,N03FFC0J07FDFE0H0HFC01FDBFFC0I07FFC003FFE,N07FF80J0JFE001FFC03FFBFF80I0IF8003FFE,N07FF0J01FJFH01FFC03FF7FF0J0IFI01FFE,N0HFE0J01FFBFF001FFC07FF7FF0J0HFE0H01FFE,N05FC0J01FD3DF001FDFDFDE5FD0I01DFC0H01FDE,N0HFE0J07FF3FFA03FKFEFFE03FF9FFE0H03FFE,N0HFC0J07FE1FF803FKF8FFE03FF9FFC0H03FFC,N0HFC0J0HFC1FF803FJFE0FFE03FFBFFC0H07FFC,M01FFC0I01FFDDFF807FJFH0HFC07FF1FFC0H07FFC,M01FFC0I01FKFC07FF7FF00FFC07FF3FFC0H0IFC,M01FFC0I03FKFC07FF7FF00FFC07FF3FFC001FHF8,M01FFE0I03FKFC07FE3FF80FFE0FFE3FFC003FHF8,M01DFDFDF87DFDFDFC0DFC3DF80DFD0DFC3DFC007DFD0,M01FKFCFLFE0FFE3FFC0FKFE3FHF81FIF0,N0LF8FLFE0FFE1FFC0FKFC1FMFE0,N0LF9FMF1FFC1FFE0FKFC1FMFC0,N0IFDFF9FF8007FF1FFC0FFE07FFDFFC1FMF80,N07FJF3FF0H07FF1FFC0FHF07FJF80FMF,N03FJF7FF0H07FF1FF807FF03FJF807FKFE,O0MFE0H03FFBFF007FF80FJF800FKF8,,:::::::::::::::::::::::::::::^XA";
        $etiqueta .= "^MMT";
        $etiqueta .= "^PW799";
        $etiqueta .= "^LL0799";
        $etiqueta .= "^LS0";
        $etiqueta .= "^FT576,160^XG000.GRF,1,1^FS";
        $etiqueta .= "^FT576,192^XG001.GRF,1,1^FS";
        $etiqueta .= "^FT640,800^XG002.GRF,1,1^FS";
        $etiqueta .= "^FT32,608^XG003.GRF,1,1^FS";
        $etiqueta .= "^FT416,640^XG004.GRF,1,1^FS";
        $etiqueta .= "^FT32,704^XG005.GRF,1,1^FS";
        $etiqueta .= "^FT416,704^XG006.GRF,1,1^FS";
        $etiqueta .= "^FT576,512^XG007.GRF,1,1^FS";
        $etiqueta .= "^FT288,128^XG008.GRF,1,1^FS";
        $etiqueta .= "^FT32,128^XG009.GRF,1,1^FS";
        $etiqueta .= "^FT32,800^XG010.GRF,1,1^FS";
        $etiqueta .= "^FT32,800^XG011.GRF,1,1^FS";
        $etiqueta .= "^FT32,192^XG012.GRF,1,1^FS";
        
        $etiqueta .= "^FO45,22^GFA,546,546,13,O078I01E,I07IF83FF801FFC0FE,I07IF8IFC03IF0FE,I07IF9IFE07IF8FE,I07IF9JF0JFCFE,I07IFBJF8JFCFE,I03IFBJF9JFEFE,J03F87FC7F9FE1FEFE,J07F87F83FDFC1FEFE,J07F87F03FDFC0IFE,J07F87F01FDFC0IFE,J07F87F83FDFC0FEFE,J07F87F83F0FC1FEFE,J07F87FCFE07F3FEFE,J07F83IF8F1IFEIFC,J07F83IF1F87FFCIFC,J07F81FFC6763FF8IFC,J07F80FFCC731FF0IFC,J07F807FCC739FE0IFC,J03F801FCC419F80IFC,Q0C038,:Q0E078,FF01FC01FF0F0F8FEFF00FF8,FF03FC03FF0F0F8FEFF01FF8,FF87FC03FF8F070FEFF01FFC,FFC7FC03FF830C0FEFF01FFC,FFEFFC07FF8100CFEFF03FFC,KFC07FFC001CFEFF03FFE,KFC0IFC007EFEFF07FFE,KFC0FEFC01IFEFF07IF,KFC0FEFE0JFEFF07F7F,KFC1FEFE0JFEFF0FE7F,KFC1FCFF0JFEFF0FE7F8,FEFEFC3FC7F0JFEFF1FE7F8,FE7CFC3JF8FEFFEFF1JF8,FE79FC3JF8FE7FEFF1JFC,FE39FC7JF8FF3FEFF3JFC,FE01FC7JFCFF3FEFF3JFE,FE01FCKFCFF1FEFF3JFE,FE01FCFF01FE7F0FEFF7F80FF,FE01FCFF01FE7F0FEFF7F00FF,^FS";
        $etiqueta .= "^FT672,57^A0N,35,45^FH\\^FD";
 
        $etiqueta .= "";
        $etiqueta .= "^FS";
        $etiqueta .= "^FT304,53^A0N,35,45^FH\\^FD";
        $etiqueta .= $response['body']['regionDestino'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT457,664^A0N,18,21^FH\\^FD$";
        $etiqueta .= $response['body']['totalOF'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT478,106^A0N,18,21^FH\\^FD$";
        $etiqueta .= $response['body']['totalOF'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT629,129^A0N,20,19^FH\\^FD";
        $etiqueta .= $response['body']['numeroDocumentoReferencia'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT588,127^A0N,14,14^FH\\^FDRef.^FS";
        $etiqueta .= "^FT587,106^A0N,14,14^FH\\^FDCantidad^FS";
        $etiqueta .= "^FT646,106^A0N,14,16^FH\\^FD";
        $etiqueta .= $response['body']['cantidadDocumentosReferencia'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT447,642^A0N,14,16^FH\\^FDTotal Monto O.F.^FS";
        $etiqueta .= "^FT610,86^A0N,14,16^FH\\^FDDocumento Respaldo^FS";
        $etiqueta .= "^FT464,87^A0N,14,16^FH\\^FDTotal Monto O.F.^FS";
        $etiqueta .= "^FT38,97^A0N,20,19^FH\\^FDFecha Emisi\\A2n^FS";
        $etiqueta .= "^FT594,608^A0N,17,16^FH\\^FDFECHA NORMAL ENTREGA^FS";
 
        $etiqueta .= "^FT435,614^A0N,23,24^FH\\^FD";
        $etiqueta .= $response['body']['nombreTipoPago'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT311,100^A0N,23,24^FH\\^FD";
        $etiqueta .= $response['body']['nombreTipoPago'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT304,68^A0N,17,16^FH\\^FD";
        $etiqueta .= $response['body']['codigoAgenciaOrigen'] . ' ' . $response['body']['nombreAgenciaOrigen'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT599,522^A0N,31,36^FH\\^FD";
        $etiqueta .= " ";
        $etiqueta .= $response['body']['Encargos']['tipoCarga'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT599,483^A0N,37,52^FH\\^FD";
        $etiqueta .= $response['body']['Encargos']['numeroBultos'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT223,212^A0N,28,26^FH\\^FD";
        $etiqueta .= $response['body']['Encargos']['codigoBarraEncargo'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT174,98^A0N,20,19^FH\\^FD";
        $etiqueta .= $response['body']['fechaEmision'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT656,578^A0N,17,16^FH\\^FDKg Volumen O.F.^FS";
        $etiqueta .= "^FT596,552^A0N,17,16^FH\\^FD";
 
        $etiqueta .= "";
        $etiqueta .= "^FS";
        $etiqueta .= "^FT596,578^A0N,17,16^FH\\^FD";
 
        $etiqueta .= "";
        $etiqueta .= "^FS";
        $etiqueta .= "^FT658,552^A0N,17,16^FH\\^FDKg Peso O.F.^FS";
        $etiqueta .= "^BY3,3,177^FT127,393^BCN,,Y,N";
        $etiqueta .= "^FD>;";
        $etiqueta .= $response['body']['Encargos']['codigoBarraEncargo'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT250,179^A0N,14,14^FH\\^FDE-MAIL /^FS";
        $etiqueta .= "^FT596,661^A0N,29,28^FH\\^FD";
        $etiqueta .= $response['body']['fechaEstimadaEntrega'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT38,179^A0N,14,14^FH\\^FDTEL\\90FONO^FS";
        $etiqueta .= "^FT677,705^A0N,28,28^FH\\^FDRAMPA^FS";
        $etiqueta .= "^FT168,477^A0N,20,19^FH\\^FD";
        if (strlen($direccionDestinatario) > 40) {
            $etiqueta .=  substr($direccionDestinatario, 41, 80);
        } else {
            $etiqueta .= " ";
        }
        $etiqueta .= "^FS";
        $etiqueta .= "^FT36,451^A0N,20,19^FH\\^FDDIRECCI\\E3N      :^FS";
        $etiqueta .= "^FT169,451^A0N,20,19^FH\\^FD";
        if (strlen($direccionDestinatario) > 40) {
            $etiqueta .=  substr($direccionDestinatario, 41, 80);
        } else {
            $etiqueta .= $direccionDestinatario;
        }
        $etiqueta .= "^FS";
        $etiqueta .= "^FT35,527^A0N,20,19^FH\\^FDR.U.T.^FS";
        $etiqueta .= "^FT301,527^A0N,20,19^FH\\^FD";
        $etiqueta .= $response['body']['telefonoDestinatario'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT90,527^A0N,20,19^FH\\^FD";
        $etiqueta .= $response['body']['rutDestinatario'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT267,527^A0N,20,19^FH\\^FDTel.^FS";
        $etiqueta .= "^FT35,580^A0N,20,19^FH\\^FD";
        $etiqueta .= " "; //Observacion linea 2
        $etiqueta .= "^FS";
        $etiqueta .= "^FT157,554^A0N,20,19^FH\\^FD";
        $etiqueta .= 'OT generada para la venta Ref: ' . $venta['Venta']['referencia']; //etiquetaEncargoVO.getObservacion());
        $etiqueta .= "^FS";
        $etiqueta .= "^FT35,554^A0N,20,19^FH\\^FDOBSERVACI\\E3N:^FS";
        $etiqueta .= "^FT36,755^A0N,14,14^FH\\^FDAGENCIA^FS";
        $etiqueta .= "^FT36,772^A0N,14,14^FH\\^FDDESTINO^FS";
        $etiqueta .= "^FT36,704^A0N,14,14^FH\\^FDREGI\\E3N Y^FS";
        $etiqueta .= "^FT36,721^A0N,14,14^FH\\^FDCOMUNA^FS";
        $etiqueta .= "^FT169,503^A0N,20,19^FH\\^FD";
        $etiqueta .= $response['body']['nombreDestinatario'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT35,503^A0N,20,19^FH\\^FDDESTINATARIO :^FS";
        $etiqueta .= "^FT599,441^A0N,14,14^FH\\^FDBULTO N\\F8^FS";
        $etiqueta .= "^FT38,155^A0N,14,14^FH\\^FDDIRECCI\\E3N^FS";
        $etiqueta .= "^FT313,179^A0N,14,14^FH\\^FD";
        $etiqueta .= $response['body']['emailRemitente'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT119,179^A0N,14,14^FH\\^FD";
        $etiqueta .= $response['body']['telefonoRemitente'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT637,177^A0N,14,16^FH\\^FD";
        $etiqueta .= $response['body']['rutEmpresaRemitente'] . '-' . $response['body']['dvRutEmpresaRemitente'];
        $etiqueta .= "^FS";
        $etiqueta .= "^FT661,153^A0N,14,16^FH\\^FD";
        $etiqueta .= $response['body']['numeroCtaCte']; //etiquetaEncargoVO.getCtaCteRemitente());
        $etiqueta .= "^FS";
        $etiqueta .= "^FT589,177^A0N,14,16^FH\\^FDR.U.T.^FS";
        $etiqueta .= "^FT589,153^A0N,14,16^FH\\^FDCTA. CTE.^FS";
        $etiqueta .= "^FT119,155^A0N,14,14^FH\\^FD";
        $etiqueta .= $direccionRemitente;
        $etiqueta .= "^FS";
        $etiqueta .= "^FT745,335^A0B,34,33^FH\\^FD^FS";
        $etiqueta .= "^FT75,369^A0B,34,33^FH\\^FD";
        $etiqueta .= $tipo_servicio;
        $etiqueta .= "^FS";
        $etiqueta .= "^FT56,645^A0B,20,19^FH\\^FDO.F.^FS";
        $etiqueta .= "^FT119,131^A0N,14,16^FH\\^FD";
        $etiqueta .= $remitenteNombre;
        $etiqueta .= "^FS";
        $etiqueta .= "^FT700,768^A0N,46,45^FH\\^FD";
        $etiqueta .= $response['body']['Encargos']['Rampa']; //etiquetaEncargoVO.getCeroGrandeAbajo());
        $etiqueta .= "^FS";
        $etiqueta .= "^FT100,721^A0N,36,35^FH\\^FD";
        $etiqueta .= $response['body']['regionDestino'] . ' ' . $response['body']['comunaDestino'] . ' - ' . $tipo_entrega; //etiquetaEncargoVO.getRegionAndComunaDestino());
        $etiqueta .= "^FS";
        $etiqueta .= "^FT100,774^A0N,36,35^FH\\^FD";
        $etiqueta .= $response['body']['codigoAgenciaDestino'] . ' ' . $response['body']['nombreAgenciaDestino']; // etiquetaEncargoVO.getCodigoAndNombreAgenciaDestino());
        $etiqueta .= "^FS";
        $etiqueta .= "^FT110,178^A0N,14,14^FH\\^FD:^FS";
        $etiqueta .= "^FT110,155^A0N,14,14^FH\\^FD:^FS";
        $etiqueta .= "^FT110,131^A0N,14,14^FH\\^FD:^FS";
        $etiqueta .= "^FT37,131^A0N,14,14^FH\\^FDREMITENTE^FS";
        $etiqueta .= "^FT66,626^A0N,45,60^FH\\^FD";
        $etiqueta .= $response['body']['nroOrdenFlete'];
        $etiqueta .= "^FS";
        $etiqueta .= "^BY2,3,32^FT64,666^BCN,,N,N";
        $etiqueta .= "^FD";
        $etiqueta .= str_replace('.', "", $response['body']['nroOrdenFlete']);
        $etiqueta .= "^FS";
        $etiqueta .= "^PQ1,0,1,Y^XZ";
        $etiqueta .= "^XA^ID000.GRF^FS^XZ";
        $etiqueta .= "^XA^ID001.GRF^FS^XZ";
        $etiqueta .= "^XA^ID002.GRF^FS^XZ";
        $etiqueta .= "^XA^ID003.GRF^FS^XZ";
        $etiqueta .= "^XA^ID004.GRF^FS^XZ";
        $etiqueta .= "^XA^ID005.GRF^FS^XZ";
        $etiqueta .= "^XA^ID006.GRF^FS^XZ";
        $etiqueta .= "^XA^ID007.GRF^FS^XZ";
        $etiqueta .= "^XA^ID008.GRF^FS^XZ";
        $etiqueta .= "^XA^ID009.GRF^FS^XZ";
        $etiqueta .= "^XA^ID010.GRF^FS^XZ";
        $etiqueta .= "^XA^ID011.GRF^FS^XZ";
        $etiqueta .= "^XA^ID012.GRF^FS^XZ";
        $etiqueta .= "^XA^ID013.GRF^FS^XZ";
 		
        return $etiqueta;
    }
}