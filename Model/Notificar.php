<?php
App::uses('AppModel', 'Model');

class Notificar extends AppModel
{


	public $displayField = 'nombre';
	public $useTable     = 'notificar';

	public $hasMany = array(
		'NotificarAsunto' => array(
			'className'				=> 'NotificarAsunto',
			'foreignKey'			=> 'notificar_id',
		)
	);
}
