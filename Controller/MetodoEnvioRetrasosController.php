<?php
App::uses('AppController', 'Controller');
class MetodoEnvioRetrasosController extends AppController
{   
    public $components = array(
		'RequestHandler',
		'WarehouseNodriza',
        'Mandrill'
	);

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

}