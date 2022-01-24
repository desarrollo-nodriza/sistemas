<?php
App::uses('AppController', 'Controller');
class ContactosController extends AppController
{

    public function admin_index()
    {
        // Filtrado de ordenes por formulario
        if ($this->request->is('post')) {
            $this->filtro('contactos', 'index', $this->Contacto->alias);
        }

        $condiciones = array();
        $joins       = array();
        $group       = array();
        $fields      = array(
            'Contacto.*'
        );

        # Filtrar
        if (isset($this->request->params['named'])) {
            foreach ($this->request->params['named'] as $campo => $valor) {
                switch ($campo) {
                    case 'administrador_id':

                        $condiciones['Contacto.administrador_id'] = trim($valor);

                        break;

                    case 'id_contacto':

                        $condiciones['Contacto.id'] = trim($valor);

                        break;

                    case 'origen':

                        $condiciones['Contacto.origen'] = trim($valor);

                        break;

                    case 'asunto':

                        $condiciones['Contacto.asunto'] = trim($valor);

                        break;

                    case 'email_contacto':

                        $condiciones['Contacto.email_contacto'] = trim($valor);

                        break;

                    case 'fono_contacto':

                        $condiciones['Contacto.fono_contacto'] = trim($valor);

                        break;

                    case 'nombre_contacto':

                        $condiciones['Contacto.nombre_contacto LIKE'] = '%' . trim($valor) . '%';

                        break;

                    case 'apellido_contacto':

                        $condiciones['Contacto.apellido_contacto LIKE'] = '%' . trim($valor) . '%';

                        break;

                    case 'atendido':

                        $condiciones['Contacto.atendido'] = ($valor == 'si') ? 1 : 0;

                        break;

                    case 'confirmado_cliente':

                        $condiciones['Contacto.confirmado_cliente'] = ($valor == 'si') ? 1 : 0;

                        break;

                    case 'fecha_desde':
                        $condiciones["Contacto.created >="] = $valor;
                        break;

                    case 'fecha_hasta':
                        $condiciones["Contacto.created <="] = $valor;
                        break;
                }
            }
        }

        $paginate = array(
            'recursive' => 0,
            'conditions' => $condiciones,
            'contain' => array(
                'Administrador' => array(
                    'fields' => array(
                        'Administrador.email'
                    )
                )
            ),
            'joins' => $joins,
            'fields' => $fields,
            'order' => array('Contacto.created' => 'DESC'),
            'limit' => 20
        );

        $this->paginate = $paginate;
        $contactos          = $this->paginate();

        $administradores = $this->Contacto->Administrador->find('list', array(
            'conditions' => array(
                'Administrador.activo' => 1,
            ),
            'fields' => array(
                'Administrador.id',
                'Administrador.email'
            )
        ));

        $asuntos = ClassRegistry::init('Asuntos')->find(
            "list",
            [
                'fields' =>
                ['nombre', 'nombre'],
                'conditions' => ['activo' => true]
            ]
        );
        $origenes = $this->Contacto->origenes();
        BreadcrumbComponent::add('Contactos', '/contactos');

        $this->set(compact('contactos', 'administradores', 'origenes', 'asuntos'));
    }

    /**
     * Notificaer cliente
     */
    public function admin_notificar_cliente($id)
    {
        if (!$this->Contacto->exists($id)) {
            $this->Session->setFlash('Registro no encontrado.', null, array(), 'danger');
            $this->redirect($this->referer('/', true));
        }

        $contacto = $this->Contacto->find('first', array(
            'conditions' => array(
                'Contacto.id' => $id
            ),
            'contain' => array(
                'Tienda',
                'VentaCliente',
                'Administrador'
            )
        ));

        if (!$this->notificar_cliente($contacto)) {
            $this->Session->setFlash('No fue posible notificar al cliente. Intente nuevamente.', null, array(), 'warning');
            $this->redirect($this->referer('/', true));
        }

        $this->Session->setFlash(sprintf('Contacto n°%d notificado con éxito a %s.', $contacto['Contacto']['id'], $contacto['Contacto']['email_contacto']), null, array(), 'success');
        $this->redirect($this->referer('/', true));
    }

    /**
     * Atender contacto desde admin
     */
    public function admin_atender($id)
    {

        if (!$this->Contacto->exists($id)) {
            $this->Session->setFlash('Registro no encontrado.', null, array(), 'danger');
            $this->redirect($this->referer('/', true));
        }

        $token = $this->Session->read('Auth.Administrador.token.token');

        App::uses('HttpSocket', 'Network/Http');
        $socket  = new HttpSocket();
        $request = $socket->post(
            Router::url('/api/contactos/attend.json?token=' . $token, true),
            array(
                'contacto_id' => $id
            )
        );

        $respuesta = json_decode($request->body(), true);

        if (!isset($respuesta['response'])) {
            $this->Session->setFlash($respuesta['message'], null, array(), 'danger');
            $this->redirect($this->referer('/', true));
        }

        if ($respuesta['response']['code'] != 200) {
            $this->Session->setFlash('No fue posible atender este contacto. Póngase en contacto con el equipo TI.', null, array(), 'danger');
            $this->redirect($this->referer('/', true));
        }

        $this->Session->setFlash('Contacto procesado con éxito.', null, array(), 'success');
        $this->redirect($this->referer('/', true));
    }

    /**
     * Ver contacto
     */
    public function admin_view($id)
    {
        if (!$this->Contacto->exists($id)) {
            $this->Session->setFlash('Registro no encontrado.', null, array(), 'danger');
            $this->redirect($this->referer('/', true));
        }

        $contacto = $this->Contacto->find('first', array(
            'conditions' => array(
                'Contacto.id' => $id
            ),
            'contain' => array(
                'Tienda',
                'VentaCliente',
                'Administrador'
            )
        ));

        BreadcrumbComponent::add('Contactos', '/contactos');
        BreadcrumbComponent::add('Ver contacto');

        $this->set(compact('contacto'));
    }

    /**
     * Api crear contacto
     */
    public function api_add()
    {
        # Sólo método post
        if (!$this->request->is('post')) {
            $response = array(
                'code'    => 501,
                'message' => 'Only POST request allow'
            );

            throw new CakeException($response);
        }

        # Existe token
        if (!isset($this->request->query['token'])) {
            $response = array(
                'code'    => 502,
                'message' => 'Expected Token'
            );

            throw new CakeException($response);
        }

        # Validamos token
        if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
            $response = array(
                'code'    => 401,
                'message' => 'Invalid or expired Token'
            );

            throw new CakeException($response);
        }

        # Validamos los campo
        if (!isset($this->request->data['origen'])) {
            $response = array(
                'code'    => 401,
                'message' => 'Origen es requerido'
            );

            throw new CakeException($response);
        }

        if (!isset($this->request->data['asunto'])) {
            $response = array(
                'code'    => 401,
                'message' => 'Asunto es requerido'
            );

            throw new CakeException($response);
        }

        if (!isset($this->request->data['mensaje'])) {
            $response = array(
                'code'    => 401,
                'message' => 'Mensaje es requerido'
            );

            throw new CakeException($response);
        }

        if (!isset($this->request->data['email_contacto'])) {
            $response = array(
                'code'    => 401,
                'message' => 'Email es requerido'
            );

            throw new CakeException($response);
        }

        if (!isset($this->request->data['fono_contacto'])) {
            $response = array(
                'code'    => 401,
                'message' => 'Fono es requerido'
            );

            throw new CakeException($response);
        }

        if (!isset($this->request->data['nombre_contacto'])) {
            $response = array(
                'code'    => 401,
                'message' => 'Nombre es requerido'
            );

            throw new CakeException($response);
        }

        if (!isset($this->request->data['apellido_contacto'])) {
            $response = array(
                'code'    => 401,
                'message' => 'Apellido es requerido'
            );

            throw new CakeException($response);
        }

        if (isset($this->request->data['tienda_id']) && !ClassRegistry::init('Tienda')->exists($this->request->data['tienda_id'])) {
            $response = array(
                'code'    => 401,
                'message' => 'Tienda no existe'
            );

            throw new CakeException($response);
        }

        # Varificamos la existencia del cliente en el sistema
        # si no existe, se crea
        $cliente =  ClassRegistry::init('VentaCliente')->find('first', array(
            'conditions' => array(
                'VentaCliente.email' => $this->request->data['email_contacto']
            ),
            'fields' => array(
                'VentaCliente.id'
            )
        ));

        $cliente_id = '';
        $tienda_id = '';

        # Se crea cliente
        if (empty($cliente)) {

            $nwCliente = array(
                'VentaCliente' => array(
                    'nombre'   => $this->request->data['nombre_contacto'],
                    'apellido' => $this->request->data['apellido_contacto'],
                    'email'    => $this->request->data['email_contacto'],
                    'telefono' => $this->request->data['fono_contacto'],
                )
            );

            ClassRegistry::init('VentaCliente')->create();
            $cliente = ClassRegistry::init('VentaCliente')->save($nwCliente);
        }

        $cliente_id = $cliente['VentaCliente']['id'];

        # Se obtiene tienda
        if (isset($this->request->data['tienda_id'])) {
            $tienda_id = $this->request->data['tienda_id'];
        } else {
            $tienda = ClassRegistry::init('Tienda')->tienda_principal(array('Tienda.id'));
            $tienda_id = $tienda['Tienda']['id'];
        }

        # Guardamos el contacto
        $contacto = array(
            'Contacto' => array(
                'tienda_id'         => $tienda_id,
                'cliente_id'        => $cliente_id,
                'origen'            => $this->request->data['origen'],
                'asunto'            => $this->request->data['asunto'],
                'mensaje'           => $this->request->data['mensaje'],
                'email_contacto'    => $this->request->data['email_contacto'],
                'fono_contacto'     => $this->request->data['fono_contacto'],
                'nombre_contacto'   => $this->request->data['nombre_contacto'],
                'apellido_contacto' => $this->request->data['apellido_contacto']
            )
        );

        $existe_asunto = ClassRegistry::init('Asunto')->find('first', [
            'conditions' => ['nombre' => $this->request->data['asunto']],

        ]);

        // TODO Si no existe el asunto se crea
        if (!$existe_asunto) {

            ClassRegistry::init('Asunto')->create();
            ClassRegistry::init('Asunto')->save(['Asunto' => [
                'nombre' => $this->request->data['asunto']
            ]]);
        }

        // TODO Obtenemos administradores para el asuntos solicitado
        $NotificarAsunto = ClassRegistry::init('NotificarAsunto')->atencion_cliente_ids($this->request->data['asunto']);

        if (!$this->Contacto->save($contacto)) {
            $response = array(
                'code'    => 401,
                'name' => 'error',
                'message' => 'No fue posible guardar el mensaje. Intente nuevamente.'
            );

            throw new CakeException($response);
        } else {

            $contactoCreado = $this->Contacto->find('first', array(
                'conditions' => array(
                    'Contacto.id' => $this->Contacto->id
                ),
                'contain' => array(
                    'Tienda' => array(
                        'fields' => array(
                            'Tienda.id',
                            'Tienda.nombre',
                            'Tienda.mandrill_apikey'
                        )
                    )
                )
            ));
            if (!$NotificarAsunto) {
                ClassRegistry::init('Log')->create();
                ClassRegistry::init('Log')->save(
                    [
                        'Log' =>
                        [
                            'administrador' => "Problemas para notificar asunto {$this->request->data['asunto']}",
                            'modulo'        => 'ContactosController',
                            'modulo_accion' => "No se han encontrado correos para notificar"
                        ]
                    ]
                );
            } else {

                $contactoCreado['Administrador']['email'] = $NotificarAsunto;
                $this->notificar_vendedor($contactoCreado);
            }
        }

        $this->set(array(
            'response' => array(
                'code' => 200,
                'name' => 'success',
                'message' => 'Mensaje guardado exitosamente.'
            ),
            '_serialize' => array('response')
        ));
    }

    /**
     * Envia el email al cliente para que confirme si fue atendido o no
     * @param array Contacto
     * @return bool
     */
    public function notificar_cliente($contacto)
    {
        # creamos un token de acceso vía email por 30 días
        $token = ClassRegistry::init('Token')->crear_token_cliente($contacto['Contacto']['cliente_id'], $contacto['Contacto']['tienda_id'], 168)['token'];
       
        if (empty($token)) {
            return false;
        }

        $this->View             = new View();
        $this->View->viewPath   = 'Contactos' . DS . 'emails';
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


    /**
     * Envia el email al cliente para que confirme si fue atendido o no
     * @param array Contacto
     * @return bool
     */
    public function notificar_vendedor_rechazo($contacto)
    {

        $this->View                    = new View();
        $this->View->viewPath        = 'Contactos' . DS . 'emails';
        $this->View->layoutPath        = 'Correos' . DS . 'html';

        $url = obtener_url_base();

        $this->View->set(compact('contacto', 'url', 'token'));
        $html = $this->View->render('notificar_vendedor');

        $mandrill_apikey = $contacto['Tienda']['mandrill_apikey'];

        if (empty($mandrill_apikey)) {
            return false;
        }

        $mandrill = $this->Components->load('Mandrill');

        $mandrill->conectar($mandrill_apikey);

        $asunto = sprintf('[CS %s] Cliente respondió el contacto - id #%d - %s', $contacto['Tienda']['nombre'], $contacto['Contacto']['id'], date('Y-m-d H:i:s'));

        if (Configure::read('ambiente') == 'dev') {
            $asunto = sprintf('[CS %s - DEV] Cliente respondió el contacto - id #%d - %s', $contacto['Tienda']['nombre'], $contacto['Contacto']['id'], date('Y-m-d H:i:s'));
        }

        $remitente = array(
            'email' => 'clientes@nodriza.cl',
            'nombre' => 'Servicio al cliente ' . $contacto['Tienda']['nombre']
        );

        $destinatarios = array();

        $destinatarios[] = array(
            'email' => $contacto['Administrador']['email'],
            'type' => 'to'
        );

        return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
    }


    /**
     * Envia el email al cliente para que confirme si fue atendido o no
     * @param array Contacto
     * @return bool
     */
    public function notificar_vendedor($contacto)
    {

        $this->View             = new View();
        $this->View->viewPath   = 'Contactos' . DS . 'emails';
        $this->View->layoutPath = 'Correos' . DS . 'html';

        $url = obtener_url_base();

        $this->View->set(compact('contacto', 'url', 'token'));
        $html = $this->View->render('notificar_vendedor_nuevo_contacto');

        $mandrill_apikey = $contacto['Tienda']['mandrill_apikey'];

        if (empty($mandrill_apikey)) {
            return false;
        }

        $mandrill = $this->Components->load('Mandrill');

        $mandrill->conectar($mandrill_apikey);

        $asunto = sprintf('[CS %s] Un cliente espera ser atendido - número de Contacto id #%d - %s', $contacto['Tienda']['nombre'], $contacto['Contacto']['id'], date('Y-m-d H:i:s'));

        if (Configure::read('ambiente') == 'dev') {
            $asunto = sprintf('[CS %s - DEV] Un cliente espera ser atendido - número de Contacto id #%d - %s', $contacto['Tienda']['nombre'], $contacto['Contacto']['id'], date('Y-m-d H:i:s'));
        }

        $remitente = array(
            'email' => 'clientes@nodriza.cl',
            'nombre' => 'Servicio al cliente ' . $contacto['Tienda']['nombre']
        );

        $destinatarios = array();

        foreach ($contacto['Administrador']['email'] as $value) {
            $destinatarios[] = array(
                'email' => $value,
                'type' => 'to'
            );
        }

        return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
    }


    /**
     * Permite marcar un contacto como atendido por un vendedor
     * @return mixed
     */
    public function api_atender()
    {
        # Sólo método post
        if (!$this->request->is('post')) {
            $response = array(
                'code'    => 501,
                'message' => 'Only POST request allow'
            );

            throw new CakeException($response);
        }

        # Existe token
        if (!isset($this->request->query['token'])) {
            $response = array(
                'code'    => 502,
                'message' => 'Expected Token'
            );

            throw new CakeException($response);
        }

        # Validamos token
        if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
            $response = array(
                'code'    => 401,
                'message' => 'Invalid or expired Token'
            );

            throw new CakeException($response);
        }


        if (!isset($this->request->data['contacto_id'])) {
            $response = array(
                'code'    => 401,
                'message' => 'contacto_id es requerido'
            );

            throw new CakeException($response);
        }

        $id = $this->request->data['contacto_id'];

        if (!$this->Contacto->exists($id)) {
            $response = array(
                'code'    => 404,
                'message' => 'Contacto no encontrado'
            );

            throw new CakeException($response);
        }

        $contacto =  $this->Contacto->find('first', array(
            'conditions' => array(
                'Contacto.id' =>  $id
            ),
            'contain' => array(
                'Tienda' => array(
                    'fields' => array(
                        'Tienda.id',
                        'Tienda.nombre',
                        'Tienda.direccion',
                        'Tienda.mandrill_apikey'
                    )
                )
            )
        ));

        # Guardamos como atendido y notificamos a cliente
        $contacto['Contacto']['atendido'] = 1;
        $contacto['Contacto']['fecha_atendido'] = date('Y-m-d H:i:s');
        $contacto['Contacto']['confirmado_cliente'] = 0;
        $contacto['Contacto']['fecha_confirmado_cliente'] = null;
        $contacto['Contacto']['fecha_no_confirmado_cliente'] = null;

        if (!$this->Contacto->save($contacto)) {
            $response = array(
                'code'    => 401,
                'message' => 'No se pudo actualizar el contacto'
            );

            throw new CakeException($response);
        }

        # Notificamos
        if (!$this->notificar_cliente($contacto)) {
            $response = array(
                'code'    => 401,
                'message' => 'No fue posible notificar al cliente. Intente notificarlo manualmente.'
            );

            throw new CakeException($response);
        }

        $this->set(array(
            'response' => array(
                'code' => 200,
                'name' => 'success',
                'message' => 'Contacto finalizado con éxito.'
            ),
            '_serialize' => array('response')
        ));
    }


    /**
     * Permite la confirmación por parte del cliente del
     * contacto mediante un link
     */
    public function cliente_confirmar($id)
    {
        $error = '';

        $PageTitle = 'Confirmar atención';

        $tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
            'Tienda.id', 'Tienda.nombre', 'Tienda.logo', 'Tienda.url', 'Tienda.whatsapp_numero'
        ));

        $contacto = $this->Contacto->find('first', array(
            'conditions' => array(
                'Contacto.id' => $id,
                'Contacto.confirmado_cliente' => 0
            ),
            'contain' => array(
                'Administrador',
                'Tienda' => array(
                    'fields' => array(
                        'Tienda.id',
                        'Tienda.nombre',
                        'Tienda.mandrill_apikey'
                    )
                )
            )
        ));

        if (!isset($this->request->query['access_token'])) {
            $error = 'No tienes permitido acceder a esta sección. Por favor ponte en contacto con nuestro equipo vía whatsapp al ' . $tienda['Tienda']['whatsapp_numero'] . '.';
            $this->set(compact('PageTitle', 'error', 'tienda', 'contacto'));
            return;
        }

        $token = $this->request->query['access_token'];

        try {
            $token_valido = ClassRegistry::init('Token')->validar_token($token);
        } catch (Exception $e) {
            $token_valido = 0;
        }

        # Validamos el token
        if (!$token_valido) {
            $error = 'El token de acceso no es válido o está caduco. Si ya se resolvió su requerimiento, ignore este mensaje. De lo contrario ponte en contacto con nosotros vía whatsapp al ' . $tienda['Tienda']['whatsapp_numero'] . '.';
            $this->set(compact('PageTitle', 'error', 'tienda', 'contacto'));
            return;
        }

        if (!isset($this->request->query['action'])) {
            $error = 'No tienes permitido acceder a esta sección. Por favor ponte en contacto con nuestro equipo vía whatsapp al ' . $tienda['Tienda']['whatsapp_numero'] . '.';
            $this->set(compact('PageTitle', 'error', 'tienda', 'contacto'));
            return;
        }

        # si existe confirmamos según corresponda
        if (!empty($contacto)) {
            $this->Contacto->id = $id;

            if ($this->request->query['action'] == 'ok') {
                $this->Contacto->savefield('confirmado_cliente', 1);
                $this->Contacto->savefield('atendido', 1);
                $this->Contacto->savefield('fecha_confirmado_cliente', date('Y-m-d H:i:s'));
                $this->Contacto->savefield('fecha_no_confirmado_cliente', null);
            } else {
                $this->Contacto->savefield('confirmado_cliente', 0);
                $this->Contacto->savefield('atendido', 0);
                $this->Contacto->savefield('fecha_confirmado_cliente', null);
                $this->Contacto->savefield('fecha_no_confirmado_cliente', date('Y-m-d H:i:s'));

                $this->notificar_vendedor_rechazo($contacto);
            }
        } else {
            $error = 'Ocurrió un error al intentar confirmar su petición o su caso ya se encuentra resuelto.';
        }

        $this->set(compact('PageTitle', 'error', 'contacto', 'tienda'));
    }

    public function admin_relacionar_contacto_administror($id)
    {
      
        $contacto = $this->Contacto->find('first', array(
            'conditions' => array(
                'Contacto.id' =>  $id,
            ),
            'fields' => [
                'Contacto.id',
                'Contacto.administrador_id'
            ]
        ));
     
        if (is_null($contacto['Contacto']['administrador_id'])) {
         
            $contacto['Contacto']['administrador_id'] = CakeSession::read('Auth.Administrador.id');
           
            if ($this->Contacto->save($contacto)) {
                $this->Session->setFlash(
                    'Contacto a sido asignado al usuario ' . CakeSession::read('Auth.Administrador.email'),
                    null,
                    array(),
                    'success'
                );

                $this->redirect(array('action' => 'view', $id));
            }
        }
        
        $this->Session->setFlash(
            'Contacto ya a sido atendido por otro usuario',
            null,
            array(),
            'danger'
        );

        $this->redirect(array('action' => 'index'));
    }
}
