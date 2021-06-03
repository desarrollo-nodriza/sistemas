<?php

Class Onestock {
    
	protected $API_DES_ONESTOCK;
    protected $API_PRO_ONESTOCK;
    protected $TOKEN;

    public function __construct()
    {

        $this->API_PRO_ONESTOCK = "https://onestock.nodriza.cl";
        $this->API_DES_ONESTOCK = "https://dev-onestock.nodriza.cl";
        $this->TOKEN = $this->obtenerTokenOnestock();
    }

    private function obtenerTokenOnestock()
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => $this->API_PRO_ONESTOCK.'/api/v1/clientes/auth',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 1000,
        CURLOPT_CONNECTTIMEOUT => 0,    
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS => array('email' => 'gpolloni@nodriza.cl','password' => '12345'),
        ));

        $response = json_decode(curl_exec($curl),true);

        curl_close($curl);

        // $response = $this->curl('POST',$this->API_PRO_ONESTOCK.'/api/v1/clientes/auth');
        
        return 
        [
            "token"         => $response['respuesta']['token'] ??null,
            "cliente_id"    => $response['cliente']  ['id']    ??null,
            "response"=>$response
        ];

    }

    public function obtenerProductoOneStock($producto_id)
    {
        set_time_limit(0);
        if(!is_int($producto_id))
        {
            return 'Debe enviar un valor númerico entero';
        }

        $credenciales = $this->obtenerTokenOnestock();

        if (!isset($credenciales['token'])) {
            
            return 'Hay problemas con las credenciales para obtener token de Onestock, por favor informar';
        }

        $response = $this->curl('GET', $this->API_PRO_ONESTOCK.'/api/v1/clientes/'.$credenciales['cliente_id'].'/productos/'.$producto_id.'?token='.$credenciales['token']);


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

    public function obtenerProductosClienteOneStock()
    {
        set_time_limit(0);
        $credenciales = $this->TOKEN;

        if (!isset($credenciales['token'])) {
            
            return ['400','Hay problemas con las credenciales para obtener token de Onestock, por favor informar',$credenciales];
        }
        
        $response = $this->curl('GET', $this->API_PRO_ONESTOCK.'/api/v1/clientes/'.$credenciales['cliente_id'].'/productos?token='.$credenciales['token']);
        
        $sinStock=[];
        $conStock=[];
        $ids_sin_stock=[];
        $ids_con_stock=[];
        $seguir = true;
        while ($seguir) {
            
            if (!isset($response['productos'])) {
                break;
            }
            foreach ($response['productos'] as $producto ) {
                $stock = false;
                if (isset($producto['producto_info']['mi_id'])) {
                    foreach ($producto['detalle_proveedores'] as $proveedore) {
    
                        if ($proveedore['disponible']== true) {
                            $stock = true;
                        }
                    }
                   
                    if (!$stock) {
                        $sinStock []=
                        [
                            'id'                    => $producto['producto_info']['mi_id'],
                            'fecha_modificacion'    => $proveedore['fecha_modificacion'],
                            'proveedor_id'          => $proveedore['id'],
                            'disponible'            => $proveedore['disponible']??false,
                            'stock'                 => $proveedore['stock']??0,
                        ];
                        $ids_sin_stock[]= $producto['producto_info']['mi_id'];
                    }else
                    {
                        $conStock []=
                        [
                            'id'                    => $producto['producto_info']['mi_id'],
                            'fecha_modificacion'    => $proveedore['fecha_modificacion'],
                            'proveedor_id'          => $proveedore['id'],
                            'disponible'            => $proveedore['disponible']??false,
                            'stock'                 => $proveedore['stock']??0,
                            // 'tipo_stock'            => $proveedore['tipo_stock'],
                        ];
                        $ids_con_stock[]= $producto['producto_info']['mi_id'];

                    }
                    
                }
               
            }
           
            if (!isset($response['next_page_url'])) {
                $seguir = false;
            }else
            {
                $response = $this->curl('GET',$response['next_page_url']);
            }
            
        }
        

        return ['sinStock'=>$sinStock,'conStock'=>$conStock,'ids_con_stock'=>$ids_con_stock,'ids_sin_stock'=>$ids_sin_stock,'response'=>$response];
       
        
    }

    public function curl($metodo , $url)
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL =>  $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 1000,
        CURLOPT_CONNECTTIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => $metodo,
        ));

        $response = json_decode(curl_exec($curl),true);
        
        curl_close($curl);

        return $response;
    }



}