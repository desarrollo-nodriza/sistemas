<?php
App::uses('AppController', 'Controller');

class DteComprasController extends AppController
{	
	/**
	 * Lista y filtra losdtes
	 * Endpoint :  /api/dtes.json
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

    	$qry = array();

    	$paginacion = array(
        	'limit' => 0,
        	'offset' => 0,
        	'total' => 0
        );

    	if (isset($this->request->query['id'])) {
    		if (!empty($this->request->query['id'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'DteCompra.id' => $this->request->query['id'])));
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

    	if (isset($this->request->query['folio'])) {
    		if (!empty($this->request->query['folio'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'DteCompra.folio' => $this->request->query['folio'])));
    		}
    	}

    	if (isset($this->request->query['rut_emisor'])) {
    		if (!empty($this->request->query['rut_emisor'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'DteCompra.rut_emisor' => $this->request->query['rut_emisor'])));
    		}
    	}

    	if (isset($this->request->query['tipo_documento'])) {
    		if (!empty($this->request->query['tipo_documento'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'DteCompra.tipo_documento' => $this->request->query['tipo_documento'])));
    		}
    	}

    	if (isset($this->request->query['fecha_emision'])) {
    		if (!empty($this->request->query['fecha_emision'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'DteCompra.fecha_emision' => $this->request->query['fecha_emision'])));
    		}
    	}
   
        $dtes = $this->DteCompra->find('all', $qry);

    	$paginacion['total'] = count($dtes);

        $this->set(array(
            'dtes' => $dtes,
            'paginacion' => $paginacion,
            '_serialize' => array('dtes', 'paginacion')
        ));
    }

	
	/**
	 * api_obtener_compras
	 * 
	 * Obtiene los DTE de compra directamente desde libredte según su estado
	 * 
	 * @param $token Token de acceso
	 * @param $tipo Tipo de registro REGISTRO, PENDIENTE, NO_ICLUIR, RECLAMADO
	 * @param $periodo Periodo del registro
	 * 
	 * https://documenter.getpostman.com/view/5911929/SWLiYkK9#fdfecd78-f701-43dd-8c46-af1ff3dc7ab9
	 *
	 * @return json
	 */
	public function api_obtener_compras()
	{	
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

		# Parámetros requeridos
		if (!isset($this->request->query['tipo'])
			|| !isset($this->request->query['periodo']))
		{
			$response = array(
				'code'    => 300, 
				'message' => 'tipo y periodo son requeridos'
			);

			throw new CakeException($response);
		}


		# Obtenemos la info para conectarnos a libredte
		$tienda = ClassRegistry::init('Tienda')->tienda_principal([
			"sincronizar_compras",
			"sii_rut",
			"sii_clave",
			"sii_public_key",
			"sii_private_key",
			"libredte_token"
		]);

		if (!$tienda['Tienda']['sincronizar_compras'])
		{	
			$response = array(
				'code'    => 506, 
				'message' => 'La tienda no tiene activa la sincronización de compras. Por favor actívela e intente nuevamente'
			);

			throw new CakeException($response);
		}

		$result = $this->obtener_dte_compras_desde_sii($tienda['Tienda']['sii_private_key'], $tienda['Tienda']['sii_public_key'], $tienda['Tienda']['sii_rut'], $tienda['Tienda']['sii_clave'], $tienda['Tienda']['libredte_token'], $this->request->query['tipo'], $this->request->query['periodo']);
		
		$response = array(
			'code'       => 200,
			'message'    => 'Result',
			'body' => $result,
			'_serialize' => array('code', 'message', 'body')
		);

		$this->set($response);

	}

	
	/**
	 * api_sincronizar_compras
	 *
	 * Obtiene los dtes de compra desde el SIIn y los registra en el sistema
	 * 
	 * @return json
	 */
	public function api_sincronizar_compras()
	{
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

		# Parámetros requeridos
		if (!isset($this->request->query['periodo']))
		{
			$response = array(
				'code'    => 300, 
				'message' => 'periodo es requerido'
			);

			throw new CakeException($response);
		}

		# Obtenemos la información para crear conectarnos a libredte
		$tienda = ClassRegistry::init('Tienda')->tienda_principal([
			"sincronizar_compras",
			"sii_rut",
			"sii_clave",
			"sii_public_key",
			"sii_private_key",
			"libredte_token"
		]);

		$log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'DteCompra',
			'modulo_accion' => 'Inicia proceso de sincronización: ' . date('Y-m-d H:i:s')
		));

		if (empty($tienda['Tienda']['sii_rut'])
			|| empty($tienda['Tienda']['sii_clave'])
			|| empty($tienda['Tienda']['libredte_token'])
			|| empty($tienda['Tienda']['sincronizar_compras'])) 
			{
			
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Error: La tienda no está configurada para sincronizar las compras desde el SII.'
			));

			ClassRegistry::init('Log')->saveMany($log);

			$response = array(
				'code'    => 500, 
				'message' => 'La tienda no está configurada para sincronizar las compras desde el SII.'
			);

			throw new CakeException($response);
		}

		# Sincronización desactivada
		if (!$tienda['Tienda']['sincronizar_compras']) {
			$response = array(
				'code'    => 500, 
				'message' => 'La tienda no está configurada para sincronizar las compras desde el SII.'
			);

			throw new CakeException($response);
		}

		$dteCompraSave = array();

		# Periodo a consultar
		$periodo = $this->request->query['periodo'];

		# Obtenemos los dtes con estado de REGISTRO
		$compras_registro   = $this->obtener_dte_compras_desde_sii($tienda['Tienda']['sii_private_key'], $tienda['Tienda']['sii_public_key'], $tienda['Tienda']['sii_rut'], $tienda['Tienda']['sii_clave'], $tienda['Tienda']['libredte_token'], 'REGISTRO', $periodo);
		
		# ITerador
		$ii = 0;

		# Guardamos dtes registros
		foreach ($compras_registro['body']['data'] as $i => $data) 
		{
			$dteCompraSave[$ii] = array(
				'DteCompra' => array(
					'tipo_documento'      => $data['detTipoDoc'],
					'rut_emisor'          => $data['detRutDoc'],
					'dv_emisor'           => $data['detDvDoc'],
					'razon_social_emisor' => $data['detRznSoc'],
					'folio'               => $data['detNroDoc'],
					'fecha_emision'       => date('Y-m-d', strtotime(str_replace('/','-', $data['detFchDoc']))),
					'fecha_recepcion'     => date('Y-m-d H:i:s', strtotime(str_replace('/','-', $data['detFecRecepcion']))),
					'monto_exento'        => $data['detMntExe'],
					'monto_neto'          => $data['detMntNeto'],
					'monto_iva'           => $data['detMntIVA'],
					'monto_total'         => $data['detMntTotal'],
					'estado' 			  => 'REGISTRO'

				)
			);

			$qry_existen = ClassRegistry::init('DteCompra')->find('first', array(
				'conditions' => array(
					'rut_emisor' => $data['detRutdata'],
					'folio' => $data['detNroDoc'],
				),
				'fields' => array(
					'id'
				)
			));

			# si existe, lo actualizamos si corresponde
			if ($qry_existen)
			{
				$dteCompraSave[$ii] = array_replace_recursive($dteCompraSave[$ii], [
					'DteCompra' => [
						'id' => $qry_existen['DteCompra']['id']
					]
				]);
			}

			$ii++;
		}
		
		# Obtenemos los dtes con estado PENDIENTE
		$compras_pendientes = $this->obtener_dte_compras_desde_sii($tienda['Tienda']['sii_private_key'], $tienda['Tienda']['sii_public_key'], $tienda['Tienda']['sii_rut'], $tienda['Tienda']['sii_clave'], $tienda['Tienda']['libredte_token'], 'PENDIENTE', $periodo);

		# Guardamos dtes pendiente
		foreach ($compras_pendientes['body']['data'] as $i => $data) 
		{
			$dteCompraSave[$ii] = array(
				'DteCompra' => array(
					'tipo_documento'      => $data['detTipoDoc'],
					'rut_emisor'          => $data['detRutDoc'],
					'dv_emisor'           => $data['detDvDoc'],
					'razon_social_emisor' => $data['detRznSoc'],
					'folio'               => $data['detNroDoc'],
					'fecha_emision'       => date('Y-m-d', strtotime(str_replace('/','-', $data['detFchDoc']))),
					'fecha_recepcion'     => date('Y-m-d H:i:s', strtotime(str_replace('/','-', $data['detFecRecepcion']))),
					'monto_exento'        => $data['detMntExe'],
					'monto_neto'          => $data['detMntNeto'],
					'monto_iva'           => $data['detMntIVA'],
					'monto_total'         => $data['detMntTotal'],
					'estado' 			  => 'PENDIENTE'

				)
			);

			$qry_existen = ClassRegistry::init('DteCompra')->find('first', array(
				'conditions' => array(
					'rut_emisor' => $data['detRutdata'],
					'folio' => $data['detNroDoc'],
				),
				'fields' => array(
					'id'
				)
			));

			# si existe, lo actualizamos si corresponde
			if ($qry_existen)
			{
				$dteCompraSave[$ii] = array_replace_recursive($dteCompraSave[$ii], [
					'DteCompra' => [
						'id' => $qry_existen['DteCompra']['id']
					]
				]);
			}

			$ii++;
		}
		
		# No se obtuvieron resultados
		if (empty($dteCompraSave)) {
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Error: No se obtuvieron dtes de compra'
			));

			ClassRegistry::init('Log')->saveMany($log);

			$response = array(
				'code'    => 500, 
				'message' => 'No se obtuvieron dtes de compra.'
			);

			throw new CakeException($response);
		}

		# Guardamos los dtes obtenidos
		if (ClassRegistry::init('DteCompra')->saveMany($dteCompraSave)) {
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Éxito: Se registraron ' . count($dteCompraSave) . ' compras. Json: ' . json_encode($dteCompraSave)
			));	
		}else{
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Error: No fue posible guardar la compras. Se encontraron un total de ' . count($dteCompraSave) . ' compras.'
			));
		}
		
		ClassRegistry::init('Log')->saveMany($log);

		$response = array(
			'code'       => 200,
			'message'    => 'Result',
			'body' => $dteCompraSave,
			'_serialize' => array('code', 'message', 'body')
		);

		$this->set($response);

	}


	/**
	 * obtener_dte_compras_desde_sii
	 * 
	 * Obtiene los DTE de compra directamente desde libredte según su estado
	 * 
	 * https://documenter.getpostman.com/view/5911929/SWLiYkK9#fdfecd78-f701-43dd-8c46-af1ff3dc7ab9
	 *
	 * @param  mixed $pkm Llave privada
	 * @param  mixed $pem Lllave pública
	 * @param  mixed $rut Rut SII
	 * @param  mixed $clave Clave SII
	 * @param  mixed $token Toen Libredte
	 * @param  mixed $tipo tipo de DTE (REGISTRO, PENDIENTE, NO_INCLUIR, RECLAMO)
	 * @param  mixed $periodo Periodo del registro 
	 * @return array
	 */
	public function obtener_dte_compras_desde_sii($pkm, $pem, $rut, $clave, $token, $tipo, $periodo)
	{
		$cert_data = [
			"private" => $pkm,
			"public" => $pem
		];

		$pass_data = [
			"rut" => formato_rut($rut),
			"clave" => $clave
		];

		$this->ApiLibreDte = $this->Components->load('ApiLibreDte');
		$this->ApiLibreDte->crearCliente($token, $cert_data, $pass_data, 0);

		$pars = [
			"formato" => "json",
			"certificacion" => 0,
			"tipo" => "csv"
		];

		return $this->ApiLibreDte->obtenerDocumentosCompras(formato_rut($rut), $periodo, 33, $tipo, $pars);
		
	}
}