<?php
App::uses('AppModel', 'Model');
class MetodoEnvio extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';


	private static $dependencia = array(
		'starken' => 'Starken/Turbus',
		'conexxion' => 'Conexxion Api',
		'boosmap' => 'Boosmap Api'
	);


	/**
	 * ASOCIACIONES
	 */
	public $hasMany = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'metodo_envio_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		)
	);


	/**
	 * Indica si nececita usar algun currier externo
	 * @param  string $dependencia [description]
	 * @return [type]              [description]
	 */
	public function dependencias($dependencia = '')
	{
		if (!empty($dependencia)) {
			return (isset(self::$dependencia[$dependencia])) ? self::$dependencia[$dependencia] : null;
		}else{
			return self::$dependencia;
		}
	}


	/**
	 * [obtener_metodo_envio_por_nombre description]
	 * @param  string $nombre [description]
	 * @return [type]         [description]
	 */
	public function obtener_metodo_envio_por_nombre($nombre = '')
	{
		return $this->find('first', array(
			'conditions' => array(
				'MetodoEnvio.nombre' => trim($nombre)
				)
			)
		);
	}


	/**
	 * [es_despacho description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function es_despacho($id)
	{	
		$this->id = $id;
		return $this->field('es_despacho');
	}
}
