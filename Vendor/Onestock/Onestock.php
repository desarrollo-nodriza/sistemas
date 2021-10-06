<?php
class Onestock
{

    private $API_ONESTOCK;
    private $ID_CLIENTE;
    private $CORREO;
    private $CLAVE;
    private $TOKEN;
    private $REPETICIONES = 0;

    public function __construct($apiurl_onestock, $cliente_id_onestock, $onestock_correo, $onestock_clave, $token_onestock)
    {

        $this->API_ONESTOCK = $apiurl_onestock;
        $this->ID_CLIENTE   = $cliente_id_onestock;
        $this->CORREO       = $onestock_correo;
        $this->CLAVE        = $onestock_clave;
        $this->TOKEN        = $token_onestock;
        $this->REPETICIONES = 0;
    }
    
    /**
     * obtenerTokenOnestock
     * Recupera el token valido
     * @return void
     */
    private function obtenerTokenOnestock()
    {
        $response   = $this->cURL_POST("/api/v1/clientes/auth?email={$this->CORREO}&password={$this->CLAVE}", []);

        if ($response['code'] != 200 && $this->REPETICIONES < 2) {
            $this->REPETICIONES++;
            $this->obtenerTokenOnestock();
        }
        $this->REPETICIONES = 0;
        $this->TOKEN = $response['response']['respuesta']['token'] ?? $this->TOKEN;
        return $this->TOKEN;
    }
    
    /**
     * obtenerProductoOneStock
     * Busca stock de un producto 
     * @param  mixed $producto_id
     * @return void
     */
    public function obtenerProductoOneStock($producto_id)
    {
        $seguir     = true;
        $intentos   = 0;
        while ($seguir) {
            $response = $this->cURL_GET("/api/v1/clientes/{$this->ID_CLIENTE}/productos/{$producto_id}?token={$this->TOKEN}");
            if ($response['code'] != 200 && $intentos < 3) {
                $this->obtenerTokenOnestock();
                $intentos++;
            } else {
                $seguir = false;
            }
        }
        $response['token']=$this->TOKEN;
        return $response;
    }
    
    /**
     * obtenerProductosClienteSinPaginacionOneStock
     * Se utiliza endpoint de Onestock que trae todo los productos sin paginacion
     * @return void
     */
    public function obtenerProductosClienteSinPaginacionOneStock()
    {
        $seguir     = true;
        $intentos   = 0;
        while ($seguir) {
            $response = $this->cURL_GET("/api/v1/clientes/{$this->ID_CLIENTE}/v2/productos?token={$this->TOKEN}");
            if ($response['code'] != 200 && $intentos < 3) {
                $this->obtenerTokenOnestock();
                $intentos++;
            } else {
                $seguir = false;
            }
        }
        $response['token']=$this->TOKEN;
        return $response;
    }

    
    /**
     * cURL_POST
     *
     * @param  mixed $URL
     * @param  mixed $POSTFIELDS
     * @return void
     */
    private function cURL_POST($URL, $POSTFIELDS)
    {
        set_time_limit(0);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL =>  $this->API_ONESTOCK . $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 1000,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($POSTFIELDS, true),
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);
        return [
            "code"          => $http_code,
            "response"      => json_decode($response, true),
            "curl_error"    => $curl_error
        ];
    }

    
    /**
     * cURL_GET
     *
     * @param  mixed $URL
     * @return void
     */
    private function cURL_GET($URL)
    {
        set_time_limit(0);
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->API_ONESTOCK . $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_SSL_VERIFYHOST => 0,
            CURLOPT_SSL_VERIFYPEER => 0
        ));

        $response   = curl_exec($curl);
        $http_code  = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $curl_error = curl_error($curl);
        curl_close($curl);

        return [
            "code"          => $http_code,
            "response"      => json_decode($response, true),
            "curl_error"    => $curl_error,
        ];
    }
}
