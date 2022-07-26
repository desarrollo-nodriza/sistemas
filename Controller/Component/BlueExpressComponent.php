<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'BlueExpress', array('file' => 'BlueExpress/BlueExpress.php'));
App::import('Vendor', 'PDFMerger', array('file' => 'PDFMerger/PDFMerger.php'));

class BlueExpressComponent extends Component
{

    private $blue_express;
    public $components = array('LAFFPack', 'LibreDte', 'Etiquetas');
    private $intentos = 1;

    public $tipo_servicio = [
        "EX" => "Express.",
        "PR" => "Premium.",
        "PY" => "Prioritario.",
        "MD" => "Same Day."
    ];

    public function crearCliente($BX_TOKEN, $BX_USERCODE, $BX_CLIENT_ACCOUNT)
    {
        $this->blue_express = new BlueExpress($BX_TOKEN, $BX_USERCODE, $BX_CLIENT_ACCOUNT);
    }

    public function regenerar_etiqueta($trackingNumber, $venta_id)
    {

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

    public function registrar_estados($TransportesVenta, $total_en_espera, $sleep = 5)
    {
        sleep($sleep);

        $log = [];

        $log[] = array(
            'Log' => array(
                'administrador' => 'Información de la Venta - vid ' . $TransportesVenta['venta_id'],
                'modulo'        => 'BlueExpressComponent',
                'modulo_accion' => json_encode($TransportesVenta)
            )
        );

        $historicos = [];

        # Registramos el estado de los bultos
        # Obtenemos los estados del bulto

        $estados = $this->blue_express->BXTrackingPull($TransportesVenta['cod_seguimiento']);

        $log[] = array(
            'Log' => array(
                'administrador'     => "Respuesta codigo de seguimiento {$TransportesVenta['cod_seguimiento']} | vid - {$TransportesVenta['venta_id']}",
                'modulo'            => 'BlueExpressComponent',
                'modulo_accion'     => json_encode($estados)
            )
        );

        if ($estados['code'] != 200) {

            $log[] = array(
                'Log' => array(
                    'administrador' => "Venta {$TransportesVenta['venta_id']} tiene problemas con api BlueExpress",
                    'modulo'        => 'BlueExpressComponent',
                    'modulo_accion' => 'Problemas con seguimiento: ' . json_encode($estados)
                )
            );
            return false;
        }

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

            foreach ($estados['response']['data']['pinchazos'] ?? [] as $e) {
                try {
                    if ($es_envio_parcial) {
                        $estado_nombre = $e['tipoMovimiento']['descripcion'] . ' parcial';
                    } else {
                        $estado_nombre = $e['tipoMovimiento']['descripcion'];
                    }

                    # Verificamos que el estado no exista en los registros
                    if (ClassRegistry::init('EnvioHistorico')->existe($estado_nombre, $TransportesVenta['id'])) {
                        continue;
                    }

                    $estado_existe = ClassRegistry::init('EstadoEnvio')->obtener_por_nombre($estado_nombre, 'BlueExpress');

                    if (!$estado_existe) {
                        $estado_existe = ClassRegistry::init('EstadoEnvio')->crear($estado_nombre, null, 'BlueExpress', "{$e['tipoMovimiento']['codigo']} - {$e['tipoMovimiento']['descripcion']}");
                    }

                    # Sólo se crean los estados nuevos
                    $historicos[] = array(
                        'EnvioHistorico' => array(
                            'transporte_venta_id'   => $TransportesVenta['id'],
                            'estado_envio_id'       => $estado_existe['EstadoEnvio']['id'],
                            'nombre'                => $estado_nombre,
                            'leyenda'               => $estado_existe['EstadoEnvio']['leyenda'],
                            'canal'                 => 'BlueExpress',
                            'created'               => $e['fechaHora'] ?? date("Y-m-d H:m:s")
                        )
                    );
                } catch (\Throwable $th) {

                    $log[] = array(
                        'Log' => array(
                            'administrador'     => "Problemas al recorrer response de BlueExpress vid - {$TransportesVenta['venta_id']}",
                            'modulo'            => 'BlueExpressComponent',
                            'modulo_accion'     => json_encode(['catch' => $th, 'response BlueExpress' => $estados])
                        )
                    );
                }
            }


            if ($historicos) {
                $log[] = array(
                    'Log' => array(
                        'administrador'     => count($historicos) . " nuevos estados del vid - {$TransportesVenta['venta_id']}",
                        'modulo'            => 'BlueExpressComponent',
                        'modulo_accion'     => json_encode($historicos)
                    )
                );
            }

            ClassRegistry::init('Log')->create();
            ClassRegistry::init('Log')->saveMany($log);

            if (empty($historicos)) {

                // * Debido a problemas para recuperar los estados se hacen tres intentos
                if ($this->intentos <= 3) {
                    $this->intentos++;
                    $this->registrar_estados($TransportesVenta, $total_en_espera, 2);
                }

                ClassRegistry::init('Log')->create();
                ClassRegistry::init('Log')->save(array(
                    'Log' => array(
                        'administrador' => "Venta {$TransportesVenta['venta_id']} obteniendo estados",
                        'modulo'        => 'BlueExpressComponent',
                        'modulo_accion' => "Número de intentos: {$this->intentos} para obtener estados de seguimiento."
                    )
                ));

                $this->intento = 1;

                return false;
            }


            ClassRegistry::init('EnvioHistorico')->create();
            $return = ClassRegistry::init('EnvioHistorico')->saveMany($historicos);
        } catch (\Throwable $th) {
        }

        return $return;
    }

    public function obtener_costo_envio($venta, $largoTotal, $anchoTotal, $altoTotal, $pesoTotal, $CuentaCorrienteTransporte)
    {

        $data = [
            'from' => [
                "district"   =>  $CuentaCorrienteTransporte['informacion_bodega']['Comuna']['district_id_blue_express']
            ],
            'to' => [
                "state"     => $venta['Comuna']['state_id_blue_express'],
                "district"  => $venta['Comuna']['district_id_blue_express'],
            ],
            'serviceType'   => $CuentaCorrienteTransporte['serviceType'],
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
                    'administrador' => "BlueExpress vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}",
                    'modulo'        => 'BlueExpressComponent',
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
                    'administrador' => "BlueExpress vid: {$venta['Venta']['id']} embalaje: {$embalaje['id']}",
                    'modulo'        => 'BlueExpressComponent',
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

            $obtener_costo_envio = $this->obtener_costo_envio($venta, $largoTotal, $anchoTotal, $altoTotal, $pesoTotal, $CuentaCorrienteTransporte);

            if ($obtener_costo_envio['code'] != 200) {

                $log[] = array(
                    'Log' => array(
                        'administrador' => 'BlueExpress, dificultades para obtener costo de envio vid:' . $venta['Venta']['id'],
                        'modulo'        => 'Ventas',
                        'modulo_accion' => json_encode(["Paquete {$numero_paquete}" => $obtener_costo_envio])
                    )
                );

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
                'serviceType'   => $CuentaCorrienteTransporte['serviceType'] ?? "",
                'companyId'     => $CuentaCorrienteTransporte['companyId'] ?? "",
                'venta'         => [
                    'id'                    => $venta['Venta']['id'],
                    'referencia'            => $venta['Venta']['referencia'],
                    'referencia_despacho'   => $venta['Venta']['referencia_despacho'],
                    'costo_envio'           => $obtener_costo_envio['response']['data']['total']
                ],
                'pickup'        => [
                    'stateId'       => $CuentaCorrienteTransporte['informacion_bodega']['Comuna']['state_id_blue_express'] ?? "",
                    'districtId'    => $CuentaCorrienteTransporte['informacion_bodega']['Comuna']['district_id_blue_express'] ?? "",
                    'address'       => inflector::slug($CuentaCorrienteTransporte['informacion_bodega']['direccion'] ?? "", ' '),
                    'name'          => inflector::slug($CuentaCorrienteTransporte['informacion_bodega']['nombre'] ?? "", ' '),
                    'fullname'      => inflector::slug($CuentaCorrienteTransporte['informacion_bodega']['nombre_contacto'] ?? "", ' '),
                    'phone'         => $CuentaCorrienteTransporte['informacion_bodega']['fono'] ?? "",
                ],
                'dropoff'       => [
                    'nombre_receptor'   => inflector::slug($venta['Venta']['nombre_receptor'], ' '),
                    'fono_receptor'     => $venta['Venta']['fono_receptor'],
                    'stateId'           => $venta['Comuna']['state_id_blue_express'],
                    'districtId'        => $venta['Comuna']['district_id_blue_express'],
                    'direccion'         => inflector::slug("{$venta['Venta']['direccion_entrega']} {$venta['Venta']['numero_entrega']}, {$venta['Venta']['otro_entrega']} ", ' '),
                    'name'              => inflector::slug($venta['Venta']['nombre_receptor'], ' '),
                ],
                'packages'      => [
                    'peso'  => round($pesoTotal, 1),
                    'largo' => round($largoTotal, 1),
                    'ancho' => round($anchoTotal, 1),
                    'alto'  => round($altoTotal, 1),
                ],
                'credenciales'  => $CuentaCorrienteTransporte['credenciales'] ?? "",
                'extendedClaim' => $CuentaCorrienteTransporte['extendedClaim'] ? true : false ?? false,
            ];

            $response = $this->blue_express->BXEmission($data);

            $log[] = array(
                'Log' => array(
                    'administrador' => $response['code'] != 200 || !$response['response']['status'] ? 'BlueExpress, problemas para generar ot para vid:' . $venta['Venta']['id'] : 'BlueExpress retorno ot vid:' . $venta['Venta']['id'],
                    'modulo'        => 'BlueExpressComponent',
                    'modulo_accion' => json_encode(["Paquete {$numero_paquete}" => $response])
                )
            );

            if ($response['code'] != 200 || !$response['response']['status']) {

                continue;
            }


            $nombreEtiqueta = $response['response']['data']['trackingNumber'] . date("Y-m-d H:i:s") . '.pdf';
            $modulo_ruta    = 'webroot' . DS . 'img' . DS . 'ModuloBlueExpress' . DS . $venta['Venta']['id'] . DS;
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

            $transportes[] =
                [
                    'TransportesVenta' =>
                    [
                        'transporte_id'             => ClassRegistry::init('Transporte')->obtener_transporte_por_nombre($carrier_name, true, $carrier_opt),
                        'venta_id'                  => $venta['Venta']['id'],
                        'cod_seguimiento'           => $response['response']['data']['trackingNumber'],
                        'etiqueta'                  => $ruta_pdfs,
                        'entrega_aprox'             => date("Y-m-d", strtotime($obtener_costo_envio['response']['data']['fechaEstimadaEntrega'])),
                        'paquete_generado'          => count($paquetes),
                        'costo_envio'               => $obtener_costo_envio['response']['data']['total'],
                        'etiqueta_envio_externa'    => $ruta_pdfs,
                        'embalaje_id'               => $embalaje["id"]
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
                        'administrador' => 'BlueExpress, se registro ot vid:' . $venta['Venta']['id'],
                        'modulo'        => 'BlueExpressComponent',
                        'modulo_accion' => json_encode($transportes)
                    )
                );

                $exito = true;
            } else {
                $log[] = array(
                    'Log' => array(
                        'administrador' => 'BlueExpress, dificultades para guardar información ot vid:' . $venta['Venta']['id'],
                        'modulo'        => 'BlueExpressComponent',
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
