<?php
App::uses('Component', 'Controller');

class ToolmaniaComponent extends Component
{	
	public static $api_url    = '';

	/**
     * Configuration for CURL
     */
    private static $CURL_OPTS = array(
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_TIMEOUT => 60
    );


    /**
     * Retorna los detalles de una compra realizada a traves de webay
     * @param  string $id    Id externo o id de venta
     * @param  string $token Tkena de acceso obtenido desde prestashop
     * @return array
     */
	public function obtenerWebpayInfo($id = '', $token = '')
	{	
		$response = array(
			'code' => 200,
			'message' => 'Datos obtenidos',
			'content' => array()
		);

		if (!empty($id) && !empty($token)) {

			$params = array(
				'id'    => (int) $id,
				'token' => $token
			);

			try {
				$get      = $this->get('/module/webpay/api', $params);
				$response = $get['body'];
			} catch (Exception $e) {
				$response['code']    = $e->getCode();
				$response['message'] = $e->getMessage();
			}
			
		}else{
			$response['code'] = 300;
			$response['message'] = 'id o token vacio';
		}

		return to_array($response);
	}


    /**
     * Retorna el tipo de documento que solicitia el cliente en su compra. (La tienda debe tener habilitado el módulo boleta/factura)
     * @param  string $id       ID externo o id de la venta
     * @param  string $customer Id del cliente
     * @param  string $token    Token de acceso obtenido desde prestashop
     * @return array
     */
	public function obtenerDocumento($id = '', $customer = '', $token = '')
	{	
		$response = array(
			'code' => 200,
			'message' => 'Datos obtenidos',
			'content' => array()
		);

		if (!empty($id) && !empty($token)) {

			$params = array(
				'id'       => (int) $id,
				'token'    => $token,
				'order'    => 'DESC',
				'limit' => 1
			);

            if (!empty($customer)) {
                $params = array_replace_recursive($params, array(
                    'customer' => (int) $customer
                ));
            }

			try {
				$get      = $this->get('/module/customorder/api', $params);
				$response = $get['body'];
			} catch (Exception $e) {
				$response['code']    = $e->getCode();
				$response['message'] = $e->getMessage();
			}
			
		}else{
			$response['code'] = 300;
			$response['message'] = 'id o token vacio';
		}

		return to_array($response);
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
            $uri = self::$api_url.$path;
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