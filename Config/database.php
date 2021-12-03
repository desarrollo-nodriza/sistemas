<?php
class DATABASE_CONFIG
{	
	public $default = array(
		'datasource'	=> 'Database/Mysql',
		'persistent'	=> false,
		'host'			=> '45.79.26.106',
		'login'			=> 'nodriza',
		'password'		=> 'IgP_8111980_IgP',
		'database'		=> 'sistemas_pro_clone',
		'prefix'		=> 'rp_',
		'encoding'		=> 'utf8'
	);

	/* Data source reports */
	public $reportes = array(
		'datasource'	=> 'Database/Mysql',
		'persistent'	=> false,
		'host'			=> '45.79.26.106',
		'login'			=> 'nodriza',
		'password'		=> 'IgP_8111980_IgP',
		'database'		=> 'sistemas_pro_clone',
		'prefix'		=> 'rp_',
		'encoding'		=> 'utf8'
	);

	/* Data source newsletter */
	public $newsletter = array(
		'datasource'	=> 'Database/Mysql',
		'persistent'	=> false,
		'host'			=> '45.79.26.106',
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
		'host' => '45.79.26.106',
		'login' => 'nodriza',
		'password' => 'IgP_8111980_IgP',
		'database' => 'tm2018',
		'prefix' => 'tm_',
		'encoding' => 'utf8',
	);

	/* Data source toolmania */
	public $walko = array(
		'datasource' => 'Database/Mysql',
		'persistent' => false,
		'host' => '45.79.26.106',
		'login' => 'nodriza',
		'password' => 'IgP_8111980_IgP',
		'database' => 'walko',
		'prefix' => 'ac_',
		'encoding' => 'utf8',
	);


	public $warehouse = array(
		'datasource'	=> 'Database/Mysql',
		'persistent'	=> false,
		'host'			=> 'nodriza.clq26lbic33x.us-east-1.rds.amazonaws.com',
		'login'			=> 'warehouse_dev',
		'password'		=> 'w4r3h05s3_d3v',
		'database'		=> 'warehouse_dev',
		'prefix'		=> '',
		'encoding'		=> 'utf8'
	);
}
