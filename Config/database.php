<?php
class DATABASE_CONFIG
{	
	public $default = array(
		'datasource'	=> 'Database/Mysql',
		'persistent'	=> false,
		'host'			=> '69.164.205.133',
		'login'			=> 'nodriza',
		'password'		=> 'IgP_8111980_IgP',
		'database'		=> 'sistemas_dev',
		'prefix'		=> 'rp_',
		'encoding'		=> 'utf8'
	);

	/* Data source reports */
	public $reportes = array(
		'datasource'	=> 'Database/Mysql',
		'persistent'	=> false,
		'host'			=> '69.164.205.133',
		'login'			=> 'nodriza',
		'password'		=> 'IgP_8111980_IgP',
		'database'		=> 'sistemas_dev',
		'prefix'		=> 'rp_',
		'encoding'		=> 'utf8'
	);

	/* Data source newsletter */
	public $newsletter = array(
		'datasource'	=> 'Database/Mysql',
		'persistent'	=> false,
		'host'			=> '69.164.205.133',
		'login'			=> 'nodriza',
		'password'		=> 'IgP_8111980_IgP',
		'database'		=> 'newsletter',
		'prefix'		=> 'news_',
		'encoding'		=> 'utf8'
	);

	/* Data source toolmania */
	public $toolmania = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => '69.164.205.133',
		'login' => 'nodriza',
		'password' => 'IgP_8111980_IgP',
		'database' => 'dev_toolmania',
		'prefix' => 'tm_',
		'encoding' => 'utf8',
	);

	/* Data source toolmania */
	public $walko = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => '69.164.205.133',
		'login' => 'nodriza',
		'password' => 'IgP_8111980_IgP',
		'database' => 'walko',
		'prefix' => 'ac_',
		'encoding' => 'utf8',
	);
}
