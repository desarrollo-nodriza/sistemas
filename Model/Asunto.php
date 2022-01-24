<?php
App::uses('AppModel', 'Model');

class Asunto extends AppModel
{

	public $displayField	= 'nombre';
	
	public $hasMany = array(
		'NotificarAsunto' => array(
			'className'				=> 'NotificarAsunto',
			'foreignKey'			=> 'asunto_id',
		)
	);
}
