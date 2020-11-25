<?php

Class Conexxion {
	
	/**
	 * @version 1.1.0
	 */
    const VERSION  = "1.1.0";

    /**
     * @var $API_ROOT_URL is a main URL to access the Conexxion API's.
     */
	protected static $API_ROOT_URL = "https://api.ecommerce.conexxion.cl/wo/";

	/**
	 * @var AUTH_TYPE is mandatory
	 */
	protected static $AUTH_TYPE    = 'external';

	/**
	 * @var API_KEY for access to resources
	 */
    protected static $API_KEY;


    /**
     * Especifica si el producto es con o sin retorno.
     * @var array
     */
    public static $HAS_RETURN = array(
		'with_return' => 'Con retorno',
		'no_return'   => 'Sin retorno'
    );


    /**
     * Tipo producto
     * @var array
     */
    public static $PRODUCT = array(
    	1 => 'Cartas',
    	2 => 'Cajas'
    );


    /**
     * Tipo de servicio.
     * @var array
     */
    public static $SERVICE = array(
    	1 => 'Entrega 48 Horas',
    	2 => 'Dia Habil Siguiente',
    	3 => 'Max. 4 Horas' 
    );


    /**
     * Tipo de notificación
     * @var array
     */
    public static $NOTIFICATION_TYPE = array(
		'none'     => 'No notificar',
		'sender'   => 'Notificar al emisor',
		'receiver' => 'Notificar al destinatario',
		'both'     => 'Notificar a emisor y destinatario',
    );


     /**
     * Configuration for CURL
     */
    public static $CURL_OPTS = array(
        CURLOPT_USERAGENT      => "CONEXXION-PHP-SDK-1.1.0", 
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_ENCODING       => "",
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_TIMEOUT        => 0
    );



    /**
     * Constructor method. Set all variables to connect in Conexxion
     *
     * @param string $apikey
     */
    public function __construct($apikey = '', $dev = false) {
        self::$API_KEY  = $apikey;

        if ($dev) {
        	self::setDevEnviroment();
        }

    }

    /**
     * Use dev host
     */
    private function setDevEnviroment()
    {
    	self::$API_ROOT_URL = "http://api.conexxion.digitechqa.cl";
    }


    /**
     * Create a orden transport in Conexxion
     * @param  Array $data array of params
     * @return mixed
     */
    public function createOt($data)
    {
    	return $this->post('/wo/', $data);
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
    public function execute($path, $opts = array(), $params = array(), $assoc = true) {
        
        $opts[CURLOPT_HTTPHEADER] = array(
            'Content-Type: application/json',
            'X-Api-Key: ' . self::$API_KEY,
            'X-Auth-Type: ' . self::$AUTH_TYPE,
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
            $uri = self::$API_ROOT_URL.$path;
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