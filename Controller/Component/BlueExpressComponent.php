<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'BlueExpress', array('file' => 'BlueExpress/BlueExpress.php'));
App::import('Vendor', 'PDFMerger', array('file' => 'PDFMerger/PDFMerger.php'));

class BlueExpressComponent extends Component
{

    private $blue_express;
    public $components = array('LAFFPack', 'LibreDte', 'Etiquetas');

    public function crearCliente($BX_TOKEN, $BX_USERCODE, $BX_CLIENT_ACCOUNT)
    {
        $this->blue_express = new BlueExpress($BX_TOKEN, $BX_USERCODE, $BX_CLIENT_ACCOUNT);
    }

    public function regenerar_etiqueta($trackingNumber, $venta_id)
    {
        $credenciales = ClassRegistry::init('TransportesVenta')->find('first', [
            'conditions' =>
            [
                ['TransportesVenta.cod_seguimiento' => $trackingNumber]
            ],
            'contain' => [
                'Venta' => [
                    'metodo_envio_id',
                    'MetodoEnvio' => array(
                        'fields' => array(
                            'MetodoEnvio.dependencia',
                            'MetodoEnvio.token_blue_express',
                            'MetodoEnvio.cod_usuario_blue_express',
                            'MetodoEnvio.cta_corriente_blue_express',
                        )
                    )
                ]
            ],
            'fields' => ['TransportesVenta.id']
        ]);

        $this->crearCliente($credenciales['Venta']['MetodoEnvio']['token_blue_express'], $credenciales['Venta']['MetodoEnvio']['cod_usuario_blue_express'], $credenciales['Venta']['MetodoEnvio']['cta_corriente_blue_express']);
        $response           = $this->blue_express->BXLabel($trackingNumber);
        $response['url']    = null;
        
        if ($response['code'] == 200 || $response['response']['status']) {

            $response['response'] = base64_decode($response['response']['data'][0]['base64']);

            $nombreEtiqueta = $trackingNumber . date("Y-m-d H:i:s") . '.pdf';
            $modulo_ruta    = 'webroot' . DS . 'img' . DS . 'ModuloBlueExpress' . DS . $venta_id . DS;
            $rutaPublica    = APP .  $modulo_ruta;

            if (!is_dir($rutaPublica)) {
                @mkdir($rutaPublica, 0775, true);
            }

            $file = fopen($rutaPublica . $nombreEtiqueta, "w");
            fwrite($file, $response['response']);
            fclose($file);
            $ruta_pdfs          = 'https://' . $_SERVER['HTTP_HOST'] . DS . $modulo_ruta . $nombreEtiqueta;
            $response['url']    = $ruta_pdfs;
        }

        return $response;
    }

    public function registrar_estados($id)
    {

        $log = [];

        # Obtenemos los transportes de la venta
        $v = ClassRegistry::init('Venta')->find('first', array(
            'conditions' => array(
                'Venta.id' => $id
            ),
            'contain' => array(
                'Transporte' => array(
                    'fields' => array(
                        'Transporte.id'
                    )
                ),
                'VentaDetalle' => array(
                    'fields' => array(
                        'VentaDetalle.id',
                        'VentaDetalle.cantidad_en_espera'
                    ),

                ),
                'MetodoEnvio' => array(
                    'fields' => array(
                        'MetodoEnvio.dependencia',
                        'MetodoEnvio.token_blue_express',
                        'MetodoEnvio.cod_usuario_blue_express',
                        'MetodoEnvio.cta_corriente_blue_express'
                    )
                )

            ),
            'fields' => array(
                'Venta.id'
            )
        ));

        $log[] = array(
            'Log' => array(
                'administrador' => 'Información de la Venta - vid ' . $id,
                'modulo'         => 'BlueExpressComponent',
                'modulo_accion' => json_encode($v)
            )
        );

        if ($v['MetodoEnvio']['dependencia'] != 'blueexpress') {

            $log[] = array(
                'Log' => array(
                    'administrador' => "Venta {$id} no tiene dependencia con blueexpress",
                    'modulo'         => 'BlueExpressComponent',
                    'modulo_accion' => json_encode($v['MetodoEnvio'])
                )
            );
            ClassRegistry::init('Log')->create();
            ClassRegistry::init('Log')->saveMany($log);
            return false;
        }

        if (is_null($v['MetodoEnvio']['token_blue_express']) or is_null($v['MetodoEnvio']['cod_usuario_blue_express']) or is_null($v['MetodoEnvio']['cta_corriente_blue_express'])) {

            $log[] = array(
                'Log' => array(
                    'administrador' => "Metodo " . $v['MetodoEnvio']['id'] . " no posee credenciales",
                    'modulo'         => 'BlueExpressComponent',
                    'modulo_accion' => json_encode($v['MetodoEnvio'])
                )
            );
            ClassRegistry::init('Log')->create();
            ClassRegistry::init('Log')->saveMany($log);
            return false;
        }

        $this->crearCliente($v['MetodoEnvio']['token_blue_express'], $v['MetodoEnvio']['cod_usuario_blue_express'], $v['MetodoEnvio']['cta_corriente_blue_express']);

        $historicos = array();

        $total_en_espera = array_sum(Hash::extract($v, 'VentaDetalle.{n}.cantidad_en_espera'));

        # Registramos el estado de los bultos
        foreach ($v['Transporte'] as $trans) {
            # Obtenemos los estados del bulto

            $estados = $this->blue_express->BXTrackingPull($trans['TransportesVenta']['cod_seguimiento']);

            if ($estados['code'] != 200) {

                $log[] = array(
                    'Log' => array(
                        'administrador' => "Venta {$id} tiene problemas con api BlueExpress",
                        'modulo'         => 'BlueExpressComponent',
                        'modulo_accion' => 'Problemas con seguimiento: ' . json_encode($estados)
                    )
                );
                continue;
            }

            $estadosHistoricosParcial = ClassRegistry::init('EnvioHistorico')->find('count', array(
                'conditions' => array(
                    'EnvioHistorico.transporte_venta_id' => $trans['TransportesVenta']['id'],
                    'EnvioHistorico.nombre LIKE' => '%parcial%'
                )
            ));

            $es_envio_parcial = false;

            # si la venta tiene productos en espera, quiere decir que es un envio parcial
            # si tiene un registro de envio parcial, termina como envio parcial
            if ($estadosHistoricosParcial > 0 || $total_en_espera > 0) {
                $es_envio_parcial = true;
            }

            foreach ($estados['response']['data']['pinchazos'] as $e) {
                try {
                    if ($es_envio_parcial) {
                        $estado_nombre = $e['tipoMovimiento']['descripcion'] . ' parcial';
                    } else {
                        $estado_nombre = $e['tipoMovimiento']['descripcion'];
                    }

                    # Verificamos que el estado no exista en los registros
                    if (ClassRegistry::init('EnvioHistorico')->existe($estado_nombre, $trans['TransportesVenta']['id'])) {
                        continue;
                    }

                    $estado_existe = ClassRegistry::init('EstadoEnvio')->obtener_por_nombre($estado_nombre, 'BlueExpress');

                    if (!$estado_existe) {
                        $estado_existe = ClassRegistry::init('EstadoEnvio')->crear($estado_nombre, null, 'BlueExpress', "{$e['tipoMovimiento']['codigo']} - {$e['tipoMovimiento']['descripcion']}");
                    }

                    # Sólo se crean los estados nuevos
                    $historicos[] = array(
                        'EnvioHistorico' => array(
                            'transporte_venta_id' => $trans['TransportesVenta']['id'],
                            'estado_envio_id' => $estado_existe['EstadoEnvio']['id'],
                            'nombre' => $estado_nombre,
                            'leyenda' => $estado_existe['EstadoEnvio']['leyenda'],
                            'canal' => 'BlueExpress',
                            'created' => $e['fechaHora'] ?? date("Y-m-d H:m:s")
                        )
                    );
                } catch (\Throwable $th) {

                    $log[] = array(
                        'Log' => array(
                            'administrador'     => "Problemas al recorrer response de BlueExpress vid - {$id}",
                            'modulo'            => 'BlueExpressComponent',
                            'modulo_accion'     => json_encode(['catch' => $th, 'response BlueExpress' => $estados])
                        )
                    );
                }
            }
        }

        if ($historicos) {
            $log[] = array(
                'Log' => array(
                    'administrador'     => count($historicos) . " nuevos estados del vid - {$id}",
                    'modulo'            => 'BlueExpressComponent',
                    'modulo_accion'     => json_encode($historicos)
                )
            );
        }

        ClassRegistry::init('Log')->create();
        ClassRegistry::init('Log')->saveMany($log);

        if (empty($historicos)) {
            return false;
        }

        ClassRegistry::init('EnvioHistorico')->create();

        return ClassRegistry::init('EnvioHistorico')->saveMany($historicos);
    }

    public function obtener_costo_envio($venta, $largoTotal, $anchoTotal, $altoTotal, $pesoTotal)
    {

        $data = [
            'from' => [
                "district"   =>  $venta['MetodoEnvio']['Bodega']['Comuna']['district_id_blue_express']
            ],
            'to' => [
                "state"     => $venta['Comuna']['state_id_blue_express'],
                "district"  => $venta['Comuna']['district_id_blue_express'],
            ],
            'serviceType' => $venta['MetodoEnvio']['tipo_servicio_blue_express'],
            'datosProducto' => [
                "producto"          => "P",
                "familiaProducto"   => "PAQU",
                "largo"             => $largoTotal * 1,
                "ancho"             => $anchoTotal * 1,
                "alto"              => $altoTotal * 1,
                "pesoFisico"        => $pesoTotal * 1,
                "cantidadPiezas"    => 1,
                "unidades"          => 1
            ]
        ];

        return $this->blue_express->BXPricing($data);
    }

    public function generar_ot($venta)
    {

        $volumenMaximo = $venta['MetodoEnvio']['volumen_maximo'];
        # Algoritmo LAFF para ordenamiento de productos
        $paquetes = $this->LAFFPack->obtener_bultos_venta_dimension_decimal($venta, $volumenMaximo);

        $log = array();

        # si no hay paquetes se retorna false
        if (empty($paquetes)) {

            $log[] = array(
                'Log' => array(
                    'administrador' => 'BlueExpress vid:' . $venta['Venta']['id'],
                    'modulo' => 'BlueExpressComponent',
                    'modulo_accion' => 'No fue posible generar la OT ya que no hay paquetes disponibles'
                )
            );

            ClassRegistry::init('Log')->create();
            ClassRegistry::init('Log')->saveMany($log);

            return false;
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
                    'administrador' => 'BlueExpress vid:' . $venta['Venta']['id'],
                    'modulo' => 'BlueExpressComponent',
                    'modulo_accion' => 'No fue posible generar la OT por restricción de peso: Peso bulto ' . $peso_total . ' kg - Peso máximo permitido ' . $peso_maximo_permitido
                )
            );

            ClassRegistry::init('Log')->create();
            ClassRegistry::init('Log')->saveMany($log);

            return false;
        }

        $transportes = array();

        $ruta_pdfs = null;
        $exito = true;

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

        $costo_envio = 0;
        $numero_paquete = 0;

        foreach ($paquetes as $paquete) {
            $numero_paquete++;
            # dimensiones de todos los paquetes unificado
            $largoTotal             = $paquete['paquete']['length'] * 1;
            $anchoTotal             = $paquete['paquete']['width'] * 1;
            $altoTotal              = $paquete['paquete']['height'] * 1;
            $pesoTotal              = $paquete['paquete']['weight'] * 1;

            $obtener_costo_envio    = $this->obtener_costo_envio($venta, $largoTotal, $anchoTotal, $altoTotal, $pesoTotal);

            if ($obtener_costo_envio['code'] != 200) {
                $log[] = array(
                    'Log' => array(
                        'administrador' => 'BlueExpress, dificultades para obtener costo de envio vid:' . $venta['Venta']['id'],
                        'modulo'        => 'Ventas',
                        'modulo_accion' => json_encode(["Paquete {$numero_paquete}" => $obtener_costo_envio])
                    )
                );
                $exito = false;
                continue;
            }

            $log[] = array(
                'Log' => array(
                    'administrador' => 'BlueExpress retorno costo de envio vid:' . $venta['Venta']['id'],
                    'modulo'        => 'BlueExpressComponent',
                    'modulo_accion' => json_encode(["Paquete {$numero_paquete}" => $obtener_costo_envio])
                )
            );

            # creamos el arreglo para generar la OT
            $data = [
                'serviceType' => $venta['MetodoEnvio']['tipo_servicio_blue_express'],
                'venta'     => [
                    'id'                    => $venta['Venta']['id'],
                    'referencia'            => $venta['Venta']['referencia'],
                    'referencia_despacho'   => $venta['Venta']['referencia_despacho'],
                    'costo_envio'           => $obtener_costo_envio['response']['data']['total']
                ],
                'pickup'    => [
                    'stateId'       => $venta['MetodoEnvio']['Bodega']['Comuna']['state_id_blue_express'],
                    'districtId'    => $venta['MetodoEnvio']['Bodega']['Comuna']['district_id_blue_express'],
                    'address'       => $venta['MetodoEnvio']['Bodega']['direccion'],
                    'name'          => $venta['MetodoEnvio']['Bodega']['nombre'],
                    'fullname'      => $venta['MetodoEnvio']['Bodega']['nombre_contacto'],
                    'phone'         => $venta['MetodoEnvio']['Bodega']['fono'],
                ],
                'dropoff'   => [
                    'nombre_receptor'   => $venta['Venta']['nombre_receptor'],
                    'fono_receptor'     => $venta['Venta']['fono_receptor'],
                    'stateId'           => $venta['Comuna']['state_id_blue_express'],
                    'districtId'        => $venta['Comuna']['district_id_blue_express'],
                    'direccion'         => "{$venta['Venta']['direccion_entrega']} {$venta['Venta']['numero_entrega']}, {$venta['Venta']['otro_entrega']} ",
                    'name'              => $venta['Venta']['nombre_receptor'],
                ],
                'packages'  => [
                    'peso'  => ceil($pesoTotal),
                    'largo' => ceil($largoTotal),
                    'ancho' => ceil($anchoTotal),
                    'alto'  => ceil($altoTotal),
                ],
                'credenciales' => $venta['MetodoEnvio']['usuario_blue_express']
            ];

            $response = $this->blue_express->BXEmission($data);
            if ($response['code'] != 200 || !$response['response']['status']) {
                $log[] = array(
                    'Log' => array(
                        'administrador' => 'BlueExpress, problemas para generar ot para vid:' . $venta['Venta']['id'],
                        'modulo'        => 'BlueExpressComponent',
                        'modulo_accion' => json_encode(["Paquete {$numero_paquete}" => $response])
                    )
                );
                $exito = false;
                continue;
            }

            $log[] = array(
                'Log' => array(
                    'administrador' => 'BlueExpress retorno ot vid:' . $venta['Venta']['id'],
                    'modulo'        => 'BlueExpressComponent',
                    'modulo_accion' => json_encode(["Paquete {$numero_paquete}" => $response])
                )
            );



            $nombreEtiqueta = $response['response']['data']['trackingNumber'] . date("Y-m-d H:i:s") . '.pdf';
            $modulo_ruta = 'webroot' . DS . 'img' . DS . 'ModuloBlueExpress' . DS . $venta['Venta']['id'] . DS;
            $rutaPublica    = APP .  $modulo_ruta;

            if (!is_dir($rutaPublica)) {
                @mkdir($rutaPublica, 0775, true);
            }

            $etiqueta = $this->Etiquetas->generarEtiquetaExternaTransporte($response['response']['data']['labels'][0]['contenido']);

            if ($etiqueta['curl_getinfo'] == 200) {

                $file = fopen($rutaPublica . $nombreEtiqueta, "w");
                fwrite($file, $etiqueta['etiquetaPdf']);
                fclose($file);

                $ruta_pdfs = 'https://' . $_SERVER['HTTP_HOST'] . DS . $modulo_ruta . $nombreEtiqueta;
            } else {
                $ruta_pdfs = null;
            }


            # Guardamos el transportista y el/los numeros de seguimiento
            $carrier_name = 'BLUEXPRESS';
            $carrier_opt = array(
                'Transporte' => array(
                    'codigo' => 'BLUEXPRESS',
                    'url_seguimiento' => 'https://www.bluex.cl/seguimiento/' // Url de seguimiento BlueExpress
                )
            );

            if (!empty($rutaPublica)) {
                $carrier_opt = array_replace_recursive($carrier_opt, array(
                    'Transporte'    => array(
                        'etiqueta'  => $rutaPublica
                    )
                ));
            }

            $transportes[] = [
                'transporte_id'   => ClassRegistry::init('Transporte')->obtener_transporte_por_nombre($carrier_name, true, $carrier_opt),
                'cod_seguimiento' => $response['response']['data']['trackingNumber'],
                'etiqueta'        => $ruta_pdfs,
                'entrega_aprox'   =>  date("Y-m-d", strtotime($obtener_costo_envio['response']['data']['fechaEstimadaEntrega']))
            ];

            if (empty($transportes)) {
                continue;
                $exito = false;
            }

            $costo_envio = $costo_envio + $obtener_costo_envio['response']['data']['total'];
        }

        if ($transportes) {
            $fin_proceso_ot = array(
                'Venta' => array(
                    'id'                        => $venta['Venta']['id'],
                    'paquete_generado'          => count($paquetes),
                    'costo_envio'               => $costo_envio,
                    'etiqueta_envio_externa'    => $ruta_pdfs
                ),
                'Transporte' => $transportes
            );

            # Se guarda la información del tracking en la venta
            if (ClassRegistry::init('Venta')->saveAll($fin_proceso_ot)) {
                $log[] = array(
                    'Log' => array(
                        'administrador' => 'BlueExpress, se registro ot vid:' . $venta['Venta']['id'],
                        'modulo'        => 'BlueExpressComponent',
                        'modulo_accion' => json_encode($fin_proceso_ot)
                    )
                );
                $exito = true;
            } else {
                $log[] = array(
                    'Log' => array(
                        'administrador' => 'BlueExpress, dificultades para guardar información ot vid:' . $venta['Venta']['id'],
                        'modulo'        => 'BlueExpressComponent',
                        'modulo_accion' => json_encode($fin_proceso_ot)
                    )
                );
            }
        }


        ClassRegistry::init('Log')->create();
        ClassRegistry::init('Log')->saveMany($log);

        return $exito;
    }
}
