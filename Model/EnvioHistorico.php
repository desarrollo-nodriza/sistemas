<?php
App::uses('AppModel', 'Model');
class EnvioHistorico extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';

	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'EstadoEnvio' => array(
			'className'				=> 'EstadoEnvio',
			'foreignKey'			=> 'estado_envio_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
        ),
        'TransportesVenta' => array(
			'className'				=> 'TransportesVenta',
			'foreignKey'			=> 'transporte_venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Tienda')
		)
	);
	

	/**
	 * Verifica la existencia de un registro 
	 * dado su estado y transporte
	 * 
	 * @param int $nombre  		Nombre del estado
	 * @param int $transporte_id	Identificador TransportesVenta 
	 * 
	 * @return array
	 */
    public function existe($nombre, $transporte_id)
	{
		return $this->find('first', array(
			'conditions' => array(
				'nombre' => $nombre,
				'transporte_venta_id' => $transporte_id
			)
		));
	}
}