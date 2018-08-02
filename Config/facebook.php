<?php
/**
 * Datos aplicacion productiva
 */
$config = array(
	'Facebook' => array(
		'appId'			=> '359469147531409',
		'apiKey'		=> '2dd9e78eb20c7fbff51d963745ed5895',
		'secret'		=> '2dd9e78eb20c7fbff51d963745ed5895',
		'cookie'		=> true,
		'locale'		=> 'es_LA',
		'scope'			=> 'email',
		'dev'			=> false,
		//'fanpage'		=> array('HitesChile' => '210117285681771'),
		//'fanpage'		=> array('MundoCopec' => '527441447268308'),
		'fanpage'		=> array('Desarrollo-Brand-On' => '252307824903144'),
		'fileUpload'	=> true
	)
);

/**
 * Datos aplicacion desarrollo
 */
if ( in_array($_SERVER['SERVER_NAME'], array('localhost', '127.0.0.1', '::1')) )
{
	$config['Facebook']		= array_merge($config['Facebook'], array(
		'appId'			=> '474381246014047',
		'apiKey'		=> 'a4039a681ee065170031d2f4499a06e4',
		'secret'		=> 'a4039a681ee065170031d2f4499a06e4',
		'dev'			=> false,
		'fanpage'		=> array('Desarrollo-Brand-On' => '252307824903144')
	));
}
