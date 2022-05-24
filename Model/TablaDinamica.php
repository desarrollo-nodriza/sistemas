<?php

class TablaDinamica extends AppModel
{

    public $useDbConfig = 'default';
    public $useTable = 'tabla_dinamica';

    public $hasAndBelongsToMany = array(
        'AtributoDinamico' => array(
            'className'             => 'AtributoDinamico',
            'joinTable'             => 'tabla_atributo',
            'foreignKey'            => 'tabla_dinamica_id',
            'associationForeignKey' => 'atributo_dinamico_id',
            'unique'                => true,
            'conditions'            => '',
            'fields'                => '',
            'order'                 => '',
            'limit'                 => '',
            'offset'                => '',
            'finderQuery'           => '',
            'deleteQuery'           => '',
            'insertQuery'           => ''
        ),
        'CategoriaTablaDinamica' => array(
            'className'             => 'CategoriaTablaDinamica',
            'joinTable'             => 'categoria_tabla',
            'foreignKey'            => 'tabla_dinamica_id',
            'associationForeignKey' => 'categoria_tabla_dinamica_id',
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
