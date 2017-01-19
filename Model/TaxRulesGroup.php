<?php 
App::uses('AppModel', 'Model');

Class TaxRulesGroup extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'TaxRulesGroup';
	public $useTable = 'tax_rules_group';
	public $primaryKey = 'id_tax_rules_group';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'toolmania';

	public $hasMany = array(
		'Toolmania' => array(
			'className'				=> 'Toolmania',
			'foreignKey'			=> 'id_tax_rules_group',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'TaxRule' => array(
			'className'				=> 'TaxRule',
			'foreignKey'			=> 'id_tax_rules_group',
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
}