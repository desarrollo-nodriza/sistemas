<?php

class DteEstado extends AppModel {

    public $useTable        = 'dte_estados';
	public $displayField	= 'nombre';


    public function estadosExistentes()
    {
       return $this->find('list', array(
            'fields' => array(
                'estado',
                'nombre'
            )
        ));
    }
	
}

