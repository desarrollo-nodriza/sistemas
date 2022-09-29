<?php
App::uses('AppController', 'Controller');
class MetodoEnvioRetrasosController extends AppController
{
    public $components = array(
        'RequestHandler',
        'Mandrill'
    );

    public function admin_index()
    {
        prx($this->notificar_retraso());
        $retrasos_sin_motificar = ClassRegistry::init('RetrasoVenta')->find('all', [
            'contain' => [
                'VentaCliente',
                'Venta'
            ]
        ]);
        prx($retrasos_sin_motificar);
    }
    /**
     * crear_registro_retrasos
     * Busca las ventas que tengan retraso respecto a una regla y crea un registro para que después sea notificado
     * @return array
     */
    public function crear_registro_retrasos()
    {
        $ventas     = $this->obtener_ventas_por_retraso();
        $retrasos   = [];

        foreach ($ventas as $venta) {

            $retrasos[] = array(
                'RetrasoVenta' => array(
                    'venta_id'          => $venta['Venta']['id'],
                    'venta_estado_id'   => $venta['Venta']['venta_estado_id'],
                    'venta_cliente_id'  => $venta['Venta']['venta_cliente_id'],
                    'horas_retraso'     => $venta[0]['horas_retraso'],
                )
            );
        }

        if ($retrasos) {
            ClassRegistry::init('RetrasoVenta')->create();
            ClassRegistry::init('RetrasoVenta')->saveAll($retrasos);
        }

        return $retrasos;
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

    /**
     * notificar_retraso
     * Busca los retraso que se han creado pero que no han sido notificados
     * @return array
     */
    public function notificar_retraso()
    {
        $retrasos_sin_motificar = ClassRegistry::init('RetrasoVenta')->find('all', [
            'conditions' => [
                'notificado' => false,
            ],
            'contain' => [
                'VentaCliente'  => [
                    'id',
                    'email',
                    'nombre',
                    'apellido',
                ],
                'Venta'         => [
                    'id',
                    'referencia',
                ],
            ]
        ]);

        $notificado         = [];
        $fecha_notificado   = date('Y-m-d H:i:s');
        foreach ($retrasos_sin_motificar as $retraso) {

            $respuesta = $this->enviar_email($retraso);
            if ($respuesta) {
                $notificado[] = [
                    'RetrasoVenta' => [
                        'id'                => $retraso['RetrasoVenta']['id'],
                        'notificado'        => $respuesta,
                        'fecha_notificado'  => $fecha_notificado,
                    ]
                ];
            }
        }

        if ($notificado) {
            ClassRegistry::init('RetrasoVenta')->create();
            ClassRegistry::init('RetrasoVenta')->saveAll($notificado);
        }

        return $notificado;
    }

    /**
     * enviar_email
     * Notifica al cliente un retraso en su venta 
     * @param  mixed $retraso
     * @return bool
     */
    public function enviar_email(array $retraso)
    {

        $this->View             = new View();
        $this->View->viewPath   = 'RetrasoVenta' . DS . 'emails';
        $this->View->layoutPath = 'Correos' . DS . 'html';
        $tienda = ClassRegistry::init('Tienda')->tienda_principal();
        $mandrill_apikey = $tienda['Tienda']['mandrill_apikey'] ?? null;

        if (empty($mandrill_apikey)) {
            return false;
        }

        $url = obtener_url_base();
        $this->View->set(compact('retraso', 'url', 'tienda'));
        $html = $this->View->render('retraso');
        $mandrill = $this->Components->load('Mandrill');
        $mandrill->conectar($mandrill_apikey);
        $asunto = sprintf("%sInformación importante de tu compra #{$retraso['Venta']['referencia']} %s", Configure::read('ambiente') == 'dev' ? "[DEV] " : "", date('Y-m-d H:i:s'));

        $remitente = array(
            'email'     => 'no-reply@nodriza.cl',
            'nombre'    => 'Ventas Toolmanía'
        );

        $destinatarios = array();
        if (Configure::read('ambiente') == 'dev') {
            $destinatarios = [
                // [
                //     'email' => "diego.romero@nodriza.cl",
                //     'type' => 'to'
                // ],
                [
                    'email' => "desarrollo@nodriza.cl",
                    'type' => 'to'
                ]
            ];
        } else {
            $destinatarios[] = array(
                'email' => $retraso['VentaCliente']['email'],
                'type' => 'to'
            );
        }


        return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
    }
}
