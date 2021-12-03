<?php

Configure::write('debug', 2);
Configure::write('ambiente', 'dev');


Configure::write('Error', array(
	'handler' => 'ErrorHandler::handleError',
	'level' => E_ALL & ~E_DEPRECATED,
	'trace' => true
));

Configure::write('Exception', array(
	'handler' => 'ErrorHandler::handleException',
	'renderer' => 'ExceptionRenderer',
	'log' => true
));

Configure::write('App.encoding', 'UTF-8');
Configure::write('Config.language', 'spa');
//Configure::write('App.baseUrl', env('SCRIPT_NAME'));
Configure::write('Routing.prefixes', array('admin'));
Configure::write('Cache.disable', true);
//Configure::write('Cache.check', true);
//Configure::write('Cache.viewPrefix', 'prefix');
Configure::write('Session', array(
	'defaults'			=> 'php',
	'cookie'			=> 'SISTEMAV2', // Unico
	'timeout' 			=> 360,
	'checkAgent'		=> true,
	'autoRegenerate'	=> true
));
Configure::write('Security.salt', 'f3ec8ed41979f4d5939d58f7ae598f3afe346819');
Configure::write('Security.cipherSeed', '316431663662343239636630303637');
// Solución a la muerte de la sessión al hacer peticiones ajax
//Configure::write('Session.checkAgent', false);
//Configure::write('Asset.timestamp', true);
//Configure::write('Asset.filter.css', 'css.php');
//Configure::write('Asset.filter.js', 'custom_javascript_output_filter.php');
Configure::write('Acl.classname', 'DbAcl');
Configure::write('Acl.database', 'default');
date_default_timezone_set('America/Santiago');

/**
 * Motor de cache
 */
$engine		= 'File';

/**
 * Duracion del cache en desarrollo
 */
$duration	= '+999 days';
if ( Configure::read('debug') > 0 )
	$duration		= '+10 seconds';

/**
 * Cambiar a prefijo unico por proyecto
 */
$prefix		= 'sistemav2_';

/**
 * Configuracion general del cache
 */
Cache::config('_cake_core_', array(
	'engine'		=> $engine,
	'prefix'		=> $prefix . 'cake_core_',
	'path'			=> CACHE . 'persistent' . DS,
	'serialize'		=> ($engine === 'File'),
	'duration'		=> $duration
));
Cache::config('_cake_model_', array(
	'engine'		=> $engine,
	'prefix'		=> $prefix . 'cake_model_',
	'path'			=> CACHE . 'models' . DS,
	'serialize'		=> ($engine === 'File'),
	'duration'		=> $duration
));