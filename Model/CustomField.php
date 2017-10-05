<?php 
App::uses('AppModel', 'Model');

Class CustomField extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'CustomField';
	public $useTable = 'fmm_custom_fields';
	public $primaryKey = 'id_custom_field';


	/*public $belongsTo = array(
		'OrdenEstado' => array(
			'className'				=> 'OrdenEstado',
			'foreignKey'			=> 'current_state',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'OrdenEstado')
		)
	);*/

	public $hasMany = array(
		'CustomUserdata' => array(
			'className'				=> 'CustomUserdata',
			'foreignKey'			=> 'id_custom_field',
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


	public $hasAndBelongsToMany = array(
		'Lang' => array(
			'className'				=> 'Lang',
			'joinTable'				=> 'fmm_custom_fields_lang',
			'foreignKey'			=> 'id_custom_field',
			'associationForeignKey'	=> 'id_lang',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'with'					=> 'CustomFieldLang',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)
	);
}