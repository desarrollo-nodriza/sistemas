<?php
App::uses('AppModel', 'Model');

class NotificarAsunto extends AppModel
{


	public $useTable = 'notificar_asunto';

	public $belongsTo = array(
		'Asunto' => array(
			'className'     => 'Asunto',
			'foreignKey'    => 'asunto_id'
		),
		'Notificar' => array(
			'className'     => 'Notificar',
			'foreignKey'    => 'notificar_id'
		)
	);


	/**
	 * atencion_cliente_ids
	 * El asunto nos permite filtrar los administradores relacionados al asunto
	 * @param  mixed $asunto
	 * @return void
	 */
	// TODO Obtenemos los responsables de atender determinado asunto
	public function atencion_cliente_ids($asunto = '')
	{
		$ultimo = $this->find('all', array(
			'fields' => array(
				'Notificar.correo',
			),
			'joins' => [
				[
					'table'      => 'asuntos',
					'alias'      => 'Asunto',
					'type'       => 'INNER',
					'conditions' => [
						'Asunto.id = NotificarAsunto.asunto_id',
						'Asunto.nombre' => $asunto,
					]
				],
				[
					'table'      => 'notificar',
					'alias'      => 'Notificar',
					'type'       => 'INNER',
					'conditions' => [
						'Notificar.id = NotificarAsunto.notificar_id'
					]
				]
			]
		));
	
		if ($ultimo) {
			$ultimo = hash::extract($ultimo, '{n}.Notificar.correo');
		}

		// TODO Si no hay uno relacionado al asunto se ira abuscar el que esta por defecto
		if (!$ultimo) {
			$ultimo = ClassRegistry::init('Notificar')->find(
				'first',
				[
					'fields' => 'correo',
					'conditions' => [
						'default' => true,
						'activo' => true,
					],

				]
			);
			// ! Les cambio la key para despues extraer notificar_id
			if ($ultimo['Notificar'] ?? false) {
				$ultimo    =  $ultimo['Notificar']['correo'];
				$ultimo    = [$ultimo];
			}
		}

	

		// TODO Si no hay uno por defecto va por cualquier Usuario activo
		if (!$ultimo) {
			$ultimo = ClassRegistry::init('Notificar')->find(
				'first',
				[
					'fields' => 'correo',
					'conditions' => [
						'activo' => true,
					],

				]
			);
			// ! Les cambio la key para despues extraer notificar_id
			if ($ultimo['Notificar'] ?? false) {
				$ultimo    =  $ultimo['Notificar']['correo'];
				$ultimo    = [$ultimo];
			}
		}
	
		// TODO Si no hay uno por defecto y activo va por cualquiera que este relacionado
		if (!$ultimo) {
			$ultimo = ClassRegistry::init('Contacto')->find(
				'first',
				[
					'fields'     => 'Contacto.administrador_id',
					'contain'    => ['Administrador' => ['fields' => 'Administrador.email',]],
				]
			);

			if ($ultimo['Administrador'] ?? false) {
				$ultimo    = $ultimo['Administrador']['email'];
				$ultimo    = [$ultimo];
			}
		}
		
		return $ultimo;
	}
}
