<?php

App::uses('AppController', 'Controller');

class ReglasGenerarOCController extends AppController
{


    public $helpers = array('Html', 'Form');

    public function filtrar($controlador = '', $accion = '')
    {
        $redirect = array(
            'controller' => $controlador,
            'action' => $accion
        );

        foreach ($this->request->data['Filtro'] as $campo => $valor) {
            if ($valor != '') {
                $redirect[$campo] = str_replace('/', '-', $valor);
            }
        }

        $this->redirect($redirect);
    }

    public function admin_index()
    {
        $filtro = [];

        if ($this->request->is('post')) {
        }
        $this->paginate = [
            'recursive' => 0,
            'limit'     => 20,
            'order'     => ['id' => 'DESC']
        ];
        $reglasGenerarOC =  $this->paginate();

        $moneda = ClassRegistry::init('Moneda')->find('list', ['conditions' => ['Moneda.activo' => true]]);

        $tiendas = ClassRegistry::init('Tienda')->find('all');

        // BreadcrumbComponent::add('Atributo Dinámicos');
        // prx($tienda);
        $administradores = ClassRegistry::init('Administrador')->find('list', ['conditions' => ['Administrador.activo' => true]]);
        $this->set(compact('reglasGenerarOC', 'moneda', 'tiendas', 'administradores'));
    }

    public function admin_regla_create()
    {

        $reglas = array_filter($this->request->data, function ($v, $k) {
            return !empty($v['medio_pago_id']);
        }, ARRAY_FILTER_USE_BOTH);
        // prx($reglas);
        $datos_a_guardar = [];

        foreach ($reglas as  $value) {
            $datos_a_guardar[] = ['ReglasGenerarOC' => $value];
        }

        ClassRegistry::init('ReglasGenerarOC')->create();
        ClassRegistry::init('ReglasGenerarOC')->saveAll($datos_a_guardar);

        $this->redirect(array('action' => 'index'));
    }

    public function admin_configuracion_tienda()
    {

        $datos_a_guardar = [];
        foreach ($this->request->data as $tienda) {
            $datos_a_guardar[] = ['Tienda' => $tienda];
        }
        // prx($datos_a_guardar);
        ClassRegistry::init('Tienda')->create();
        ClassRegistry::init('Tienda')->saveAll($datos_a_guardar);
        $this->redirect(array('action' => 'index'));
    }
}
