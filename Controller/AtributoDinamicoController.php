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
        // prx('hola');
        $this->paginate = [
            'recursive' => 0,
            'limit'     => 20,
            'order'     => ['id' => 'DESC']
        ];

        $this->paginate();

        BreadcrumbComponent::add('Atributo Dinámicas');
        $this->set(compact(''));
    }
}
