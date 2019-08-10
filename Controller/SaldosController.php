<?php
App::uses('AppController', 'Controller');

class SaldosController extends AppController
{
	public function admin_index()
	{
		$opt = array();

		$proveedores = ClassRegistry::init('Proveedor')->find('all', $opt);

		foreach ($proveedores as $ip => $p) {
			$proveedores[$ip]['Proveedor']['saldo'] = $this->Saldo->obtener_saldo_total_proveedor($p['Proveedor']['id']);
		}

		$ocs = ClassRegistry::init('OrdenCompra')->find('list', array(
			'conditions' => array(
				'OR' => array(
					array(
						'OrdenCompra.parent_id !=' => '',
						'OrdenCompra.oc_manual' => 0
					),
					array(
						'OrdenCompra.parent_id' => '',
						'OrdenCompra.oc_manual' => 1
					)
				)
			)
		));

		$facturas         = ClassRegistry::init('OrdenCompraFactura')->find('list', array('fields' => array('OrdenCompraFactura.folio')));
		
		$proveedoresLista =  ClassRegistry::init('Proveedor')->find('list', array('conditions' => array('activo' => 1)));
		
		$pagos            = ClassRegistry::init('Pago')->find('list', array('fields' => array('Pago.identificador')));
		
		$tipoSaldo 		  = array(
			'crear' => 'Positivo',
			'descontar' => 'Negativo'
		);

		BreadcrumbComponent::add('Saldos', '/saldos');
		BreadcrumbComponent::add('Saldos por proveedores');

		$this->set(compact('proveedores', 'facturas', 'proveedoresLista', 'pagos', 'ocs', 'tipoSaldo'));
	}


	public function admin_usar($id_proveedor)
	{
		$saldo = $this->Saldo->obtener_saldo_total_proveedor($id_proveedor);
		
		if ($this->Saldo->descontar($id_proveedor, null, null, null, $saldo)) {
			$this->Session->setFlash('Saldo descontado con éxito.', null, array(), 'success');
		}else{
			$this->Session->setFlash('No fue posible descontar el saldo.', null, array(), 'danger');
		}

		$this->redirect(array('action' => 'index'));
	}


	public function admin_add()
	{
		if (!$this->request->is('post')) {
			$this->Session->setFlash('Método no permitido.', null, array(), 'warning');
			$this->redirect(array('action' => 'index'));
		}

		$result = false;

		if ($this->request->data['Saldo']['tipo'] == 'crear') {
			$result = ClassRegistry::init('Saldo')->crear($this->request->data['Saldo']['proveedor_id'], $this->request->data['Saldo']['orden_compra_id'], $this->request->data['Saldo']['orden_compra_factura_id'], $this->request->data['Saldo']['pago_id'], $this->request->data['Saldo']['monto']);
		}


		if ($this->request->data['Saldo']['tipo'] == 'descontar') {
			$result = ClassRegistry::init('Saldo')->descontar($this->request->data['Saldo']['proveedor_id'], $this->request->data['Saldo']['orden_compra_id'], $this->request->data['Saldo']['orden_compra_factura_id'], $this->request->data['Saldo']['pago_id'], $this->request->data['Saldo']['monto']);
		}

		if ($result) {
			$this->Session->setFlash('Saldo creado con éxito.', null, array(), 'success');
		}else{
			$this->Session->setFlash('No fue posible crear el saldo.', null, array(), 'danger');
		}

		$this->redirect(array('action' => 'index'));

	}
}