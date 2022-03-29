<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'LibreDTE-API', array('file' => 'LibreDTE-API/LibreDTE-API.php'));



class ApiLibreDTEComponent extends Component
{

    private $LibreDTEAPI;
    
    public $components = array('LibreDte');

    private static $tipo_compras = [
        'PENDIENTE' => 'PENDIENTE', 
        'REGISTRO' => 'REGISTRO',
        'NO_INCLUIR' => 'NO_INCLUIR',
        'RECLAMADO' => 'RECLAMADO'
    ];
    
    /**
     * Crear conexión con el SII a través de libredte
     * 
     * @param string    $token  Token de acceso de libredte
     * @param array     $cert   Datos para login con certificado digital
     * @param array     $pass   Datos para login con clave y contraseña del sii
     * 
     * @return void
     */
    public function crearCliente($token, $cert = [], $pass = [])
    {
        $this->LibreDTEAPI = new LibreDTEAPI($token, $cert, $pass);
    }

    
    /**
     * Obtiene los documentos de compra según periodo
     * 
     * Ref: https://documenter.getpostman.com/view/5911929/SWLiYkK9#f847aa91-47f7-42a4-a380-05ab5bdb5fff
     *
     * @param  mixed $receptor  Rut del receptor con dv y guión
     * @param  mixed $periodo   Año y mes del periodo a consultar ej: 202201
     * @param  mixed $tipo_dte  Por defecto se usa tipo 33 facturas
     * @param  mixed $estado    Estado de los dtes a consultar
     * @param  mixed $params    Parámetros adicionales para la query
     * @return 
     */
    public function obtenerDocumentosCompras($receptor, $periodo, $tipo_dte = 33, $estado, $params = [])
    {
        return $this->LibreDTEAPI->obtenerDocumentosCompras($receptor, $periodo, $tipo_dte, $estado, $params);
    }

    
    /**
     * cambiarEstadoDteCompra
     *
     * @param  mixed $docs
     * @return void
     */
    public function cambiarEstadoDteCompra($docs)
    {   
        $params = [
            'certificacion' => 0
        ];

        return $this->LibreDTEAPI->cambiarEstadoDteCompra($docs, $params);
    }

    
    /**
     * obtener_estados
     *
     * @return array
     */
    public function obtener_estados()
    {
        return self::$tipo_compras;
    }

    // public function aceptarFactura($TipoDTE = 33, $Folio, $FchEmis, $RUTEmisor, $RUTRecep, $MntTotal)
    // {
    //     $data = [
    //         "auth" => [
    //             "pass" => [
    //                 "rut" => "",
    //                 "clave" => ""
    //             ]
    //         ],
    //         "documentos" => [
    //             [
    //                 "TipoDTE" => 33,
    //                 "Folio" => 1,
    //                 "FchEmis" => "2020-03-10",
    //                 "RUTEmisor" => "96806980-2",
    //                 "RUTRecep" => "76192083-9",
    //                 "MntTotal" => 10000,
    //                 "EstadoRecepDTE" => "ERM",
    //                 "RecepDTEGlosa" => "Ok"
    //             ]
    //         ]
    //     ];

    //     $this->ApiLibreDTE->consume('/libredte/dte/intercambios/respuesta_sii?certificacion=0');
    // }

    public function EstadoEnvioXMLSII($track_id = 6760212170, $emisor = 76891050)
    {

        $this->LibreDTEAPI = new LibreDTEAPI($this->token);
        return $this->LibreDTEAPI->EstadoEnvioXMLSII("76381142-5", "ToolMania9", $track_id, $this->digitador($emisor));
    }

    public function VerificacionAvanzadaDocumentoSII($emisor = 79996420, $receptor = 76381142, $dte = 33, $folio = 191349, $fecha = "2022-01-21", $total = 159110)
    {   
        $this->LibreDTEAPI = new LibreDTEAPI($this->token);
        return $this->LibreDTEAPI->VerificacionAvanzadaDocumentoSII($this->digitador($emisor), $this->digitador($receptor), $dte, $folio);
    }

    public function EstadoDeFolio($emisor = 76891050, $dte = 33, $folio = 115602)
    {

        $this->LibreDTEAPI = new LibreDTEAPI($this->token);
        return $this->LibreDTEAPI->EstadoDeFolio("76381142-5", "ToolMania9", $this->digitador($emisor), $dte, $folio);
    }
    


    private function digitador($r = 76381142)
    {
        $rut = $r;
        $s = 1;
        for ($m = 0; $r != 0; $r /= 10)
            $s = ($s + $r % 10 * (9 - $m++ % 6)) % 11;
        return  "{$rut}-" . chr($s ? $s + 47 : 75);
    }
}
