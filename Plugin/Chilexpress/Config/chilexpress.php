<?php
$config = array(
	'Chilexpress' => array(
		'georeferencia' => array(
			'negocio' 		=> '?',
			'sistema'		=> '?',
			'username'		=> 'UsrTestServicios',
			'password'		=> 'U$$vr2$tS2T',
			'endpoint'		=> 'http://qaws.ssichilexpress.cl/GeoReferencia?wsdl',
			'namespace'		=> 'http://www.chilexpress.cl/CorpGR/',
			'wsdl'			=> dirname(__FILE__) . DS . implode(DS, array('chilexpress', 'WSDL_GeoReferencia_QA.wsdl')),
			'soap'			=> SOAP_1_2,
			'USAR_WSDL'		=> 1
			),
		'tarificacion' => array(
			'negocio' 		=> '?',
			'sistema'		=> '?',
			'username'		=> 'UsrTestServicios',
			'password'		=> 'U$$vr2$tS2T',
			'endpoint'		=> 'http://qaws.ssichilexpress.cl/TarificarCourier?wsdl',
			'namespace'		=> 'http://www.chilexpress.cl/TarificaCourier/',
			'wsdl'			=> dirname(__FILE__) . DS . implode(DS, array('chilexpress', 'WSDL_Tarificacion_QA.wsdl')),
			'soap'			=> SOAP_1_2,
			'USAR_WSDL' 	=> 1
			),
		'seguimiento' => array(
			'negocio' 		=> '?',
			'sistema'		=> '?',
			'username'		=> 'UsrTestServicios',
			'password'		=> 'U$$vr2$tS2T',
			'endpoint'		=> 'http://qaws.ssichilexpress.cl/TarificarCourier?wsdl',
			'namespace'		=> 'http://www.chilexpress.cl/TarificaCourier/',
			'wsdl'			=> dirname(__FILE__) . DS . implode(DS, array('chilexpress', 'WSDL_Tarificacion_QA.wsdl')),
			'soap'			=> SOAP_1_2,
			'USAR_WSDL' 	=> 1
			),
		'ot' => array(
			'negocio' 		=> '?',
			'sistema'		=> '?',
			'username'		=> 'UsrTestServicios',
			'password'		=> 'U$$vr2$tS2T',
			'endpoint'		=> 'http://qaws.ssichilexpress.cl/OSB/GenerarOTDigitalIndividualC2C?wsdl',
			'namespace'		=> 'http://www.chilexpress.cl/IntegracionAsistida/',
			'wsdl'			=> dirname(__FILE__) . DS . implode(DS, array('chilexpress', 'GenerarOTDigitalIndividualC2C.wsdl')),
			'soap'			=> SOAP_1_2,
			'USAR_WSDL' 	=> 0
			)
		)
	);
