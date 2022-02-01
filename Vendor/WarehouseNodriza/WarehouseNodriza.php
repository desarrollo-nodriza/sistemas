<?php
class WarehouseNodriza
{

    protected static $API_ROOT_URL;

    // ** La URL local apunta a heroku que es donde se suben los cambios para no cambiar de rama en desarrollo
    private $URLs =  [
        'local' => 'https://warehouse-api-nodriza.herokuapp.com',
        'dev'   => 'https://dev-warehouse.nodriza.cl',
        'prod'  => 'https://warehouse.nodriza.cl',
    ];

    protected static $BX_TOKEN;

    /**
     * __construct
     * Es necesario inicializar la clase para hacer uso de los metodos
     * @param  mixed $BX_TOKEN
     * @param  mixed $BX_USERCODE
     * @param  mixed $BX_CLIENT_ACCOUNT
     * @return void
     */
    // TODO Se inializan las variables  
    public function __construct($BX_TOKEN, $API_ROOT_URL = 'local')
    {
        self::$API_ROOT_URL = $this->URLs[$API_ROOT_URL] ?? 'https://dev-warehouse.nodriza.cl';
        self::$BX_TOKEN     = $BX_TOKEN ?? '';
    }

    // TODO Solo cambia estado del embalaje
    public function CambiarCancelado($embalajes)
    {
        return $this->cURL_POST('/api/v1/embalaje/cambiar-estado-a-cancelado', $embalajes);
    }

    // TODO Metodo que ademas de cambiar estado cancela embalajes que no hayan sido cancelados ni finzalizados, y zonifica productos que se hayan estado embalando
    public function CambiarCancelado_V2($venta_id, $responsable_id_cancelado, $devolucion, $motivo_cancelado = null)
    {
        return $this->cURL_POST(
            '/api/v2/embalaje/cambiar-estado-a-cancelado',
            [
                "venta_id"                  => $venta_id,
                "responsable_id_cancelado"  => $responsable_id_cancelado,
                "devolucion"                => $devolucion,
                "motivo_cancelado"          => $motivo_cancelado,
            ]
        );
    }
    // TODO Cuando se anulan Items de una venta se vuelve a recrear embalaje
    public function RecrearEmbalajesPorItemAnulados($venta)
    {
        return $this->cURL_POST(
            '/api/v1/embalaje/recrear-embalajes-por-item-anulados',
            $venta
        );
    }

    // TODO Añade OT al embalaje
    public function OrdenTransporteEmbalajes($orden_transporte)
    {
        // $ejemplo = [
        //     [
        //         "embalaje_id"       => 218,
        //         "orden_transporte"  => 12312312323
        //     ],
        //     [
        //         "embalaje_id"       => 218,
        //         "orden_transporte"  => 12312312323
        //     ]
        // ];

        return $this->cURL_POST(
            '/api/v1/orden_transporte_embalajes/crear',
            $orden_transporte
        );
    }

    // TODO Crea un embalaje
    public function CrearPedido($embalaje)
    {
        // [
        //     "venta_id"=> 52624,
        //     "bodega_id"=> 1,
        //     "marketplace_id"=> 2,
        //     "metodo_envio_id"=> 2,
        //     "comuna_id"=> 1,
        //     "prioritario"=> 1,
        //     "fecha_venta"=> "2020-05-05",
        //     "responsable"=>232,
        //     "productos"=> [
        //         [
        //             "producto_id"=> 10121,
        //             "detalle_id"=> 74466,
        //             "cantidad_a_embalar"=> 2
        //         ],
        //         [
        //             "producto_id"=> 15727,
        //             "detalle_id"=> 74466,
        //             "cantidad_a_embalar"=> 1
        //         ]
        //     ]
        // ]
        return $this->cURL_POST(
            '/api/v2/embalaje/crear-pedido',
            $embalaje
        );
    }

    // TODO Obtenemos evidencia de los embalajes despachados
    public function ObtenerEvidencia($embalaje)
    {
        // [
        //     "embalajes_id" => [
        //         [
        //             "embalaje_id" => 6701
        //         ],
        //         [
        //             "embalaje_id" => 6711
        //         ],
        //     ]
        // ]
        return $this->cURL_POST(
            '/api/v1/embalaje/obtener-evidencia',
            $embalaje
        );
    }
    
    /**
     * ObtenerEmbalajesVenta
     * Obtenemos todos los embalajes excepto los cancelados
     * @param  mixed $venta_id
     * @return void
     */
    public function ObtenerEmbalajesVenta($venta_id)
    {
        return $this->cURL_GET("/api/v1/embalaje/embalaje-venta/{$venta_id}");
    }

    // TODO Abstraccion para hacer peticiones a endpoints por metodo POST
    private function cURL_POST($URL, $POSTFIELDS)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$API_ROOT_URL . $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HEADER => false,
            CURLOPT_POSTFIELDS => json_encode($POSTFIELDS, true),
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . self::$BX_TOKEN
            ),
        ));

        $response   = curl_exec($curl);
        $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error     = curl_error($curl);
        curl_close($curl);
        return [
            "code"        => $http_code,
            "request"     => $POSTFIELDS,
            "response"    => json_decode($response, true),
            "curl_error"  => $curl_error,
            'url'         => self::$API_ROOT_URL . $URL
        ];
    }

    // TODO Abstraccion para hacer peticiones a endpoints por metodo GET
    private function cURL_GET($URL)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => self::$API_ROOT_URL . $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 1000,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_HTTPHEADER => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . self::$BX_TOKEN
            ),
        ));

        $response       = curl_exec($curl);
        $http_code      = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error     = curl_error($curl);

        curl_close($curl);

        return [
            "code"       => $http_code,
            "response"   => json_decode($response, true),
            "curl_error" => $curl_error,
            'url'        => self::$API_ROOT_URL . $URL
        ];
    }
}
