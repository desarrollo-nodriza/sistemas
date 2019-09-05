<?php
App::uses('AppModel', 'Model');
class OrdenComprasVentaDetalleProducto extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	
	/**
	 * Set Cake config DB
	 */
	public $primaryKey = 'id';

	/**
	 * BEHAVIORS
	 */
	var $actsAs			= array(
		/**
		 * IMAGE UPLOAD
		 */
		/*
		'Image'		=> array(
			'fields'	=> array(
				'imagen'	=> array(
					'versions'	=> array(
						array(
							'prefix'	=> 'mini',
							'width'		=> 100,
							'height'	=> 100,
							'crop'		=> true
						)
					)
				)
			)
		)
		*/
	);

	/**
	 * VALIDACIONES
	 
	public function afterSave($created = true, $options = array()) {

		parent::afterSave($created, $options);

		if ( ! empty($this->data[$this->alias]) && $created ) {
			
			#Buscamos al grupo
			$manifiestos = ClassRegistry::init('ManifiestoVenta')->find('count', array(
				'conditions' => array(
					'ManifiestoVenta.manifiesto_id' => $this->data[$this->alias]['manifiesto_id']
					)
				));
			
			# actualizamos el campo contador al grupo
			ClassRegistry::init('Grupocaracteristica')->id = $this->data[$this->alias]['manifiesto_id'];
			ClassRegistry::init('Grupocaracteristica')->saveField('count_caracteristicas', $manifiestos);

		}		
	}*/


	public function obtener_cantidad_confirmada_proveedor($id_producto, $id_oc)
	{
		$ocDetalles = $this->find('first', array(
			'conditions' => array(
				'OrdenComprasVentaDetalleProducto.venta_detalle_producto_id' => $id_producto,
				'OrdenComprasVentaDetalleProducto.orden_compra_id' => $id_oc
			)
		));

		return $ocDetalles['OrdenComprasVentaDetalleProducto']['cantidad'];

	}


	public function obtener_productos_por_oc($id_oc)
	{
		
	}

}