<?php

class CategoriaTablaDinamica extends AppModel
{

    public $useDbConfig     = 'default';
    public $useTable        = 'categoria_tabla_dinamica';
    public $displayField    = 'nombre';

    public $hasMany = array(
        'TablaDinamica' => array(
            'className'             => 'TablaDinamica',
            'joinTable'             => 'categoria_tabla',
            'foreignKey'            => 'categoria_tabla_dinamica_id',
            'associationForeignKey' => 'tabla_dinamica_id',
            'unique'                => true,
            'conditions'            => '',
            'fields'                => '',
            'order'                 => '',
            'limit'                 => '',
            'offset'                => '',
            'finderQuery'           => '',
            'deleteQuery'           => '',
            'insertQuery'           => ''
        )
    );
}
