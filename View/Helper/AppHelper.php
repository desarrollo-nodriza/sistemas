<?php
App::uses('Helper', 'View');
class AppHelper extends Helper
{
	public function menuActivo($link = array())
	{
		if ( ! is_array($link) || empty($link) )
		{
			return false;
		}

		$action				= $this->request->params['action'];
		$controller			= $this->request->params['controller'];
		$prefix				= (isset($this->request->params['prefix']) ? $this->request->params['prefix'] : null);


		if ( $prefix && isset($this->request->params[$prefix]) && $this->request->params[$prefix] )
		{
			$tmp_action			= explode('_', $action);
			if ( $tmp_action[0] === $prefix )
			{
				array_shift($tmp_action);
				$action			= implode('_', $tmp_action);
			}
		}

		return (
			(isset($link['controller']) ? ($link['controller'] == $controller) : true) &&
			(isset($link['action']) ? ($link['action'] == $action) : true)
		);
	}

	/**
	 * Traducir Mes a Español
	 * @param 	String 	Mes a traducir
	 * @return  String 	Mes traducido	
	 */
	public function translateMonth( $mes = null ) {

		if (!empty($mes)) {

			$mesesAEspanol = array(
				'January' => 'Enero',
				'Feruary' => 'Febrero',
				'March' => 'Marzo',
				'April' => 'Abril',
				'May' => 'Mayo',
				'June'=> 'Junio',
				'July' => 'Julio',
				'August' => 'Agosto',
				'September' => 'Septiembre',
				'October' => 'Octubre',
				'November' => 'Noviembre',
				'December' => 'Diciembre'
			);

			if ( array_key_exists($mes, $mesesAEspanol) ) {
				$mes = $mesesAEspanol[$mes];
				return $mes;
			}else{
				return $mes;
			}
		}
	}

	public function assetUrl($path, $options = array()) {
        if (!empty($this->request->params['ext']) && $this->request->params['ext'] === 'pdf') {
            $options['fullBase'] = true;
        }
        return parent::assetUrl($path, $options);
    }


    public function dteEstado($slug = '', $lista = false)
    {
    	if (!empty($slug)) {
    		$estados = array(
    			'no_generado' => '<label class="label label-warning">DTE no emitido</label>',
    			'dte_temporal_no_emitido' => '<label class="label label-info">DTE Temporal no emitido</label>',
    			'dte_real_no_emitido' => '<label class="label label-warning">DTE Real no emitido</label>',
    			'dte_real_emitido' => '<label class="label label-success">DTE Emitido</label>'
    		);

    		return $estados[$slug];
    	}

    	if ($lista) {
    		$listaEstados = array(
    			'no_generado' => 'DTE No Emitido',
    			'dte_temporal_no_emitido' => 'DTE Temporal no emitido',
    			'dte_real_no_emitido' => 'DTE Real no emitido',
    			'dte_real_emitido' => 'DTE Emitido'
    		);

    		return $listaEstados;
    	}

    	return '<label class="label label-warning">DTE no emitido</label>';
    }

    public $tipoDocumento = array(
		#30 => 'factura',
		#32 => 'factura de venta bienes y servicios no afectos o exentos de IVA',
		#35 => 'Boleta',
		#38 => 'Boleta exenta',
		#45 => 'factura de compra',
		#55 => 'nota de débito',
		#60 => 'nota de crédito',
		#103 => 'Liquidación',
		#40 => 'Liquidación Factura',
		#43 => 'Liquidación - Factura Electrónica',
		33 => 'Factura Electrónica',
		#34 => 'Factura No Afecta o Exenta Electrónica',
		39 => 'Boleta Electrónica',
		#41 => 'Boleta Exenta Electrónica',
		#46 => 'Factura de Compra Electrónica',
		56 => 'Nota de Débito Electrónica',
		61 => 'Nota de Crédito Electrónica',
		#50 => 'Guía de Despacho',
		52 => 'Guía de Despacho Electrónica',
		#110 => 'Factura de Exportación Electrónica',
		#111 => 'Nota de Débito de Exportación Electrónica',
		#112 => 'Nota de Crédito de Exportación Electrónica',
		#801 => 'Orden de Compra', 
		#802 => 'Nota de pedido',
		#803 => 'Contrato',
		#804 => 'Resolución',
		#805 => 'Proceso ChileCompra',
		#806 => 'Ficha ChileCompra',
		#807 => 'DUS',
		#808 => 'B/L (Conocimiento de embarque)',
		#809 => 'AWB (Air Will Bill)',
		#810 => 'MIC/DTA',
		#811 => 'Carta de Porte',
		#812 => 'Resolución del SNA donde califica Servicios de Exportación',
		#813 => 'Pasaporte',
		#814 => 'Certificado de Depósito Bolsa Prod. Chile',
		#815 => 'Vale de Prenda Bolsa Prod. Chile'
	);

	public function getFormatedValue($field)
    {
        $field_type = $field['field_type'];
        $field_value = $field['field_value'];
        if (($field_type == 'multiselect' && $field_value) || ($field_type == 'radio' && $field_value) || ($field_type == 'checkbox' && $field_value) || ($field_type == 'message' && $field_value)) {
            $value = unserialize($field_value);
            return join(', ', $value);
        }
        return $field_value;
    }


    /**
     * Se encarga de definir cuantos días de retraso tiene la venta según su fecha de venta.
     * @param  string $fecha [description]
     * @return string        Mensaje
     */
    public function calcular_retraso($fecha = '')
    {
    	if (!empty($fecha)) {
    		
    		$fechaVenta = new DateTime($fecha);
			$hoy = new DateTime(date('Y-m-d H:i:s'));
			$retraso = $hoy->diff($fechaVenta);

			$retrasoHoras = $fechaVenta->diff($hoy);
			
			if ($retrasoHoras->days > 0) {
				return ($retrasoHoras->days > 1) ? sprintf('<label class="label btn-block label-danger label-form"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> %d días de retraso</label>', $retrasoHoras->days) : sprintf('<label class="label label-warning label-form"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> %d día de retraso</label>', $retrasoHoras->days);
			}else{
				return sprintf('<span class="text-muted btn-block"><small>Creada hace %d horas</small></span>', $retraso->h);
			}

			return '';
    	}
    }


    /**
     * Retorna un rut formateado
     * @param  [type] $rut [description]
     * @return [type]      [description]
     */
    public function rut( $rut ) {
	    return number_format( substr ( $rut, 0 , -1 ) , 0, "", ".") . '-' . substr ( $rut, strlen($rut) -1 , 1 );
	}
}
