<?php 

class SincronizarComprasShell extends AppShell {

	public function main() {

		$conf = ClassRegistry::init('Tienda')->tienda_principal(
			array(
				'Tienda.sii_rut',
				'Tienda.sii_clave',
				'Tienda.libredte_token',
				'Tienda.sincronizar_compras'
			)
		);

		$log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'DteCompra',
			'modulo_accion' => 'Inicia proceso de sincronización: ' . date('Y-m-d H:i:s')
		));

		if (empty($conf['Tienda']['sii_rut'])
			|| empty($conf['Tienda']['sii_clave'])
			|| empty($conf['Tienda']['libredte_token'])
			|| empty($conf['Tienda']['sincronizar_compras'])) {
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Error: La tienda no está configurada para sincronizar las compras desde el SII.'
			));

			ClassRegistry::init('Log')->saveMany($log);

			return;
		}

		# Sincronización desactivada
		if (!$conf['Tienda']['sincronizar_compras']) {
			return;
		}

		$periodo = date('Ym');

		$body = array(
			'auth' => array(
				'pass' => array(
					'rut' => $conf['Tienda']['sii_rut'],
					'clave' => $conf['Tienda']['sii_clave']
				)
			)
		);

		$compras_registro   = $this->obtener_compras($conf['Tienda']['sii_rut'], $periodo, 33, 'REGISTRO', $body, $conf['Tienda']['libredte_token']);		
		$compras_pendientes = $this->obtener_compras($conf['Tienda']['sii_rut'], $periodo, 33, 'PENDIENTE', $body, $conf['Tienda']['libredte_token']);

		$compras = Hash::merge($compras_pendientes, $compras_registro);
		
		# Se obtuvieron resultados
		if (empty($compras['data'])) {
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Error: ' . $compras['respEstado']['codRespuesta'] . ' - ' . $compras['respEstado']['msgeRespuesta']
			));

			ClassRegistry::init('Log')->saveMany($log);

			return;
		}

		$save = array();

		# Guardamos
		foreach ($compras['data'] as $i => $data) {

			# si ya tenemos registado un folio
			if (ClassRegistry::init('DteCompra')->existe_por_folio($data['detNroDoc'], $data['detTipoDoc'], $data['detRutDoc'])) {
				continue;
			}

			$save[] = array(
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
					'monto_total'         => $data['detMntTotal']

				)
			);
		}
		
		if (ClassRegistry::init('DteCompra')->saveMany($save)) {
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Éxito: Se registraron ' . count($save) . ' compras.'
			));	
		}else{
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Error: No fue posible guardar la compras. Se encontraron un total de ' . count($save) . ' compras.'
			));
		}
		
		ClassRegistry::init('Log')->saveMany($log);

		return true;
	}



	private function obtener_compras($rut_receptor, $periodo, $tipo_dte = 33, $estado = 'REGISTRO', $body = array(), $token = '')
	{

		$curl = curl_init();

		curl_setopt_array($curl, array(
			CURLOPT_URL            => "https://api.libredte.cl/api/v1/sii/rcv/compras/detalle/".$rut_receptor."/".$periodo."/".$tipo_dte."/REGISTRO?formato=json&certificacion=0&tipo=rcv",
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_ENCODING       => "",
			CURLOPT_MAXREDIRS      => 10,
			CURLOPT_TIMEOUT        => 0,
			CURLOPT_FOLLOWLOCATION => true,
			CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
			CURLOPT_CUSTOMREQUEST  => "POST",
			CURLOPT_POSTFIELDS     => json_encode($body),
			CURLOPT_HTTPHEADER     => array(
				"Accept: application/json",
				"Content-Type: application/json",
				"Authorization: Bearer " . trim($token)
			)
		));

		$response = curl_exec($curl);

		curl_close($curl);
		
		return json_decode($response, true);

	}

}