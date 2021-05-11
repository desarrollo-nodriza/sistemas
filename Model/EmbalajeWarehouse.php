<?php 
App::uses('AppModel', 'Model');

Class EmbalajeWarehouse extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $useDbConfig = 'warehouse';
	public $useTable = 'embalajes';
	public $displayField	= 'id';


	private $estados = array(
		'inicial' => 'Inicial',
		'en_revision' => 'En revisión manual',
		'listo_para_embalar' => 'Listo para embalar',
		'procesando' => 'En preparación',
		'finalizado' => 'Finalizado',
		'cancelado' => 'Cancelado'
	);


	/**
	 * Asosiaciones
	 * @var array
	 */
	public $belongsTo = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
        ),
        'Bodega' => array(
			'className'				=> 'Bodega',
			'foreignKey'			=> 'bodega_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
        'MetodoEnvio' => array(
			'className'				=> 'MetodoEnvio',
			'foreignKey'			=> 'metodo_envio_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
        'Marketplace' => array(
			'className'				=> 'Marketplace',
			'foreignKey'			=> 'marketplace_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
        'Comuna' => array(
			'className'				=> 'Comuna',
			'foreignKey'			=> 'comuna_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		)
	);

	public $hasMany = array(
		'EmbalajeProductoWarehouse' => array(
			'className'				=> 'EmbalajeProductoWarehouse',
			'foreignKey'			=> 'embalaje_id',
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


	public function obtener_estados()
	{
		return $this->estados;
	}

	
	/**
	 * cancelar_emabalaje
	 *
	 * @param  mixed $id
	 * @param  mixed $responsable
	 * @return void
	 */
	public function cancelar_embalaje($id, $responsable = '')
	{	
		$emabalaje = array(
			'EmbalajeWarehouse' => array(
				'id' => $id,
				'estado' => 'cancelado',
				'responsable_id_cancelado' => $responsable,
				'fecha_cancelado' => date('Y-m-d H:i:s')
			)
		);

		if ($this->save($emabalaje))
		{
			return true;
		}

		return false;
	}
}