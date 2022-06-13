<?php

class LibreDTEAPI
{
    protected $API_ROOT_URL = 'https://api.libredte.cl/api/v1';
    protected $BX_TOKEN;
    protected $cert_auth, $pass_auth, $cert;

    /**
     * Configuration for CURL
     */
    public static $CURL_OPTS = array(
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_TIMEOUT => 60
    );

    
    public function __construct($BX_TOKEN, $cert_auth = [], $pass_auth = [])
    {
        $this->BX_TOKEN = $BX_TOKEN ?? '';
        
        # Definimos información del certificado digital para el SII
        if ($cert_auth)
        {
            $this->cert_auth = $cert_auth;
        }

        # Definimos información de usuario y clave del SII
        if ($pass_auth)
        {
            $this->pass_auth = $pass_auth;
        }

    }


    /**
     * Obtiene los documentos de compra según periodo
     * 
     * Ref: https://documenter.getpostman.com/view/5911929/SWLiYkK9#f847aa91-47f7-42a4-a380-05ab5bdb5fff
     *
     * @param  mixed $receptor  Rut del receptor con dv y guión
     * @param  mixed $periodo   Año y mes del periodo a consultar ej: 202201
     * @param  mixed $tipo_dte  Por defecto se usa tipo 33 facturas
     * @param  mixed $estado    Estado de los dtes a consultar
     * @param  array $params    Parámetros adicionales para la query
     * @return array
     */
    public function obtenerDocumentosCompras($receptor, $periodo, $tipo_dte = 33, $estado, $params = [])
    {   
        # Requerido para autenticarse en el sii
        $body = [
            "auth" => [
                "pass" => [
                    "rut" => $this->pass_auth['rut'],
                    "clave" => $this->pass_auth['clave']
                ]
            ]
        ];

        return $this->post("/sii/rcv/compras/detalle/{$receptor}/{$periodo}/{$tipo_dte}/{$estado}", $body, $params);
    }


    
    /**
     * Cambia el estado del registro de uno o varios documentos de compra
     * 
     * Ref: https://documenter.getpostman.com/view/5911929/SWLiYkK9#f067ee1c-20f9-4370-aa18-e2922deadab9
     *
     * @param  array $docs  Documentos para registrar respuesta  
     * @param  array $params ver doc
     * @return array
     */
    public function cambiarEstadoDteCompra($docs = [], $params = [])
    {   
        # Se envian de a 10 documentos
        $iteraciones = array_chunk($docs, 10);

        $results = [];
        
        foreach($iteraciones as $dtes)
        {
             # Requerido para autenticarse en el sii
            $body = [
                "auth" => [
                    "cert" => [
                        "cert-data" => $this->cert_auth['cert'],
                        "pkey-data" => $this->cert_auth['pkey']
                    ]
                ],
                "documentos" => $dtes
            ];

            $results[] = $this->post("/libredte/dte/intercambios/respuesta_sii", $body, $params);
        }

        return $results;

    }


    /**
     * Execute a GET Request
     * 
     * @param string $path
     * @param array $params
     * @param boolean $assoc
     * @return mixed
     */
    public function get($path, $params = null, $assoc = false) {
    	$opts = array(
    		CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer {$this->BX_TOKEN}",
				"Content-Type: application/json",
                "Accept: application/json"
			),
			CURLOPT_CUSTOMREQUEST => "GET"
    	);
        $exec = $this->execute($path, $opts, $params, $assoc);

        return $exec;
    }

    
    /**
     * Execute a POST Request
     * 
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function post($path, $body = null, $params = array()) {
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer {$this->BX_TOKEN}",
				"Content-Type: application/json",
                "Accept: application/json"
			),
            CURLOPT_POST => true, 
            CURLOPT_POSTFIELDS => $body
        );
        
        $exec = $this->execute($path, $opts, $params, true);

        return $exec;
    }


    /**
     * Execute a PUT Request
     * 
     * @param string $path
     * @param string $body
     * @param array $params
     * @return mixed
     */
    public function put($path, $body = null, $params = array()) {
        $body = json_encode($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer {$this->BX_TOKEN}",
				"Content-Type: application/json",
                "Accept: application/json"
			),
            CURLOPT_CUSTOMREQUEST => "PUT",
            CURLOPT_POSTFIELDS => $body
        );
        
        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute a DELETE Request
     * 
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function delete($path, $params) {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "DELETE",
            CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer {$this->BX_TOKEN}",
				"Content-Type: application/json",
                "Accept: application/json"
			),
        );
        
        $exec = $this->execute($path, $opts, $params);
        
        return $exec;
    }

    /**
     * Execute a OPTION Request
     * 
     * @param string $path
     * @param array $params
     * @return mixed
     */
    public function options($path, $params = null) {
        $opts = array(
            CURLOPT_CUSTOMREQUEST => "OPTIONS",
            CURLOPT_HTTPHEADER => array(
				"Authorization: Bearer {$this->BX_TOKEN}",
				"Content-Type: application/json",
                "Accept: application/json"
			),
        );
        
        $exec = $this->execute($path, $opts, $params);

        return $exec;
    }

    /**
     * Execute all requests and returns the json body and headers
     * 
     * @param string $path
     * @param array $opts
     * @param array $params
     * @param boolean $assoc
     * @return mixed
     */
    public function execute($path, $opts = array(), $params = array(), $assoc = false) {
        $uri = $this->make_path($path, $params);
        
        $ch = curl_init($uri);
        curl_setopt_array($ch, self::$CURL_OPTS);

        if(!empty($opts))
            curl_setopt_array($ch, $opts);

        $return["body"] = json_decode(curl_exec($ch), $assoc);
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $return["error"] = curl_error($ch);

        curl_close($ch);
        
        return $return;
    }

    /**
     * Check and construct an real URL to make request
     * 
     * @param string $path
     * @param array $params
     * @return string
     */
    public function make_path($path, $params = array()) {
        if (!preg_match("/^http/", $path)) {
            if (!preg_match("/^\//", $path)) {
                $path = '/'.$path;
            }
            $uri = $this->API_ROOT_URL.$path;
        } else {
            $uri = $path;
        }

        if(!empty($params)) {
            $paramsJoined = array();

            foreach($params as $param => $value) {
               $paramsJoined[] = "$param=$value";
            }
            $params = '?'.implode('&', $paramsJoined);
            $uri = $uri.$params;
        }

        return $uri;
    }

    // public function AceptarFactura($rut = "76381142-5", $clave = "ToolMania9", $TipoDTE = 33, $Folio, $FchEmis, $RUTEmisor, $RUTRecep, $MntTotal)
    // {
    //     $data = [
    //         "auth" => [
    //             "pass" => [
    //                 "rut" => "",
    //                 "clave" => ""
    //             ]
    //         ],
    //         "documentos" => [
    //             [
    //                 "TipoDTE"        => 33,
    //                 "Folio"          => 1,
    //                 "FchEmis"        => "2020-03-10",
    //                 "RUTEmisor"      => "96806980-2",
    //                 "RUTRecep"       => "76192083-9",
    //                 "MntTotal"       => 10000,
    //                 "EstadoRecepDTE" => "ERM",
    //                 "RecepDTEGlosa"  => "Ok"
    //             ]
    //         ]
    //     ];

    //     $this->ApiLibreDTE->consume('/libredte/dte/intercambios/respuesta_sii?certificacion=0');
    // }

    public function EstadoDeFolio($rut = "76381142-5", $clave = "ToolMania9", $emisor = 77000087, $dte = 33, $folio = 55448663)
    {
        $data = [
            "auth" => [
                "pass" => [
                    "rut"   => $rut,
                    "clave" => $clave
                ]
            ]
        ];

        return   $this->cURL_POST("/sii/dte/caf/estado/{$emisor}/{$dte}/{$folio}?certificacion=0&formato=json", $data);
    }


    public function VerificacionAvanzadaDocumentoSII($emisor = 77000087, $receptor = 76381142, $dte = 33, $folio = 55448663, $fecha = "2022-01-21", $total = 159110)
    {

        $data = [
            "auth" => [
                "pass" => [
                    "rut"   => $this->pass_auth['rut'],
                    "clave" => $this->pass_auth['clave']
                ]
            ],
            "dte" => [
                "emisor"   => $emisor,
                "receptor" => $receptor,
                "dte"      => $dte,
                "folio"    => $folio,
                "fecha"    => $total,
                "total"    => null,
                "firma"    => null
            ]
        ];

        return  $this->cURL_POST("/sii/dte/emitidos/verificar?certificacion=0", $data);
    }

    public function EstadoEnvioXMLSII($rut = "76381142-5", $clave = "ToolMania9", $track_id, $emisor)
    {

        $data = [
            "auth" => [
                "pass" => [
                    "rut"   => $rut,
                    "clave" => $clave
                ]
            ]
        ];
      
        return $this->cURL_POST("/sii/dte/emitidos/estado_envio/{$emisor}/{$track_id}?certificacion=0&formato=json", $data);
    }

    // TODO Abstraccion para hacer peticiones a endpoints por metodo POST
    private function cURL_POST($URL, $POSTFIELDS)
    {

        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL             => $this->API_ROOT_URL . $URL,
            CURLOPT_RETURNTRANSFER  => true,
            CURLOPT_ENCODING        => '',
            CURLOPT_MAXREDIRS       => 10,
            CURLOPT_TIMEOUT         => 0,
            CURLOPT_FOLLOWLOCATION  => true,
            CURLOPT_HTTP_VERSION    => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST   => 'POST',
            CURLOPT_SSL_VERIFYHOST  => 0,
            CURLOPT_SSL_VERIFYPEER  => 0,
            CURLOPT_HEADER          => false,
            CURLOPT_POSTFIELDS      => json_encode($POSTFIELDS, true),
            CURLOPT_HTTPHEADER      => array(
                'Content-Type: application/json',
                'Authorization: Bearer ' . $this->BX_TOKEN,
                'Accept: application/json',
            ),
        ));

        $response   = curl_exec($curl);
        $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);
        return [
            "code"        => $http_code,
            "request"     => $POSTFIELDS,
            "response"    => json_decode($response, true),
            "curl_error"  => $curl_error,
            'url'         => $this->API_ROOT_URL . $URL
        ];
    }

    // TODO Abstraccion para hacer peticiones a endpoints por metodo GET
    private function cURL_GET($URL)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->API_ROOT_URL . $URL,
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
                'Authorization: Bearer ' . $this->BX_TOKEN
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
            'url'        => $this->API_ROOT_URL . $URL
        ];
    }
}
