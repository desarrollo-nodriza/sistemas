<?php 
App::uses('AppModel', 'Model');

Class Orders extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Orders';
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