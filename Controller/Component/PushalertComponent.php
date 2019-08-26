<?
App::uses('Component', 'Controller');

class PushalertComponent extends Component
{	
	public static $api_key = '';
	public static $api_host = 'https://api.pushalert.co/rest/v1';

	/**
     * Configuration for CURL
     */
    public static $CURL_OPTS = array(
        CURLOPT_USERAGENT => "SistemaNodriza.0.1", 
        CURLOPT_SSL_VERIFYPEER => true,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_TIMEOUT => 60
    );

    
	public function enviarNotificacion($titulo, $mensaje, $url, $icono = '')
	{
		$post_vars = array(
			"title"   => $titulo,
			"message" => $mensaje,
			"url"     => $url
		);

		if (!empty($icono)) {
			$post_vars = array_replace_recursive($post_vars, array('icon' => $icono));
		}

		$output = $this->post('/send', $post_vars);
		
		if ($output['httpCode'] == 200) {
			return true;
		}else{
			return false;
		}
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
            CURLOPT_HTTPHEADER => array("Authorization: api_key=".self::$api_key)
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
        $body = http_build_query($body);
        $opts = array(
            CURLOPT_HTTPHEADER => array("Authorization: api_key=".self::$api_key),
            CURLOPT_POST => true, 
            CURLOPT_POSTFIELDS => $body
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

        curl_close($ch);
        
        return json_decode(json_encode($return), true);
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
            $uri = self::$api_host.$path;
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