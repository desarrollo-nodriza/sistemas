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
    // * Se inializan las variables  
    public function __construct($BX_TOKEN, $API_ROOT_URL = 'local')
    {
        self::$API_ROOT_URL = $this->URLs[$API_ROOT_URL] ?? 'https://dev-warehouse.nodriza.cl';
        self::$BX_TOKEN     = $BX_TOKEN ?? '';
    }

    // * Solo cambia estado del embalaje
    public function CambiarCancelado($embalajes)
    {
        return $this->cURL_POST('/api/v1/embalaje/cambiar-estado-a-cancelado', $embalajes);
    }

    // * Metodo que ademas de cambiar estado cancela embalajes que no hayan sido cancelados ni finzalizados, y zonifica productos que se hayan estado embalando
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
    // * Cuando se anulan Items de una venta se vuelve a recrear embalaje
    public function RecrearEmbalajesPorItemAnulados($venta)
    {
        return $this->cURL_POST(
            '/api/v1/embalaje/recrear-embalajes-por-item-anulados',
            $venta
        );
    }

    // * Añade OT al embalaje
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

    // * Crea un embalaje
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

    // * Obtenemos evidencia de los embalajes despachados
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


    /**
     * ObtenerEmbalaje desde warehouse
     *
     * @param  mixed $embalaje_id
     * @return void
     */
    public function ObtenerEmbalaje($embalaje_id)
    {
        return $this->cURL_GET("/api/v1/embalaje/ver/{$embalaje_id}");
    }


    /**
     * Obtiene los embalajes de una venta dado su filtro
     * 
     * @param int $venta_id ID de la venta
     * @param array $filtro    criterios para filtrar
     * 
     * @return mixed    Embalajes
     */
    public function ObtenerEmbalajesVentaV2($venta_id, $filtro = [])
    {

        $path = "/api/v2/embalaje/embalaje-venta/{$venta_id}";

        foreach ($filtro as $param => $val) {
            $path = $path . "?{$param}=$val";
        }

        return $this->cURL_GET($path);
    }


    /** 
     * Crear nota de despacho
     * 
     * @param   array   $body   Arreglo de datos para crear la nota
     * 
     * @return mixed
     */
    public function crearNotaDespacho($body)
    {
        return $this->cURL_POST('/api/v1/embalaje/nota-embalaje-crear', $body);
    }


    /**
     * Editar nota de despacho
     * 
     * @param   int $id Identificador de la nota
     * @param   array   $body   Arreglo de datos
     * 
     * @return mixed
     */
    public function editarNotaDespacho($id, $body)
    {
        return $this->cURL_POST("/api/v1/embalaje/editar-nota/{$id}", $body);
    }


    /**
     * Elimina una nota de despacho
     * 
     * @param   int     $id Identificador de la nota
     * 
     * @return mixed
     */
    public function eliminarNotaDespacho($id)
    {
        return $this->cURL_POST("/api/v1/embalaje/eliminar-nota/{$id}", []);
    }


    /**
     * ObtenerNotasDespacho
     *
     * @param  mixed $filtro
     * @return void
     */
    public function ObtenerNotasDespacho($filtro = [])
    {
        $path = "/api/v1/embalaje/notas-despacho";

        foreach ($filtro as $param => $val) {
            $path = $path . "?{$param}=$val";
        }

        return $this->cURL_GET($path);
    }

    // * Obtenemos evidencia de los embalajes despachados
    public function CrearEntradaSalidaZonificacion($zonificacion)
    {
        // [
        //     [
        //         "producto_id"            => "19411",
        //         "cantidad"               => "10",
        //         "responsable_id"         => "23",
        //         "bodega_id"              => 1,
        //         "embalaje_id"            => null,
        //         "nueva_ubicacion_id"     => null,
        //         "antigua_ubicacion_id"   => null,
        //         "glosa"                  => null,
        //         "orden_de_compra"        => null,
        //         "movimiento"             => "item_devueltos_por_nota_de_credito",
        //     ],
        //     [
        //         "producto_id"            => "19411",
        //         "cantidad"               => "10",
        //         "responsable_id"         => "23",
        //         "bodega_id"              => 1,
        //         "embalaje_id"            => null,
        //         "nueva_ubicacion_id"     => null,
        //         "antigua_ubicacion_id"   => null,
        //         "glosa"                  => null,
        //         "orden_de_compra"        => null,
        //         "movimiento"             => "item_devueltos_por_nota_de_credito",
        //     ],
        // ];

        return $this->cURL_POST(
            '/api/v1/zonificacion/crear-entrada-salida',
            $zonificacion
        );
    }

    public function CambiarEstadoAEnTrasladoABodega($body)
    {
        // [
        //     "id" => 12,
        //     "responsable_id_en_traslado_a_bodega" => 12,
        // ]
        return $this->cURL_POST("/api/v1/embalaje/cambiar-estado-a-en-traslado-a-bodega", $body);
    }

    public function RecepcionarEmbalajeTrasladado($body)
    {
        // [
        //     "id" => 12,
        //     "responsable" => 12,
        // ]
        return $this->cURL_POST("/api/v1/embalaje/recepcionar-embalaje-trasladado", $body);
    }
    /**
     * EditarProducto
     *  https://nodrizaspa.postman.co/workspace/API-Nodriza~9880e216-60c2-431c-be9f-72bc4e5551fc/request/2480566-0df6995a-dee3-433d-bc7f-03fea4a80af8
     * @param  mixed $body
     * @return void
     */
    public function EditarProducto($body)
    {

        // "productos": [
        //     {
        //         "id": "39",
        //         "cod_barra": "7804647174604"
        //     }
        // ]

        return $this->cURL_POST("/api/v1/productos/editar-masivo", $body);
    }


    /**
     * obtener_tiempo_preparacion
     * 
     * Obtiene le timepo de preparación (Tiempo que tarda un embaljes desde su creación hasta su finalización)
     *
     * @param  int $id_producto
     * @return mixed
     */
    public function obtener_tiempo_preparacion(int $id_producto)
    {
        return $this->cURL_GET("/api/v1/productos/tiempo-preparacion/" . $id_producto);
    }

    /**
     * obtener_bodegas
     *
     * @return void
     */
    public function obtener_bodegas()
    {
        return $this->cURL_GET("/api/v1/bodegas?activo=1");
    }

    public function obtener_tiempo_preparacion_en_dias()
    {
        return $this->cURL_GET("/api/v1/productos/tiempo-preparacion-todos");
    }

    public function UltimaApk()
    {
        return $this->cURL_GET("/api/v1/versionamiento/ultima-apk");
    }

    // * Abstraccion para hacer peticiones a endpoints por metodo POST
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

    // * Abstraccion para hacer peticiones a endpoints por metodo GET
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
