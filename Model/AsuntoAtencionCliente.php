<?php
App::uses('AppModel', 'Model');

class AsuntoAtencionCliente extends AppModel
{


	public $useTable = 'asuntos_atencion_cliente';

	public $belongsTo = array(
		'Asunto' => array(
			'className'     => 'Asunto',
			'foreignKey'    => 'asunto_id'
		),
		'AtencionCliente' => array(
			'className'     => 'AtencionCliente',
			'foreignKey'    => 'atencion_cliente_id'
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
				'AsuntoAtencionCliente.atencion_cliente_id',
			),
			'joins' => [
				[
					'table'      => 'asuntos',
					'alias'      => 'Asunto',
					'type'       => 'INNER',
					'conditions' => [
						'Asunto.id = AsuntoAtencionCliente.asunto_id',
						'Asunto.nombre' => $asunto,
					]
				]
			],
			'order' => array('AsuntoAtencionCliente.id' => 'desc')
		));

		// TODO Si no hay uno relacionado al asunto se ira abuscar el que esta por defecto
		if (!$ultimo) {
			$ultimo = ClassRegistry::init('AtencionCliente')->find(
				'first',
				[
					'fields' => 'id as atencion_cliente_id',
					'conditions' => [
						'default' => true,
						'activo' => true,
					],

				]
			);
			// ! Les cambio la key para despues extraer atencion_cliente_id
			if ($ultimo['AtencionCliente'] ?? false) {
				$ultimo[] = ['AsuntoAtencionCliente' => $ultimo['AtencionCliente']];
			}
		}

		// TODO Si no hay uno por defecto va por cualquier Usuario activo
		if (!$ultimo) {
			$ultimo = ClassRegistry::init('AtencionCliente')->find(
				'first',
				[
					'fields' => 'id as atencion_cliente_id',
					'conditions' => [
						'activo' => true,
					],

				]
			);
			// ! Les cambio la key para despues extraer atencion_cliente_id
			if ($ultimo['AtencionCliente'] ?? false) {
				$ultimo[] = ['AsuntoAtencionCliente' => $ultimo['AtencionCliente']];
			}
		}

		// TODO Si no hay uno por defecto y activo va por cualquiera que este relacionado
		if (!$ultimo) {
			$ultimo = $this->find('all', array(
				'fields' => array(
					'atencion_cliente_id',
				),
				'order' => array('AsuntoAtencionCliente.id' => 'desc'),
				'limit' => 1
			));
		}
		
		return Hash::extract($ultimo, '{n}.AsuntoAtencionCliente.atencion_cliente_id') ?? [];
	}
}
