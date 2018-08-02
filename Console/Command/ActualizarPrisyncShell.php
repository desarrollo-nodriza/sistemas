<?

class ActualizarPrisyncShell extends AppShell
{	

	/**
     * @var $API_ROOT_URL es la url base del la API de Prisync
     */
	protected static $API_ROOT_URL     = "https://prisync.com";

	/**
     * Configuration for CURL
     */
    public static $CURL_OPTS = array(
        CURLOPT_SSL_VERIFYPEER => false,
        CURLOPT_CONNECTTIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_TIMEOUT => 60
    );

    protected $api_key = '';
    protected $api_token = '';

	public function main() {

		ini_set('max_execution_time', 0);

        $errors = array();
		$productos = array();
		$rutaCsv = $_SERVER['DOCUMENT_ROOT'] . 'webroot/id_prisync.csv';
		$i = 0;
		$countProductos = 0;

		$time_start = microtime(true); 

		try {

			$file = fopen($rutaCsv,"r");
			while(! feof($file)) {
				$fila      = fgetcsv($file, 0, ';');
				$idPrisync = $fila[0];
				$newCode   = $fila[1];
				$postData = array();

				$productoPrisync = $this->obtenerProductoPorId($idPrisync);

				if (isset($productoPrisync['id'])) {
					
					$url                      = '/api/v2/edit/product/id/'.$productoPrisync['id'];
					$postData['name']         = $productoPrisync['name'];
					$postData['brand']        = $productoPrisync['brand']['name'];
					$postData['category']     = $productoPrisync['category']['name'];
					$postData['product_code'] = $newCode;
					$postData['cost']         = $productoPrisync['product_cost'];
					
					$result = $this->actualizarProducto($url, $postData);

					if ($result['result']) {
						$this->out($postData['name'] . ' Actualizado con éxito.');
						$this->out('Código anterior: ' . $productoPrisync['product_code']);
						$this->out('Código nuevo: ' . $newCode);
						$this->hr();
					}else{
						$this->out($postData['name'] . 'No se pudo actualizar.');
						$this->hr();
					}
					
				}

				
			}
			fclose($file);
		

		} catch (Exception $e) {
			$errors['Productos'][] = $e->getMessage();
		}


		$time_end = microtime(true);

		$execution_time = ($time_end - $time_start)/60;

		$this->hr();
		$this->out('Tiempo de ejecución: ' . $execution_time);
		$this->hr();


		exit;
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
				"apikey: " . $this->api_key,
				"apitoken: " . $this->api_token,
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
				"apikey: " . $this->api_key,
				"apitoken: " . $this->api_token,
				"Content-Type: multipart/form-data"
			),
            CURLOPT_POST => 1, 
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
            CURLOPT_HTTPHEADER => array(
				"apikey: " . $this->api_key,
				"apitoken: " . $this->api_token,
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
				"apikey: " . $this->api_key,
				"apitoken: " . $this->api_token,
			)
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
				"apikey: " . $this->api_key,
				"apitoken: " . $this->api_token,
			)
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



    public function autenticacion()
	{	
		$activo = 1;

		if (!$activo) {
			return false;
		}

		$this->api_key   = 'mduarte@nodriza.cl';
		$this->api_token = '02bd9fb7c6aa50f4b70e0e8dcde03673';
	
	}


	public function actualizarProducto($url, $data = array())
	{
		if (!empty($url) && !empty($data)) {
			$this->autenticacion();

			$result = $this->post($url, $data);

			if ($result['httpCode'] >= 300) {
				throw new Exception( sprintf('%s. Código de error: %d', $result['body']->error, $result['body']->errorCode));
				return;
			}else{
				return to_array($result['body']);
			}
		}
	}



	public function obtenerProductos($url = '/api/v2/list/product/startFrom/0')
	{	
		$this->autenticacion();

		$productos = $this->get($url);
		
		if ($productos['httpCode'] >= 300) {
			throw new Exception( sprintf('%s. Código de error: %d', $productos['body']->error, $productos['body']->errorCode));
			return;
		}else{
			return to_array($productos['body']);
		}
	}


	public function obtenerProductoPorId($id = '')
	{	
		if (!empty($id)) {

			$this->autenticacion();
			
			$url = '/api/v2/get/product/id/' . $id;
			$producto = $this->get($url);
			
			if ($producto['httpCode'] >= 300) {
				throw new Exception( sprintf('%s. Código de error: %d', $producto['body']->error, $producto['body']->errorCode));
				return;
			}else{
				return to_array($producto['body']);
			}
		}
	}


	public function obtenerCompetidoresPorProducto($id = '')
	{
		if (!empty($id)) {

			$this->autenticacion();

			$url  = '/api/v2/get/url/id/' . $id;
			
			$urls = $this->get($url);
			
			if ($urls['httpCode'] >= 300) {
				throw new Exception( sprintf('%s. Código de error: %d', $urls['body']->error, $urls['body']->errorCode));
				return;
			}else{
				return to_array($urls['body']);
			}

		}
	}
}