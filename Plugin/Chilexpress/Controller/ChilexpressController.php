<?php
App::uses('AppController', 'Controller');
class ChilexpressController extends ChilexpressAppController
{	
	public function admin_index(){
		
	}


	public function tracking()
	{	

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'fields' => array(
				'Tienda.logo',
				'Tienda.url',
				'Tienda.nombre',
				'Tienda.id'
			),
			'conditions' => array(
				'Tienda.activo' => 1
			)
		));

		if ( $this->request->is('post') ) {
			
			$tracking_number = $this->request->data['Tracking']['tracking_number'];

			if (empty($tracking_number)) {
				$this->Session->setFlash('El número de seguimiento no está correcto. Verifiquelo y vuelva a intentarlo.', null, array(), 'danger');
				$this->redirect(array('controller' => 'chilexpress', 'action' => 'tracking', 'plugin' => false));
			}

			$tracking = $this->trackingChilexpress($tracking_number);

			if (empty($tracking)) {
				$this->Session->setFlash('El número de seguimiento ingresado no registra información.', null, array(), 'warning');
				$this->redirect(array('controller' => 'chilexpress', 'action' => 'tracking', 'plugin' => false));
			}

			$this->Session->setFlash('Datos obtenidos con éxito', null, array(), 'success');

			$this->layout = 'chilexpress';
			$this->set(compact('tracking', 'tiendas', 'tracking_number'));
			$this->render('result');
			
		}

		$this->layout = 'chilexpress';
		$this->set(compact('tiendas'));
	}


	public function result()
	{

	}


	public function trackingChilexpress($ot = '')
	{	
		if (empty($ot)) {
			return array();
		}

		$ruta    = Configure::read('Chilexpress.seguimiento.path');
		$archivo = Configure::read('Chilexpress.seguimiento.filename');
		
		$fullpath = $ruta . $archivo;

		//$arr = $this->Tracking->leer_excel_tracking($fullpath, '99574733764');
		$arr = $this->Tracking->leer_excel_tracking($fullpath, $ot);

		return $arr;
	}
}
