<?php

class BlueExpress
{

    protected static $API_ROOT_URL;
    protected static $BX_TOKEN;
    protected static $BX_USERCODE;
    protected static $BX_CLIENT_ACCOUNT;

    
    /**
     * __construct
     * Es necesario inicializar la clase para hacer uso de los metodos
     * @param  mixed $BX_TOKEN
     * @param  mixed $BX_USERCODE
     * @param  mixed $BX_CLIENT_ACCOUNT
     * @return void
     */
    public function __construct($BX_TOKEN, $BX_USERCODE, $BX_CLIENT_ACCOUNT)
    {
        self::$API_ROOT_URL = "https://bx-tracking.bluex.cl";
        self::$BX_TOKEN = $BX_TOKEN ?? '';
        self::$BX_USERCODE = $BX_USERCODE ?? '';
        self::$BX_CLIENT_ACCOUNT = $BX_CLIENT_ACCOUNT ?? '';
    }
    
    /**
     * BXGeolocation
     * Por medio del endpoint se retorna todos las comunas que tienen cobertura
     * @return void
     */
    public function BXGeolocation()
    {
        return $this->cURL_GET("https://bx-tracking.bluex.cl/bx-geo/state/all");
    }
    
    /**
     * FiltrarCiudadRegion
     * Por medio del endpoint se retorna las ciudades y distrito de determianda region
     * @param  mixed $REGION
     * @return void
     */
    public function FiltrarCiudadRegion($REGION)
    {
        $this->cURL_GET("https://bx-tracking.bluex.cl/bx-geo/state/cl/{$REGION}");
    }
    
    /**
     * CentrosDistribucion
     * Por medio del endpoint se retorna los centros de distribucion de BlueExpress
     * @return void
     */
    public function CentrosDistribucion()
    {
        return $this->cURL_GET("https://bx-tracking.bluex.cl/bx-geo/base/all");
    }
    
    /**
     * BXPricing
     * Por medio de este endpoint se obtiene el valor del la OT(orden de trasnporte) asi como la fecha de entrega aproximada
     * @param  mixed $from
     * @param  mixed $to
     * @param  mixed $datosProducto
     * @return void
     */
    public function BXPricing($from, $to, $datosProducto)
    {
        $POSTFIELDS = [
            'from' => [
                'country'   => 'CL',
                'district'  => $from['district'] ?? ''
            ],
            'to' => [
                'country'   => 'CL',
                'state'     => $to['state'] ?? '',
                'district'  => $to['district'] ?? ''
            ],
            'serviceType'               => 'EX',
            'serviciosComplementarios'  => null,
            'datosProducto' =>
            [
                'producto'          => $datosProducto['producto'] ?? '',
                'familiaProducto'   => 'PAQU',
                'largo'             => $datosProducto['largo'] ?? '',
                'ancho'             => $datosProducto['ancho'] ?? '',
                'alto'              => $datosProducto['alto'] ?? '',
                'pesoFisico'        => $datosProducto['pesoFisico'] ?? '',
                'cantidadPiezas'    => $datosProducto['cantidadPiezas'] ?? '',
                'unidades'          => $datosProducto['cantidadPiezas'] ?? ''
            ]
        ];
        
        return $this->cURL_POST('https://bx-tracking.bluex.cl/bx-pricing/v1', $POSTFIELDS);
    }

    public function BXEmission($BULTO)
    {
        $OT = [
            "printFormatCode" => 4,
            "orderNumber" =>  $BULTO['venta']['id'],
            "references" => [
                $BULTO['venta']['referencia'],
            ],
            "serviceType" => "EX",
            "productType" => "P",
            "productCategory" => "PAQU",
            "currency" => "CLP",
            "shipmentCost" => $BULTO['venta']['costo_envio'] * 1,
            "extendedClaim" => true,
            "companyId" => 2000,
            "userName" => $BULTO['credenciales'],
            "comments" => $BULTO['venta']['referencia_despacho'],
            "pickup" => [
                "location" => [
                    "stateId" => $BULTO['pickup']['stateId'],
                    "districtId" =>  $BULTO['pickup']['districtId'],
                    "address" => $BULTO['pickup']['address'],
                    "name" =>  $BULTO['pickup']['name'],
                ],
                "contact" => [
                    "fullname" =>  $BULTO['pickup']['fullname'],
                    "phone" =>  $BULTO['pickup']['phone'],
                ]
            ],
            "dropoff" => [
                "contact" => [
                    "fullname" => $BULTO['dropoff']['nombre_receptor'],
                    "phone" => $BULTO['dropoff']['fono_receptor'],
                ],
                "location" => [
                    "stateId" => $BULTO['dropoff']['stateId'],
                    "districtId" => $BULTO['dropoff']['districtId'],
                    "address" => $BULTO['dropoff']['direccion'],
                    "name" => $BULTO['dropoff']['name'],
                ]
            ],
            "packages" => [
                [
                    "weightUnit" => "KG",
                    "lengthUnit" => "CM",
                    "weight" => $BULTO['packages']['peso'],
                    "length" => $BULTO['packages']['largo'],
                    "width" => $BULTO['packages']['ancho'],
                    "height" => $BULTO['packages']['alto'],
                    "quantity" => 1
                ]
            ],
            "dangerousPackages" => null,
            "returnDocuments" => null,
            "collectionsOnDelivery" => null,
            "notificationContacts" => null,
            "extras" => null
        ];

        return $this->cURL_POST('https://bx-tracking.bluex.cl/bx-emission/v1', $OT);
    }
    
    /**
     * BXLabel
     * Por medio de este enpoint se puede recuperar la etiqueta externa para BlueExpress en formato base64 el cual se decodifica con metodo base64_decode()
     * @param  mixed $trackingNumber
     * @return void
     */
    public function BXLabel($trackingNumber)
    {
        return $this->cURL_GET('https://bx-tracking.bluex.cl/bx-label/v1/' . $trackingNumber ?? '');
    }
    
    /**
     * BXTrackingPull
     * Se conecta con endpoint para obtener estados de una OT(orden de trasnporte)
     * @param  mixed $trackingNumber
     * @return void
     */
    public function BXTrackingPull($trackingNumber)
    {
        return $this->cURL_GET('https://bx-tracking.bluex.cl/bx-tracking/v1/tracking-pull/' . $trackingNumber ?? '',true);
    }
    
    /**
     * cURL_POST
     * Se estandariza cURL para peticiones POST
     * @param  mixed $URL
     * @param  mixed $POSTFIELDS
     * @return void
     */
    private function cURL_POST($URL, $POSTFIELDS)
    {
        $curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 1000,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($POSTFIELDS, true),
            CURLOPT_HTTPHEADER => array(
                "BX-TOKEN:" . self::$BX_TOKEN,
                "BX-USERCODE:" . self::$BX_USERCODE,
                "BX-CLIENT_ACCOUNT:" . self::$BX_CLIENT_ACCOUNT,
                'Content-Type:application/json'
            )
        ));
        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);
        return [
            "code"     => $http_code,
            "response" => json_decode($response, true)
        ];
    }
    
    /**
     * cURL_GET
     * Se estandariza cURL para peticiones GET
     * 
     * @param  mixed $URL -> URL a la cual se hará la petición
     * @param  mixed $CHANGE_CLIENT Debido a que el endpoint esta dañado la key (BX-CLIENT_ACCOUNT) debe enviarse como (BX-CLIENT-ACCOUNT)
     * la diferencia es el último -
     * @return void
     */
    private function cURL_GET($URL,$CHANGE_CLIENT = false)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $URL,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 1000,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                "BX-TOKEN:" . self::$BX_TOKEN,
                "BX-USERCODE:" . self::$BX_USERCODE,
                ($CHANGE_CLIENT?"BX-CLIENT-ACCOUNT:":"BX-CLIENT_ACCOUNT:") . self::$BX_CLIENT_ACCOUNT
            ),
        ));

        $response = curl_exec($curl);
        $http_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        curl_close($curl);

        return [
            "code"     => $http_code,
            "response" => json_decode($response, true)
        ];
    }

}
