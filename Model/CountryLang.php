<?php 
App::uses('AppModel', 'Model');

Class CountryLang extends AppModel {

	/**
	 * Set Cake config DB
	 */
	public $name = 'CountryLang';
	public $useTable = 'country_lang';
	public $primaryKey = 'id_lang';

}