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

        $medio_de_pago = ClassRegistry::init('MedioPago')->find('list', ['conditions' => ['MedioPago.activo' => true]]);

        // BreadcrumbComponent::add('Atributo Dinámicos');

        $this->set(compact('reglasGenerarOC', 'medio_de_pago'));
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
}
