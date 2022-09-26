<?php

App::uses('Controller', 'Controller');
App::uses('CampanasController', 'Controller');

class GenerarGoogleFeedXmlShell extends AppShell
{

	public function main()
	{

		$log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' 		=> 'GenerarGoogleFeedXmlShell',
			'modulo_accion' => 'Inicia proceso de crear GoogleFeedXml automaticas: ' . date('Y-m-d H:i:s')
		));

		$CampanasController = new CampanasController();
		$campana 			= $this->Campana->find('list', array(
			'fields' => ['Campana.id', 'Campana.id'],
			'conditions' => array(
				'Campana.activo' => true
			),

		));
		$respuesa = [];

		foreach ($campana as $id) {
			$respuesa[$id] = $CampanasController->google_generar_xml_feed(ClassRegistry::init('Tienda')->tienda_principal()['Tienda']['id'], $id, true);
		}


		if ($respuesa) {
			$log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' 		=> 'GenerarGoogleFeedXmlShell',
				'modulo_accion' => json_encode(
					[
						'Finaliza proceso de crear GoogleFeedXml automaticas: ' . date('Y-m-d H:i:s') => $respuesa
					]
				)
			));

			# Guardamos el log
			ClassRegistry::init('Log')->saveMany($log);
		}


		return true;
	}
}
