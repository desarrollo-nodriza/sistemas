<?php

Class Zonificacion extends AppModel {

	/**
	 * Set Cake config DB
	 */
    public $useDbConfig = 'warehouse';
	public $useTable = 'zonificaciones';
	public $primaryKey = 'id';

	public $belongsTo = array(
        'Ubicacion' => array(
            'className' => 'Ubicacion',
            'foreignKey' => 'ubicacion_id'
        )
    );

	
	
}