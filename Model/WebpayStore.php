<?php 
App::uses('AppModel', 'Model');

Class WebpayStore extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'WebpayStore';
	public $useTable = 'webpay_detail_order';
	public $primaryKey = 'id_webpay_detail_order';

	/**
	* Config
	*/
	public $displayField	= 'id_webpay_detail_order';

	public $validate = array(

	);


	/**
	 * Asosiaciones
	 * @var array
	 */
	
	public $belongsTo = array(
		'Carro' => array(
			'className'				=> 'Carro',
			'foreignKey'			=> 'id_order',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);
}