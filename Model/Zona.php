<?php

class Zona extends AppModel {

    public $useDbConfig     = "warehouse";
	public $useTable        = 'zonas';
	public $displayField	= 'nombre';
        public $belongsTo = array(
		'Bodega' => array(
			'className'	                => 'Bodega',
			'foreignKey'			=> 'bodega_id',
			'conditions'			=> '',
			'fields'		        => '',
			'order'			        => '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
        ));
}

