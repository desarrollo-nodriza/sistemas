<?php
App::uses('AppController', 'Controller');
class RegionesController extends AppController
{
	public function admin_regiones_por_tienda_pais ( $tienda = null, $pais = null) {
		if (empty($tienda) || empty($pais)) {
    		echo json_encode(array('error' => 'Error al obtener las regiones'));
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

		$regiones = $this->Region->find('list', array(
			'conditions' => array('Region.id_country' => $pais)));

		if (empty($regiones)) {
			echo json_encode(array('error' => 'No se encontraron regiones'));
    		exit;
		}
		
		$htmlLista = '<option value="#">Seleccione region</option>';
		foreach ($regiones as $key => $value) {
			$htmlLista .= sprintf('<option value="%d">%s</option>',$key, $value);
		}

		echo $htmlLista;
    	exit;
	}
}