<?php 
App::uses('AppModel', 'Model');

Class HistorialEmbalaje extends AppModel {

	/**
	 * Set Cake config DB
	 */
    public $useDbConfig = 'default';
	public $name = 'HistorialEmbalaje';
	public $useTable = 'historial_embalajes';
	public $primaryKey = 'id';

	
	
}