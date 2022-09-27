<?php
App::uses('AppController', 'Controller');
class MetodoEnvioRetrasosController extends AppController
{   
    public $components = array(
		'RequestHandler',
		'WarehouseNodriza',
        'Mandrill'
	);
    
    /**
     * calcular_horas_retraso
     *
     * @param  mixed $fecha_cambio_estado
     * @return int horas de retrasos
     */
    public function calcular_horas_retraso($fecha_cambio_estado)
    {
        $ahora  = date_create(date('Y-m-d H:i:s'));
        $f_estado = date_create($fecha_cambio_estado);

        $diferencia = date_diff($ahora, $f_estado);

        return (int) ($diferencia->days * 24) + (int) $diferencia->h; 
    }


    public function enviar_email($embalaje_id)
    {
        $embalaje = $this->WarehouseNodriza->ObtenerEmbalaje($embalaje_id);
        prx($embalaje);

        $this->View             = new View();
        $this->View->viewPath   = 'MetodoEnvioRetraso' . DS . 'emails';
        $this->View->layoutPath = 'Correos' . DS . 'html';

        $url = obtener_url_base();
       
        $this->View->set(compact('contacto', 'url', 'token'));
        $html = $this->View->render('confirmar_contacto');

        $mandrill_apikey = $contacto['Tienda']['mandrill_apikey'];

        if (empty($mandrill_apikey)) {
            return false;
        }

        $mandrill = $this->Components->load('Mandrill');

        $mandrill->conectar($mandrill_apikey);

        $asunto = sprintf('[CS %s] ¿Resolvimos tu requerimiento? - id #%d - %s', $contacto['Tienda']['nombre'], $contacto['Contacto']['id'], date('Y-m-d H:i:s'));

        if (Configure::read('ambiente') == 'dev') {
            $asunto = sprintf('[CS %s - DEV] ¿Resolvimos tu requerimiento? - id #%d - %s', $contacto['Tienda']['nombre'], $contacto['Contacto']['id'], date('Y-m-d H:i:s'));
        }

        $remitente = array(
            'email' => 'clientes@nodriza.cl',
            'nombre' => 'Servicio al cliente ' . $contacto['Tienda']['nombre']
        );

        $destinatarios = array();
        $destinatarios[] = array(
            'email' => $contacto['Contacto']['email_contacto'],
            'type' => 'to'
        );
        
        return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
    }

    public function api_enviar_notificacion_prueba()
    {
        # Existe token
		if (!isset($this->request->query['token'])) 
            return $this->api_response(401, 'Expected Token');

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token']))
			return $this->api_response(401, 'Invalid or expired Token');

        $tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
            'mandrill_apikey', 
            'nombre'
        ));

        prx($tienda);
    }

    
      
    /**
     * obtener_ventas_por_retraso
     * Obtenemos las ventas de todas las reglas que tengan retraso
     * @return array
     */
    public function obtener_ventas_por_retraso()
    {
        # consultamos las reglas
        $reglas = $this->MetodoEnvioRetraso->reglas_activas();
        $ventas =   [];
        foreach ($reglas as $regla) {
            $ventas = array_merge($ventas, $this->consultar_ventas_por_regla_de_retraso($regla));
           
        }

        return $ventas;
    }
    
      
    /**
     * crear_registro_retraso
     * retorna el modelo para ser guardado dsps
     * @param  array $venta
     * @return array
     */
    public function crear_registro_retraso(array $venta)
    {
        return array(
            'RetrasoVenta' => array(
                'venta_id'          => $venta['Venta']['id'],
                'venta_estado_id'   => $venta['Venta']['venta_estado_id'],
                'venta_cliente_id'  => $venta['Venta']['venta_cliente_id'],
                'horas_retraso'     => $venta[0]['horas_retraso'],
            )
        );
    }

    public function admin_index()
    {
        $ventas     = $this->obtener_ventas_por_retraso();
        prx($ventas);

        $retrasos   = [];
        foreach ($ventas as $venta) {
            $retrasos[] = $this->crear_registro_retraso($venta);
        }
       
        if ($retrasos) {
            ClassRegistry::init('RetrasoVenta')->create();
            ClassRegistry::init('RetrasoVenta')->saveAll($retrasos);
        }

        prx($retrasos);

        
    }

    /**
     * consultar_ventas_por_regla_de_retraso
     * Se traen las ventas que tengan mucho tiempo en un estado segun se configuren las reglas de retraso
     * Para traer las ventas se filtra por : bodega | metodo de envio | la categoria del estado de la venta | que no sea marketplace |
     * las horas de retraso sean igual o mayor a las de la regla | que no tenga registros de retraso
     * @param  array $regla
     * @return array
     */
    public function consultar_ventas_por_regla_de_retraso(array $regla)
    {
        return ClassRegistry::init('Venta')->find('all', array(
            'joins' => array(
                array(
                    'table' => 'rp_metodo_envios',
                    'alias' => 'metodo_envios',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'metodo_envios.id = Venta.metodo_envio_id',
                    )
                ),
                array(
                    'table' => 'rp_venta_estados',
                    'alias' => 'venta_estados',
                    'type'  => 'INNER',
                    'conditions' => array(
                        'venta_estados.id = Venta.venta_estado_id',
                    )
                ),
                array(
                    'table' => 'rp_retraso_ventas',
                    'alias' => 'retraso_ventas',
                    'type'  => 'LEFT',
                    'conditions' => array(
                        'retraso_ventas.venta_estado_id = Venta.venta_estado_id',
                        'retraso_ventas.venta_id = Venta.id',
                    )
                ),
            ),
            'conditions' => array(
                'Venta.bodega_id'                           => $regla['MetodoEnvioRetraso']['bodega_id'],
                'Venta.marketplace_id'                      => null,
                'Venta.metodo_envio_id'                     => $regla['MetodoEnvioRetraso']['metodo_envio_id'],
                'venta_estados.venta_estado_categoria_id'   => $regla['MetodoEnvioRetraso']['venta_estado_categoria_id'],
                'metodo_envios.notificar_retraso'           => true,
            ),
            'fields' => array(
                'Venta.id',
                'Venta.venta_estado_id',
                'Venta.venta_cliente_id',
                '(SELECT TIMESTAMPDIFF(HOUR, `ev`.`fecha`, NOW())
                from rp_estados_ventas ev
                where `ev`.`venta_id` = `Venta`.`id`
                  and `ev`.`venta_estado_id` = `Venta`.`venta_estado_id`
                order by `ev`.`fecha`
                limit 1) horas_retraso',
                'count(retraso_ventas.id) hay_retraso'
            ),
            'having'    => [
                "horas_retraso >=" => $regla['MetodoEnvioRetraso']['horas_retraso'],
                "hay_retraso " => 0,
            ],
            'group'     => ['Venta.id'],

        ));
    }
}