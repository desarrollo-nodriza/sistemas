<?php
App::uses('AppController', 'Controller');
class PaisesController extends AppController
{
	public function admin_paises_por_tienda ( $tienda = null) {
		if (empty($tienda)) {
    		echo json_encode(array('error' => 'Error al obtener los paises'));
    		exit;
    	}

    	$tiendaR = ClassRegistry::init('Tienda')->find('first', array(
    		'conditions' => array(
    			'Tienda.id' => $tienda,
    			'Tienda.activo' => 1
    			)
    		));

    	if (empty($tiendaR)) {
    		echo json_encode(array('error' => 'No se encontró la tienda'));
    		exit;
    	}

    	// Cambiamos la configuración de la base de datos
		$this->cambiarConfigDB($tiendaR['Tienda']['configuracion']);

		$paises = $this->Paise->find('all', array(
			'contain' => array('Lang')));

		if (empty($paises)) {
			echo json_encode(array('error' => 'No se encontraron paises'));
    		exit;
		}
		
		$arrayLista = array();
		foreach ($paises as $pais) {
			$arrayLista[$pais['Paise']['id_country']] = implode(Hash::extract($pais, 'Lang.{n}.PaisIdioma.name')); 
		}
		
		$htmlLista = '<option value="#">Seleccione pais</option>';
		foreach ($arrayLista as $key => $value) {
			$htmlLista .= sprintf('<option value="%d">%s</option>',$key, $value);
		}

		echo $htmlLista;
    	exit;
	}
}