<?php 
App::uses('AppModel', 'Model');

Class Orden extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Orden';
	public $useTable = 'orders';
	public $primaryKey = 'id_order';


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

	);


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