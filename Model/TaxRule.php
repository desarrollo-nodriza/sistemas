<?php 
App::uses('AppModel', 'Model');

Class TaxRule extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'TaxRule';
	public $useTable = 'tax_rule';
	public $primaryKey = 'id_tax_rule';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'toolmania';

	public $belongsTo = array(
		'TaxRulesGroup' => array(
			'className'				=> 'TaxRulesGroup',
			'foreignKey'			=> 'id_tax_rules_group',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		),
		'Tax' => array(
			'className'				=> 'Tax',
			'foreignKey'			=> 'id_tax',
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'counterCache'			=> true,
			//'counterScope'			=> array('Asociado.modelo' => 'Plantilla')
		)
	);
}