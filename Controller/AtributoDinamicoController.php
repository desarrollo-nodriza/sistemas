<?php

App::uses('AppController', 'Controller');

class AtributoDinamicoController extends AppController
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
        $AtributoDinamico =  $this->paginate();
        BreadcrumbComponent::add('Atributo Dinámicos');

        $this->set(compact('AtributoDinamico'));
    }

    public function admin_atributo_create()
    {

        $atributos = array_filter($this->request->data, function ($v, $k) {
            return !empty($v['nombre']) || !empty($v['id']);
        }, ARRAY_FILTER_USE_BOTH);
        // prx($atributos);
        $datos_a_guardar = [];

        foreach ($atributos as  $value) {
            $datos_a_guardar[] = ['AtributoDinamico' => $value];
        }

        ClassRegistry::init('AtributoDinamico')->create();
        ClassRegistry::init('AtributoDinamico')->saveAll($datos_a_guardar);

        $this->redirect(array('action' => 'index'));
    }
}
