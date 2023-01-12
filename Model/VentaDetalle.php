<?php
App::uses('AppModel', 'Model');
class VentaDetalle extends AppModel
{
	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'Venta' => array(
			'className'				=> 'Venta',
			'foreignKey'			=> 'venta_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Venta')
		),
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'foreignKey'			=> 'venta_detalle_producto_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'VentaDetalleProducto')
		)
	);

	public $hasMany = array(
		'EmbalajeProductoWarehouse' => array(
			'className'				=> 'EmbalajeProductoWarehouse',
			'foreignKey'			=> 'detalle_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> '',
		),
		'HistorialEmbalaje' => array(
			'className'				=> 'HistorialEmbalaje',
			'foreignKey'			=> 'detalle_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
		)
		,
		'VentaDetallesReserva' => array(
			'className'				=> 'VentaDetallesReserva',
			'foreignKey'			=> 'venta_detalle_id',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
		)
	);

	public $hasAndBelongsToMany = array(
		'Atributo' => array(
			'className'				=> 'Atributo',
			'joinTable'				=> 'venta_detalles_atributos',
			'foreignKey'			=> 'venta_detalle_id',
			'associationForeignKey'	=> 'atributo_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
    );


	public function afterSave($created, $options = array())
	{	
		# Relacionamos el atributo con el detalle
		if (isset($this->data['Atributo']))
		{	
			$atributos_detalles = array();

			foreach ($this->data['Atributo'] as $atributo) 
			{
				$atributos_detalles[] = array(
					'VentaDetallesAtributo' => array(
						'venta_detalle_id' => $this->data['VentaDetalle']['id'],
						'atributo_id' => $atributo['atributo_id'],
						'valor' => $atributo['valor']
					)
				);
			}

			ClassRegistry::init('VentaDetallesAtributo')->create();
			ClassRegistry::init('VentaDetallesAtributo')->saveMany($atributos_detalles);
		}
	}


	public function recalcular_total_producto($id_detalle){

		$detalle = $this->find('first', array(
			'conditions' => array(
				'VentaDetalle.id' => $id_detalle
			)
		));

		$detalle['VentaDetalle']['precio_bruto'] = monto_bruto($detalle['VentaDetalle']['precio']);
		$detalle['VentaDetalle']['total_neto']   = ($detalle['VentaDetalle']['precio'] * ($detalle['VentaDetalle']['cantidad'] - $detalle['VentaDetalle']['cantidad_anulada']));
		$detalle['VentaDetalle']['total_bruto']  = monto_bruto($detalle['VentaDetalle']['total_neto']);

		return $this->save($detalle);

	}

	/**
	 * Si hay items disponible se reserva el stock y se actualiza el picking_estado de la venta.
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */

	//  ! No se está usando, no actualizar.
	//  ! Revisar mismo metodo en modelo Venta
	public function reservar_stock_producto($id)
	{	

		$log = array();
		
		$ventaDetalle     = $this->find('first', array(
			'conditions' => array(
				'VentaDetalle.id' => $id
			),
			'fields' => array(
				'VentaDetalle.id',
				'VentaDetalle.venta_id',
				'VentaDetalle.venta_detalle_producto_id',
				'VentaDetalle.cantidad_reservada',
				'VentaDetalle.cantidad',
				'VentaDetalle.cantidad_anulada',
				'VentaDetalle.cantidad_entregada',
				'VentaDetalle.cantidad_en_espera',
				'VentaDetalle.fecha_llegada_en_espera'
			)
		));

		$log[] = array(
			'Log' => array(
				'administrador' => 'Producto reservar inicia ' . $id,
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($ventaDetalle)
			)
		);
		
		if (empty($ventaDetalle)) 
		{
			return 0;
		}

		$venta = $this->find('first', array(
			'conditions' => array(
				'Venta.id' => $ventaDetalle['VentaDetalle']['venta_id']
			),
			'contain' => array(				
				'VentaEstado' => array(
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.id','VentaEstadoCategoria.reserva_stock'
						)
					),
					'fields' => array(
						'VentaEstado.id'
					)
				),
			),
			'fields' => array(
				'Venta.id'				
			)
		));

		if ($venta['VentaEstado']['VentaEstadoCategoria']['reserva_stock'] == false) {

			$log[] = array(
				'Log' => array(
					'administrador' => "No se han realizado reservas debido a la categoría ({$venta['VentaEstado']['venta_estado_categoria_id']}) del estado ({$venta['VentaEstado']['id']}) de la venta {$ventaDetalle['VentaDetalle']['venta_id']}" ,
					'modulo'        => 'Ventas',
					'modulo_accion' => json_encode($venta)
				)
			);

			# Guardamos el log
			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);
			return 0;
		
		}

		$cant_reservada = $ventaDetalle['VentaDetalle']['cantidad_reservada'];
		$cant_vendida   = $ventaDetalle['VentaDetalle']['cantidad'] - $ventaDetalle['VentaDetalle']['cantidad_anulada'];
		$cant_entregada = $ventaDetalle['VentaDetalle']['cantidad_entregada'];
		$cant_en_espera = $ventaDetalle['VentaDetalle']['cantidad_en_espera'];
		$fecha_llegada  = $ventaDetalle['VentaDetalle']['fecha_llegada_en_espera'];

		if ($cant_vendida == $cant_entregada) {
			return 0;
		}

		$reservar = $cant_vendida - $cant_reservada - $cant_entregada;
		
		$disponible = ClassRegistry::init('Bodega')->calcular_reserva_stock($ventaDetalle['VentaDetalle']['venta_detalle_producto_id'], $reservar, ClassRegistry::init('Venta')->bodega_id($ventaDetalle['VentaDetalle']['venta_id']));
		$reservado  = $cant_reservada + $disponible;

		# Solo se reserva si la cantidad reservada es distinta a la cantidad comprada por el cliente
		if ($cant_reservada != $cant_vendida ) 
		{
			$cant_vendida = $cant_vendida - $cant_entregada;
			$ventaDetalle['VentaDetalle']['cantidad_reservada'] = $reservado;
			$diff = $cant_vendida - $reservado;

			if ($diff >= $cant_en_espera) 
			{
				$ventaDetalle['VentaDetalle']['cantidad_en_espera'] = $cant_en_espera;
			}
			else 
			{
				$ventaDetalle['VentaDetalle']['cantidad_en_espera'] = $cant_en_espera - $diff;	
			}

			if (empty($fecha_llegada)) 
			{
				unset($ventaDetalle['VentaDetalle']['cantidad_en_espera']);
			}

			$log[] = array(
				'Log' => array(
					'administrador' => 'Producto reservar finaliza ' . $id,
					'modulo' => 'Ventas',
					'modulo_accion' => json_encode($ventaDetalle)
				)
			);

			# Guardamos el log
			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);
			
			if(!$this->save($ventaDetalle))
				return 0;
		}
		
		$venta = ClassRegistry::init('Venta')->find('first', array(
			'conditions' => array(
				'Venta.id' => $ventaDetalle['VentaDetalle']['venta_id']
			),
			'contain' => array(
				'VentaDetalle' => array(
					'fields' => array(
						'VentaDetalle.cantidad',
						'VentaDetalle.cantidad_anulada',
						'VentaDetalle.cantidad_entregada',
						'VentaDetalle.cantidad_en_espera',
						'VentaDetalle.cantidad_reservada'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.picking_estado'
			)
		));
		
		$total_cantidad = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_anulada')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_entregada')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_en_espera'));
		$total_reservado = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_reservada'));
		
		if ( $total_reservado == $total_cantidad && $total_reservado > 0) 
		{
			if (empty($venta['Venta']['picking_estado']) || $venta['Venta']['picking_estado'] == 'no_definido' || $venta['Venta']['picking_estado'] == 'en_revision' || $venta['Venta']['picking_estado'] == 'empaquetado' ) 
			{
				# Pasa a picking
				ClassRegistry::init('Venta')->cambiar_estado_picking($venta['Venta']['id'], 'empaquetar');
			}
		}
		else
		{
			# Se cambia a no preparado
			ClassRegistry::init('Venta')->cambiar_estado_picking($venta['Venta']['id'], 'no_definido');
		}

		return $reservado;
	}


	/**
	 * [obtener_cantidad_reservada description]
	 * @param  [type] $id       [description]
	 * @param  [type] $id_venta [description]
	 * @return [type]           [description]
	 */
	// !Obsoleta se esta ocupando la del modelo VentaDetalleProducto
	public function obtener_cantidad_reservada($id_producto, $id_venta = null)
	{
		$qry = array(
			'conditions' => array(
				'VentaDetalle.venta_detalle_producto_id' => $id_producto,
				'VentaDetalle.completo' => 0,
				'VentaDetalle.created >' => '2019-05-28 18:00:00' 
			),
			'joins'      => array(
				array(
					'table' => 'rp_ventas',
					'alias' => 'Venta',
					'type' => 'INNER',
					'conditions' => array(
						'Venta.id = VentaDetalle.venta_id'
					)
				),
				array(
					'table' => 'rp_venta_estados',
					'alias' => 'VentaEstado',
					'type' => 'INNER',
					'conditions' => array(
						'VentaEstado.id = Venta.venta_estado_id'
					)
				),
				array(
					'table' => 'rp_venta_estado_categorias',
					'alias' => 'VentaEstadoCategoria',
					'type' => 'INNER',
					'conditions' => array(
						'VentaEstadoCategoria.id = VentaEstado.venta_estado_categoria_id',
						'VentaEstadoCategoria.reserva_stock = 1'
					)
				)
			),
			'fields' => array(
				'VentaDetalle.cantidad_pendiente_entrega', 'VentaDetalle.cantidad_entregada', 'VentaDetalle.cantidad', 'VentaDetalle.cantidad_reservada', 'VentaDetalle.venta_id', 'VentaDetalle.id'
			)
		);

		if (!empty($id_venta)) {
			$qry = array_replace_recursive($qry, array('conditions' => array(
				'VentaDetalle.venta_id' => $id_venta
			)));
		}
		
		$vendidos = $this->find('all', $qry);
		
		if (empty($vendidos)) {
			return 0;
		}

		$total = 0;

		foreach ($vendidos as $iv => $vendido) {

			if ($vendido['VentaDetalle']['cantidad_reservada'] == 0)
				continue;

			$total = $total + ( $vendido['VentaDetalle']['cantidad_reservada']);
		}

		return $total;

	}

	public function Venta_id($id)
	{	
		$this->id = $id;
		return $this->field('venta_id');
	}

	public function tiempo_despacho_producto_con_stock($mes = 1)
	{
		return $this->query("
			select 	
					VentaDetalle.id,
					count(VentaDetalle.venta_id)                            cantidad_productos,
					VentaDetalle.venta_detalle_producto_id,
					(timestampdiff(DAY, VentaDetalle.created, rvdr.modified) - (select count(id) from rp_feriados where feriado BETWEEN VentaDetalle.created and rvdr.modified))  tiempo_despacho

			from rp_venta_detalles VentaDetalle

					inner join rp_venta_detalles_reservas rvdr on VentaDetalle.id = rvdr.venta_detalle_id
					left join rp_orden_compras_ventas rocv on VentaDetalle.venta_id = rocv.venta_id

			where 
				rvdr.embalaje_id is not null
				and TIMESTAMPDIFF(MONTH, VentaDetalle.created, Now()) <= $mes
				and TIMESTAMPDIFF(MONTH, rvdr.created, Now()) <= $mes
				and ((SELECT SUM(rpb.cantidad)
						FROM `rp_bodegas_venta_detalle_productos` AS `rpb`
						WHERE `rpb`.`tipo` <> 'GT'
						and rpb.venta_detalle_producto_id = VentaDetalle.venta_detalle_producto_id) - (SELECT SUM(rvdr.cantidad_reservada)
																								FROM `rp_venta_detalles_reservas` AS `rvdr`
																								where rvdr.venta_detalle_producto_id = VentaDetalle.venta_detalle_producto_id)) >
					0
				and rocv.id is null
			group by VentaDetalle.venta_detalle_producto_id
			having cantidad_productos = 1
			order by VentaDetalle.venta_detalle_producto_id desc
 		", false);
	}
}
