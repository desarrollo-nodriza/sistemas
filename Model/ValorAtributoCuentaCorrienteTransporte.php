<?php

class ValorAtributoCuentaCorrienteTransporte extends AppModel
{

    public $useDbConfig = 'default';
    public $useTable = 'valor_atributo_cuenta_corriente_transporte';

    public $belongsTo = array(
        'TablaAtributo' => array(
            'className' => 'TablaAtributo',
            'foreignKey' => 'tabla_atributo_id'
        )
    );
}
