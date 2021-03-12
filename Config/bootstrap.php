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
CakePlugin::load('Chilexpress', array('routes' => true));

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
if ( Configure::read('ambiente') == 'dev' ) {
	@define('FULL_BASE_URL', 'https://sistemasdev.nodriza.cl/');
}else{
	@define('FULL_BASE_URL', 'https://sistema.nodriza.cl/');
}

// URL DEV Base para consola
if ( !defined('FULL_BASE_URL_DEV') ) {
	define('FULL_BASE_URL_DEV', 'https://sistemasdev.nodriza.cl/');
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

function obtener_url_base()
{
	if (Configure::read('ambiente') == 'dev') {
		return 'https://sistemasdev.nodriza.cl/';
	}else{
		return 'https://sistema.nodriza.cl/';
	}
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

function retornarDescuento($monto_total, $monto_oferta)
{
	if ( ! empty($monto_total) && ! empty($monto_oferta) ) {
		$dscto = ($monto_oferta*100) / $monto_total;
		return round($dscto);
	}
}

// (d1+d2) - (d1*d2)
function calcularDescuentoCompuesto($sum = array(), $base = null)
{	
	$base = $base/100;

	foreach ($sum as $i => $d) {
		$base = ($base + ($d / 100)) - ( $base * ($d / 100) );
	}

	return $base;
}

function abecedario($indice, $min = false)
{
	$abc = array();
	for ($i=65;$i<=90;$i++) {
	  $abc[] = chr($i);                 
	}

	if (isset($abc[$indice])) {
		return ($min) ? strtolower($abc[$indice]) : $abc[$indice];
	}

	return null;
}

function monto_bruto($precio = null, $iva = 19, $round = 2)
{
	if (!is_null($precio)) {

		$iva = (Configure::read('iva_clp') / 100) +1;

		return round( $precio * $iva, $round );
	}
	
	return 0;
}


function quitar_iva($precio, $iva = 19)
{
	if (!is_null($precio)) {

		$iva = (Configure::read('iva_clp') / 100) +1;

		return round( $precio / $iva, 2 );
	}
	
	return 0;
}

function agregar_iva($precio, $iva = 19)
{
	$iva_monto = obtener_iva($precio, Configure::read('iva_clp'));
	return round($precio + $iva_monto, 2);
}


function monto_neto($precio = null, $iva = 19, $round = 2)
{
	$iva = (Configure::read('iva_clp') / 100) +1;

	return round( $precio / $iva, $round );
}


function obtener_iva($monto, $iva = 19)
{	
	$iva = (Configure::read('iva_clp') / 100);

	return (float)($monto * $iva);
}


function obtener_descuento_monto($monto, $descuento)
{
	$descuento = ($descuento / 100);

	return (float)($monto * $descuento);
}


function calcular_sobreprecio($monto, $aumento)
{
	$aumento = ($aumento / 100) + 1;

	return (double) ($monto*$aumento);
}


function random_color_part() {
    return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
}


function random_color() {
    return random_color_part() . random_color_part() . random_color_part();
}


function getDatesFromRange($start, $end, $format = 'Y-m-d') { 
      
    // Declare an empty array 
    $array = array(); 
      
    // Variable that store the date interval 
    // of period 1 day 
    $interval = new DateInterval('P1D'); 
  
    $realEnd = new DateTime($end); 
    $realEnd->add($interval); 
  
    $period = new DatePeriod(new DateTime($start), $interval, $realEnd); 
  
    // Use loop to store date into array 
    foreach($period as $date) {                  
        $array[] = $date->format($format);  
    } 
  
    // Return the array elements 
    return $array; 
}


function external_url_exists( $url = NULL ) {

    if( empty( $url ) ){
        return false;
    }

    $ch = curl_init( $url );
 
    // Establecer un tiempo de espera
    curl_setopt( $ch, CURLOPT_TIMEOUT, 5 );
    curl_setopt( $ch, CURLOPT_CONNECTTIMEOUT, 5 );

    // Establecer NOBODY en true para hacer una solicitud tipo HEAD
    curl_setopt( $ch, CURLOPT_NOBODY, true );
    // Permitir seguir redireccionamientos
    curl_setopt( $ch, CURLOPT_FOLLOWLOCATION, true );
    // Recibir la respuesta como string, no output
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );

    // Descomentar si tu servidor requiere un user-agent, referrer u otra configuración específica
    // $agent = 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/70.0.3538.102 Safari/537.36';
    // curl_setopt($ch, CURLOPT_USERAGENT, $agent)

    $data = curl_exec( $ch );

    // Obtener el código de respuesta
    $httpcode = curl_getinfo( $ch, CURLINFO_HTTP_CODE );
    //cerrar conexión
    curl_close( $ch );
    
    // Aceptar solo respuesta 200 (Ok), 301 (redirección permanente) o 302 (redirección temporal)
    $accepted_response = array( 200, 301, 302 );
    if( in_array( $httpcode, $accepted_response ) ) {
        return true;
    } else {
        return false;
    }

}


function unique_multidim_array($array, $key) {
    $temp_array = array();
    $i = 0;
    $key_array = array();
   
    foreach($array as $val) {
        if (!in_array($val[$key], $key_array)) {
            $key_array[$i] = $val[$key];
            $temp_array[$i] = $val;
        }
        $i++;
    }
    return $temp_array;
}

function formato_rut($rut)
{	
	if (!empty($rut)) {

		# Quitamos los puntos
		$rut = trim(str_replace('.', '', $rut));
		$rut = str_replace('-', '', $rut);

		$dv  = substr($rut, -1);
		$rut = substr($rut, 0, -1);
		
		# Formateamos
		$rut = $rut . '-' . $dv;
	}

	return $rut;
}

function array_keys_recursive($input, $maxDepth = INF, $depth = 0, $arrayKeys = [])
{
	if ($depth < $maxDepth) {
		$depth++;
		$keys = array_keys($input);
		foreach ($keys as $key) {
			if (is_array($input[$key]))
				$arrayKeys[$key] = array_keys_recursive($input[$key], $maxDepth, $depth);
			else
				$arrayKeys[] = $key;
		}
	}
	return $arrayKeys;
}