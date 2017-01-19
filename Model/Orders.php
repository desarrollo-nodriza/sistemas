<?php 
App::uses('AppModel', 'Model');

Class Orders extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'Orders';
	public $useTable = 'orders';
	public $primaryKey = 'id_order';

	/**
	 * Use Toolmania Connect
	 */
	public $useDbConfig = 'toolmania';

}