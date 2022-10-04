<?php
App::uses('Component', 'Controller');
App::import('Vendor', 'PDFMerger', array('file' => 'PDFMerger/PDFMerger.php'));

class EtiquetasComponent extends Component
{   

    private static $formato = array(
        'venta' => array(
            'id',
            'embalaje_id',
            'metodo_envio',
            'canal',
            'medio_de_pago',
            'fecha_venta'
        ),
        'transportista' => array(
            'nombre',
            'tipo_servicio',
            'codigo_barra'
        ),
        'remitente' => array(
            'nombre',
            //'rut', No requerido
            'fono',
            'url',
            'email',
            'direccion'
        ),
        'destinatario' => array(
            'nombre',
            //'rut', No requerido
            'fono',
            'email',
            'direccion',
            'comuna',
            //'region' No requerido
        ),
        'bulto' => array(
            'referencia',
            'peso',
            'ancho',
            'alto',
            'largo'
        ),
        'pdf' => array(
            'dir'
        )
    );


    private static $formato_interna = array(
        'venta' => array(
            'id',
            'metodo_envio',
            'canal',
            'externo',
            'medio_de_pago',
            'fecha_venta'
        ),
        'embalaje' => array(
            'id'
        ),
        'transportista' => array(
            'nombre'
        ),
        'destinatario' => array(
            'nombre',
            //'rut', No requerido
            'fono',
            'email',
            'direccion',
            'comuna',
            //'region' No requerido
        ),
        'bulto' => array(
            'referencia',
            'peso',
            'ancho',
            'alto',
            'largo',
            'n_items'
        ),
        'mensajes' => array(
            'texto'
        ),
        'pdf' => array(
            'dir'
        )
    );

    /**
     * Obtiene el formato requerido para crear una etiqueta
     */
    public function getFormat()
    {
        return self::$formato;
    }


    /**
     * Obtiene el formato requerido para crear una etiqueta
     */
    public function getInternalFormat()
    {
        return self::$formato_interna;
    }

    /**
     * Valida que el arreglo ingresado sea válido
     */
    public function validarformato($array)
    {
        # Para la validación quitamos los indices opcionales
        if (isset($array['remitente']['rut']))
            unset($array['remitente']['rut']);

        if (isset($array['destinatario']['rut']))
            unset($array['destinatario']['rut']);

        if (isset($array['destinatario']['region']))
            unset($array['destinatario']['region']);
        
        if(Hash::diff(self::$formato, array_keys_recursive($array)))
        {
            return false;
        }

        return true;
        
    }


    /**
     * Valida que el arreglo ingresado sea válido
     */
    public function validarformatointerno($array)
    {
        # Para la validación quitamos los indices opcionales
        if (isset($array['remitente']['rut']))
            unset($array['remitente']['rut']);

        if (isset($array['destinatario']['rut']))
            unset($array['destinatario']['rut']);

        if (isset($array['destinatario']['region']))
            unset($array['destinatario']['region']);
        
        if(Hash::diff(self::$formato_interna, array_keys_recursive($array)))
        {
            return false;
        }

        return true;
        
    }
    
    /**
     * Genera la etiqueta del transportista seleccionado para una venta
     */
    public function generarEtiquetaTransporte($datos)
    {   
        $respuesta = array(
            'url' => '',
            'path' => ''
        );
        
        # Validamos que los campos minimos existan
        if (!$this->validarformato($datos))
        {   
            return $respuesta;
        }

        $etiquetaZpl = $this->formatoEtiquetaZplTransporte($datos);	
        
        $pathEtiquetas  = APP . 'webroot' . DS . 'img' . DS . $datos['pdf']['dir'] . DS . $datos['venta']['id'] . DS;
        $nombreEtiqueta = $datos['transportista']['codigo_barra'] . '-label.pdf';
        
        

        $curl = curl_init();
        // adjust print density (8dpmm), label width (4 inches), label height (6 inches), and label index (0) as necessary
        curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/6x4/0/");
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $etiquetaZpl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/pdf")); // omit this line to get PNG images back
        
        $etiquetaPdf = curl_exec($curl);
        
        # Creamos el directorio
        if (!is_dir($pathEtiquetas)) {
            @mkdir($pathEtiquetas, 0775, true);
        }

        # Creamos la etiqueta y guardamos
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200) {
            $file = fopen($pathEtiquetas . $nombreEtiqueta, "w");
            fwrite($file, $etiquetaPdf);
            fclose($file);

            $respuesta['url'] = obtener_url_base() . 'img/' . $datos['pdf']['dir'] . '/' . $datos['venta']['id'] . '/' . $nombreEtiqueta;
            $respuesta['path'] = $pathEtiquetas . $nombreEtiqueta;

        }

        curl_close($curl);

        return $respuesta;
    }


    public function generarEtiquetaInterna($datos)
    {   
        $respuesta = array(
            'url' => '',
            'path' => ''
        );

        # Validamos que los campos minimos existan
        if (!$this->validarformatointerno($datos))
        {
            return $respuesta;
        }

        $etiquetaZpl = $this->formatoEtiquetaZplInterna($datos);	

        $pathEtiquetas  = APP . 'webroot' . DS . 'img' . DS . $datos['pdf']['dir'] . DS . $datos['embalaje']['id'] . DS;
        $nombreEtiqueta = date('Y-m-d-H-i-s') . '-label.pdf';

        $curl = curl_init();
        // adjust print density (8dpmm), label width (4 inches), label height (6 inches), and label index (0) as necessary
        curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/6x4/0/");
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $etiquetaZpl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/pdf")); // omit this line to get PNG images back
        
        $etiquetaPdf = curl_exec($curl);
        
        # Creamos el directorio
        if (!is_dir($pathEtiquetas)) {
            @mkdir($pathEtiquetas, 0775, true);
        }

        # Creamos la etiqueta y guardamos
        if (curl_getinfo($curl, CURLINFO_HTTP_CODE) == 200) {
            $file = fopen($pathEtiquetas . $nombreEtiqueta, "w");
            fwrite($file, $etiquetaPdf);
            fclose($file);

            $respuesta['url'] = obtener_url_base() . 'img/' . $datos['pdf']['dir'] . '/' . $datos['embalaje']['id'] . '/' . $nombreEtiqueta;
            $respuesta['path'] = $pathEtiquetas . $nombreEtiqueta;

        }

        curl_close($curl);

        return $respuesta;
    }

    /**
     * Crear el formato ZPL para generar la etiqueta de transporte
     */
    public function formatoEtiquetaZplTransporte($datos = array()) {
        
        $region = '--'; 
        
        if ( isset($datos['destinatario']['region']) )
        {
            $region = Inflector::slug($datos['destinatario']['region'], ' ');
        }

		$etiqueta = "^XA
		^FX LOGO.
		^FO45,45^GFA,1197,1197,19,K07JFE003FFK03FFI0FFC,K0KFE01IFCJ0IFE00FFC,K0LF03JFI03JF00FFE,K0LF0KFC007JFC0FFE,K0LF0KFE00KFC0FFE,K0LF1LF03KFE0FFE,K0LF7LF03LF8FFE,K0LF7LF87LF8FFE,:K0SFCMFEFFE,L07FFC1IF8IFCIFC7FFEFFE,L03FF80IF03LF03FFEFFE,L03FF81FFE01KFE01KFE,:L03FF81FFC01KFE00KFE,::L03FF81FFE01KFE01KFE,L03FF81IF03FF03FF03KFE,L03FF80IF87FF03FF87FFEFFE,L03FF80LFC00LFEIF,L03FF807JFE0FC1KF8KF8,L03FF807JFC3FF0KF8KF8,L03FF807JF87FF87JF8KF8,L03FF803IFE3JF1JF0KF8,L03FF801IFE3EFDF1IFE0KF8,L03FF800IFE78FC79IFC0KF8,L03FF8007FFE78FC79IF00KF8,L03FF8001FFE78FC79FFE00KF8,L03FF8I07FE783879FF800KF8,V0780078,::Q01FFC07E01F8O03FFC,IF003FF8003IF07F03F83FF3FFC003FFE,IF007FF8007IF07F03F87FF3FFC007IF,IF807FF8007IF07F03F87FF3FFC007IF,IFC0IF8007IF87F03F87FF3FFC00JF8,IFC0IF8007IF83F03F07FF3FFC00JF8,IFE3IF800JFC0703807FF3FFC01JF8,:JFBIF801JFE01I0E7FF3FFC01JFC,NF803JFEJ07JF3FFC03JFE,NF803JFEJ0KF3FFC03JFE,NF803JFEI03KF3FFC03JFE,NF803KF00MF3FFC07KF,NF807KF00MF3FFC0LF,NF807KF80MF3FFC0LF,NF80IF3FFC0MF3FFC0IF7FF8,NF80FFE3FFC0MF3FFC0FFE7FF8,NF81FFE3FFC0MF3FFC1FFE3FF8,FFEKF81FFE3FFC0MF3FFC1FFE7FFC,FFEFFBFF81LFE0MF3FFC3LFE,FFE7F9FF81LFE0MF3FFC3LFE,FFE3F1FF83MF0IF7IF3FFC7LFE,FFE3E3FF83MF0IF7IF3FFC7LFE,FFE1C3FF87MF8IF3IF3FFC7MF,IF003FF8NF8IF1IF3FFCNF,IF003FF8NF8IF1IF3FFCNF8IF003FF8NF8IF0IF3FFCNF8IF003FF8IF007FFCIF07FF3FFDFFE007FFCIF003FF8IF003FFEIF03FF3FFDFFE003FFCIF003LF003FFEIF03FF3KFE003FFC^FS^
		^FX Recuadros.
		
		^FO225,10^GB1,130,2^FS
		^FO800,140^GB1,650,2^FS
		^FO10,140^GB1180,1,2,B,0^FS
		^FO225,45^GB965,1,2,B,0^FS
		^FO10,10^GB1180,780,2^FS
		^FO800,175^GB390,1,2,B,0^FS
		^FO800,325^GB390,1,2,B,0^FS
		^FO10,560^GB790,1,2,B,0^FS
		^FO10,270^GB790,1,2,B,0^FS
		^FO800,625^GB390,1,2,B,0^FS
		
		^FX Información superior
		^CF0,20
		^FO240,20^FDTransporte: " . $datos['transportista']['nombre'] . "^FS
		
		^CF0,20
		^FO815,20^FDVID:#" . $datos['venta']['id'] . "^FS
		
		^CF0,80
		^FO240,65^FD" . strtoupper(Inflector::slug($datos['venta']['metodo_envio'], ' ')) . "^FS
		
		
		^FX Remitente
		^CF0,25
		^FO20,155^FDREMITENTE : " . Inflector::slug($datos['remitente']['nombre'], ' ') . "^FS
		^FO400,155^FDRUT : " . formato_rut($datos['remitente']['rut']) . "^FS
		^FO20,195^FDFONO : " . Inflector::slug($datos['remitente']['fono'], ' ') . "^FS
		^FO400,195^FDEMAIL : " . Inflector::slug($datos['remitente']['email'], ' ') . "^FS
		^FO20,234^FDDIRECCION : " . Inflector::slug($datos['remitente']['direccion'], ' ') . "^FS
		
		^FX detalle compra
		^CF0,20
		^FO815,152^FDDETALLE DE LA VENTA^FS
		
		^CF0,20
		^FO815,190^FDCANAL DE VENTA: " . Inflector::slug($datos['venta']['canal'], ' ') . "^FS
		^FO815,225^FDMEDIO DE PAGO: " . Inflector::slug($datos['venta']['medio_de_pago'], ' ') . "^FS
		^FO815,260^FDMETODO ENVIO: " . Inflector::slug($datos['venta']['metodo_envio'], ' ') . "^FS
		^FO815,295^FDFECHA VENTA: " . $datos['venta']['fecha_venta'] . "^FS
		
		^FX Barra
		^BY5,3,177^FT117,490^BCN,,Y,N^FD" . $datos['transportista']['codigo_barra'] . "^FS
		
		^FX QR
		^FO920,165^BQN,2,4^FDQA," . obtener_url_base() . "api/ventas/" . $datos['venta']['id'] . ".json^FS
		^CF0,70
		^FO810,540^FDVID: #" . $datos['venta']['id'] . "^FS

		^FX Destinatario
		^CF0,25
		^FO20,580^FDDESTINATARIO : " . Inflector::slug($datos['destinatario']['nombre'], ' ') . "^FS
		^FO20,615^FDRUT : " . formato_rut($datos['destinatario']['rut']) . "^FS
		^FO20,650^FDFONO : " . $datos['destinatario']['fono'] . "^FS
		^FO400,650^FDEMAIL : " . $datos['destinatario']['email'] . "^FS
		^FO20,685^FDDIRECCION : " . Inflector::slug($datos['destinatario']['direccion'], ' ') . "^FS
		^FO20,720^FDCOMUNA : " . Inflector::slug($datos['destinatario']['comuna'], ' ') . "^FS
		^FO20,755^FDREGION : " . $region . "^FS
        ^FX Bultos
        ^CF0,25
        ^FO810,640^FDBULTO REF: " . $datos['bulto']['referencia'] . "^FS
        ^FO810,675^FDPESO TOTAL: " . $datos['bulto']['peso'] . " KG^FS        
        ^FO810,710^FDANCHO: " . $datos['bulto']['ancho'] . "^FS
        ^FO930,710^FDLARGO: " . $datos['bulto']['largo'] . "^FS
        ^FO1050,710^FDALTO: " . $datos['bulto']['alto'] . "^FS
        ^FO810,745^FDTIPO SERVICIO: " . Inflector::slug($datos['transportista']['tipo_servicio'], ' ') . "^FS
        ^XZ";
		return $etiqueta;
    }
    
    /**
     * Crear el formato ZPL para generar la etiqueta interna
     *
     * @param  mixed $datos Información de la etiqueta
     * @return void
     */
    public function formatoEtiquetaZplInterna($datos = array())
    {   
        $etiqueta = "^XA
        ^FX LOGO.
        ^FO45,45^GFA,1197,1197,19,K07JFE003FFK03FFI0FFC,K0KFE01IFCJ0IFE00FFC,K0LF03JFI03JF00FFE,K0LF0KFC007JFC0FFE,K0LF0KFE00KFC0FFE,K0LF1LF03KFE0FFE,K0LF7LF03LF8FFE,K0LF7LF87LF8FFE,:K0SFCMFEFFE,L07FFC1IF8IFCIFC7FFEFFE,L03FF80IF03LF03FFEFFE,L03FF81FFE01KFE01KFE,:L03FF81FFC01KFE00KFE,::L03FF81FFE01KFE01KFE,L03FF81IF03FF03FF03KFE,L03FF80IF87FF03FF87FFEFFE,L03FF80LFC00LFEIF,L03FF807JFE0FC1KF8KF8,L03FF807JFC3FF0KF8KF8,L03FF807JF87FF87JF8KF8,L03FF803IFE3JF1JF0KF8,L03FF801IFE3EFDF1IFE0KF8,L03FF800IFE78FC79IFC0KF8,L03FF8007FFE78FC79IF00KF8,L03FF8001FFE78FC79FFE00KF8,L03FF8I07FE783879FF800KF8,V0780078,::Q01FFC07E01F8O03FFC,IF003FF8003IF07F03F83FF3FFC003FFE,IF007FF8007IF07F03F87FF3FFC007IF,IF807FF8007IF07F03F87FF3FFC007IF,IFC0IF8007IF87F03F87FF3FFC00JF8,IFC0IF8007IF83F03F07FF3FFC00JF8,IFE3IF800JFC0703807FF3FFC01JF8,:JFBIF801JFE01I0E7FF3FFC01JFC,NF803JFEJ07JF3FFC03JFE,NF803JFEJ0KF3FFC03JFE,NF803JFEI03KF3FFC03JFE,NF803KF00MF3FFC07KF,NF807KF00MF3FFC0LF,NF807KF80MF3FFC0LF,NF80IF3FFC0MF3FFC0IF7FF8,NF80FFE3FFC0MF3FFC0FFE7FF8,NF81FFE3FFC0MF3FFC1FFE3FF8,FFEKF81FFE3FFC0MF3FFC1FFE7FFC,FFEFFBFF81LFE0MF3FFC3LFE,FFE7F9FF81LFE0MF3FFC3LFE,FFE3F1FF83MF0IF7IF3FFC7LFE,FFE3E3FF83MF0IF7IF3FFC7LFE,FFE1C3FF87MF8IF3IF3FFC7MF,IF003FF8NF8IF1IF3FFCNF,IF003FF8NF8IF1IF3FFCNF8IF003FF8NF8IF0IF3FFCNF8IF003FF8IF007FFCIF07FF3FFDFFE007FFCIF003FF8IF003FFEIF03FF3FFDFFE003FFCIF003LF003FFEIF03FF3KFE003FFC^FS^
        ^FX Recuadros.
        
        ^FO225,10^GB2,130,2^FS
        ^FO800,10^GB2,35,2^FS
        ^FO980,10^GB2,35,2^FS
        ^FO800,140^GB1,650,2^FS
        ^FO10,140^GB1180,1,2,B,0^FS
        ^FO225,45^GB965,1,2,B,0^FS
        ^FO10,10^GB1180,780,2^FS
        ^FO800,175^GB390,1,2,B,0^FS
        ^FO800,325^GB390,1,2,B,0^FS
        ^FO10,325^GB790,1,2,B,0^FS
        ^FO800,625^GB390,1,2,B,0^FS
        
        ^FX Información superior
        ^CF0,20
        ^FO240,20^FDTRANSPORTE: " . $datos['transportista']['nombre'] . "^FS
        
        ^CF0,20
        ^FO815,20^FDVID: #" . $datos['venta']['id'] . "^FS
        
        ^CF0,20
        ^FO1015,20^FDEMBALAJE:#" . $datos['embalaje']['id'] . "^FS
        
        ^CF0,80
        ^FO240,65^FD" . strtoupper(Inflector::slug($datos['venta']['metodo_envio'], ' ')) . "^FS
        
        
        ^FX Destinatario
        ^CF0,25
        ^FO20,155^FDDESTINATARIO : " . Inflector::slug($datos['destinatario']['nombre'], ' ') . "^FS
        ^FO20,190^FDRUT : " . $datos['destinatario']['rut'] . "^FS
        ^FO20,225^FDFONO : " . $datos['destinatario']['fono'] . "^FS
        ^FO400,225^FDEMAIL : " . $datos['destinatario']['email'] . "^FS
        ^FO20,260^FDDIRECCION : " . Inflector::slug($datos['destinatario']['direccion'], ' ') . "^FS
        ^FO20,295^FDCOMUNA : " . Inflector::slug($datos['destinatario']['comuna'], ' ') . "^FS
        
        ^FX Mensajes
        ^CF0,25
        ^FO20,350
        ^FB770,270,,^FDMENSAJE:". $this->autoLine($datos['mensajes']['texto'], 60) ."^FS
        ^FX detalle compra
        ^CF0,20
        ^FO815,152^FDDETALLE DE LA VENTA^FS
        
        ^CF0,20
        ^FO815,190^FDCANAL DE VENTA: " . Inflector::slug($datos['venta']['canal'], ' ') . "^FS
        ^FO815,225^FDEXTERNO: " . $datos['venta']['externo'] . "^FS
        ^FO815,260^FDMEDIO DE PAGO: " . Inflector::slug($datos['venta']['medio_de_pago'], ' ') . "^FS
        ^FO815,295^FDFECHA VENTA: " . $datos['venta']['fecha_venta'] . "^FS
        
        ^FX QR
        ^FO910,345^BQN,2,4^FDQA," . obtener_url_base() . "api/ventas/" . $datos['venta']['id'] . ".json^FS
        ^CF0,60
        ^FO830,550^FDVID: #" . $datos['venta']['id'] . "^FS
        
        ^FX Bultos
        ^CF0,22
        ^FO810,640^FDBULTO REF: #" . $datos['embalaje']['id'] . "^FS
        ^FO810,680^FDPESO TOTAL:  " . $datos['bulto']['peso'] . "KG^FS        
        ^FO810,720^FDANCHO: " . $datos['bulto']['ancho'] . "CM^FS
        ^FO930,720^FDLARGO: " . $datos['bulto']['largo'] . "CM^FS
        ^FO1050,720^FDALTO: " . $datos['bulto']['alto'] . "CM^FS
        ^FO810,760^FDTOTAL ITEMS: " . $datos['bulto']['n_items'] . "^FS
        ^XZ";

        return $etiqueta;
    }

    /**
	 * Uni distintos archivos PDF en uno solo
     * maximo 500 pdf a unir
     * 
	 * @param  array  $archivos Arreglo con las rutas de los archivos PDF a unir
	 * @param  string $vid Identificador de la venta
	 * @return array
	 */
	public function unir_documentos($archivos = array(), $vid = '')
	{
		$pdfs       = array();
		$limite     = 500;
		$lote = 0;
		$ii = 1;

		foreach ($archivos as $i => $archivo) {

			if (file_exists($archivo)) {
				$pdfs[$lote][$ii] = $archivo;

				if ($ii%$limite == 0) {
					$lote++;
				}	
			}

			$ii++;
		}

		if (!is_dir(APP . 'webroot' . DS. 'Venta' . DS . $vid)) {
			@mkdir(APP . 'webroot' . DS. 'Venta' . DS . $vid, 0775);
		}

		# Se procesan por Lotes de 500 documentos para no volcar la memoria
		foreach ($pdfs as $ip => $lote) {
			$pdf = new PDFMerger;
			foreach ($lote as $id => $document) {
				$pdf->addPDF($document, 'all');	
			}
			try {
				
				$pdfname = 'etiqueta-envio-' . date('YmdHis') .'.pdf';

				$res = $pdf->merge('file', APP . 'webroot' . DS. 'Venta' . DS . $vid . DS . $pdfname);
				if ($res) {
					$resultados['result'][]['document'] = Router::url('/', true) . 'Venta/' . $vid . '/' . $pdfname;
				}

			} catch (Exception $e) {
				$resultados['errors']['messages'][] = $e->getMessage();
			}
		}

		return $resultados;
	}

        
    /**
     * autoLine
     *
     * @param  mixed $texto
     * @param  mixed $limite
     * @param  mixed $top
     * @return void
     */
    public function autoLine($texto, $limite)
    {   

        if (empty($texto))
            return $texto;

        $text = wordwrap($texto, $limite, "\n", false);
        
        return $text;
    }

    public function generarEtiquetaExternaTransporte($etiquetaZpl)
    {   
        
        $curl = curl_init();
        // adjust print density (8dpmm), label width (4 inches), label height (6 inches), and label index (0) as necessary
        curl_setopt($curl, CURLOPT_URL, "http://api.labelary.com/v1/printers/8dpmm/labels/4x6/0/");
        curl_setopt($curl, CURLOPT_POST, TRUE);
        curl_setopt($curl, CURLOPT_POSTFIELDS, $etiquetaZpl);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, TRUE);
        curl_setopt($curl, CURLOPT_HTTPHEADER, array("Accept: application/pdf")); // omit this line to get PNG images back
        
        $etiquetaPdf = curl_exec($curl);
        $curl_getinfo = curl_getinfo ($curl, CURLINFO_HTTP_CODE );
        # Creamos el directorio
        curl_close($curl);
        return ['curl_getinfo' => $curl_getinfo ,'etiquetaPdf' =>$etiquetaPdf];
    }

}