<?php 
App::uses('AppModel', 'Model');

Class EmbalajeWarehouse extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $useDbConfig = 'warehouse';
	public $useTable = 'embalajes';
	public $displayField	= 'id';


	private $estados = array(
		'inicial' => 'Inicial',
		'en_revision' => 'En revisión manual',
		'listo_para_embalar' => 'Listo para embalar',
		'procesando' => 'En preparación',
		'finalizado' => 'Finalizado',
		'cancelado' => 'Cancelado'
	);


	/**
	 * Asosiaciones
	 * @var array
	 */
	public $belongsTo = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
        ),
        'Bodega' => array(
			'className'				=> 'Bodega',
			'foreignKey'			=> 'bodega_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
        'MetodoEnvio' => array(
			'className'				=> 'MetodoEnvio',
			'foreignKey'			=> 'metodo_envio_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
        'Marketplace' => array(
			'className'				=> 'Marketplace',
			'foreignKey'			=> 'marketplace_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
        'Comuna' => array(
			'className'				=> 'Comuna',
			'foreignKey'			=> 'comuna_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
		'BodegaTrasladar' => array(
			'className'				=> 'Bodega',
			'foreignKey'			=> 'bodega_id_para_trasladar',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
	);

	public $hasMany = array(
		'EmbalajeProductoWarehouse' => array(
			'className'				=> 'EmbalajeProductoWarehouse',
			'foreignKey'			=> 'embalaje_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		)
		,
		'HistorialEmbalaje' => array(
			'className'				=> 'HistorialEmbalaje',
			'foreignKey'			=> 'embalaje_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
		)
	);


	public function obtener_estados()
	{
		return $this->estados;
	}

	
	/**
	 * cancelar_emabalaje
	 *
	 * @param  mixed $id
	 * @param  mixed $responsable
	 * @return void
	 */
	// ! En Desuso
	public function cancelar_embalaje($id, $responsable = '')
	{	
		$logs = array();

		$embalaje = $this->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'contain' => array(
				'EmbalajeProductoWarehouse'
			)
		));
		
		$embalaje['EmbalajeWarehouse']['estado'] = 'cancelado';
		$embalaje['EmbalajeWarehouse']['responsable_id_cancelado'] = $responsable;
		$embalaje['EmbalajeWarehouse']['fecha_cancelado'] = date('Y-m-d H:i:s');
		$embalaje['EmbalajeWarehouse']['ultima_modifacion'] = date('Y-m-d H:i:s');

		# Anulamos las unidades que no corresponden
		foreach ($embalaje['EmbalajeProductoWarehouse'] as $im => $p) 
		{
			$embalaje['EmbalajeProductoWarehouse'][$im]['cantidad_a_embalar'] = 0;
			$embalaje['EmbalajeProductoWarehouse'][$im]['ultima_modifacion'] = date('Y-m-d H:i:s');
		}

		$logs[] = array(
			'Log' => array(
				'administrador' => 'Inicia cancelar embalaje ' . $id,
				'modulo' => 'EmbalajeWarehouse',
				'modulo_accion' => json_encode($embalaje)
			)
		);
		
		$return = false;

		if ($this->saveAll($embalaje))
		{
			$this->NotificacionFirebase('¡Ups un embalaje a sido cancelado!');
			$respuesta = ClassRegistry::init('Zonificacion')->crearEntradaVentaCanceladaZonificacion($id);
			$return = true;
			$logs[] = array(
				'Log' => array(
					'administrador' => 'Embalaje cancelado ' . $id,
					'modulo' => 'EmbalajeWarehouse',
					'modulo_accion' => json_encode($embalaje)
				)
			);
		}

		ClassRegistry::init('Log')->saveMany($logs);

		return $return;
	}

	
	/**
	 * finalizar_embalaje
	 *
	 * @param  mixed $id
	 * @param  mixed $responsable
	 * @return void
	 */
	public function finalizar_embalaje($id, $responsable = '')
	{
		$logs = array();

		$embalaje = $this->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'contain' => array(
				'EmbalajeProductoWarehouse'
			)
		));
		
		$embalaje['EmbalajeWarehouse']['estado'] = 'finalizado';
		$embalaje['EmbalajeWarehouse']['responsable_id_finalizado'] = $responsable;
		$embalaje['EmbalajeWarehouse']['fecha_finalizado'] = date('Y-m-d H:i:s');
		$embalaje['EmbalajeWarehouse']['ultima_modifacion'] = date('Y-m-d H:i:s');

		# Anulamos las unidades que no corresponden
		foreach ($embalaje['EmbalajeProductoWarehouse'] as $im => $p) 
		{
			$embalaje['EmbalajeProductoWarehouse'][$im]['cantidad_embalada'] = $p['cantidad_a_embalar'];
			$embalaje['EmbalajeProductoWarehouse'][$im]['ultima_modifacion'] = date('Y-m-d H:i:s');
		}

		# si todos los productos estan embalados

		$logs[] = array(
			'Log' => array(
				'administrador' => 'Inicia finalizar embalaje ' . $id,
				'modulo' => 'EmbalajeWarehouse',
				'modulo_accion' => json_encode($embalaje)
			)
		);

		$return = false;
		
		if ($this->saveAll($embalaje))
		{
			$return = true;

			$logs[] = array(
				'Log' => array(
					'administrador' => 'Embalaje finalizado ' . $id,
					'modulo' => 'EmbalajeWarehouse',
					'modulo_accion' => json_encode($embalaje)
				)
			);
		}

		ClassRegistry::init('Log')->saveMany($logs);

		return $return;
	}

	
	/**
	 * procesar_embalajes
	 *
	 * @param  mixed $id
	 * @return void
	 */
	// ! No se usa metodo del modelo, solo usar el que esta en el componente
	// public function procesar_embalajes($id)
	// {

	// 	$logs = array();
	// 	// $Firebase = $this->Components->load('Firebase');
	// 	$venta = ClassRegistry::init('Venta')->find('first', array(
	// 		'conditions' => array(
	// 			'Venta.id' => $id
	// 		),
	// 		'contain' => array(
	// 			'VentaDetalle' => array(
	// 				'EmbalajeProductoWarehouse' => array(
	// 					'EmbalajeWarehouse'
	// 				)
	// 				),
	// 			'MetodoEnvio' => ['fields' => ['MetodoEnvio.retiro_local']],
	// 			'Bodega'  => ['fields' => ['Bodega.nombre']],
	// 		),
	// 		'fields' => array(
	// 			'Venta.id',
	// 			'Venta.metodo_envio_id',
	// 			'Venta.marketplace_id',
	// 			'Venta.comuna_id',
	// 			'Venta.fecha_venta',
	// 			'Venta.venta_estado_id',
	// 			'Venta.administrador_id',
	// 			'Venta.picking_estado',
	// 			'Venta.prioritario',
	// 			'Venta.bodega_id',
	// 			'Venta.nota_interna'
	// 		)
	// 	));

	// 	$logs[] = array(
	// 		'Log' => array(
	// 			'administrador' => 'Inicia embalaje venta ' . $id,
	// 			'modulo' => 'EmbalajeWarehouse',
	// 			'modulo_accion' => json_encode($venta)
	// 		)
	// 	);


	// 	$bodega = ClassRegistry::init('Bodega')->obtener_bodega_principal();
	// 	$dte_valido = ClassRegistry::init('Dte')->obtener_dte_valido_venta($id);

	// 	switch ($venta['Venta']['picking_estado']) {
	// 		case 'no_definido':

	// 			# si hay un embalaje creado se cancela siempre y cuando sean las 
	// 			# unidades del embalaje las que se quitan de la reserva
	// 			foreach ($venta['VentaDetalle'] as $d) {
	// 				if (!empty($d['EmbalajeProductoWarehouse'])) {
	// 					# Cancelamos todos loe embalajes relacionados al detalle
	// 					foreach ($d['EmbalajeProductoWarehouse'] as $emp) {
	// 						if (in_array($emp['EmbalajeWarehouse']['estado'], array('cancelado', 'finalizado', 'entregado'))) {
	// 							continue;
	// 						}

	// 						$this->cancelar_embalaje($emp['embalaje_id']);
	// 					}
	// 				}
	// 			}

	// 			break;

	// 		case 'empaquetar':

	// 			# si el estado de la venta no es pagado no pasa
	// 			if (!ClassRegistry::init('VentaEstado')->es_estado_pagado($venta['Venta']['venta_estado_id'])) {
	// 				break;
	// 			}
	// 			$bodegas_activas = ClassRegistry::init('Bodegas')->find(
	// 				'list',
	// 				['conditions' => ['Bodegas.activo' => true]]
	// 			);

	// 			$reservas_separadas_por_bodega = [];

	// 			// TODO Extraemos solo los productos que fueron reservados en otras bodegas
	// 			foreach ($bodegas_activas as $key => $value) {
	// 				$reservas_separadas_por_bodega[] = Hash::extract($venta['VentaDetalle'], "{n}[reservar_stock_bodega_id={$key}]");
	// 			}

	// 			// TODO Extraemos la que no fueron reservadas en bodegas auxiliares
	// 			$reservas_separadas_por_bodega[] = Hash::extract($venta['VentaDetalle'], "{n}[reservar_stock_bodega_id=/^$/]");

	// 			// TODO Se filtra aquellas bodegas que no tuvieron reserva en stock
	// 			$reservas_separadas_por_bodega = array_filter($reservas_separadas_por_bodega);

	// 			foreach ($reservas_separadas_por_bodega as $productos_por_bodegas) {

	// 				$embalaje = array(
	// 					'EmbalajeWarehouse' => array(
	// 						'venta_id' 					=> $venta['Venta']['id'],
	// 						'estado' 					=> 'listo_para_embalar',
	// 						'bodega_id' 				=> $venta['Venta']['bodega_id'] ?? $bodega['Bodega']['id'],
	// 						'metodo_envio_id' 			=> $venta['Venta']['metodo_envio_id'],
	// 						'marketplace_id'  			=> $venta['Venta']['marketplace_id'],
	// 						'comuna_id' 	  			=> $venta['Venta']['comuna_id'],
	// 						'venta_estado_id' 			=> '',
	// 						'prioritario' 	  			=> ($venta['Venta']['prioritario']) ? 1 : 0,
	// 						'fecha_venta' 	  			=> $venta['Venta']['fecha_venta'],
	// 						'fecha_creacion'  			=> date('Y-m-d H:i:s'),
	// 						'fecha_listo_para_embalar' 	=> date('Y-m-d H:i:s'),
	// 						'ultima_modifacion' 		=> date('Y-m-d H:i:s')
	// 					),
	// 					'EmbalajeProductoWarehouse' => array()
	// 				);
	// 				$bodega_distinta_a_principal = false;
	// 				foreach ($productos_por_bodegas as $d) {

	// 					$cantidad_a_embalar = $d['cantidad_reservada'];

	// 					if (!empty($d['EmbalajeProductoWarehouse'])) {
	// 						foreach ($d['EmbalajeProductoWarehouse'] as $emp) {

	// 							$cantidad_a_embalar = $cantidad_a_embalar - $emp['cantidad_a_embalar'];
	// 						}
	// 					}

	// 					# Agregamos el item al nuevo embalaje
	// 					if ($cantidad_a_embalar > 0) {
	// 						if (!is_null($d['reservar_stock_bodega_id'])) {

	// 							$embalaje['EmbalajeWarehouse']['bodega_id'] = $d['reservar_stock_bodega_id'];
	// 							$bodega_distinta_a_principal 				= true;
	// 						}

	// 						$embalaje['EmbalajeProductoWarehouse'][] = array(
	// 							'producto_id' 		 => $d['venta_detalle_producto_id'],
	// 							'detalle_id' 		 => $d['id'],
	// 							'cantidad_a_embalar' => $cantidad_a_embalar,
	// 							'fecha_creacion' 	 => date('Y-m-d H:i:s'),
	// 							'ultima_modifacion'  => date('Y-m-d H:i:s')
	// 						);
	// 					}
	// 				}

	// 				# si hay productos para embalar y tiene dte válido pasa a embalaje
	// 				if (!empty($embalaje['EmbalajeProductoWarehouse']) && $dte_valido) {

	// 					$logs[] = array(
	// 						'Log' => array(
	// 							'administrador' => 'Crear embalaje venta ' . $id,
	// 							'modulo' => 'EmbalajeWarehouse',
	// 							'modulo_accion' => json_encode($embalaje)
	// 						)
	// 					);
						
	// 					$guardar_embalaje = ClassRegistry::init('EmbalajeWarehouse')->saveAll($embalaje);

	// 					if ($guardar_embalaje) {
	// 						$this->NotificacionFirebase('¡Tenemos un nuevo embalaje que puedes procesar!');
	// 						if($bodega_distinta_a_principal){

	// 							if ($venta['MetodoEnvio']['retiro_local']) {
	// 								try {
	// 									$nota= is_null($venta['Venta']['nota_interna'])? '' : "{$venta['Venta']['nota_interna']} - El embalaje {$bodega_distinta_a_principal['EmbalajeWarehouse']['id']} requiere ser trasladado a la bodega {$venta['Bodega']['nombre']} para ser retirado en tienda por el cliente.";
	// 								} catch (\Throwable $th) {
										
	// 									$nota= is_null($venta['Venta']['nota_interna'])? '' : "{$venta['Venta']['nota_interna']} - El embalaje requiere ser trasladado a la bodega {$venta['Bodega']['nombre']} para ser retirado en tienda por el cliente.";
	// 								}
									

	// 								ClassRegistry::init('Venta')->save([
	// 									'Venta'=>
	// 										[
	// 											'nota_interna' => $nota,
	// 											'id'		   => $venta['Venta']['id']
	// 										]
	// 								]);
	// 							}
	// 						}
	// 					};
	// 				}
	// 			}
			
	// 			break;

	// 		case 'empaquetando':

	// 			break;
	// 		case 'empaquetado':

	// 			break;
	// 	}

	// 	ClassRegistry::init('Log')->saveMany($logs);

	// 	return;
	// }

	private function NotificacionFirebase($mensaje)
    {
		$tokens = ClassRegistry::init('NotificacionFirebase')->find('all',
				[
					'fields'=>
						[
							'token'
                        ]
				]
        );
		$tokens = Hash::extract($tokens, '{n}.NotificacionFirebase.token');
        
        
        $data =
        [
            "notification"      => 
            [
                "title"         =>  "Embalajes",
                "body"          =>  $mensaje,
                "click_action"  =>  "https://warehouse-app.nodriza.cl/catalogue"
            ] ,
            "registration_ids"  =>  $tokens,
        ];

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://fcm.googleapis.com/fcm/send',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => json_encode($data,true),
        CURLOPT_HTTPHEADER => array(
        'Content-Type: application/json',
        'Authorization: Bearer AAAALM6Ru-g:APA91bEw4kiA0eCGEV6iAyqgJV4b_1l0Awg75RkPB61QuoD9c3-Le5TwznNdYNen1g-xPL2LWRPacXbAMNA2sEaOtw-uYi_3mqwnVsykfOKCnpFOgKNWNZp0ALDhntv5zkA81R1VTw59'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);


		
		return json_decode($response,true);
    }
}