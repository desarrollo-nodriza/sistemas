<?php
App::uses('AppModel', 'Model');

class Pmp extends AppModel
{
	/**
	 * ASOCIACIONES
	 */
	public $belongsTo = array(
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'foreignKey'			=> 'venta_detalle_producto_id',
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
		)
	);


	/**
	 * Guardamos el último pmpdel producto por bodega
	 * @param  [type] $id_producto [description]
	 * @param  [type] $id_bodega   [description]
	 * @return [type]              [description]
	 */
	public function registrar_pmp($id_producto, $id_bodega)
	{	
		$historico = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto,
				'BodegasVentaDetalleProducto.bodega_id' => $id_bodega
			)
		));
			
		if (!empty($historico)) {
			// Sumatoria de las cantidades
			$inCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].cantidad'));
			$edCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=ED].cantidad'));

			$inTotal  = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].total'));
			$outTotal = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=ED].total'));

			$Q = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.cantidad'));
			$PQ = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.total'));
		
			if ($Q != 0) {

				$pmp = $PQ / $Q;	
			
				$this->save(array(
					'Pmp' => array(
						'bodega_id'                 => $id_bodega,
						'venta_detalle_producto_id' => $id_producto,
						'pmp'                       => (float) $pmp
					)
				));
			}else{

				$this->save(array(
					'Pmp' => array(
						'bodega_id'                 => $id_bodega,
						'venta_detalle_producto_id' => $id_producto,
						'pmp'                       => $this->obtener_ultimo_pmp($id_producto, $id_bodega)
					)
				));
			}
		}

		return;
	}


	/**
	 * Obtener PMP por producto y bodega
	 * @param  [type] $id_producto [description]
	 * @param  [type] $bodega_id   [description]
	 * @return float              pmp
	 */
	public function obtener_pmp($id_producto, $id_bodega = '')
	{	

		$qry = array(
			'conditions' => array(
				'BodegasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto
			)
		);

		if (!empty($id_bodega)) {
			$qry['conditions'] = array_replace_recursive($qry['conditions'], array(
				'BodegasVentaDetalleProducto.bodega_id' => $id_bodega
			));

			$pmp = $this->obtener_ultimo_pmp($id_producto, $id_bodega);
		}else{
			$pmp = $this->obtener_ultimo_pmp($id_producto);
		}

		$historico = ClassRegistry::init('BodegasVentaDetalleProducto')->find('all', $qry);
		
		if (!empty($historico)) {
			// Sumatoria de las cantidades
			$inCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].cantidad'));
			$edCantidad = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=ED].cantidad'));

			$inTotal  = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=IN].total'));
			$outTotal = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto[io=ED].total'));

			$Q = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.cantidad'));
			$PQ = array_sum(Hash::extract($historico, '{n}.BodegasVentaDetalleProducto.total'));
			
			if ($Q != 0) {

				$pmp = $PQ / $Q;	
			
			}
		}

		return $pmp;
	}


	/**
	 * Obtener último pmp registrado por producto y bodega
	 * @param  [type] $id_producto [description]
	 * @param  [type] $bodega_id   [description]
	 * @return float              pmp
	 */
	public function obtener_ultimo_pmp($id_producto, $id_bodega = '')
	{	
		$qry = array(
			'conditions' => array(
				'Pmp.venta_detalle_producto_id' => $id_producto
			),
			'fields' => array(
				'Pmp.pmp'
			),
			'order' => array('Pmp.created' => 'DESC')
		);

		if (!empty($id_bodega)) {
			$qry['conditions'] = array_replace_recursive($qry['conditions'], array(
				'Pmp.bodega_id' => $id_bodega
			));
		}

		$pmp = $this->find('first', $qry);

		if (!empty($pmp)) {
			return (float) $pmp['Pmp']['pmp'];
		}else{
			return (float) 0;
		}
	}
}