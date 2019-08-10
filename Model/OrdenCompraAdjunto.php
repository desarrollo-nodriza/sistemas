<?php
App::uses('AppModel', 'Model');
class OrdenCompraAdjunto extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'id';

	var $actsAs			= array(
		/**
		 * IMAGE UPLOAD
		 */
		'Image'		=> array(
			'fields'	=> array(
				'adjunto'	=> array(
				)
			)
		)
	);

	/*public $validate = array(
		'adjunto' => array(
            'required' => array(
                'rule' => 'required',
                'required' => true,
                'message' => 'Requerido'
            )
        )
	);*/


	public $belongsTo = array(
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'orden_compra_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);

	public $hasMany = array(
		'Pago' => array(
			'className'				=> 'Pago',
			'foreignKey'			=> 'orden_compra_adjunto_id',
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

}