<?php

class Ubicacion extends AppModel {

        public $useDbConfig     = "warehouse";
		public $useTable        = 'ubicaciones';

        public $belongsTo = array(
		'Zona' => array(
			'className'	                => 'Zona',
			'foreignKey'			=> 'zona_id',
			'conditions'			=> '',
			'fields'		        => '',
			'order'			        => '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
        ));
}

