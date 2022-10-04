<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'PDFMerger', array('file' => 'PDFMerger/PDFMerger.php'));

class TransporteInternoComponent extends Component
{
    public $components = array('LAFFPack', 'Etiquetas', 'WarehouseNodriza');

    public $leyendas = [
        "EX" => "Express.",
        "PR" => "Premium.",
        "PY" => "Prioritario.",
        "MD" => "Same Day."
    ];


    /**
     * registrar_estados
     *
     * @param  mixed $TransportesVenta
     * @param  mixed $total_en_espera
     * @param  mixed $sleep
     * @return void
     */
    public function registrar_estados($TransportesVenta, $total_en_espera)
    {
        $log = [];

        $log[] = array(
            'Log' => array(
                'administrador' => 'Información de la Venta - vid ' . $TransportesVenta['venta_id'],
                'modulo'        => 'TransporteInternoComponent',
                'modulo_accion' => json_encode($TransportesVenta)
            )
        );

        $historicos = [];

        # Registramos el estado de los bultos
        # Obtenemos los estados del bulto

        # Obtenemos la venta y el nombre de su estado para usarlo
        $venta = ClassRegistry::init('Venta')->find('first', array(
            'conditions' => array(
                'Venta.id' => $TransportesVenta['venta_id']
            ),
            'contain' => array(
                'VentaEstado' => array(
                    'fields' => array(
                        'VentaEstado.nombre'
                    )
                )
            ),
            'fields' => array(
                'Venta.id',
                'Venta.venta_estado_id'
            )
        ));
        
        # Buscamos e estado del embalaje asociado y lo regitramos en el historico
        $embalaje =  $this->WarehouseNodriza->ObtenerEmbalaje($TransportesVenta['cod_seguimiento']);
        
        $log[] = array(
            'Log' => array(
                'administrador'     => "Respuesta codigo de seguimiento {$TransportesVenta['cod_seguimiento']} | vid - {$TransportesVenta['venta_id']}",
                'modulo'            => 'TransporteInternoComponent',
                'modulo_accion'     => json_encode($embalaje)
            )
        );
       
        if ($embalaje['code'] != 200) {

            $log[] = array(
                'Log' => array(
                    'administrador' => "Venta {$TransportesVenta['venta_id']} tiene problemas con api de WarehouseNodriza",
                    'modulo'        => 'TransporteInternoComponent',
                    'modulo_accion' => 'Problemas con seguimiento: ' . json_encode($embalaje)
                )
            );
            return false;
        }

        $embalaje = $embalaje['response']['body'];
        
        $estadosHistoricosParcial = ClassRegistry::init('EnvioHistorico')->find('count', array(
            'conditions' => array(
                'EnvioHistorico.transporte_venta_id'    => $TransportesVenta['id'],
                'EnvioHistorico.nombre LIKE'            => '%parcial%'
            )
        ));

        $es_envio_parcial = false;

        # si la venta tiene productos en espera, quiere decir que es un envio parcial
        # si tiene un registro de envio parcial, termina como envio parcial
        if ($estadosHistoricosParcial > 0 || $total_en_espera > 0) {
            $es_envio_parcial = true;
        }

        $return = false;

        try {

            if ($es_envio_parcial) {
                $estado_nombre = sprintf('%s - %s', $venta['VentaEstado']['nombre'], $embalaje['estado']) . ' parcial';
            } else {
                $estado_nombre = sprintf('%s - %s', $venta['VentaEstado']['nombre'], $embalaje['estado']);
            }

            # Verificamos que el estado historico no exista en los registros
            if (ClassRegistry::init('EnvioHistorico')->existe($estado_nombre, $TransportesVenta['id'])) {
                throw new  Exception('Estado historico ya registrado');
            }

            # Verificamos que el estado no exista en los registros
            $estado_existe = ClassRegistry::init('EstadoEnvio')->obtener_por_nombre($estado_nombre, 'TransporteInterno');

            if (!$estado_existe) {
                $estado_existe = ClassRegistry::init('EstadoEnvio')->crear($estado_nombre, null, 'TransporteInterno', "");
            }

            # Sólo se crean los estados nuevos
            $historicos[] = array(
                'EnvioHistorico' => array(
                    'transporte_venta_id'   => $TransportesVenta['id'],
                    'estado_envio_id'       => $estado_existe['EstadoEnvio']['id'],
                    'nombre'                => $estado_nombre,
                    'leyenda'               => $estado_existe['EstadoEnvio']['leyenda'],
                    'canal'                 => 'TransporteInterno',
                    'created'               => date("Y-m-d H:m:s")
                )
            );
            
            if ($historicos) {
                $log[] = array(
                    'Log' => array(
                        'administrador'     => count($historicos) . " nuevos estados del vid - {$TransportesVenta['venta_id']}",
                        'modulo'            => 'TransporteInternoComponent',
                        'modulo_accion'     => json_encode($historicos)
                    )
                );

                # Regitramos los nuevos estados
                ClassRegistry::init('EnvioHistorico')->create();
                $return = ClassRegistry::init('EnvioHistorico')->saveMany($historicos);

            }

            ClassRegistry::init('Log')->create();
            ClassRegistry::init('Log')->saveMany($log);

        } catch (\Throwable $th) {
            $return = false;
        }

        return $return;
    }

    
    /**
     * generar_ot
     *
     * @param  mixed $venta
     * @param  mixed $embalaje
     * @param  mixed $CuentaCorrienteTransporte
     * @return void
     */
    public function generar_ot($venta, $embalaje, $CuentaCorrienteTransporte)
    {
        
        $volumenMaximo  = $venta['MetodoEnvio']['volumen_maximo'];
        $exito          = false;
        $log            = [];
        $transportes    = [];
        $paquetes       = $this->LAFFPack->obtener_bultos_venta_dimension_decimal_por_embalaje($embalaje, $volumenMaximo);
        # si no hay paquetes se retorna false
        if (empty($paquetes)) {

            $log[] = array(
                'Log' => array(
                    'administrador' => "TransporteInterno vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}",
                    'modulo'        => 'TransporteInternoComponent',
                    'modulo_accion' => 'No fue posible generar la OT ya que no hay paquetes disponibles'
                )
            );

            ClassRegistry::init('Log')->create();
            ClassRegistry::init('Log')->saveMany($log);
            return $exito;
        }


        # Si los paquetes no tienen dimensiones se setean con el valor default
        foreach ($paquetes as $ip => $paquete) {

            $paquetes[$ip]['paquete']['length'] =  $paquetes[$ip]['paquete']['length'] == 0 ? $venta['MetodoEnvio']['largo_default'] : $paquetes[$ip]['paquete']['length'];

            $paquetes[$ip]['paquete']['width']  = $paquetes[$ip]['paquete']['width'] == 0 ? $venta['MetodoEnvio']['ancho_default'] : $paquetes[$ip]['paquete']['width'];

            $paquetes[$ip]['paquete']['height'] = $paquetes[$ip]['paquete']['height'] == 0 ? $venta['MetodoEnvio']['alto_default'] : $paquetes[$ip]['paquete']['height'];

            $paquetes[$ip]['paquete']['weight'] = $paquetes[$ip]['paquete']['weight'] == 0 ? $venta['MetodoEnvio']['peso_default'] : $paquetes[$ip]['paquete']['weight'];
        }

        $peso_total            = array_sum(Hash::extract($paquetes, '{n}.paquete.weight'));
        $peso_maximo_permitido = $venta['MetodoEnvio']['peso_maximo'];

        if ($peso_total > $peso_maximo_permitido) {

            $log[] = array(
                'Log' => array(
                    'administrador' => "TransporteInterno vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}",
                    'modulo'        => 'TransporteInternoComponent',
                    'modulo_accion' => 'No fue posible generar la OT por restricción de peso: Peso bulto ' . $peso_total . ' kg - Peso máximo permitido ' . $peso_maximo_permitido
                )
            );

            ClassRegistry::init('Log')->create();
            ClassRegistry::init('Log')->saveMany($log);
            return $exito;
        }

        $ruta_pdfs = null;
        $numero_paquete = 0;

        foreach ($paquetes as $paquete) {

            $numero_paquete++;
            # dimensiones de todos los paquetes unificado
            $largoTotal  = $paquete['paquete']['length'] * 1;
            $anchoTotal  = $paquete['paquete']['width']  * 1;
            $altoTotal   = $paquete['paquete']['height'] * 1;
            $pesoTotal   = $paquete['paquete']['weight'] * 1;

            $canal_venta = '';

			if ($venta['Venta']['venta_manual']) {
				$canal_venta = 'POS de venta';
			} else if ($venta['Venta']['marketplace_id']) {
				$canal_venta = $venta['Marketplace']['nombre'];
			} else {
				$canal_venta = $venta['Tienda']['nombre'];
			}

			$etiquetaArr = array(
				'venta' => array(
					'id' 			=> $venta['Venta']['id'],
                    'embalaje_id'   => $embalaje['id'],
					'metodo_envio' 	=> Inflector::slug($CuentaCorrienteTransporte['Transporte interno'], ' '),
					'canal' 		=> $canal_venta,
					'medio_de_pago' => $venta['MedioPago']['nombre'],
					'fecha_venta' 	=> $venta['Venta']['fecha_venta']
				),
				'transportista' 	=> array(
					'nombre' 		=> $venta['MetodoEnvio']['nombre'],
					'tipo_servicio' => 'Despacho',
					'codigo_barra' 	=> $embalaje['id']
				),
				'remitente' 		=> array(
					'nombre' 		=> $venta['Tienda']['nombre'],
					'rut' 			=> $venta['Tienda']['rut'],
					'fono' 			=> $venta['Tienda']['fono'],
					'url' 			=> $venta['Tienda']['url'],
					'email' 		=> 'ventas@toolmania.cl',
					'direccion' 	=> $venta['Tienda']['direccion']
				),
				'destinatario' 		=> array(
					'nombre' 		=> (empty($venta['Venta']['nombre_receptor'])) ? $venta['VentaCliente']['nombre'] . ' ' . $venta['VentaCliente']['apellido'] : $venta['Venta']['nombre_receptor'],
					'rut' 			=> $venta['VentaCliente']['rut'],
					'fono' 			=> $venta['Venta']['fono_receptor'],
					'email' 		=> $venta['VentaCliente']['email'],
					'direccion' 	=> $venta['Venta']['direccion_entrega'] . ' ' . $venta['Venta']['numero_entrega']  . ', ' . $venta['Venta']['comuna_entrega'],
					'comuna' 		=> $venta['Venta']['comuna_entrega']
				),
				'bulto' 			=> array(
					'referencia' 	=> $venta['Venta']['referencia'],
					'peso' 			=> $paquete['paquete']['weight'],
					'ancho' 		=> (int) $paquete['paquete']['width'],
					'alto' 			=> (int) $paquete['paquete']['height'],
					'largo' 		=> (int) $paquete['paquete']['length']
				),
				'pdf' 				=> array(
					'dir' 			=> 'TransporteInterno'
				)
			);
            
            # Guardamos el transportista y el/los numeros de seguimiento
            $carrier_name = 'TRANSPORTE-INTERNO';
            $carrier_opt = array(
                'Transporte' => array(
                    'codigo' => 'TRANSPORTE-INTERNO',
                    'url_seguimiento' => 'https://toolmania.cl/module/nodriza/centroayuda#main' // Url de seguimiento TransporteInterno
                )
            );

            $etiqueta = $this->Etiquetas->generarEtiquetaTransporte($etiquetaArr);
            
            if (!empty($etiqueta['path']))
				$ruta_pdfs[] = $etiqueta['path'];

			if (!empty($etiqueta['url'])) {
				$carrier_opt = array_replace_recursive($carrier_opt, array(
					'Transporte' => array(
						'etiqueta' => $etiqueta['url']
					)
				));
			} else {

				$log[] = array(
					'Log' => array(
						'administrador' => "TransporteInterno vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}",
						'modulo' 		=> 'TransporteInternoComponent',
						'modulo_accion' => 'Problemas con la URL de la etiqueta: ' . json_encode($etiquetaArr)
					)
				);
			}

            $union = null;

			if (!empty($ruta_pdfs)) {

				$union = $this->Etiquetas->unir_documentos($ruta_pdfs, $venta['Venta']['id']);

				if (!empty($union['result'])) {
					$union = $union['result'][0]['document'];
				}
			}
            
            $transportes[] =
                [
                    'TransportesVenta' =>
                    [
                        'transporte_id'             => ClassRegistry::init('Transporte')->obtener_transporte_por_nombre($carrier_name, true, $carrier_opt),
                        'venta_id'                  => $venta['Venta']['id'],
                        'cod_seguimiento'           => $venta['Venta']['referencia'],
                        'etiqueta'                  => $union,
                        'paquete_generado'          => count($paquetes),
                        'etiqueta_envio_externa'    => $union,
                        'embalaje_id'               => $embalaje["id"],
                        'embalaje_largo'            => $largoTotal,
                        'embalaje_ancho'            => $anchoTotal,
                        'embalaje_alto'             => $altoTotal,
                        'embalaje_peso'             => $pesoTotal
                    ]
                ];

            if (empty($transportes)) {
                continue;
            }
        }

        if ($transportes) {
            # Se guarda la información del tracking en la venta
            if (ClassRegistry::init('TransportesVenta')->saveAll($transportes)) {
                $log[] = array(
                    'Log' => array(
                        'administrador' => 'TransporteInterno, se registro ot vid:' . $venta['Venta']['id'],
                        'modulo'        => 'TransporteInternoComponent',
                        'modulo_accion' => json_encode($transportes)
                    )
                );

                $exito = true;
            } else {
                $log[] = array(
                    'Log' => array(
                        'administrador' => 'TransporteInterno, dificultades para guardar información ot vid:' . $venta['Venta']['id'],
                        'modulo'        => 'TransporteInternoComponent',
                        'modulo_accion' => json_encode($transportes)
                    )
                );
            }
        }
        
        ClassRegistry::init('Log')->create();
        ClassRegistry::init('Log')->saveMany($log);

        return $exito;
    }
}
