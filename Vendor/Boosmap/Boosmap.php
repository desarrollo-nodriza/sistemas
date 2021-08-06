<?php

Class Boosmap {
	
	/**
	 * @version 1.1.0
	 */
    const VERSION  = "1.1.0";

    /**
     * @var $API_ROOT_URL is a main URL to access the Boosmap API's.
     */
	protected static $API_ROOT_URL = "https://api.boosmap.io";
    protected static $API_DEV_ROOT_URL = "https://api-uat.boosmap.io";


	/**
	 * @var API_TOKEN for access to resources
	 */
    protected static $API_TOKEN;
    

    /**
     * Tipo de servicio.
     * @var array
     */
    public static $SERVICE = array(
    	'Nextday' => 'Nextday',
		'Express'   => 'Express',
        'Sameday' => 'Sameday',
        'NextdayWithWindow' => 'NextdayWithWindow',
        'SamedayWithWindow' => 'SamedayWithWindow'
    );

    public  static $STATES = array(
		'ingresado' => array(
			'nombre' => 'Ingresado',
			'leyenda' => 'El pedido fue creado en el sistema',
			'tipo' => 'inicial'
		),
		'asignacion_aceptada' => array(
			'nombre' => 'Asignación aceptada',
			'leyenda' => 'El repartido está llegando a nuestra bodega',
			'tipo' => 'sin_especificar'
		),
		'en_punto_de_retiro' => array(
			'nombre' => 'En punto de retiro',
			'leyenda' => 'El repartido llegó a nuestra bodega',
			'tipo' => 'sin_especificar'
		),
        'en_camino_entrega' => array(
			'nombre' => 'En Camino Entrega',
			'leyenda' => 'El repartidos va camino a destino con el producto',
			'tipo' => 'en_reparto'
		),
		'en_despacho' => array(
			'nombre' => 'En despacho',
			'leyenda' => 'El pedido está en reparto',
			'tipo' => 'en_reparto'
		),
		'entregado' => array(
			'nombre' => 'Entregado',
			'leyenda' => 'El pedido fue entregado al destinatario',
			'tipo' => 'entregado'
		),
		'sin_moradores' => array(
			'nombre' => 'Sin moradores',
			'leyenda' => 'El repartidor no encontró a nadie en el domicilio',
			'tipo' => 'error'
		),
        'pre_recepcion_virtual' => array(
			'nombre' => 'Pre recepción virtual',
			'leyenda' => 'No informado',
			'tipo' => 'sin_especificar'
		),
        'retirado' => array(
			'nombre' => 'Retirado de nuestra bodega',
			'leyenda' => 'El repartidor se llevó el pedido desde nuestra bodega',
			'tipo' => 'sin_especificar'
		),
        'devolucion_exitosa' => array(
			'nombre' => 'Devuelto a bodega',
			'leyenda' => 'El pedido volvió a nuestra bodega pero no te preocupes. Buscaremos una solución.',
			'tipo' => 'error'
		),
        'devolucion_al_cliente' => array(
			'nombre' => 'Devuelto a cliente',
			'leyenda' => 'El pedido será devuelvo al cliente.',
			'tipo' => 'sin_especificar'
		),
        'retiro_cd_cliente' => array(
			'nombre' => 'Retiro CD cliente',
			'leyenda' => '',
			'tipo' => 'sin_especificar'
		),
        'recepcion_en_bodega' => array(
			'nombre' => 'Recepción en bodega',
			'leyenda' => '',
			'tipo' => 'sin_especificar'
		),
        'pedido_anulado_cliente' => array(
			'nombre' => 'Pedido anulado por cliente',
			'leyenda' => 'Se ha anulado el envio',
			'tipo' => 'error'
		),
		'rechazado_cliente' => array(
			'nombre' => 'Pedido rechazado por cliente',
			'leyenda' => 'El destinatario rechazó el pedido',
			'tipo' => 'error'
		),
		'error_direccion' => array(
			'nombre' => 'Error con la dirección',
			'leyenda' => 'No pudimos dar con la dirección proporcionada',
			'tipo' => 'error'
		),
		'extraviado_en_bodega' => array(
			'nombre' => 'Pedido extraviado',
			'leyenda' => 'Lo sentimos pero el pedido se extravió en el camino a destino. Nos podremos en contacto con usted lo antes posible.',
			'tipo' => 'error'
		),
		'pedido_anulado' => array(
			'nombre' => 'Pedido anulado',
			'leyenda' => 'El pedido fue anulado',
			'tipo' => 'error'
		),
		'cancelado' => array(
			'nombre' => 'Pedido cancelado',
			'leyenda' => 'El pedido fue cancelado',
			'tipo' => 'error'
        ),
        'recepcion_log_inversa' => array(
			'nombre' => 'Recepcion Log inversa',
			'leyenda' => 'Re intento de entrega luego de un intento de entrega a cliente',
			'tipo' => 'sin_especificar'
		),
        'almacenaje' => array(
			'nombre' => 'Almacenaje',
			'leyenda' => '',
			'tipo' => 'sin_especificar'
		)
	);

    public static $PICKUPS = array();

     /**
     * Configuration for CURL
     */
    public static $CURL_OPTS = array(
        CURLOPT_USERAGENT      => "NODRIZA-SPA", 
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1,
        CURLOPT_ENCODING       => "",
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_FOLLOWLOCATION => 0,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_TIMEOUT        => 0
    );


    /**
     * Iniciar clase Boosmap
     *
     * @param string $apitoken
     */
    public function __construct($apitoken = '', $dev = false) 
    {   
        self::$API_TOKEN  = $apitoken;
        
        if ($dev)
        {
            $this->useDevEnviroment();
        }
        
    }

    public function useDevEnviroment()
    {
    	self::$API_ROOT_URL = self::$API_DEV_ROOT_URL;
    }


    public function useProdEnviroment()
    {   
        self::$API_ROOT_URL = self::$API_ROOT_URL;
    }


    public function getToken($user, $pass)
    {   

        $data = array(
            'email' => $user,
            'password' => $pass
        );

        return $this->post('/login/', $data);
    }


    /**
     * Create a orden transport in Boosmap
     * @param  Array $data array of params
     * @return mixed
     */
    public function createOt($data)
    {   
    	return $this->post('/order/', $data);
    }

    /**
     * Obtiene la información de una OT
     * 
     * @param int $id_ot  Identificador de la OT en boosmap
     */
    public function getOT($id_ot)
    {   
        return $this->get('/order/' . rawurlencode($id_ot));
    }


    /**
     * Obtiene los distritos de boosmap (comunas de alcance)
     */
    public function getDistrict()
    {   
        return $this->get('/district');
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
            'Authorization: Bearer ' . self::$API_TOKEN
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