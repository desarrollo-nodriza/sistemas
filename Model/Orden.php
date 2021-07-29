<?php 
App::uses('AppModel', 'Model');

Class Orden extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Orden';
	public $useTable = 'orders';
	public $primaryKey = 'id_order';

	# Módulos externos de PS
	public $externalTables = array(
		'fmm_custom_userdata',
		'fmm_custom_fields',
		'fmm_custom_fields_lang',
		'webpay_detail_order'
	);


	/**
	 * [$tipo_ndc description]
	 * @var array
	 */
	private static $tipo_ndc = array(
		'devolucion' => 'Anular y devolver producto/s (Devuelve items a bodega)',
		'anulacion'  => 'Anular producto/s (No devuelve items a bodega)', 
		'cambio_dte' => 'Cambio de DTE (No tiene efecto en items ni en la venta)', 
		'garantia'   => 'Garantía (Devuelve items a bodega sin afectar el stock)',
		'stockout'   => 'Stockout (Devuelve items a bodega si corresponde)'
	);

	public $belongsTo = array(
		'OrdenEstado' => array(
			'className'				=> 'OrdenEstado',
			'foreignKey'			=> 'current_state',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'OrdenEstado')
		),
		'Cliente' => array(
			'className'				=> 'Cliente',
			'foreignKey'			=> 'id_customer',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Carro' => array(
			'className'				=> 'Carro',
			'foreignKey'			=> 'id_cart',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

	public $hasMany = array(
		'OrdenDetalle' => array(
			'className'				=> 'OrdenDetalle',
			'foreignKey'			=> 'id_order',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'Dte' => array(
			'className'				=> 'Dte',
			'foreignKey'			=> 'id_order',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'ClienteHilo' => array(
			'className'				=> 'ClienteHilo',
			'foreignKey'			=> 'id_order',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'OrdenTransporte' => array(
			'className'				=> 'OrdenTransporte',
			'foreignKey'			=> 'id_order',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
	);


	public $hasAndBelongsToMany = array(
		'Manifiesto' => array(
			'className'				=> 'Manifiesto',
			'joinTable'				=> 'manifiestos_ventas',
			'foreignKey'			=> 'id_order',
			'associationForeignKey'	=> 'manifiesto_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'ManifiestosVenta',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);

	public function beforeFind($options = array()) {
		parent::beforeFind($options);
		
		//$this->validarModulosExternos();
		
	}


	/**
	 * Valida la existencia de las tablas en la bas de datos de la tienda PS
	 * @return bool 
	 */
	public function validarModulosExternos()
	{
		$conf = CakeSession::read('Tienda.configuracion');
		$prefix = CakeSession::read('Tienda.prefijo');

		$sf = false;

		# Verificamos existencia de tablas externas en PS
		$db = ConnectionManager::getDataSource($conf);
		$tables = $db->listSources();
		
		foreach ($this->externalTables as $it => $table) {
			if (in_array(sprintf('%s%s', $prefix, $table), $tables)) {
				$sf = true;
			}else{
				$sf = false;
			}
		}

		if ($sf) {
			$this->hasMany = array_replace_recursive($this->hasMany, array(
				'CustomUserdata' => array(
					'className'				=> 'CustomUserdata',
					'foreignKey'			=> 'id_order',
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
			));
		}

		return $sf;	
	}


	public function get_tipos_ndc(){

		return self::$tipo_ndc;

	}

	/*public function getUniqReference($id_cart = '') {
		$referencia = $this->find('first', array(
			'conditions' => array('Orders.id_cart' => $id_cart),
			'fields' => array('MIN(id_order) as min', 'MAX(id_order) as max', 'id_order', 'reference')
			));

		if ( $referencia['Orders']['min'] == $referencia['Orders']['max'] ) {
			return $referencia['Orders']['reference'];
		}else {
			return $referencia['Orders']['reference'].'#'.($referencia['Orders']['id_order'] + 1 - $referencia['Orders']['min']);
		}
	}*/

}