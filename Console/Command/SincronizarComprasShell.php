<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('DteComprasController', 'Controller');

class SincronizarComprasShell extends AppShell {

	public function main() {

		# Obtenemos la información para crear conectarnos a libredte
		$conf = ClassRegistry::init('Tienda')->tienda_principal([
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

		if (empty($conf['Tienda']['sii_rut'])
			|| empty($conf['Tienda']['sii_clave'])
			|| empty($conf['Tienda']['libredte_token'])
			|| empty($conf['Tienda']['sincronizar_compras'])) 
			{
			
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

		$dteCompraSave = array();

		# Periodo a consultar
		$periodo = date('Ym');
		
		$controller = new DteComprasController(new CakeRequest(), new CakeResponse());

		# Obtenemos los dtes con estado de REGISTRO
		$compras_registro   = $controller->obtener_dte_compras_desde_sii($conf['Tienda']['sii_private_key'], $conf['Tienda']['sii_public_key'], $conf['Tienda']['sii_rut'], $conf['Tienda']['sii_clave'], $conf['Tienda']['libredte_token'], 'REGISTRO', $periodo);
		
		# Guardamos dtes registros
		foreach ($compras_registro['body']['data'] as $i => $data) 
		{
			# si ya tenemos registado un folio
			if (ClassRegistry::init('DteCompra')->existe_por_folio($data['detNroDoc'], $data['detTipoDoc'], $data['detRutDoc'])) {
				continue;
			}

			$dteCompraSave[] = array(
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
		}
		
		# Obtenemos los dtes con estado PENDIENTE
		$compras_pendientes = $controller->obtener_dte_compras_desde_sii($conf['Tienda']['sii_private_key'], $conf['Tienda']['sii_public_key'], $conf['Tienda']['sii_rut'], $conf['Tienda']['sii_clave'], $conf['Tienda']['libredte_token'], 'PENDIENTE', $periodo);

		# Guardamos dtes pendiente
		foreach ($compras_pendientes['body']['data'] as $i => $data) 
		{
			# si ya tenemos registado un folio
			if (ClassRegistry::init('DteCompra')->existe_por_folio($data['detNroDoc'], $data['detTipoDoc'], $data['detRutDoc'])) {
				continue;
			}

			$dteCompraSave[] = array(
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
		}
		
		# No se obtuvieron resultados
		if (empty($dteCompraSave)) {
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Error: No se obtuvieron dtes de compra'
			));

			ClassRegistry::init('Log')->saveMany($log);

			return;
		}

		# Guardamos los dtes obtenidos
		if (ClassRegistry::init('DteCompra')->saveMany($dteCompraSave)) {
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Éxito: Se registraron ' . count($dteCompraSave) . ' compras.'
			));	
		}else{
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Error: No fue posible guardar la compras. Se encontraron un total de ' . count($dteCompraSave) . ' compras.'
			));
		}
		
		ClassRegistry::init('Log')->saveMany($log);

		return true;
	}

}