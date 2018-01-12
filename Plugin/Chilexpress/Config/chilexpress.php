<?php
$config = array(
	'Chilexpress' => array(
		'georeferencia' => array(
			'negocio' 		=> '0',
			'sistema'		=> 'XX',
			'username'		=> 'UsrTestServicios',
			'password'		=> 'U$$vr2$tS2T',
			'usuario'		=> '999999999',
			'oficinaCaja' 	=> 'YY',
			'endpoint'		=> 'http://qaws.ssichilexpress.cl/GeoReferencia?wsdl',
			'namespace'		=> 'http://www.chilexpress.cl/CorpGR/',
			'wsdl'			=> dirname(__FILE__) . DS . implode(DS, array('chilexpress', 'WSDL_GeoReferencia_QA.wsdl')),
			'soap'			=> SOAP_1_1,
			'USAR_WSDL'		=> 0
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
			'wsdl'			=> dirname(__FILE__) . DS . implode(DS, array('chilexpress', 'WSDL_Tarificacion_QA.wsdl')),
			'soap'			=> SOAP_1_1,
			'USAR_WSDL' 	=> 0
			),
		'seguimiento' => array(
			'path' 			=> WWW_ROOT . 'Tracking' . DS
			),
		'ot' => array(
			'negocio' 		=> '0',
			'sistema'		=> 'XX',
			'username'		=> 'UsrTestServicios',
			'password'		=> 'U$$vr2$tS2T',
			'usuario'		=> '999999999',
			'oficinaCaja' 	=> 'YY',
			'endpoint'		=> 'http://qaws.ssichilexpress.cl/OSB/GenerarOTDigitalIndividualC2C?wsdl',
			'namespace'		=> 'http://www.chilexpress.cl/IntegracionAsistida/',
			'wsdl'			=> dirname(__FILE__) . DS . implode(DS, array('chilexpress', 'GenerarOTDigitalIndividualC2C.wsdl')),
			'soap'			=> SOAP_1_1,
			'USAR_WSDL' 	=> 0
			)
		)
	);
