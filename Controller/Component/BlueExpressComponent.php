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

    // public function prueba()
    // {
    //     set_time_limit(0);
    //     $this->crearCliente('823a23c8a5ae0efc91e1bd8b40a12a63', '14372', '96801150-11-8');
    //     // return $this->blue_express->BXTrackingPull(74438);
    //     $venta = ClassRegistry::init('Venta')->obtener_venta_por_id(74438);

    //     return $this->generar_ot($venta);

    //     // return $this->registrar_estados(74438);
    // }

    public function solicitar_etiqueta($trackingNumber)
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
        $response = $this->blue_express->BXLabel($trackingNumber);
        if ($response['code'] == 200) {
            $response['response'] = base64_decode($response['response']['data'][0]['base64']);
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
                        'MetodoEnvio.cta_corriente_blue_express',
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
        foreach ($v['Transporte'] as $it => $trans) {
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

            $log[] = array(
                'Log' => array(
                    'administrador'     => "Estados de BlueExpress, Seguimiento n° " . $trans['TransportesVenta']['cod_seguimiento'] . " vid {$id}",
                    'modulo'            => 'BlueExpressComponent',
                    'modulo_accion'     => 'Estados embalaje: ' . json_encode($estados)
                )
            );

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

                    $log[] = array(
                        'Log' => array(
                            'administrador'     => "Nuevo estado del vid - {$id}",
                            'modulo'            => 'BlueExpressComponent',
                            'modulo_accion'     => json_encode($historicos)
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

        $from = [
            "district"   =>  $venta['MetodoEnvio']['Bodega']['Comuna']['district_id_blue_express']
        ];
        $to = [
            "state"     => $venta['Comuna']['state_id_blue_express'],
            "district"  => $venta['Comuna']['district_id_blue_express'],
        ];
        $datosProducto = [
            "producto"          => "P",
            "familiaProducto"   => "PAQU",
            "largo"             => $largoTotal * 1,
            "ancho"             => $anchoTotal * 1,
            "alto"              => $altoTotal * 1,
            "pesoFisico"        => $pesoTotal * 1,
            "cantidadPiezas"    => 1,
            "unidades"          => 1
        ];

        return $this->blue_express->BXPricing($from, $to, $datosProducto);
    }

    public function generar_ot($venta)
    {

        $volumenMaximo = (float) 60;
        # Algoritmo LAFF para ordenamiento de productos
        $paquetes = $this->LAFFPack->obtener_bultos_venta_dimension_decimal($venta, $volumenMaximo);

        $log = array();

        # si no hay paquetes se retorna false
        if (empty($paquetes)) {

            $log[] = array(
                'Log' => array(
                    'administrador' => 'BlueExpress vid:' . $venta['Venta']['id'],
                    'modulo' => 'Ventas',
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
                    'modulo' => 'Ventas',
                    'modulo_accion' => 'No fue posible generar la OT por restricción de peso: Peso bulto ' . $peso_total . ' kg - Peso máximo permitido ' . $peso_maximo_permitido
                )
            );

            ClassRegistry::init('Log')->create();
            ClassRegistry::init('Log')->saveMany($log);

            return false;
        }

        $transportes = array();

        $ruta_pdfs = array();
        $nwVenta = [];
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

        foreach ($paquetes as $paquete) {

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
                        'modulo_accion' => json_encode(($obtener_costo_envio))
                    )
                );
                $exito = false;
                continue;
            }

            $log[] = array(
                'Log' => array(
                    'administrador' => 'BlueExpress retorno costo de envio vid:' . $venta['Venta']['id'],
                    'modulo' => 'Ventas',
                    'modulo_accion' => json_encode($obtener_costo_envio)
                )
            );

            # creamos el arreglo para generar la OT
            $data = [
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
                    'direccion'         => $venta['Venta']['direccion_entrega'],
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

            if ($response['code'] != 200) {
                $log[] = array(
                    'Log' => array(
                        'administrador' => 'BlueExpress, problemas para generar ot para vid:' . $venta['Venta']['id'],
                        'modulo' => 'Ventas',
                        'modulo_accion' => json_encode($response)
                    )
                );
                $exito = false;
                continue;
            }

            $log[] = array(
                'Log' => array(
                    'administrador' => 'BlueExpress retorno ot vid:' . $venta['Venta']['id'],
                    'modulo' => 'Ventas',
                    'modulo_accion' => json_encode($response)
                )
            );

            $nombreEtiqueta = $response['response']['data']['trackingNumber'] . date("Y-m-d H:i:s") . '.pdf';
            $modulo_ruta = 'img' . DS . 'ModuloBlueExpress' . DS;
            $rutaPublica    = APP . 'webroot' . DS . $modulo_ruta . $venta['Venta']['id'] . DS;


            if (!is_dir($rutaPublica)) {
                @mkdir($rutaPublica, 0775, true);
            }

            $file = fopen($rutaPublica . $nombreEtiqueta, "w");
            fwrite($file, base64_decode($response['response']['data']['labels'][0]['contenido']));
            fclose($file);

            $ruta_pdfs = 'https://' . $_SERVER['HTTP_HOST'] . DS. $modulo_ruta . $nombreEtiqueta;
            
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
                    'Transporte' => array(
                        'etiqueta' => $rutaPublica
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

            $fin_proceso_ot = array(
                'Venta' => array(
                    'id'                => $venta['Venta']['id'],
                    'paquete_generado'  => 1,
                    'costo_envio'       => $obtener_costo_envio['response']['data']['total'],
                    'etiqueta_envio_externa' => $ruta_pdfs
                ),
                'Transporte' => $transportes
            );
            $nwVenta[] = $fin_proceso_ot;
            # Se guarda la información del tracking en la venta
            if (ClassRegistry::init('Venta')->saveAll($fin_proceso_ot)) {
                $log[] = array(
                    'Log' => array(
                        'administrador' => 'BlueExpress, se registro ot vid:' . $venta['Venta']['id'],
                        'modulo' => 'Ventas',
                        'modulo_accion' => json_encode($fin_proceso_ot)
                    )
                );
                $exito = true;
            } else {
                $log[] = array(
                    'Log' => array(
                        'administrador' => 'BlueExpress, dificultades para guardar información ot vid:' . $venta['Venta']['id'],
                        'modulo' => 'Ventas',
                        'modulo_accion' => json_encode($$fin_proceso_ot)
                    )
                );
            }
        }

        ClassRegistry::init('Log')->create();
        ClassRegistry::init('Log')->saveMany($log);

        return $exito;
    }

    public function homologar_comunas()
    {
        set_time_limit(0);
        $comunas = ClassRegistry::init("Comuna")->find('all', [
            'conditions' => ['Comuna.district_id_blue_express' => null]
        ]);

        $this->crearCliente("1", "1", "1");
        $comunas_blue_express = json_decode($this->blue_express::$STATE, true);
        $contador = 0;
        foreach ($comunas as $key => $comuna) {
            $romper = false;
            $nombre = trim(mb_strtoupper($comuna['Comuna']['nombre']));
            $nombre = str_replace('Á', 'A', $nombre);
            $nombre = str_replace('É', 'E', $nombre);
            $nombre = str_replace('Í', 'I', $nombre);
            $nombre = str_replace('Ó', 'O', $nombre);
            $nombre = str_replace('Ú', 'U', $nombre);
            $nombre = str_replace('Ü', 'U', $nombre);
            $nombre = str_replace('Ñ', 'N', $nombre);
            $NN[] = $nombre;
            foreach ($comunas_blue_express['data'][0]['states'] as $region) {
                $distritos = Hash::extract($region, 'ciudades.{*}.districts.{*}');

                foreach ($distritos as $distrito) {

                    if (trim($distrito['name']) == $nombre) {

                        $comuna['Comuna']['district_id_blue_express'] = $distrito['code'];
                        $comuna['Comuna']['state_id_blue_express']    = $region['code'];
                        $comunas[$key] =  $comuna;
                        $contador++;
                        $romper = true;

                        break;
                    }
                }

                if ($romper) {
                    break;
                }
            }
        }
        return ClassRegistry::init("Comuna")->saveAll($comunas) ? 'si' : 'no';
    }

    public function validar_comunas_con_blue_express()
    {
        set_time_limit(0);
        $comunas = ClassRegistry::init("Comuna")->find('all');

        $this->crearCliente("1", "1", "1");
        $comunas_blue_express   = json_decode($this->blue_express::$STATE, true);
        $comunas_blue_express_2 = [];

        foreach ($comunas as $comuna) {
            $romper = false;
            foreach ($comunas_blue_express['data'][0]['states'] as $region) {
                $distritos = Hash::extract($region, 'ciudades.{*}.districts.{*}');

                foreach ($distritos as $distrito) {

                    if (trim($distrito['code']) == $comuna['Comuna']['district_id_blue_express']) {

                        $romper                     = true;
                        if ($region['code'] != $comuna['Comuna']['state_id_blue_express']) {
                            $distrito[]                 = $region['code'];
                            $comunas_blue_express_2[]   = [$comuna['Comuna'], $distrito];
                        }
                        break;
                    }
                }

                if ($romper) {
                    break;
                }
            }
        }
        return $comunas_blue_express_2;
    }
}
