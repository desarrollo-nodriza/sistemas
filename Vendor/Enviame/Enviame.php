<?php

class Enviame {

	/**
	 * @version 1.1.0
	 */
    const VERSION  = "1.1.0";

    /**
     * @var $API_ROOT_URL is a main URL to access the Enviame API's.
     */
    protected static $API_ROOT_URL = "https://stage.api.enviame.io/api";

    /**
     * Configuration for CURL
     */
    public static $CURL_OPTS = array(
        CURLOPT_USERAGENT      => "ENVIAME-PHP-SDK-1.1.0", 
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_ENCODING       => "",
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_TIMEOUT        => 0
    );

    protected $apikey;
    protected $company;
    protected $host;


    /**
     * Constructor method. Set all variables to connect in Enviame
     *
     * @param string $apikey
     * @param string $company
     * @param string $host
     */
    public function __construct($apikey, $company, $host = "https://stage.api.enviame.io/api") {
        $this->apikey  = $apikey;
        $this->company = $company;
        $this->host    = $host;
    }


    /**
     *  GET
     */


    /**
     * Listar bodegas de la empresa
     * Ref: https://documenter.getpostman.com/view/4240163/apis-stage/RW1dGeAa?version=latest#19c0d094-dea9-3990-2d62-29963c3177bd
     * @return mixed
     */
    public function ver_bodegas()
    {
        return $this->get('/s1/v1/companies/' . $this->company . '/warehouses')['body'];
    }


    /**
     * Ver bodega de la empresa
     * Ref: https://documenter.getpostman.com/view/4240163/apis-stage/RW1dGeAa?version=latest#49f94360-b015-4259-9d4a-ee4cc2e2561a
     * @return mixed
     */
    public function ver_bodega($id_bodega)
    {
        return $this->get('/s1/v1/warehouses/' . $id_bodega);
    }


    /**
     * Obtiene un envio creado
     * Ref: https://documenter.getpostman.com/view/4240163/apis-stage/RW1dGeAa?version=latest#e1dddae3-aee9-11dd-2b3e-8d103978fe82
     * @param  int  $id_envio Idetificador de la venta
     * @return mixed
     */
    public function ver_envio($id_envio)
    {
        return $this->get('/s2/v2/deliveries/' . $id_envio);
    }


    /**
     * Obtiene el número de seguimiento de un envio
     * Ref: https://documenter.getpostman.com/view/4240163/apis-stage/RW1dGeAa?version=latest#6774f695-7d42-167f-3b77-9d4a2ab5975c
     * @param  int  $id_envio   Idetificador de la venta
     * @return mixed
     */
    public function ver_numero_seguimiento($id_envio)
    {
        return $this->get('/s2/v2/deliveries/' . $id_envio . '/tracking');
    }


    /**
     * Obtiene la etiqueta del envio
     * Ref: https://documenter.getpostman.com/view/4240163/apis-stage/RW1dGeAa?version=latest#38c63552-b6f0-6b04-e01d-038ea5f39bf9
     * @param  int    $id_envio Idetificador de la venta
     * @param  string $tipo     tipo de documento
     * @return document
     */
    public function ver_etiqueta($id_envio, $tipo = 'pdf')
    {
        return $this->get('/s2/deliveries/' . $id_envio . '/label', array('type' => $tipo));
    }


    /**
     * Listar los retiros creados
     * Ref: https://documenter.getpostman.com/view/4240163/apis-stage/RW1dGeAa?version=latest#79a78e93-348f-63a7-63f1-f36348f803ba
     * @return mixed
     */
    public function ver_retiros_empresa()
    {
        return $this->get('/s2/v2/companies/' . $this->company . '/pickups');
    }



    /**
     *  POST
     */
    

    /**
     * Ref: https://documenter.getpostman.com/view/4240163/apis-stage/RW1dGeAa?version=latest#f083be47-52a0-0e2e-5cdc-e346f39a30f6
     * @param  array  $orden        información de la orden
     * @param  array  $destino      información del destino
     * @param  array  $origen       información del origen
     * @param  array  $transporte              
     * @return mixed  Resultado de la operación
     */
    public function crear_envio_como_empresa($orden = array(), $destino = array(), $origen = array(), $transporte = array())
    {
        $data_to_send = array(
            'shipping_order'       => $orden,
            'shipping_destination' => $destino,
            'shipping_origin'      => $origen,
            'carrier'              => $transporte
        );
        
        return $this->post('/s2/v2/companies/'.$this->company.'/deliveries', $data_to_send);
    }


    /**
     * Generar etiquetas de envios existentes
     * Ref: https://documenter.getpostman.com/view/4240163/apis-stage/RW1dGeAa?version=latest#683fbf29-765d-5db1-67ea-f011ee08ba9b
     * @param  array   $envios               Lista con los id de las ventas
     * @param  string  $tipo                 tipo de documento
     * @param  integer $etiquetas_por_pagina cantidad de etiquetas por oágina del pdf
     * @return mixed
     */
    public function generar_etiquetas($envios, $tipo = 'pdf', $etiquetas_por_pagina = 1)
    {
        $data_to_send = array(
            'deliveries'    => $envios,
            'type'          => $tipo,
            'labelsPerPage' => $etiquetas_por_pagina
        );

        return $this->post('/s2/v2/companies/'.$this->company.'/labels', $data_to_send);
    }


    /**
     * Generar etiquetas de envios existentes
     * Ref: https://documenter.getpostman.com/view/4240163/apis-stage/RW1dGeAa?version=latest#7413bf91-2935-3153-2328-dd27c36e8d41
     * @param  array   $envios               Lista con los id de las ventas
     * @return mixed
     */
    public function generar_manifiestos($envios)
    {
        $data_to_send = array(
            'deliveries'    => $envios
        );

        return $this->post('/s2/v2/companies/'.$this->company.'/summary', $data_to_send);
    }


    /**
     * Solicita retiro de los bultos al transportista indicado.
     * Ref: https://documenter.getpostman.com/view/4240163/apis-stage/RW1dGeAa?version=latest#21a54b85-67bb-8371-aeba-db9380d3b247
     * @param  string     $cod_transportista  Código del transprtista
     * @param  string     $cod_bodega         Código de la bodega
     * @param  int        $cantidad           N° de bultos
     * @param  string     $nombre_contacto    Nombre del contacto interno
     * @param  string     $fono_contacto      Fono del contacto interno
     * @param  string     $ampm               AM o PM
     * @param  string     $fecha_retiro       2018-05-12
     * @return mixed
     */
    public function crear_retiro_como_empresa($cod_transportista, $cod_bodega, $cantidad, $nombre_contacto, $fono_contacto, $ampm, $fecha_retiro = '')
    {
        $data_to_send = array(
            'carrier_code' => $cod_transportista,
            'warehouse_code' => $cod_bodega,
            'qty' => $cantidad,
            'contact_name' => $nombre_contacto,
            'contact_phone' => $fono_contacto,
            'range_time' => $ampm
        );

        if (!empty($fecha_retiro)) {
            $data_to_send = array_replace_recursive($data_to_send, array('pickup_date' => $fecha_retiro));
        }

        return $this->post('/s2/v2/companies/'.$this->company.'/pickups', $data_to_send);
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
        $exec = $this->execute($path, null, $params, $assoc);

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
            CURLOPT_POST => true, 
            CURLOPT_POSTFIELDS => $body
        );
        
        $exec = $this->execute($path, $opts, $params);

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
            CURLOPT_CUSTOMREQUEST => "DELETE"
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
            CURLOPT_CUSTOMREQUEST => "OPTIONS"
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
        
        $opts[CURLOPT_HTTPHEADER] = array(
            'Content-Type: application/json',
            'api-key: ' . $this->apikey,
            'Accept: application/json'
        );

        $uri = $this->make_path($path, $params);
        
        $ch = curl_init($uri);
        curl_setopt_array($ch, self::$CURL_OPTS);

        if(!empty($opts))
            curl_setopt_array($ch, $opts);

        $return["body"] = json_decode(curl_exec($ch), $assoc);
        $return["httpCode"] = curl_getinfo($ch, CURLINFO_HTTP_CODE);

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
            $uri = $this->host.$path;
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
}
