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
     * 
     * Obtiene las ventas que tengan un método de envio configurado para notificar el retraso.
     * 
     * El parámetro $retraso indica cuantas horas ha permanecido la venta en un mismo estado
     *
     * @param  int $retraso Horas de retraso de las ventas que queremos obtener 
     * @return array
     */
    public function obtener_ventas_por_retraso(int $bodega, int $retraso)
    {   
        # consultamos las reglas
        $reglas = $this->MetodoEnvioRetraso->find('all', array(
            'conditions' => array(
                'bodega_id' => $bodega,
                'horas_retraso' => $retraso,
                'notificar_retraso' => 1
            )
        ));
        
        $ids_estados = array_unique(Hash::extract($reglas, '{n}.MetodoEnvioRetraso.venta_estado_categoria_id'));
        $ids_metodo_envios = array_unique(Hash::extract($reglas, '{n}.MetodoEnvioRetraso.metodo_envio_id'));
        
        # Obtenemos las ventas filtradas
        $ventas = ClassRegistry::init('Venta')->find('all', array(
            'joins' => array(
                array(
                    'table' => 'rp_metodo_envios',
                    'alias' => 'metodo_envios',
                    'type' => 'INNER',
                    'conditions' => array(
                        'metodo_envios.id = Venta.metodo_envio_id',
                        'metodo_envios.notificar_retraso' => 1
                    )
                ),
                array(
                    'table' => 'rp_venta_estados',
                    'alias' => 'venta_estados',
                    'type' => 'INNER',
                    'conditions' => array(
                        'venta_estados.id = Venta.venta_estado_id',
                        'venta_estados.venta_estado_categoria_id' => $ids_estados
                    )
                ),
                array(
                    'table' => 'rp_estados_ventas',
                    'alias' => 'estados_ventas',
                    'type' => 'INNER',
                    'conditions' => array(
                        'estados_ventas.venta_id = Venta.id'
                    )
                )
            ),
            'conditions' => array(
                'Venta.bodega_id' => $bodega,
                'Venta.marketplace_id' => null,
                'Venta.metodo_envio_id' => $ids_metodo_envios
            ),
            'contain' => array(
                'VentaEstado2' => array(
                    'fields' => array(
                        'VentaEstado2.id',
                        'VentaEstado2.nombre'
                    ),
                    'order' => array(
                        'EstadosVenta.fecha DESC'
                    )
                )
            ),
            'fields' => array(
                'Venta.id',
                'Venta.venta_estado_id',
                'Venta.fecha_venta',
            ),
            'group' => array('Venta.id'),
            'order' => array(
                'estados_ventas.fecha ASC'
            )
        ));

        $result = array();

        # Comparamos las fechas
        foreach ($ventas as $v)
        {      
            # si el ultimo historico no es igual al estado actual de la venta, se omite
            if ($v['Venta']['venta_estado_id'] != $v['VentaEstado2'][0]['EstadosVenta']['venta_estado_id'])
                continue;

            $horas_desde_el_cambio = $this->calcular_horas_retraso($v['VentaEstado2'][0]['EstadosVenta']['fecha']); 
            
            if ($horas_desde_el_cambio > $retraso)
            {
                $result[] = $v;
            }
        }

        return $result;
    }
    
    /**
     * crear_registro_retraso
     * 
     * Registra el retraso para luego ser notificado
     *
     * @param  mixed $venta_id
     * @return bool
     */
    public function crear_registro_retraso(int $venta_id)
    {
        $venta = ClassRegistry::init('Venta')->find('first', array(
            'conditions' => array(
                'Venta.id' => $venta_id
            ),
            'contain' => array(
                'VentaEstado2' => array(
                    'fields' => array(
                        'VentaEstado2.id',
                        'VentaEstado2.nombre'
                    ),
                    'order' => array(
                        'EstadosVenta.fecha DESC'
                    )
                )
            ),
            'fields' => array(
                'Venta.id',
                'Venta.referencia',
                'Venta.fecha_venta',
                'Venta.total',
                'Venta.tienda_id',
                'Venta.bodega_id',
                'Venta.venta_estado_id',
                'Venta.venta_cliente_id'
            )
        ));

        $embalajes = $this->WarehouseNodriza->ObtenerEmbalajesVenta($venta_id);

        $registros = [];

        $horas_desde_el_cambio = $this->calcular_horas_retraso($venta['VentaEstado2'][0]['EstadosVenta']['fecha']); 

        if ($embalajes['code'] == 200)
        {
            foreach ($embalajes['response']['body'] as $embalaje)
            {
                if (in_array($embalaje['estado'], array('cancelado', 'finalizado', 'entregado', 'despachado')))
                    continue;

                # Se crea un registro por cada embalaje retrasado
                $registros[] = array(
                    'RetrasoEmbalaje' => array(
                        'venta_id' => $venta_id,
                        'embalaje_id' => $embalaje['id'],
                        'venta_estado_id' => $venta['Venta']['venta_estado_id'],
                        'venta_cliente_id' => $venta['Venta']['venta_cliente_id'],
                        'horas_retraso' => $horas_desde_el_cambio
                    )
                );
            }
        }

        
        # Si no hay embalajes para notificar se registra solamente el retraso de la venta
        if (empty($registros))
        {
            $registros[] = array(
                'RetrasoEmbalaje' => array(
                    'venta_id' => $venta_id,
                    'embalaje_id' => null,
                    'venta_estado_id' => $venta['Venta']['venta_estado_id'],
                    'venta_cliente_id' => $venta['Venta']['venta_cliente_id'],
                    'horas_retraso' => $horas_desde_el_cambio
                )
            );
        }

        return $embalajes;
    }

    public function admin_index()
    {
        $ventas = $this->obtener_ventas_por_retraso(1, 240);
        $embalajes = [];
        foreach($ventas as $venta)
        {
            $embalajes[] = $this->crear_registro_retraso($venta['Venta']['id']);
        }
        prx($embalajes);
    }

}