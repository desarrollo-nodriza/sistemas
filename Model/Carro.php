<?php 
App::uses('AppModel', 'Model');

Class Carro extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Carro';
	public $useTable = 'cart';
	public $primaryKey = 'id_cart';

	# Módulos externos de PS
	public $externalTables = array(
		'webpay_detail_order'
	);

	/**
	* Config
	*/
	public $displayField	= 'id_cart';

	public $validate = array(

	);


	/**
	 * Asosiaciones
	 * @var array
	 */
	
	public $hasMany = array(
		'Orden' => array(
			'className'				=> 'Orden',
			'foreignKey'			=> 'id_cart',
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


	public function beforeFind($options = array()) {
		parent::beforeFind($options);
		$this->validarModulosExternos();
		
	}



	/**
	 * Valida la existencia de las tablas en la base de datos de la tienda PS
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
				'WebpayStore' => array(
					'className'				=> 'WebpayStore',
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
}