<?php
$config = array(
	'Chilexpress' => array(
		'georeferencia' => array(
			'username'		=> 'UsrTestServicios',
			'password'		=> 'U$$vr2$tS2T',
			'endpoint'		=> 'http://qaws.ssichilexpress.cl/GeoReferencia?wsdl',
			'server_cert'	=> dirname(__FILE__) . DS . implode(DS, array('chilexpress', 'tbk.pem')),
			'private_key'	=> dirname(__FILE__) . DS . implode(DS, array('chilexpress', '597020000547.key')),
			'cert_file'		=> dirname(__FILE__) . DS . implode(DS, array('chilexpress', '597020000547.crt'))
			)
		)
	);
