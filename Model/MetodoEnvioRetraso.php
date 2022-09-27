<?php
App::uses('AppModel', 'Model');
class MetodoEnvioRetraso extends AppModel
{
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

	public function reglas_activas()
	{
		return $this->find('all', array(
			'joins' => array(
				array(
					'table' => 'rp_metodo_envios',
					'alias' => 'MetodoEnvio',
					'type' => 'INNER',
					'conditions' => array(
						'MetodoEnvioRetraso.metodo_envio_id = MetodoEnvio.id',
					)
				),
			),
			'conditions' => array(
				'MetodoEnvio.notificar_retraso' => 1,
				'MetodoEnvio.activo' => 1,
			),
		));
	}
}
