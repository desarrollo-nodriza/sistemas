<?php
App::uses('AppModel', 'Model');
class EntradaSalidaProducto extends AppModel
{
	
	/**
	 * VALIDACIONES
	 */
	public $validate = array(
		'tipo' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				'message'		=> 'Requerido',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'cantidad' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				'message'		=> 'Requerido',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'valor' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				'message'		=> 'Requerido',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		),
		'total' => array(
			'notBlank' => array(
				'rule'			=> array('notBlank'),
				'last'			=> true,
				'message'		=> 'Requerido',
				//'allowEmpty'	=> true,
				//'required'		=> false,
				//'on'			=> 'update', // Solo valida en operaciones de 'create' o 'update'
			),
		)
	);


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
		)
	);


	public function obtener_pmp_por_id($id_producto = null)
	{
		$historico = $this->find('all', array(
			'conditions' => array(
				'EntradaSalidaProducto.venta_detalle_producto_id' => $id_producto
			)
		));

		if (!empty($historico)) {
			// Sumatoria de las cantidades
			$inCantidad = array_sum(Hash::extract($historico, '{n}.EntradaSalidaProducto[tipo=IN].cantidad'));
			$edCantidad = array_sum(Hash::extract($historico, '{n}.EntradaSalidaProducto[tipo=ED].cantidad'));

			$inTotal = array_sum(Hash::extract($historico, '{n}.EntradaSalidaProducto[tipo=IN].total'));
			$outTotal = array_sum(Hash::extract($historico, '{n}.EntradaSalidaProducto[tipo=ED].total'));

			$pmp = ($inTotal - $outTotal) / ($inCantidad - $edCantidad);	
		}

		prx($pmp);

	} 


	public function obtener_pmp_por_sku($sku = null)
	{
		$historico = $this->find('all', array(
			'conditions' => array(
				'EntradaSalidaProducto.sku' => $sku
			)
		));

		// Sumatoria de las cantidades
		$inCantidad = array_sum(Hash::extract($historico, '{n}.EntradaSalidaProducto[tipo=IN].cantidad'));
	} 

}