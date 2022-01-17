<?php
App::uses('AppModel', 'Model');

class AtencionCliente extends AppModel
{


	public $displayField	= 'nombre';
	public $useTable = 'atencion_cliente';

	public $hasMany = array(
		'AsuntoAtencionCliente' => array(
			'className'				=> 'AsuntoAtencionCliente',
			'foreignKey'			=> 'atencion_cliente_id',
		)
	);
}
