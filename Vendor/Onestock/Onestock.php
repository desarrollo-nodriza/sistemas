<?php

Class Onestock {
    
	protected $API_DES_ONESTOCK;
    protected $API_PRO_ONESTOCK;

    public function __construct()
    {

        $this->API_PRO_ONESTOCK = "https://onestock.nodriza.cl";
        $this->API_DES_ONESTOCK = "https://dev-onestock.nodriza.cl";
    }

    private function obtenerTokenOnestock()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->API_PRO_ONESTOCK.'/api/v1/clientes/auth',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('email' => 'gpolloni@nodriza.cl','password' => '12345'),
        ));

        $response = json_decode(curl_exec($curl),true);

        curl_close($curl);
        
        return [
            "token"         => $response['respuesta']['token'] ??null,
            "cliente_id"    => $response['cliente']  ['id']    ??null];

    }

    public function obtenerProductoOneStock($producto_id)
    {

        if(!is_int($producto_id))
        {
            return 'Debe enviar un valor númerico entero';
        }

        $credenciales = $this->obtenerTokenOnestock();

        if (!isset($credenciales['token'])) {
            
            return 'Hay problemas con las credenciales para obtener token de Onestock, por favor informar';
        }

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL =>  $this->API_PRO_ONESTOCK.'/api/v1/clientes/'.$credenciales['cliente_id'].'/productos/'.$producto_id.'?token='.$credenciales['token'],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'GET',
        ));

        $response = json_decode(curl_exec($curl),true);
        
        curl_close($curl);
        $hayStock = [];
        if (isset($response['respuesta']['detalle_proveedores'])) {
            foreach ($response['respuesta']['detalle_proveedores'] as $proveedor) {
                $hayStock[]=
                [   'proveedor_id'   => $proveedor['id'],
                    'tipo_stock'            => $proveedor['tipo_stock'],
                    'stock'                 => $proveedor['stock'],
            ];
            }
        }

        return (count($hayStock)>0)?$hayStock:$response;

    }



}