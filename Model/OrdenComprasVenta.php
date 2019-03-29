<?php
App::uses('AppModel', 'Model');
class OrdenComprasVenta extends AppModel
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
}