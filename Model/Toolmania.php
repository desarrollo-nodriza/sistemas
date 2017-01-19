<?php 
App::uses('AppModel', 'Model');

Class Toolmania extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Toolmania';
	public $useTable = 'product';
	public $primaryKey = 'id_product';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'toolmania';

	/**
	* Config
	*/
	public $displayField	= 'reference';

	/**
	 * @Overrride paginate method
	 
	public function paginate($conditions, $fields, $order, $limit, $page = 1, $recursive = null, $extra = array()) {

		// separamos los campos que se desean tomar
		$qryFlds = '*';
		if ($fields) {
			$qryFlds = implode(', ', $fields);
		}

		$qryCond = '';
		if (isset($conditions) && !empty($conditions)) {
			foreach ($conditions as $key => $value) {
				$qryCond .= ' AND ' . trim($key) . ' LIKE "%' . trim($value) . '%"'; 
			}
		}


		// Se arma la query (Sorry for the disorder, it´s a long query)
		$sql = 'SELECT concat(\'http://www.toolmania.cl/img/p/\',mid(im.id_image,1,1),\'/\', if (length(im.id_image)>1,concat(mid(im.id_image,2,1),\'/\'),\'\'),if (length(im.id_image)>2,concat(mid(im.id_image,3,1),\'/\'),\'\'),if (length(im.id_image)>3,concat(mid(im.id_image,4,1),\'/\'),\'\'),if (length(im.id_image)>4,concat(mid(im.id_image,5,1),\'/\'),\'\'), im.id_image, \'.jpg\' ) AS url_image, '.$qryFlds.' FROM tm_product p LEFT JOIN tm_product_lang pl ON p.id_product=pl.id_product INNER JOIN tm_image im ON p.id_product = im.id_product WHERE pl.id_lang=1 AND p.active = 1 AND p.available_for_order = 1 AND p.id_shop_default = 1 '.$qryCond.' LIMIT ' . (($page-1) * $limit) . ', ' . $limit;

		// Ejecutamos la query
		try {
			$results = $this->query($sql);
		}catch(Exception $e) {
			$results = $e->getMessage();
		}
		
		// Retornamos el resultado
		return $results;
	}*/


	/**
	 * @Override paginate count method
	 
	public function paginateCount($conditions = null, $recursive = 0, $extra = array()) {

		$qryCond = '';
		if (isset($conditions) && !empty($conditions)) {
			foreach ($conditions as $key => $value) {
				$qryCond .= ' AND ' . trim($key) . ' LIKE "%' . trim($value) . '%"'; 
			}
		}

		$sql = 'SELECT COUNT(*) as count FROM tm_product p LEFT JOIN tm_product_lang pl ON p.id_product=pl.id_product WHERE pl.id_lang=1 AND p.active = 1 '.$qryCond;
		$this->recursive = -1;

		$results = $this->query($sql);
		return $results[0][0]['count'];
	}*/

	/**
	* Asociaciones
	*/
	public $hasAndBelongsToMany = array(
		'Categoria' => array(
			'className'				=> 'Categoria',
			'joinTable'				=> 'categorias_toolmanias',
			'foreignKey'			=> 'id_product',
			'associationForeignKey'	=> 'categoria_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'CategoriasToolmania',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);

	public $belongsTo = array(
		'TaxRulesGroup' => array(
			'className'				=> 'TaxRulesGroup',
			'foreignKey'			=> 'id_tax_rules_group',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

	public $hasMany = array(
		'SpecificPrice' => array(
			'className'				=> 'SpecificPrice',
			'foreignKey'			=> 'id_product',
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
		'SpecificPricePriority' => array(
			'className'				=> 'SpecificPricePriority',
			'foreignKey'			=> 'id_product',
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
	* CAllbacks
	*/
	public function beforeSave($options = array()) {
		parent::beforeSave();
		
	}

	public function afterSave($created = null, $options = Array()) {
		parent::afterSave();
	}

}
	
?>