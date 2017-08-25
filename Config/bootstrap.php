<?php
// Setup a 'default' cache configuration for use in the application.
Cache::config('default', array('engine' => 'File'));

/**
 * The settings below can be used to set additional paths to models, views and controllers.
 *
 * App::build(array(
 *     'Model'                     => array('/path/to/models', '/next/path/to/models'),
 *     'Model/Behavior'            => array('/path/to/behaviors', '/next/path/to/behaviors'),
 *     'Model/Datasource'          => array('/path/to/datasources', '/next/path/to/datasources'),
 *     'Model/Datasource/Database' => array('/path/to/databases', '/next/path/to/database'),
 *     'Model/Datasource/Session'  => array('/path/to/sessions', '/next/path/to/sessions'),
 *     'Controller'                => array('/path/to/controllers', '/next/path/to/controllers'),
 *     'Controller/Component'      => array('/path/to/components', '/next/path/to/components'),
 *     'Controller/Component/Auth' => array('/path/to/auths', '/next/path/to/auths'),
 *     'Controller/Component/Acl'  => array('/path/to/acls', '/next/path/to/acls'),
 *     'View'                      => array('/path/to/views', '/next/path/to/views'),
 *     'View/Helper'               => array('/path/to/helpers', '/next/path/to/helpers'),
 *     'Console'                   => array('/path/to/consoles', '/next/path/to/consoles'),
 *     'Console/Command'           => array('/path/to/commands', '/next/path/to/commands'),
 *     'Console/Command/Task'      => array('/path/to/tasks', '/next/path/to/tasks'),
 *     'Lib'                       => array('/path/to/libs', '/next/path/to/libs'),
 *     'Locale'                    => array('/path/to/locales', '/next/path/to/locales'),
 *     'Vendor'                    => array('/path/to/vendors', '/next/path/to/vendors'),
 *     'Plugin'                    => array('/path/to/plugins', '/next/path/to/plugins'),
 * ));
 *
 */

/**
 * Custom Inflector rules, can be set to correctly pluralize or singularize table, model, controller names or whatever other
 * string is passed to the inflection functions
 *
 * Inflector::rules('singular', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 * Inflector::rules('plural', array('rules' => array(), 'irregular' => array(), 'uninflected' => array()));
 *
 */

/**
 * Inflecciones en Español -- bside / 2013-05-23
 */
Inflector::rules(
	'singular',
	array(
		'rules'	=> array(
			'/(categoria)s$/i'				=> '\1',
			'/(padre)s$/i'					=> '\1',
			'/(banner)s$/i'					=> '\1',
			'/(email)s$/i'					=> '\1',
			'/(query)s$/i'					=> '\1',

			'/([r|d|j|n|l|m|y|z])es$/i'		=> '\1',
			'/as$/i'						=> 'a',
			'/([ti])a$/i'					=> '\1a'
		),
		'irregular'			=> array(),
		'uninflected'		=> array()
	)
);

Inflector::rules(
	'plural',
	array(
		'rules'			=> array(
			'/(categoria)$/i'				=> '\1s',
			'/(padre)$/i'					=> '\1s',
			'/(banner)$/i'					=> '\1s',
			'/(email)$/i'					=> '\1s',
			'/(query)$/i'					=> '\1s',

			'/([r|d|j|n|l|m|y|z])$/i'		=> '\1es',
			'/a$/i'							=> '\1as'
		),
		'irregular'			=> array(),
		'uninflected'		=> array()
	)
);


/**
 * Plugins need to be loaded manually, you can either load them one by one or all of them in a single call
 * Uncomment one of the lines below, as you need. make sure you read the documentation on CakePlugin to use more
 * advanced ways of loading plugins
 *
 * CakePlugin::loadAll(); // Loads all plugins at once
 * CakePlugin::load('DebugKit'); //Loads a single plugin named DebugKit
 *
 */
//CakePlugin::loadAll();
CakePlugin::load('DebugKit');

/**
 * You can attach event listeners to the request lifecycle as Dispatcher Filter . By Default CakePHP bundles two filters:
 *
 * - AssetDispatcher filter will serve your asset files (css, images, js, etc) from your themes and plugins
 * - CacheDispatcher filter will read the Cache.check configure variable and try to serve cached content generated from controllers
 *
 * Feel free to remove or add filters as you see fit for your application. A few examples:
 *
 * Configure::write('Dispatcher.filters', array(
 *		'MyCacheFilter', //  will use MyCacheFilter class from the Routing/Filter package in your app.
 *		'MyPlugin.MyFilter', // will use MyFilter class from the Routing/Filter package in MyPlugin plugin.
 * 		array('callable' => $aFunction, 'on' => 'before', 'priority' => 9), // A valid PHP callback type to be called on beforeDispatch
 *		array('callable' => $anotherMethod, 'on' => 'after'), // A valid PHP callback type to be called on afterDispatch
 *
 * ));
 */
Configure::write('Dispatcher.filters', array(
	'AssetDispatcher',
	'CacheDispatcher'
));

/**
 * Configures default file logging options
 */
App::uses('CakeLog', 'Log');
CakeLog::config('debug', array(
	'engine' => 'FileLog',
	'types' => array('notice', 'info', 'debug'),
	'file' => 'debug',
));
CakeLog::config('error', array(
	'engine' => 'FileLog',
	'types' => array('warning', 'error', 'critical', 'alert', 'emergency'),
	'file' => 'error',
));


//Moneda local
CakeNumber::addFormat('CLP', array('before' => '$', 'thousands' => '.', 'decimals' => ',', 'places' => 0));


# Cake pdf
CakePlugin::load('CakePdf', array('bootstrap' => true, 'routes' => true));

// Configuración CakePDF
Configure::write('CakePdf', array(
    'engine' => 'CakePdf.DomPdf',
    'pageSize' => 'A4',
    'orientation' => 'portrait'
));

 define('DOMPDF_ENABLE_REMOTE', true);

// URL Base para consola
if ( !defined('FULL_BASE_URL') ) {
	define('FULL_BASE_URL', 'http://sistemas.nodriza.cl/');
}

/**
 * Funciones personalizadas
 */
function prx()
{
	foreach ( func_get_args() as $arg )
		pr($arg);
	exit;
}

function to_array($obj)
{
	return json_decode(json_encode($obj), true);
}

function getDashboard($rol_id)
{	
	if (empty($rol_id)) {
		return false;
	}else{

		$dashboard = ClassRegistry::init('Rol')->find('first', array('conditions' => array('Rol.id' => $rol_id)));
		
		if (!empty($dashboard) && $dashboard['Rol']['mostrar_dashboard']) {
			return true;
		}
	}

	return false;
}