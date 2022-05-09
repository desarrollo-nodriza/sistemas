<?php

class TablaAtributo extends AppModel
{

    public $useDbConfig = 'default';
    public $useTable = 'tabla_atributo';

    public $hasMany = array(
        'ValorAtributoCuentaCorrienteTransporte' => array(
            'className'    => 'ValorAtributoCuentaCorrienteTransporte',
            'foreignKey'   => 'tabla_atributo_id',
            'conditions'   => '',
            'fields'       => '',
            'order'        => '',
            'counterCache' => true,
        )
    );
    // public $belongsTo = array(
    //     'ValorAtributoCuentaCorrienteTransporte' => array(
    //         'className'    => 'ValorAtributoCuentaCorrienteTransporte',
    //         'foreignKey'   => 'tabla_atributo_id',
    //         'conditions'   => '',
    //         'fields'       => '',
    //         'order'        => '',
    //         'counterCache' => true,
    //     )
    // );
}
