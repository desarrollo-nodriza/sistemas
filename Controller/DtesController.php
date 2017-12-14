<?php
App::uses('AppController', 'Controller');
class DtesController extends AppController
{	
	public $tipoDocumento = array(
		#30 => 'factura',
		#32 => 'factura de venta bienes y servicios no afectos o exentos de IVA',
		#35 => 'Boleta',
		#38 => 'Boleta exenta',
		#45 => 'factura de compra',
		#55 => 'nota de débito',
		#60 => 'nota de crédito',
		#103 => 'Liquidación',
		#40 => 'Liquidación Factura',
		#43 => 'Liquidación - Factura Electrónica',
		33 => 'Factura Electrónica',
		#34 => 'Factura No Afecta o Exenta Electrónica',
		39 => 'Boleta Electrónica',
		#41 => 'Boleta Exenta Electrónica',
		#46 => 'Factura de Compra Electrónica',
		56 => 'Nota de Débito Electrónica',
		61 => 'Nota de Crédito Electrónica',
		#50 => 'Guía de Despacho',
		52 => 'Guía de Despacho Electrónica',
		#110 => 'Factura de Exportación Electrónica',
		#111 => 'Nota de Débito de Exportación Electrónica',
		#112 => 'Nota de Crédito de Exportación Electrónica',
		#801 => 'Orden de Compra', 
		#802 => 'Nota de pedido',
		#803 => 'Contrato',
		#804 => 'Resolución',
		#805 => 'Proceso ChileCompra',
		#806 => 'Ficha ChileCompra',
		#807 => 'DUS',
		#808 => 'B/L (Conocimiento de embarque)',
		#809 => 'AWB (Air Will Bill)',
		#810 => 'MIC/DTA',
		#811 => 'Carta de Porte',
		#812 => 'Resolución del SNA donde califica Servicios de Exportación',
		#813 => 'Pasaporte',
		#814 => 'Certificado de Depósito Bolsa Prod. Chile',
		#815 => 'Vale de Prenda Bolsa Prod. Chile'
	);


	/**
	 * Códigos de Webpay
	 * @var array
	 */
	public $paymentTypeCodearray = array(
        "VD" => "Venta Debito",
        "VN" => "Venta Normal", 
        "VC" => "Venta en cuotas", 
        "SI" => "3 cuotas sin interés", 
        "S2" => "2 cuotas sin interés", 
        "NC" => "N cuotas sin interés", 
    );


	/**
	 * Retorna el estado según su código
	 * @param  string  $slug  código del estado
	 * @return string         Valor humanizado del código
	 */
	public function dteEstado($slug = '')
    {
    	if (!empty($slug)) {
    		$estados = array(
    			'no_generado' => 'DTE no emitido',
    			'dte_temporal_no_emitido' => 'DTE Temporal no emitido',
    			'dte_real_no_emitido' => 'DTE Real no emitido',
    			'dte_real_emitido' => 'DTE Emitido'
    		);

    		return $estados[$slug];
    	}

    	return 'DTE no emitido';
    }


	/**
     * Crea un redirect y agrega a la URL los parámetros del filtro
     * @param 		$controlador 	String 		Nombre del controlador donde redirijirá la petición
     * @param 		$accion 		String 		Nombre del método receptor de la petición
     * @return 		void
     */
    public function filtrar($controlador = '', $accion = '')
    {
    	$redirect = array(
    		'controller' => $controlador,
    		'action' => $accion
    		);

		foreach ($this->request->data['Filtro'] as $campo => $valor) {
			if (!empty($valor)) {
				$redirect[$campo] = $valor;
			}
		}

    	$this->redirect($redirect);

    }

	public function admin_index()
	{	
		
		$this->verificarTienda();

		$paginate = array(); 
    	$conditions = array();
    	$total = 0;
    	$totalMostrados = 0;

		// Filtrado de dtes por formulario
		if ( $this->request->is('post') ) {

			$this->filtrar('dtes', 'index');

		}

		$paginate = array_replace_recursive($paginate, array(
			'limit' => 20,
			'conditions' => array(),
			'order' => array('Dte.fecha' => 'DESC'),
			'contain' => array('Orden')
			));


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'by':
						if ($valor == 'fol' && isset($this->request->params['named']['txt'])) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.folio LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}

						if ($valor == 'ord' && isset($this->request->params['named']['txt'])) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.id_order LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}

						if ($valor == 'rut' && isset($this->request->params['named']['txt'])) {
							$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.rut_receptor LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}
						
						break;
					case 'tyd':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.tipo_documento' => $valor)));
						break;
					case 'sta':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.estado' => $valor)));
						break;
					case 'dtf':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.fecha >=' => trim($valor))));
						break;
					case 'dtt':
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array('Dte.fecha <=' => trim($valor))));
						break;
				}
			}
		}


		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden', 'WebpayStore'));
		
		$this->paginate = $paginate;

		$dtes	= $this->paginate();
		$totalMostrados = $this->Dte->find('count');

		BreadcrumbComponent::add('DTE´s ');


		$estados = Hash::extract($dtes, '{n}.Dte.estado');

		$this->set(compact('dtes', 'estados'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Dte->create();
			if ( $this->Dte->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		$ordenes	= $this->Dte->Orden->find('list');
		$this->set(compact('ordenes'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Dte->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Dte->save($this->request->data) )
			{
				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}
		else
		{
			$this->request->data	= $this->Dte->find('first', array(
				'conditions'	=> array('Dte.id' => $id)
			));
		}
		$ordenes	= $this->Dte->Orden->find('list');
		$this->set(compact('ordenes'));
	}

	public function admin_delete($id = null)
	{
		$this->Dte->id = $id;
		if ( ! $this->Dte->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Dte->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}


	/**
	 * Método encargado de ajustar los datos al excel.
	 * @param  array  $data DTES
	 * @return array       datos del excel
	 */
	public function prepararExcel($data = array())
	{	
		$newData = array();

		foreach ($data as $indice => $valor) {
			$newData[$indice]['Dte']['id_order']                  = $valor['Dte']['id_order'];
			$newData[$indice]['Dte']['referencia']                = $valor['Orden']['reference'];

			# Identificador de la/s transacciones
			$newData[$indice]['Dte']['id_transaccion']            = implode(Hash::extract($valor['Orden'], 'OrdenPago.{n}.transaction_id'));
			$newData[$indice]['Dte']['metodo']                    = $valor['Orden']['payment'];
			$newData[$indice]['Dte']['total']                     = CakeNumber::currency($valor['Orden']['total_paid'], 'CLP');
			$newData[$indice]['Dte']['envio']                     = CakeNumber::currency($valor['Orden']['total_shipping'], 'CLP');
			$newData[$indice]['Dte']['folio']                     = (!empty($valor['Dte']['folio'])) ? $valor['Dte']['folio'] : 'No aplica';
			$newData[$indice]['Dte']['tipo_documento']            = $this->tipoDocumento[$valor['Dte']['tipo_documento']];
			$newData[$indice]['Dte']['rut_receptor']              = (!empty($valor['Dte']['rut_receptor'])) ? $valor['Dte']['rut_receptor'] : 'No aplica';
			$newData[$indice]['Dte']['estado']                    = $this->dteEstado($valor['Dte']['estado']);
			$newData[$indice]['Dte']['fecha']                     = $valor['Dte']['fecha'];
			
			# Webpay
			$newData[$indice]['Dte']['authorization_code_webpay'] = implode(Hash::extract($valor['Orden'], 'Carro.WebpayStore.{n}.authorization_code'));
			$newData[$indice]['Dte']['amount_webpay']             = implode(Hash::extract($valor['Orden'], 'Carro.WebpayStore.{n}.amount'));
			$newData[$indice]['Dte']['payment_type_webpay']       = implode(Hash::extract($valor['Orden'], 'Carro.WebpayStore.{n}.payment_type'));
			$newData[$indice]['Dte']['create_webpay']             = implode(Hash::extract($valor['Orden'], 'Carro.WebpayStore.{n}.create'));
			$newData[$indice]['Dte']['reponse_code']              = implode(Hash::extract($valor['Orden'], 'Carro.WebpayStore.{n}.reponse_code'));

		}

		return $newData;
	}


	public function admin_exportar()
	{	
		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
		set_time_limit(300);

		$this->verificarTienda();

		$query = array(
			'fields' => array(
				'Dte.id_order',
				'Dte.folio',
				'Dte.tipo_documento',
				'Dte.rut_receptor',
				'Dte.estado',
				'Dte.fecha'
			),
			'contain' => array(
				'Orden' => array(
					'fields' => array(
						'Orden.payment',
						'Orden.reference',
						'Orden.total_paid',
						'Orden.total_shipping'),
					'Carro' => array(
						'WebpayStore'
					)
				)
			)
		); 
    	
		$query = array_replace_recursive($query, array(
			'conditions' => array(),
			'order' => array('Dte.folio' => 'DESC')
			));


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'by':
						if ($valor == 'fol' && isset($this->request->params['named']['txt'])) {
							$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.folio LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}

						if ($valor == 'ord' && isset($this->request->params['named']['txt'])) {
							$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.id_order LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}

						if ($valor == 'rut' && isset($this->request->params['named']['txt'])) {
							$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.rut_receptor LIKE' => '%'.trim($this->request->params['named']['txt']).'%')));
						}
						
						break;
					case 'tyd':
						$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.tipo_documento' => $valor)));
						break;
					case 'sta':
						$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.estado' => $valor)));
						break;
					case 'dtf':
						$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.fecha >=' => trim($valor))));
						break;
					case 'dtt':
						$query = array_replace_recursive($query, array(
							'conditions' => array('Dte.fecha <=' => trim($valor))));
						break;
				}
			}
		}


		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden', 'Carro' , 'WebpayStore', 'OrdenPago'));

		$datos = $this->Dte->find('all', $query);

		
		$pagos = ClassRegistry::init('OrdenPago')->find('all', array(
			'conditions' => array(
				'OrdenPago.order_reference' => Hash::extract($datos, '{n}.Orden.reference')
			)			
		));

		foreach ($datos as $id => $vd) {
			foreach ($pagos as $ip => $vp) {
				if ($vd['Orden']['reference'] == $vp['OrdenPago']['order_reference']) {
					$datos[$id]['Orden']['OrdenPago'][] =  $vp['OrdenPago'];
				}
			}
		}

		$cabeceras =  array(
			'Pedido',
			'Referencia',
			'ID Transacción/es',
			'Medio de pago',
			'Total pagado',
			'Total envio',
			'Folio DTE',
			'Tipo de documento DTE',
			'Rut del receptor DTE',
			'Estado DTE',
			'Fecha emisión DTE',
			'Autorización Webpay',
			'Monto pagado Webpay',
			'Tipo de pago Webpay',
			'Fecha de pago Webpay',
			'Código de respuesta Webpay'
		);
		
		
		$datos  = $this->prepararExcel($datos);
		$campos = $cabeceras;
		$modelo = $this->Dte->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
