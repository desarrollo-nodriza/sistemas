<?php
App::uses('AppModel', 'Model');

class Asunto extends AppModel
{

	public $displayField	= 'nombre';
	
	public $hasMany = array(
		'AsuntoAtencionCliente' => array(
			'className'				=> 'AsuntoAtencionCliente',
			'foreignKey'			=> 'asunto_id',
		)
	);
}
