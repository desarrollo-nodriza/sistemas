<?php
$config = array(
	'Chilexpress' => array(
		'tcc' => '22106942',
		'georeferencia' => array(
			'negocio' 		=> '0',
			'sistema'		=> 'XX',
			'username'		=> 'UsrTestServicios',
			'password'		=> 'U$$vr2$tS2T',
			'usuario'		=> '999999999',
			'oficinaCaja' 	=> 'YY',
			'endpoint'		=> 'http://qaws.ssichilexpress.cl/GeoReferencia?wsdl',
			'namespace'		=> 'http://www.chilexpress.cl/CorpGR/',
			'soap'			=> SOAP_1_1,
		),
		'tarificacion' => array(
			'negocio' 		=> '0',
			'sistema'		=> 'XX',
			'username'		=> 'UsrTestServicios',
			'password'		=> 'U$$vr2$tS2T',
			'usuario'		=> '999999999',
			'oficinaCaja' 	=> 'YY',
			'endpoint'		=> 'http://qaws.ssichilexpress.cl/TarificarCourier?wsdl',
			'namespace'		=> 'http://www.chilexpress.cl/TarificaCourier/',
			'soap'			=> SOAP_1_1,
		),
		'seguimiento' => array(
			'path' 			=> WWW_ROOT . 'Tracking' . DS // Ruta FTP donde se guardarán el documento de seguimientos entregado por  Chilexpress
		),
		'ot' => array(
			'negocio' 		=> '0',
			'sistema'		=> 'XX',
			'username'		=> 'UsrTestServicios',
			'password'		=> 'U$$vr2$tS2T',
			'usuario'		=> '999999999',
			'oficinaCaja' 	=> 'YY',
			'pathEtiquetas' => WWW_ROOT . 'OT' . DS,
			'pathPublica'	=> FULL_BASE_URL . DS . APP_DIR . DS . WEBROOT_DIR . DS . 'OT' . DS,
			'endpoint'		=> 'http://qaws.ssichilexpress.cl/OSB/GenerarOTDigitalIndividualC2C?wsdl',
			'namespace'		=> 'http://www.chilexpress.cl/IntegracionAsistida/',
			'soap'			=> SOAP_1_1,
		)
	)
);