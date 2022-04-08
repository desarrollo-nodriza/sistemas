<?php
App::uses('AppModel', 'Model');
class BodegasMetodoEnvio extends AppModel
{
    public $useTable = 'bodegas_metodo_envio';

    public $belongsTo = array(
		'Bodega' => array(
			'className'				=> 'Bodega',
			'foreignKey'			=> 'bodega_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
		),
        'MetodoEnvio' => array(
			'className'				=> 'MetodoEnvio',
			'foreignKey'			=> 'metodo_envio_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
		),
	);
}
