<?php 
App::uses('AppModel', 'Model');

Class EmbalajeWarehouse extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $useDbConfig = 'warehouse';
	public $useTable = 'embalajes';
	public $displayField	= 'id';


	private $estados = array(
		'inicial' => 'Inicial',
		'en_revision' => 'En revisión manual',
		'listo_para_embalar' => 'Listo para embalar',
		'procesando' => 'En preparación',
		'finalizado' => 'Finalizado',
		'cancelado' => 'Cancelado'
	);


	/**
	 * Asosiaciones
	 * @var array
	 */
	public $belongsTo = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
        ),
        'Bodega' => array(
			'className'				=> 'Bodega',
			'foreignKey'			=> 'bodega_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
        'MetodoEnvio' => array(
			'className'				=> 'MetodoEnvio',
			'foreignKey'			=> 'metodo_envio_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
        'Marketplace' => array(
			'className'				=> 'Marketplace',
			'foreignKey'			=> 'marketplace_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		),
        'Comuna' => array(
			'className'				=> 'Comuna',
			'foreignKey'			=> 'comuna_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Rol')
		)
	);

	public $hasMany = array(
		'EmbalajeProductoWarehouse' => array(
			'className'				=> 'EmbalajeProductoWarehouse',
			'foreignKey'			=> 'embalaje_id',
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


	public function obtener_estados()
	{
		return $this->estados;
	}

	
	/**
	 * cancelar_emabalaje
	 *
	 * @param  mixed $id
	 * @param  mixed $responsable
	 * @return void
	 */
	public function cancelar_embalaje($id, $responsable = '')
	{	
		$embalaje = array(
			'EmbalajeWarehouse' => array(
				'id' => $id,
				'estado' => 'cancelado',
				'responsable_id_cancelado' => $responsable,
				'fecha_cancelado' => date('Y-m-d H:i:s')
			)
		);

		if ($this->save($embalaje))
		{
			return true;
		}

		return false;
	}



	public function procesar_embalajes($id)
	{
		$venta = ClassRegistry::init('Venta')->find('first', array(
			'conditions' => array(
				'Venta.id' => $id
			),
			'contain' => array(	
				'VentaDetalle' => array(
					'EmbalajeProductoWarehouse' => array(
						'EmbalajeWarehouse'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.metodo_envio_id',
				'Venta.marketplace_id',
				'Venta.comuna_id',
				'Venta.fecha_venta',
				'Venta.venta_estado_id',
				'Venta.administrador_id',
				'Venta.picking_estado'
			)
		));
		
		$bodega = ClassRegistry::init('Bodega')->obtener_bodega_principal();

		$dte_valido = ClassRegistry::init('Dte')->obtener_dte_valido_venta($id);


		switch ($venta['Venta']['picking_estado']) {
			case 'no_definido':

				# si hay un embalaje creado se cancela siempre y cuando sean las 
				# unidades del embalaje las que se quitan de la reserva
				foreach ($venta['VentaDetalle'] as $d) 
				{	
					if (!empty($d['EmbalajeProductoWarehouse']) && $d['cantidad_reservada'] == 0)
					{	
						# Cancelamos todos loe embalajes relacionados al detalle
						foreach ($d['EmbalajeProductoWarehouse'] as $emp) 
						{
							ClassRegistry::init('EmbalajeWarehouse')->cancelar_embalaje($emp['embalaje_id']);
						}
					}
				}
				
				break;
			
			case 'empaquetar':

				# Embalaje
				$embalaje = array(
					'EmbalajeWarehouse' => array(
						'venta_id' => $venta['Venta']['id'],
						'estado' => 'listo_para_embalar',
						'bodega_id' => $bodega['Bodega']['id'],
						'metodo_envio_id' => $venta['Venta']['metodo_envio_id'],
						'marketplace_id' => $venta['Venta']['marketplace_id'],
						'comuna_id' => $venta['Venta']['comuna_id'],
						'venta_estado_id' => '',
						'fecha_venta' => $venta['Venta']['fecha_venta'],
						'fecha_creacion' => date('Y-m-d H:i:s'),
						'fecha_listo_para_embalar' => date('Y-m-d H:i:s'),
						'ultima_modifacion' => date('Y-m-d H:i:s')
					),
					'EmbalajeProductoWarehouse' => array()
				);
				
				# Asignamos los productos al embalaje
				foreach ($venta['VentaDetalle'] as $ivd => $d) 
				{	
					$crear_emabalaje = true;

					if (!empty($d['EmbalajeProductoWarehouse']) && $d['cantidad_reservada'] > 0)
					{	
						# si el producto ya está en un embalaje valido, se omite
						# su creación.
						foreach ($d['EmbalajeProductoWarehouse'] as $emp) 
						{
							if (in_array($emp['EmbalajeWarehouse']['estado'], array('listo_para_embalar', 'procesando', 'finalizado')))
							{
								$crear_emabalaje = false;
							}
						}
					}
					
					# Agregamos el item al nuevo embalaje
					if ($crear_emabalaje && $d['cantidad_reservada'] > 0)
					{	
						$embalaje['EmbalajeProductoWarehouse'][] = array(
							'producto_id' => $d['venta_detalle_producto_id'],
							'detalle_id' => $d['id'],
							'cantidad_a_embalar' => $d['cantidad_reservada'],
							'fecha_creacion' => date('Y-m-d H:i:s'),
							'ultima_modifacion' => date('Y-m-d H:i:s')
						);
					}

				}
				
				# si hay productos para embalar y tiene dte válido pasa a embalaje
				if (!empty($embalaje['EmbalajeProductoWarehouse']) && $dte_valido)
				{
					ClassRegistry::init('EmbalajeWarehouse')->saveAll($embalaje);
				}
				
				break;

			case 'empaquetando':

				break;
			case 'empaquetado':

				break;
		}
	}
}