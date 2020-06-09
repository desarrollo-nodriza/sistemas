<?php
/**
 * Test getEstadoReclamo.
 * @author		Nodriza Spa (http://nodriza.cl/)
 * @copyright	Copyright (c) Nodriza Spa (http://nodriza.cl/)
 * @link		http://nodriza.cl/
 * @version		1.0
 */

require_once((__DIR__ . '/../../starken-ws.class.php')); //Se incluye la librería.
$request = file_get_contents(__DIR__ . '/data.json'); //Se toma el request del json para tests.

$starkenWs = new StarkenWebServices();
$response = $starkenWs->getEstadoReclamo($request, true);
$responseArray = json_decode($response, true);
?>

<!DOCTYPE html>
<html lang="es">
	<head>
		<meta charset="UTF-8">
		<title>Test getEstadoReclamo</title>
	</head>
<body>
	<div>
		<h3>Test getEstadoReclamo</h3>
		<h4>Request:</h4>
		<pre><?php print_r($request); ?></pre>
		<h4>Response:</h4>
		<block><?php print_r($response); ?></block>
		<h4>Response Array:</h4>
		<pre><?php print_r($responseArray); ?></pre>
	</div>
</body>
</html>

