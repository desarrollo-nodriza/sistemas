<?php
App::uses('AppController', 'Controller');
App::uses('VentaDetalleProductosController', 'Controller');
App::uses('EmbalajeWarehousesController', 'Controller');
App::uses('DtesController', 'Controller');
App::uses('CakePdf', 'Plugin/CakePdf/Pdf');
App::uses('MetodoEnviosController', 'Controller');

//App::import('Vendor', 'Mercadopago', array('file' => 'Mercadopago/mercadopago.php'));
App::import('Vendor', 'Mercadolibre', array('file' => 'Meli/meli.php'));
App::import('Vendor', 'PDFMerger', array('file' => 'PDFMerger/PDFMerger.php'));

App::uses('CakeTime', 'Utility');

class VentasController extends AppController {

	//public $Mercadopago;
	public static $Mercadolibre;
	public $shell = false;


	public $components = array(
		'RequestHandler',
		'Linio',
		'Prestashop',
		'MeliMarketplace',
		'Mercadopago',
		'Toolmania',
		'LibreDte',
		'Starken',
		'Conexxion',
		'Boosmap',
		'Etiquetas',
		'LAFFPack',
		'BlueExpress',
		'WarehouseNodriza'
	);

	private $tipo_venta = [
		'Pago aceptado',
		'Transacción en curso'
	];
	

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

		foreach ($this->request->data['Venta'] as $campo => $valor) {
			if ($valor != '') {
				$redirect[$campo] = str_replace('/', '-', $valor);
			}
		}
		
    	$this->redirect($redirect);

    }


    /**
     * Indice
     * @return [type] [description]
     */
	public function admin_index () {
		
		$condiciones = array(
			'Venta.bodega_id IN' => Hash::extract($this->Auth->user('Bodega'), '{n}.id') 
		);

		$joins       = array();
		$group       = array();
		$fields      = array(
			'Venta.id', 
			'Venta.id_externo', 
			'Venta.referencia', 
			'Venta.fecha_venta', 
			'Venta.total', 
			'Venta.atendida', 
			'Venta.activo',
			'Venta.venta_estado_id', 
			'Venta.tienda_id', 
			'Venta.marketplace_id', 
			'Venta.medio_pago_id', 
			'Venta.venta_cliente_id', 
			'Venta.prioritario', 
			'Venta.picking_estado', 
			'Venta.venta_manual',
			'Venta.total',
			'Venta.bodega_id'
		);

		

		$FiltroVenta                = '';
		$FiltroVentaId              = '';
		$FiltroCliente              = '';
		$FiltroTienda               = '';
		$FiltroMarketplace          = '';
		$FiltroMedioPago            = '';
		$FiltroVentaEstadoCategoria = '';
		$FiltroAtributoGrupo = '';
		$FiltroAtributo = '';
		$FiltroVentaOrigen 			= '';
		$FiltroPrioritario          = '';
		$FiltroPicking              = '';
		$FiltroFechaDesde           = '';
		$FiltroFechaHasta           = '';
		$FiltroDte           	    = '';
		$FiltroMontoDesde           = '';
		$FiltroMontoHasta           = '';
		$FiltroAdministrador        = '';
		$FiltroBodega        		= '';
		$FiltroMetodoEnvio          = '';

		// Filtrado de ordenes por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ventas', 'index');
		}


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {

					case 'filtroventa':
						$FiltroVenta = trim($valor);

						if ($FiltroVenta != "") {

							$condiciones["OR"] = array(
								"Venta.id_externo LIKE '%" .$FiltroVenta. "%'",
								"Venta.referencia LIKE '%" .$FiltroVenta. "%'"
							);
							
						}
						break;

					case 'filtroventa_id':
						$FiltroVentaId = trim($valor);

						if ($FiltroVentaId != "") {
							$condiciones['Venta.id'] = explode(',',$FiltroVentaId);
						}
						break;
					case 'filtrocliente':
						$FiltroCliente = trim($valor);

						if ($FiltroCliente != "") {

							$joins[] = array(
								'table' => 'rp_venta_clientes',
								'alias' => 'clientes',
								'type' => 'INNER',
								'conditions' => array(
									'clientes.id = Venta.venta_cliente_id',
									'OR' => array(
										"clientes.nombre LIKE '%" .$FiltroCliente. "%'",
										"clientes.apellido LIKE '%" .$FiltroCliente. "%'",
										"clientes.rut LIKE '%" .$FiltroCliente. "%'",
										"clientes.email LIKE '%" .$FiltroCliente. "%'",
										"clientes.telefono LIKE '%" .$FiltroCliente. "%'"
									)
								)
							);
							
						}
						break;
					case 'tienda_id':
						$FiltroTienda = $valor;

						if ($FiltroTienda != "") {
							$condiciones['Venta.tienda_id'] = $FiltroTienda;
						} 
						break;
					case 'bodega_id':
						$FiltroBodega = $valor;

						if ($FiltroBodega != "") {
							$condiciones['Venta.bodega_id'] = $FiltroBodega;
						} 
						break;
					case 'marketplace_id':
						$FiltroMarketplace = $valor;

						if ($FiltroMarketplace != "" && $FiltroMarketplace != 999) {
							$condiciones['Venta.marketplace_id'] = ($FiltroMarketplace == 0) ? null : $FiltroMarketplace;
							$condiciones['Venta.venta_manual'] = 0;
						}

						// Pos de venta
						if ($FiltroMarketplace == 999) {
							$condiciones['Venta.venta_manual'] = 1;
						}

						break;
					case 'medio_pago_id':
						$FiltroMedioPago = $valor;

						if ($FiltroMedioPago != "") {
							$condiciones['Venta.medio_pago_id'] = $FiltroMedioPago;
						} 
						break;
					case 'venta_estado_categoria_id':
						$FiltroVentaEstadoCategoria = $valor;

						if ($FiltroVentaEstadoCategoria != "") {

							$joins[] = array(
								'table' => 'rp_venta_estados',
								'alias' => 'ventas_estados',
								'type' => 'INNER',
								'conditions' => array(
									'ventas_estados.id = Venta.venta_estado_id',
									"ventas_estados.venta_estado_categoria_id = " .$FiltroVentaEstadoCategoria
								)
							);

						}
						break;
					case 'atributo':
						$FiltroAtributo = $valor;

						if ($FiltroAtributo != "") {

							$joins[] = array(
								'table' => 'rp_venta_detalles',
								'alias' => 'venta_detalles',
								'type' => 'INNER',
								'conditions' => array(
									'venta_detalles.venta_id= Venta.id'
								)
							);

							$joins[] = array(
								'table' => 'rp_venta_detalles_atributos',
								'alias' => 'vd_atributos',
								'type' => 'INNER',
								'conditions' => array(
									'vd_atributos.venta_detalle_id= venta_detalles.id',
									'vd_atributos.atributo_id' =>  $FiltroAtributo
								)
							);

						}
						break;
					case 'prioritario':
						$FiltroPrioritario = $valor;

						if ($FiltroPrioritario != "") {
							$condiciones['Venta.prioritario'] = $FiltroPrioritario;
						} 
						break;
					case 'picking_estado':
						$FiltroPicking = $valor;

						if ($FiltroPicking != "") {
							$condiciones['Venta.picking_estado'] = $FiltroPicking;
						} 
						break;
					case 'FechaDesde':
						$FiltroFechaDesde = trim($valor);

						if ($FiltroFechaDesde != "") {

							$ArrayFecha = explode("-", $FiltroFechaDesde);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 00:00:00"));

							$condiciones["Venta.fecha_venta >="] = $Fecha;

						}
						break;
					case 'FechaHasta':
						$FiltroFechaHasta = trim($valor);

						if ($FiltroFechaHasta != "") {

							$ArrayFecha = explode("-", $FiltroFechaHasta);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 23:59:59"));

							$condiciones["Venta.fecha_venta <="] = $Fecha;

						} 
						break;

					case 'MontoDesde':
						

						if (isset($valor)) {

							$FiltroMontoDesde = trim($valor);
							$condiciones["Venta.total >="] = $FiltroMontoDesde;
							

						} 
						break;
					case 'MontoHasta':
						

						if (isset($valor)) {							
							$FiltroMontoHasta = trim($valor);
							$condiciones["Venta.total <="] = $FiltroMontoHasta;
							

						} 
						break;
					case 'facturado':

						$FiltroDte = trim($valor);

						if ($FiltroDte == 1) { // Facturao
							$joins[] = array(
								'table' => 'rp_dtes',
								'alias' => 'dtes',
								'type' => 'INNER',
								'conditions' => array(
									'dtes.venta_id = Venta.id',
									"dtes.tipo_documento" => array(33, 39),
									"dtes.estado = 'dte_real_emitido'",
									"dtes.invalidado = 0"
								)
							);
						}else if ($FiltroDte == 2){ # Mal facturado
							
							
							$db = $this->Venta->Dte->getDataSource();
							
							$subQuery = $db->buildStatement(
							    array(
							        'fields'     => array('Dte2.venta_id'),
							        'table'      => $db->fullTableName($this->Venta->Dte),
							        'alias'      => 'Dte2',
							        'limit'      => null,
							        'offset'     => null,
							        'joins'      => array(),
							        'conditions' => array('Dte2.estado' => 'dte_real_emitido', 'Dte2.venta_id <>' => NULL, 'Dte2.tipo_documento' => array(33, 39), 'Dte2.invalidado' => 0),
							        'order'      => null,
							        'group'      => null
							    ),
							    $this->Venta->Dte
							);

							$subQuery = 'Venta.id NOT IN (' . $subQuery . ') ';
							$subQueryExpression = $db->expression($subQuery);
							
							$joins[] = array(
								'table' => 'rp_dtes',
								'alias' => 'dtes',
								'type' => 'INNER',
								'conditions' => array(
									'dtes.venta_id = Venta.id'									
								)
							);

							#$condiciones[] = $subQueryExpression->value;
							$condiciones['OR'] = array(
								'dtes.id' => NULL,
							    $subQueryExpression->value
							);

							/*
							$joins[] = array(
								'table' => 'rp_dtes',
								'alias' => 'dtes',
								'type' => 'INNER',
								'conditions' => array(
									'dtes.venta_id = Venta.id',
									"dtes.estado <> 'dte_real_emitido'",
									'OR' => array(
							            'dtes.id' => NULL,
							            'dtes.estado' => array('no_generado', 'dte_temporal_no_emitido')
							        )
								)
							);*/

							$group[] = 'Venta.id';
						
						}else{ # Sin factura

							$joins[] = array(
								'table' => 'rp_dtes',
								'alias' => 'dtes',
								'type' => 'LEFT',
								'conditions' => array(
									'dtes.venta_id = Venta.id',
								)
							);

							$condiciones['dtes.id'] = NULL;
						}

						break;
				
					case 'canal_venta_id' :
						
						$FiltroVentaOrigen = $valor;

						if ($FiltroVentaOrigen != "") {
							$condiciones['Venta.canal_venta_id'] = $FiltroVentaOrigen;
						}
						break;
					case 'administrador_id' :
						
						$FiltroAdministrador = $valor;

						if ($FiltroAdministrador != "") {
							$condiciones['Venta.administrador_id'] = $FiltroAdministrador;
						}
						break;
					case 'metodos_de_envios' :
						$FiltroMetodoEnvio = $valor;
				
						if ($FiltroMetodoEnvio != "") {
							$condiciones['Venta.metodo_envio_id'] = $FiltroMetodoEnvio;
						}
						break;
				
				}
			}
		}

		$paginate = array(
			'recursive' => 0,
			'contain' => array(
				'VentaEstado' => array(
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo', 'VentaEstadoCategoria.venta', 'VentaEstadoCategoria.final'
						)
					),
					'fields' => array(
						'VentaEstado.id', 'VentaEstado.nombre', 'VentaEstado.venta_estado_categoria_id', 'VentaEstado.permitir_dte', 'VentaEstado.permitir_retiro_oc', 'VentaEstado.notificacion_cliente'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.id', 'Tienda.nombre'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.id', 'Marketplace.nombre'
					)
				),
				'MedioPago' => array(
					'fields' => array(
						'MedioPago.id', 'MedioPago.nombre'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono',
					)
				),
				'VentaTransaccion' => array(
					'fields' => array(
						'VentaTransaccion.nombre', 'VentaTransaccion.monto'
					)
				),
				'Dte' => array(
					'fields' => array(
						'Dte.id', 'Dte.estado'
					)
				),
				'Bodega' => array(
					'fields' => array(
						'Bodega.nombre',
						'Bodega.codigo_sucursal'
					)
				)
			),
			'conditions' => $condiciones,
			'joins' => $joins,
			'fields' => $fields,
			'group' => $group,
			'order' => array('Venta.fecha_venta' => 'DESC'),
			'limit' => 20
		);

		//----------------------------------------------------------------------------------------------------
		$this->paginate = $paginate;
		$ventas = $this->paginate();
		
		//----------------------------------------------------------------------------------------------------
		$tiendas = $this->Venta->Tienda->find(
			'list',
			array(
				'conditions' => array(
					'Tienda.activo' => 1
				),
				'order' => 'Tienda.nombre ASC'
			)
		);

		//----------------------------------------------------------------------------------------------------
		$marketplaces = $this->Venta->Marketplace->find(
			'list',
			array(
				'conditions' => array(
					'Marketplace.activo' => 1
				),
				'order' => 'Marketplace.nombre ASC'
			)
		);

		$marketplaces[0] = 'Sólo tienda';
		$marketplaces[999] = 'Pos de Venta';

		//----------------------------------------------------------------------------------------------------
		$ventaEstadoCategorias = $this->Venta->VentaEstado->VentaEstadoCategoria->find('list');

		//----------------------------------------------------------------------------------------------------
		$medioPagos = $this->Venta->MedioPago->find(
			'list',
			array(
				'conditions' => array(
					'MedioPago.activo' => 1
				),
				'order' => 'MedioPago.nombre ASC'
			)
		);

		# Atributos
		$atributos = ClassRegistry::init('Atributo')->find('list', array(
			'fields' => array(
				'Atributo.nombre'
			),
		));

		$picking = ClassRegistry::init('Venta')->picking_estados_lista;
		
		# Mercadolibre conectar
		$meliConexion = $this->admin_verificar_conexion_meli();

		$vendedores = ClassRegistry::init('Administrador')->find('list',[
			'joins' => [array(
				'table' => 'rp_roles',
				'alias' => 'rol',
				'type' => 'INNER',
				'conditions' => array(
					'rol.id = Administrador.rol_id',
				)
			)],
			'conditions'=>[
				'rol.app_perfil'=> 'vendedor',
				'Administrador.activo'=>true
			]
		]);

		# Canales de venta
		$canal_ventas = $this->Venta->CanalVenta->find('list', array(
			'conditions' => array(
				'activo' => 1
			)
		));

		# Bodegas permitidas para el rol
		$bodegas = [];

		foreach ($this->Auth->user('Bodega') as $b)
		{
			$bodegas[$b['id']] = $b['nombre'];
		}
		
		$metodos_de_envios=[];
		$metodoEnvios_sin_procesar = ClassRegistry::init('MetodoEnvio')->find('all', array(
			'contain'=>[
				'Bodega'=>['fields'=>'Bodega.nombre'

			]],
			'fields'=>['MetodoEnvio.id','MetodoEnvio.nombre','MetodoEnvio.dependencia'],
			'conditions' => [
				'MetodoEnvio.activo' => 1,
				'MetodoEnvio.bodega_id' => Hash::extract(CakeSession::read('Auth.Administrador.Bodega'), '{n}.id') 
			]));

		foreach ($metodoEnvios_sin_procesar as $value) {
			$metodos_de_envios[$value['MetodoEnvio']['id']] ="{$value['Bodega']['nombre']} - {$value['MetodoEnvio']['nombre']} ".(isset($value['MetodoEnvio']['dependencia'])?"| Dependencia {$value['MetodoEnvio']['dependencia']}":'');
		}

		BreadcrumbComponent::add('Ventas', '/ventas');

		$this->set(compact(
			'ventas', 
			'tiendas', 
			'marketplaces', 
			'ventaEstadoCategorias', 
			'medioPagos',
			'atributos',
			'FiltroVenta', 
			'FiltroCliente', 
			'FiltroTienda', 
			'FiltroBodega',
			'FiltroMarketplace', 
			'FiltroMedioPago', 
			'FiltroVentaEstadoCategoria', 
			'FiltroPrioritario', 
			'FiltroPicking', 
			'FiltroFechaDesde', 
			'FiltroFechaHasta', 
			'FiltroDte', 
			'FiltroMetodoEnvio', 
			'meliConexion', 
			'picking', 
			'FiltroVentaOrigen',
			'FiltroMontoDesde',
			'FiltroMontoHasta',
			'FiltroVentaId',
			'FiltroAtributo',
			'FiltroAdministrador',
			'vendedores',
			'canal_ventas',
			'bodegas',
			'metodos_de_envios'
		));

	}


	public function admin_recalcular_totales_ventas()
	{	
		ini_set('max_execution_time', '0');
		
		$ventas = $this->Venta->find('all', array(
			'conditions' => array(
				'Venta.fecha_venta >' => '2018-01-01 00:00:00'
			),
			'contain' => array(
				'VentaDetalle' => array(
					'fields' => array(
						'VentaDetalle.id'
					)
				)
			),
			'fields' => array(
				'Venta.id'
			)
		));

		$total = 0;

		foreach ($ventas as $iv => $v) {
			foreach ($v['VentaDetalle'] as $ivd => $vd) {
				if(ClassRegistry::init('VentaDetalle')->recalcular_total_producto($vd['id'])){
					$total++;
				}
			}
		}

	}



	/**
	 * [admin_obtener_venta_manual description]
	 * @return [type] [description]
	 */
	public function admin_obtener_venta_manual()
	{
		$log = array();

		if ($this->request->is('post')) {
			
			#Vemos si existe en la BD
			$qry = array(
				'conditions' => array(
					'Venta.id_externo' => $this->request->data['Venta']['id_externo'],
					'Venta.tienda_id' => $this->request->data['Venta']['tienda_id']
				),
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.venta_estado_id', 'Venta.estado_anterior', 'Venta.venta_estado_responsable'
				)
			);


			$tipo_canal = 'Prestashop';

			if (!empty($this->request->data['Venta']['marketplace_id'])) {
				$qry = array_replace_recursive($qry, array(
					'conditions' => array(
						'Venta.marketplace_id' => $this->request->data['Venta']['marketplace_id']
					)
				));

				$tipo_market = ClassRegistry::init('Marketplace')->field('marketplace_tipo_id', array('id' => $this->request->data['Venta']['marketplace_id']));

				$tipo_canal = ($tipo_market == 1) ? 'Linio' : 'Mercadolibre';
			}

			$existe = $this->Venta->find('first', $qry);

			$id_externo = trim($this->request->data['Venta']['id_externo']);

			switch ($tipo_canal) {
				case 'Prestashop':

					if (empty($existe)) {

						$accion = $this->crear_venta_prestashop($this->request->data['Venta']['tienda_id'], $id_externo);

						$log[] = array(
							'Log' => array(
								'administrador' => 'Prestashop crear: ' . $this->Auth->user('email'),
								'modulo' => 'Ventas',
								'modulo_accion' => json_encode($this->request->data)
							)
						);

						if ($accion) {
							$this->Session->setFlash('Venta con Id Externo #'. $id_externo . ' creada con éxito', null, array(), 'success');
						}else{
							$this->Session->setFlash('No fue posible obtener la venta. Verifique los campos.', null, array(), 'danger');
						}

					}else{

						$accion = $this->actualizar_venta_prestashop($this->request->data['Venta']['tienda_id'], $id_externo);

						$log[] = array(
							'Log' => array(
								'administrador' => 'Prestashop actualizar: ' . $this->Auth->user('email'),
								'modulo' => 'Ventas',
								'modulo_accion' => json_encode($this->request->data)
							)
						);

						if ($accion) {
							$this->Session->setFlash('Venta #'. $id_externo . ' actualizada con éxito', null, array(), 'success');
						}else{
							$this->Session->setFlash('No fue posible obtener la venta. Verifique los campos.', null, array(), 'danger');
						}
					}

					break;
				
				case 'Linio':
					
					if (empty($existe)) {
						$accion = $this->crear_venta_linio($this->request->data['Venta']['marketplace_id'], $id_externo);

						$log[] = array(
							'Log' => array(
								'administrador' => 'Linio crear: ' . $this->Auth->user('email'),
								'modulo' => 'Ventas',
								'modulo_accion' => json_encode($this->request->data)
							)
						);

						if ($accion) {
							$this->Session->setFlash('Venta #'. $id_externo . ' creada con éxito', null, array(), 'success');
						}else{
							$this->Session->setFlash('No fue posible obtener la venta. Verifique los campos.', null, array(), 'danger');
						}

					}else{

						$accion = $this->actualizar_venta_linio($this->request->data['Venta']['marketplace_id'], $id_externo, $existe);

						$log[] = array(
							'Log' => array(
								'administrador' => 'Linio actualizar: ' . $this->Auth->user('email'),
								'modulo' => 'Ventas',
								'modulo_accion' => json_encode($this->request->data)
							)
						);

						if ($accion) {
							$this->Session->setFlash('Venta #'. $id_externo . ' actualizada con éxito', null, array(), 'success');
						}else{
							$this->Session->setFlash('No fue posible obtener la venta. Verifique los campos.', null, array(), 'danger');
						}
					}


					break;
				case 'Mercadolibre':
					
					if (!empty($existe)) {

						$accion = $this->actualizar_venta_meli($this->request->data['Venta']['marketplace_id'], $id_externo);

						$log[] = array(
							'Log' => array(
								'administrador' => 'Meli Actualizar: ' . $this->Auth->user('email'),
								'modulo' => 'Ventas',
								'modulo_accion' => json_encode($this->request->data)
							)
						);

						if ($accion) {
							$this->Session->setFlash('Venta #'. $id_externo . ' actualizada con éxito', null, array(), 'success');
						}else{
							$this->Session->setFlash('No fue posible obtener la venta. Verifique los campos.', null, array(), 'danger');
						}

					}else{

						$log[] = array(
							'Log' => array(
								'administrador' => 'Meli Crear: ' . $this->Auth->user('email'),
								'modulo' => 'Ventas',
								'modulo_accion' => json_encode($this->request->data)
							)
						);

						$accion = $this->crear_venta_meli($this->request->data['Venta']['marketplace_id'], $id_externo);
						
						if ($accion) {
							$this->Session->setFlash('Venta #'. $id_externo . ' creada con éxito', null, array(), 'success');
						}else{
							$this->Session->setFlash('No fue posible obtener la venta. Verifique los campos.', null, array(), 'danger');
						}
					}

					break;
			}


			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);

		}

		$this->redirect($this->referer('/', true));

	}


	/**
     * Indice bodega
     * @return [type] [description]
     */
	public function admin_index_bodega () {

		$metodo_envios = ClassRegistry::init('MetodoEnvio')->find('list');

		$tiendas = ClassRegistry::init('Tienda')->find('list'); 

		$canales = ClassRegistry::init('Marketplace')->find('list');

		$comunas = array_unique($this->Venta->find('list', array(
			'fields' => array(
				'Venta.comuna_entrega'
			), 
			'order' => array(
				'Venta.comuna_entrega' => 'ASC'
			),
			'conditions' => array(
				'Venta.fecha_venta >=' => date("Y-m-d H:i:s",strtotime(date('Y-m-d')."-2 month"))
			)
		)));

		BreadcrumbComponent::add('Ventas', '/ventas/index_bodega');

		$this->set(compact('metodo_envios', 'tiendas', 'canales', 'comunas'));

	}


	/**
	 * [admin_obtener_ventas_preparacion_modal description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	// ! En desuzo
	public function admin_obtener_ventas_preparacion_modal($id)
	{
		$this->layout   = 'ajax';
		$this->viewPath = 'Ventas/ajax';
		$this->output   = '';

		$venta  = $this->preparar_venta($id);
		
		# quitamos de la lista de productos los items que no corresponda embalar en esta ocación
		foreach ($venta['VentaDetalle'] as $ivd => $detalle)
		{
			if ($detalle['cantidad_reservada'] <= 0)
			{
				unset($venta['VentaDetalle'][$ivd]);
			}
		}

		$url    = Router::url( sprintf('/api/ventas/%d.json', $venta['Venta']['id']), true);
		$tamano = '500x500';

		$volumenMaximo = $venta['Tienda']['volumen_enviame'];
		
		$Enviame = $this->Components->load('Enviame');

		# Iniciamos LAFF
		$Enviame->LAFFinit();

		# Bultos sugeridos
		$bultos = $Enviame->obtener_bultos_venta($venta, $volumenMaximo);
		
		# Información de los bultos
		foreach ($bultos as $ib => $b) : 	
			
			$items = array(); 
			
			foreach ($b['items'] as $item) : 
				$items[] = '<li>' . Hash::extract($venta['VentaDetalle'], '{n}.VentaDetalleProducto[id='.$item['id'].'].nombre')[0] . '</li>';
			endforeach;
      		
      		$bultos[$ib]['items'] = implode('', $items);
      		
      	endforeach;

		$this->set(compact('venta', 'url', 'tamano', 'bultos'));

		$vista = $this->render('venta_preparacion_modal');

		$response =  array(
			'code'    => 200,
			'message' => 'Venta obtenida con éxito',
			'html' => $vista->body()
		);

		echo json_encode($response);
		exit;

	}



	/**
	 * [admin_obtener_ventas_preparacion description]
	 * @param  integer $limit1  [description]
	 * @param  integer $offset1 [description]
	 * @param  integer $limit2  [description]
	 * @param  integer $offset2 [description]
	 * @return [type]           [description]
	 */
	public function admin_obtener_ventas_preparacion($limit1 = 10, $offset1 = 0, $limit2 = 10, $offset2 = 0, $id_venta = 0, $id_metodo_envio = 0, $id_marketplace = 0, $id_tienda = 0, $comuna = '')
	{	
		ini_set('memory_limit', '-1');
		set_time_limit(0);

		$estados_ids = Hash::extract(ClassRegistry::init('VentaEstadoCategoria')->find('all', array('conditions' => array('venta' => 1, 'final' => 0, 'excluir_preparacion' => 0), 'fields' => array('id'))), '{n}.VentaEstadoCategoria.id');

		$estados_preparados_ids = Hash::extract(ClassRegistry::init('VentaEstadoCategoria')->find('all', array('conditions' => array('venta' => 1, 'final' => 0), 'fields' => array('id'))), '{n}.VentaEstadoCategoria.id');

		$ventas_empaquetar         = $this->Venta->obtener_ventas_preparar('empaquetar', 20, 0, $estados_ids, $id_venta, $id_metodo_envio, $id_marketplace, $id_tienda, $comuna);
		$ventas_empaquetar_total   = $this->Venta->obtener_ventas_preparar('empaquetar', -1, 0, $estados_ids, $id_venta, $id_metodo_envio, $id_marketplace, $id_tienda, $comuna);
		$ventas_empaquetando       = $this->Venta->obtener_ventas_preparar('empaquetando', -1, 0, $estados_ids);
		$ventas_empaquetando_total = $this->Venta->obtener_ventas_preparar('empaquetando', -1, 0, $estados_ids);
		$ventas_empaquetado        = $this->Venta->obtener_ventas_preparadas('empaquetado', 20, 0, $estados_preparados_ids);
		$ventas_empaquetado_total  = $this->Venta->obtener_ventas_preparadas('empaquetado', -1, 0, $estados_preparados_ids);
		
		$this->layout = 'ajax';

		$html_empaquetar   = '';
		$html_empaquetando = '';
		$html_empaquetado  = '';

		$venta_estados = ClassRegistry::init('VentaEstado')->obtener_estados_logistica(true);

		foreach ($ventas_empaquetar as $iv => $ve) {

			$this->viewPath   = 'Ventas/ajax';
			$this->output     = '';
			
			$venta  = $this->Venta->obtener_venta_por_id_tiny($ve['Venta']['id']);

			$url    = Router::url( sprintf('/api/ventas/%d.json', $venta['Venta']['id']), true);
			$tamano = '500x500';
			
			$this->set(compact('venta', 'url', 'tamano', 'venta_estados'));

			$vista = $this->render('venta_preparacion');
			$html_empaquetar .= $vista->body();
			$offset1++;

		}

		$html_empaquetar .= '<div class="task-drop push-down-10">
            <span class="fa fa-cloud"></span>
            Arrastra la venta aquí para reiniciar el proceso
        	</div>';


		foreach ($ventas_empaquetando as $iv => $ve) {
			$this->viewPath   = 'Ventas/ajax';
			$this->output     = '';

			$venta  = $this->Venta->obtener_venta_por_id_tiny($ve['Venta']['id']);

			$url    = Router::url( sprintf('/api/ventas/%d.json', $venta['Venta']['id']), true);
			$tamano = '120x120';
			
			$this->set(compact('venta', 'url', 'tamano', 'venta_estados'));

			$vista = $this->render('venta_preparacion');
			$html_empaquetando .= $vista->body();

			$offset2++;

		}

		$html_empaquetando .= '<div class="task-drop push-down-10">
            <span class="fa fa-cloud"></span>
            Arrastra la venta aquí para comenzar a procesar
        </div>';


        foreach ($ventas_empaquetado as $iv => $ve) {
			$this->viewPath   = 'Ventas/ajax';
			$this->output     = '';

			$venta  = $this->Venta->obtener_venta_por_id_tiny($ve['Venta']['id']);
			$url    = Router::url( sprintf('/api/ventas/%d.json', $venta['Venta']['id']), true);
			$tamano = '500x500';
			
			$this->set(compact('venta', 'url', 'tamano', 'venta_estados'));

			$vista = $this->render('venta_preparacion');
			$html_empaquetado .= $vista->body();

			$offset2++;

		}

		$html_empaquetado .= '<div class="task-drop push-down-10">
            <span class="fa fa-cloud"></span>
            Arrastra la venta aquí para finalizar su prepración
        </div>';


		$response =  array(
			'code'    => 200,
			'message' => 'Ventas obtenidas con éxito',
			'data'    => array(
				'empaquetar'   => array(
					'html' => $html_empaquetar,
					'limit' => $limit1 + 10,
					'offset' => $offset1,
					'total' => count($ventas_empaquetar_total)
				),
				'empaquetando' => array(
					'html' => $html_empaquetando,
					'limit' => $limit2 + 10,
					'offset' => $offset2,
					'total' => count($ventas_empaquetando_total)
				),
				'empaquetado' => array(
					'html' => $html_empaquetado,
					'limit' => $limit2 + 10,
					'offset' => $offset2,
					'total' => count($ventas_empaquetado_total)
				)
			)
		);

		echo json_encode($response);
		exit;
	}



	public function admin_generar_envio_externo_manual($id)
	{

		if (!$this->Venta->exists($id)) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$venta = $this->Venta->obtener_venta_por_id($id);

		$metodo_envio_enviame = explode(',', $venta['Tienda']['meta_ids_enviame']);

		# Creamos pedido en enviame si corresponde
		if (in_array($venta['Venta']['metodo_envio_id'], $metodo_envio_enviame) && $venta['Tienda']['activo_enviame']) {

			$Enviame = $this->Components->load('Enviame');

			# conectamos con enviame
			$Enviame->conectar($venta['Tienda']['apikey_enviame'], $venta['Tienda']['company_enviame'], $venta['Tienda']['apihost_enviame']);

			$resultadoEnviame = $Enviame->crearEnvio($venta);

			if ($resultadoEnviame) {
				$this->Session->setFlash('Envío creado con éxito en Starken.', null, array(), 'success');
			} else {
				$this->Session->setFlash('No fue posible crear el envío Starken.', null, array(), 'danger');
			}
		} elseif ($venta['MetodoEnvio']['dependencia'] == 'starken' && $venta['MetodoEnvio']['generar_ot']) {
			# Es una venta para starken

			# Creamos cliente starken
			$this->Starken->crearCliente($venta['MetodoEnvio']['rut_api_rest'], $venta['MetodoEnvio']['clave_api_rest'], $venta['MetodoEnvio']['rut_empresa_emisor'], $venta['MetodoEnvio']['rut_usuario_emisor'], $venta['MetodoEnvio']['clave_usuario_emisor']);

			# Creamos la OT
			if ($this->Starken->generar_ot($venta)) {

				$this->Session->setFlash('Envío creado con éxito.', null, array(), 'success');
			} else {
				$this->Session->setFlash('No fue posible crear el envío.', null, array(), 'danger');
			}
		} elseif ($venta['MetodoEnvio']['dependencia'] == 'conexxion' && $venta['MetodoEnvio']['generar_ot']) {
			# Es una venta para conexxion

			# Creamos cliente conexxion
			$this->Conexxion->crearCliente($venta['MetodoEnvio']['api_key']);

			# Creamos la OT
			if ($this->Conexxion->generar_ot($venta)) {
				$this->Session->setFlash('Envío creado con éxito en Conexxion.', null, array(), 'success');
			} else {
				$this->Session->setFlash('No fue posible crear el envío Conexxion.', null, array(), 'danger');
			}
		} elseif ($venta['MetodoEnvio']['dependencia'] == 'boosmap' && $venta['MetodoEnvio']['generar_ot']) {
			# Es una venta para boosmap

			# Creamos cliente boosmap
			$this->Boosmap->crearCliente($venta['MetodoEnvio']['boosmap_token']);

			# Creamos la OT
			if ($this->Boosmap->generar_ot($venta)) {


				$this->Session->setFlash('Envío creado con éxito en Boosmap.', null, array(), 'success');
			} else {
				$this->Session->setFlash('No fue posible crear el envío Boosmap.', null, array(), 'danger');
			}
		} elseif ($venta['MetodoEnvio']['dependencia'] == 'blueexpress' && $venta['MetodoEnvio']['generar_ot']) {
			# Es una venta para blueexpress

			# Creamos cliente blueexpress
			$this->BlueExpress->crearCliente($venta['MetodoEnvio']['token_blue_express'], $venta['MetodoEnvio']['cod_usuario_blue_express'], $venta['MetodoEnvio']['cta_corriente_blue_express']);

			# Creamos la OT
			if ($this->BlueExpress->generar_ot($venta)) {


				$this->Session->setFlash('Envío creado con éxito en BlueExpress.', null, array(), 'success');
			} else {
				$this->Session->setFlash('No fue posible crear el envío BlueExpress.', null, array(), 'danger');
			}
		} else {
			$this->Session->setFlash('La venta no aplica para usar un currier externo.', null, array(), 'danger');
		}

		$this->redirect($this->referer('/', true));
	}



	/**
	 * [admin_cambiar_estado description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_cambiar_estado($id)
	{

		$respuesta = array(
			'code' => 501,
			'message' => 'Error inexplicable'
		);

		if (!$this->Venta->exists($id)) {
			$respuesta['code'] = 404;
			$respuesta['message'] = 'No se encontró la venta en nuestros registros.';
			echo json_encode($respuesta);
			exit;
		}

		if (!$this->request->is('post')) {
			$respuesta['code'] = 502;
			$respuesta['message'] = 'Método no permitido.';
			echo json_encode($respuesta);
			exit;
		}

		# Quitar los productos reservados de la bodega
		$detalles = ClassRegistry::init('VentaDetalle')->find('all', array(
			'conditions' => array(
				'VentaDetalle.venta_id' => $id,
				'VentaDetalle.completo' => 0,
			)
		));

		if (array_sum(Hash::extract($detalles, '{n}.VentaDetalle.confirmado_app')) != count(Hash::extract($detalles, '{n}.VentaDetalle.id'))) {
			$respuesta['code'] = 503;
			$respuesta['message'] = 'Debes confirmar los productos de la venta';
			echo json_encode($respuesta);
			exit;
		}

		try {
			$cambiar_estado = $this->cambiarEstado($id, $this->request->data['Venta']['id_externo'], $this->request->data['Venta']['venta_estado_id'], $this->request->data['Venta']['tienda_id'], $this->request->data['Venta']['marketplace_id'], '', '', $this->Session->read('Auth.Administrador.nombre'));
		} catch (Exception $e) {
			$respuesta['code'] = 506;
			$respuesta['message'] = $e->getMessage();
			echo json_encode($respuesta);
			exit;
		}

		$log = array();

		$subestado = (isset($this->request->data['Venta']['picking_estado'])) ? $this->request->data['Venta']['picking_estado'] : '';

		if ($cambiar_estado) {

			if ($subestado == 'empaquetado') {

				$this->Venta->id = $id;
				$this->Venta->saveField('picking_fecha_termino', date('Y-m-d H:i:s'));
				$this->Venta->saveField('picking_estado', $subestado);
				$this->Venta->saveField('prioritario', 0);

				# Sub estados OC de la venta
				if (array_sum(Hash::extract($detalles, '{n}VentaDetalle.cantidad_pendiente_entrega')) > 0) {
					$this->Venta->saveField('subestado_oc', 'parcialmente_entregado');
				} else {
					$this->Venta->saveField('subestado_oc', 'entregado');
				}
			}

			$respuesta['code'] = 200;
			$respuesta['message'] = 'Estado actualizado con éxito.';
		}

		echo json_encode($respuesta);
		exit;
	}


	/**
	 * [admin_cambiar_subestado description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	// ! Metodo obsoleto y en desuzo
	public function admin_cambiar_subestado($id = null)
	{	
		$respuesta = array(
			'code' => 501,
			'message' => 'Error inexplicable'
		);

		if ( ! $this->Venta->exists($id) ) {
			$respuesta['code'] = 404;
			$respuesta['message'] = 'No se encontró la venta en nuestros registros.';
			echo json_encode($respuesta);
			exit;
		}

		if (!$this->request->is('post')) {
			$respuesta['code'] = 502;
			$respuesta['message'] = 'Método no permitido.';
			echo json_encode($respuesta);
			exit;
		}

		$subestado = $this->request->data['subestado'];

		
		$this->Venta->id = $id;
		$subestadoActual = $this->Venta->field('picking_estado');

		if ($subestado == $subestadoActual) {
			$respuesta['code'] = 200;
			$respuesta['message'] = 'Ok';
			echo json_encode($respuesta);
			exit;
		}

		$venta = $this->Venta->obtener_venta_por_id($id);

		# Verificamos que todos los productos de la venta se encuentren en la bodega principal
		foreach ($venta['VentaDetalle'] as $ivd => $vd) {
			
			$bodega_principal = ClassRegistry::init('Bodega')->find('first', array('conditions' => array('Bodega.principal' => 1), 'limit' => 1, 'fields' => array('Bodega.id')))['Bodega']['id'];

			# Obtenemos las unidades en la bodega principal
			$cant_en_bodega = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($vd['venta_detalle_producto_id'], $bodega_principal, true);

			# Si no hay suficiente stock en la bodega principal, se mueve el stock de las bodegas con stock hacia la principal
			if ($cant_en_bodega < $vd['cantidad_reservada']) {
				
				$vd['cantidad_reservada'] = $vd['cantidad_reservada'] - $cant_en_bodega;

				$bodegas = ClassRegistry::init('Bodega')->obtener_bodegas();

				foreach ($bodegas as $bodega_id => $bodega) {
					if ($bodega_id == $bodega_principal) 
						continue;

					# si ya esta completo se termina.
					if ($vd['cantidad_reservada'] == 0) {
						break;
					}
					
					$cant_en_bodega2 = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($vd['venta_detalle_producto_id'], $bodega_id, true);

					# Tenemos las unidades, las movemos a la bodega principal
					if ($cant_en_bodega2 >= $vd['cantidad_reservada']) 
					{
						ClassRegistry::init('Bodega')->moverProductoBodega($vd['venta_detalle_producto_id'], $bodega_id, $bodega_principal, $vd['cantidad_reservada']);
						$vd['cantidad_reservada'] = $vd['cantidad_reservada'] - $cant_en_bodega2;
					}
					else if ($cant_en_bodega2 < $vd['cantidad_reservada'] && $cant_en_bodega2 > 0){
						ClassRegistry::init('Bodega')->moverProductoBodega($vd['venta_detalle_producto_id'], $bodega_id, $bodega_principal, $cant_en_bodega2);
						$vd['cantidad_reservada'] = $vd['cantidad_reservada'] - $cant_en_bodega2;
					}

				}

			}else{
				$vd['cantidad_reservada'] = 0;
			}

			if ($vd['cantidad_reservada'] > 0) {
				$respuesta['code'] = 520;
				$respuesta['message'] = 'El producto #' . $vd['venta_detalle_producto_id'] . ' no tiene stock suficiente en la bodega principal. Solicite un movimiento entre bodegas.';
				echo json_encode($respuesta);
				exit;
			}
		}

		if ($this->Venta->saveField('picking_estado', $subestado)) {

			if ($subestado == 'empaquetando') {
				
				$log = array();

				$this->Venta->saveField('picking_email', $this->Auth->user('email'));
				$this->Venta->saveField('picking_fecha_inicio', date('Y-m-d H:i:s'));

				#$this->cambiar_estado_preparada($venta);

				# Obtenemos estado de en prepracion
				$preparacion      = ClassRegistry::init('VentaEstado')->obtener_estado_preparacion();

				if (!empty($preparacion)) {
					try {
						$this->cambiarEstado($id, $venta['Venta']['id_externo'], $preparacion['VentaEstado']['id'], $venta['Venta']['tienda_id'], $venta['Venta']['marketplace_id'], '', '', $this->Session->read('Auth.Administrador.nombre'));
					} catch (Exception $e) {
						// Nothing
					}	
				}

				if ($venta['MetodoEnvio']['dependencia'] == 'starken' && $venta['MetodoEnvio']['generar_ot'] && !$venta['Venta']['paquete_generado']) {
					# Es una venta para starken
					
					# Creamos cliente starken
					$this->Starken->crearCliente($venta['MetodoEnvio']['rut_api_rest'], $venta['MetodoEnvio']['clave_api_rest'], $venta['MetodoEnvio']['rut_empresa_emisor'], $venta['MetodoEnvio']['rut_usuario_emisor'], $venta['MetodoEnvio']['clave_usuario_emisor']);

					# Creamos la OT
					if($this->Starken->generar_ot($venta)){

						$this->Starken->registrar_estados($venta['Venta']['id']);

						$log[] = array(
							'Log' => array(
								'administrador' => 'Cambiar estado venta: Ingresa Starken',
								'modulo' => 'Ventas',
								'modulo_accion' => 'creado: OT generada'
							)
						);
					}

				}elseif ($venta['MetodoEnvio']['dependencia'] == 'conexxion' && $venta['MetodoEnvio']['generar_ot'] && !$venta['Venta']['paquete_generado']) {
					# Es una venta para conexxion
					
					# Creamos cliente conexxion
					$this->Conexxion->crearCliente($venta['MetodoEnvio']['api_key']);

					# Creamos la OT
					if($this->Conexxion->generar_ot($venta)){
						$log[] = array(
							'Log' => array(
								'administrador' => 'Cambiar estado venta: Ingresa Conexxion',
								'modulo' 		=> 'Ventas',
								'modulo_accion' => 'creado: OT generada'
							)
						);
					}

				}elseif ($venta['MetodoEnvio']['dependencia'] == 'boosmap' && $venta['MetodoEnvio']['generar_ot'] && !$venta['Venta']['paquete_generado']) {
					# Es una venta para boosmap
					
					# Creamos cliente boosmap
					$this->Boosmap->crearCliente($venta['MetodoEnvio']['boosmap_token']);
					
					# Creamos la OT
					if($this->Boosmap->generar_ot($venta)){


						$log[] = array(
							'Log' => array(
								'administrador' => 'Cambiar estado venta: Ingresa Boosmap',
								'modulo' 		=> 'Ventas',
								'modulo_accion' => 'creado: OT generada'
							)
						);
					}
		
				}elseif ($venta['MetodoEnvio']['dependencia'] == 'blueexpress' && $venta['MetodoEnvio']['generar_ot'] && !$venta['Venta']['paquete_generado']) {
					# Es una venta para blueexpress

					# Creamos cliente blueexpress
					$this->BlueExpress->crearCliente($venta['MetodoEnvio']['token_blue_express'], $venta['MetodoEnvio']['cod_usuario_blue_express'], $venta['MetodoEnvio']['cta_corriente_blue_express']);

					# Creamos la OT
					if ($this->BlueExpress->generar_ot($venta)) {

				

						$log[] = array(
							'Log' => array(
								'administrador' => 'Cambiar estado venta: Ingresa BlueExpress',
								'modulo' 		=> 'Ventas',
								'modulo_accion' => 'creado: OT generada'
							)
						);
					}
				}

				ClassRegistry::init('Log')->create();
				ClassRegistry::init('Log')->saveMany($log);

			}

			if ($subestado == 'empaquetado') {				

				# Quitar los productos reservados de la bodega
				$detalles = ClassRegistry::init('VentaDetalle')->find('all', array(
					'conditions' => array(
						'VentaDetalle.venta_id' => $id,
						'VentaDetalle.completo' => 0,
						'VentaDetalle.cantidad_reservada >' => 0
					)
				));

				if (count(Hash::extract($detalles, '{n}.VentaDetalle.confirmado_app')) != count(Hash::extract($detalles, '{n}.VentaDetalle.id')) ) 
				{
					$respuesta['code'] = 503;
					$respuesta['message'] = 'Debes confirmar los productos de la venta';
					echo json_encode($respuesta);
					exit;
				}

				foreach ($detalles as $idd => $d) 
				{

					$cantidad_entregada = 0;
					$cantidad_vendida = $d['VentaDetalle']['cantidad'] - $d['VentaDetalle']['cantidad_anulada'];

					# Obtenemos los movimientos del productos en esta venta
					$cantidad_mv = ClassRegistry::init('Bodega')->obtener_total_mv_por_venta($id, $d['VentaDetalle']['venta_detalle_producto_id']);
					
					# tiene salida
					if ($cantidad_mv < 0)
					{
						$cantidad_entregada = ($cantidad_mv * -1);
					}

					# Pedido completado
					$detalles[$idd]['VentaDetalle']['completo']                   = ($detalles[$idd]['VentaDetalle']['cantidad'] == $d['VentaDetalle']['cantidad_reservada']) ? 1 : 0;
					$detalles[$idd]['VentaDetalle']['fecha_completado']			  = ($detalles[$idd]['VentaDetalle']['completo']) ? date('Y-m-d H:i:s') : '';

					$detalles[$idd]['VentaDetalle']['cantidad_entregada'] = $cantidad_entregada + $d['VentaDetalle']['cantidad_reservada'];

					# Se finaliza la reserva
					if ($detalles[$idd]['VentaDetalle']['cantidad_entregada'] == $cantidad_vendida)
					{
						$detalles[$idd]['VentaDetalle']['cantidad_reservada'] = 0;
						$detalles[$idd]['VentaDetalle']['cantidad_pendiente_entrega'] = 0;
						$detalles[$idd]['VentaDetalle']['cantidad_en_espera'] = 0;
						$detalles[$idd]['VentaDetalle']['fecha_llegada_en_espera'] = '';
					}
					else
					{	
						# Vuelve a calcular la reserva
						$cantidad_reservar = $cantidad_vendida - $detalles[$idd]['VentaDetalle']['cantidad_entregada'];
						$cantidad_reservado = ClassRegistry::init('Bodega')->calcular_reserva_stock($d['VentaDetalle']['venta_detalle_producto_id'],  $cantidad_reservar, $venta['Venta']['bodega_id']);
						$detalles[$idd]['VentaDetalle']['cantidad_reservada'] = $cantidad_reservado;
					}

					$detalles[$idd]['VentaDetalle']['cantidad_pendiente_entrega'] = $d['VentaDetalle']['cantidad'] - ($d['VentaDetalle']['cantidad_anulada'] + $cantidad_entregada + $d['VentaDetalle']['cantidad_reservada']);
					
					# Se calcula la cantidad en espera
					if ($d['VentaDetalle']['cantidad_en_espera'] > 0)
					{
						$detalles[$idd]['VentaDetalle']['cantidad_en_espera'] = ($d['VentaDetalle']['cantidad'] - $d['VentaDetalle']['cantidad_anulada']) - $d['VentaDetalle']['cantidad_reservada'];
					}

					ClassRegistry::init('Bodega')->crearSalidaBodega($d['VentaDetalle']['venta_detalle_producto_id'], null, $d['VentaDetalle']['cantidad_reservada'], null, 'VT', null, $id);
						
				}

				if (!empty($detalles)) 
				{
					ClassRegistry::init('VentaDetalle')->saveMany($detalles);
				}

				$this->Venta->saveField('picking_fecha_termino', date('Y-m-d H:i:s'));
				$this->Venta->saveField('prioritario', 0);
				$this->Venta->saveField('paquete_generado', 0);

				# Sub estados OC de la venta
				if (array_sum(Hash::extract($detalles, '{n}VentaDetalle.cantidad_pendiente_entrega')) > 0 ) {
					$this->Venta->saveField('subestado_oc', 'parcialmente_entregado');
				}else{
					$this->Venta->saveField('subestado_oc', 'entregado');
				}	
			}

			$respuesta['code'] = 200;
			$respuesta['message'] = 'Ok';
		}

		echo json_encode($respuesta);
		exit;
	}


	/**
	 * [admin_marcar_prioritaria description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_marcar_prioritaria($id = null)
	{
		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['prioritario'] = 1;

			if ( $this->Venta->save($this->request->data) ) {

				$embalajes = ClassRegistry::init('EmbalajeWarehouse')->find('all', array(
					'conditions' => array(
						'EmbalajeWarehouse.venta_id' => $id,
						'EmbalajeWarehouse.estado' => 'listo_para_embalar',
						'EmbalajeWarehouse.prioritario' => 0
					),
					'fields' => 'EmbalajeWarehouse.id'
				));

				$embalajesController = new EmbalajeWarehousesController();

				foreach ($embalajes as $e) 
				{
					$embalajesController->embalaje_prioritario($e['EmbalajeWarehouse']['id'], 1);	
				}

				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect($this->referer('/', true));
	}


	/**
	 * [admin_marcar_prioritaria description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_marcar_no_prioritaria($id = null)
	{
		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['prioritario'] = 0;

			if ( $this->Venta->save($this->request->data) ) {

				$embalajes = ClassRegistry::init('EmbalajeWarehouse')->find('all', array(
					'conditions' => array(
						'EmbalajeWarehouse.venta_id' => $id,
						'EmbalajeWarehouse.estado' => 'listo_para_embalar',
						'EmbalajeWarehouse.prioritario' => 1
					),
					'fields' => 'EmbalajeWarehouse.id'
				));

				$embalajesController = new EmbalajeWarehousesController();

				foreach ($embalajes as $e) 
				{
					$embalajesController->embalaje_prioritario($e['EmbalajeWarehouse']['id'], 0);	
				}

				$this->Session->setFlash('Registro editado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect($this->referer('/', true));
	}


	/**
	 * Elimnar venta
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_eliminar($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'id' => $id
			),
			'contain' => array(
				'VentaDetalle'
			)
		));

		if (!empty($venta['VentaDetalle'])) {
			$this->Session->setFlash( sprintf('No es posible eliminar la venta #%d con productos asociados. Sólo se permite cancelarla', $id), null, array(), 'danger');
		}

		# Eliminamos todos los registros
		if ( $this->Venta->deleteAll(array('Venta.id' => $id), true) ) {
			$this->Session->setFlash( sprintf('Venta #%d eliminada correctamente', $id), null, array(), 'success');
		}
		else {
			$this->Session->setFlash( sprintf('Error al eliminar la venta #%d', $id), null, array(), 'danger');
		}
		
		$this->redirect($this->referer('/', true));

	}


	/**
	 * Activar venta
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_activar($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['activo'] = 1;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Registro activado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}


	/**
	 * Desactivar Venta
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_desactivar($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['activo'] = 0;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Registro desactivado correctamente', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}


	/**
	 * Forzar venta como antendida
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_marcar_atendida($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['atendida'] = 1;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Venta marcada como Atendida', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}


	/**
	 * Forzar venta no atendida
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_marcar_no_atendida($id = null) {

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('El registro no es válido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') ) {

			$this->request->data['Venta']['id'] = $id;
			$this->request->data['Venta']['atendida'] = 0;

			if ( $this->Venta->save($this->request->data) ) {
				$this->Session->setFlash('Venta marcada como No Atendida', null, array(), 'success');
			}
			else {
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

		}
		
		$this->redirect(array('action' => 'index'));

	}


	/****************************************************************************************************/
	//obtiene las tiendas (prestashop) que deben ser actualizadas
	private function obtener_tiendas () {

		return $this->Venta->Tienda->find(
			'all',
			array(
				'conditions' => array(
					'Tienda.activo' => 1
				),
				'contain' => array(
					'Marketplace' => array(
						'conditions' => array(
							'Marketplace.activo' => 1
						),
						'fields' => array(
							'Marketplace.id', 'Marketplace.tienda_id', 'Marketplace.nombre', 'Marketplace.fee', 'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.marketplace_tipo_id', 'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token', 'Marketplace.seller_id'
						),
						'MarketplaceTipo' => array(
							'conditions' => array(
								'MarketplaceTipo.activo' => 1
							),
							'fields' => array(
								'MarketplaceTipo.nombre'
							)
						)
					)
				),
				'fields' => array(
					'Tienda.id', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.configuracion'
				)
			)
		);

	}


	/**
	 * Método antiguo para obtener ventas desde prestashop
	 * @param  array  $params [description]
	 * @param  array  $tienda [description]
	 * @return [type]         [description]
	 */
	private function prestashop_obtener_ventas_antiguo($params = array(), $tienda = array())
	{
		# Modelos que requieren agregar configuración
		$this->cambiarDatasource(array('Orden'), $tienda);

		$ordenes	= ClassRegistry::init('Orden')->find('all', $params);

		$result = array();

		if (!empty($ordenes)) {

			foreach ($ordenes as $key => $value) {
				$result['order'][$key]['id']                       = $value['Orden']['id_order'];
				$result['order'][$key]['id_address_delivery']      = $value['Orden']['id_address_delivery'];
				$result['order'][$key]['id_customer']              = $value['Orden']['id_customer'];
				$result['order'][$key]['current_state']            = $value['Orden']['current_state'];
				$result['order'][$key]['date_add']                 = $value['Orden']['date_add'];
				$result['order'][$key]['payment']                  = $value['Orden']['payment'];
				$result['order'][$key]['total_discounts_tax_incl'] = $value['Orden']['total_discounts_tax_incl'];
				$result['order'][$key]['total_paid']               = $value['Orden']['total_paid'];
				$result['order'][$key]['total_products']           = $value['Orden']['total_products'];
				$result['order'][$key]['total_shipping_tax_incl']  = $value['Orden']['total_shipping_tax_incl'];
				$result['order'][$key]['reference']                = $value['Orden']['reference'];
				$result['order'][$key]['id_carrier']               = $value['Orden']['id_carrier'];
			}
		}

		return $result;

	}


	/****************************************************************************************************/
	//obtiene las órdenes de una tienda
	private function prestashop_obtener_ventas ($tienda) {

		//se obtiene la última venta registrada para consultar solo las nuevas a prestashop
		$venta = $this->Venta->find(
			'first',
			array(
				'conditions' => array(
					'Venta.tienda_id' => $tienda['Tienda']['id'],
					'Venta.marketplace_id' => null
				),
				'fields' => array(
					'Venta.id_externo'
				),
				'order' => 'Venta.id_externo DESC'
			)
		);
		
		$DataVentas = $this->Prestashop->prestashop_obtener_ventas($tienda['Tienda']['id'], $venta);
		
		# si no logra obtener ventas via WS intenta obtenerlas directamente por la base de datos
		if (empty($DataVentas) && !empty($venta)) {

			$opt = array(
				'conditions'	=> array('Orden.id_order >' => $venta['Venta']['id_externo']),
				'fields' => array(
					'Orden.id_order',
					'Orden.id_address_delivery',
					'Orden.id_customer',
					'Orden.current_state',
					'Orden.date_add',
					'Orden.payment',
					'Orden.total_discounts_tax_incl',
					'Orden.total_paid',
					'Orden.total_products',
					'Orden.total_shipping_tax_incl',
					'Orden.reference',
					'Orden.id_carrier'
				)
			);

			$DataVentas = $this->prestashop_obtener_ventas_antiguo($opt, $tienda);
		}
	
		return $DataVentas;

	}


	/****************************************************************************************************/
	//guarda un producto si no existe
	private function prestashop_guardar_producto ($DetalleVenta, $excluir = array()) {

		$producto = $this->Venta->VentaDetalle->VentaDetalleProducto->find(
			'first',
			array(
				'conditions' => array(
					'VentaDetalleProducto.id_externo' => $DetalleVenta['product_id']
				),
				'fields' => array(
					'VentaDetalleProducto.id',
					'VentaDetalleProducto.id_externo',
					'VentaDetalleProducto.cantidad_virtual'
				)
			)
		);

		if (empty($producto)) {

			$item = $this->Prestashop->prestashop_obtener_producto($DetalleVenta['product_id']);

			$producto = array();
			$producto['VentaDetalleProducto']['id']         			= $DetalleVenta['product_id'];
			$producto['VentaDetalleProducto']['id_externo'] 			= $DetalleVenta['product_id'];
			$producto['VentaDetalleProducto']['nombre']     			= $DetalleVenta['product_name'];
			$producto['VentaDetalleProducto']['cantidad_virtual']     	= 1;
			$producto['VentaDetalleProducto']['ancho']					= round($item['width'], 2);
			$producto['VentaDetalleProducto']['alto']					= round($item['height'], 2);
			$producto['VentaDetalleProducto']['largo']					= round($item['depth'], 2);
			$producto['VentaDetalleProducto']['peso']					= round($item['weight'], 2);
			
			$this->Venta->VentaDetalle->VentaDetalleProducto->create();
			$this->Venta->VentaDetalle->VentaDetalleProducto->save($producto);
		}

		return $producto['VentaDetalleProducto']['id_externo'];

	}


	/****************************************************************************************************/
	//guarda un producto si no existe
	private function linio_guardar_producto ($DetalleVenta, $excluir = array()) {

		$producto = $this->Venta->VentaDetalle->VentaDetalleProducto->find(
			'first',
			array(
				'conditions' => array(
					'VentaDetalleProducto.id_externo' => $DetalleVenta['Sku']
				),
				'fields' => array(
					'VentaDetalleProducto.id',
					'VentaDetalleProducto.cantidad_virtual'
				)
			)
		);

		$nuevaCantidad = 0;
		
		if (empty($producto)) {

			$data = array();
			$data['VentaDetalleProducto']['id']         = $DetalleVenta['Sku'];
			$data['VentaDetalleProducto']['id_externo'] = $DetalleVenta['Sku'];
			$data['VentaDetalleProducto']['nombre']     = $DetalleVenta['Name'];
			$data['VentaDetalleProducto']['cantidad_virtual']     	= 11;

			$this->Venta->VentaDetalle->VentaDetalleProducto->create();
			$this->Venta->VentaDetalle->VentaDetalleProducto->save($data);

			
		}

		return $DetalleVenta['Sku'];

	}


	/****************************************************************************************************/
	//obtiene el estado de venta
	private function obtener_estado_id ($estado, $origen) {

		$VentaEstado = $this->Venta->VentaEstado->find(
			'first',
			array(
				'conditions' => array(
					'VentaEstado.nombre' => $estado
				),
				'fields' => array(
					'VentaEstado.id'
				)
			)
		);

		if (!empty($VentaEstado)) {
			return $VentaEstado['VentaEstado']['id'];
		}

		//si el estado de venta no existe, se crea
		$data = array();
		$data['VentaEstado']['nombre'] = $estado;
		$data['VentaEstado']['origen'] = $origen;

		$this->Venta->VentaEstado->create();
		$this->Venta->VentaEstado->save($data);

		return $this->Venta->VentaEstado->id;

	}


	/****************************************************************************************************/
	//obtiene el id de un medio de pago
	private function obtener_medio_pago_id ($medio_pago) {

		$MedioPago = $this->Venta->MedioPago->find(
			'first',
			array(
				'conditions' => array(
					'MedioPago.nombre' => $medio_pago
				),
				'fields' => array(
					'MedioPago.id'
				)
			)
		);

		if (!empty($MedioPago)) {
			return $MedioPago['MedioPago']['id'];
		}

		//si el medio de pago no existe, se crea
		$data = array();
		$data['MedioPago']['nombre'] = $medio_pago;

		$this->Venta->MedioPago->create();
		$this->Venta->MedioPago->save($data);

		return $this->Venta->MedioPago->id;

	}


	/****************************************************************************************************/
	//obtiene el id de un medio de pago
	private function obtener_metodo_envio_id ($metodo_envio) {

		$MetodoEnvio = $this->Venta->MetodoEnvio->find(
			'first',
			array(
				'conditions' => array(
					'MetodoEnvio.nombre' => $metodo_envio
				),
				'fields' => array(
					'MetodoEnvio.id'
				)
			)
		);

		if (!empty($MetodoEnvio)) {
			return $MetodoEnvio['MetodoEnvio']['id'];
		}

		//si el medio de pago no existe, se crea
		$bodega 					      = ClassRegistry::init('Bodega')->obtener_bodega_principal();
		$data = array();
		$data['MetodoEnvio']['nombre']    = $metodo_envio;
		$data['MetodoEnvio']['bodega_id'] = $bodega['Bodega']['id'];
		$this->Venta->MetodoEnvio->create();
		$this->Venta->MetodoEnvio->save($data);

		return $this->Venta->MetodoEnvio->id;

	}


	/****************************************************************************************************/
	//obtiene el estado de venta
	private function obtener_cliente_id ($DataVenta) {

		$rut = str_replace(".", "", $DataVenta['NationalRegistrationNumber']);

		$VentaCliente = $this->Venta->VentaCliente->find(
			'first',
			array(
				'conditions' => array(
					'VentaCliente.rut' => $rut
				),
				'fields' => array(
					'VentaCliente.id'
				)
			)
		);

		if (!empty($VentaCliente)) {
			return $VentaCliente['VentaCliente']['id'];
		}

		//si el cliente no existe, se crea
		$data = array();
		$data['VentaCliente']['nombre'] = $DataVenta['CustomerFirstName'];
		$data['VentaCliente']['apellido'] = $DataVenta['CustomerLastName'];
		$data['VentaCliente']['rut'] = $rut;

		$this->Venta->VentaCliente->create();
		$this->Venta->VentaCliente->save($data);

		return $this->Venta->VentaCliente->id;

	}


	/****************************************************************************************************/
	//sincroniza el stock de productos de una tienda y los marketplaces asociados
	// 1. se buscan los productos en prestashop para ver su stock
	// 2. se restan las cantidades vendidas al stock tomado de prestashop para actualizarlo.
	// 3. el nuevo stock se envía a la tienda y marketplaces asociados
	private function sincronizar_stock_productos ($tienda, $ArrayProductosSincronizacion, $ArrayCantidadesVendidos, $ConexionPrestashop) {

		//para actualizar los productos a cada marketplace Linio si existen
		//esto debido a que Linio va reuniendo los productos y luego actualiza en una sola llamada
		$ArrayLinios = array();

		//----------------------------------------------------------------------------------------------------
		//ciclo para recorrer los productos que estaban en las ventas
		foreach ($ArrayProductosSincronizacion as $producto_key => $producto_id) {

			//se obtiene el stock de prestashop
			$opt = array();
			$opt['resource'] = 'stock_availables';
			$opt['display'] = '[quantity]';
			$opt['filter[id_product]'] = '[' .$producto_id. ']';

			$xml = $ConexionPrestashop->get($opt);

			$PrestashopResources = $xml->children()->children();

			//para cambiar el objeto xml a un array
			$json = json_encode($PrestashopResources);
			$stock = json_decode($json, true);

			$NuevoStock = $stock['stock_available']['quantity'];

			//se resta la cantidad vendida en marketplaces para actualizar el stock
			//si el producto no existe en el array de vendidos (si no se vendió en los marketplaces) no es necesario actualizar el stock de prestashop
			if (!empty($ArrayCantidadesVendidos[$producto_key])) {

				$NuevoStock = $NuevoStock - $ArrayCantidadesVendidos[$producto_key];

				//se actualiza el stock en prestashop
				$resources->stock_available->quantity = $NuevoStock;

				$opt = array('resource' => 'stock_availables');
				$opt['putXml'] = $xml->asXML();
				$opt['id'] = $producto_id;
				$xml = $ConexionPrestashop->edit($opt);

			} //fin resta de cantidad vendida en marketplaces

			//----------------------------------------------------------------------------------------------------
			//si la tienda tiene marketplaces asociados para actualizar
			if (!empty($tienda['Marketplace'])) {

				//recorrido de marketplaces
				foreach ($tienda['Marketplace'] as $marketplace_key => $marketplace) {

					//----------------------------------------------------------------------------------------------------
					//si el marketplace es Linio
					if ($marketplace['marketplace_tipo_id'] == 1) {

						//si no existe la posición del marketplace para ir reuniendo los productos, se agrega
						if (!isset($ArrayLinios[$marketplace_key])) {
							$ArrayLinios[$marketplace_key] = Endpoints::product()->productUpdate();
						}

						$ArrayLinios[$marketplace_key]->updateProduct($producto_id)->setQuantity($NuevoStock);

					} //fin si el marketplace es Linio

					//----------------------------------------------------------------------------------------------------
					//si el marketplace es Mercado Libre

				} //fin recorrido de los marketplaces

			} //fin si la tienda tiene marketplaces asociados

		} //fin ciclo de productos que estaban en las ventas

		//si existen productos para actualizar en cuentas de Linio
		if (!empty($ArrayLinios)) {

			foreach ($ArrayLinios as $linio_key => $linio) {

				$ConexionLinio = Client::create(new Configuration($tienda['Marketplace'][$linio_key]['api_host'], $tienda['Marketplace'][$linio_key]['api_user'], $tienda['Marketplace'][$linio_key]['api_key']));

				$linio->build()->call($ConexionLinio);

			}

		} //fin si existen productos para actualizar en tiendas de linio

	}


	/**
	 * Método encargado de actualizar, obtener el token de MELI. 
	 * Sí no existe Token, crea la url para la conexión
	 * @return array
	 */
	public function admin_verificar_conexion_meli($redirect = '')
	{
		$tiendas     = $this->obtener_tiendas();
		if (empty($redirect)) {
			$redirectURI = Router::url( array('controller' => $this->request->controller, 'action' => 'index'), true );	
		}else{
			$redirectURI = Router::url( $redirect, true );

			# Para la consola se carga el componente on the fly!
			$this->MeliMarketplace = $this->Components->load('MeliMarketplace');
		}
		
		$siteId      = 'MLC';
		$results     = array();
		$response    = array();
		$code 		 = isset($_GET['code']) ? $_GET['code'] : '' ;

		foreach ($tiendas as $it => $tienda) {
			foreach ($tienda['Marketplace'] as $im => $marketplace) {
				if ($marketplace['marketplace_tipo_id'] == 2) {

					if (!empty($marketplace['api_user']) && !empty($marketplace['api_key'])) {

						$this->MeliMarketplace->crearCliente($marketplace['api_user'], $marketplace['api_key'], $marketplace['access_token'], $marketplace['refresh_token']);
						$results[$marketplace['id']] = $this->MeliMarketplace->mercadolibre_conectar($code, $marketplace, $redirectURI, $siteId);	
						
					}else{

						$results[$marketplace['id']]['errors'] = sprintf('%s no tiene configurado su API_USER y API_KEY', $marketplace['nombre']);
					}	
				}

			}
		}
		
		foreach ($results as $ir => $result) {
			if (!empty($result['success']) && !$this->shell) {
				SessionComponent::setFlash($this->crearAlertaUl(array($result['success']), 'Resultados'), null, array(), 'success');
			}

			if (!empty($result['errors']) && !$this->shell) {
				SessionComponent::setFlash($this->crearAlertaUl(array($result['errors']), 'Errores'), null, array(), 'danger');
			}

			if (!empty($result['access'])) {
				$response[] = $result['access'];
			}
		}

		return $response;
	}


	/**
	 * Método encargado de conectar MELI con Sistemas
	 * @param  array  $marketplace Arreglo con la información del Marketplace 
	 * @param  string $redirectURI Url de redirección para el login de MEli
	 * @param  string $siteId      Identificador de API (MLC = Mercadolibre Chile)
	 * @return array  			   Información del procedimiento	
	 */
	public function admin_mercadolibre_conectar($marketplace = array(), $redirectURI = '', $siteId = 'MLC') 
	{	
		$m = array();
		$response = array(
			'access' => array(),
			'success' => array(),
			'errors' => array()
		);
		
		if(isset($_GET['code']) || isset($marketplace['access_token'])) {
			// If code exist and session is empty
			if(isset($_GET['code']) && !isset($marketplace['access_token'])) {
				// //If the code was in get parameter we authorize
				try{
					$user = $this->Mercadolibre->authorize($_GET["code"], $redirectURI);
					
					if ($user['httpCode'] == 200) {
						// Now we save credentials with the authenticated user
						$m['Marketplace']['access_token']  = $user['body']->access_token;
						$m['Marketplace']['expires_token'] = time() + $user['body']->expires_in;
						$m['Marketplace']['refresh_token'] = $user['body']->refresh_token;
						$m['Marketplace']['seller_id']     = $user['body']->user_id;
					}
				}catch(Exception $e){
					$response['errors'] = $e->getMessage();
				}
			} else {
				// We can check if the access token in invalid checking the time
				if($marketplace['expires_token'] < time()) {

					try {
						// Make the refresh proccess
						$refresh = $this->Mercadolibre->refreshAccessToken();
						
						if ($refresh['httpCode'] == 200) {
							// Now we save credentials with the new parameters
							$m['Marketplace']['access_token']  = $refresh['body']->access_token;
							$m['Marketplace']['expires_token'] = time() + $refresh['body']->expires_in;
							$m['Marketplace']['refresh_token'] = $refresh['body']->refresh_token;
							$m['Marketplace']['seller_id']     = $refresh['body']->user_id;
						}
					} catch (Exception $e) {
					  	$response['errors'] = $e->getMessage();
					}
				}
			}
			
			// save in db marketplace tokens
			if (!empty($m) && empty($response[$marketplace['id']]['errors'])) {

				ClassRegistry::init('Marketplace')->id = $marketplace['id'];
				if (!ClassRegistry::init('Marketplace')->save($m)) {
					$response['errors'] = sprintf('%s no se logró conectar a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}else{
					$response['success'] = sprintf('%s se ha conectado con éxito a %s', $marketplace['nombre'], $marketplace['MarketplaceTipo']['nombre']);
				}
			}

			return $response;

		} else {

			$response['access']['marketplace'] = $marketplace['nombre'];
			$response['access']['url'] = $this->Mercadolibre->getAuthUrl($redirectURI, Meli::$AUTH_URL[$siteId]);
			
			return $response;
		}
	}


	/****************************************************************************************************/
	//actualización de estatus de ventas marcadas como No Atendidas para marketplaces (mercado libre)
	public function actualizar_ventas_anteriores_mercadolibre ($ventas, $marketplace) {
		# Para la consola se carga el componente on the fly!
		if ($this->shell) {
			$this->MeliMarketplace = $this->Components->load('MeliMarketplace');
		}
		# Cliente y conexión Meli
		$this->MeliMarketplace->crearCliente( $marketplace['Marketplace']['api_user'], $marketplace['Marketplace']['api_key'], $marketplace['Marketplace']['access_token'], $marketplace['Marketplace']['refresh_token'] );
		$this->MeliMarketplace->mercadolibre_conectar('', $marketplace['Marketplace']);

		$dataToSave = array();

		foreach ($ventas as $venta) {

			$response = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($marketplace['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);
			
			if (empty($response)) {
				continue;
			}

			$EstatusMeli = $response['status'];
		
			$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
			$venta['Venta']['venta_estado_id'] = $this->obtener_estado_id($EstatusMeli, $marketplace['Marketplace']['marketplace_tipo_id']);

			if (CakeSession::check('Auth.Administrador.id')) {
				$venta['Venta']['venta_estado_responsable'] = $this->Auth->user('nombre');	
			}

			$dataToSave[] = $venta;

		}

		$this->Venta->saveMany($dataToSave);

	}


	/****************************************************************************************************/
	//actualización de estatus de ventas marcadas como No Atendidas para marketplaces (linio)
	public function actualizar_ventas_anteriores_linio ($ventas, $marketplace) {
		# Para la consola se carga el componente on the fly!
		if ($this->shell) {
			$this->Linio = $this->Components->load('Linio');
		}
		# Cliente Linio
		$this->Linio->crearCliente($marketplace['Marketplace']['api_host'], $marketplace['Marketplace']['api_user'], $marketplace['Marketplace']['api_key']);

		$dataToSave = array();

		foreach ($ventas as $venta) {

			$EstatusLinio = $this->Linio->linio_obtener_venta($venta['Venta']['id_externo']);

			sleep(1);

			$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
			$venta['Venta']['venta_estado_id'] = $this->obtener_estado_id($EstatusLinio, $marketplace['Marketplace']['marketplace_tipo_id']);

			if (CakeSession::check('Auth.Administrador.id')) {
				$venta['Venta']['venta_estado_responsable'] = $this->Auth->user('nombre');	
			}

			$dataToSave[] = $venta;

		}

		$this->Venta->saveMany($dataToSave);
		
	}


	/****************************************************************************************************/
	//actualización de estatus de ventas marcadas como No Atendidas para tiendas (prestashop)
	public function actualizar_ventas_anteriores_prestashop ($ventas, $tienda) {
		# Para la consola se carga el componente on the fly!
		if ($this->shell) {
			$this->Prestashop = $this->Components->load('Prestashop');
		}
		# Cliente Prestashop
		$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

		$dataToSave = array();

		$bodega 							     = ClassRegistry::init('Bodega')->obtener_bodega_principal();
		foreach ($ventas as $venta) {

			$dataWS = $this->Prestashop->prestashop_obtener_venta($venta['Venta']['id_externo']);
			$dataWS = array(); // Se debe quitar la caché del WS
			if (empty($dataWS)) {

				# Modelos que requieren agregar configuración
				$this->cambiarDatasource(array('Orden'), $tienda);
					
				# Obtenemos la venta directamente de la BD
				$data = ClassRegistry::init('Orden')->find('first', array(
					'conditions' => array(
						'Orden.id_order' => $venta['Venta']['id_externo']
					),
					'fields' => array(
						'Orden.current_state',
						'Orden.total_paid',
						'Orden.total_discounts_tax_incl',
						'Orden.total_shipping_tax_incl',
						'Orden.id_carrier'
					)
				));

				if (empty($data)) {
					$venta['Venta']['estado_anterior'] = 1;
					$venta['Venta']['venta_estado_id'] = 1; //Sin Estado
				}
				else {
					$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
					$venta['Venta']['venta_estado_id'] = $this->Prestashop->prestashop_obtener_venta_estado($data['Orden']['current_state']);
					$venta['Venta']['costo_envio']     = round($data['Orden']['total_shipping_tax_incl'], 2);
					$venta['Venta']['descuento']       = round($data['Orden']['total_discounts_tax_incl'], 2);
					$venta['Venta']['metodo_envio_id'] = $this->Prestashop->prestashop_obtener_transportista($data['Orden']['id_carrier']);
					$venta['Venta']['total']           = round($data['Orden']['total_paid'], 2);
					$venta['Venta']['bodega_id'] 	   = ClassRegistry::init('MetodoEnvio')->bodega_id($venta['Venta']['metodo_envio_id']) ?? $bodega['Bodega']['id']; 
				}

			}else{

				if (empty($dataWS)) {
					$venta['Venta']['estado_anterior'] = 1;
					$venta['Venta']['venta_estado_id'] = 1; //Sin Estado
				}
				else {
					$venta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
					$venta['Venta']['venta_estado_id'] = $this->Prestashop->prestashop_obtener_venta_estado($dataWS['current_state']);
					$venta['Venta']['costo_envio']     = round($dataWS['total_shipping_tax_incl'], 2);
					$venta['Venta']['descuento']       = round($dataWS['total_discounts_tax_incl'], 2);
					$venta['Venta']['metodo_envio_id'] = $this->Prestashop->prestashop_obtener_transportista($dataWS['id_carrier']);
					$venta['Venta']['total']           = round($dataWS['total_paid'], 2);
					$venta['Venta']['bodega_id'] 	   = ClassRegistry::init('MetodoEnvio')->bodega_id($venta['Venta']['metodo_envio_id']) ?? $bodega['Bodega']['id']; 
				}

			}

			if (CakeSession::check('Auth.Administrador.id')) {
				$venta['Venta']['venta_estado_responsable'] = $this->Auth->user('nombre');	
			}

			$dataToSave[] = $venta;

		}
		
		$this->Venta->saveMany($dataToSave);

	}


	/****************************************************************************************************/
	//recorre las tiendas y marketplaces, busca las ventas para actualizar y las envía a los métodos
	//actualizar_ventas_anteriores_prestashop, actualizar_ventas_anteriores_linio y actualizar_ventas_anteriores_mercadolibre, según corresponda
	public function actualizar_ventas_anteriores () {

		$this->layout = 'ajax';

		set_time_limit(0);

		//----------------------------------------------------------------------------------------------------
		//lista de tiendas (prestashop)
		$tiendas = $this->Venta->Tienda->find(
			'all',
			array(
				'conditions' => array(
					'Tienda.activo' => 1,
					'Tienda.apiurl_prestashop <>' => '',
					'Tienda.apikey_prestashop <>' => ''
				),
				'fields' => array(
					'Tienda.id', 'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.configuracion'
				)
			)
		);

		//recorrido de las tiendas (prestashop)
		foreach ($tiendas as $tienda) {

			$ventas = $this->Venta->find(
				'all',
				array(
					'conditions' => array(
						'Venta.atendida' => 0,
						'Venta.activo' => 1,
						'Venta.tienda_id' => $tienda['Tienda']['id'],
						'Venta.marketplace_id IS NULL'
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.venta_estado_id', 'Venta.estado_anterior'
					)
				)
			);
			
			if (!empty($ventas)) {
				$this->actualizar_ventas_anteriores_prestashop($ventas, $tienda);
			}
			
		}

		//----------------------------------------------------------------------------------------------------
		//lista de marketplaces (linio y mercado libre)
		$marketplaces = $this->Venta->Marketplace->find(
			'all',
			array(
				'contain' => array(
					'MarketplaceTipo' => array(
						'fields' => array(
							'MarketplaceTipo.nombre'
						)
					)
				),
				'conditions' => array(
					'Marketplace.activo' => 1
				),
				'fields' => array(
					'Marketplace.id', 'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token', 'Marketplace.seller_id', 'Marketplace.marketplace_tipo_id', 'Marketplace.nombre'
				)
			)
		);

		//recorrido de los marketplaces (linio y mercado libre)
		foreach ($marketplaces as $marketplace) {

			$ventas = $this->Venta->find(
				'all',
				array(
					'conditions' => array(
						'Venta.atendida' => 0,
						'Venta.activo' => 1,
						'Venta.marketplace_id' => $marketplace['Marketplace']['id']
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.venta_estado_id', 'Venta.estado_anterior'
					)
				)
			);
			
			if (!empty($ventas)) {

				switch ($marketplace['Marketplace']['marketplace_tipo_id']) {

					case 1:
						$this->actualizar_ventas_anteriores_linio($ventas, $marketplace);
					break;

					case 2:
						$this->actualizar_ventas_anteriores_mercadolibre($ventas, $marketplace);
					break;

				}

			}
			
		}

	}


	/**
	 * Devuelve el stock de las ventas con eststados que tanga habilitada la opción de
	 * revertir stock
	 * @param  array  $excluir Id y tipo de canal el cual se excluirá la actualización
	 * @return void
	 */
	public function ventas_estados_revertidas( $excluir = array() )
	{	
		$ventas = $this->Venta->find('all', array(
			'conditions' => array(
				'Venta.activo' => 1,
				'Venta.atendida' => 0
			),
			'contain' => array(
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.revertir_stock'
					)
				),
				'VentaDetalle' => array(
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.cantidad_virtual',
							'VentaDetalleProducto.id_externo',
							'VentaDetalleProducto.id'
						)
					),
					'fields' => array(
						'VentaDetalle.cantidad'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.venta_estado_id',
				'Venta.estado_anterior'
			)
		));

		# agrupa los nuevos stocks de los productos
		$productoStocks = array();
		
		if (!empty($ventas)) {

			# Descontar stock virtual y refrescar canales
			$productosController = new VentaDetalleProductosController();

			foreach ($ventas as $iv => $venta) {

				# solo se procesa si el estado de la venta ha cambiado
				if ($venta['VentaEstado']['revertir_stock'] && $venta['Venta']['venta_estado_id'] != $venta['Venta']['estado_anterior'] ) {
					/*foreach ($venta['VentaDetalle'] as $ip => $producto) {

						$productoStocks[$producto['VentaDetalleProducto']['id']]['id_externo'] = $producto['VentaDetalleProducto']['id_externo'];
						//$productoStocks[$producto['VentaDetalleProducto']['id']]['descontar']  = $producto['cantidad'];

						if (!isset($productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad'])) {
							$productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad']   = $producto['VentaDetalleProducto']['cantidad_virtual'] + $producto['cantidad'];
						}else{
							$productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad']   = $productoStocks[$producto['VentaDetalleProducto']['id']]['nueva_cantidad'] + $producto['cantidad'];
						}
						
					}*/

					# Devolvemos el stock reservado si corresponde
					$this->Venta->cancelar_venta($venta['Venta']['id']);
					$this->WarehouseNodriza->procesar_embalajes($venta['Venta']['id']);
					$this->actualizar_canales_stock($venta['Venta']['id']);


				}
			}
			
			# Se refrescan solo una vez cada producto
			foreach ($productoStocks as $id_producto => $data) {
				#$productosController->descontar_stock_virtual($id_producto, $data['id_externo'], $data['nueva_cantidad'], $excluir);
			}

		}

		return;

	}


	/**
	 * Según el estado que tenga la venta se marca como finalizada o no
	 * @return void
	 */
	public function ventas_estados_atendidos( )
	{
		$ventas = $this->Venta->find('all', array(
			'conditions' => array(
				'Venta.activo' => 1
			),
			'contain' => array(
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.marcar_atendida'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.venta_estado_id',
				'Venta.estado_anterior',
				'Venta.fecha_venta',
				'Venta.atendida'
			)
		));


		$limite_mes  = mktime(0, 0, 0, date("m")-3, date("d"),   date("Y"));
		
		if (!empty($ventas)) {

			foreach ($ventas as $iv => $venta) {

				if ($venta['VentaEstado']['marcar_atendida'] && !$venta['Venta']['atendida']) {
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 1
						)
					)); 
				}elseif ( strtotime($venta['Venta']['fecha_venta']) < $limite_mes && !$venta['Venta']['atendida']) { // si es una venta muy antigua se cierra
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 1
						)
					)); 
				}elseif ($venta['VentaEstado']['marcar_atendida'] && $venta['Venta']['atendida'] && $venta['Venta']['venta_estado_id'] != $venta['Venta']['estado_anterior']) {
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 1
						)
					)); 
				}elseif ($venta['Venta']['atendida']) {
					continue; // Ya fue atendida
				}else{
					$this->Venta->save(array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'atendida' => 0
						)
					));
				}
			}

		}

		return;
	}


	/**
	 * [ventas_estados_pagadas description]
	 * @return [type] [description]
	 */
	public function ventas_estados_pagadas()
	{
		$ventas = $this->Venta->find('all', array(
			'conditions' => array(
				'Venta.activo'        => 1,
				'Venta.atendida'      => 0
			),
			'fields' => array(
				'Venta.id',
				'Venta.atendida'
			)
		));
		
		if (!empty($ventas)) {

			foreach ($ventas as $iv => $venta) {

				$this->Venta->pagar_venta($venta['Venta']['id']);
				$this->actualizar_canales_stock($venta['Venta']['id']);
			}
		}

		return;
	}



	/****************************************************************************************************/
	//actualización de ventas

	// ! Metodo obsoleto y en desuzo
	public function admin_actualizar_ventas () {
		
		$this->layout = 'ajax';
		set_time_limit(0);
		# Mercadolibre conectar
		#$this->admin_verificar_conexion_meli();
		//actualización de estatus de ventas marcadas como No Atendidas
		#$this->actualizar_ventas_anteriores();
		//ids de productos para actualizar su stock al finalizar la carga de ventas
		$ArrayProductosSincronizacion = array();
		$ArrayCantidadesVendidos = array();
		//se buscan las tiendas que se deben procesar
		$tiendas = $this->obtener_tiendas();
		//si hay tiendas para procesar
		if (!empty($tiendas)) {
			//ciclo para procesar cada tienda (prestashop)
			foreach ($tiendas as $tienda) {
				# Para la consola se carga el componente on the fly!
				if ($this->shell) {
					$this->Prestashop = $this->Components->load('Prestashop');
				}
				# Cliente Prestashop
				$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );
				//si se cargaron ventas
				if ($TiendaVentas = $this->prestashop_obtener_ventas($tienda)) {
					if (!isset($TiendaVentas['order'][0])) {
						$TiendaVentas = array(
							'order' => array(
								'0' => $TiendaVentas['order']
							)
						);
					}
					
					//ciclo de ventas
					foreach ($TiendaVentas['order'] as $DataVenta) {
						
						#Vemos si existe en la BD
						$existe = $this->Venta->find('first', array(
							'conditions' => array(
								'Venta.id_externo'     => $DataVenta['id'],
								'Venta.tienda_id'      => $tienda['Tienda']['id'],
								'Venta.marketplace_id' => null
							)
						));
						if (!empty($existe)) {
							continue;
						}
						//datos de la venta a registrar
						$NuevaVenta                         = array();
						$NuevaVenta['Venta']['tienda_id']   = $tienda['Tienda']['id'];
						$NuevaVenta['Venta']['id_externo']  = $DataVenta['id'];
						$NuevaVenta['Venta']['referencia']  = $DataVenta['reference'];
						$NuevaVenta['Venta']['fecha_venta'] = $DataVenta['date_add'];
						$NuevaVenta['Venta']['descuento']   = round($DataVenta['total_discounts_tax_incl'], 2);
						$NuevaVenta['Venta']['costo_envio'] = round($DataVenta['total_shipping_tax_incl'], 2);
						$NuevaVenta['Venta']['total']       = round($DataVenta['total_paid'], 2);
						//se obtienen las transacciones de una venta
						//si la venta tiene transacciones asociadas
						if ($VentaTransacciones = $this->Prestashop->prestashop_obtener_venta_transacciones($DataVenta['reference'])) {
							if (!isset($VentaTransacciones['order_payment'][0])) {
								$VentaTransacciones = array(
									'order_payment' => array(
										'0' => $VentaTransacciones['order_payment']
									)
								);
							}
							
							foreach ($VentaTransacciones['order_payment'] as $transaccion) {
								$NuevaTransaccion = array();
								if (!empty($transaccion['transaction_id'])) {
									$NuevaTransaccion['nombre'] = $transaccion['transaction_id'];
								}
								$NuevaTransaccion['monto'] = (!empty($transaccion['amount'])) ? $transaccion['amount'] : 0;
								$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;
								
							}
						}
						# Direccion de entrega
						$direccionEntrega = $this->Prestashop->prestashop_obtener_venta_direccion($DataVenta['id_address_delivery']);
						// Dirección de entrega
						if (!isset($DataVenta['address'])) {
							
							$direccion_entrega = '';
							$comuna_entrega    = '';
							$nombre_receptor   = '';
							$fono_receptor     = '';
							if (!empty($direccionEntrega['address']['address1'])) {
								if (is_array($direccionEntrega['address']['address1'])) {
									$direccionEntrega['address']['address1'] = implode(', ', $direccionEntrega['address']['address1']);
								}
								$direccion_entrega .= $direccionEntrega['address']['address1'];
							}
							if (!empty($direccionEntrega['address']['address2'])) {
								if (is_array($direccionEntrega['address']['address2'])) {
									$direccionEntrega['address']['address2'] = implode(', ', $direccionEntrega['address']['address2']);
								}
								$direccion_entrega .= ', ' . $direccionEntrega['address']['address2'];
							}
							if (!empty($direccionEntrega['address']['other'])) {
								if (is_array($direccionEntrega['address']['other'])) {
									$direccionEntrega['address']['other'] = implode(', ', $direccionEntrega['address']['other']);
								}
								$direccion_entrega .= ', ' . $direccionEntrega['address']['other'];
							}
							if (!empty($direccionEntrega['address']['city'])) {
								$comuna_entrega .= $direccionEntrega['address']['city'];
							}
							if (!empty($direccionEntrega['address']['firstname'])) {
								$nombre_receptor .= $direccionEntrega['address']['firstname'] . ' ' . $direccionEntrega['address']['lastname'];
							}
							if (!empty($direccionEntrega['address']['phone'])) {
								if (is_array($direccionEntrega['address']['phone'])) {
									$direccionEntrega['address']['phone'] = implode(' - ', $direccionEntrega['address']['phone']);
								}
								$fono_receptor .= trim($direccionEntrega['address']['phone']);
							}
							if (!empty($direccionEntrega['address']['phone_mobile'])) {
								$fono_receptor .=  ' - ' . trim($direccionEntrega['address']['phone_mobile']);
							}
							if (isset($direccionEntrega['address']['id_state'])) {
								$comuna_entrega = $this->Prestashop->prestashop_obtener_comuna_por_id($direccionEntrega['address']['id_state'])['state']['name'];
							}

							$comuna_id = ClassRegistry::init('Comuna')->obtener_id_comuna_por_nombre($comuna_entrega);

							$NuevaVenta['Venta']['direccion_entrega'] =  $direccion_entrega;
							$NuevaVenta['Venta']['comuna_entrega']    =  ClassRegistry::init('Comuna')->field('nombre', array('id' => $comuna_id));
							$NuevaVenta['Venta']['comuna_id']         =  $comuna_id;
							$NuevaVenta['Venta']['nombre_receptor']   =  $nombre_receptor;
							$NuevaVenta['Venta']['fono_receptor']     =  $fono_receptor;
						}
						//se obtienen el detalle de la venta
						$VentaDetalles = $this->Prestashop->prestashop_obtener_venta_detalles($DataVenta['id']);
						if (isset($VentaDetalles['order_detail']) && !isset($VentaDetalles['order_detail'][0])) {
							$VentaDetalles = array(
								'order_detail' => array(
									'0' => $VentaDetalles['order_detail']
								)
							);
						}
						//se obtiene el estado de la venta
						if (empty($DataVenta['current_state']) || $DataVenta['current_state'] == 0) {
							$NuevaVenta['Venta']['venta_estado_id'] = 1; //Sin Estado
							$NuevaVenta['Venta']['estado_anterior'] = 1;
						}
						else {
							$NuevaVenta['Venta']['venta_estado_id'] = $this->Prestashop->prestashop_obtener_venta_estado($DataVenta['current_state']);
							$NuevaVenta['Venta']['estado_anterior'] = $NuevaVenta['Venta']['venta_estado_id'];
						}
						
						$NuevaVenta['Venta']['metodo_envio_id']  = $this->Prestashop->prestashop_obtener_transportista($DataVenta['id_carrier']);

						$bodega 							     = ClassRegistry::init('Bodega')->obtener_bodega_principal();
						$NuevaVenta['Venta']['bodega_id'] 		 = ClassRegistry::init('MetodoEnvio')->bodega_id($NuevaVenta['Venta']['metodo_envio_id']) ?? $bodega['Bodega']['id']; 
						//se obtiene el medio de pago
						$NuevaVenta['Venta']['medio_pago_id']    = $this->Prestashop->prestashop_obtener_medio_pago($DataVenta['payment']);
						
						//se obtiene el cliente
						$NuevaVenta['Venta']['venta_cliente_id'] = $this->Prestashop->prestashop_obtener_cliente($DataVenta['id_customer']);
						// Existen ventas sin productos xD
						if (isset($VentaDetalles['order_detail'])) {
							//ciclo para recorrer el detalle de la venta
							foreach ($VentaDetalles['order_detail'] as $DetalleVenta) {
								if (!empty($DetalleVenta['product_id'])) {
									
									$NuevoDetalle = array();
									$NuevoDetalle['venta_detalle_producto_id']  = $DetalleVenta['product_id'];
									$NuevoDetalle['precio']                     = round($DetalleVenta['unit_price_tax_excl'], 2);
									$NuevoDetalle['precio_bruto']               = round($DetalleVenta['unit_price_tax_incl'], 2);
									$NuevoDetalle['cantidad']                   = $DetalleVenta['product_quantity'];
									$NuevoDetalle['cantidad_pendiente_entrega'] = $DetalleVenta['product_quantity'];
									$NuevoDetalle['cantidad_reservada'] 		= 0;
									$NuevoDetalle['total_neto']              = $NuevoDetalle['precio'] * $NuevoDetalle['cantidad'];			
									$NuevoDetalle['total_bruto']				= monto_bruto($NuevoDetalle['total_neto']);
									if (ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['venta_estado_id'])) {
										$NuevoDetalle['cantidad_reservada']     = ClassRegistry::init('Bodega')->calcular_reserva_stock($DetalleVenta['product_id'], $DetalleVenta['product_quantity'],$NuevaVenta['Venta']['bodega_id']);	
									}
									$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;
									# Evitamos que se vuelva actualizar el stock en prestashop
									$excluirPrestashop = array('Prestashop' => array($tienda['Tienda']['id']));
									//se guarda el producto si no existe
									$this->prestashop_guardar_producto($DetalleVenta, $excluirPrestashop);
									//se toma el id de producto para usarlo luego en la sincronización de stock
									if (!in_array($DetalleVenta['product_id'], $ArrayProductosSincronizacion)) {
										$ArrayProductosSincronizacion[] = $DetalleVenta['product_id'];
									}
								}
								
							} //fin ciclo detalle de venta
						}
						# si la venta tiene sus productos reservados ésta disponible para ser procesada
						if (array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad_reservada')) == array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad'))) {
							$NuevaVenta['Venta']['picking_estado'] = 'empaquetar';
						}
						
						//se guarda la venta
						$this->Venta->create();
						$this->Venta->saveAll($NuevaVenta);
					} //fin ciclo de ventas
				} //fin si se cargaron ventas
				//----------------------------------------------------------------------------------------------------
				//registro de las ventas de marketplaces asociados a la tienda
				if (!empty($tienda['Marketplace'])) {
					//recorrido de marketplaces
					foreach ($tienda['Marketplace'] as $marketplace) {
						//----------------------------------------------------------------------------------------------------
						//si el marketplace es Linio
						if ($marketplace['marketplace_tipo_id'] == 1) {
							# Para la consola se carga el componente on the fly!
							if ($this->shell) {
								$this->Linio = $this->Components->load('Linio');
							}
							# Cliente Linio	
							$this->Linio->crearCliente($marketplace['api_host'], $marketplace['api_user'], $marketplace['api_key']);
							$finalizarLinio = false;
							do {
								
								$LinioVentas = $this->Linio->linio_obtener_ventas($marketplace['id'], $finalizarLinio);
									
								//si se cargaron ventas
								if ($LinioVentas) {
									if (!isset($LinioVentas[0])) {
										$LinioVentas = array(
											'0' => $LinioVentas
										);
									}
									//ciclo de ventas
									foreach ($LinioVentas as $DataVenta) {
										#Vemos si existe en la BD
										$existe = $this->Venta->find('first', array(
											'conditions' => array(
												'Venta.id_externo'     => $DataVenta['OrderId'],
												'Venta.tienda_id'      => $tienda['Tienda']['id'],
												'Venta.marketplace_id' => $marketplace['id']
											)
										));
										if (!empty($existe)) {
											continue;
										}
										//datos de la venta a registrar
										$NuevaVenta = array();
										$NuevaVenta['Venta']['tienda_id']      = $tienda['Tienda']['id'];
										$NuevaVenta['Venta']['marketplace_id'] = $marketplace['id'];
										$NuevaVenta['Venta']['id_externo']     = $DataVenta['OrderId'];
										$NuevaVenta['Venta']['referencia']     = $DataVenta['OrderNumber'];
										$NuevaVenta['Venta']['fecha_venta']    = $DataVenta['CreatedAt'];
										$NuevaVenta['Venta']['total']          = $DataVenta['Price'];
										//$NuevaVenta['Venta']['total'] = 0;
										
										// Guardar transacción
										$NuevaTransaccion = array();
										if (!empty($DataVenta['OrderNumber'])) {
											$NuevaTransaccion['nombre'] = $DataVenta['OrderNumber'];
										}
										$NuevaTransaccion['monto'] = (!empty($DataVenta['Price'])) ? $DataVenta['Price'] : 0;
										$NuevaTransaccion['fee']   = ($NuevaTransaccion['monto'] * ($marketplace['fee'] / 100));
										$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;
												
										//se obtienen el detalle de la venta
										$VentaDetalles = $this->Linio->linio_obtener_venta_detalles($DataVenta['OrderId']);
										
										if (!isset($VentaDetalles[0])) {
											$VentaDetalles = array(
												'0' => $VentaDetalles
											);
										}

										$comuna_id = ClassRegistry::init('Comuna')->obtener_id_comuna_por_nombre($DataVenta['AddressShipping']['City']);
										
										// Direccion despacho
										$NuevaVenta['Venta']['direccion_entrega'] =  $DataVenta['AddressShipping']['Address1'] . ', ' . $DataVenta['AddressShipping']['Address2'];
										$NuevaVenta['Venta']['comuna_entrega']    =  ClassRegistry::init('Comuna')->field('nombre', array('id' => $comuna_id));
										$NuevaVenta['Venta']['comuna_id']         =  $comuna_id;
										$NuevaVenta['Venta']['nombre_receptor']   =  $DataVenta['AddressShipping']['FirstName'] . ' ' . $DataVenta['AddressShipping']['LastName'];
										$NuevaVenta['Venta']['fono_receptor']     =  trim($DataVenta['AddressShipping']['Phone']) . '-' .  trim($DataVenta['AddressShipping']['Phone2']) ;
										
										$totalDespacho = (float) 0;
										$metodo_envio  = '';
										$NuevaVenta['Venta']['costo_envio']      = (float) $totalDespacho;
										
										//se obtiene el estado de la venta
										$NuevaVenta['Venta']['venta_estado_id']  = $this->obtener_estado_id($DataVenta['Statuses']['Status'], $marketplace['marketplace_tipo_id']);
										$NuevaVenta['Venta']['estado_anterior']  = $NuevaVenta['Venta']['venta_estado_id'];
										
										//se obtiene el medio de pago
										$NuevaVenta['Venta']['medio_pago_id']    = $this->obtener_medio_pago_id($DataVenta['PaymentMethod']);
										//se obtiene el metodo de envio
										$NuevaVenta['Venta']['metodo_envio_id']  = $this->obtener_metodo_envio_id($metodo_envio);
										
										$bodega 							     = ClassRegistry::init('Bodega')->obtener_bodega_principal();
										
										$NuevaVenta['Venta']['bodega_id'] 		 = ClassRegistry::init('MetodoEnvio')->bodega_id($NuevaVenta['Venta']['metodo_envio_id']) ?? $bodega['Bodega']['id']; 
										
										//se obtiene el cliente
										$NuevaVenta['Venta']['venta_cliente_id'] = $this->obtener_cliente_id($DataVenta);
										$NuevaVenta['Venta']['total'] 			 = (float) 0; // El total se calcula en en base a la sumatoria de items
										# Se marca como prioritaria
										$NuevaVenta['Venta']['prioritario'] 	= 1;
										//ciclo para recorrer el detalle de la venta
										foreach ($VentaDetalles as $DetalleVenta) {
											$DetalleVenta['Sku'] = intval($DetalleVenta['Sku']);
											# Evitamos que se vuelva actualizar el stock en linio
											$excluirLinio = array('Linio' => array($marketplace['id']));
											//se guarda el producto si no existe
											$idNuevoProducto = $this->linio_guardar_producto($DetalleVenta, $excluirLinio);
											$NuevoDetalle = array();
											$NuevoDetalle['venta_detalle_producto_id'] = $idNuevoProducto;
											if ( round($DetalleVenta['VoucherAmount']) > 0 ) {
												$NuevoDetalle['precio']                    = monto_neto(round($DetalleVenta['PaidPrice'] + $DetalleVenta['VoucherAmount'], 2));
												$NuevoDetalle['precio_bruto']              = round($DetalleVenta['PaidPrice'] + $DetalleVenta['VoucherAmount'], 2);	
											}else{
												$NuevoDetalle['precio']                    = monto_neto(round($DetalleVenta['PaidPrice'], 2));
												$NuevoDetalle['precio_bruto']              = $DetalleVenta['PaidPrice'];
											}
											
											$NuevoDetalle['cantidad_pendiente_entrega'] = 1;
											$NuevoDetalle['cantidad_reservada']         = 0;
											$NuevoDetalle['cantidad']         			= 1;
											$NuevoDetalle['total_neto']              = $NuevoDetalle['precio'] * $NuevoDetalle['cantidad'];			
											$NuevoDetalle['total_bruto']				= monto_bruto($NuevoDetalle['total_neto']);
											if (ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['venta_estado_id'])) {
												$NuevoDetalle['cantidad_reservada']    = ClassRegistry::init('Bodega')->calcular_reserva_stock($idNuevoProducto, 1, $NuevaVenta['Venta']['bodega_id']);	
											}
											# OBtenemos el último metodo de envio
											$metodo_envio = $DetalleVenta['ShipmentProvider'];
											$totalDespacho = $totalDespacho + round($DetalleVenta['ShippingAmount'], 2);
											// Se agrega el valor de la compra sumanod el precio de los productos
											$NuevaVenta['Venta']['total'] = $NuevaVenta['Venta']['total'] + $NuevoDetalle['precio_bruto'];
											# costo de despacho
											$NuevaVenta['Venta']['costo_envio'] = $NuevaVenta['Venta']['costo_envio'] + round($DetalleVenta['ShippingAmount'], 2);
											$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;
											
											//se toma el id de producto para usarlo luego en la sincronización de stock
											if (!in_array($DetalleVenta['Sku'], $ArrayProductosSincronizacion)) {
												$ArrayProductosSincronizacion[] = $DetalleVenta['Sku'];
											}
											//se aumenta la cantidad de vendidos
											$pos = array_search($DetalleVenta['Sku'], $ArrayProductosSincronizacion);
											if (!isset($ArrayCantidadesVendidos[$pos])) {
												$ArrayCantidadesVendidos[$pos] = 1;
											}
											else {
												$ArrayCantidadesVendidos[$pos]++;
											}
										} //fin ciclo detalle de venta
										# si la venta tiene sus productos reservados ésta disponible para ser procesada
										if (array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad_reservada')) == array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad'))) {
											$NuevaVenta['Venta']['picking_estado'] = 'empaquetar';
										}
										/*
										obtener mensajes de la venta
										$mensajes = $this->Linio->linio_obtener_venta_mensajes ($DataVenta['OrderId'], $ConexionLinio);
										*/
										//se guarda la venta
										$this->Venta->create();
										$this->Venta->saveAll($NuevaVenta);
									} //fin ciclo de ventas
								} //fin si se cargaron ventas
								sleep(1); // Delay de 3 segunos para evitar excepción Too many request
							} while ( !$finalizarLinio );
						} //fin si el marketplace es Linio
						# Es mercadolibre
						if ($marketplace['marketplace_tipo_id'] == 2) {
							# Para la consola se carga el componente on the fly!
							if ($this->shell) {
								$this->MeliMarketplace = $this->Components->load('MeliMarketplace');
							}
							# cliente y conexion Meli
							$this->MeliMarketplace->crearCliente( $marketplace['api_user'], $marketplace['api_key'], $marketplace['access_token'], $marketplace['refresh_token'] );
							$this->MeliMarketplace->mercadolibre_conectar('', $marketplace);
							$ventasMercadolibre = $this->MeliMarketplace->mercadolibre_obtener_ventas($marketplace);
							
							if (count($ventasMercadolibre['ventasMercadolibre']) > 0) {
								//ciclo de ventas
								foreach ($ventasMercadolibre['ventasMercadolibre'] as $DataVenta) {
									
									#Vemos si existe en la BD
									$existe = $this->Venta->find('first', array(
										'conditions' => array(
											'Venta.id_externo'     => $DataVenta['id'],
											'Venta.tienda_id'      => $tienda['Tienda']['id'],
											'Venta.marketplace_id' => $marketplace['id']
										)
									));
									if (!empty($existe)) {
										continue;
									}
										
									//datos de la venta a registrar
									$NuevaVenta                            = array();
									$NuevaVenta['Venta']['tienda_id']      = $tienda['Tienda']['id'];
									$NuevaVenta['Venta']['marketplace_id'] = $marketplace['id'];
									$NuevaVenta['Venta']['id_externo']     = $DataVenta['id'];
									$NuevaVenta['Venta']['referencia']     = $DataVenta['id'];
									
									$NuevaVenta['Venta']['fecha_venta']    = CakeTime::format($DataVenta['date_created'], '%Y-%m-%d %H:%M:%S');
									$NuevaVenta['Venta']['total']          = round($DataVenta['total_amount'], 2);
									# Se marca como prioritaria
									$NuevaVenta['Venta']['prioritario'] 	= 1;
									# costo envio
									if (isset($DataVenta['shipping']['cost'])) {
										$NuevaVenta['Venta']['costo_envio'] = $DataVenta['shipping']['cost'];
										$NuevaVenta['Venta']['total']       = round($DataVenta['total_amount'] + $DataVenta['shipping']['cost'], 2);
									}
									
									//se obtienen el detalle de la venta
									$VentaDetalles = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($marketplace['access_token'], $DataVenta['id'], true);
									
									// Detalles de envio
									$direccion_entrega = '';
									$comuna_entrega  = '';
									$nombre_receptor = '';
									$fono_receptor   = '';

									if (isset($VentaDetalles['shipping']['id'])) {

										$envio = $this->MeliMarketplace->mercadolibre_obtener_envio($VentaDetalles['shipping']['id']);

										if (isset($envio['receiver_address']['address_line'])
											&& isset($envio['receiver_address']['city']['name'])) {
											$direccion_entrega = $envio['receiver_address']['address_line'] . ', ' . $envio['receiver_address']['city']['name'];
										}
										if (isset($envio['receiver_address']['city']['name'])) {
											$comuna_entrega = $envio['receiver_address']['city']['name'];
										}
										if (isset($envio['receiver_address']['receiver_name'])) {
											$nombre_receptor = $envio['receiver_address']['receiver_name'];
										}
										if (isset($envio['receiver_address']['receiver_phone'])) {
											$fono_receptor = $envio['receiver_address']['receiver_phone'];
										}
									}

									$comuna_id = ClassRegistry::init('Comuna')->obtener_id_comuna_por_nombre($comuna_entrega);

									// Direccion despacho
									$NuevaVenta['Venta']['direccion_entrega'] =  $direccion_entrega;
									$NuevaVenta['Venta']['comuna_entrega']    =  ClassRegistry::init('Comuna')->field('nombre', array('id' => $comuna_id));
									$NuevaVenta['Venta']['comuna_id']         =  $comuna_id;
									$NuevaVenta['Venta']['nombre_receptor']   =  $nombre_receptor;
									$NuevaVenta['Venta']['fono_receptor']     =  $fono_receptor;
									
									if (!isset($VentaDetalles['order_items'][0])) {
										$VentaDetalles['order_items'] = array(
											'0' => $VentaDetalles['order_items']
										);
									}
									# Mercado libre puede tener más de 1 pago
									foreach ($DataVenta['payments'] as $venta) {
										//se obtiene el estado de la venta
										$NuevaVenta['Venta']['venta_estado_id'] = $this->obtener_estado_id($venta['status'], $marketplace['marketplace_tipo_id']);
										$NuevaVenta['Venta']['estado_anterior'] = $NuevaVenta['Venta']['venta_estado_id'];
										
										//se obtiene el medio de pago
										$NuevaVenta['Venta']['medio_pago_id']   = $this->obtener_medio_pago_id($venta['payment_type']);
										$NuevaTransaccion = array();
										if (!empty($venta['id'])) {
											$NuevaTransaccion['nombre'] = $venta['id'];
										}
										$NuevaTransaccion['monto'] = (!empty($venta['total_paid_amount'])) ? $venta['total_paid_amount'] : 0;
										$NuevaTransaccion['fee'] = (!empty($venta['marketplace_fee'])) ? $venta['marketplace_fee'] : 0;
										$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;
									}
									if (isset($DataVenta['shipping']['shipping_option']['name'])) {
										//se obtiene el metodo de envio
										$NuevaVenta['Venta']['metodo_envio_id']  = $this->obtener_metodo_envio_id($DataVenta['shipping']['shipping_option']['name']);	
									}else{
										$NuevaVenta['Venta']['metodo_envio_id']  = $this->obtener_metodo_envio_id('Marketplace Externo');	
									}

									$bodega 							     = ClassRegistry::init('Bodega')->obtener_bodega_principal();

									$NuevaVenta['Venta']['bodega_id'] 		 = ClassRegistry::init('MetodoEnvio')->bodega_id($NuevaVenta['Venta']['metodo_envio_id']) ?? $bodega['Bodega']['id']; 

									//se obtiene el cliente
									$NuevaVenta['Venta']['venta_cliente_id'] = $this->MeliMarketplace->mercadolibre_obtener_cliente($DataVenta);
									
									# Obtener mensajes de la venta
									$mensajes = $this->MeliMarketplace->mercadolibre_obtener_mensajes($marketplace['access_token'], $DataVenta['id']);
									foreach ($mensajes as $im => $mensaje) {
	
										$NuevaVenta['VentaMensaje'][$im]['nombre']   = (empty($mensaje['subject'])) ? 'Sin asunto' : $mensaje['subject'] ;
										$NuevaVenta['VentaMensaje'][$im]['fecha']    = CakeTime::format($mensaje['date'], '%Y-%m-%d %H:%M:%S');
										$NuevaVenta['VentaMensaje'][$im]['emisor']   = $mensaje['from']['user_id'];
										$NuevaVenta['VentaMensaje'][$im]['mensaje']  = $this->removeEmoji($mensaje['text']['plain']);
									}
									//ciclo para recorrer el detalle de la venta
									foreach ($VentaDetalles['order_items'] as $DetalleVenta) {
										if (!empty($DetalleVenta['item']['seller_custom_field']) ) {
											
											$DetalleVenta['Sku']  = intval($DetalleVenta['item']['seller_custom_field']);
											$DetalleVenta['Name'] = $DetalleVenta['item']['title'];
											if ($DetalleVenta['Sku'] == 0) {
												$DetalleVenta['Sku'] = $DetalleVenta['item']['seller_sku'];
											}
											if ($DetalleVenta['Sku'] == 0) {
												continue;
											}
											# Evitamos que se vuelva actualizar el stock en meli
											$excluirMeli = array('Mercadolibre' => array($marketplace['id']));
											//se guarda el producto si no existe
											$idNuevoProducto = $this->linio_guardar_producto($DetalleVenta, $excluirMeli);
											$NuevoDetalle                               = array();
											$NuevoDetalle['venta_detalle_producto_id']  = $idNuevoProducto;
											$NuevoDetalle['precio']                     = monto_neto(round($DetalleVenta['unit_price'], 2));
											$NuevoDetalle['precio_bruto']               = round($DetalleVenta['unit_price'], 2);
											$NuevoDetalle['cantidad']                   = $DetalleVenta['quantity'];
											$NuevoDetalle['cantidad_pendiente_entrega'] = $DetalleVenta['quantity'];
											$NuevoDetalle['cantidad_reservada'] 		= 0;
											$NuevoDetalle['total_neto']              = $NuevoDetalle['precio'] * $NuevoDetalle['cantidad'];			
											$NuevoDetalle['total_bruto']				= monto_bruto($NuevoDetalle['total_neto']);
											if (ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['metodo_envio_id'])) {
												$NuevoDetalle['cantidad_reservada'] 	= ClassRegistry::init('Bodega')->calcular_reserva_stock($idNuevoProducto, $DetalleVenta['quantity'], $NuevaVenta['Venta']['bodega_id']);	
											}											
											$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;
											//se toma el id de producto para usarlo luego en la sincronización de stock
											if (!in_array($DetalleVenta['Sku'], $ArrayProductosSincronizacion)) {
												$ArrayProductosSincronizacion[] = $DetalleVenta['Sku'];
											}
											//se aumenta la cantidad de vendidos
											$pos = array_search($DetalleVenta['Sku'], $ArrayProductosSincronizacion);
											if (!isset($ArrayCantidadesVendidos[$pos])) {
												$ArrayCantidadesVendidos[$pos] = 1;
											}
											else {
												$ArrayCantidadesVendidos[$pos]++;
											}
											
										} // fin no empty
									
									} //fin ciclo detalle de venta
									# si la venta tiene sus productos reservados ésta disponible para ser procesada
									if (array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad_reservada')) == array_sum(Hash::extract($NuevaVenta['VentaDetalle'], '{n}.cantidad'))) {
										$NuevaVenta['Venta']['picking_estado'] = 'empaquetar';
									}
									//se guarda la venta
									$this->Venta->create();
									$this->Venta->saveAll($NuevaVenta);
								} //fin ciclo de ventas
							} // Fin agregar mercadopago
						} // Fin mercadolibre
					} //fin ciclo de marketplaces
				} //fin si hay marketplaces para procesar
				//----------------------------------------------------------------------------------------------------
				//sincronizar stock de productos
				/*if (!empty($ArrayProductosSincronizacion)) {
					$this->sincronizar_stock_productos($tienda, $ArrayProductosSincronizacion, $ArrayCantidadesVendidos, $ConexionPrestashop);
				}*/ // fin si hay productos para sincronizar
			} //fin ciclo de tiendas
		} //fin si hay ventas para procesar
		# revertir el stock virtual del producto segun su estado
		$this->ventas_estados_revertidas();
		# Marca las ventas como atendidas segun su estado.
		$this->ventas_estados_atendidos();
		
		if (!$this->shell) {
			$this->Session->setFlash('Actualización realizada correctamente', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
	}


	/**
	 * [admin_exportar description]
	 * @return [type] [description]
	 */
	public function admin_exportar () {

		set_time_limit(0);

		ini_set('memory_limit', '-1');

		$condiciones = array();
		$joins = array();
		
		$FiltroVenta                = '';
		$FiltroCliente              = '';
		$FiltroTienda               = '';
		$FiltroMarketplace          = '';
		$FiltroMedioPago            = '';
		$FiltroVentaEstadoCategoria = '';
		$FiltroPrioritario          = '';
		$FiltroPicking              = '';
		$FiltroFechaDesde           = '';
		$FiltroFechaHasta           = '';
		$FiltroVentaId              = '';
		$FiltroAtributo              = '';

		// Filtrado de ordenes por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ventas', 'exportar');
		}


		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'filtroventa':
						$FiltroVenta = trim($valor);

						if ($FiltroVenta != "") {

							$condiciones["OR"] = array(
								"Venta.id_externo LIKE '%" .$FiltroVenta. "%'",
								"Venta.referencia LIKE '%" .$FiltroVenta. "%'"
							);
							
						}
						break;

					case 'filtroventa_id':
						$FiltroVentaId = trim($valor);

						if ($FiltroVentaId != "") {
							$condiciones['Venta.id'] = explode(',',$FiltroVentaId);
						}
						break;
					case 'filtrocliente':
						$FiltroCliente = trim($valor);

						if ($FiltroCliente != "") {

							$joins[] = array(
								'table' => 'rp_venta_clientes',
								'alias' => 'clientes',
								'type' => 'INNER',
								'conditions' => array(
									'clientes.id = Venta.venta_cliente_id',
									'OR' => array(
										"clientes.nombre LIKE '%" .$FiltroCliente. "%'",
										"clientes.apellido LIKE '%" .$FiltroCliente. "%'",
										"clientes.rut LIKE '%" .$FiltroCliente. "%'",
										"clientes.email LIKE '%" .$FiltroCliente. "%'",
										"clientes.telefono LIKE '%" .$FiltroCliente. "%'"
									)
								)
							);
							
						}
						break;
					case 'tienda_id':
						$FiltroTienda = $valor;

						if ($FiltroTienda != "") {
							$condiciones['Venta.tienda_id'] = $FiltroTienda;
						} 
						break;
					case 'marketplace_id':
						$FiltroMarketplace = $valor;

						if ($FiltroMarketplace != "") {
							$condiciones['Venta.marketplace_id'] = ($FiltroMarketplace == 0) ? null : $FiltroMarketplace;
						} 
						break;
					case 'medio_pago_id':
						$FiltroMedioPago = $valor;

						if ($FiltroMedioPago != "") {
							$condiciones['Venta.medio_pago_id'] = $FiltroMedioPago;
						} 
						break;
					case 'venta_estado_categoria_id':
						$FiltroVentaEstadoCategoria = $valor;

						if ($FiltroVentaEstadoCategoria != "") {

							$joins[] = array(
								'table' => 'rp_venta_estados',
								'alias' => 'ventas_estados',
								'type' => 'INNER',
								'conditions' => array(
									'ventas_estados.id = Venta.venta_estado_id',
									"ventas_estados.venta_estado_categoria_id = " .$FiltroVentaEstadoCategoria
								)
							);

						}
						break;
					case 'atributo':
						$FiltroAtributo = $valor;

						if ($FiltroAtributo != "") {

							$joins[] = array(
								'table' => 'rp_venta_detalles',
								'alias' => 'venta_detalles',
								'type' => 'INNER',
								'conditions' => array(
									'venta_detalles.venta_id= Venta.id'
								)
							);

							$joins[] = array(
								'table' => 'rp_venta_detalles_atributos',
								'alias' => 'vd_atributos',
								'type' => 'INNER',
								'conditions' => array(
									'vd_atributos.venta_detalle_id= venta_detalles.id',
									'vd_atributos.atributo_id' =>  $FiltroAtributo
								)
							);

						}
						break;
					case 'prioritario':
						$FiltroPrioritario = $valor;

						if ($FiltroPrioritario != "") {
							$condiciones['Venta.prioritario'] = $FiltroPrioritario;
						} 
						break;
					case 'picking_estado':
						$FiltroPicking = $valor;

						if ($FiltroPicking != "") {
							$condiciones['Venta.picking_estado'] = $FiltroPicking;
						} 
						break;
					case 'FechaDesde':
						$FiltroFechaDesde = trim($valor);

						if ($FiltroFechaDesde != "") {

							$ArrayFecha = explode("-", $FiltroFechaDesde);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 00:00:00"));

							$condiciones["Venta.fecha_venta >="] = $Fecha;

						}
						break;
					case 'FechaHasta':
						$FiltroFechaHasta = trim($valor);

						if ($FiltroFechaHasta != "") {

							$ArrayFecha = explode("-", $FiltroFechaHasta);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 23:59:59"));

							$condiciones["Venta.fecha_venta <="] = $Fecha;

						} 
						break;
				}
			}
		}

		//----------------------------------------------------------------------------------------------------
		$datos = $this->Venta->find('all',
			array(
				'contain' => array(
					'VentaEstado' => array(
						'VentaEstadoCategoria' => array(
							'fields' => array(
								'VentaEstadoCategoria.id', 'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo'
							)
						),
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.venta_estado_categoria_id'
						)
					),
					'Tienda' => array(
						'fields' => array(
							'Tienda.id', 'Tienda.nombre'
						)
					),
					'Marketplace' => array(
						'fields' => array(
							'Marketplace.id', 'Marketplace.nombre'
						)
					),
					'MedioPago' => array(
						'fields' => array(
							'MedioPago.id', 'MedioPago.nombre'
						)
					),
					'VentaCliente' => array(
						'fields' => array(
							'VentaCliente.nombre', 'VentaCliente.apellido', 'VentaCliente.rut', 'VentaCliente.email', 'VentaCliente.telefono',
						)
					),
					'Dte' => array(
						'fields' => array(
							'Dte.estado', 'Dte.folio', 'Dte.tipo_documento'
						)
					)
				),
				'conditions' => $condiciones,
				'joins' => $joins,
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.referencia', 'Venta.fecha_venta', 'Venta.total', 'Venta.atendida', 'Venta.activo',
					'Venta.venta_estado_id', 'Venta.tienda_id', 'Venta.marketplace_id', 'Venta.medio_pago_id', 'Venta.venta_cliente_id','Venta.picking_estado'
				),
				'order' => 'Venta.fecha_venta DESC'
			)
		);
		
		$this->set(compact('datos'));

	}


	/**
	 * Ver los detalles de una venta
	 * @param  [type] $id Identificador de la venta
	 */
	public function admin_view ($id = null) 
	{

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			if (CakeSession::check('Auth.Administrador.id')) {
				$this->request->data['Venta']['venta_estado_responsable'] = $this->Auth->user('nombre');	
			}

			try {
				$cambiar_estado = $this->cambiarEstado($id, $this->request->data['Venta']['id_externo'], $this->request->data['Venta']['venta_estado_id'], $this->request->data['Venta']['tienda_id'], $this->request->data['Venta']['marketplace_id'], '', '', $this->Session->read('Auth.Administrador.nombre'));
			} catch (Exception $e) {
				$this->Session->setFlash($e->getMessage(), null, array(), 'danger');
				$this->redirect(array('action' => 'view', $id));
			}

			if ($cambiar_estado) {
				$this->Session->setFlash('Cambio estado realizado con éxito y notificado al cliente.', null, array(), 'success');
				$this->redirect(array('action' => 'view', $id));
			}

		}

		$venta = $this->request->data = $this->preparar_venta($id);
		
		# Estados disponibles para esta venta
		$ventaEstados = ClassRegistry::init('VentaEstado')->find('list', array(
			'conditions' => array(
				'activo' => 1,
				'origen' => (empty($this->request->data['Venta']['marketplace_id'])) ? 0 : $this->request->data['Venta']['marketplace_id'] 
			),
			'order' => array('nombre' => 'ASC')
		));

		$transportes = ClassRegistry::init('Transporte')->find('list', array('conditions' => array('activo' => 1)));

		$comunas = ClassRegistry::init('Comuna')->find('list', array('fields' => array('Comuna.nombre', 'Comuna.nombre'), 'order' => array('Comuna.nombre' => 'ASC')));

		$starken_info = array(); 
		# Starken
		if ($venta['MetodoEnvio']['dependencia'] == 'starken') {
			
			# Creamos cliente starken
			$this->Starken->crearCliente($venta['Tienda']['starken_rut'], $venta['Tienda']['starken_clave'], $venta['MetodoEnvio']['rut_empresa_emisor'], $venta['MetodoEnvio']['rut_usuario_emisor'], $venta['MetodoEnvio']['clave_usuario_emisor']);
			
			$seguimientos = array();

			if (!empty($venta['Transporte'])) {
				# creamos una lista con los n° de seguimiento
				foreach ($venta['Transporte'] as $iv => $t) {
					
					if (empty($t['TransportesVenta']['cod_seguimiento']))
						continue;

					$seguimientos['listaSeguimientos'][]['numeroOrdenFlete'] = $t['TransportesVenta']['cod_seguimiento'];

				}

				if (!empty($seguimientos)) {
					# Consultamos por los envios
					$res = $this->Starken->seguimiento($seguimientos);
				}
			}
			
		}
		
		# Estados de envios
		foreach ($venta['Transporte'] as $it => $t)
		{	
			$historico = ClassRegistry::init('EnvioHistorico')->find(
				'all',
				array(
					'conditions' => array(
						'EnvioHistorico.transporte_venta_id' => $t['TransportesVenta']['id']
					),
					'contain' => array(
						'EstadoEnvio' => array(
							'EstadoEnvioCategoria' => array(
								'VentaEstado' => array(
									'VentaEstadoCategoria'
								)
							)
						)
					),
					'order' => array('EnvioHistorico.created' => 'DESC')
				)
			);
			
			$venta['Transporte'][$it]['TransportesVenta']['EnvioHistorico'] = $historico; 
			
		}

		$metodos_de_envios=[];
		$metodoEnvios_sin_procesar = ClassRegistry::init('MetodoEnvio')->find('all', array(
			'contain'=>[
				'Bodega'=>['fields'=>'Bodega.nombre'

			]],
			'fields'=>['MetodoEnvio.id','MetodoEnvio.nombre','MetodoEnvio.dependencia'],
			'conditions' => array('MetodoEnvio.activo' => 1)));

		foreach ($metodoEnvios_sin_procesar as $value) {
			$metodos_de_envios[$value['MetodoEnvio']['id']] ="{$value['Bodega']['nombre']} - {$value['MetodoEnvio']['nombre']} ".(isset($value['MetodoEnvio']['dependencia'])?"| Dependencia {$value['MetodoEnvio']['dependencia']}":'');
		}

		# Canales de venta
		$canal_ventas = $this->Venta->CanalVenta->find('list', array(
			'conditions' => array(
				'activo' => 1
			)
		));
		
		BreadcrumbComponent::add('Listado de ventas', '/ventas');
		BreadcrumbComponent::add('Detalles de Venta');

		$embaljes_ids = Hash::extract($venta, "HistorialEmbalaje.{n}");
		$response = $this->WarehouseNodriza->ObtenerEvidencia(["embalajes_id"=>$embaljes_ids]);		
		
		foreach ($venta['VentaDetalle'] as $key => $value ) {
			
		
			foreach ($value['HistorialEmbalaje'] as $key2 => $value2) {
				$existe = Hash::extract($response['response']['body'], "{n}[embalaje_id={$value2['embalaje_id']}]");
				if (!empty($existe)) {
					$venta['VentaDetalle'][$key]['HistorialEmbalaje'][$key2] = array_replace_recursive($venta['VentaDetalle'][$key]['HistorialEmbalaje'][$key2], $existe[0]);
				}
			}
		}

		foreach ($venta['EmbalajeWarehouse'] as $key => $value ) {
		
			foreach ($value['HistorialEmbalaje'] as $key2 => $value2) {
				$existe = Hash::extract($response['response']['body'], "{n}[embalaje_id={$value2['embalaje_id']}]");

				if (!empty($existe)) {
					$venta['EmbalajeWarehouse'][$key]['HistorialEmbalaje'][$key2] = array_replace_recursive($venta['EmbalajeWarehouse'][$key]['HistorialEmbalaje'][$key2], $existe[0]);
				}
			}
		}

		# Obtener embalajes vía api y las notas relacionadas
		$embalajes_req = $this->WarehouseNodriza->ObtenerEmbalajesVentaV2($id);	
		$notas_req = $this->WarehouseNodriza->ObtenerNotasDespacho(['venta_id' => $id]);		
		
		$embalajes = [];
		$notas_despacho = [];

		if ($embalajes_req['code'] == 200)
		{
			$embalajes = $embalajes_req['response'];
		}

		if ($notas_req['code'] == 200)
		{
			$notas_despacho = $notas_req['response'];
		}
		$bodegas = ClassRegistry::init("Bodega")->obtener_bodegas();
		# Aislamos solo las notas de despacho y las ordenamos
		$notas_embalajes = Hash::extract($embalajes, 'body.{n}.notas_despacho.{n}');
		
		$this->sort_array_by_key($notas_embalajes, 'fecha_creacion');
		
		$this->set(compact('venta', 'ventaEstados', 'transportes', 'enviame_info', 'comunas','metodos_de_envios', 'canal_ventas', 'embalajes', 'notas_embalajes', 'notas_despacho','bodegas'));

	}

	
	/**
	 * admin_crear_embalajes
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function admin_crear_embalajes($id)
	{
		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id' => $id
			),
			'contain' => array(
				'EmbalajeWarehouse'
			),
			'fields' => array(
				'Venta.id',
				'Venta.metodo_envio_id',
				'Venta.marketplace_id',
				'Venta.comuna_id',
				'Venta.fecha_venta',
				'Venta.venta_estado_id',
				'Venta.administrador_id',
				'Venta.picking_estado',
				'Venta.bodega_id'
			)
		));

		$bodega = ClassRegistry::init('Bodega')->obtener_bodega_principal();
		
		switch ($venta['Venta']['picking_estado']) {
			case 'no_definido':

				
				# si no hay embalaje lo creamos en estado inicial
				if (empty($venta['EmbalajeWarehouse']))
				{
					ClassRegistry::init('EmbalajeWarehouse')->save(array(
						'EmbalajeWarehouse' => array(
							'venta_id' => $venta['Venta']['id'],
							'estado' => 'inicial',
							'bodega_id' => $venta['Venta']['bodega_id'] ?? $bodega['Bodega']['id'],
							'metodo_envio_id' => $venta['Venta']['metodo_envio_id'],
							'marketplace_id' => $venta['Venta']['marketplace_id'],
							'comuna_id' => $venta['Venta']['comuna_id'],
							'venta_estado_id' => $venta['Venta']['venta_estado_id'],
							'fecha_venta' => $venta['Venta']['fecha_venta'],
							'fecha_creacion' => date('Y-m-d H:i:s'),
							'ultima_modifacion' => date('Y-m-d H:i:s')
						)
					));
				}
				
				break;
			
			case 'empaquetar':
				
				# si existe embalaje inicial lo actualizamos
				if (!empty($venta['EmbalajeWarehouse']))
				{
					foreach($venta['EmbalajeWarehouse'] as $embalaje)
					{
						if ($embalaje['estado'] == 'inicial')
						{
							ClassRegistry::init('EmbalajeWarehouse')->save(array(
								'EmbalajeWarehouse' => array(
									'id' => $embalaje['id'],
									'estado' => 'listo_para_embalar',
									'bodega_id' => $venta['Venta']['bodega_id'] ?? $bodega['Bodega']['id'],
									'metodo_envio_id' => $venta['Venta']['metodo_envio_id'],
									'marketplace_id' => $venta['Venta']['marketplace_id'],
									'comuna_id' => $venta['Venta']['comuna_id'],
									'venta_estado_id' => $venta['Venta']['venta_estado_id'],
									'fecha_listo_para_embalar' => date('Y-m-d H:i:s'),
									'ultima_modifacion' => date('Y-m-d H:i:s')
								)
							));
						}
					}
				}
				else
				{	# Se crea
					ClassRegistry::init('EmbalajeWarehouse')->save(array(
						'EmbalajeWarehouse' => array(
							'venta_id' => $venta['Venta']['id'],
							'estado' => 'listo_para_embalar',
							'bodega_id' => $venta['Venta']['bodega_id'] ?? $bodega['Bodega']['id'],
							'metodo_envio_id' => $venta['Venta']['metodo_envio_id'],
							'marketplace_id' => $venta['Venta']['marketplace_id'],
							'comuna_id' => $venta['Venta']['comuna_id'],
							'venta_estado_id' => $venta['Venta']['venta_estado_id'],
							'fecha_venta' => $venta['Venta']['fecha_venta'],
							'fecha_creacion' => date('Y-m-d H:i:s'),
							'fecha_listo_para_embalar' => date('Y-m-d H:i:s'),
							'ultima_modifacion' => date('Y-m-d H:i:s')
						)
					));
				}
				
				break;

			case 'empaquetando':

				
				# si existe embalaje listo para embalar lo actualizamos
				if (!empty($venta['EmbalajeWarehouse']))
				{
					foreach($venta['EmbalajeWarehouse'] as $embalaje)
					{
						if ($embalaje['estado'] == 'listo_para_embalar')
						{
							ClassRegistry::init('EmbalajeWarehouse')->save(array(
								'EmbalajeWarehouse' => array(
									'id' => $embalaje['id'],
									'estado' => 'procesando',
									'bodega_id' => $venta['Venta']['bodega_id'] ?? $bodega['Bodega']['id'],
									'metodo_envio_id' => $venta['Venta']['metodo_envio_id'],
									'marketplace_id' => $venta['Venta']['marketplace_id'],
									'comuna_id' => $venta['Venta']['comuna_id'],
									'venta_estado_id' => $venta['Venta']['venta_estado_id'],
									'ultima_modifacion' => date('Y-m-d H:i:s'),
									'responsable_id_procesando' => $venta['Venta']['administrador_id'],
									'fecha_procesando' => date('Y-m-d H:i:s')
								)
							));
						}
					}
				}

				break;
			case 'empaquetado':

				
				# si existe embalaje procesado lo actualizamos
				if (!empty($venta['EmbalajeWarehouse']))
				{
					foreach($venta['EmbalajeWarehouse'] as $embalaje)
					{
						if ($embalaje['estado'] == 'procesando')
						{
							ClassRegistry::init('EmbalajeWarehouse')->save(array(
								'EmbalajeWarehouse' => array(
									'id' => $embalaje['id'],
									'estado' => 'finalizado',
									'bodega_id' => $venta['Venta']['bodega_id'] ?? $bodega['Bodega']['id'],
									'metodo_envio_id' => $venta['Venta']['metodo_envio_id'],
									'marketplace_id' => $venta['Venta']['marketplace_id'],
									'comuna_id' => $venta['Venta']['comuna_id'],
									'venta_estado_id' => $venta['Venta']['venta_estado_id'],
									'ultima_modifacion' => date('Y-m-d H:i:s'),
									'responsable_id_finalizado' => $venta['Venta']['administrador_id'],
									'fecha_finalizado' => date('Y-m-d H:i:s')
								)
							));
						}
					}
				}

				break;
		}

		$this->redirect($this->referer('/', true));
	}


	public function admin_specials()
	{	
		
		$ids = $this->Venta->obtener_ventas_productos_retraso_ids();

		$condiciones = array(
			'Venta.id' => $ids
		);

		$joins = array();

		// Filtrado de ordenes por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ventas', 'specials');
		}

		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'ide':

						$id = trim($valor);

						if ($id != "") {

							$condiciones = array();

							$condiciones["OR"] = array(
								"Venta.id"         => $id,
								"Venta.id_externo"         => $id,
								"Venta.referencia" => $id
							);
							
						}

						break;
					case 'picking':

							$estado = trim($valor);
	
							$condiciones['Venta.picking_estado'] = $estado;
	
							break;
					case 'cliente_email':

						$email = trim($valor);

						$joins[] = array(
							'table' => 'rp_venta_clientes',
							'alias' => 'vc',
							'type' => 'INNER',
							'conditions' => array(
								'vc.id = Venta.venta_cliente_id',
								'OR' => array(
									"vc.nombre LIKE '%" .$email. "%'",
									"vc.apellido LIKE '%" .$email. "%'",
									"vc.rut LIKE '%" .$email. "%'",
									"vc.email LIKE '%" .$email. "%'",
									"vc.telefono LIKE '%" .$email. "%'"
								)
							)
						);

						break;
					case 'mensaje':
						
						if ($valor == 'cancelar')
						{
							$joins[] = array(
								'table' => 'rp_mensajes',
								'alias' => 'mj',
								'type' => 'INNER',
								'conditions' => array(
									'mj.venta_id = Venta.id',
									'mj.origen' => 'cliente',
									'mj.mensaje' => '(auto-atención) Cliente solicita cancelar la venta.' 
								)
							);
						}

						if ($valor == 'procesar')
						{
							$joins[] = array(
								'table' => 'rp_mensajes',
								'alias' => 'mj',
								'type' => 'INNER',
								'conditions' => array(
									'mj.venta_id = Venta.id',
									'mj.origen' => 'cliente',
									'mj.mensaje' => '(auto-atención) Cliente solicita devolución del dinero del/los productos con stockout y que se le envien el/los productos con existencias.' 
								)
							);
						}

						if ($valor == 'cambio')
						{
							$joins[] = array(
								'table' => 'rp_mensajes',
								'alias' => 'mj',
								'type' => 'INNER',
								'conditions' => array(
									'mj.venta_id = Venta.id',
									'mj.origen' => 'cliente',
									'mj.mensaje' => '(auto-atención) Cliente solicita cambio del/los productos con stockout, llamarlo y ofrecerle una alternativa.' 
								)
							);
						}

						if ($valor == 'no-auto')
						{
							$joins[] = array(
								'table' => 'rp_mensajes',
								'alias' => 'mj',
								'type' => 'LEFT',
								'conditions' => array(
									'mj.venta_id = Venta.id',
									'mj.origen' => 'cliente',
								)
							);

							$condiciones['mj.id'] = null;
						}

						break;
					case 'fecha_desde':
						$FiltroFechaDesde = trim($valor);

						if ($FiltroFechaDesde != "") {

							$ArrayFecha = explode("-", $FiltroFechaDesde);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 00:00:00"));

							$condiciones["Venta.fecha_venta >="] = $Fecha;

						}
						break;
					case 'fecha_hasta':
						$FiltroFechaHasta = trim($valor);

						if ($FiltroFechaHasta != "") {

							$ArrayFecha = explode("-", $FiltroFechaHasta);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 23:59:59"));

							$condiciones["Venta.fecha_venta <="] = $Fecha;

						} 
						break;
				}
			}
		}

		$qry = array(
			'recursive' => -1,
			'order' => array('Venta.fecha_venta' => 'DESC'),
			'contain' => array(
				'Mensaje' => array(
					'fields' => array(
						'Mensaje.mensaje'
					),
					'conditions' => array(
						'Mensaje.mensaje LIKE' => '(auto%'
					)
				),
				'VentaDetalle' => array(
					'fields' => array(
						'VentaDetalle.id',
						'VentaDetalle.cantidad',
						'VentaDetalle.cantidad_en_espera',
						'VentaDetalle.fecha_llegada_en_espera',
					),
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.nombre'
						)
					),
					'VentaDetallesReserva'=>[
						'fields'=>[ 
								'VentaDetallesReserva.venta_detalle_id',
								'VentaDetallesReserva.venta_detalle_producto_id',
								'VentaDetallesReserva.cantidad_reservada',
								'VentaDetallesReserva.bodega_id',
							]
						]
				),
				'OrdenCompra' => array(
					'fields' => array(
						'OrdenCompra.id'
					),
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.id'
						)
					)
				),
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.nombre'
					),
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.venta',
							'VentaEstadoCategoria.final',
							'VentaEstadoCategoria.estilo',
							'VentaEstadoCategoria.nombre'
						)
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.nombre'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.nombre'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.nombre',
						'VentaCliente.apellido',
						'VentaCliente.rut',
						'VentaCliente.email',
						'VentaCliente.telefono'
					)
				)
			),
			'conditions' => $condiciones,
			'joins' => $joins,
			'group' => 'Venta.id',
			'limit' => 10
		);
	
		$this->paginate = $qry;

		$ventas = $this->paginate();
		
		foreach ($ventas as $iv => $v) {
			
			foreach ($v['VentaDetalle'] as $ivd => $vd) :

				$items = Hash::extract($v['OrdenCompra'], '{n}.VentaDetalleProducto.{n}[id='.$vd['venta_detalle_producto_id'].'].OrdenComprasVentaDetalleProducto[estado_proveedor!=accept]');

				if (!empty($items)) {
					foreach ($items as $i => $item) {
						if (!empty($item['estado_proveedor'])) {
							$ventas[$iv]['OrdenCompraDetalle'][] = $item;		
						}
					}		
				}
			endforeach;

		}
		BreadcrumbComponent::add('Ventas', '/index');
		BreadcrumbComponent::add('Ventas especiales');

		$picking = ClassRegistry::init('Venta')->picking_estados_lista;

		$this->set(compact('ventas', 'picking'));

	}


	public function admin_export_specials()
	{	
		set_time_limit(0);
		ini_set('memory_limit', '-1');

		$ids = $this->Venta->obtener_ventas_productos_retraso_ids();

		$condiciones = array(
			'Venta.id' => $ids
		);

		$joins = array();
		$joins[] = array(
			'table' => 'rp_venta_mensajes',
			'alias' => 'VentaMensaje',
			'type' => 'LEFT',
			'conditions' =>[
			'VentaMensaje.venta_id = Venta.id']
		);
		// Filtrado de ordenes por formulario
		if ( $this->request->is('post') ) {
			$this->filtrar('ventas', 'export_specials');
		}

		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'ide':

						$id = trim($valor);

						if ($id != "") {

							$condiciones = array();

							$condiciones["OR"] = array(
								"Venta.id"         => $id,
								"Venta.id_externo"         => $id,
								"Venta.referencia" => $id
							);
							
						}

						break;
					case 'picking':

							$estado = trim($valor);
	
							$condiciones['Venta.picking_estado'] = $estado;
	
							break;
					case 'cliente_email':

						$email = trim($valor);

						$joins[] = array(
							'table' => 'rp_venta_clientes',
							'alias' => 'vc',
							'type' => 'INNER',
							'conditions' => array(
								'vc.id = Venta.venta_cliente_id',
								'OR' => array(
									"vc.nombre LIKE '%" .$email. "%'",
									"vc.apellido LIKE '%" .$email. "%'",
									"vc.rut LIKE '%" .$email. "%'",
									"vc.email LIKE '%" .$email. "%'",
									"vc.telefono LIKE '%" .$email. "%'"
								)
							)
						);

						break;
					case 'mensaje':
						
						if ($valor == 'cancelar')
						{
							$joins[] = array(
								'table' => 'rp_mensajes',
								'alias' => 'mj',
								'type' => 'INNER',
								'conditions' => array(
									'mj.venta_id = Venta.id',
									'mj.origen' => 'cliente',
									'mj.mensaje' => '(auto-atención) Cliente solicita cancelar la venta.' 
								)
							);
						}

						if ($valor == 'procesar')
						{
							$joins[] = array(
								'table' => 'rp_mensajes',
								'alias' => 'mj',
								'type' => 'INNER',
								'conditions' => array(
									'mj.venta_id = Venta.id',
									'mj.origen' => 'cliente',
									'mj.mensaje' => '(auto-atención) Cliente solicita devolución del dinero del/los productos con stockout y que se le envien el/los productos con existencias.' 
								)
							);
						}

						if ($valor == 'cambio')
						{
							$joins[] = array(
								'table' => 'rp_mensajes',
								'alias' => 'mj',
								'type' => 'INNER',
								'conditions' => array(
									'mj.venta_id = Venta.id',
									'mj.origen' => 'cliente',
									'mj.mensaje' => '(auto-atención) Cliente solicita cambio del/los productos con stockout, llamarlo y ofrecerle una alternativa.' 
								)
							);
						}

						if ($valor == 'no-auto')
						{
							$joins[] = array(
								'table' => 'rp_mensajes',
								'alias' => 'mj',
								'type' => 'LEFT',
								'conditions' => array(
									'mj.venta_id = Venta.id',
									'mj.origen' => 'cliente',
								)
							);

							$condiciones['mj.id'] = null;
						}

						break;
					case 'fecha_desde':
						$FiltroFechaDesde = trim($valor);

						if ($FiltroFechaDesde != "") {

							$ArrayFecha = explode("-", $FiltroFechaDesde);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 00:00:00"));

							$condiciones["Venta.fecha_venta >="] = $Fecha;

						}
						break;
					case 'fecha_hasta':
						$FiltroFechaHasta = trim($valor);

						if ($FiltroFechaHasta != "") {

							$ArrayFecha = explode("-", $FiltroFechaHasta);

							$Fecha = $ArrayFecha[2]. "-" .$ArrayFecha[1]. "-" .$ArrayFecha[0];

							$Fecha = date('Y-m-d H:i:s', strtotime($Fecha . " 23:59:59"));

							$condiciones["Venta.fecha_venta <="] = $Fecha;

						} 
						break;
				}
			}
		}

		$qry = array(
			'recursive' => -1,
			'order' => array('Venta.fecha_venta' => 'DESC'),
			'conditions' => $condiciones,
			'joins' => $joins,
			'group' => 'Venta.id',
			'contain'=> ['VentaMensaje'],
		);
		
		$datos 					= $this->Venta->find('all', $qry);
		$cantidad_comentarios 	= 0;
		$campos					= array_keys($this->Venta->_schema);
		foreach ($datos as $key => $value) {

			$observaciones = Hash::extract($value,'VentaMensaje.{*}.mensaje');
			if($observaciones){
				$datos[$key]['Venta'] =array_merge($datos[$key]['Venta'],$observaciones);
				if ($cantidad_comentarios < count($observaciones) ) {
					$cantidad_comentarios = count($observaciones);
				}
			}
			unset($datos[$key]['VentaMensaje']);
		}

		for ($i=1; $i <= $cantidad_comentarios; $i++) { 
			array_push($campos,('observacion_'.$i));
		}
		$this->set(compact('datos', 'campos'));

	}


	/**
	 * Intenta reservar el stock a la ventas más antiguas primero
	 * @return void
	 */
	public function reservar_ventas_sin_reserva()
	{

		$ventas = $this->Venta->obtener_ventas_sin_reserva();

		foreach ($ventas as $venta) {
			foreach ($venta['VentaDetalle'] as $detalle) {

				if (ClassRegistry::init('Venta')->reservar_stock_producto($detalle['id']) > 0) {
					// * Se sigue misma logica de instanciar metodo que hay en metodo "reservar_stock_producto"
					$this->WarehouseNodriza->procesar_embalajes($detalle['venta_id']);
				}
			}	
		}

		return;
	}


	/**
	 * Crear venta manualmente
	 */
	public function admin_add($id = '')
	{	
		# Referencia de la venta
		$referencia = ClassRegistry::init('Venta')->generar_referencia();

		if (empty($id)) {
			
			$currVenta = array(
				'Venta' => array(
					'referencia'       => $referencia,
					'tienda_id'        => $this->Session->read('Tienda.id'),
					'venta_estado_id'  => 1,
					'administrador_id' => $this->Auth->user('id'),
					'venta_manual'     => 1,
					'fecha_venta'      => date('Y-m-d H:i:s')
				)
			);
			$this->Venta->create();
			if ($this->Venta->save($currVenta)) {
				$this->redirect(array('action' => 'add', $this->Venta->id));
			}

		}

		if ($this->request->is('post') || $this->request->is('put')) {
			
			if (empty($this->request->data['Venta']['venta_cliente_id'])) {
				$this->Session->setFlash('No se logró relacionar al cliente con la venta. Intentelo nuevamente.', null, array(), 'warning');
				$this->redirect(array('action' => 'add', $id));
			}

			if ($this->request->data['Venta']['total'] == 0) {
				$this->Session->setFlash('El total de la venta no pude ser $0.', null, array(), 'warning');
				$this->redirect(array('action' => 'add', $id));
			}

			$this->request->data['VentaDetalle'] = Hash::extract($this->request->data['VentaDetalle'], '{n}');

			foreach ($this->request->data['VentaDetalle'] as $iv => $d) 
			{
				$this->request->data['VentaDetalle'][$iv] = array_replace_recursive($this->request->data['VentaDetalle'][$iv], array(
					'precio' => monto_neto($d['precio_bruto']),
					'cantidad_pendiente_entrega' => $d['cantidad'],
					'cantidad_reservada' => 0,
					'total_neto' => monto_neto($d['precio_bruto']) * $d['cantidad'],
					'total_bruto' => monto_bruto(monto_neto($d['precio_bruto']) * $d['cantidad'])
				));
			}
			# Evitamos la duplicación de productos en la misma venta
			$this->request->data['VentaDetalle'] = unique_multidim_array($this->request->data['VentaDetalle'], 'venta_detalle_producto_id');

			$total_pagado = 0;	
			foreach ($this->request->data['VentaTransaccion'] as $ip => $p) {
				$total_pagado = $total_pagado + (float) $p['monto'];
			}

			$estado_nuevo = ClassRegistry::init('VentaEstado')->obtener_estado_por_id($this->request->data['Venta']['venta_estado_id']);
			$estado_nuevo = $estado_nuevo['VentaEstado']['nombre'];
			
			
			# si el monto pagado >= al monto vendido se cambia a pago aceptado.
			if ($this->request->data['Venta']['total'] <= $total_pagado) {
				
				$this->request->data['Venta']['estado_anterior'] 			= 1;
				$this->request->data['Venta']['venta_estado_responsable'] 	= $this->Auth->user('email');
				
				# Guardamos el estado anterior en la tabla pivot
				$this->request->data['VentaEstado2'] = array(
					array(
						'venta_estado_id' => $this->request->data['Venta']['venta_estado_id'],
						'fecha'           => date('Y-m-d H:i:s'),
						'responsable'     => $this->Auth->user('email')
					)
				);

			}

			# Viene comuna
			if (!empty($this->request->data['Venta']['comuna_entrega'])) 
			{
				$this->request->data['Venta']['comuna_id'] =  ClassRegistry::init('Comuna')->obtener_id_comuna_por_nombre($this->request->data['Venta']['comuna_entrega']);
			}

			$this->request->data['Venta']['bodega_id'] = ClassRegistry::init('MetodoEnvio')->bodega_id($this->request->data['Venta']['metodo_envio_id']); 
			
			# Guardamos el origen de la venta
			$this->request->data['Venta']['origen_venta_manual'] = ClassRegistry::init('CanalVenta')->field('nombre', array(
				'id' => $this->request->data['Venta']['canal_venta_id']
			));	

			if ($this->Venta->saveAll($this->request->data) ) {

				$tienda = ClassRegistry::init('Tienda')->obtener_tienda($this->request->data['Venta']['tienda_id'], array('Tienda.nombre', 'Tienda.activar_notificaciones', 'Tienda.notificacion_apikey'));

				if ($tienda['Tienda']['activar_notificaciones'] && !empty($tienda['Tienda']['notificacion_apikey'])) {
					$this->Pushalert = $this->Components->load('Pushalert');

					$this->Pushalert::$api_key = $tienda['Tienda']['notificacion_apikey'];

					$tituloPush = sprintf('Nueva venta en %s', $tienda['Tienda']['nombre']);
					$mensajePush = sprintf('Pincha aquí para verla');
					$urlPush = Router::url('/', true) . 'ventas/view/' . $id;

					$this->Pushalert->enviarNotificacion($tituloPush, $mensajePush, $urlPush);	
				}

				# si es un estado pagado se reserva el stock disponible
				if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($this->request->data['Venta']['venta_estado_id'])) {
					$this->Venta->pagar_venta($id);
					$this->actualizar_canales_stock($id);
				}

				# Plantilla nuevo estado
				ClassRegistry::init('VentaEstado')->id = $this->request->data['Venta']['venta_estado_id'];
				$notificar        = ClassRegistry::init('VentaEstado')->field('notificacion_cliente');
				$plantillaEmail   = ClassRegistry::init('VentaEstadoCategoria')->field('plantilla', array('id' => ClassRegistry::init('VentaEstado')->field('venta_estado_categoria_id')));	
				
				if (!empty($plantillaEmail) && $notificar) {
					$this->notificar_cambio_estado($id, $plantillaEmail, $estado_nuevo);
				}

				$this->Session->setFlash('Venta #' . $id . ' creada exitosamente.', null, array(), 'success');
				$this->redirect(array('controller' => 'ordenes', 'action' => 'generar', $id));

			}else{
				$this->Session->setFlash('No fue posible crear la venta. Verifique los campos e intente nuevamente: ', null, array(), 'danger');
				$this->redirect(array('action' => 'add', $id));
			}

		}

		$this->request->data = $this->Venta->obtener_venta_por_id($id);

		BreadcrumbComponent::add('Listado de ventas', '/ventas');
		BreadcrumbComponent::add('Crear venta');

		# Estados disponibles para esta venta
		$ventaEstados = ClassRegistry::init('VentaEstado')->find('list', array('conditions' => array('activo' => 1)));
		
		$transportes  = ClassRegistry::init('Transporte')->find('list', array('conditions' => array('activo' => 1)));
		
		$comunas = ClassRegistry::init('Comuna')->find('list', array('fields' => array('Comuna.nombre', 'Comuna.nombre'), 'order' => array('Comuna.nombre' => 'ASC')));
		
		$marketplaces = ClassRegistry::init('Marketplace')->find('list', array('conditions' => array('activo' => 1)));
		
		$medioPagos   = ClassRegistry::init('MedioPago')->find('list', array('conditions' => array('activo' => 1)));
		$metodoEnvios = []; 
		
		$metodoEnvios_sin_procesar = ClassRegistry::init('MetodoEnvio')->find('all', array(
			'contain'=>[
				'Bodega'=>['fields'=>'Bodega.nombre'

			]],
			'fields'=>['MetodoEnvio.id','MetodoEnvio.nombre','MetodoEnvio.dependencia'],
			'conditions' => array('MetodoEnvio.activo' => 1,'MetodoEnvio.bodega_id IN' => Hash::extract($this->Auth->user('Bodega'), '{n}.id' ))));

		foreach ($metodoEnvios_sin_procesar as $value) {
			$metodoEnvios[$value['MetodoEnvio']['id']] ="{$value['Bodega']['nombre']} - {$value['MetodoEnvio']['nombre']} ".(isset($value['MetodoEnvio']['dependencia'])?"| Dependencia {$value['MetodoEnvio']['dependencia']}":'');
		}
		
		$clientes     = ClassRegistry::init('VentaCliente')->find('list', array('fields' => array('VentaCliente.id', 'VentaCliente.email')));

		$origen_venta = ClassRegistry::init('CanalVenta')->find('list', array('conditions' => array('activo' => 1)));
		
		$tipo_venta   =	ClassRegistry::init('VentaEstado')->find('list',['conditions'=>
		[['VentaEstado.nombre '=>$this->tipo_venta]]
		]);

		$bodega_id = $this->Auth->user('Rol.bodega_id');

		$this->set(compact('ventaEstados', 'transportes', 'comunas', 'marketplaces', 'clientes', 'medioPagos', 'referencia', 'metodoEnvios', 'origen_venta','tipo_venta','bodega_id'));
		
	}


	public function admin_edit($id)
	{
		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if ($this->request->is('post') || $this->request->is('put')) {

			$venta = ClassRegistry::init('Venta')->find(
				'first',
				array(
					'conditions' => array(
						'Venta.id' => $id
					),
					'contain' => array(
						'VentaDetalle' => array(
							'fields' => array(
								'VentaDetalle.precio', 'VentaDetalle.cantidad','VentaDetalle.monto_anulado'
							)
						)
					),
					'fields' => array(
						'Venta.id',
						'Venta.descuento',
						'Venta.direccion_entrega',
						'Venta.numero_entrega',
						'Venta.otro_entrega',
						'Venta.comuna_entrega',
						'Venta.metodo_envio_id',
						'Venta.rut_receptor',
						'Venta.nombre_receptor',
						'Venta.fono_receptor',
						'Venta.ciudad_entrega',	
						'Venta.costo_envio',	
						'Venta.comuna_id',
						'Venta.total',
						'Venta.referencia_despacho',
						'Venta.nota_interna',
						'Venta.bodega_id',
						'Venta.tienda_id',
						'Venta.venta_manual',
						'Venta.id_externo',
						'Venta.marketplace_id',
					)
				)
			);

			if(isset($this->request->data['Venta']['opt'])){ 
 
				if (!empty($this->request->data['Venta']['comuna_entrega'])) { 
					$this->request->data['Venta']['comuna_id'] =  ClassRegistry::init('Comuna')->obtener_id_comuna_por_nombre($this->request->data['Venta']['comuna_entrega']); 
				} 

				$metodo_envio =  ClassRegistry::init('MetodoEnvio')->find('first',[
					'fields'     => [	'MetodoEnvio.retiro_local','MetodoEnvio.id'],
					'conditions' => 
						[
						'MetodoEnvio.id' =>$this->request->data['Venta']['metodo_envio_id']
						]
				]);
				
				if ($metodo_envio) {

					$TotalProductos = 0;
					
					foreach ($venta['VentaDetalle'] as $detalle) {
						$TotalProductos 	= $TotalProductos + ($detalle['precio'] * $detalle['cantidad'] - $detalle['monto_anulado']);
					}
					$this->request->data['Venta']['total'] = monto_bruto($TotalProductos,null,0) + $this->request->data['Venta']['costo_envio'] - $venta['Venta']['descuento']??0;
					
				}
			} 
			
			$metodo_envio_id_old = ClassRegistry::init('Venta')->metodo_envio_id($this->request->data['Venta']['id']);
			$cambiar_metodo_envio = false;
			if (isset($this->request->data['Venta']['metodo_envio_id'])) {
				if ($metodo_envio_id_old != $this->request->data['Venta']['metodo_envio_id'] ) {
					$this->request->data['Venta']['bodega_id'] = ClassRegistry::init('MetodoEnvio')->bodega_id($this->request->data['Venta']['metodo_envio_id']); 
					$cambiar_metodo_envio= true;
				}
			}
			
			# Actualizar canal venta - Homologamos el campo
			if (isset($this->request->data['Venta']['canal_venta_id']))
			{	
				$this->request->data['Venta']['origen_venta_manual'] = ClassRegistry::init('CanalVenta')->field('nombre', array(
					'id' => $this->request->data['Venta']['canal_venta_id']
				));	
			}

			if ($this->Venta->save($this->request->data)) {
				
				ksort($venta['Venta']);
				ksort($this->request->data['Venta']);

				$log[] = array(
					'Log' => array(
						'administrador' => 'Cambio información despacho vid - ' . $id,
						'modulo' => 'Ventas',
						'modulo_accion' => json_encode(
							[
								"Usuario ".CakeSession::read('Auth.Administrador.id')." realizo siguiente cambios"=>
									[
										'original'	=> $venta['Venta'],
										'cambios'	=> $this->request->data['Venta'],
									]
							])
					)
				);
			
				if ($cambiar_metodo_envio) {

					if (Configure::read('ambiente') != 'dev') {

						if (!$venta['Venta']['marketplace_id'] && !empty($venta['Venta']['id_externo']) && !$venta['Venta']['venta_manual']) {

							ClassRegistry::init('Tienda')->id = $venta['Venta']['tienda_id'];
							$this->Prestashop->crearCliente( ClassRegistry::init('Tienda')->field('apiurl_prestashop'), ClassRegistry::init('Tienda')->field('apikey_prestashop'));
							$metodo_envio_prestashop =  $this->Prestashop->prestashop_obtener_trasnportista_por_nombre(ClassRegistry::init('MetodoEnvio')->nombre($this->request->data['Venta']['metodo_envio_id']));
					
							if ($metodo_envio_prestashop) {
								$this->Prestashop->prestashop_cambiar_transportista_actual_venta($venta['Venta']['id_externo'], $metodo_envio_prestashop['id']);
								$log[] = array(
									'Log' => array(
										'administrador' => "Se actualizo  vid - $id | id_externo {$venta['Venta']['id_externo']} en Prestashop",
										'modulo'	 	=> 'Ventas',
										'modulo_accion' => "Se actualiza metodo envio en prestashop {$metodo_envio_prestashop['id']} | Sistema {$this->request->data['Venta']['metodo_envio_id']} - ".ClassRegistry::init('MetodoEnvio')->nombre($this->request->data['Venta']['metodo_envio_id'])
									)
								);
							}else{
	
								$log[] = array(
									'Log' => array(
										'administrador' => "Problemas para actualizar  vid - $id en Prestashop",
										'modulo'	 	=> 'Ventas',
										'modulo_accion' => "No se pudo actualizar metodo de envio {$this->request->data['Venta']['metodo_envio_id']} debido a que no fue encontrado en Prestashop."
									)
								);
							}
						}
					}

					$response = $this->WarehouseNodriza->CambiarCancelado_V2($venta['Venta']['id'],CakeSession::read('Auth.Administrador.id')??1,true,"Se han cancelado embalajes de la vid {$venta['Venta']['id']} debido a cambios en el metodo de envio");
					
					if ($response['code'] == 200) {
						$this->Venta->save([
							'Venta'=>[
								'id'=> $id,
								'picking_estado' => 'empaquetar'
							]
							]);
					}
					
					$log[] = array(
						'Log' => array(
							'administrador' => "Se solicito cancelar embalajes de la vid {$venta['Venta']['id']} a Warehouse",
							'modulo' 		=> 'Ventas',
							'modulo_accion' => json_encode($response)
						)
					);

					$response = $this->WarehouseNodriza->procesar_embalajes($venta['Venta']['id']);
										
				}

				ClassRegistry::init('Log')->create();
				ClassRegistry::init('Log')->saveMany($log);

				$this->Session->setFlash('Venta actualizada con éxito.', null, array(), 'success');
			}else{
				$this->Session->setFlash('No fue posible actualizar la venta.', null, array(), 'danger');
			}
		}

		$this->redirect($this->referer('/', true));
	}

	
	/**
	 * admin_crear_nota_despacho
	 *
	 * @return void
	 */
	public function admin_crear_nota_despacho()
	{	

		if (!$this->request->is('post'))
		{
			$this->Session->setFlash('Sólo se permite request de tipo post.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if (!$this->Venta->exists($this->request->data['Nota']['venta_id']))
		{
			$this->Session->setFlash('La venta seleccionada no existe.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}
		
		$nota = [
			'venta_id' => $this->request->data['Nota']['venta_id'],
			'nombre' => $this->request->data['Nota']['titulo'],
			'descripcion' => $this->request->data['Nota']['nota_despacho_global'],
			'id_usuario' => $this->Auth->user('id'),
			'nombre_usuario' => $this->Auth->user('nombre'),
			'mail_usuario' => $this->Auth->user('email')
		];

		# Se asignan los id de embalajes si vienen
		if (isset($this->request->data['Nota']['embalaje_id']))
		{
			$nota = array_replace_recursive($nota, [
				'embalajes' => [
					['id_embalaje' => $this->request->data['Nota']['embalaje_id']]
				]
			]);
		}
	
		# Creamos la nota vía api
		$result = $this->WarehouseNodriza->crearNotaDespacho($nota);

		if ($result['code'] == 200)
		{
			$this->Session->setFlash('Notificación creada con éxito.', null, array(), 'success');
			$this->redirect($this->referer('/', true));
		}

		$this->Session->setFlash('No fue posible crear la nota. Intente nuevamente.', null, array(), 'danger');
		$this->redirect($this->referer('/', true));
	}

	
	/**
	 * admin_eliminar_nota_despacho
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function admin_eliminar_nota_despacho($id)
	{	

		if (!$this->request->is('post'))
		{
			$this->Session->setFlash('Sólo se permite request de tipo post.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		# Creamos la nota vía api
		$result = $this->WarehouseNodriza->eliminarNotaDespacho($id);
		
		if ($result['code'] == 200)
		{
			$this->Session->setFlash('Notificación eliminada con éxito.', null, array(), 'success');
			$this->redirect($this->referer('/', true));
		}

		$this->Session->setFlash('No fue posible eliminar la nota. Intente nuevamente.', null, array(), 'danger');
		$this->redirect($this->referer('/', true));
	}
	

	public function admin_en_espera($id)
	{
		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if ($this->request->is('post') || $this->request->is('put')) {
			
			if ($this->Venta->VentaDetalle->saveMany($this->request->data)) {
				$this->Session->setFlash('Venta actualizada con éxito.', null, array(), 'success');
				$this->shell = true;
				$this->admin_reservar_stock_venta($id);
				$this->shell = false;
			}else{
				$this->Session->setFlash('No fue posible actualizar la venta.', null, array(), 'danger');
			}
		}

		$this->redirect($this->referer('/', true));
	}


	/**
	 * Permite limpiar los campos de agendamiento de una línea
	 * de producto en una venta dada
	 * @param int $id Id de la venta
	 * @param int $id_detalle Id del detalle del producto
	 * @return redirect
	 */
	public function admin_quitar_en_espera($id, $id_detalle)
	{	
		if ( ! $this->Venta->exists($id) ||
			! $this->Venta->VentaDetalle->exists($id_detalle) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		$ventaDetalle = array(
			'VentaDetalle' => array(
				'id'                      => $id_detalle,
				'cantidad_en_espera'      => 0,
				'fecha_llegada_en_espera' => null
			)
		);

		if ($this->Venta->VentaDetalle->save($ventaDetalle)){
			$this->Session->setFlash('Línea actualizada correctamente.', null, array(), 'success');
			
			$this->shell = true;
			$this->admin_reservar_stock_venta($id);
			$this->shell = false;

		}else{
			$this->Session->setFlash('No fue posible limpiar el agendamiento.', null, array(), 'danger');
		}

		$this->redirect($this->referer('/', true));
	
	}


	public function admin_crear_mensaje_venta($id)
	{
		$venta = $this->Venta->obtener_venta_por_id($id);

		if (empty($venta)) {
			$this->Session->setFlash('La venta no existe', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		$canal = 'prestashop';
		if (!empty($venta['Venta']['marketplace_id'])) {

			$tipoMarketplace = ClassRegistry::init('Marketplace')->field('marketplace_tipo_id', array('id' => $venta['Venta']['marketplace_id']) );

			if ($tipoMarketplace == 1) {
				$canal = 'linio';
			}

			if ($tipoMarketplace == 2) {
				$canal = 'mercadolibre';
			}
		}


		switch ($canal) {
			case 'prestashop':
				# Registrar mensaje en prestashop

				$this->Prestashop->crearCliente($venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop']);
				$this->Prestashop->prestashop_crear_mensaje($venta['Venta']['id_externo']);

				break;
			case 'linio':
				# Registrar mensaje en linio (proximamente)


				break;
			case 'mercadolibre':
				# Registrar mensaje en meli (proximamente)

				break;
		}
		
	}



	/**
	 * [admin_registrar_seguimiento description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_registrar_seguimiento($id = null)
	{
		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		if (!$this->request->is('put')) {
			$this->Session->setFlash('Acción no permittida', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}

		$dataToSave = array(
			'Venta' => array(
				'id' => $id
			)
		);

		foreach ($this->request->data['Transporte'] as $it => $t) {

			if (empty($t['transporte_id']) || empty($t['cod_seguimiento'])) {
				continue;
			}

			$dataToSave['Transporte'][$it]['transporte_id']   = $t['transporte_id'];
			$dataToSave['Transporte'][$it]['cod_seguimiento'] = $t['cod_seguimiento'];
			$dataToSave['Transporte'][$it]['created']         = date('Y-m-d H:i:s');
		}

		# Guardamos los códigos de seguimiento
		if ($this->Venta->saveAll($dataToSave)) {
			$this->Session->setFlash('N° seguimiento registrado con éxito.', null, array(), 'success');
			$this->redirect($this->referer('/', true));
		}else{
			$this->Session->setFlash('Error al registrar el n°de seguimiento. Intente nuevamente.', null, array(), 'danger');
			$this->redirect($this->referer('/', true));
		}
	}


	/**
	 * [cambiarEstado description]
	 * @param  [type] $id_venta         [description]
	 * @param  [type] $id_externo       [description]
	 * @param  [type] $estado_nuevo_id  [description]
	 * @param  [type] $tienda_id        [description]
	 * @param  [type] $marketplace_id   [description]
	 * @param  string $razonCancelado   [description]
	 * @param  string $detalleCancelado [description]
	 * @return [type]                   [description]
	 */
	public function cambiarEstado($id_venta, $id_externo, $estado_nuevo_id, $tienda_id, $marketplace_id = null, $razonCancelado = '', $detalleCancelado = '', $responsable = '', $fecha_cambio = '')
	{
		ClassRegistry::init('VentaEstado')->id = $estado_nuevo_id;
		ClassRegistry::init('Tienda')->id      = $tienda_id;

		$log = array();

		# si es marketplace definimos el objeto
		if (!is_null($marketplace_id)) {
			ClassRegistry::init('Marketplace')->id = $marketplace_id;				
		}

		$venta                = $this->preparar_venta($id_venta);

		$log[] = array(
			'Log' => array(
				'administrador' => 'Inicia cambio estado ' . $id_venta,
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($venta)
			)
		);
		
		$notificar            = ClassRegistry::init('VentaEstado')->field('notificacion_cliente');
		$notificado           = false;

		$estado_actual_nombre = ClassRegistry::init('VentaEstado')->obtener_estado_por_id($venta['Venta']['venta_estado_id'])['VentaEstado']['nombre'];
		$estado_nuevo_nombre  = ClassRegistry::init('VentaEstado')->field('nombre');
		
		$esPrestashop         = (empty($marketplace_id) && !$venta['Venta']['venta_manual']) ? true : false;
		
		$plantillaEmail       = ClassRegistry::init('VentaEstadoCategoria')->field('plantilla', array('id' => ClassRegistry::init('VentaEstado')->field('venta_estado_categoria_id')));		

		$esMercadolibre = false;
		$esLinio        = false;

		$apiurlprestashop = '';
		$apikeyprestashop = '';
		$apiurllinio      = '';
		$apiuserlinio     = '';
		$apikeylinio      = '';
		
		# Es marketplace
		if (!$esPrestashop && !empty($marketplace_id)) 
		{
			switch ( ClassRegistry::init('Marketplace')->field('marketplace_tipo_id') ) 
			{
				case 1: // Linio
					$esLinio      = true;
					$apiurllinio  = ClassRegistry::init('Marketplace')->field('api_host');
					$apiuserlinio = ClassRegistry::init('Marketplace')->field('api_user');
					$apikeylinio  = ClassRegistry::init('Marketplace')->field('api_key');
					break;
				
				case 2: // Meli
					$esMercadolibre = true;
					break;
				default:
					$esLinio = false;
					$esMercadolibre = false;
					$esPrestashop = false;
			}
		}
		else
		{
			$apiurlprestashop = ClassRegistry::init('Tienda')->field('apiurl_prestashop');
			$apikeyprestashop = ClassRegistry::init('Tienda')->field('apikey_prestashop');
		}

		# Prestashop
		if ( $esPrestashop && !empty($apiurlprestashop) && !empty($apikeyprestashop)) 
		{	
			# Para la consola se carga el componente on the fly!
			$this->Prestashop = $this->Components->load('Prestashop');
			# Cliente Prestashop
			$this->Prestashop->crearCliente( $apiurlprestashop, $apikeyprestashop );

			# OBtenemos el ID prestashop del estado
			$estadoPrestashop = $this->Prestashop->prestashop_obtener_estado_por_nombre($estado_nuevo_nombre);
			
			if (empty($estadoPrestashop)) {
				throw new Exception("Error al cambiar el estado. No fue posible obtener el estado de Prestashop", 505);
			}

			if (Configure::read('ambiente') == 'dev') {
				$resCambio = true;
			}else{
				$resCambio = $this->Prestashop->prestashop_cambiar_estado_venta($id_externo, $estadoPrestashop['id']);
			}

			$log[] = array(
				'Log' => array(
					'administrador' => 'Respuesta cambio estado ' . $id_venta,
					'modulo' 		=> 'Ventas',
					'modulo_accion' => 'Resultado: ' . $resCambio . ' - Estado nuevo:' . json_encode($estadoPrestashop)
				)
			);
			
			if ($resCambio) {

				# Enviar email al cliente
				if (!empty($plantillaEmail) && $notificar) {
					$notificado = $this->notificar_cambio_estado($id_venta, $plantillaEmail, $estado_nuevo_nombre);

					$log[] = array(
						'Log' => array(
							'administrador' => 'Notificacion cambio estado ' . $id_venta,
							'modulo' 		=> 'Ventas',
							'modulo_accion' => 'Resultado: ' . json_encode(array(
								'notificado' 	=> $notificado,
								'plantilla' 	=> $plantillaEmail,
								'nuevo_estado' 	=> $estado_nuevo_nombre
							))
						)
					);

				}

				# si es un estado pagado se reserva el stock disponible
				if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_pagado($estado_nuevo_id) && !ClassRegistry::init('VentaEstado')->es_estado_entregado($estado_nuevo_id) && !ClassRegistry::init('VentaEstado')->estado_mueve_bodega($estado_nuevo_id)) {
					$this->Venta->pagar_venta($id_venta);
					$this->actualizar_canales_stock($id_venta);

					$log[] = array(
						'Log' => array(
							'administrador' => 'Pagar venta cambio estado ' . $id_venta,
							'modulo' => 'Ventas',
							'modulo_accion' => 'Resultado: ' . json_encode(array(
								'nuevo_estado' => $estado_nuevo_nombre
							))
						)
					);
				}

				# Se entrega la venta
				if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_pagado($estado_nuevo_id) && ClassRegistry::init('VentaEstado')->estado_mueve_bodega($estado_nuevo_id)) {
					
					$this->Venta->entregar($id_venta);

					$log[] = array(
						'Log' => array(
							'administrador' => 'Entregar venta cambio estado ' . $id_venta,
							'modulo' => 'Ventas',
							'modulo_accion' => 'Resultado: ' . json_encode(array(
								'nuevo_estado' => $estado_nuevo_nombre
							))
						)
					);
				}

				# si es un estado cancelado se devuelve el stock a la bodega
				if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_rechazo($estado_nuevo_id) && !ClassRegistry::init('VentaEstado')->es_estado_cancelado($estado_nuevo_id)) {
					$this->Venta->cancelar_venta($id_venta);
					$this->WarehouseNodriza->procesar_embalajes($id_venta);
					$this->actualizar_canales_stock($id_venta);

					$log[] = array(
						'Log' => array(
							'administrador' => 'Cancelar venta cambio estado ' . $id_venta,
							'modulo' => 'Ventas',
							'modulo_accion' => 'Resultado: ' . json_encode(array(
								'nuevo_estado' => $estado_nuevo_nombre
							))
						)
					);
				}
				
				if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_cancelado($estado_nuevo_id) ) {
					$this->Venta->cancelar_venta($id_venta);
					$this->WarehouseNodriza->procesar_embalajes($id_venta);
					$this->actualizar_canales_stock($id_venta);

					$log[] = array(
						'Log' => array(
							'administrador' => 'Cancelar venta cambio estado ' . $id_venta,
							'modulo' => 'Ventas',
							'modulo_accion' => 'Resultado: ' . json_encode(array(
								'nuevo_estado' => $estado_nuevo_nombre
							))
						)
					);
				}
				
			}else{
				throw new Exception('Error al cambiar el estado. No fue posible cambiar el estado en Prestashop.', 506);
			}
			
		# Linio
		}
		elseif ( $esLinio && !empty($apiurllinio) && !empty($apiuserlinio) && !empty($apikeylinio)) 
		{	
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Linio = $this->Components->load('Linio');
			}
			# cliente Linio
			$this->Linio->crearCliente( $apiurllinio, $apiuserlinio, $apikeylinio );

			$itemsVenta = $this->Linio->linio_obtener_venta_detalles($id_externo);

			if (!isset($itemsVenta[0])) {
				$itemsVenta = array(
					0 => $itemsVenta
				);
			}


			if (!array_key_exists($estado_nuevo_nombre, $this->Linio->estados)) {
				throw new Exception('¡Error! El estado seleccionado no está disponible en Linio', 507);
			}
			
			switch ($estado_nuevo_nombre) {
				case 'canceled':

					foreach ($itemsVenta as $ii => $item) {
						# Cancelamos pedido en Linio
						if(!$this->Linio->linio_cancelar_pedido($item['OrderItemId'], $razonCancelado, $detalleCancelado)){
							throw new Exception('Imposible cambiar el estado. Intente cancelarla directamente en Seller Center.', 508);
						}

					}

					break;
				case 'ready_to_ship':
					 
					# Listo para envio pedido en Linio Por defecto se usa Blue Express
					if(!$this->Linio->linio_listo_para_envio( Hash::extract($itemsVenta, '{n}.OrderItemId') )) {
						throw new Exception('Imposible cambiar el estado. Intente cancelarla directamente en Seller Center.', 508);
					}

					break;
			}

			# si es un estado pagado se reserva el stock disponible
			if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_pagado($estado_nuevo_id) && !ClassRegistry::init('VentaEstado')->es_estado_entregado($estado_nuevo_id)) {
				$this->Venta->pagar_venta($id_venta);
				$this->actualizar_canales_stock($id_venta);
			}

			# se entrega la venta
			if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_pagado($estado_nuevo_id) && ClassRegistry::init('VentaEstado')->es_estado_entregado($estado_nuevo_id)) {
				$this->Venta->entregar($id_venta);
			}

			# si es un estado cancelado se devuelve el stock a la bodega
			if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_rechazo($estado_nuevo_id) && !ClassRegistry::init('VentaEstado')->es_estado_cancelado($estado_nuevo_id)) {
				$this->Venta->revertir_venta($id_venta);
				$this->actualizar_canales_stock($id_venta);
			}

			if ( $estado_actual_nombre != $estado_nuevo_nombre && ClassRegistry::init('VentaEstado')->es_estado_cancelado($estado_nuevo_id)) {
				$this->Venta->cancelar_venta($id_venta);
				$this->WarehouseNodriza->procesar_embalajes($id_venta);
				$this->actualizar_canales_stock($id_venta);
			}
			
		# Meli
		}
		elseif ( $esMercadolibre ) 
		{	
			#throw new Exception('¡Error! No está habilitada la opción de cambios de estado en Meli.', 501);
			
		}
		elseif ( $venta['Venta']['venta_manual'])
		{	
			# Venta manual
			# Enviar email al cliente
			if (!empty($plantillaEmail) && $notificar) {
				$notificado = $this->notificar_cambio_estado($id_venta, $plantillaEmail, $estado_nuevo_nombre);

				$log[] = array(
					'Log' => array(
						'administrador' => 'Notificacion cambio estado ' . $id_venta,
						'modulo' => 'Ventas',
						'modulo_accion' => 'Resultado: ' . json_encode(array(
							'notificado' => $notificado,
							'plantilla' => $plantillaEmail,
							'nuevo_estado' => $estado_nuevo_nombre
						))
					)
				);
			}

			# si es un estado pagado se reserva el stock disponible
			if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($estado_nuevo_id) && !ClassRegistry::init('VentaEstado')->es_estado_entregado($estado_nuevo_id) && !ClassRegistry::init('VentaEstado')->estado_mueve_bodega($estado_nuevo_id)) {
				$this->Venta->pagar_venta($id_venta);
				$this->actualizar_canales_stock($id_venta);

				$log[] = array(
					'Log' => array(
						'administrador' => 'Pagar venta cambio estado ' . $id_venta,
						'modulo' => 'Ventas',
						'modulo_accion' => 'Resultado: ' . json_encode(array(
							'nuevo_estado' => $estado_nuevo_nombre
						))
					)
				);
			}

			# Se entrega la venta
			if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($estado_nuevo_id) && ClassRegistry::init('VentaEstado')->estado_mueve_bodega($estado_nuevo_id)) {
				$this->Venta->entregar($id_venta);

				$log[] = array(
					'Log' => array(
						'administrador' => 'Entregar venta cambio estado ' . $id_venta,
						'modulo' => 'Ventas',
						'modulo_accion' => 'Resultado: ' . json_encode(array(
							'nuevo_estado' => $estado_nuevo_nombre
						))
					)
				);
			}

			# si es un estado cancelado se devuelve el stock a la bodega
			if ( ClassRegistry::init('VentaEstado')->es_estado_rechazo($estado_nuevo_id) && !ClassRegistry::init('VentaEstado')->es_estado_cancelado($estado_nuevo_id)) {
				$this->Venta->cancelar_venta($id_venta);
				$this->WarehouseNodriza->procesar_embalajes($id_venta);
				$this->actualizar_canales_stock($id_venta);

				$log[] = array(
					'Log' => array(
						'administrador' => 'Cancelar venta cambio estado ' . $id_venta,
						'modulo' => 'Ventas',
						'modulo_accion' => 'Resultado: ' . json_encode(array(
							'nuevo_estado' => $estado_nuevo_nombre
						))
					)
				);
			}

			if ( ClassRegistry::init('VentaEstado')->es_estado_cancelado($estado_nuevo_id) ) {
				$this->Venta->cancelar_venta($id_venta);
				$this->WarehouseNodriza->procesar_embalajes($id_venta);
				$this->actualizar_canales_stock($id_venta);

				$log[] = array(
					'Log' => array(
						'administrador' => 'Cancelar venta cambio estado ' . $id_venta,
						'modulo' => 'Ventas',
						'modulo_accion' => 'Resultado: ' . json_encode(array(
							'nuevo_estado' => $estado_nuevo_nombre
						))
					)
				);
			}
		}
		else
		{	
			throw new Exception('¡Error! Se debe actualizar el estado actual por otro.', 501);
		}

		# se setea el id de la venta
		$saveVenta['Venta']['id']                       = $venta['Venta']['id'];
		$saveVenta['Venta']['venta_estado_id']          = $estado_nuevo_id;
		$saveVenta['Venta']['venta_estado_responsable'] = (!empty($responsable)) ? $responsable : $this->Session->read('Auth.Administrador.nombre');
		
		# Guardamos el estado anterior en la tabla pivot
		$saveVenta['VentaEstado2'] = array(
			array(
				'venta_estado_id' => $estado_nuevo_id,
				'fecha'           => ($fecha_cambio) ? $fecha_cambio : date('Y-m-d H:i:s'),
				'responsable'     => $saveVenta['Venta']['venta_estado_responsable']
			)
		);

		foreach ($venta['VentaEstado2'] as $ive => $ve) {
			$saveVenta['VentaEstado2'][] = $ve['EstadosVenta'];
		}

		# Guardamos el log
		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);
		
		if ($this->Venta->saveAll($saveVenta)) {

			$this->WarehouseNodriza->procesar_embalajes($id_venta);	
			return true;
			
		}else{
			
			if ($notificar && !$notificado) {
				throw new Exception('No fue posible notificar al cliente el cambio de estado.', 707);
			}else{
				throw new Exception('Error al cambiar el estado. Intente nuevamente.', 303);
			}
		}
	}

	/**
	 * [admin_reservar_stock_venta description]
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function admin_reservar_stock_venta($id = '')
	{
		$venta = $this->Venta->obtener_venta_por_id($id);
		$result = array();
		if ($venta['VentaEstado']['VentaEstadoCategoria']['reserva_stock'] == true) {

			foreach ($venta['VentaDetalle'] as $key => $value) {

				$cant = $this->Venta->reservar_stock_producto($value['id']);
				if ( $cant > 0) {
					// * Se sigue misma logica de instanciar metodo que hay en metodo "reservar_stock_producto"
					$this->WarehouseNodriza->procesar_embalajes(ClassRegistry::init('VentaDetalle')->Venta_id($value['id']));
				}
	
				if ($cant == 1) {
					$result['success'][]  = $value['VentaDetalleProducto']['nombre'] . ' - Cant reservada: ' . $cant  . ' unidad.';
				}elseif($cant > 1) {
					$result['success'][]  = $value['VentaDetalleProducto']['nombre'] . ' - Cant reservada: ' . $cant  . ' unidades.';
				}elseif ($cant == 0) {
					$result['warning'][]  = $value['VentaDetalleProducto']['nombre'] . ' - Cant reservada: ' . $cant  . ' unidades.';
				}
			}

			if (!empty($result['success']) && !$this->shell) {
				$this->Session->setFlash($this->crearAlertaUl($result['success'], 'Resultados'), null, array(), 'success');
			}
	
			if (!empty($result['warning']) && !$this->shell) {
				$this->Session->setFlash($this->crearAlertaUl($result['warning'], 'Resultados'), null, array(), 'warning');
			}
	
			if ($this->shell) {
				return $result;
			}else{
				$this->redirect($this->referer('/', true));
			}
		}
		
		$this->Session->setFlash('No se han realizados reserva ya que la categoría del estado no lo permite', null, array(), 'warning');
		
		$this->redirect($this->referer('/', true));
	}


	/**
	 * [actualizar_canales_stock description]
	 * @param  [type] $id_venta [description]
	 * @return [type]           [description]
	 */
	public function actualizar_canales_stock($id_venta, $excluir = array())
	{	
		$venta = $this->Venta->obtener_venta_por_id($id_venta);
		
		# si el marketplace tiene desactivada la opcion de stock se termina el flujo
		if (!empty($venta['Marketplace'])) {
			if ($venta['Marketplace']['id'] && !$venta['Marketplace']['stock_automatico']) {
				return false;
			}
		}
		
		foreach ($venta['VentaDetalle'] as $ip => $producto) {

			# si el producto tiene desactivada la opcion de stock se termina el flujo
			if (!$producto['VentaDetalleProducto']['stock_automatico']) {
				continue;
			}
			
			# Descontar stock virtual y refrescar canales
			$productosController = new VentaDetalleProductosController();

			$productosController->actualizar_canales_stock($producto['VentaDetalleProducto']['id_externo'], $producto['VentaDetalleProducto']['cantidad_virtual'], $excluir);	

		}

		return false;
	}


	/**
	 * [admin_liberar_stock_reservado description]
	 * @param  string $id           [description]
	 * @param  string $id_detalle   [description]
	 * @param  [type] $cant_liberar [description]
	 * @return [type]               [description]
	 */
	public function admin_liberar_stock_reservado($venta_detalle_reserva_id , $venta_id, $venta_detalle_id = null , $venta_detalle_producto_id = null )
	{
		// * Cuando envian "venta_detalle_id" "venta_detalle_producto_id" es para eliminar todas las reservas del producto
		// * Cuando se libera una reserva se elimina el registro en VentaDetallesReserva
		$venta_detalle_reserva_ids=[];
		if (!is_null($venta_detalle_id) && !is_null($venta_detalle_id)) {
			$venta_detalle_reserva_ids = Hash::extract(ClassRegistry::init('VentaDetallesReserva')->find('all',[
				'conditions' => [
					'VentaDetallesReserva.venta_detalle_producto_id' => $venta_detalle_producto_id,
					'VentaDetallesReserva.venta_detalle_id' 	 	 => $venta_detalle_id
				],
				'fields'=> 'VentaDetallesReserva.id'
			]), "{n}.VentaDetallesReserva.id") ;
		}else{
			$venta_detalle_reserva_ids[] = $venta_detalle_reserva_id;
		}

		$liberar = 0;
		$result  = [];
	
		foreach ($venta_detalle_reserva_ids as $id) {
			ClassRegistry::init('VentaDetallesReserva')->id = $id;
			ClassRegistry::init('VentaDetalleProducto')->id = ClassRegistry::init('VentaDetallesReserva')->field('venta_detalle_producto_id');
			$liberar = $liberar + $this->Venta->liberar_reserva_stock_producto($id);
			
		}
		if ( $liberar > 0) {
			// * Preparamos los embalajes
			$this->WarehouseNodriza->procesar_embalajes($venta_id);
		}

		if ($liberar == 1) {
			$result['mesaje'] = ClassRegistry::init('VentaDetalleProducto')->field('codigo_proveedor') . ' - Cant liberada: ' . $liberar  . ' unidad.';
			$result['tipo']   = 'success';
		}elseif($liberar > 1) {
			$result['mesaje'] = ClassRegistry::init('VentaDetalleProducto')->field('codigo_proveedor') . ' - Cant liberada: ' . $liberar  . ' unidades.';
			$result['tipo']   = 'success';
		}else{
			$result['mesaje'] = ClassRegistry::init('VentaDetalleProducto')->field('codigo_proveedor') . ' - Cant liberada: ' . $liberar  . ' unidades.';
			$result['tipo']   = 'warning';
		}
		
		$this->Session->setFlash($result['mesaje'], null, array(), $result['tipo']);

		$this->redirect($this->referer('/', true));

	}


	public function reservar_stock_detalle($id_detalle)
	{
		$cant = $this->Venta->reservar_stock_producto($id_detalle);
		if ( $cant > 0) {
			// * Se sigue misma logica de instanciar metodo que hay en metodo "reservar_stock_producto"
			$this->WarehouseNodriza->procesar_embalajes(ClassRegistry::init('VentaDetalle')->Venta_id($id_detalle));
		}
		return $cant;
	}


	/**
	 * [admin_enviar_email_estado description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_enviar_email_estado($id = null)
	{	
		$notificar = $this->notificar_cambio_estado($id);

		if ($notificar) {
			$this->Session->setFlash('Email enviado con éxito', null, array(), 'success');
		}else{
			$this->Session->setFlash('Error al enviar el email.', null, array(), 'danger');
		}

		$refer_url = $this->referer('/', true);

		$this->redirect($refer_url);
	}


	public function admin_notificar_llegada_productos($ids)
	{

		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
			'mandrill_apikey', 'nombre'
		));

		$detalles = ClassRegistry::init('VentaDetalle')->find('all', array(
			'conditions' => array(
				'VentaDetalle.id' => $ids
			),
			'contain' => array(
				'Venta' => array(
					'VentaCliente' => array(
						'fields' => array('VentaCliente.nombre', 'VentaCliente.email')
					),
					'fields' => array('Venta.id', 'Venta.referencia')
				),
				'VentaDetalleProducto' => array(
					'fields' => array(
						'VentaDetalleProducto.nombre', 'VentaDetalleProducto.codigo_proveedor'
					)
				)
			)
		));


		/**
		 * Clases requeridas
		 */
		$this->View           = new View();
		$this->View->viewPath = 'Ventas' . DS . 'html';
		$this->View->layout   = 'backend' . DS . 'emails';
		
		$this->View->set(compact('detalles'));
		
		$html = $this->View->render('notificar_llegada_producto_agendado');

		$mandrill_apikey = $tienda['Tienda']['mandrill_apikey'];

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		$asunto = '[Nodriza Spa-'.rand(100,10000).'] Recordatorio de llegada de productos';
		
		if (Configure::read('ambiente') == 'dev') {
			$asunto = '[Nodriza Spa-'.rand(100,10000).'-DEV] Recordatorio de llegada de productos';
		}

		$remitente = array(
			'email' => 'no-reply@nodriza.cl',
			'nombre' => 'Nodriza Spa'
		);

		$emails = ClassRegistry::init('Administrador')->obtener_email_por_tipo_notificacion('ventas');

		$destinatarios = array();

		foreach ($emails as $im => $e) {
			$destinatarios[$im]['email'] = $e;
		}
		
		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);

	}

	/**
	 * Permite decontar desde bodega la cantidad de productos solicitadas por una venta.
	 * @return [type] [description]
	 */
	// ! Metodo obsoleto y en desuzo
	public function admin_procesar_ventas($id = null)
	{	

		if ( ! $this->Venta->exists($id) ) {
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}
	
		$this->Session->setFlash('Método inactivo temporalmente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));

		if ($this->request->is('post') || $this->request->is('put')) {
			

			$aceptados = array();
			$erorres   = array();

			foreach ($this->request->data['VentaDetalle'] as $idd => $detalle) {

				# vemos la cantidad de existencia que hay en bodega principal.
				$enBodega = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodega($detalle['venta_detalle_producto_id'], null, true);
				$enBodegas = ClassRegistry::init('Bodega')->obtenerCantidadProductoBodegas($detalle['venta_detalle_producto_id'], true);
					
				# Guardar resultados del detalle
				if ($enBodega < $detalle['cantidad_entregar']) {

					$errores[] = 'Item ' . ClassRegistry::init('VentaDetalleProducto')->field('nombre', $detalle['venta_detalle_producto_id']) . ' no puede ser retirado: Stock bodega principal ('.$enBodega.') - Stock global ('.$enBodegas.') - Vendidos ('.$detalle['cantidad_entregar'].')';
					continue;

				}elseif ($detalle['cantidad_entregar'] > 0) {

					if (ClassRegistry::init('Bodega')->crearSalidaBodega($detalle['venta_detalle_producto_id'], null, $detalle['cantidad_entregar'], null, 'VT', null, $id)) {

						$aceptados[] = 'Item ' . ClassRegistry::init('VentaDetalleProducto')->field('nombre', $detalle['venta_detalle_producto_id']) . ': Se descontaron ' . $detalle['cantidad_entregar'] . ' items de bodega principal';

						# se obtiene la cantidad reservada de éste producto para ésta venta.
						$cantidad_reservada = ClassRegistry::init('VentaDetalleProducto')->obtener_cantidad_reservada($detalle['venta_detalle_producto_id'], $id);
						
						# Actualizamos los campos
						$this->request->data['VentaDetalle'][$idd]['cantidad_pendiente_entrega'] = $detalle['cantidad'] - $detalle['cantidad_entregar'];
						$this->request->data['VentaDetalle'][$idd]['cantidad_reservada']         = $cantidad_reservada - $detalle['cantidad_entregar'];
						$this->request->data['VentaDetalle'][$idd]['cantidad_entregada'] 		 = $detalle['cantidad_entregar'];

						# si no quedan pendientes se marca como completado
						if (!$this->request->data['VentaDetalle'][$idd]['cantidad_pendiente_entrega']) {
							$this->request->data['VentaDetalle'][$idd]['completo']         = 1;
							$this->request->data['VentaDetalle'][$idd]['fecha_completado'] = date('Y-m-d H:i:s');
						}

					}else{
						$errores[] = 'Item ' . ClassRegistry::init('VentaDetalleProducto')->field('nombre', $detalle['venta_detalle_producto_id']) . ' no puede ser retirado: Stock bodega principal ('.$enBodega.') - Stock global ('.$enBodegas.') - Vendidos ('.$detalle['cantidad_entregar'].')';
					}
				}else{
					$this->request->data['VentaDetalle'][$idd]['cantidad_pendiente_entrega'] = $detalle['cantidad'];
				}
			}
			
			# Sub estados de la venta
			if (array_sum(Hash::extract($this->request->data['VentaDetalle'], '{n}.cantidad_pendiente_entrega')) > 0 ) {
				$this->request->data['Venta']['subestado_oc'] = 'parcialmente_entregado';
				$this->Session->setFlash('La venta se ha marcado como parcialmente entregado. Se recordará vía email la reposición del/los productos faltantes.', null, array(), 'warning');
			}else{
				$this->request->data['Venta']['subestado_oc'] = 'entregado';
			}


			if (!empty($aceptados)) {
				$this->Session->setFlash($this->crearAlertaUl($aceptados, 'Correcto'), null, array(), 'success');
			}

			if (!empty($errores)) {
				$this->Session->setFlash($this->crearAlertaUl($errores, 'Errores'), null, array(), 'danger');
			}

			
			if ($this->Venta->saveAll($this->request->data, array('callbacks' => false))) {
				$this->redirect(array('action' => 'index'));
			}
			

		}else {

			$this->request->data = $this->Venta->find('first', array(
				'conditions' => array(
					'Venta.id' => $id,
				),
				'contain' => array(
					'VentaDetalle' => array(
						'VentaDetalleProducto' => array(
							'Bodega'
						)
					),
					'VentaEstado' => array(
						'fields' => array(
							'VentaEstado.id', 'VentaEstado.permitir_retiro_oc', 'VentaEstado.nombre'
						)
					)
				),
				'fields' => array(
					'Venta.id'
				)
			));

		}


		BreadcrumbComponent::add('Ventas ', '/ventas');
		BreadcrumbComponent::add('Retirar productos ');

		

	} 


	/**
	 * Genera un paquete con los items selecionados
	 * @param  [type] $id  Venta
	 * @return [type]     [description]
	 */
	public function admin_linio_generar_paquete($id)
	{	
		if ($this->request->is('POST') || $this->request->is('PUT')) {

			$venta = $this->Venta->find(
				'first',
				array(
					'conditions' => array(
						'Venta.id' => $id,
						'Venta.paquete_generado' => false
					),
					'contain' => array(
						
						'Marketplace' => array(
							'fields' => array(
								'Marketplace.id', 'Marketplace.nombre', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id',
								'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key',
								'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.access_token'
							)
						)
					),
					'fields' => array(
						'Venta.id', 'Venta.id_externo', 'Venta.paquete_generado'
					)
				)
			);
			
			# cliente Linio
			$this->Linio->crearCliente( $venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'] );

			$orderItemIds     = json_decode($this->request->data['Venta']['OrderItemIds']); // Please change the set of Order Item IDs for Your system.
			$deliveryType     = 'dropship';
			$shipmentProvider = $this->request->data['Venta']['ShippingProvider'];

			$response = $this->Linio->linio_paquete_embalado($orderItemIds, $deliveryType, $shipmentProvider);

			if ($response) {
			    
				$tipodocumentos = array(
					'invoice', 'exportInvoice', 'shippingLabel', 'shippingParcel', 'carrierManifest', 'serialNumber'
				);

				$documentos = array();

				foreach ($tipodocumentos as $tdocumento) {
					$documentos[] = $this->Linio->linio_obtener_documentos($orderItemIds, $tdocumento);
				}

				$this->Venta->id = $id;
				
				$this->Venta->saveField('Venta.documento', json_encode($documentos));
				$this->Session->setFlash('Paquete creado con éxito.', null, array(), 'success');
				$this->redirect(array('action' => 'view', $id));


			} else {

			    $this->Session->setFlash('No fue posible cambiar el estado del pedido.', null, array(), 'danger');
				$this->redirect(array('action' => 'view', $id));
			
			}	
		}
	}



	/**
	 * Obtiene la etiqueta segun el tipo y canal de venta
	 * @param  [type] $id   [description]
	 * @param  string $tipo [description]
	 * @return [type]       [description]
	 */
	public function admin_obtener_etiqueta($id, $tipo = '') 
	{
		$venta = $this->request->data = $this->Venta->obtener_venta_por_id($id);
		
		# Linio
		if ($venta['Marketplace']['marketplace_tipo_id'] == 1) {
			
			# cliente Linio
			$this->Linio->crearCliente( $venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'] );

			$detallesVenta = $this->Linio->linio_obtener_venta($venta['Venta']['id_externo'], true);

			$documento = $this->Linio->linio_obtener_documentos(Hash::extract($detallesVenta['Products'], '{n}.OrderItemId'), $tipo);

			if (!empty($documento)) {
				$this->ver_documento($documento['mimeType'], $documento['pdf']);
			}else{
				exit;
			}
			
		}

		# MEli
		if ($venta['Marketplace']['marketplace_tipo_id'] == 2) {

			$this->MeliMarketplace->crearCliente($venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token']);
			
			$this->MeliMarketplace->mercadolibre_conectar('', $venta['Marketplace']);

			// Detalles de la venta externa
			$venta['VentaExterna'] = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($venta['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);

			if (isset($venta['VentaExterna']['shipping']['id'])) {

				$envio = $this->MeliMarketplace->mercadolibre_obtener_envio($venta['VentaExterna']['shipping']['id']);
				
				$this->MeliMarketplace->mercadolibre_obtener_etiqueta_envio($envio);	
			}
			
		}	

		# PRestashop
		if (!$venta['Venta']['marketplace_id'] 
		&& !empty($venta['Venta']['id_externo']) 
		&& !$venta['Venta']['venta_manual']) {

			# Cliente Prestashop
			$this->Prestashop->crearCliente( $venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'] );	

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Prestashop->prestashop_obtener_venta($venta['Venta']['id_externo']);

			if (!empty($venta['VentaExterna'])) {					

				$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

				$venta['VentaMensaje'] = $this->Prestashop->prestashop_obtener_venta_mensajes($venta['Venta']['id_externo']);

				$direccionEnvio       = $this->Prestashop->prestashop_obtener_venta_direccion($venta['VentaExterna']['id_address_delivery']);
				$direccionFacturacion = $this->Prestashop->prestashop_obtener_venta_direccion($venta['VentaExterna']['id_address_invoice']);
				
				// Detalles de envio
				$venta['Envio'][0] = array(
					'id'                      => $direccionEnvio['address']['id'],
					'tipo'                    => 'Dir. despacho',
					'estado'                  => (!$direccionEnvio['address']['deleted']) ? 'activo' : 'eliminada',
					'direccion_envio'         => @sprintf('%s %s, %s', $direccionEnvio['address']['address1'], implode(',', $direccionEnvio['address']['address2']), $direccionEnvio['address']['city']),
					'nombre_receptor'         => @sprintf('%s %s', $direccionEnvio['address']['firstname'], $direccionEnvio['address']['lastname']),
					'fono_receptor'           => '',
					'producto'                => null,
					'cantidad'                => 1, // No especifica
					'costo'                   => $venta['VentaExterna']['total_shipping_tax_incl'],
					'fecha_entrega_estimada'  => 'No especificado',
					'comentario'              => implode(',', $direccionEnvio['address']['other']),
					'mostrar_etiqueta'        => true,
					'paquete' 				  => false
				);

				if (is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) {
					if (is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
						$venta['Envio'][0]['fono_receptor'] = sprintf('Cel: %s Tel: %s', implode(',', $direccionEnvio['address']['phone_mobile']), implode(',', $direccionEnvio['address']['phone']) );
					}else{
						$venta['Envio'][0]['fono_receptor'] = sprintf('Cel: %s Tel: %s', implode(',', $direccionEnvio['address']['phone_mobile']), $direccionEnvio['address']['phone'] );
					}  
				}else{
					if (is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
						$venta['Envio'][0]['fono_receptor'] = sprintf('Cel: %s Tel: %s', implode(',', $direccionEnvio['address']['phone_mobile']), implode(',', $direccionEnvio['address']['phone']) );
					}else{
						$venta['Envio'][0]['fono_receptor'] = sprintf('Cel: %s Tel: %s', implode(',', $direccionEnvio['address']['phone_mobile']), $direccionEnvio['address']['phone'] );
					}
				}  
			}
			
			$this->obtener_etiqueta_envio_default($venta);

		}
	}


	/**
	 * Busca y genera todos los PDFS disponibles para una venta: Transporte, etiqueta interna de venta, DTes.
	 * @param  int  		$id       id de la venta
	 * @param  boolean 		$ajax     es una peticion ajax o no
	 * @param  boolean 		$crearDte si la venta no tiene DTE generado, lo crea siempre y cuando tenga los datos de facturación cargados.
	 * @return url del documento
	 */
	public function admin_generar_documentos($id, $ajax = false, $crearDte = false)
	{	
		# Toda la información de la venta
		$venta = $this->Venta->obtener_venta_por_id($id);

		# Variable que contendrá los documentos
		$archivos = array();
		
		# Linio
		if ($venta['Marketplace']['marketplace_tipo_id'] == 1) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Linio = $this->Components->load('Linio');
			}
			# cliente Linio
			$this->Linio->crearCliente( $venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'] );

			//$mensajes =  $this->Linio->linio_obtener_venta_mensajes($venta, $ConexionLinio);

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Linio->linio_obtener_venta($venta['Venta']['id_externo'], true);

			// Datos d facturacion
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);

			// Se define transportista
			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

			// Detalles de envio
			$venta['Envio'][0] = array(
				'id'                      => null,
				'tipo'                    => null,
				'estado'                  => null,
				'direccion_envio'         => sprintf('%s, %s, %s', $venta['VentaExterna']['AddressShipping']['Address1'], $venta['VentaExterna']['AddressShipping']['Address2'], $venta['VentaExterna']['AddressShipping']['City']),
				'nombre_receptor'         => sprintf('%s %s', $venta['VentaExterna']['AddressShipping']['FirstName'], $venta['VentaExterna']['AddressShipping']['LastName']),
				'fono_receptor'           => $venta['VentaExterna']['AddressShipping']['Phone'],
				'producto'                => null,
				'cantidad'                => 1, // No especifica
				'costo'                   => 0,
				'fecha_entrega_estimada'  => $venta['VentaExterna']['PromisedShippingTime'],
				'comentario'              => '',
				'mostrar_etiqueta'        => false,
				'paquete' 				  => false
			);

			$documentoEnvio   = $this->Linio->linio_obtener_documentos(Hash::extract($venta['VentaExterna']['Products'], '{n}.OrderItemId'), 'shippingParcel');
			$documentoInvoice = $this->Linio->linio_obtener_documentos(Hash::extract($venta['VentaExterna']['Products'], '{n}.OrderItemId'), 'invoice');
			
			$rutaAbsoluta = APP . 'webroot' . DS. 'Venta' . DS . $id . DS;
			$rutaPublica  =  Router::url('/', true) . 'Venta/' . $id . '/';

			# Invoice Linio
			if (!empty($documentoInvoice)) {

				$invoice = $this->generar_pdf(base64_decode($documentoInvoice['pdf']), $id, 'invoice');
		 		
		 		if (!empty($invoice)) {
		 			$archivos[] = $invoice['path'];
		 		}

			}

			# Doc tranportista linio
			if (!empty($documentoEnvio)) {

				$archivoPdfEnvio = 'transporte' . rand() . '.pdf';

				$documentoEnvioPdfs = $this->guardar_pdf_base64($documentoEnvio['pdf'], $rutaAbsoluta, $rutaPublica, $archivoPdfEnvio);
		 		
		 		if (!empty($documentoEnvioPdfs)) {
		 			$archivos[] = $documentoEnvioPdfs['path'];
		 		}

			}
			
		}

		# MEli
		if ($venta['Marketplace']['marketplace_tipo_id'] == 2) {


			$this->MeliMarketplace->crearCliente( $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token'] );
			$this->MeliMarketplace->mercadolibre_conectar('', $venta['Marketplace']);

			$mensajes = $this->MeliMarketplace->mercadolibre_obtener_mensajes($venta['Marketplace']['access_token'], $venta['Venta']['id_externo']);

			foreach ($mensajes as $mensaje) {
				$data = array();
				$data['mensaje'] = $this->removeEmoji($mensaje['text']['plain']);
				$data['fecha'] = CakeTime::format($mensaje['date'], '%d-%m-%Y %H:%M:%S');
				$data['asunto'] = (empty($mensaje['subject'])) ? 'Sin asunto' : $mensaje['subject'] ;
				$venta['VentaMensaje'][] = $data;
			}

			// Detalles de la venta externa
			$venta['VentaExterna'] = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($venta['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);

			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;
			
			// Datos d facturacion
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);


			if (isset($venta['VentaExterna']['shipping']['id'])) {

				$envio = $this->MeliMarketplace->mercadolibre_obtener_envio($venta['VentaExterna']['shipping']['id']);

				// Detalles de envio
				$direccion_envio = '';
				$nombre_receptor = '';
				$fono_receptor   = '';
				$comentario      = '';

				if (isset($envio['receiver_address']['address_line'])
					&& isset($envio['receiver_address']['city']['name'])) {
					$direccion_envio = sprintf('%s, %s', $envio['receiver_address']['address_line'], $envio['receiver_address']['city']['name']);
				}

				if (isset($envio['receiver_address']['receiver_name'])) {
					$nombre_receptor = $envio['receiver_address']['receiver_name'];
				}

				if (isset($envio['receiver_address']['receiver_phone'])) {
					$fono_receptor = $envio['receiver_address']['receiver_phone'];
				}

				if (isset($envio['receiver_address']['comment'])) {
					$comentario = $envio['receiver_address']['comment'];
				}

				
				$venta['Envio'][0] = array(
					'id'                      => $envio['id'],
					'tipo'                    => $envio['shipping_option']['name'],
					'estado'                  => $envio['status'],
					'direccion_envio'         => $direccion_envio,
					'nombre_receptor'         => $nombre_receptor,
					'fono_receptor'           => $fono_receptor,
					'producto'                => null,
					'cantidad'                => 1,
					'costo'                   => $envio['shipping_option']['cost'],
					'fecha_entrega_estimada'  => (isset($envio['shipping_option']['estimated_delivery_time'])) ? CakeTime::format($envio['shipping_option']['estimated_delivery_time']['date'], '%d-%m-%Y %H:%M:%S') : __('No especificado') ,
					'comentario'              => $comentario,
					'mostrar_etiqueta'        => ($envio['status'] == 'ready_to_ship') ? true : false,
					'paquete' 				  => false
				);	
				
			}

			$documentoEnvio = $this->MeliMarketplace->mercadolibre_obtener_etiqueta_envio($envio, 'Y');
			
			$rutaAbsoluta = APP . 'webroot' . DS. 'Venta' . DS . $id . DS;
			$rutaPublica  =  Router::url('/', true) . 'Venta/' . $id . '/';

			# Tranposrte Meli
			if (!empty($documentoEnvio)) {

				$archivoPdfEnvio = 'transporte' . rand() . '.pdf';

				$documentoEnvioPdfs = $this->guardar_pdf_base64($documentoEnvio, $rutaAbsoluta, $rutaPublica, $archivoPdfEnvio, false);
		 		
		 		if (!empty($documentoEnvioPdfs)) {
		 			$archivos[] = $documentoEnvioPdfs['path'];
		 		}

			}

		}	

		# Prestashop
		if (!$venta['Venta']['marketplace_id'] 
		&& !empty($venta['Venta']['id_externo']) 
		&& !$venta['Venta']['venta_manual']) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Prestashop = $this->Components->load('Prestashop');
			}
			# Cliente Prestashop
			$this->Prestashop->crearCliente( $venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'] );	

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Prestashop->prestashop_obtener_venta($venta['Venta']['id_externo']);		

			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

			$venta['VentaMensaje'] = $this->Prestashop->prestashop_obtener_venta_mensajes($venta['Venta']['id_externo']);

			$direccionEnvio       = $this->Prestashop->prestashop_obtener_venta_direccion($venta['VentaExterna']['id_address_delivery']);				

			// Detalles de envio
			$telefonosEnvio = '';
			
			if (is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) {
				$telefonosEnvio .= implode(' ', $direccionEnvio['address']['phone_mobile']);
			}

			if (!is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) {
				$telefonosEnvio .= ' ' . $direccionEnvio['address']['phone_mobile'];
			}


			if (is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
				$telefonosEnvio .= implode(' ', $direccionEnvio['address']['phone']);
			}

			if (!is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
				$telefonosEnvio .= ' ' . $direccionEnvio['address']['phone'];
			}
			
			// Detalles de envio
			$venta['Envio'][0] = array(
				'id'                      => $direccionEnvio['address']['id'],
				'tipo'                    => 'Dir. despacho',
				'estado'                  => (!$direccionEnvio['address']['deleted']) ? 'activo' : 'eliminada',
				'direccion_envio'         => @sprintf('%s %s, %s', $direccionEnvio['address']['address1'], implode(',', $direccionEnvio['address']['address2']), $direccionEnvio['address']['city']),
				'nombre_receptor'         => @sprintf('%s %s', $direccionEnvio['address']['firstname'], $direccionEnvio['address']['lastname']),
				'fono_receptor'           => $telefonosEnvio,
				'producto'                => null,
				'cantidad'                => 1, // No especifica
				'costo'                   => $venta['VentaExterna']['total_shipping_tax_incl'],
				'fecha_entrega_estimada'  => 'No especificado',
				'comentario'              => implode(',', $direccionEnvio['address']['other']),
				'mostrar_etiqueta'        => true,
				'paquete' 				  => false
			);

			# Datos de facturación para compras por Prestashop
			$this->Toolmania = $this->Components->load('Toolmania');
			$this->Toolmania::$api_url = $venta['Tienda']['apiurl_prestashop'];
			
			#Obtener información webpay si es necesario
			#$webpay                      = $this->Toolmania->obtenerWebpayInfo($this->request->data['Orden']['id_cart'], $this->Session->read('Tienda.apikey_prestashop'));
			$documentos                  = $this->Toolmania->obtenerDocumento($venta['Venta']['id_externo'], null, $venta['Tienda']['apikey_prestashop']);
			
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);
			
			if (!empty($documentos['content'])) {

				$tipoDoc = ($documentos['content'][0]['boleta']) ? 39 : 33;

				$facturacion = array(
					'tipo_documento'        => $tipoDoc,
					'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[$tipoDoc],
					'rut_receptor'          => $documentos['content'][0]['rut'],
					'razon_social_receptor' => $documentos['content'][0]['empresa'],
					'giro_receptor'         => $documentos['content'][0]['giro'],
					'direccion_receptor'    => $documentos['content'][0]['calle']
				);
				# Para la consola se carga el componente on the fly!
				if ($this->shell) {
					$this->LibreDte = $this->Components->load('LibreDte');
				}
				// Obtenemos la información del contribuyente desde el SII
				$this->LibreDte->crearCliente($venta['Tienda']['facturacion_apikey']);
		
				$info = $this->LibreDte->obtenerContribuyente($this->rutSinDv($documentos['content'][0]['rut']));
				
				// Agregamos comuna
				if (isset($info['comuna_glosa'])) {
					$facturacion['comuna_receptor'] = $info['comuna_glosa'];
				}

				// Agregamos razon social
				if (empty($documentos['content'][0]['empresa']) && isset($info['razon_social'])) {
					$facturacion['razon_social_receptor'] = $info['razon_social'];
				}

				// Agregamos giro
				if (empty($documentos['content'][0]['giro']) && isset($info['giro'])) {
					$facturacion['giro_receptor'] = $info['giro'];
				}	

				// Agregamos direccon
				if (empty($documentos['content'][0]['direccion_receptor']) && isset($info['direccion'])) {
					$facturacion['direccion_receptor'] = $info['direccion'];
				}	
				
				
				# Guardamos el rut de la persona
				ClassRegistry::init('VentaCliente')->id = $venta['VentaCliente']['id'];
				ClassRegistry::init('VentaCliente')->saveField('rut', $documentos['content'][0]['rut']);

				$this->request->data['VentaCliente']['rut'] = $documentos['content'][0]['rut'];

				$venta['VentaExterna']['facturacion'] = array_replace_recursive($venta['VentaExterna']['facturacion'], $facturacion);
			}
			
			
		}

		/*$url_etiqueta_qr = $this->obtener_codigo_qr_url($venta['Venta']['id']);
				
		$archivos[] = $url_etiqueta_qr['path'];*/

		if ($crearDte) {
			$resultDte = $this->crearDteAutomatico($venta);

			if (!empty($resultDte['success'])) {

				$nwDte = ClassRegistry::init('Dte')->find('all', array(
					'conditions' => array(
						'Dte.venta_id' => $venta['Venta']['id']
					),
					'fields' => array(
						'Dte.id', 'Dte.folio', 'Dte.tipo_documento', 'Dte.rut_receptor', 'Dte.razon_social_receptor', 'Dte.giro_receptor', 'Dte.neto', 'Dte.iva',
						'Dte.total', 'Dte.fecha', 'Dte.estado', 'Dte.venta_id', 'Dte.pdf', 'Dte.invalidado'
					),
					'order' => 'Dte.fecha DESC'
				));

				$venta['Dte'] = Hash::extract($nwDte, '{n}.Dte');
			}

		}

		# Obtenemos DTE
		if (!empty($venta['Dte'])) {
			$dtes = $this->obtener_dtes_pdf_venta($venta['Dte'], 2);
		
			foreach ($dtes as $dte) {
				$archivos[] = $dte['path'];
			}
		}

		$venta              = $this->preparar_venta($id);
		$url_etiqueta_envio = $this->obtener_etiqueta_envio_default_url($venta);
		$archivos[]         = $url_etiqueta_envio['path'];


		# Unimos todos los PDFS obtenidos
		if (!empty($archivos)) {
			
			$this->layoutPath = '';
			$this->layout = 'ajax';

			$pdf = $this->unir_documentos($archivos, $id);

			if ($ajax) {
				echo json_encode($pdf);
				exit;
			}

		}else{

			if ($ajax) {
				echo '';
				exit;
			}

			$this->Session->setFlash('No hay documentos para generar.', null, array(), 'warning');
			$this->redirect(array('action' => 'view', $id));
		}

	}


	/**
	 * Genera la etiqueta de envio: Transporte, etiqueta interna de venta, DTes.
	 * @param  int  		$id       id de la venta
	 * @param  boolean 		$ajax     es una peticion ajax o no
	 * @param  boolean 		$crearDte si la venta no tiene DTE generado, lo crea siempre y cuando tenga los datos de facturación cargados.
	 * @return url del documento
	 */
	public function admin_generar_etiqueta($id, $ajax = false)
	{	
		# Toda la información de la venta
		$venta = $this->preparar_venta($id);

		# Variable que contendrá los documentos
		$archivos = array();
		
		$url_etiqueta_envio = $this->obtener_etiqueta_envio_default_url($venta);
		
		$archivos[] = $url_etiqueta_envio['path'];

		# Unimos todos los PDFS obtenidos
		if (!empty($archivos)) {
			
			$this->layoutPath = '';
			$this->layout = 'ajax';

			$pdf = $this->unir_documentos($archivos, $id);

			if ($ajax) {
				echo json_encode($pdf);
				exit;
			}

		}else{

			if ($ajax) {
				echo '';
				exit;
			}

			$this->Session->setFlash('No hay documentos para generar.', null, array(), 'warning');
			$this->redirect(array('action' => 'view', $id));
		}

	}


	public function admin_generar_dte_etiqueta($id, $ajax = false)
	{	
		# Toda la información de la venta
		$venta = $this->preparar_venta($id);

		# Variable que contendrá los documentos
		$archivos = array();

		# Obtenemos DTE
		if (!empty($venta['Dte'])) {
			$dtes = $this->obtener_dtes_pdf_venta($venta['Dte'], 1);
		
			foreach ($dtes as $dte) {
				$archivos[] = $dte['path'];
			}
		}
		
		$url_etiqueta_envio = $this->obtener_etiqueta_envio_default_url($venta, 'vertical');
		
		$archivos[] = $url_etiqueta_envio['path'];
		
		# Unimos todos los PDFS obtenidos
		if (!empty($archivos)) {
			
			$this->layoutPath = '';
			$this->layout = 'ajax';

			$pdf = $this->unir_documentos($archivos, $id);

			if ($ajax) {
				echo json_encode($pdf);
				exit;
			}

		}else{

			if ($ajax) {
				echo '';
				exit;
			}

			$this->Session->setFlash('No hay documentos para generar.', null, array(), 'warning');
			$this->redirect(array('action' => 'view', $id));
		}

	}


	/**
	 * Metodo utilizado para saber si una venta tien o no generado ya un dte valido
	 * @param  [type] $id_venta
	 * @return json object
	 */
	public function admin_consultar_dte($id_venta)
	{
		$res['dte_generado'] = !DtesController::unicoDteValido($id_venta);
		echo json_encode($res);
		exit;
	}


	/**
	 * [guardar_pdf_base64 description]
	 * @param  string $data         [description]
	 * @param  string $rutaAbsoluta [description]
	 * @param  string $rutaPublica  [description]
	 * @param  [type] $nombre       [description]
	 * @return [type]               [description]
	 */
	public function guardar_pdf_base64($data = '', $rutaAbsoluta = '', $rutaPublica = '', $nombre, $base64 = true)
	{
 		# Creamos la ruta absoluta
 		if( !@mkdir($rutaAbsoluta, 0777, true) ) {
 			# Ya existe ruta
 		}

 		$rutaCompletaAbsoluta = $rutaAbsoluta . $nombre;
 		$rutaCompletaPublica  = $rutaPublica . $nombre;

 		if ($base64) {
 			$data = base64_decode($data);
 		}

 		# guardamos el PDF
		if (file_put_contents($rutaCompletaAbsoluta, $data) == E_WARNING) {
			return array();
		}else{
			return array('public' => $rutaCompletaPublica, 'path' => $rutaCompletaAbsoluta);
		}
	}


	/**
	 * [obtener_etiqueta_envio_default description]
	 * @param  array  $venta [description]
	 * @return [type]        [description]
	 */
	public function obtener_etiqueta_envio_default($venta = array())
	{	
		$logo = FULL_BASE_URL . '/webroot/img/Tienda/' . $venta['Tienda']['id'] . '/' . $venta['Tienda']['logo'] ;
		
		$this->layoutPath = 'pdf';
		$this->viewPath   = 'Ventas/';
		$this->autoRender = false;
		$this->output     = '';
		$this->layout 	  = 'default';
		$this->pdfConfig  = array(
			'orientation' => 'landscape',
			'download' => true,
			'filename' => rand() .'.pdf'
		);

		$this->set(compact('venta', 'logo'));
		$this->render('etiqueta_envio_default');
		#$html  = $vista->body();
		#$url   = $this->generar_pdf($html, $venta['Venta']['id'], 'transporte', 'landscape');
		
	}


	/**
	 * Genera la etiqueta de envio default intenra y retorna la url púbica y absoluta del archivo.
	 * @param  array  $venta [description]
	 * @param  string $orientacion horizontal/vertical
	 * @return [type]        [description]
	 */
	public function obtener_etiqueta_envio_default_url($venta = array(), $orientacion = 'horizontal')
	{	
		# Dejamos solo DTES validos
		if (!empty($venta['Dte'])) {
			$venta['Dte'] = ClassRegistry::init('Dte')->preparar_dte_venta_valido($venta['Dte']);
		}

		# Creamos la etiqueta de despacho interna
		$logo = FULL_BASE_URL . '/webroot/img/Tienda/' . $venta['Tienda']['id'] . '/' . $venta['Tienda']['logo'] ;
		
		$this->View           = new View();
		$this->View->layoutPath = 'pdf';
		$this->View->viewPath   = 'Ventas/pdf';
		$this->View->output     = '';
		$this->View->layout     = 'default';
		
		$url    = Router::url( sprintf('/api/ventas/%d.json', $venta['Venta']['id']), true);
		$tamano = '500x500';

		$this->View->set(compact('venta', 'logo', 'url', 'tamano'));

		if ($orientacion == 'horizontal') {
			$vista = $this->View->render('etiqueta_envio_default');	
		}
		
		if ($orientacion == 'vertical') {
			$vista = $this->View->render('etiqueta_envio_default_vertical');	
		}

		if ($orientacion == 'horizontal') {
			$url   = $this->generar_pdf($vista, $venta['Venta']['id'], 'transporte', 'landscape', '10x15');
		}

		if ($orientacion == 'vertical') {
			$url   = $this->generar_pdf($vista, $venta['Venta']['id'], 'transporte', 'portrait', 'nodriza');
		}

		return $url;
	}


	/**
	 * Genera Código QR identificador de la venta
	 * @param  [type] $id_venta [description]
	 * @return [type]           [description]
	 */
	public function obtener_codigo_qr_url($id_venta)
	{	
		$this->layoutPath = 'pdf';
		$this->viewPath   = 'Ventas/pdf';
		$this->autoRender = false;
		$this->output     = '';
		$this->layout 	  = 'default';

		$url = Router::url( sprintf('/api/ventas/%d.json', $id_venta), true);
		$tamano = '500x500';

		$this->set(compact('url', 'tamano'));

		$vista = $this->render('qr');
		$html  = $vista->body();

		$url   = $this->generar_pdf($html, $id_venta, 'etiquetaqr');

		return $url;
	}


	/**
	 * [obtener_dtes_pdf_venta description]
	 * @param  array  $dtes [description]
	 * @return [type]       [description]
	 */
	public function obtener_dtes_pdf_venta($dtes = array(), $copias = 1)
	{	
		$rutas = array();

		foreach ($dtes as $i => $dte) {

			if ($dte['invalidado']) {
				continue;
			}

			if ($dte['estado'] != 'dte_real_emitido') {
				continue;
			}

			# solo boleta o factura no invalidada
			if ($dte['tipo_documento'] == 39 || $dte['tipo_documento'] == 33) {

				for ($ix=0; $ix < $copias; $ix++) { 
					# Ruta absoluta PDF DTE
					$rutas[$i.$ix]['path'] = APP . 'webroot' . DS. 'Dte' . DS . $dte['venta_id'] . DS . $dte['id'] . DS . $dte['pdf'];
					$rutas[$i.$ix]['public'] = Router::url('/', true) . 'Dte/' . $dte['venta_id'] . '/' . $dte['id'] . '/'.  $dte['pdf'];
				}
			}
		}

		return $rutas;
	}


	/**
	 * [unir_documentos description]
	 * @param  array  $archivos [description]
	 * @param  string $venta_id [description]
	 * @return [type]           [description]
	 */
	public function unir_documentos($archivos = array(), $venta_id = '')
	{
		$pdfs       = array();
		$limite     = 500;
		$lote = 0;
		$ii = 1;

		foreach ($archivos as $i => $archivo) {

			if (file_exists($archivo)) {
				$pdfs[$lote][$ii] = $archivo;

				if ($ii%$limite == 0) {
					$lote++;
				}	
			}

			$ii++;
		}

		if (!is_dir(APP . 'webroot' . DS. 'Venta' . DS . $venta_id)) {
			@mkdir(APP . 'webroot' . DS. 'Venta' . DS . $venta_id, 0775);
		}

		# Se procesan por Lotes de 500 documentos para no volcar la memoria
		foreach ($pdfs as $ip => $lote) {
			$pdf = new PDFMerger;
			foreach ($lote as $id => $document) {
				$pdf->addPDF($document, 'all');	
			}
			try {
				
				$pdfname = 'documentos-' . date('YmdHis') .'.pdf';

				$res = $pdf->merge('file', APP . 'webroot' . DS. 'Venta' . DS . $venta_id . DS . $pdfname);
				if ($res) {
					$resultados['result'][]['document'] = Router::url('/', true) . 'Venta/' . $venta_id . '/' . $pdfname;
				}

			} catch (Exception $e) {
				$resultados['errors']['messages'][] = $e->getMessage();
			}
		}

		return $resultados;
	}


	/**
	 * Muestra en pantalla el contenido segun su tipo
	 * @param  string $mimetype [description]
	 * @param  string $cuerpo   [description]
	 * @return [type]           [description]
	 */
	public function ver_documento($mimetype = '', $cuerpo = '')
	{
		header('Content-type:' . $mimetype);

		switch ($mimetype) {
			case 'application/pdf':
				header('Content-Disposition:inline;filename="'.rand().'.pdf"');
				break;
			case 'application/zip':
				header('Content-Disposition:attachment;filename="'.rand().'.zip"');
				break;
		}

		echo base64_decode($cuerpo);
		exit;
		
	}

	/**
	 * Crea un archivo PDF con el contenido HTML pasado por argumento
	 * en la ruta deifinida para ventas
	 * @param  string $html        [description]
	 * @param  string $venta_id    [description]
	 * @param  string $nombre      [description]
	 * @param  string $orientacion [description]
	 * @return [type]              [description]
	 */
	public function generar_pdf($html = '', $venta_id = '', $nombre = '', $orientacion = 'potrait', $tamano = 'A4') {

		$nombre = $nombre . rand();
		$rutaAbsoluta = APP . 'webroot' . DS . 'Pdf' . DS . 'Venta' . DS . $venta_id . DS . $nombre . '.pdf';

		try {
			$this->CakePdf = new CakePdf();
			$this->CakePdf->orientation($orientacion);
			$this->CakePdf->pageSize($tamano);
			@$this->CakePdf->write($rutaAbsoluta, true, $html);	
		} catch (Exception $e) { 
			return array();
		}

		# Ruta para guardar en la Base de datos
		$archivo = Router::url('/', true) . 'Pdf/Venta/' . $venta_id . '/' . $nombre . '.pdf';

		return array('public' => $archivo, 'path' => $rutaAbsoluta);

	}

	
	/**
	 * Obtiene las ventas retrasadas y las notifica en caso de que corresponda
	 */
	public function notificar_retraso_ventas()
	{	
		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
			'notificacion_retraso_venta_dias', 'notificacion_retraso_venta_limite', 'notificacion_retraso_venta', 'notificacion_retraso_venta_repetir'
		));

		if (!$tienda['Tienda']['notificacion_retraso_venta'])
			return false;

		$ventas = $this->Venta->obtener_ventas_retrasadas($tienda['Tienda']['notificacion_retraso_venta_dias'], $tienda['Tienda']['notificacion_retraso_venta_limite']);

		
		$ventas_notificadas = array();

		foreach ($ventas as $venta)
		{	
			# Tenemos hasta 5 notificaciones
			if ($venta['Venta']['notificado_retraso_cliente'] == 5)
			{	
				continue;
			}
			
			$fechaVenta = new DateTime($venta['Venta']['fecha_venta']);
			$hoy = new DateTime(date('Y-m-d H:i:s'));
			
			# Verificamos retraso de la venta
			$retraso = $fechaVenta->diff($hoy);
			
			$template = '';
			
			# Primera notificación
			if ($retraso->days == $tienda['Tienda']['notificacion_retraso_venta_dias']) {
				$template = 'notificar_retraso_venta_cliente_0';
			}
			
			# Repeticion de notificación
			else if ($retraso->days % $tienda['Tienda']['notificacion_retraso_venta_repetir'] == 0)
			{
				$template = sprintf('notificar_retraso_venta_cliente_%d', $venta['Venta']['notificado_retraso_cliente']);
			}
			
			# Notificamos al cliente
			$notificar = $this->notificar_retraso_venta($venta, $template);
			
			$notificaciones = '';

			if ($notificar)
			{
				$notificaciones = $venta['Venta']['notificado_retraso_cliente'] + 1;
			}
			else
			{
				continue;
			}

			# Almacenamos resultado de la notificación de la venta
			$ventas_notificadas[] = array(
				'Venta' => array(
					'id' => $venta['Venta']['id'],
					'notificado_retraso_cliente' => $notificaciones,
					'notificado_retraso_cliente_fecha' => date('Y-m-d')
				)
			);
		}

		# Guardamos las ventas notificadas
		if (!empty($ventas_notificadas))
		{
			$this->Venta->saveMany($ventas_notificadas);
		}

		return $ventas_notificadas;
	}

	/**
	 * Permite notificar vía email el retraso de una venta
	 * @param $id int ID de la venta
	 * @return Bool
	 */
	public function notificar_retraso_venta($venta, $template = 'notificar_retraso_venta_cliente_1')
	{	
		$url = obtener_url_base();
		
		/**
		 * Clases requeridas
		 */
		$this->View           = new View();
		$this->View->viewPath = 'Ventas' . DS . 'emails';
		$this->View->layout   = 'backend' . DS . 'emails';

		/**
		 * Correo a cliente
		 */
		$this->View->set(compact('venta', 'url'));
		$html = $this->View->render($template);
	
		$mandrill_apikey = $venta['Tienda']['mandrill_apikey'];

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		$asunto = '['.$venta['Tienda']['nombre'].'] Venta #' . $venta['Venta']['id'] . ' - Información importante';
		
		if (Configure::read('ambiente') == 'dev') {
			$asunto = '['.$venta['Tienda']['nombre'].'-DEV] Venta #' . $venta['Venta']['id'] . ' - Información importante';
		}

		$remitente = array(
			'email' => 'no-reply@nodriza.cl',
			'nombre' => 'Ventas ' . $venta['Tienda']['nombre']
		);

		$destinatarios = array(
			array(
				'email' => trim($venta['VentaCliente']['email']),
				'name' => $venta['VentaCliente']['nombre'] . ' ' . $venta['VentaCliente']['apellido']
			)
		);
		
		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
	}


	/**
	 * Notifica al cliente sobre el cambi de estado de su pedido o venta
	 * @param  [type] $id_venta            [description]
	 * @param  [type] $plantillaEmail      [description]
	 * @param  string $nombre_estado_nuevo [description]
	 * @return [type]                      [description]
	 */
	public function notificar_cambio_estado($id_venta = null, $plantillaEmail = null, $nombre_estado_nuevo = '')
	{	
		$venta = $this->Venta->obtener_venta_por_id($id_venta);
		
		$plantillaDefault = @$venta['VentaEstado']['VentaEstadoCategoria']['plantilla'];
		$estadoDefault    = @$venta['VentaEstado']['nombre'];

		if (empty($plantillaEmail) && !empty($plantillaDefault)) {
			$plantillaEmail = $plantillaDefault;
		}

		if (empty($plantillaEmail) && empty($plantillaDefault)) {
			return false;
		}

		if (empty($nombre_estado_nuevo) && !empty($estadoDefault)) {
			$nombre_estado_nuevo = $estadoDefault;
		}

		if (empty($nombre_estado_nuevo) && empty($estadoDefault)) {
			return false;
		}

		/**
		 * Clases requeridas
		 */
		$this->View           = new View();
		$this->View->viewPath = 'VentaEstados' . DS . 'emails';
		$this->View->layout   = 'backend' . DS . 'emails';
		
		/**
		 * QR
		 */
		$urlQr = Router::url( sprintf('/api/ventas/%d.json', $id_venta), true);
		$tamanoQr = '500x500';

		/**
		 * Correo a ventas
		 */
		$this->View->set(compact('venta', 'urlQr', 'tamanoQr'));
		$html = $this->View->render($plantillaEmail);

		$mandrill_apikey = $venta['Tienda']['mandrill_apikey'];

		if (empty($mandrill_apikey)) {
			return false;
		}

		$mandrill = $this->Components->load('Mandrill');

		$mandrill->conectar($mandrill_apikey);

		$asunto = '['.$venta['Tienda']['nombre'].'] Venta #' . $id_venta . ' - ' . $nombre_estado_nuevo;
		
		if (Configure::read('ambiente') == 'dev') {
			$asunto = '['.$venta['Tienda']['nombre'].'-DEV] Venta #' . $id_venta . ' - ' . $nombre_estado_nuevo;
		}

		$remitente = array(
			'email' => 'no-reply@nodriza.cl',
			'nombre' => 'Ventas ' . $venta['Tienda']['nombre']
		);

		$destinatarios = array(
			array(
				'email' => trim($venta['VentaCliente']['email']),
				'name' => $venta['VentaCliente']['nombre'] . ' ' . $venta['VentaCliente']['apellido']
			)
		);
		
		if (Configure::read('ambiente') == 'dev') {
            $destinatarios = array(
				array(
					'email' => 'cristian.rojas@nodriza.cl',
					'name' => 'Cristian rojas'
				)
			);
      	}

		return $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
		
	}

	/**
	 * [crearDteAutomatico description]
	 * @param  [type] $venta [description]
	 * @return [type]        [description]
	 */
	public function crearDteAutomatico($venta)
	{	
		$respuesta =  array(
			'success', 'errors'
		);

		$tipo_documento = $venta['VentaExterna']['facturacion']['tipo_documento'];

		# Solo acepta boleta y factura
		if (!in_array($tipo_documento, array(33,39))) {
			$respuesta['errors'] = sprintf('Venta #%d: Tipo de documento no permitido', $venta['Venta']['id']);
			return $respuesta;
		}

		 # Ya tiene DTE valido
		 if ( ($tipo_documento == 33 || $tipo_documento == 39) && !DtesController::unicoDteValido($venta['Venta']['id'])) {
		 	$respuesta['errors'] = sprintf('La venta #%d ya tien un DTE de venta válido.', $venta['Venta']['id']);
		 	return $respuesta;
		}

		# si no tiene items no se puede procesar
		if (empty($venta['VentaDetalle'])) {
			$respuesta['errors'] = sprintf('La venta #%d no tiene cargado productos para facturar. Intente emitir el DTE manualmente.', $venta['Venta']['id']);
			return $respuesta;
		}

		# si es factura y no tiene todos los datos de facturación, retorna.
		if ($tipo_documento == 33 && 
			(empty($venta['VentaExterna']['facturacion']['rut_receptor'])
			|| empty($venta['VentaExterna']['facturacion']['razon_social_receptor'])
			|| empty($venta['VentaExterna']['facturacion']['giro_receptor'])
			|| empty($venta['VentaExterna']['facturacion']['direccion_receptor'])
			|| empty($venta['VentaExterna']['facturacion']['comuna_receptor']) )

		) {
			$respuesta['errors'] = sprintf('La venta #%d no tiene los datos de facturación cargados. Intente emitir el DTE manualmente.', $venta['Venta']['id']);
			return $respuesta;
		}

		# DTE
		$dte['Dte']['tipo_documento']        = $tipo_documento;
		$dte['Dte']['fecha']        		 = date('Y-m-d');
		$dte['Dte']['razon_social_receptor'] = $venta['VentaExterna']['facturacion']['razon_social_receptor'];
		$dte['Dte']['giro_receptor']         = $venta['VentaExterna']['facturacion']['giro_receptor'];
		$dte['Dte']['direccion_receptor']    = $venta['VentaExterna']['facturacion']['direccion_receptor'];
		$dte['Dte']['comuna_receptor']       = $venta['VentaExterna']['facturacion']['comuna_receptor'];
		$dte['Dte']['estado']                = 'no_generado';
		$dte['Dte']['venta_id']              = $venta['Venta']['id'];
		$dte['Dte']['tienda_id']             = $venta['Tienda']['id'];
		$dte['Dte']['externo']               = $venta['Venta']['id_externo'];
		$dte['Dte']['administrador_id']      = $this->Auth->user('id');
		$dte['Dte']['medio_de_pago']         = 1; // Contado por defecto
		if ($venta['Bodega']['principal'] != 1) {
		$dte['Dte']['sucursal_sii']          = $venta['Bodega']['codigo_sucursal'];
		}

		$dte['Dte']['glosa'] = __('Dte generado automáticamente para la venta # ') . $venta['Venta']['id'];

		# Rut sin puntos
		if (!empty($venta['VentaExterna']['facturacion']['rut_receptor'])) {
			$dte['Dte']['rut_receptor'] = formato_rut($venta['VentaExterna']['facturacion']['rut_receptor']);
		}else{
			$dte['Dte']['rut_receptor'] = '66666666-6';
		}
	
		# Si existe costo de transporte se agrega como ITEM
		if (round($venta['Venta']['costo_envio']) > 0) {
			$cantidadItem = (count($venta['VentaDetalle']) + 1);

			$dte['DteDetalle'][$cantidadItem]['VlrCodigo'] = "COD-Trns";
			$dte['DteDetalle'][$cantidadItem]['NmbItem']   = "Transporte";

			# Para boleta se envia el valor bruto y así evitar que el monto aumente o disminuya por el calculo de iva
			if ($tipo_documento == 39) {
				$dte['DteDetalle'][$cantidadItem]['PrcItem'] = round($venta['Venta']['costo_envio']);
			}else{
				$dte['DteDetalle'][$cantidadItem]['PrcItem'] = monto_neto($venta['Venta']['costo_envio']);
			}
			$dte['DteDetalle'][$cantidadItem]['QtyItem'] = 1;
		}

		foreach ($venta['VentaDetalle'] as $k => $item) {

			if ($item['precio'] <= 0) {
				continue;
			}

			$dte['DteDetalle'][$k]['VlrCodigo'] = sprintf('COD-%d', $item['venta_detalle_producto_id']);
			$dte['DteDetalle'][$k]['NmbItem'] = $item['VentaDetalleProducto']['nombre'];
			$dte['DteDetalle'][$k]['QtyItem'] = $item['cantidad'] - $item['cantidad_anulada'];

			# Boleta valores brutos o con iva
			if ($tipo_documento == 39) 
			{ 
				$dte['DteDetalle'][$k]['PrcItem'] = $this->precio_bruto($item['precio']);	
			}
			else
			{
				$dte['DteDetalle'][$k]['PrcItem'] = $item['precio'];
			}

		}

		// Descuento Bruto en boletas
		if ($venta['Venta']['descuento'] > 0) 
		{	
			# Boleta valores brutos o con iva
			if ($tipo_documento == 39) 
			{ 
				$dte['DscRcgGlobal']['ValorDR'] = $venta['Venta']['descuento'];
			}
			else
			{
				$dte['DscRcgGlobal']['ValorDR'] = monto_neto($venta['Venta']['descuento']);
			}
		}
		
		$DteModel = ClassRegistry::init('Dte');

		# Guardar información del DTE en base de datos local
		if($DteModel->saveAll($dte)) {

			$this->LibreDte->crearCliente($venta['Tienda']['facturacion_apikey']);
		
			$nwDte  = $this->LibreDte->prepararDte($dte);
			$id_dte = $DteModel->id;
			
			if (!empty($id_dte)) {
				# Obtener DTE interno por id
				$dteInterno = ClassRegistry::init('Dte')->find('first', array('conditions' => array('id' => $id_dte)));
			}else{
				# Obtener último DTE guardado
				$dteInterno = ClassRegistry::init('Dte')->find('first', array('order' => array('id' => 'DESC')));
			}

			try {
				
				// crear DTE temporal
				$dte_temporal = $this->LibreDte->crearDteTemporal($nwDte, $dteInterno);

				if (empty($dte_temporal)) {
					$respuesta['errors'] = sprintf('No fue posible generar el DTE temporal para la venta #%d. Verifique los campos e intente nuevamente.', $venta['Venta']['id']);
					return $respuesta;
				}

				if (Configure::read('ambiente') == 'dev') 
				{
					// crear DTE test en base a dte temporal
					$generar = $this->LibreDte->crearDteTest($dte_temporal, $dteInterno);
				}
				else
				{
					// crear DTE real
					$generar = $this->LibreDte->crearDteReal($dte_temporal, $dteInterno);
				}

			} catch (Exception $e) {

				if($e->getCode() != 200) {
					$respuesta['errors'] = sprintf('Venta #%d error: %s', $venta['Venta']['id'], $e->getMessage());
					return $respuesta;
				}

			}

			# Preparamos los embalajes
			$this->WarehouseNodriza->procesar_embalajes($venta['Venta']['id']);

			try {
				$this->LibreDte->generarPDFDteEmitido($dteInterno['Dte']['venta_id'], $dteInterno['Dte']['id'], $dteInterno['Dte']['tipo_documento'], $dteInterno['Dte']['folio'], $dteInterno['Dte']['emisor'] );
			} catch (Exception $e) {
				if($e->getCode() != 200) {
					$respuesta['errors'] = sprintf('Venta #%d error: %s', $venta['Venta']['id'], $e->getMessage());
					return $respuesta;
				}
			}	


			# Enviamos doc al cliente
			if (!empty($venta['VentaCliente'])) {

				$emails = array(
					$venta['VentaCliente']['email']
				);

				$asunto = sprintf('Su Factura Electrónica ha sido emitida.', $this->LibreDte->tipoDocumento[$dteInterno['Dte']['tipo_documento']]);

				$mensaje = sprintf('Estimado/a %s %s. Hemos emitido su %s exitosamente para su compra referencia %s. El documento los encontrará adjunto a este email. Por favor NO RESPONDA ESTE EMAIL ya que es generado automáticamente.', $venta['VentaCliente']['nombre'], $venta['VentaCliente']['apellido'], $venta['Venta']['referencia'], $this->LibreDte->tipoDocumento[$dteInterno['Dte']['tipo_documento']]);

				$enviar = $this->LibreDte->enviarDteEmail(
					$emails, 
					$dteInterno['Dte']['tipo_documento'], 
					$dteInterno['Dte']['folio'], 
					$dteInterno['Dte']['emisor'],
					$asunto,
					$mensaje);
				
				if ($enviar) {
					$respuesta['success'] = sprintf('Venta #%d: DTE generado y enviado existosamente.', $venta['Venta']['id']);
				}else{
					$respuesta['success'] = sprintf('Venta #%d: DTE generado existosamente.', $venta['Venta']['id']);
				}
			}

			return $respuesta;

		}else{
			$respuesta['errors'] = sprintf('No fue posible generar el DTE para la venta #%d. Verifique los campos e intente nuevamente.', $venta['Venta']['id']);
			return $respuesta;
		}
	}


	/**
	 * Obtiene todos los datos necesarios de una venta
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function preparar_venta($id)
	{
		# Toda la información de la venta
		$venta = $this->Venta->obtener_venta_por_id($id);
		$this->LibreDte = $this->Components->load('LibreDte');
		$this->Prestashop = $this->Components->load('Prestashop');
		# Linio
		if ($venta['Marketplace']['marketplace_tipo_id'] == 1) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Linio = $this->Components->load('Linio');
			}
			# cliente Linio
			$this->Linio->crearCliente( $venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'] );

			//$mensajes =  $this->Linio->linio_obtener_venta_mensajes($venta, $ConexionLinio);

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Linio->linio_obtener_venta($venta['Venta']['id_externo'], true);

			// Datos d facturacion
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);

			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

			// Detalles de envio
			$venta['Envio'][0] = array(
				'id'                      => null,
				'tipo'                    => null,
				'estado'                  => null,
				'direccion_envio'         => sprintf('%s, %s, %s', $venta['VentaExterna']['AddressShipping']['Address1'], $venta['VentaExterna']['AddressShipping']['Address2'], $venta['VentaExterna']['AddressShipping']['City']),
				'nombre_receptor'         => sprintf('%s %s', $venta['VentaExterna']['AddressShipping']['FirstName'], $venta['VentaExterna']['AddressShipping']['LastName']),
				'fono_receptor'           => $venta['VentaExterna']['AddressShipping']['Phone'],
				'producto'                => null,
				'cantidad'                => 1, // No especifica
				'costo'                   => 0,
				'fecha_entrega_estimada'  => $venta['VentaExterna']['PromisedShippingTime'],
				'comentario'              => '',
				'mostrar_etiqueta'        => false,
				'paquete' 				  => false
			);
			
		}

		# MEli
		if ($venta['Marketplace']['marketplace_tipo_id'] == 2) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->MeliMarketplace = $this->Components->load('MeliMarketplace');
			}
			$this->MeliMarketplace->crearCliente( $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token'] );
			$this->MeliMarketplace->mercadolibre_conectar('', $venta['Marketplace']);

			$mensajes = $this->MeliMarketplace->mercadolibre_obtener_mensajes($venta['Marketplace']['access_token'], $venta['Venta']['id_externo']);

			foreach ($mensajes as $mensaje) {
				$data = array();
				$data['mensaje'] = $this->removeEmoji($mensaje['text']['plain']);
				$data['fecha'] = CakeTime::format($mensaje['date'], '%d-%m-%Y %H:%M:%S');
				$data['asunto'] = $mensaje['subject'];
				$venta['VentaMensaje'][] = $data;
			}

			// Detalles de la venta externa
			$venta['VentaExterna'] = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($venta['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);
			
			// Datos d facturacion
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);


			if (isset($venta['VentaExterna']['shipping']['id'])) {

				$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

				$envio = $this->MeliMarketplace->mercadolibre_obtener_envio($venta['VentaExterna']['shipping']['id']);

				// Detalles de envio
				$direccion_envio = '';
				$nombre_receptor = '';
				$fono_receptor   = '';
				$comentario      = '';

				if (isset($envio['receiver_address']['address_line'])
					&& isset($envio['receiver_address']['city']['name'])) {
					$direccion_envio = sprintf('%s, %s', $envio['receiver_address']['address_line'], $envio['receiver_address']['city']['name']);
				}

				if (isset($envio['receiver_address']['receiver_name'])) {
					$nombre_receptor = $envio['receiver_address']['receiver_name'];
				}

				if (isset($envio['receiver_address']['receiver_phone'])) {
					$fono_receptor = $envio['receiver_address']['receiver_phone'];
				}

				if (isset($envio['receiver_address']['comment'])) {
					$comentario = $envio['receiver_address']['comment'];
				}
				
				$venta['Envio'][0] = array(
					'id'                      => $envio['id'],
					'tipo'                    => $envio['shipping_option']['name'],
					'estado'                  => $envio['status'],
					'direccion_envio'         => $direccion_envio,
					'nombre_receptor'         => $nombre_receptor,
					'fono_receptor'           => $fono_receptor,
					'producto'                => null,
					'cantidad'                => 1,
					'costo'                   => $envio['shipping_option']['cost'],
					'fecha_entrega_estimada'  => (isset($envio['shipping_option']['estimated_delivery_time'])) ? CakeTime::format($envio['shipping_option']['estimated_delivery_time']['date'], '%d-%m-%Y %H:%M:%S') : __('No especificado') ,
					'comentario'              => $comentario,
					'mostrar_etiqueta'        => ($envio['status'] == 'ready_to_ship') ? true : false,
					'paquete' 				  => false
				);	
				
			}

		}	

		# Prestashop
		if (!$venta['Venta']['marketplace_id'] 
		&& !empty($venta['Venta']['id_externo'])
		&& !$venta['Venta']['venta_manual']) 
		{
			
			# Cliente Prestashop
			$this->Prestashop->crearCliente( $venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'] );	
		
			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Prestashop->prestashop_obtener_venta($venta['Venta']['id_externo']);		

			$transacciones = $this->Prestashop->prestashop_obtener_venta_transacciones($venta['Venta']['referencia']); 

			if (isset($transacciones['order_payment']['transaction_id'])) {
				$venta['VentaExterna']['transacciones']['order_payment'] = array(
					0 => $transacciones['order_payment']
				);
			}else{
				$venta['VentaExterna']['transacciones'] = $transacciones;
			}

			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

			$venta['VentaMensaje'] = $this->Prestashop->prestashop_obtener_venta_mensajes($venta['Venta']['id_externo']);

			$direccionEnvio       = $this->Prestashop->prestashop_obtener_venta_direccion($venta['VentaExterna']['id_address_delivery']);				

			// Detalles de envio
			$telefonosEnvio = '';
			
			if (is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) {
				$telefonosEnvio .= implode(' ', $direccionEnvio['address']['phone_mobile']);
			}

			if (!is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) {
				$telefonosEnvio .= ' ' . $direccionEnvio['address']['phone_mobile'];
			}


			if (is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
				$telefonosEnvio .= implode(' ', $direccionEnvio['address']['phone']);
			}

			if (!is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) {
				$telefonosEnvio .= ' ' . $direccionEnvio['address']['phone'];
			}
			

			$comuna = 'No obtenida';

			if (isset($direccionEnvio['address']['id_state'])) {
				$comuna = $this->Prestashop->prestashop_obtener_comuna_por_id($direccionEnvio['address']['id_state'])['state']['name'];
			}
			
			
			
			// Detalles de envio
			$venta['Envio'][0] = array(
				'id'                      => $direccionEnvio['address']['id'],
				'tipo'                    => 'Dir. despacho',
				'estado'                  => (!$direccionEnvio['address']['deleted']) ? 'activo' : 'eliminada',
				'direccion_envio'         => @sprintf('%s %s, %s, %s', $direccionEnvio['address']['address1'], implode(',', $direccionEnvio['address']['address2']), $direccionEnvio['address']['city'], $comuna),
				'nombre_receptor'         => @sprintf('%s %s', $direccionEnvio['address']['firstname'], $direccionEnvio['address']['lastname']),
				'fono_receptor'           => $telefonosEnvio,
				'producto'                => null,
				'cantidad'                => 1, // No especifica
				'costo'                   => $venta['VentaExterna']['total_shipping_tax_incl'],
				'fecha_entrega_estimada'  => 'No especificado',
				'comentario'              => @implode(',', $direccionEnvio['address']['other']),
				'mostrar_etiqueta'        => true,
				'paquete' 				  => false
			);

			# Datos de facturación para compras por Prestashop
			$this->Toolmania = $this->Components->load('Toolmania');
			$this->Toolmania::$api_url = $venta['Tienda']['apiurl_prestashop'];
			
			#Obtener información webpay si es necesario
			#$webpay                      = $this->Toolmania->obtenerWebpayInfo($this->request->data['Orden']['id_cart'], $this->Session->read('Tienda.apikey_prestashop'));
			$documentos                  = $this->Toolmania->obtenerDocumento($venta['Venta']['id_externo'], null, $venta['Tienda']['apikey_prestashop']);
			
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);
			
			if (!empty($documentos['content'])) {

				$tipoDoc = ($documentos['content'][0]['boleta']) ? 39 : 33;

				$facturacion = array(
					'tipo_documento'        => $tipoDoc,
					'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[$tipoDoc],
					'rut_receptor'          => $documentos['content'][0]['rut'],
					'razon_social_receptor' => $documentos['content'][0]['empresa'],
					'giro_receptor'         => $documentos['content'][0]['giro'],
					'direccion_receptor'    => $documentos['content'][0]['calle']
				);
				# Para la consola se carga el componente on the fly!
				if ($this->shell) {
					$this->LibreDte = $this->Components->load('LibreDte');
				}
				// Obtenemos la información del contribuyente desde el SII
				$this->LibreDte->crearCliente($venta['Tienda']['facturacion_apikey']);
		
				$info = $this->LibreDte->obtenerContribuyente($this->rutSinDv($documentos['content'][0]['rut']));
				
				// Agregamos comuna
				if (isset($info['comuna_glosa'])) {
					$facturacion['comuna_receptor'] = $info['comuna_glosa'];
				}

				// Agregamos razon social
				if (empty($documentos['content'][0]['empresa']) && isset($info['razon_social'])) {
					$facturacion['razon_social_receptor'] = $info['razon_social'];
				}

				// Agregamos giro
				if (empty($documentos['content'][0]['giro']) && isset($info['giro'])) {
					$facturacion['giro_receptor'] = $info['giro'];
				}	

				// Agregamos direccon
				if (empty($documentos['content'][0]['direccion_receptor']) && isset($info['direccion'])) {
					$facturacion['direccion_receptor'] = $info['direccion'];
				}	
				
				
				# Guardamos el rut de la persona
				ClassRegistry::init('VentaCliente')->id = $venta['VentaCliente']['id'];
				ClassRegistry::init('VentaCliente')->saveField('rut', $documentos['content'][0]['rut']);

				$venta['VentaCliente']['rut'] = $documentos['content'][0]['rut'];

				$venta['VentaExterna']['facturacion'] = array_replace_recursive($venta['VentaExterna']['facturacion'], $facturacion);
			}
		}

		# Tienda principal
		$tienda = ClassRegistry::init('Tienda')->find('first', array(
			'conditions' => array(
				'Tienda.principal' => 1,
				'Tienda.activo' => 1
			),
			'fields' => array(
				'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop'
			)
		));

		# Agregamos las imagenes de prstashop al arreglo
		if (!empty($tienda)) {

			$this->Prestashop->crearCliente($tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop']);

			foreach ($venta['VentaDetalle'] as $iv => $d) {
				$venta['VentaDetalle'][$iv]['VentaDetalleProducto']['imagenes'] = $this->Prestashop->prestashop_obtener_imagenes_producto($d['venta_detalle_producto_id'], $tienda['Tienda']['apiurl_prestashop']);	
			}

		}


		return $venta;
	}

	/**
	 * [admin_crear_dte_one_click description]
	 * @param  [type] $id [description]
	 * @return [type]     [description]
	 */
	public function admin_crear_dte_one_click($id)
	{	
		$venta = $this->preparar_venta($id);

		try {
			$result = $this->crearDteAutomatico($venta);
		} catch (Exception $e) {
			$result['errors'] = $e->getMessage();
		}
		

		if (!empty($result['success'])) {
			$this->Session->setFlash($result['success'], null, array(), 'success');			
		}

		if (!empty($result['errors'])) {
			$this->Session->setFlash($result['errors'], null, array(), 'danger');			
		}

		$this->redirect(array('action' => 'view', $id));

	}


	/**
	 * Cambia el estado a preparación segun corresponda
	 * @param  [type] $venta [description]
	 * @return [type]        [description]
	 */
	public function cambiar_estado_preparada($venta)
	{	
		ClassRegistry::init('VentaEstado')->id = $venta['Venta']['venta_estado_id'];
		ClassRegistry::init('Tienda')->id      = $venta['Venta']['tienda_id'];

		if (!empty($venta['Venta']['marketplace_id'])) {
			ClassRegistry::init('Marketplace')->id = $venta['Venta']['marketplace_id'];				
		}
				
		$notificar        = ClassRegistry::init('VentaEstado')->field('notificacion_cliente');
		$esPrestashop     = (empty($venta['Venta']['marketplace_id'])) ? true : false;
		$estado_actual    = $venta['Venta']['venta_estado_id'];
		$estado_nuevo     = '';
		$estado_nuevo_arr = array();
		$id_externo       = $venta['Venta']['id_externo'];
		$plantillaEmail   = ClassRegistry::init('VentaEstadoCategoria')->field('plantilla', array('id' => ClassRegistry::init('VentaEstado')->field('venta_estado_categoria_id')));
		
		$esMercadolibre = false;
		$esLinio        = false;

		# Verificamos el canal de la venta
		if (!empty($venta['Venta']['marketplace_id'])) {
			switch ( ClassRegistry::init('Marketplace')->field('marketplace_tipo_id') ) {
				case 1: // Linio
					$esLinio      = true;
					$apiurllinio  = ClassRegistry::init('Marketplace')->field('api_host');
					$apiuserlinio = ClassRegistry::init('Marketplace')->field('api_user');
					$apikeylinio  = ClassRegistry::init('Marketplace')->field('api_key');
					break;
				
				case 2: // Meli
					$esMercadolibre = true;
					break;
			}
		}

		$apiurlprestashop = ClassRegistry::init('Tienda')->field('apiurl_prestashop');
		$apikeyprestashop = ClassRegistry::init('Tienda')->field('apikey_prestashop');

		
		# Prestashop
		if ( $esPrestashop && !empty($apiurlprestashop) && !empty($apikeyprestashop)) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Prestashop = $this->Components->load('Prestashop');
			}
			# Cliente Prestashop
			$this->Prestashop->crearCliente( $apiurlprestashop, $apikeyprestashop );

			# Obtenemos estado de en prepracion
			$preparacion      = ClassRegistry::init('VentaEstado')->obtener_estado_preparacion();

			if (empty($preparacion)) {
				return false;
			}

			$estado_nuevo     = $preparacion['VentaEstado']['nombre'];
			$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
			
			# El estado ya se ha actualizado
			if ($estado_actual == $estado_nuevo_arr['VentaEstado']['id']) {
				return false;
			}

			# OBtenemos el ID prestashop del estado
			$estadoPrestashop = $this->Prestashop->prestashop_obtener_estado_por_nombre($estado_nuevo);
			
			if (empty($estadoPrestashop)) {
				return false;
			}
			
			if (Configure::read('ambiente') == 'dev') {
				$resCambio = true;
			}else{
				$resCambio = $this->Prestashop->prestashop_cambiar_estado_venta($id_externo, $estadoPrestashop['id']);
			}
			
			if ($resCambio) {

				# Asignamos el nuevo estado a la venta intenra
				$venta['Venta']['venta_estado_id'] = $estado_nuevo_arr['VentaEstado']['id'];
				
				# Plantilla nuevo estado
				ClassRegistry::init('VentaEstado')->id = $venta['Venta']['venta_estado_id'];
				$notificar        = ClassRegistry::init('VentaEstado')->field('notificacion_cliente');
				$plantillaEmail   = ClassRegistry::init('VentaEstadoCategoria')->field('plantilla', array('id' => ClassRegistry::init('VentaEstado')->field('venta_estado_categoria_id')));	
				
				if (!empty($plantillaEmail) && $notificar) {
					$this->notificar_cambio_estado($venta['Venta']['id'], $plantillaEmail, $estado_nuevo);
				}

			}
		}
		
		# Guardamos el nuevo estado
		$this->Venta->id = $venta['Venta']['id'];
		if ($this->Venta->saveField('venta_estado_id', $venta['Venta']['venta_estado_id'])) {
			return true;

		}else{
			return false;
		}

	}
	/**
	 * [admin_facturacion_masiva description]
	 * @return [type] [description]
	 */
	public function admin_facturacion_masiva()
	{	
		$result = array(
			'success',
			'errors'
		);

		$pdfs = array();

		$pdfsEtiquetas = array();

		$url_retorno = $this->request->data['Venta']['return_url'];

		if ($this->request->is('post')) {
			
			foreach ($this->request->data['Venta'] as $iv => $v) {
				
				if ($iv == 'return_url')
					continue;

				# No existe a venta
				if (!$this->Venta->exists($v['id'])) {
					$result['errors'][] = sprintf('La venta #%d no existe en los registros', $v['id']);
					continue;
				}

				$venta = $this->preparar_venta($v['id']);
				
				$result_dte = $this->crearDteAutomatico($venta);

				if (!empty($result_dte['success'])) {

					$result['success'][] = $result_dte['success'];

					$nwDte = ClassRegistry::init('Dte')->find('all', array(
						'conditions' => array(
							'Dte.venta_id' => $venta['Venta']['id']
						),
						'fields' => array(
							'Dte.id', 'Dte.folio', 'Dte.tipo_documento', 'Dte.rut_receptor', 'Dte.razon_social_receptor', 'Dte.giro_receptor', 'Dte.neto', 'Dte.iva',
							'Dte.total', 'Dte.fecha', 'Dte.estado', 'Dte.venta_id', 'Dte.pdf', 'Dte.invalidado'
						),
						'order' => 'Dte.fecha DESC'
					));

					if (empty($nwDte)) {
						continue;
					}

					$venta['Dte'] = Hash::extract($nwDte, '{n}.Dte');

					$dtes = $this->obtener_dtes_pdf_venta($venta['Dte'], 2);
					
					if (empty($dtes)) {
						continue;
					}

					#$this->cambiar_estado_preparada($venta);

					foreach ($dtes as $dte) {
						$pdfs[] = $dte['path'];
					}

					$venta = $this->preparar_venta($v['id']);

					$url_etiqueta_envio = $this->obtener_etiqueta_envio_default_url($venta);
					
					$pdfs[] = $url_etiqueta_envio['path'];

					# solo etiquetas
					$pdfsEtiquetas[] = $url_etiqueta_envio['path'];
					
					continue;
				}

				if (!empty($result_dte['errors'])) {

					$result['errors'][] = $result_dte['errors'];
					continue;
				}

			}
		}
		
		if (!empty($result['errors'])) {
			$this->Session->setFlash($this->crearAlertaUl($result['errors'], 'Errores encontrados'), null, array(), 'danger');
		}
		
		if (!empty($result['success'])) {
			
			$this->Session->setFlash($this->crearAlertaUl($result['success'], 'Procesados correctamente'), null, array(), 'success');

			if (!empty($pdfs)) {
				
				$pdf = $this->unir_documentos($pdfs, 'todo');
				$pdfEtiqueta = $this->unir_documentos($pdfsEtiquetas, 'todo_etiquetas');

				$pdf_resultado = array();

				if (!empty($pdf['result'])) {
					foreach ($pdf['result'] as $ir => $url) {
						$pdf_resultado[] = '<a href="'.$url['document'].'" class="link" download><i class="fa fa-download"></i> Descargar PDF Dte, Envio, Etiqueta </a>';
					}
				}

				if (!empty($pdfEtiqueta['result'])) {
					foreach ($pdfEtiqueta['result'] as $ir => $url) {
						$pdf_resultado[] = '<a href="'.$url['document'].'" class="link" download><i class="fa fa-download"></i> Descargar PDF Etiquetas </a>';
					}
				}

				if (!empty($pdf_resultado)) {
					$this->Session->setFlash($this->crearAlertaUl($pdf_resultado, 'Descargas disponibles'), null, array(), 'success');	
				}
			}
		}
	
		$this->redirect($url_retorno);
	}


	/**
	 * [crear_venta_linio description]
	 * @param  [type] $marketplace_id [description]
	 * @param  [type] $id_externo     [description]
	 * @return [type]                 [description]
	 */
	public function crear_venta_linio($marketplace_id, $id_externo)
	{	
		# Obtenemos el marketplace
		$marketplace = ClassRegistry::init('Marketplace')->find('first', array(
			'conditions' => array(
				'Marketplace.id' => $marketplace_id,
				'Marketplace.activo' => 1
			),
			'fields' => array(
				'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.tienda_id', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id', 'Marketplace.nombre'
			)
		));

		# No existe
		if (empty($marketplace)) {
			return false;
		}

		# Para la consola se carga el componente on the fly!
		if ($this->shell) {
			$this->Linio = $this->Components->load('Linio');
		}

		#Vemos si existe en la BD
		$existe = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id_externo'     => $id_externo,
				'Venta.marketplace_id' => $marketplace_id
			)
		));

		if (!empty($existe)) {
			return true;
		}

		# Cliente Linio	
		$this->Linio->crearCliente($marketplace['Marketplace']['api_host'], $marketplace['Marketplace']['api_user'], $marketplace['Marketplace']['api_key']);

		$detalle_venta = $this->Linio->linio_obtener_venta($id_externo, true); 
		
		# datos de la venta a registrar
		$NuevaVenta = array();
		$NuevaVenta['Venta']['tienda_id']      = $marketplace['Marketplace']['tienda_id'];
		$NuevaVenta['Venta']['marketplace_id'] = $marketplace_id;
		$NuevaVenta['Venta']['id_externo']     = $id_externo;
		$NuevaVenta['Venta']['referencia']     = $detalle_venta['OrderNumber'];
		$NuevaVenta['Venta']['fecha_venta']    = $detalle_venta['CreatedAt'];

		# Dirección cliente
		$direcciones = array(
			$detalle_venta['AddressShipping']['Address1'],
			$detalle_venta['AddressShipping']['Address2'],
			$detalle_venta['AddressShipping']['Address3'],
			$detalle_venta['AddressShipping']['Address4'],
			$detalle_venta['AddressShipping']['Address5']
		);

		$comuna_id = ClassRegistry::init('Comuna')->obtener_id_comuna_por_nombre($detalle_venta['AddressShipping']['City']);

		// Direccion despacho
		$NuevaVenta['Venta']['direccion_entrega'] =  implode(', ', $direcciones);
		$NuevaVenta['Venta']['comuna_entrega']    =  ClassRegistry::init('Comuna')->field('nombre', array('id' => $comuna_id));
		$NuevaVenta['Venta']['comuna_id']         =  $comuna_id;
		$NuevaVenta['Venta']['nombre_receptor']   =  $detalle_venta['AddressShipping']['FirstName'] . ' ' . $detalle_venta['AddressShipping']['LastName'];
		$NuevaVenta['Venta']['fono_receptor']     =  trim($detalle_venta['AddressShipping']['Phone']) . '-' .  trim($detalle_venta['AddressShipping']['Phone2']) ;
		
		//se obtiene el estado de la venta
		$NuevaVenta['Venta']['venta_estado_id']  = $this->obtener_estado_id($detalle_venta['Statuses']['Status'], $marketplace['Marketplace']['marketplace_tipo_id']);
		$NuevaVenta['Venta']['estado_anterior']  = 1;
		
		//se obtiene el medio de pago
		$NuevaVenta['Venta']['medio_pago_id']    = $this->obtener_medio_pago_id($detalle_venta['PaymentMethod']);

		//se obtiene el metodo de envio
		$NuevaVenta['Venta']['metodo_envio_id']  = $this->obtener_metodo_envio_id('');

		$bodega 							     = ClassRegistry::init('Bodega')->obtener_bodega_principal();
		$NuevaVenta['Venta']['bodega_id'] 		 = ClassRegistry::init('MetodoEnvio')->bodega_id($NuevaVenta['Venta']['metodo_envio_id']) ?? $bodega['Bodega']['id']; 
		
		//se obtiene el cliente
		$NuevaVenta['Venta']['venta_cliente_id'] = $this->obtener_cliente_id($detalle_venta);
		
		$NuevaVenta['Venta']['descuento']        = (float) 0;
		$NuevaVenta['Venta']['costo_envio']      = (float) 0;

		# si es un estado pagado se reserva el stock disponible
		if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['venta_estado_id']) ) {
			#$ActualizarVenta['Venta']['prioritario'] = 1;
		}

		# Guardar n° de seguimiento
		$NuevaVenta['Transporte'] = array();

		//ciclo para recorrer el detalle de la venta
		foreach ($detalle_venta['Products'] as $DetalleVenta) {

			$DetalleVenta['Sku'] = intval($DetalleVenta['Sku']);

			//se guarda el producto si no existe
			$idNuevoProducto = $this->linio_guardar_producto($DetalleVenta);

			$NuevoDetalle = array();
			$NuevoDetalle['venta_detalle_producto_id'] = $idNuevoProducto;

			if ( round($DetalleVenta['VoucherAmount']) > 0 ) {
				$NuevoDetalle['precio']                    = monto_neto(round($DetalleVenta['PaidPrice'] + $DetalleVenta['VoucherAmount'], 2));
				$NuevoDetalle['precio_bruto']              = round($DetalleVenta['PaidPrice'] + $DetalleVenta['VoucherAmount'], 2);	
			}else{
				$NuevoDetalle['precio']                    = monto_neto(round($DetalleVenta['PaidPrice'], 2));
				$NuevoDetalle['precio_bruto']              = $DetalleVenta['PaidPrice'];
			}
			
			$NuevoDetalle['cantidad_pendiente_entrega'] = 1;
			$NuevoDetalle['cantidad_reservada']         = 0;
			$NuevoDetalle['cantidad']         			= 1;
			$NuevoDetalle['total_neto']                 = $NuevoDetalle['precio'] * $NuevoDetalle['cantidad'];			
			$NuevoDetalle['total_bruto']				= (float) monto_bruto($NuevoDetalle['total_neto']);
			
			# costo de despacho
			$NuevaVenta['Venta']['costo_envio'] = $NuevaVenta['Venta']['costo_envio'] + round($DetalleVenta['ShippingAmount'], 2);

			$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;

			# agregamos los n° de seguimiento
			if (!empty($DetalleVenta['ShipmentProvider']) && !empty($DetalleVenta['TrackingCode'])) {
				$seguimiento =  array(
					'transporte_id' => ClassRegistry::init('Transporte')->obtener_transporte_por_nombre($DetalleVenta['ShipmentProvider']),
					'cod_seguimiento' => $DetalleVenta['TrackingCode']
				);

				$NuevaVenta['Transporte'][] = $seguimiento;
			}

		} //fin ciclo detalle de venta


		// Guardar transacción
		$NuevaTransaccion = array();

		if (!empty($detalle_venta['OrderNumber'])) {
			$NuevaTransaccion['nombre'] = $detalle_venta['OrderNumber'];
		}

		$NuevaTransaccion['monto'] = (!empty($detalle_venta['Price'])) ? $detalle_venta['Price'] : 0;
		$NuevaTransaccion['fee']   = ($NuevaTransaccion['monto'] * ($marketplace['Marketplace']['fee'] / 100));

		$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;


		# Evitamos que se vuelva actualizar el stock en linio
		$excluirLinio = array('Linio' => array($marketplace_id));
		
		//se guarda la venta
		$this->Venta->create();
		if ($this->Venta->saveAll($NuevaVenta) ) {

			$tienda = ClassRegistry::init('Tienda')->obtener_tienda($marketplace['Marketplace']['tienda_id'], array('Tienda.activar_notificaciones', 'Tienda.notificacion_apikey'));

			if ($tienda['Tienda']['activar_notificaciones'] && !empty($tienda['Tienda']['notificacion_apikey'])) {
				$this->Pushalert = $this->Components->load('Pushalert');

				$this->Pushalert::$api_key = $tienda['Tienda']['notificacion_apikey'];

				$tituloPush = sprintf('Nueva venta en %s', $marketplace['Marketplace']['nombre']);
				$mensajePush = sprintf('Pincha aquí para verla');
				$urlPush = Router::url('/', true) . 'ventas/view/' . $this->Venta->id;

				$this->Pushalert->enviarNotificacion($tituloPush, $mensajePush, $urlPush);	
			}

			# si es un estado pagado se reserva el stock disponible
			if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['venta_estado_id']) && !ClassRegistry::init('VentaEstado')->es_estado_entregado($NuevaVenta['Venta']['venta_estado_id'])) {
				$this->Venta->pagar_venta($this->Venta->id);
				$this->actualizar_canales_stock($this->Venta->id, $excluirLinio);
			}

			return true;

		}else{
			return false;
		}
		
	}


	public function actualizar_venta_linio($marketplace_id, $id_externo, $venta, $nuevo_estado = '')
	{	

		if (empty($nuevo_estado)) {

			$marketplace = ClassRegistry::init('Marketplace')->find('first', array(
				'conditions' => array(
					'Marketplace.id' => $marketplace_id
				),
				'fields' => array(
					'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.tienda_id', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id', 'Marketplace.nombre'
				)
			));

			# Cliente Linio	
			$this->Linio->crearCliente($marketplace['Marketplace']['api_host'], $marketplace['Marketplace']['api_user'], $marketplace['Marketplace']['api_key']);

			$detalle_venta = $this->Linio->linio_obtener_venta($id_externo, true);

			$nuevo_estado = $detalle_venta['Statuses']['Status'];
		}

		$nw_estado_id = $this->obtener_estado_id($nuevo_estado, $marketplace['Marketplace']['marketplace_tipo_id']);

		$venta['Venta']['estado_anterior']          = $venta['Venta']['venta_estado_id'];
		$venta['Venta']['venta_estado_id']          = $nw_estado_id;
		$venta['Venta']['venta_estado_responsable'] = 'Linio Webhook';

		# si es un estado rechazo se devuelve el stock disponible
		if ( ClassRegistry::init('VentaEstado')->es_estado_rechazo($venta['Venta']['venta_estado_id']) ) {
			$venta['Venta']['prioritario'] = 0;
		}

		if ($this->Venta->save($venta)) {

			$tienda = ClassRegistry::init('Tienda')->obtener_tienda($marketplace['Marketplace']['tienda_id'], array('Tienda.activar_notificaciones', 'Tienda.notificacion_apikey'));

			if ($tienda['Tienda']['activar_notificaciones'] && !empty($tienda['Tienda']['notificacion_apikey'])) {
				$this->Pushalert = $this->Components->load('Pushalert');

				$this->Pushalert::$api_key = $tienda['Tienda']['notificacion_apikey'];

				$tituloPush = sprintf('Actualización de venta en %s', $marketplace['Marketplace']['nombre']);
				$mensajePush = sprintf('La venta #%d cambió a %s', $venta['Venta']['id'], ClassRegistry::init('VentaEstado')->obtener_estado_por_id($venta['Venta']['venta_estado_id'])['VentaEstado']['nombre'] );
				$urlPush = Router::url('/', true) . 'ventas/view/' . $venta['Venta']['id'];

				$this->Pushalert->enviarNotificacion($tituloPush, $mensajePush, $urlPush);	
			}

			# Evitamos que se vuelva actualizar el stock en linio
			$excluirLinio = array('Linio' => array($marketplace_id));

			# si es un estado pagado se reserva el stock disponible
			if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($nw_estado_id) ) {
				$this->Venta->pagar_venta($venta['Venta']['id']);
				$this->actualizar_canales_stock($venta['Venta']['id'], $excluirLinio);
			}

			# si es un estado rechazo se devuelve el stock disponible
			if ( ClassRegistry::init('VentaEstado')->es_estado_rechazo($nw_estado_id) && !ClassRegistry::init('VentaEstado')->es_estado_cancelado($nw_estado_id) ) {
				$this->Venta->revertir_venta($venta['Venta']['id']);
				$this->actualizar_canales_stock($venta['Venta']['id'], $excluirLinio);
			}

			if ( ClassRegistry::init('VentaEstado')->es_estado_cancelado($nw_estado_id) ) {
				$this->Venta->cancelar_venta($venta['Venta']['id']);
				$this->WarehouseNodriza->procesar_embalajes($venta['Venta']['id']);
				$this->actualizar_canales_stock($venta['Venta']['id'], $excluirLinio);
			}

			return true;

		}else{
			
			return false;
		
		}
	}


	/**
	 * [actualizar_venta_meli description]
	 * @param  [type] $marketplace_id [description]
	 * @param  [type] $id_externo     [description]
	 * @return [type]                 [description]
	 */
	public function actualizar_venta_meli($marketplace_id, $id_externo)
	{
		# Obtenemos el marketplace
		$marketplace = ClassRegistry::init('Marketplace')->find('first', array(
			'conditions' => array(
				'Marketplace.id' => $marketplace_id,
				'Marketplace.activo' => 1
			),
			'fields' => array(
				'Marketplace.id', 'Marketplace.nombre', 'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.tienda_id', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id', 'Marketplace.access_token', 'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.seller_id'
			)
		));


		//datos de la venta a registrar
		$ActualizarVenta = array();

		# No existe
		if (empty($marketplace)) {
			return false;
		}

		#Vemos si existe en la BD
		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id_externo'     => $id_externo,
				'Venta.marketplace_id' => $marketplace_id
			),
			'fields' => array(
				'Venta.id', 'Venta.venta_estado_id'
			),
			'contain' => array(
				'VentaTransaccion'
			)
		));

		# componente on the fly!
		$this->MeliMarketplace = $this->Components->load('MeliMarketplace');

		# cliente y conexion Meli
		$this->MeliMarketplace->crearCliente( $marketplace['Marketplace']['api_user'], $marketplace['Marketplace']['api_key'], $marketplace['Marketplace']['access_token'], $marketplace['Marketplace']['refresh_token'] );

		$this->MeliMarketplace->mercadolibre_conectar('', $marketplace['Marketplace']);

		$ventaMeli = $this->MeliMarketplace->mercadolibre_obtener_venta($marketplace['Marketplace']['access_token'], $id_externo);

		if (empty($ventaMeli)) {
			return false;
		}
		
		$ActualizarVenta['Venta']['id'] = $venta['Venta']['id'];

		//se obtiene el estado de la venta
		$ActualizarVenta['Venta']['estado_anterior'] = $venta['Venta']['venta_estado_id'];
		$ActualizarVenta['Venta']['venta_estado_id'] = $this->obtener_estado_id($ventaMeli['status'], $marketplace['Marketplace']['marketplace_tipo_id']);
		$ActualizarVenta['Venta']['venta_estado_responsable'] = 'Meli Webhook';

		# si es un estado pagado se reserva el stock disponible
		if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($ActualizarVenta['Venta']['venta_estado_id']) ) {
			#$ActualizarVenta['Venta']['prioritario'] = 1;
		}

		# si es un estado rechazo se devuelve el stock disponible
		if ( ClassRegistry::init('VentaEstado')->es_estado_rechazo($ActualizarVenta['Venta']['venta_estado_id']) ) {
			$ActualizarVenta['Venta']['prioritario'] = 0;
		}

		# Mercado libre puede tener más de 1 pago
		foreach ($ventaMeli['payments'] as $ventaTransaccion) {

			$NuevaTransaccion = array();

			if (!empty($ventaTransaccion['id'])) {
				$NuevaTransaccion['nombre'] = $ventaTransaccion['id'];

				if (Hash::check($venta, 'VentaTransaccion.{n}[nombre='.$ventaTransaccion['id'].'].id')) {
					continue;
				}
			}

			$NuevaTransaccion['monto'] = (!empty($ventaTransaccion['total_paid_amount'])) ? $ventaTransaccion['total_paid_amount'] : 0;
			$NuevaTransaccion['fee'] = (!empty($ventaTransaccion['marketplace_fee'])) ? $ventaTransaccion['marketplace_fee'] : 0;
			$NuevaTransaccion['estado'] = (!empty($ventaTransaccion['status'])) ? $ventaTransaccion['status'] : '';

			$ActualizarVenta['VentaTransaccion'][] = $NuevaTransaccion;
		}

		# Evitamos que se vuelva actualizar el stock en prestashop
		$excluirMeli = array('Mercadolibre' => array($marketplace_id));
		
		// Se guarda la venta
		if($this->Venta->saveAll($ActualizarVenta)){

			$tienda = ClassRegistry::init('Tienda')->obtener_tienda($marketplace['Marketplace']['tienda_id'], array('Tienda.activar_notificaciones', 'Tienda.notificacion_apikey'));

			if ($tienda['Tienda']['activar_notificaciones'] && !empty($tienda['Tienda']['notificacion_apikey'])) {
				
				$this->Pushalert = $this->Components->load('Pushalert');
				$this->Pushalert::$api_key = $tienda['Tienda']['notificacion_apikey'];

				$tituloPush = sprintf('Actualización de venta en %s', $marketplace['Marketplace']['nombre']);
				$mensajePush = sprintf('La venta #%d cambió a %s', $venta['Venta']['id'], ClassRegistry::init('VentaEstado')->obtener_estado_por_id($ActualizarVenta['Venta']['venta_estado_id'])['VentaEstado']['nombre'] );
				$urlPush = Router::url('/', true) . 'ventas/view/' . $venta['Venta']['id'];

				$this->Pushalert->enviarNotificacion($tituloPush, $mensajePush, $urlPush);	
			}

			# si es un estado pagado se reserva el stock disponible
			if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($ActualizarVenta['Venta']['venta_estado_id']) && !ClassRegistry::init('VentaEstado')->es_estado_entregado($ActualizarVenta['Venta']['venta_estado_id'])) {
				$this->Venta->pagar_venta($venta['Venta']['id']);
				$this->actualizar_canales_stock($venta['Venta']['id'], $excluirMeli);
			}

			# se entrega la venta
			if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($ActualizarVenta['Venta']['venta_estado_id']) && ClassRegistry::init('VentaEstado')->es_estado_entregado($ActualizarVenta['Venta']['venta_estado_id'])) {
				$this->Venta->entregar($venta['Venta']['id']);
			}

			# si es un estado rechazo se devuelve el stock disponible
			if ( ClassRegistry::init('VentaEstado')->es_estado_rechazo($ActualizarVenta['Venta']['venta_estado_id']) && !ClassRegistry::init('VentaEstado')->es_estado_cancelado($ActualizarVenta['Venta']['venta_estado_id']) ) {
				$this->Venta->revertir_venta($venta['Venta']['id']);
				$this->actualizar_canales_stock($venta['Venta']['id'], $excluirMeli);
			}

			if ( ClassRegistry::init('VentaEstado')->es_estado_cancelado($ActualizarVenta['Venta']['venta_estado_id']) ) {
				$this->Venta->cancelar_venta($venta['Venta']['id']);
				$this->WarehouseNodriza->procesar_embalajes($venta['Venta']['id']);
				$this->actualizar_canales_stock($venta['Venta']['id'], $excluirMeli);
			}

			return true;

		}else{
			return false;
		}
	}


	/**
	 * [crear_venta_meli description]
	 * @param  [type] $marketplace_id [description]
	 * @param  [type] $id_externo     [description]
	 * @return [type]                 [description]
	 */
	public function crear_venta_meli($marketplace_id, $id_externo)
	{
		# Obtenemos el marketplace
		$marketplace = ClassRegistry::init('Marketplace')->find('first', array(
			'conditions' => array(
				'Marketplace.id' => $marketplace_id,
				'Marketplace.activo' => 1
			),
			'fields' => array(
				'Marketplace.id', 'Marketplace.nombre', 'Marketplace.api_host', 'Marketplace.api_user', 'Marketplace.api_key', 'Marketplace.tienda_id', 'Marketplace.fee', 'Marketplace.marketplace_tipo_id', 'Marketplace.access_token', 'Marketplace.refresh_token', 'Marketplace.expires_token', 'Marketplace.seller_id'
			)
		));


		//datos de la venta a registrar
		$NuevaVenta = array();

		# No existe
		if (empty($marketplace)) {
			return false;
		}

		# componente on the fly!
		$this->MeliMarketplace = $this->Components->load('MeliMarketplace');

		# cliente y conexion Meli
		$this->MeliMarketplace->crearCliente( $marketplace['Marketplace']['api_user'], $marketplace['Marketplace']['api_key'], $marketplace['Marketplace']['access_token'], $marketplace['Marketplace']['refresh_token'] );

		$this->MeliMarketplace->mercadolibre_conectar('', $marketplace['Marketplace']);

		$ventaMeli = $this->MeliMarketplace->mercadolibre_obtener_venta($marketplace['Marketplace']['access_token'], $id_externo);

		if (empty($ventaMeli)) {
			return false;
		}
		
		# Info de la nueva venta 
		$NuevaVenta['Venta']['tienda_id']      = $marketplace['Marketplace']['tienda_id'];
		$NuevaVenta['Venta']['marketplace_id'] = $marketplace_id;
		$NuevaVenta['Venta']['id_externo']     = $ventaMeli['id'];
		$NuevaVenta['Venta']['referencia']     = $ventaMeli['id'];

		$NuevaVenta['Venta']['fecha_venta']    = CakeTime::format($ventaMeli['date_created'], '%Y-%m-%d %H:%M:%S');

		//se obtiene el estado de la venta
		$NuevaVenta['Venta']['venta_estado_id'] = $this->obtener_estado_id($ventaMeli['status'], $marketplace['Marketplace']['marketplace_tipo_id']);
		$NuevaVenta['Venta']['estado_anterior'] = 1;

		# Se marca como prioritaria solos si es un pago aceptado
		if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['venta_estado_id'])) {
			$NuevaVenta['Venta']['prioritario'] 	= 1;
		}
		

		# costo envio
		if (isset($ventaMeli['shipping']['cost'])) {
			$NuevaVenta['Venta']['costo_envio'] = $ventaMeli['shipping']['cost'];
		}

		# Descuento a 0
		$NuevaVenta['Venta']['descuento']       = 0;
		
		
		// Detalles de envio
		$direccion_entrega = 'No aplica';
		$numero_entrega    = 'No aplica';
		$comuna_entrega    = 'No aplica';
		$nombre_receptor   = 'No aplica';
		$fono_receptor     = 'No aplica';

		if (isset($ventaMeli['shipping']['id'])) {

			$envio = $this->MeliMarketplace->mercadolibre_obtener_envio($ventaMeli['shipping']['id']);

			if (isset($envio['receiver_address']['address_line'])) {
				$direccion_entrega = $envio['receiver_address']['address_line'];
			}

			if (isset($envio['receiver_address']['street_number'])) {
				$numero_entrega = $envio['receiver_address']['street_number'];
			}

			if (isset($envio['receiver_address']['city']['name'])) {
				$comuna_entrega = $envio['receiver_address']['city']['name'];
			}

			if (isset($envio['receiver_address']['city']['name'])) {
				$comuna_entrega = $envio['receiver_address']['city']['name'];
			}

			if (isset($envio['receiver_address']['receiver_name'])) {
				$nombre_receptor = $envio['receiver_address']['receiver_name'];
			}

			if (isset($envio['receiver_address']['receiver_phone'])) {
				$fono_receptor = $envio['receiver_address']['receiver_phone'];
			}	
		}

		$comuna_id = ClassRegistry::init('Comuna')->obtener_id_comuna_por_nombre($comuna_entrega);

		// Direccion despacho
		$NuevaVenta['Venta']['direccion_entrega'] =  $direccion_entrega;
		$NuevaVenta['Venta']['numero_entrega']    =  $numero_entrega;
		$NuevaVenta['Venta']['comuna_entrega']    =  ClassRegistry::init('Comuna')->field('nombre', array('id' => $comuna_id));
		$NuevaVenta['Venta']['comuna_id']         =  $comuna_id;
		$NuevaVenta['Venta']['nombre_receptor']   =  $nombre_receptor;
		$NuevaVenta['Venta']['fono_receptor']     =  $fono_receptor;
		
		//se obtiene el medio de pago
		$NuevaVenta['Venta']['medio_pago_id']     = $this->obtener_medio_pago_id('Meli gateway'); # Metodo de pago generico para ventas a través de mercadolibre

		# Mercado libre puede tener más de 1 pago
		foreach ($ventaMeli['payments'] as $ventaTransaccion) {

			$NuevaTransaccion = array();

			if (!empty($ventaTransaccion['id'])) {
				$NuevaTransaccion['nombre'] = $ventaTransaccion['id'];
			}

			$NuevaTransaccion['monto'] = (!empty($ventaTransaccion['total_paid_amount'])) ? $ventaTransaccion['total_paid_amount'] : 0;
			$NuevaTransaccion['fee'] = (!empty($ventaTransaccion['marketplace_fee'])) ? $ventaTransaccion['marketplace_fee'] : 0;
			$NuevaTransaccion['estado'] = (!empty($ventaTransaccion['status'])) ? $ventaTransaccion['status'] : '';

			$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;
		}

		# se obtiene el metodo de envio
		if (isset($ventaMeli['shipping']['shipping_option']['name'])) {
			$NuevaVenta['Venta']['metodo_envio_id']  = $this->obtener_metodo_envio_id($ventaMeli['shipping']['shipping_option']['name']);	
		}else{
			$NuevaVenta['Venta']['metodo_envio_id']  = $this->obtener_metodo_envio_id('A coordinar con comprador');	
		}

		$bodega 							     = ClassRegistry::init('Bodega')->obtener_bodega_principal();

		$NuevaVenta['Venta']['bodega_id'] 		 = ClassRegistry::init('MetodoEnvio')->bodega_id($NuevaVenta['Venta']['metodo_envio_id']) ?? $bodega['Bodega']['id']; 

		# se obtiene el cliente
		$NuevaVenta['Venta']['venta_cliente_id'] = $this->MeliMarketplace->mercadolibre_obtener_cliente($ventaMeli);
		
		# Obtener mensajes de la venta
		$mensajes = $this->MeliMarketplace->mercadolibre_obtener_mensajes($marketplace['Marketplace']['access_token'], $ventaMeli['id']);

		foreach ($mensajes as $im => $mensaje) {

			$NuevaVenta['VentaMensaje'][$im]['nombre']   = (empty($mensaje['subject'])) ? 'Sin asunto' : $mensaje['subject'] ;
			$NuevaVenta['VentaMensaje'][$im]['fecha']    = CakeTime::format($mensaje['date'], '%Y-%m-%d %H:%M:%S');
			$NuevaVenta['VentaMensaje'][$im]['emisor']   = $mensaje['from']['user_id'];
			$NuevaVenta['VentaMensaje'][$im]['mensaje']  = $this->removeEmoji($mensaje['text']['plain']);

		}
		
		//ciclo para recorrer el detalle de la venta
		foreach ($ventaMeli['order_items'] as $DetalleVenta) {
			if (!empty($DetalleVenta['item']['seller_custom_field']) ) {
				
				$DetalleVenta['Sku']  = intval($DetalleVenta['item']['seller_custom_field']);
				$DetalleVenta['Name'] = $DetalleVenta['item']['title'];

				if ($DetalleVenta['Sku'] == 0) {
					$DetalleVenta['Sku'] = $DetalleVenta['item']['seller_sku'];
				}

				if ($DetalleVenta['Sku'] == 0) {
					continue;
				}

				//se guarda el producto si no existe
				$idNuevoProducto = $this->linio_guardar_producto($DetalleVenta);

				$NuevoDetalle                              = array();
				$NuevoDetalle['venta_detalle_producto_id'] = $idNuevoProducto;
				$NuevoDetalle['precio']                    = monto_neto(round($DetalleVenta['unit_price'], 2));
				$NuevoDetalle['cantidad']                  = $DetalleVenta['quantity'];
				$NuevoDetalle['precio_bruto']              = round($DetalleVenta['unit_price'], 2);				
				$NuevoDetalle['total_neto']                = $NuevoDetalle['precio'] * $DetalleVenta['quantity'];			
				$NuevoDetalle['total_bruto']               = monto_bruto($NuevoDetalle['total_neto']);
				$NuevaVenta['VentaDetalle'][]              = $NuevoDetalle;
				
			} // fin no empty
		
		} //fin ciclo detalle de venta
		
		//se guarda la venta
		$this->Venta->create();

		# Evitamos que se vuelva actualizar el stock en prestashop
		$excluirMeli = array('Mercadolibre' => array($marketplace_id));
		
		if ( $this->Venta->saveAll($NuevaVenta) ) {

			$tienda = ClassRegistry::init('Tienda')->obtener_tienda($marketplace['Marketplace']['tienda_id'], array('Tienda.activar_notificaciones', 'Tienda.notificacion_apikey'));

			if ($tienda['Tienda']['activar_notificaciones'] && !empty($tienda['Tienda']['notificacion_apikey'])) {
				$this->Pushalert = $this->Components->load('Pushalert');

				$this->Pushalert::$api_key = $tienda['Tienda']['notificacion_apikey'];

				$tituloPush = sprintf('Nueva venta en %s', $marketplace['Marketplace']['nombre']);
				$mensajePush = sprintf('Pincha aquí para verla');
				$urlPush = Router::url('/', true) . 'ventas/view/' . $this->Venta->id;

				$this->Pushalert->enviarNotificacion($tituloPush, $mensajePush, $urlPush);	
			}

			# si es un estado pagado se reserva el stock disponible
			if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['venta_estado_id']) && !ClassRegistry::init('VentaEstado')->es_estado_entregado($NuevaVenta['Venta']['venta_estado_id'])) {
				$this->Venta->pagar_venta($this->Venta->id);
				$this->actualizar_canales_stock($this->Venta->id, $excluirMeli);
			}

			return true;

		}else{
			return false;
		}
	}


	/**
	 * [crear_venta_prestashop description]
	 * @param  [type] $tienda_id  [description]
	 * @param  [type] $id_externo [description]
	 * @return [type]             [description]
	 */
	public function crear_venta_prestashop($tienda_id, $id_externo, $nuevo_estado = '')
	{	

		
		$tienda = ClassRegistry::init('Tienda')->find('first', array(
			'conditions' => array(
				'Tienda.id' => $tienda_id
			),
			'fields' => array(
				'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.activar_notificaciones', 'Tienda.notificacion_apikey', 'Tienda.nombre'
			)
		));

		$log = array();

		if (empty($tienda)) {
			return false;
		}

		# componente on the fly!
		$this->Prestashop = $this->Components->load('Prestashop');

		# Cliente Prestashop
		$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

		$nwVenta = $this->Prestashop->prestashop_obtener_venta($id_externo); 
		
		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop Crear Venta - Obtener venta',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($nwVenta)
			)
		);

		if (empty($nwVenta)) {
			return false;
		}

		if (empty($nuevo_estado)) {
			$nuevo_estado = $nwVenta['current_state'];
		}

		//datos de la venta a registrar
		$NuevaVenta                         = array();
		$NuevaVenta['Venta']['tienda_id']   = $tienda_id;
		$NuevaVenta['Venta']['id_externo']  = $id_externo;
		$NuevaVenta['Venta']['referencia']  = $nwVenta['reference'];
		$NuevaVenta['Venta']['fecha_venta'] = $nwVenta['date_add'];
		$NuevaVenta['Venta']['descuento']   = round($nwVenta['total_discounts_tax_incl'], 2);
		$NuevaVenta['Venta']['costo_envio'] = round($nwVenta['total_shipping_tax_incl'], 2);
		$NuevaVenta['Venta']['total']       = round($nwVenta['total_paid'], 2);
	
		//se obtienen las transacciones de una venta
		//si la venta tiene transacciones asociadas
		if ($VentaTransacciones = $this->Prestashop->prestashop_obtener_venta_transacciones($nwVenta['reference'])) {
			
			if (!isset($VentaTransacciones['order_payment'][0])) {
				$VentaTransacciones = array(
					'order_payment' => array(
						'0' => $VentaTransacciones['order_payment']
					)
				);
			}
			
			foreach ($VentaTransacciones['order_payment'] as $transaccion) {

				$NuevaTransaccion = array();

				if (!empty($transaccion['transaction_id'])) {
					$NuevaTransaccion['nombre'] = $transaccion['transaction_id'];
				}else{
					# Intenta volver a obtener el nombre
					$trans2 = $this->Prestashop->prestashop_obtener_transaccion($transaccion['id']);

					if (empty($trans2))
					{
						$trans2['order_payment']['transaction_id'] = 'undefined';
					}

					$NuevaTransaccion['nombre'] = $trans2['order_payment']['transaction_id'];
				}

				$NuevaTransaccion['monto'] = (!empty($transaccion['amount'])) ? $transaccion['amount'] : 0;

				$NuevaVenta['VentaTransaccion'][] = $NuevaTransaccion;
				
			}

		}
	
		# Direccion de entrega
		$direccionEntrega = $this->Prestashop->prestashop_obtener_venta_direccion($nwVenta['id_address_delivery']);
		
		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop Crear Venta - Direccion',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($direccionEntrega)
			)
		);

		// Dirección de entrega
		if (!isset($nwVenta['address'])) {
			
			$direccion_entrega = '';
			$numero_entrega    = '';
			$otro_entrega      = '';
			$comuna_entrega    = '';
			$ciudad_entrega    = '';
			$nombre_receptor   = '';
			$rut_receptor      = '';
			$fono_receptor     = '';

			# Calle/pasaje
			if (!empty($direccionEntrega['address']['address1'])) {

				if (is_array($direccionEntrega['address']['address1'])) {
					$direccionEntrega['address']['address1'] = implode(', ', $direccionEntrega['address']['address1']);
				}

				$direccion_entrega = $direccionEntrega['address']['address1'];
			}

			# Numero de casa/edificio
			if (!empty($direccionEntrega['address']['address2'])) {

				if (is_array($direccionEntrega['address']['address2'])) {
					$direccionEntrega['address']['address2'] = implode(', ', $direccionEntrega['address']['address2']);
				}

				$numero_entrega = $direccionEntrega['address']['address2'];
			}

			# Dpto/ofi/block
			if (!empty($direccionEntrega['address']['other'])) {
				if (is_array($direccionEntrega['address']['other'])) {
					$direccionEntrega['address']['other'] = implode(', ', $direccionEntrega['address']['other']);
				}
				$otro_entrega = $direccionEntrega['address']['other'];
			}

			# Ciudad declarada
			if (!empty($direccionEntrega['address']['city'])) {
				$ciudad_entrega = $direccionEntrega['address']['city'];
			}

			# Nombre del receptor
			if (!empty($direccionEntrega['address']['firstname'])) {
				$nombre_receptor .= $direccionEntrega['address']['firstname'] . ' ' . $direccionEntrega['address']['lastname'];
			}

			# Rut del receptor
			if (!empty($direccionEntrega['address']['dni'])) {
				$rut_receptor =  formato_rut($direccionEntrega['address']['dni']);
			}

			# Fono
			if (!empty($direccionEntrega['address']['phone_mobile'])) {
				$fono_receptor = trim($direccionEntrega['address']['phone_mobile']);
			}

			# Comuna seleccionada
			if (isset($direccionEntrega['address']['id_state'])) {
				$comuna_entrega = $this->Prestashop->prestashop_obtener_comuna_por_id($direccionEntrega['address']['id_state'])['state']['name'];
			}

			$comuna_id = ClassRegistry::init('Comuna')->obtener_id_comuna_por_nombre($comuna_entrega);

			$NuevaVenta['Venta']['direccion_entrega'] =  $direccion_entrega;
			$NuevaVenta['Venta']['numero_entrega']    =  $numero_entrega;
			$NuevaVenta['Venta']['otro_entrega']      =  $otro_entrega;
			$NuevaVenta['Venta']['rut_receptor']      =  $rut_receptor;
			$NuevaVenta['Venta']['comuna_entrega']    =  ClassRegistry::init('Comuna')->field('nombre', array('id' => $comuna_id));
			$NuevaVenta['Venta']['comuna_id']         =  $comuna_id;
			$NuevaVenta['Venta']['nombre_receptor']   =  $nombre_receptor;
			$NuevaVenta['Venta']['fono_receptor']     =  $fono_receptor;
		}
		
		//se obtienen el detalle de la venta
		$VentaDetalles = $this->Prestashop->prestashop_obtener_venta_detalles($nwVenta['id']);
		
		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop Crear Venta - Detalles',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($VentaDetalles)
			)
		);

		if (isset($VentaDetalles['order_detail']) && !isset($VentaDetalles['order_detail'][0])) {
			$VentaDetalles = array(
				'order_detail' => array(
					'0' => $VentaDetalles['order_detail']
				)
			);
		}

		//se obtiene el estado de la venta
		if (empty($nuevo_estado) || $nuevo_estado == 0) {
			$NuevaVenta['Venta']['venta_estado_id'] = 1; //Sin Estado
			$NuevaVenta['Venta']['estado_anterior'] = 1;
		}
		else {
			$NuevaVenta['Venta']['venta_estado_id'] = $this->Prestashop->prestashop_obtener_venta_estado($nuevo_estado);
			$NuevaVenta['Venta']['estado_anterior'] = 1;
		}

		$NuevaVenta['VentaEstado2'] = array(
			array(
				'venta_estado_id' => $NuevaVenta['Venta']['venta_estado_id'],
				'fecha'           => date('Y-m-d H:i:s'),
				'responsable'     => 'origen'
			)
		);

		$NuevaVenta['Venta']['metodo_envio_id']  = $this->Prestashop->prestashop_obtener_transportista($nwVenta['id_carrier']);
		
		$bodega 							     = ClassRegistry::init('Bodega')->obtener_bodega_principal();

		$NuevaVenta['Venta']['bodega_id'] 		 = ClassRegistry::init('MetodoEnvio')->bodega_id($NuevaVenta['Venta']['metodo_envio_id']) ?? $bodega['Bodega']['id']; 
		//se obtiene el medio de pago
		$NuevaVenta['Venta']['medio_pago_id']    = $this->Prestashop->prestashop_obtener_medio_pago($nwVenta['payment']);
		
		//se obtiene el cliente
		$NuevaVenta['Venta']['venta_cliente_id'] = $this->Prestashop->prestashop_obtener_cliente($nwVenta['id_customer']);

		// Existen ventas sin productos xD
		if (isset($VentaDetalles['order_detail'])) {
			//ciclo para recorrer el detalle de la venta
			foreach ($VentaDetalles['order_detail'] as $DetalleVenta) {
				if (!empty($DetalleVenta['product_id'])) {
					
					$NuevoDetalle = array();
					$NuevoDetalle['venta_detalle_producto_id']  = $DetalleVenta['product_id'];
					$NuevoDetalle['precio']                     = round($DetalleVenta['unit_price_tax_excl'], 2);
					$NuevoDetalle['precio_bruto']               = round($DetalleVenta['unit_price_tax_incl'], 2);
					$NuevoDetalle['cantidad']                   = $DetalleVenta['product_quantity'];
					$NuevoDetalle['cantidad_pendiente_entrega'] = $DetalleVenta['product_quantity'];
					$NuevoDetalle['cantidad_reservada'] 		= 0;
					$NuevoDetalle['total_neto']              	= $NuevoDetalle['precio'] * $NuevoDetalle['cantidad'];			
					$NuevoDetalle['total_bruto']				= monto_bruto($NuevoDetalle['total_neto']);

					# Atributos
					if ($DetalleVenta['product_attribute_id'])
					{
						$atributo_producto = $this->Prestashop->prestashop_obtener_atributo_producto($DetalleVenta['product_attribute_id']);
						$atributo = $this->Prestashop->prestashop_obtener_atributo($atributo_producto['combination']['associations']['product_option_values']['product_option_value']['id']);
						$combinacion = $this->Prestashop->prestashop_obtener_atributo_grupo($atributo['product_option_value']['id_attribute_group']);
						
						# Obtenemos la combinación local
						$combinacion_local = ClassRegistry::init('AtributoGrupo')->obtener_por_nombre($combinacion['product_option']['name']['language']);

						# Obtenemos el atributo local
						$atributo_local = ClassRegistry::init('Atributo')->obtener_por_nombre_grupo($atributo['product_option_value']['name']['language'], $combinacion_local['AtributoGrupo']['id']);
						$NuevoDetalle['Atributo'][] = array(
							'atributo_id' => $atributo_local['Atributo']['id'],
							'valor' => sprintf('%s - %s', $combinacion_local['AtributoGrupo']['nombre'], $atributo_local['Atributo']['nombre'])
						);
					}

					$NuevaVenta['VentaDetalle'][] = $NuevoDetalle;

					//se guarda el producto si no existe
					$this->prestashop_guardar_producto($DetalleVenta);

				}
				
			} //fin ciclo detalle de venta
		}

		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop Crear Venta - Guardar venta',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($NuevaVenta)
			)
		);

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);
		
		//se guarda la venta
		$this->Venta->create();
		if ( $this->Venta->saveAll($NuevaVenta) ) {

			if ($tienda['Tienda']['activar_notificaciones'] && !empty($tienda['Tienda']['notificacion_apikey'])) {
				$this->Pushalert = $this->Components->load('Pushalert');

				$this->Pushalert::$api_key = $tienda['Tienda']['notificacion_apikey'];

				$tituloPush = sprintf('Nueva venta en %s', $tienda['Tienda']['nombre']);
				$mensajePush = sprintf('Pincha aquí para verla');
				$urlPush = Router::url('/', true) . 'ventas/view/' . $this->Venta->id;

				$this->Pushalert->enviarNotificacion($tituloPush, $mensajePush, $urlPush);	
			}


			# si es un estado pagado se reserva el stock disponible
			if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($NuevaVenta['Venta']['venta_estado_id']) && !ClassRegistry::init('VentaEstado')->es_estado_entregado($NuevaVenta['Venta']['venta_estado_id'])) {
				# Flujo de venta pagda
				$this->Venta->pagar_venta($this->Venta->id);

				# Evitamos que se vuelva actualizar el stock en prestashop
				$excluirPrestashop = array('Prestashop' => array($tienda_id));
				$this->actualizar_canales_stock($this->Venta->id, $excluirPrestashop);
			}

			# Enviar email correspondiente
			$this->notificar_cambio_estado($this->Venta->id);


			return true;
		}else{
			return false;
		}

	}


	/**
	 * [actualizar_venta_prestashop description]
	 * @param  [type] $tienda_id    [description]
	 * @param  [type] $id_externo   [description]
	 * @param  [type] $nuevo_estado [description]
	 * @return [type]               [description]
	 */
	public function actualizar_venta_prestashop($tienda_id, $id_externo, $nuevo_estado = '')
	{
		$tienda = ClassRegistry::init('Tienda')->find('first', array(
			'conditions' => array(
				'Tienda.id' => $tienda_id
			),
			'fields' => array(
				'Tienda.apiurl_prestashop', 'Tienda.apikey_prestashop', 'Tienda.activar_notificaciones', 'Tienda.notificacion_apikey', 'Tienda.nombre', 'Tienda.configuracion'
			)
		));

		if (empty($tienda)) {
			return false;
		}

		$log = array();

		#Vemos si existe en la BD
		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id_externo'     => $id_externo,
				'Venta.tienda_id' 	   => $tienda_id
			),
			'fields' => array(
				'Venta.id', 'Venta.venta_estado_id'
			),
			'contain' => array(
				'VentaTransaccion',
				'VentaDetalle'
			)
		));

		# componente on the fly!
		$this->Prestashop = $this->Components->load('Prestashop');

		# Cliente Prestashop
		$this->Prestashop->crearCliente( $tienda['Tienda']['apiurl_prestashop'], $tienda['Tienda']['apikey_prestashop'] );

		$nwVenta = $this->Prestashop->prestashop_obtener_venta($id_externo, $tienda); 
	
		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop Crear Venta - Obtener venta',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($nwVenta)
			)
		);

		if (empty($nwVenta)) {
			return false;
		}

		if (empty($nuevo_estado)) {
			$nuevo_estado = $nwVenta['current_state'];
		}

		//datos de la venta a registrar
		$ActualizarVenta                        = array();
		$ActualizarVenta['Venta']['id']			= $venta['Venta']['id'];
		$ActualizarVenta['Venta']['descuento']   = round($nwVenta['total_discounts_tax_incl'], 2);
		$ActualizarVenta['Venta']['costo_envio'] = round($nwVenta['total_shipping_tax_incl'], 2);
		$ActualizarVenta['Venta']['total']       = round($nwVenta['total_paid'], 2);

		//se obtienen las transacciones de una venta
		//si la venta tiene transacciones asociadas
		if ($VentaTransacciones = $this->Prestashop->prestashop_obtener_venta_transacciones($nwVenta['reference'])) {

			if (!isset($VentaTransacciones['order_payment'][0])) {
				$VentaTransacciones = array(
					'order_payment' => array(
						'0' => $VentaTransacciones['order_payment']
					)
				);
			}
			
			foreach ($VentaTransacciones['order_payment'] as $transaccion) {

				$NuevaTransaccion = array();

				if (!empty($transaccion['transaction_id'])) {
					$NuevaTransaccion['nombre'] = $transaccion['transaction_id'];
				}else{
					$transaccion['transaction_id'] = 'undefined';
					$NuevaTransaccion['nombre'] = $transaccion['transaction_id'];
				}

				if (!empty($transaccion['transaction_id']) && Hash::check($venta, 'VentaTransaccion.{n}[nombre='.$transaccion['transaction_id'].'].id')) {
					continue;
				}

				$NuevaTransaccion['monto'] = (!empty($transaccion['amount'])) ? $transaccion['amount'] : 0;

				$ActualizarVenta['VentaTransaccion'][] = $NuevaTransaccion;
				
			}

		}

		# Direccion de entrega
		$direccionEntrega = $this->Prestashop->prestashop_obtener_venta_direccion($nwVenta['id_address_delivery']);

		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop Crear Venta - Direccion',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($direccionEntrega)
			)
		);

		// Dirección de entrega
		if (!isset($nwVenta['address'])) {
			
			$direccion_entrega = '';
			$numero_entrega    = '';
			$otro_entrega      = '';
			$comuna_entrega    = '';
			$ciudad_entrega    = '';
			$nombre_receptor   = '';
			$rut_receptor      = '';
			$fono_receptor     = '';

			# Calle/pasaje
			if (!empty($direccionEntrega['address']['address1'])) {

				if (is_array($direccionEntrega['address']['address1'])) {
					$direccionEntrega['address']['address1'] = implode(', ', $direccionEntrega['address']['address1']);
				}

				$direccion_entrega = $direccionEntrega['address']['address1'];
			}

			# Numero de casa/edificio
			if (!empty($direccionEntrega['address']['address2'])) {

				if (is_array($direccionEntrega['address']['address2'])) {
					$direccionEntrega['address']['address2'] = implode(', ', $direccionEntrega['address']['address2']);
				}

				$numero_entrega = $direccionEntrega['address']['address2'];
			}

			# Dpto/ofi/block
			if (!empty($direccionEntrega['address']['other'])) {
				if (is_array($direccionEntrega['address']['other'])) {
					$direccionEntrega['address']['other'] = implode(', ', $direccionEntrega['address']['other']);
				}
				$otro_entrega = $direccionEntrega['address']['other'];
			}

			# Ciudad declarada
			if (!empty($direccionEntrega['address']['city'])) {
				$ciudad_entrega = $direccionEntrega['address']['city'];
			}

			# Nombre del receptor
			if (!empty($direccionEntrega['address']['firstname'])) {
				$nombre_receptor .= $direccionEntrega['address']['firstname'] . ' ' . $direccionEntrega['address']['lastname'];
			}

			# Rut del receptor
			if (!empty($direccionEntrega['address']['dni'])) {
				$rut_receptor =  formato_rut($direccionEntrega['address']['dni']);
			}

			# Fono
			if (!empty($direccionEntrega['address']['phone_mobile'])) {
				$fono_receptor = trim($direccionEntrega['address']['phone_mobile']);
			}

			# Comuna seleccionada
			if (isset($direccionEntrega['address']['id_state'])) {
				$comuna_entrega = $this->Prestashop->prestashop_obtener_comuna_por_id($direccionEntrega['address']['id_state'])['state']['name'];
			}

			$comuna_id = ClassRegistry::init('Comuna')->obtener_id_comuna_por_nombre($comuna_entrega);

			$ActualizarVenta['Venta']['direccion_entrega'] =  $direccion_entrega;
			$ActualizarVenta['Venta']['numero_entrega']    =  $numero_entrega;
			$ActualizarVenta['Venta']['otro_entrega']      =  $otro_entrega;
			$ActualizarVenta['Venta']['rut_receptor']      =  $rut_receptor;
			$ActualizarVenta['Venta']['comuna_entrega']    =  ClassRegistry::init('Comuna')->field('nombre', array('id' => $comuna_id));
			$ActualizarVenta['Venta']['comuna_id']    =  $comuna_id;
			$ActualizarVenta['Venta']['nombre_receptor']   =  $nombre_receptor;
			$ActualizarVenta['Venta']['fono_receptor']     =  $fono_receptor;
		}

		//se obtienen el detalle de la venta
		$VentaDetalles = $this->Prestashop->prestashop_obtener_venta_detalles($nwVenta['id']);

		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop Crear Venta - Detalles',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($VentaDetalles)
			)
		);

		if (isset($VentaDetalles['order_detail']) && !isset($VentaDetalles['order_detail'][0])) {
			$VentaDetalles = array(
				'order_detail' => array(
					'0' => $VentaDetalles['order_detail']
				)
			);
		}

		// Existen ventas sin productos xD
		if (isset($VentaDetalles['order_detail'])) {
			//ciclo para recorrer el detalle de la venta
			foreach ($VentaDetalles['order_detail'] as $DetalleVenta) {
				if (!empty($DetalleVenta['product_id'])) {

					if (!Hash::check($venta, 'VentaDetalle.{n}[venta_detalle_producto_id='.$DetalleVenta['product_id'].'].id')) {
						
						$NuevoDetalle = array();
						$NuevoDetalle['venta_detalle_producto_id']  = $DetalleVenta['product_id'];
						$NuevoDetalle['precio']                     = round($DetalleVenta['unit_price_tax_excl'], 2);
						$NuevoDetalle['precio_bruto']               = round($DetalleVenta['unit_price_tax_incl'], 2);
						$NuevoDetalle['cantidad']                   = $DetalleVenta['product_quantity'];
						$NuevoDetalle['cantidad_pendiente_entrega'] = $DetalleVenta['product_quantity'];
						$NuevoDetalle['cantidad_reservada'] 		= 0;
						$NuevoDetalle['total_neto']              	= $NuevoDetalle['precio'] * $NuevoDetalle['cantidad'];			
						$NuevoDetalle['total_bruto']				= monto_bruto($NuevoDetalle['total_neto']);

						# Atributos
						if ($DetalleVenta['product_attribute_id'])
						{
							$atributo_producto = $this->Prestashop->prestashop_obtener_atributo_producto($DetalleVenta['product_attribute_id']);
							$atributo = $this->Prestashop->prestashop_obtener_atributo($atributo_producto['combination']['associations']['product_option_values']['product_option_value']['id']);
							$combinacion = $this->Prestashop->prestashop_obtener_atributo_grupo($atributo['product_option_value']['id_attribute_group']);
							
							# Obtenemos la combinación local
							$combinacion_local = ClassRegistry::init('AtributoGrupo')->obtener_por_nombre($combinacion['product_option']['name']['language']);

							# Obtenemos el atributo local
							$atributo_local = ClassRegistry::init('Atributo')->obtener_por_nombre_grupo($atributo['product_option_value']['name']['language'], $combinacion_local['AtributoGrupo']['id']);
							$NuevoDetalle['Atributo'][] = array(
								'atributo_id' => $atributo_local['Atributo']['id'],
								'valor' => sprintf('%s - %s', $combinacion_local['AtributoGrupo']['nombre'], $atributo_local['Atributo']['nombre'])
							);
						}

						$ActualizarVenta['VentaDetalle'][] = $NuevoDetalle;

						//se guarda el producto si no existe
						$this->prestashop_guardar_producto($DetalleVenta);

					}

				}
				
			} //fin ciclo detalle de venta
		}

		$ActualizarVenta['Venta']['estado_anterior']          = $venta['Venta']['venta_estado_id'];
		$ActualizarVenta['Venta']['venta_estado_id']          = $this->Prestashop->prestashop_obtener_venta_estado($nuevo_estado);
		$ActualizarVenta['Venta']['venta_estado_responsable'] = 'Prestashop Webhook';

		$ActualizarVenta['VentaEstado2'] = array(
			array(
				'venta_estado_id' => $ActualizarVenta['Venta']['venta_estado_id'],
				'fecha'           => date('Y-m-d H:i:s'),
				'responsable'     => 'origen'
			)
		);

		

		$ActualizarVenta['Venta']['metodo_envio_id']  = $this->Prestashop->prestashop_obtener_transportista($nwVenta['id_carrier']);

		$bodega 							          = ClassRegistry::init('Bodega')->obtener_bodega_principal();

		$ActualizarVenta['Venta']['bodega_id'] 		  = ClassRegistry::init('MetodoEnvio')->bodega_id($ActualizarVenta['Venta']['metodo_envio_id']) ?? $bodega['Bodega']['id']; 

		//se obtiene el medio de pago
		$ActualizarVenta['Venta']['medio_pago_id']    = $this->Prestashop->prestashop_obtener_medio_pago($nwVenta['payment']);
		
		//se obtiene el cliente
		$ActualizarVenta['Venta']['venta_cliente_id'] = $this->Prestashop->prestashop_obtener_cliente($nwVenta['id_customer']);
		

		# Evitamos que se vuelva actualizar el stock en prestashop
		$excluirPrestashop = array('Prestashop' => array($tienda_id));

		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop Actualizar Venta',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($ActualizarVenta)
			)
		);
		
		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);
		
		//se guarda la venta
		if ( $this->Venta->saveAll($ActualizarVenta) ){

			if ($tienda['Tienda']['activar_notificaciones'] && !empty($tienda['Tienda']['notificacion_apikey'])) {
				$this->Pushalert = $this->Components->load('Pushalert');

				$this->Pushalert::$api_key = $tienda['Tienda']['notificacion_apikey'];

				$tituloPush = sprintf('Actualización de venta en %s', $tienda['Tienda']['nombre']);
				$mensajePush = sprintf('La venta #%d cambió a %s', $venta['Venta']['id'], ClassRegistry::init('VentaEstado')->obtener_estado_por_id($ActualizarVenta['Venta']['venta_estado_id'])['VentaEstado']['nombre'] );
				$urlPush = Router::url('/', true) . 'ventas/view/' . $venta['Venta']['id'];

				$this->Pushalert->enviarNotificacion($tituloPush, $mensajePush, $urlPush);	
			}

			# si es un estado pagado se reserva el stock disponible
			if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($ActualizarVenta['Venta']['venta_estado_id']) && !ClassRegistry::init('VentaEstado')->es_estado_entregado($ActualizarVenta['Venta']['venta_estado_id'])) {
				$this->Venta->pagar_venta($venta['Venta']['id']);
				$this->actualizar_canales_stock($venta['Venta']['id'], $excluirPrestashop);
			}

			# se entrega la venta
			if ( ClassRegistry::init('VentaEstado')->es_estado_pagado($ActualizarVenta['Venta']['venta_estado_id']) && ClassRegistry::init('VentaEstado')->es_estado_entregado($ActualizarVenta['Venta']['venta_estado_id'])) {
				$this->Venta->entregar($venta['Venta']['id']);
			}

			# si es un estado rechazo se devuelve el stock disponible
			if ( ClassRegistry::init('VentaEstado')->es_estado_rechazo($ActualizarVenta['Venta']['venta_estado_id']) && !ClassRegistry::init('VentaEstado')->es_estado_cancelado($ActualizarVenta['Venta']['venta_estado_id']) ) {
				$this->Venta->revertir_venta($venta['Venta']['id']);
				$this->actualizar_canales_stock($venta['Venta']['id'], $excluirPrestashop);
			}

			if ( ClassRegistry::init('VentaEstado')->es_estado_cancelado($ActualizarVenta['Venta']['venta_estado_id']) ) {
				$this->Venta->cancelar_venta($venta['Venta']['id']);
				$this->WarehouseNodriza->procesar_embalajes($venta['Venta']['id']);
				$this->actualizar_canales_stock($venta['Venta']['id'], $excluirPrestashop);
			}

			return true;
		}else{
			return false;
		}
	}


	/**
	 * Prepara la información para mostrar de forma ordenada
	 * los estados de venta y envio
	 * 
	 * @param $venta
	 * @return array
	 */
	public function preparar_estados($venta)
	{
		$estados = array(
			'SubEstados' => array(
				'inicial' => array(
					'actual' => true,
					'alias' => 'Pago aceptado',
					'descripcion' => '',
					'check' => false
				),
				'preparacion' => array(
					'actual' => false,
					'alias' => 'En preparación',
					'descripcion' => '',
					'check' => false
				),
				'preparado' => array(
					'actual' => false,
					'alias' => 'Procesado',
					'descripcion' => '',
					'check' => false
				),
				'entregado' => array(
					'actual' => false,
					'alias' => 'Entregado',
					'descripcion' => '',
					'check' => false
				)
			)
		);

		$textos = array(
			'pago_en_espera'    => 'Aún no hemos recibido su pago.',
			'pago_aceptado'    => 'Su pago ha sido aceptado.',
			'pago_rechazado'   => 'Su pago a sido rechazado o cancelado.',
			'en_preparacion'   => 'Su pedido esta siendo preparado en nuestra bodega.',
			'retiro_en_tienda' => 'Su pedido esta listo para retiro.',
			'listo_para_envio' => 'Su pedido esta listo para despacho.',
			'enviado'          => 'Su pedido fue enviado por la empresa de transporte seleccionada.',
			'entregado'        => 'Su pedido fue entregado correctamente.',
			'procesado'        => 'Su pedido fue enviado o retirado desde nuestra bodega.',
		);

		# Inicial
		if ($venta['VentaEstado']['VentaEstadoCategoria']['venta'])
		{
			$estados['SubEstados']['inicial']['descripcion'] = $textos['pago_aceptado'];
			$estados['SubEstados']['inicial']['check'] = true;
		}
		
		if ($venta['VentaEstado']['VentaEstadoCategoria']['rechazo'])
		{	
			$estados['SubEstados']['inicial']['alias'] = 'Pago rechazado';
			$estados['SubEstados']['inicial']['descripcion'] = $textos['pago_rechazado'];
		}

		if (!$venta['VentaEstado']['VentaEstadoCategoria']['venta']
			&& !$venta['VentaEstado']['VentaEstadoCategoria']['rechazo'])
		{	
			$estados['SubEstados']['inicial']['alias'] = 'Pago en espera';
			$estados['SubEstados']['inicial']['descripcion'] = $textos['pago_en_espera'];
		}
		
		# Preparación
		if (in_array($venta['Venta']['picking_estado'], array('empaquetar', 'empaquetando'))
			&& $venta['VentaEstado']['VentaEstadoCategoria']['venta'])
		{	

			$estados['SubEstados']['inicial']['actual'] = false;
			$estados['SubEstados']['inicial']['check'] = true;

			$estados['SubEstados']['preparacion']['actual'] = true;
			$estados['SubEstados']['preparacion']['descripcion'] = $textos['en_preparacion'];
			$estados['SubEstados']['preparacion']['check'] = true;
			
		}

		# Preparado retiro en tienda
		if ($venta['Venta']['picking_estado'] == 'empaquetado'
			&& $venta['VentaEstado']['VentaEstadoCategoria']['venta']
			&& $venta['VentaEstado']['VentaEstadoCategoria']['retiro_en_tienda'])
		{	

			$estados['SubEstados']['inicial']['actual'] = false;
			$estados['SubEstados']['inicial']['check'] = true;

			$estados['SubEstados']['preparacion']['actual'] = false;
			$estados['SubEstados']['preparacion']['descripcion'] = $textos['en_preparacion'];
			$estados['SubEstados']['preparacion']['check'] = true;

			$estados['SubEstados']['preparado']['actual'] = true;
			$estados['SubEstados']['preparado']['alias'] = $venta['VentaEstado']['VentaEstadoCategoria']['nombre'];
			$estados['SubEstados']['preparado']['descripcion'] = $textos['retiro_en_tienda'];
			$estados['SubEstados']['preparado']['check'] = true;

		}


		# Preparado listo para envio
		if ($venta['Venta']['picking_estado'] == 'empaquetado'
			&& $venta['VentaEstado']['VentaEstadoCategoria']['venta']
			&& $venta['VentaEstado']['VentaEstadoCategoria']['listo_para_envio'])
		{	

			$estados['SubEstados']['inicial']['actual'] = false;
			$estados['SubEstados']['inicial']['check'] = true;

			$estados['SubEstados']['preparacion']['actual'] = false;
			$estados['SubEstados']['preparacion']['descripcion'] = $textos['en_preparacion'];
			$estados['SubEstados']['preparacion']['check'] = true;

			$estados['SubEstados']['preparado']['actual'] = true;
			$estados['SubEstados']['preparado']['alias'] = $venta['VentaEstado']['VentaEstadoCategoria']['nombre'];
			$estados['SubEstados']['preparado']['descripcion'] = $textos['listo_para_envio'];
			$estados['SubEstados']['preparado']['check'] = true;

		}

		# Preparado enviado
		if ($venta['Venta']['picking_estado'] == 'empaquetado'
			&& $venta['VentaEstado']['VentaEstadoCategoria']['venta']
			&& $venta['VentaEstado']['VentaEstadoCategoria']['envio'])
		{	

			$estados['SubEstados']['inicial']['actual'] = false;
			$estados['SubEstados']['inicial']['check'] = true;

			$estados['SubEstados']['preparacion']['actual'] = false;
			$estados['SubEstados']['preparacion']['descripcion'] = $textos['en_preparacion'];
			$estados['SubEstados']['preparacion']['check'] = true;
			
			$estados['SubEstados']['preparado']['actual'] = true;
			$estados['SubEstados']['preparado']['alias'] = $venta['VentaEstado']['VentaEstadoCategoria']['nombre'];
			$estados['SubEstados']['preparado']['descripcion'] = $textos['enviado'];
			$estados['SubEstados']['preparado']['check'] = true;

		}


		# Entregado
		if ($venta['Venta']['picking_estado'] == 'empaquetado'
			&& $venta['VentaEstado']['VentaEstadoCategoria']['venta']
			&& $venta['VentaEstado']['VentaEstadoCategoria']['final'])
		{	

			$estados['SubEstados']['inicial']['actual'] = false;
			$estados['SubEstados']['inicial']['check'] = true;

			$estados['SubEstados']['preparacion']['actual'] = false;
			$estados['SubEstados']['preparacion']['descripcion'] = $textos['en_preparacion'];
			$estados['SubEstados']['preparacion']['check'] = true;

			$estados['SubEstados']['preparado']['actual'] = false;
			$estados['SubEstados']['preparado']['descripcion'] = $textos['procesado'];
			$estados['SubEstados']['preparado']['check'] = true;

			$estados['SubEstados']['entregado']['actual'] = true;
			$estados['SubEstados']['entregado']['descripcion'] = $textos['entregado'];
			$estados['SubEstados']['entregado']['check'] = true;

		}

		return $estados;
	}


	/**
	 * Clients
	 */
	public function cliente_compras()
	{	

		$paginate = array(
			'recursive' => 0,
			'conditions' => array(
				'Venta.venta_cliente_id' => $this->Auth->user('id'),
			),
			'contain' => array(
				'VentaDetalle' => array(
					'fields' => array(
						'VentaDetalle.cantidad', 'VentaDetalle.cantidad_anulada'
					)
				),
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.nombre', 'VentaEstado.venta_estado_categoria_id'
					),
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.nombre', 'VentaEstadoCategoria.estilo', 'VentaEstadoCategoria.venta', 'VentaEstadoCategoria.final'
						)
					)
				), 
				'Dte' => array(
					'fields' => array(
						'Dte.id', 'Dte.tipo_documento', 'Dte.invalidado', 'Dte.estado', 'Dte.pdf'
					)
				)
			),
			'fields' => array('Venta.id', 'Venta.fecha_venta', 'Venta.total', 'Venta.venta_estado_id', 'Venta.picking_estado', 'Venta.referencia'),
			'order' => array('Venta.fecha_venta' => 'DESC'),
			'limit' => 20
		);

		//----------------------------------------------------------------------------------------------------
		$this->paginate = $paginate;

		$ventas = $this->paginate();

		# Total comprado
		foreach ($ventas as $iv => $v) {	
			if (!empty($v['Dte'])) {
				$ventas[$iv]['Dte'] = $this->obtener_dtes_pdf_venta($v['Dte']);
			}
		}

		$this->layout = 'private';

		BreadcrumbComponent::add('Dashboard', '/cliente');
		BreadcrumbComponent::add('Mis compras', '/cliente/mis-compras');
		$PageTitle = 'Mis compras';
		$this->set(compact('PageTitle', 'ventas'));
	}


	public function cliente_ver($id)
	{
		$venta = $this->preparar_venta($id);
		$this->layout = 'private';

		BreadcrumbComponent::add('Dashboard', '/cliente');
		BreadcrumbComponent::add('Mis compras', '/cliente/mis-compras');
		BreadcrumbComponent::add('Compra ref: ' . $venta['Venta']['referencia'], '/mis-compras/' . $id);

		$PageTitle = 'Compra ref:' . $venta['Venta']['referencia'];

		$tab_activo = 'venta';

		if (isset($this->request->query['tab'])) {
			$tab_activo = $this->request->query['tab'];
		}


		$this->set(compact('PageTitle', 'venta', 'tab_activo'));
	}


	/**
	 * Métodos REST
	 */
	public function api_index()
	{
		$token = '';

    	if (isset($this->request->query['token'])) {
    		$token = $this->request->query['token'];
    	}

    	if (!$this->request->is('get')) {
			$response = array(
				'code'    => 510, 
				'message' => 'Method not allowed'
			);

			throw new CakeException($response);
		}

    	# Existe token
		if (!isset($token)) {
			$response = array(
				'code'    => 502, 
				'message' => 'Expected Token'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($token)) {
			$response = array(
				'code'    => 505, 
				'message' => 'Invalid or expired Token'
			);

			throw new CakeException($response);
		}

    	$qry = array(
    		'order' => array('Venta.fecha_venta' => 'desc'),
    		'contain' => array(
    			'VentaEstado' => array(
    				'VentaEstadoCategoria'
    			),
    			'MetodoEnvio',
    			'VentaDetalle' => array(
    				'VentaDetalleProducto'
    			)
    		)
    	);

    	$paginacion = array(
        	'limit' => 0,
        	'offset' => 0,
        	'total' => 0
        );

    	if (isset($this->request->query['id'])) {
    		if (!empty($this->request->query['id'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array('Venta.id' => $this->request->query['id'])));
    		}
    	}

    	if (isset($this->request->query['id_externo'])) {
    		if (!empty($this->request->query['id_externo'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array('Venta.id_externo' => $this->request->query['id_externo'])));
    		}
    	}

    	if (isset($this->request->query['venta_cliente_id'])) {
    		if (!empty($this->request->query['venta_cliente_id'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array('Venta.venta_cliente_id' => $this->request->query['venta_cliente_id'])));
    		}
    	}

    	if (isset($this->request->query['desde'])) {
    		if (!empty($this->request->query['desde'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array('Venta.fecha_venta >=' => $this->request->query['desde'])));
    		}
    	}

    	if (isset($this->request->query['hasta'])) {
    		if (!empty($this->request->query['hasta'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array('Venta.fecha_venta <=' => $this->request->query['hasta'])));
    		}
    	}

    	if (isset($this->request->query['direction'])) {
    		if (!empty($this->request->query['direction'])) {
    			$qry = array_replace_recursive($qry, array('order' => array('Venta.fecha_venta' => $this->request->query['direction'])));
    		}
    	}

    	if (isset($this->request->query['limit'])) {
    		if (!empty($this->request->query['limit'])) {
    			$qry = array_replace_recursive($qry, array('limit' => $this->request->query['limit']));
    			$paginacion['limit'] = $this->request->query['limit'];
    		}
    	}

    	if (isset($this->request->query['offset'])) {
    		if (!empty($this->request->query['offset'])) {
    			$qry = array_replace_recursive($qry, array('offset' => $this->request->query['offset']));
    			$paginacion['offset'] = $this->request->query['offset'];
    		}
    	}

    	if (isset($this->request->query['estado'])) {
    		if (!empty($this->request->query['estado'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'Venta.venta_estado_id' => $this->request->query['estado'] )));
    		}
    	}

    	if (isset($this->request->query['envio'])) {
    		if (!empty($this->request->query['envio'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'Venta.metodo_envio_id' => $this->request->query['envio'] )));
    		}
    	}

    	if (isset($this->request->query['picking'])) {
    		if (!empty($this->request->query['picking'])) {
    			$qry = array_replace_recursive($qry, array('conditions' => array( 'Venta.picking_estado' => $this->request->query['picking'] )));
    		}
    	}
   
        $ventas = $this->Venta->find('all', $qry);

        $paginacion['total'] = count($ventas);

        $this->set(array(
            'ventas' => $ventas,
            'paginacion' => $paginacion,
            '_serialize' => array('ventas', 'paginacion')
        ));
	}


	/**
	 * Obtener venta por referencia
	 * @param string $referencia Referencia de la venta
	 * @return mixed
	 */
	public function api_ver_por_referencia()
	{
		# Sólo método Get
		if (!$this->request->is('get')) {
			$response = array(
				'response' => array(
					'code'    => 401, 
					'message' => 'Only GET request allow'
				)
			);

			throw new CakeException($response);
		}

		if (!isset($this->request->query['referencia'])) {
			
			$response = array(
				'response' => array(
					'code'    => 401, 
					'name' => 'error',
					'message' => 'referencia es requerido'
				)
			);

			throw new CakeException($response);
		}

		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'response' => array(
					'code'    => 401, 
					'name' => 'error',
					'message' => 'Token requerido'
				)
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'response' => array(
					'code'    => 401, 
					'name' => 'error',
					'message' => 'Token de sesión expirado o invalido'
				)
			);			

			throw new CakeException($response);
		}

		$qry = array(
			'conditions' => array(
				'OR' => array(
					array('Venta.id LIKE' => trim($this->request->query['referencia'])),
					array('Venta.referencia' => trim($this->request->query['referencia']))
				)
			),
			'contain' => array(
				'Tienda' => array(
					'fields' => array(
						'Tienda.id',
						'Tienda.nombre'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.id',
						'Marketplace.nombre'
					)
				),
				'VentaEstado' => array(
					'fields' => array(
						'VentaEstado.id',
						'VentaEstado.nombre',
						'VentaEstado.venta_estado_categoria_id'
					),
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.id',
							'VentaEstadoCategoria.nombre',
							'VentaEstadoCategoria.estilo',
							'VentaEstadoCategoria.venta',
							'VentaEstadoCategoria.envio',
							'VentaEstadoCategoria.rechazo',
							'VentaEstadoCategoria.final',
							'VentaEstadoCategoria.listo_para_envio',
							'VentaEstadoCategoria.retiro_en_tienda'
						)
					)
				),
				'VentaDetalle' => array(
					'fields' => array(
						'VentaDetalle.id',
						'VentaDetalle.venta_detalle_producto_id',
						'VentaDetalle.precio',
						'VentaDetalle.cantidad'
					)
				),
				'TransportesVenta' => array(
					'Transporte' => array(
						'fields' => array(
							'Transporte.nombre'
						)
					),
					'EnvioHistorico' => array(
						'EstadoEnvio' => array(
							'EstadoEnvioCategoria' => array(
								'fields' => array(
									'EstadoEnvioCategoria.nombre',
									'EstadoEnvioCategoria.clase'
								)
							),
							'fields' => array(
								'EstadoEnvio.nombre',
								'EstadoEnvio.origen',
								'EstadoEnvio.leyenda'
							)
						),
						'fields' => array(
							'EnvioHistorico.created'
						)
					)
				)

			),
			'fields' => array(
				'Venta.id',
				'Venta.id_externo',
				'Venta.referencia',
				'Venta.venta_estado_id',
				'Venta.picking_estado',
				'Venta.tienda_id',
				'Venta.marketplace_id'
			)
		);

		# Buscamos la venta
		$venta = $this->Venta->find('first', $qry);
		
		$respuesta = array(
			'code'    => 404, 
			'name' => 'error',
			'message' => 'Venta no encontrada'
		);

		# Existe
		if (!empty($venta)) {

			$venta = array_replace_recursive($venta, $this->preparar_estados($venta));

			$respuesta = array(
				'code'    => 200,
				'name'    => 'success',
				'message' => 'Venta encontrada',
				'data'    => $venta
			);
		}

		$this->set(array(
			'response' => $respuesta,
			'_serialize' => array('response')
		));
	}


	public function api_venta_existe_externo($id)
	{
		# Sólo método Get
		if (!$this->request->is('get')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only GET request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id_externo' => $id
			),
			'fields' => array(
				'Venta.id'
			)
		));

		# No existe venta
		if (empty($venta)) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			throw new CakeException($response);
		}else{
			$this->set(array(
	            'response' => array(
	            	'code' => 200,
	            	'name' => 'success',
	            	'message' => 'Venta existe'
	            ),
	            '_serialize' => array('response')
	        ));
		}
	}


	/**
	 * Enpoint /api/ventas/:id.json
	 * @param  [type] $id Identificador de la venta
	 * @return mixed
	 */
	public function api_obtener_venta($id)
	{	
		# Sólo método Get
		if (!$this->request->is('get')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only GET request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			throw new CakeException($response);
		}

		# Detalles de la venta
		$venta = $this->preparar_venta($id);

		if ($venta['Venta']['canal_venta_id'])
		{
			$venta['Tienda']['nombre'] = $venta['CanalVenta']['nombre'] . ' - ' . $venta['Tienda']['nombre'];
		}

		$respuesta =  array(
			'cliente' => array(
				'rut'      => $venta['VentaCliente']['rut'],
				'nombre'   => $venta['VentaCliente']['nombre'],
				'apellido' => $venta['VentaCliente']['apellido'],
				'email'    => $venta['VentaCliente']['email'],
				'fono'     => $venta['VentaCliente']['telefono'],
			),
			'venta' => array(
				'id'              => $venta['Venta']['id'],
				'id_externo'      => $venta['Venta']['id_externo'],
				'referencia'      => $venta['Venta']['referencia'],
				'fecha_venta'     => $venta['Venta']['fecha_venta'],
				'total'           => $venta['Venta']['total'],
				'total_clp'       => CakeNumber::currency($venta['Venta']['total'], 'CLP'),
				'descuento'   	  => $venta['Venta']['descuento'],
				'descuento_clp'   => CakeNumber::currency($venta['Venta']['descuento'], 'CLP'),
				'costo_envio' 	  => $venta['Venta']['costo_envio'],
				'costo_envio_clp' => CakeNumber::currency($venta['Venta']['costo_envio'], 'CLP'),
				'estado'          => $venta['VentaEstado']['VentaEstadoCategoria']['nombre'],
				'subestado'       => $venta['VentaEstado']['nombre'],
				'canal_venta'     => (!empty($venta['Marketplace']['id'])) ? $venta['Marketplace']['nombre'] : $venta['Tienda']['nombre'], 
			),
			'entrega' => array(
				'metodo'                 => $venta['MetodoEnvio']['nombre'],
				'fecha_entrega_estimada' => 'No definido',
			),
			'itemes' => array(),
			'confirm_url' => array(
				'host' => Router::url('/', true),
				'endpoint' => sprintf('api/ventas/change_state/%d.json', $id),
				'required_params' => array(
					'type' => '',
					'token' => 'your access token'
				)
			),
		);


		foreach ($venta['VentaDetalle'] as $i => $item) {

			$total_items = $item['cantidad'];

			# si la cantidad de items es 0 se quita la línea
			#if ($total_items == 0)
				#continue;

			$respuesta['itemes'][$i] = array(
				'id'               			 => $item['id'],
				'producto_id'                => $item['VentaDetalleProducto']['id'],
				'nombre'           			 => $item['VentaDetalleProducto']['nombre'],
				'cantidad'         			 => $total_items,
				'precio_neto_clp'      		 => CakeNumber::currency($item['precio'], 'CLP'),
				'precio_neto'      			 => $item['precio'],
				'precio_bruto'     			 => $this->precio_bruto($item['precio']),
				'precio_bruto_clp' 			 => CakeNumber::currency($this->precio_bruto($item['precio']), 'CLP'),
				'codigo_barra'     			 => null,
				'cantidad_pendiente_entrega' => $item['cantidad_pendiente_entrega'],
				'cantidad_entregada' 		 => $item['cantidad_entregada'],
			);
			
			if (!empty($item['VentaDetalleProducto']['imagenes'])) {
				$respuesta['itemes'][$i] = array_replace_recursive($respuesta['itemes'][$i], array(
					'imagen' => Hash::extract($item['VentaDetalleProducto']['imagenes'], '{n}[principal=1].url')[0]
				));
			}
		}
		
		# si la venta no tiene items se retorna un error
		if (empty($respuesta['itemes'])){
			$response = array(
				'code'    => 506, 
				'name' => 'error',
				'message' => 'Venta no aplica para picking'
			);

			throw new CakeException($response);
		}

		$this->set(array(
            'response' => $respuesta,
            '_serialize' => array('response')
        ));

	}


	/**
	 * Retorna una venta
	 */
	public function api_obtener_venta_por_id($id)
	{	
		# Sólo método Get
		if (!$this->request->is('get')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only GET request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			throw new CakeException($response);
		}

		# Detalles de la venta
		$venta = $this->Venta->obtener_venta_por_id_tiny($id);

		$respuesta = array(
			'code' => 200,
			'message' => 'Venta obtenida con éxito',
			'data' => $venta
		);

		$this->set(array(
            'response' => $respuesta,
            '_serialize' => array('response')
        ));

	}

	
	/**
	 * generar_documentos
	 *
	 * @param  mixed $venta
	 * @return void
	 */
	public function generar_documentos($venta)
	{	

		# Variable que contendrá los documentos
		$archivos = array();
		
		# Linio
		if ($venta['Marketplace']['marketplace_tipo_id'] == 1) 
		{
			# cliente Linio
			$this->Linio = $this->Components->load('Linio');
			$this->Linio->crearCliente( $venta['Marketplace']['api_host'], $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'] );

			//$mensajes =  $this->Linio->linio_obtener_venta_mensajes($venta, $ConexionLinio);

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Linio->linio_obtener_venta($venta['Venta']['id_externo'], true);

			// Datos d facturacion
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);

			// Se define transportista
			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

			// Detalles de envio
			$venta['Envio'][0] = array(
				'id'                      => null,
				'tipo'                    => null,
				'estado'                  => null,
				'direccion_envio'         => sprintf('%s, %s, %s', $venta['VentaExterna']['AddressShipping']['Address1'], $venta['VentaExterna']['AddressShipping']['Address2'], $venta['VentaExterna']['AddressShipping']['City']),
				'nombre_receptor'         => sprintf('%s %s', $venta['VentaExterna']['AddressShipping']['FirstName'], $venta['VentaExterna']['AddressShipping']['LastName']),
				'fono_receptor'           => $venta['VentaExterna']['AddressShipping']['Phone'],
				'producto'                => null,
				'cantidad'                => 1, // No especifica
				'costo'                   => 0,
				'fecha_entrega_estimada'  => $venta['VentaExterna']['PromisedShippingTime'],
				'comentario'              => '',
				'mostrar_etiqueta'        => false,
				'paquete' 				  => false
			);

			$documentoEnvio   = $this->Linio->linio_obtener_documentos(Hash::extract($venta['VentaExterna']['Products'], '{n}.OrderItemId'), 'shippingParcel');
			$documentoInvoice = $this->Linio->linio_obtener_documentos(Hash::extract($venta['VentaExterna']['Products'], '{n}.OrderItemId'), 'invoice');
			
			$rutaAbsoluta = APP . 'webroot' . DS. 'Venta' . DS . $id . DS;
			$rutaPublica  =  Router::url('/', true) . 'Venta/' . $id . '/';

			# Invoice Linio
			if (!empty($documentoInvoice)) 
			{
				$invoice = $this->generar_pdf(base64_decode($documentoInvoice['pdf']), $id, 'invoice');
		 		
		 		if (!empty($invoice)) 
				{
		 			$archivos[] = $invoice['path'];
		 		}

			}

			# Doc tranportista linio
			if (!empty($documentoEnvio)) 
			{
				$archivoPdfEnvio = 'transporte' . rand() . '.pdf';

				$documentoEnvioPdfs = $this->guardar_pdf_base64($documentoEnvio['pdf'], $rutaAbsoluta, $rutaPublica, $archivoPdfEnvio);
		 		
		 		if (!empty($documentoEnvioPdfs)) 
				{
		 			$archivos[] = $documentoEnvioPdfs['path'];
		 		}

			}
			
		}

		# MEli
		if ($venta['Marketplace']['marketplace_tipo_id'] == 2) 
		{
			$this->MeliMarketplace = $this->Components->load('MeliMarketplace');

			$this->MeliMarketplace->crearCliente( $venta['Marketplace']['api_user'], $venta['Marketplace']['api_key'], $venta['Marketplace']['access_token'], $venta['Marketplace']['refresh_token'] );
			$this->MeliMarketplace->mercadolibre_conectar('', $venta['Marketplace']);

			$mensajes = $this->MeliMarketplace->mercadolibre_obtener_mensajes($venta['Marketplace']['access_token'], $venta['Venta']['id_externo']);

			foreach ($mensajes as $mensaje) 
			{
				$data = array();
				$data['mensaje'] = $this->removeEmoji($mensaje['text']['plain']);
				$data['fecha'] = CakeTime::format($mensaje['date'], '%d-%m-%Y %H:%M:%S');
				$data['asunto'] = (empty($mensaje['subject'])) ? 'Sin asunto' : $mensaje['subject'] ;
				$venta['VentaMensaje'][] = $data;
			}

			// Detalles de la venta externa
			$venta['VentaExterna'] = $this->MeliMarketplace->mercadolibre_obtener_venta_detalles($venta['Marketplace']['access_token'], $venta['Venta']['id_externo'], true);

			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;
			
			// Datos d facturacion
			$venta['VentaExterna']['facturacion'] = array(
				'tipo_documento'        => 39, # Boleta por defecto,
				'glosa_tipo_documento'  => $this->LibreDte->tipoDocumento[39],
				'rut_receptor'          => $venta['VentaCliente']['rut'],
				'razon_social_receptor' => $venta['VentaCliente']['nombre']  . ' ' . $venta['VentaCliente']['apellido'],
				'giro_receptor'         => null,
				'direccion_receptor'    => $venta['Venta']['direccion_entrega'],
				'comuna_receptor'       => $venta['Venta']['comuna_entrega']
			);


			if (isset($venta['VentaExterna']['shipping']['id'])) 
			{

				$envio = $this->MeliMarketplace->mercadolibre_obtener_envio($venta['VentaExterna']['shipping']['id']);

				// Detalles de envio
				$direccion_envio = '';
				$nombre_receptor = '';
				$fono_receptor   = '';
				$comentario      = '';

				if (isset($envio['receiver_address']['address_line'])
					&& isset($envio['receiver_address']['city']['name'])) 
				{
					$direccion_envio = sprintf('%s, %s', $envio['receiver_address']['address_line'], $envio['receiver_address']['city']['name']);
				}

				if (isset($envio['receiver_address']['receiver_name'])) 
				{
					$nombre_receptor = $envio['receiver_address']['receiver_name'];
				}

				if (isset($envio['receiver_address']['receiver_phone'])) 
				{
					$fono_receptor = $envio['receiver_address']['receiver_phone'];
				}

				if (isset($envio['receiver_address']['comment'])) 
				{
					$comentario = $envio['receiver_address']['comment'];
				}

				$venta['Envio'][0] = array(
					'id'                      => $envio['id'],
					'tipo'                    => $envio['shipping_option']['name'],
					'estado'                  => $envio['status'],
					'direccion_envio'         => $direccion_envio,
					'nombre_receptor'         => $nombre_receptor,
					'fono_receptor'           => $fono_receptor,
					'producto'                => null,
					'cantidad'                => 1,
					'costo'                   => $envio['shipping_option']['cost'],
					'fecha_entrega_estimada'  => (isset($envio['shipping_option']['estimated_delivery_time'])) ? CakeTime::format($envio['shipping_option']['estimated_delivery_time']['date'], '%d-%m-%Y %H:%M:%S') : __('No especificado') ,
					'comentario'              => $comentario,
					'mostrar_etiqueta'        => ($envio['status'] == 'ready_to_ship') ? true : false,
					'paquete' 				  => false
				);	
				
			}

			$documentoEnvio = $this->MeliMarketplace->mercadolibre_obtener_etiqueta_envio($envio, 'Y');
			
			$rutaAbsoluta = APP . 'webroot' . DS. 'Venta' . DS . $id . DS;
			$rutaPublica  =  Router::url('/', true) . 'Venta/' . $id . '/';

			# Tranposrte Meli
			if (!empty($documentoEnvio)) 
			{
				$archivoPdfEnvio = 'transporte' . rand() . '.pdf';

				$documentoEnvioPdfs = $this->guardar_pdf_base64($documentoEnvio, $rutaAbsoluta, $rutaPublica, $archivoPdfEnvio, false);
		 		
		 		if (!empty($documentoEnvioPdfs)) 
				{
		 			$archivos[] = $documentoEnvioPdfs['path'];
		 		}
			}
		}	

		# Prestashop
		if (!$venta['Venta']['marketplace_id'] 
		&& !empty($venta['Venta']['id_externo']) 
		&& !$venta['Venta']['venta_manual']) 
		{
			# Para la consola se carga el componente on the fly!
			$this->Prestashop = $this->Components->load('Prestashop');

			# Cliente Prestashop
			$this->Prestashop->crearCliente( $venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop'] );	

			// Obtener detall venta externo
			$venta['VentaExterna'] = $this->Prestashop->prestashop_obtener_venta($venta['Venta']['id_externo']);		

			$venta['VentaExterna']['transportista'] = (!empty($venta['MetodoEnvio']['id'])) ? $venta['MetodoEnvio']['nombre'] : 'Sin especificar' ;

			$venta['VentaMensaje'] = $this->Prestashop->prestashop_obtener_venta_mensajes($venta['Venta']['id_externo']);

			$direccionEnvio       = $this->Prestashop->prestashop_obtener_venta_direccion($venta['VentaExterna']['id_address_delivery']);				

			// Detalles de envio
			$telefonosEnvio = '';
			
			if (is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) 
			{
				$telefonosEnvio .= implode(' ', $direccionEnvio['address']['phone_mobile']);
			}

			if (!is_array($direccionEnvio['address']['phone_mobile']) && !empty($direccionEnvio['address']['phone_mobile'])) 
			{
				$telefonosEnvio .= ' ' . $direccionEnvio['address']['phone_mobile'];
			}

			if (is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) 
			{
				$telefonosEnvio .= implode(' ', $direccionEnvio['address']['phone']);
			}

			if (!is_array($direccionEnvio['address']['phone']) && !empty($direccionEnvio['address']['phone'])) 
			{
				$telefonosEnvio .= ' ' . $direccionEnvio['address']['phone'];
			}
			
			// Detalles de envio
			$venta['Envio'][0] = array(
				'id'                      => $direccionEnvio['address']['id'],
				'tipo'                    => 'Dir. despacho',
				'estado'                  => (!$direccionEnvio['address']['deleted']) ? 'activo' : 'eliminada',
				'direccion_envio'         => @sprintf('%s %s, %s', $direccionEnvio['address']['address1'], implode(',', $direccionEnvio['address']['address2']), $direccionEnvio['address']['city']),
				'nombre_receptor'         => @sprintf('%s %s', $direccionEnvio['address']['firstname'], $direccionEnvio['address']['lastname']),
				'fono_receptor'           => $telefonosEnvio,
				'producto'                => null,
				'cantidad'                => 1, // No especifica
				'costo'                   => $venta['VentaExterna']['total_shipping_tax_incl'],
				'fecha_entrega_estimada'  => 'No especificado',
				'comentario'              => @implode(',', $direccionEnvio['address']['other']),
				'mostrar_etiqueta'        => true,
				'paquete' 				  => false
			);
		}

		# Obtenemos DTE
		if (!empty($venta['Dte'])) 
		{
			$dtes = $this->obtener_dtes_pdf_venta($venta['Dte'], 2);
		
			foreach ($dtes as $dte) 
			{
				$archivos[] = $dte['path'];
			}
		}

		$url_etiqueta_envio = $this->obtener_etiqueta_envio_default_url($venta, 'vertical');
		
		$archivos[]         = $url_etiqueta_envio['path'];

		# Unimos todos los PDFS obtenidos
		if (!empty($archivos)) 
		{	
			

			$pdf = $this->unir_documentos($archivos, $venta['Venta']['id']);

			return $pdf;

		}
		
		return;
	}

		
	/**
	 * api_obtener_venta_bodega
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function api_obtener_venta_bodega($id)
	{	
		# Sólo método Get
		if (!$this->request->is('get')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only GET request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			throw new CakeException($response);
		}

		# Detalles de la venta
		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id' => $id
			),
			'contain' => array(
				'VentaCliente',
				'MetodoEnvio',
				'VentaDetalle' => array(
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.id',
							'VentaDetalleProducto.nombre',
							'VentaDetalleProducto.codigo_proveedor'
						)
					),
					'EmbalajeProductoWarehouse' => array(
						'EmbalajeWarehouse'
					),
					'Atributo' => array(
						'AtributoGrupo'
					)
				),
				'VentaMensaje',
				'Mensaje',
				'Comuna',
				'Dte',
				'MedioPago',
				'Transporte' => array(
					'order' => array(
						'Transporte.id' => 'DESC'
					)
				),
				'Tienda',
				'Marketplace',
				'VentaEstado' => array(
					'VentaEstadoCategoria'
				),
				'EmbalajeWarehouse',
				'CanalVenta'
			)
		));

		# En ocaciones la BD registra duplicado los embalajes, por ende al procesar el primero de ellos, eliminaremos los duplicados
		foreach ($venta['VentaDetalle'] as $detalle)
		{
			$tmp_embalaje = [];
			$diff = [];
			
			foreach ($detalle['EmbalajeProductoWarehouse'] as $embalaje_producto)
			{	
				$em = [
					'id' => $embalaje_producto['id'],
					'embalaje_id' => $embalaje_producto['embalaje_id'],
					'producto_id' => $embalaje_producto['producto_id'],
					'detalle_id' => $embalaje_producto['detalle_id'],
					'cantidad_a_embalar' => $embalaje_producto['cantidad_a_embalar'],
					'cantidad_embalada' => $embalaje_producto['cantidad_embalada'],
					'fecha_creacion' => $embalaje_producto['fecha_creacion'],
					'ultima_modifacion' => $embalaje_producto['ultima_modifacion'],
					'cantidad_anulada' => $embalaje_producto['cantidad_anulada'],
					'embalaje_bodega_id' => $embalaje_producto['EmbalajeWarehouse']['bodega_id'],
					'embalaje_estado' => $embalaje_producto['EmbalajeWarehouse']['estado'],
					'embalaje_fecha' => $embalaje_producto['EmbalajeWarehouse']['fecha_creacion']
				];

				# Buscamos las diferencias entre los embalajes
				$diff = Hash::diff($em, $tmp_embalaje);
				
				# Asignamos el embalaje a una variable temporal que la contendrá para compararla en la siguente iteración
				$tmp_embalaje = $em;

				# si no existen estos indices en las diferencias queire decir que es un embalaje duplicado
				if (!isset($diff['embalaje_fecha']) 
				&& !isset($diff['embalaje_bodega_id'])
				&& !isset($diff['cantidad_a_embalar']))
				{

					$mandrill_apikey = $venta['Tienda']['mandrill_apikey'];

					if (empty($mandrill_apikey)) {
						return false;
					}

					$mandrill = $this->Components->load('Mandrill');

					$mandrill->conectar($mandrill_apikey);

					$asunto = '['.$venta['Tienda']['nombre'].' ALERTA] ¡Venta #' . $venta['Venta']['id'] . ' tiene embalaje duplicado!';
					
					if (Configure::read('ambiente') == 'dev') {
						$asunto = '['.$venta['Tienda']['nombre'].' ALERTA - DEV] ¡Venta #' . $venta['Venta']['id'] . ' tiene embalaje duplicado!';
					}

					$remitente = array(
						'email' => 'warehouse@nodriza.cl',
						'nombre' => 'Ventas ' . $venta['Tienda']['nombre']
					);

					$destinatarios = array(
						array(
							'email' => 'cristian.rojas@nodriza.cl',
							'name' => 'Cristian rojas'
						)
					);

					$html = "<h1>La venta #{$venta['Venta']['id']} al parecer tiene un embalaje duplicado. Revíselo a la brevedad.</h1>";

					$mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);

				}
			}
	
		}
		
		$etiquetas_embalajes = array();
		
		$embalajesController = new EmbalajeWarehousesController();

		# Creamos las etiquetas internas necesarias
		foreach ($venta['EmbalajeWarehouse'] as $iem => $e) 
		{
			if ($e['estado'] == 'procesando')
			{
				$etiquetas_embalajes[] = $embalajesController->obtener_etiqueta_envio_interna_url($e['id'], $venta);
			}
		}

		# Cambiamos valor de la nota interna y le ponemos la referencia del despacho
		$venta['Venta']['nota_interna'] = $venta['Venta']['referencia_despacho'];

		# si es una venta parcial se indica en la nota interna
		$total_agendado = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_en_espera'));

		if ($total_agendado)
		{
			$venta['Venta']['nota_interna'] = $venta['Venta']['nota_interna'] . "\r\n\r\n---Embalaje/venta parcial---";
		}

		# Unir etiquetas embalajes. nunca serán más de 500
		$this->Etiquetas = $this->Components->load('Etiquetas');
		$etiqueta_interna2 = @$this->Etiquetas->unir_documentos(Hash::extract($etiquetas_embalajes, '{n}.path'), date('Y-m-d-H-i-s'))['result'][0]['document'];

		$documentos = $this->generar_documentos($venta);
		
		$etiqueta_interna = $this->obtener_etiqueta_envio_default_url($venta);
		
		$dtes = $this->obtener_dtes_pdf_venta($venta['Dte'], 1);

		$etiqueta_externa = $venta['Venta']['etiqueta_envio_externa'];

		# Obtenems la etiqueta externa si no está definida aun
		if (empty($etiqueta_externa))
		{
			# Buscamos la última etiqueta generada en el transporte
			foreach ($venta['Transporte'] as $it => $t) 
			{
				if ($t['TransportesVenta']['etiqueta'])
				{
					$etiqueta_externa = $t['TransportesVenta']['etiqueta'];
					break;
				}
			}
		}

		if ($venta['Venta']['canal_venta_id'])
		{
			$venta['Tienda']['nombre'] = $venta['CanalVenta']['nombre'] . ' - ' . $venta['Tienda']['nombre'];
		}
		
		$respuesta =  array(
			'code' => 200,
			'message' => 'Información obtenida con éxito',
			'body' => array(
				'cliente' => array(
					'rut'      => $venta['VentaCliente']['rut'],
					'nombre'   => $venta['VentaCliente']['nombre'],
					'apellido' => $venta['VentaCliente']['apellido'],
					'email'    => $venta['VentaCliente']['email'],
					'fono'     => $venta['VentaCliente']['telefono'],
				),
				'venta' => array(
					'id'          => $venta['Venta']['id'],
					'id_externo'  => $venta['Venta']['id_externo'],
					'referencia'  => $venta['Venta']['referencia'],
					'fecha_venta' => $venta['Venta']['fecha_venta'],
					'estado'      => $venta['VentaEstado']['VentaEstadoCategoria']['nombre'],
					'subestado'   => $venta['VentaEstado']['nombre'],
					'canal_venta' => (!empty($venta['Marketplace']['id'])) ? $venta['Marketplace']['nombre'] : $venta['Tienda']['nombre'],
					'nota_interna' => $venta['Venta']['nota_interna']
				),
				'etiquetas' => array(
					'todos' => $documentos['result'],
					'interna' => (empty($etiqueta_interna2)) ? $etiqueta_interna['public'] : $etiqueta_interna2,
					'externa' => $etiqueta_externa,
					'dtes' => $dtes 
				),
				'entrega' => array(
					'metodo' => $venta['MetodoEnvio']['nombre'],
					'fecha_entrega_estimada' => 'No definido',
					'calle' => $venta['Venta']['direccion_entrega'],
					'numero' => $venta['Venta']['numero_entrega'],
					'otro' => $venta['Venta']['otro_entrega'],
					'comuna' => $venta['Comuna']['nombre'],
					'receptor' => $venta['Venta']['nombre_receptor'],
					'rut' => $venta['Venta']['rut_receptor'],
					'fono_receptor' => $venta['Venta']['fono_receptor']
				),
				'mensajes' => array(),
				'transportes' => array(),
				'itemes' => array(),
				'embalajes' => $venta['EmbalajeWarehouse']
			)
		);
		
		$mensajes = array();
		$auxFechas = array();

		# Mensajes venta
		foreach($venta['VentaMensaje'] as $mensaje)
		{
			$mensajes[] = array(
				'emisor' => $mensaje['emisor'],
				'fecha' => $mensaje['fecha'],
				'asunto' => $mensaje['nombre'],
				'mensaje' => $mensaje['mensaje']
			);
		}

		# Mensajes adicionales
		foreach ($venta['Mensaje'] as $mensaje2) 
		{
			$mensajes[] = array(
				'emisor' => $venta['VentaCliente']['rut'],
				'fecha' => $mensaje2['created'],
				'asunto' => ($mensaje2['origen'] == 'cliente') ? 'Mensaje de cliente' : 'Mensaje interno',
				'mensaje' => $mensaje2['mensaje']
			);
		}

		# Agrupamos para ordenar
		foreach ($mensajes as $im => $mensaje3) 
		{
			$auxFechas[$im] = $mensaje3['fecha'];
		}
		
		# Ordenamos los mensajes por fecha
		if ($auxFechas)
		{
			array_multisort($auxFechas, SORT_DESC, $mensajes);
		}		
		
		$respuesta['body']['mensajes'] = $mensajes;

		# Agregamos los transportes
		foreach ($venta['Transporte'] as $transporte)
		{
			$respuesta['body']['transportes'][] =  array(
				'transporte_id' => $transporte['id'],
				'nombre' => $transporte['nombre'],
				'codigo' => $transporte['codigo'],
				'url_seguimiento_externa' => $transporte['url_seguimiento'],
				'cod_seguimiento' => $transporte['TransportesVenta']['cod_seguimiento'],
				'fecha_entrega_aprox' => $transporte['TransportesVenta']['entrega_aprox'],
				'etiqueta' => $transporte['TransportesVenta']['etiqueta'],
				'activo' => $transporte['activo'] 
			);
		}

		# Cargamos componente prestashop para obtener info del produto desde toolmania
		$this->Prestashop = $this->Components->load('Prestashop');
		$this->Prestashop->crearCliente($venta['Tienda']['apiurl_prestashop'], $venta['Tienda']['apikey_prestashop']);

		# Agregamos los productos
		foreach ($venta['VentaDetalle'] as $i => $item) 
		{	
			# Producto bodega
			$pbodega = ClassRegistry::init('ProductoWarehouse')->find('first', array(
				'conditions' => array(
					'id' => $item['venta_detalle_producto_id']
				)
			));
			
			# Se obtiene imagen desde prestashop
			$imagen = $this->Prestashop->prestashop_obtener_imagenes_producto($item['venta_detalle_producto_id'], $venta['Tienda']['apiurl_prestashop']);

			foreach ($item['EmbalajeProductoWarehouse'] as $iemp => $emp) 
			{	
				if ($emp['EmbalajeWarehouse']['estado'] == 'cancelado')
					continue;

				# Le concatenamos los atributos si corresponde
				if (!empty($item['Atributo']))
				{
					$item['VentaDetalleProducto']['nombre'] = $item['VentaDetalleProducto']['nombre'] . ' - ' . $item['Atributo'][0]['VentaDetallesAtributo']['valor'];
				}

				$respuesta['body']['itemes'][] = array(
					'id' => $item['id'],
					'producto_id' => $item['venta_detalle_producto_id'],
					'nombre' => $item['VentaDetalleProducto']['nombre'],
					'sku' => $item['VentaDetalleProducto']['codigo_proveedor'],
					'cantidad_pendiente_entrega' => (int) $item['cantidad_pendiente_entrega'],
					'cantidad_reservada' => (int) $item['cantidad_reservada'],
					'cantidad_a_emabalar' => $emp['cantidad_a_embalar'] - $emp['cantidad_embalada'],
					'imagen' => Hash::extract($imagen, '{n}[principal=1].url')[0],
					'peso' => $pbodega['ProductoWarehouse']['peso'],
					'ancho' => $pbodega['ProductoWarehouse']['ancho'],
					'largo' => $pbodega['ProductoWarehouse']['largo'],
					'alto' => $pbodega['ProductoWarehouse']['alto']
				);

				$venta['VentaDetalle'][$i]['VentaDetalleProducto']['peso'] = $pbodega['ProductoWarehouse']['peso'];
				$venta['VentaDetalle'][$i]['VentaDetalleProducto']['alto'] = $pbodega['ProductoWarehouse']['alto'];
				$venta['VentaDetalle'][$i]['VentaDetalleProducto']['ancho'] = $pbodega['ProductoWarehouse']['ancho'];
				$venta['VentaDetalle'][$i]['VentaDetalleProducto']['largo'] = $pbodega['ProductoWarehouse']['largo'];
			}
		}
	
		if (empty($respuesta['body']['itemes']))
		{
			$response = array(
				'code'    => 401, 
				'name' => 'error',
				'message' => 'Venta no disponible para procesar'
			);

			throw new CakeException($response);
		}
		
		# bultos 
		$this->LAFFPack = $this->Components->load('LAFFPack');
		$respuesta['body']['bultos'] = $this->LAFFPack->obtener_bultos_venta($venta, $venta['MetodoEnvio']['peso_maximo']); 

		$this->set(array(
            'response' => $respuesta,
            '_serialize' => array('response')
        ));

	}


	# https://sistemasdev.nodriza.cl/api/ventas/enviame_webhook.json?token=bf085eddd7e1fbebbbfb938804598ced13adfd1b622b7bf0
	public function api_enviame_webhook()
	{	

		$log = array(
			'Log' => array(
				'administrador' => 'Enviame Webhook',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($this->request)
			)
		);

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		# Solo método POST
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501,
				'name' => 'error',
				'message' => 'Método no permitido'
			);

			throw new CakeException($response);
		}

		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		$token      = @$this->request->query['token'];

		$data = $this->request->data;


		if (!$this->Venta->exists($data['order_number'])) {
			$response = array(
				'code'    => 506, 
				'name' => 'error',
				'message' => 'La venta ' . $data['order_number'] . ' no existe.'
			);

			throw new CakeException($response);
		}

		$nuevo_estado = '';

		switch ($data['status_id']) {
			case 6:
				$nuevo_estado = 'enviado';
				break;

			case 7:
				$nuevo_estado = 'enviado';
				break;
			
			case 8:
				$nuevo_estado = 'enviado';
				break;

			case 9:
				$nuevo_estado = 'enviado';
				break;
			
			case 10:
				$nuevo_estado = 'delivered';
				break;
		}

		App::uses('HttpSocket', 'Network/Http');
		$socket			= new HttpSocket();
		$request		= $socket->post(
			Router::url('/api/ventas/change_state/'.$data['order_number'].'.json?token=' . $token, true),
			array(
				'type' => $nuevo_estado
			)
		);

		$log = array(
			'Log' => array(
				'administrador' => 'Enviame Webhook',
				'modulo' => 'Ventas',
				'modulo_accion' => $request->body()
			)
		);

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		$this->set(array(
			'response' => json_decode($request->body(), true),
            '_serialize' => array('response')
        ));
		
	}

	/**
	 * [api_cambiar_estado description]
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function api_cambiar_estado_v2($id = '')
	{

		if (!$this->request->is('post')) {

		throw new MethodNotAllowedException('Método no permitido');
		}


		if (!isset($this->request->query['token'])) {

		throw new NotFoundException('Token requerido', 403);
		}


		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {

		throw new NotFoundException('Token de sesión expirado o invalido', 403);
		}


		if (!$this->Venta->exists($id)) {
		throw new NotFoundException("Venta {$id} no encontrada", 404);
		}

		$embalaje 	 = $this->request->data['embalaje_producto']?? null;
		$responsable = $this->request->data['responsable']??null;

	

		if (empty($embalaje)) {
			throw new NotFoundException('Se requiere productos', 403);
		}

		if (!isset($responsable)) {
			throw new NotFoundException('Se requiere un responsable', 403);
		}
	

		foreach ($embalaje as $productos) {

			if (empty(Hash::extract($productos, "embalaje_id")) ||	empty(Hash::extract($productos, "producto_id")) ||	empty(Hash::extract($productos, "cantidad_embalada")) || empty(Hash::extract($productos, "detalle_id"))) {
				throw new NotFoundException("Asegurece de haber enviado todos los a parametros: embalaje_id, producto_id, cantidad_embalada, detalle_id", 403);
			}
		}

		$HistorialEmbalaje = ClassRegistry::init('HistorialEmbalaje')->find('all', [
			'fields' => [
				'producto_id',
				'embalaje_id',
				'cantidad_embalada'
			],
			'conditions' => ['venta_id' => $id]
		]);
	
		$VentaDetalle = ClassRegistry::init('VentaDetalle')->find('all', [
		'fields' => [
			'VentaDetalle.venta_detalle_producto_id',
			'VentaDetalle.id',
			'VentaDetalle.cantidad_entregada',
			'VentaDetalle.cantidad_pendiente_entrega',

		],
			'conditions' => ['venta_id' => $id]
		]);

		$actualizar = [];
		$log = [];
		foreach ($embalaje as $value) {

			$existe                = Hash::extract($HistorialEmbalaje, "{n}.HistorialEmbalaje[embalaje_id={$value['embalaje_id']}][producto_id={$value['producto_id']}]");
			
			// * Se valida si el embalaje ya se registro en el historial
			if ($existe) {
				$log[] = array(
				'Log' => array(
					'administrador' => "App Nodriza metodo api_cambiar_estado_v2 vid {$id}",
					'modulo'        => 'Ventas',
					'modulo_accion' => json_encode(["Se intento registrar nuevamente el embalaje {$value['embalaje_id']} a la venta {$id}" => ['Detalle de la Venta' => $VentaDetalle, 'HistorialEmbalaje desde sistema' => $HistorialEmbalaje, 'Embalaje desde la app' => $embalaje]])
				)
				);
				continue;
			}

			$cantidad_entregada_sistema         = array_sum(Hash::extract($VentaDetalle, "{n}.VentaDetalle[id={$value['detalle_id']}].cantidad_entregada"));
			$cantidad_pendiente_entrega_sistema = array_sum(Hash::extract($VentaDetalle, "{n}.VentaDetalle[id={$value['detalle_id']}].cantidad_pendiente_entrega"));
			$cantidad_a_entregar_sistema        = $cantidad_entregada_sistema +  $cantidad_pendiente_entrega_sistema;
			$cantidad_embalada_warehouse        = array_sum(Hash::extract($HistorialEmbalaje, "{n}.HistorialEmbalaje[producto_id={$value['producto_id']}].cantidad_embalada"));

			// ** Se valida que la cantidad entregada sea la correcta y no se entregue de más
			if ($cantidad_embalada_warehouse >= $cantidad_a_entregar_sistema) {

				$log[] = array(
					'Log' => array(
					'administrador' => "App Nodriza metodo api_cambiar_estado_v2 vid {$id}",
					'modulo'        => 'Ventas',
					'modulo_accion' => json_encode(["La cantidad embalada($$cantidad_embalada_warehouse) no coinciden con la entregada($cantidad_a_entregar_sistema)."])
					)
				);

				continue;
			}

			$actualizar[] =[
				'HistorialEmbalaje' =>
					[
						'detalle_id'        => $value['detalle_id'],
						'embalaje_id'       => $value['embalaje_id'],
						'producto_id'       => $value['producto_id'],
						'cantidad_embalada' => $value['cantidad_embalada'],
						'venta_id'          => $id,
					]
			];
		}
		if (!$actualizar) {

			$log[] = array(
				'Log' => array(
				'administrador' => "App Nodriza metodo api_cambiar_estado_v2 vid {$id}",
				'modulo'        => 'Ventas',
				'modulo_accion' => json_encode(["Problemas para registrar embalaje {$embalaje[0]['embalaje_id']} ." => ['Detalle de la Venta' => $VentaDetalle, 'HistorialEmbalaje desde sistema' => $HistorialEmbalaje, 'Embalaje desde la app' => $embalaje]])
				)
			);

			if ($log) {

				ClassRegistry::init('Log')->create();
				ClassRegistry::init('Log')->saveAll($log);
			}

			throw new BadRequestException("Problemas para registrar entrega del embalaje {$embalaje[0]['embalaje_id']}");
		}


		ClassRegistry::init('HistorialEmbalaje')->create();
		ClassRegistry::init('HistorialEmbalaje')->saveAll($actualizar);

		$response = array(
			'name'    => 'Solicitud procesada con exito',
			'message' => "Se registro embalaje {$embalaje[0]['embalaje_id']} a la VID-{$id}."
		);

		// * Se consulta para saber si la venta ya entrego todos sus productos 
		$HistorialEmbalaje = ClassRegistry::init('HistorialEmbalaje')->find('all', [
		'fields' =>
		[
			'cantidad_embalada',
			'producto_id'
		],
		'conditions' =>
		[
			'venta_id' => $id,
		]
		]);

		$venta_parcial = false;

		// * Si existe una diferencia entre los embalajes entregados y el detalle de la venta el estado sera Entregado Parcial
		foreach ($VentaDetalle as $producto) {

		$total                       = $producto['VentaDetalle']['cantidad_pendiente_entrega'] + $producto['VentaDetalle']['cantidad_entregada'];
		$cantidad_embalada_warehouse = Hash::extract($HistorialEmbalaje, "{n}.HistorialEmbalaje[producto_id={$producto['VentaDetalle']['venta_detalle_producto_id']}].cantidad_embalada");
		$cantidad_embalada_warehouse = $cantidad_embalada_warehouse ? array_sum($cantidad_embalada_warehouse) : 0;

		if ($total != $cantidad_embalada_warehouse) {
			$venta_parcial = true;
		}
		}

		$estado = ClassRegistry::init('VentaEstado')->find(
		'first',
		array(
			'conditions' => array(
			'VentaEstado.nombre' => trim($venta_parcial ? "Entregado Parcial" : "Entregado")
			)
		)
		);

		$venta = $this->Venta->find(
		'first',
		[
			'fields'     => [
			'Venta.id_externo',
			'Venta.tienda_id'
			],
			'conditions' => ['id' => $id]
		]
		);
		
		try {
			$this->cambiarEstado($id, $venta['Venta']['id_externo'], $estado['VentaEstado']['id'], $venta['Venta']['tienda_id'],null,'','',$responsable);
		} catch (\Throwable $th) {
		}

		if ($log) {

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveAll($log);
		}


		$this->set(array(
		'response'   => $response,
		'_serialize' => array('response')
		));
	}

	public function api_cambiar_estado($id = '')
	{	
		# Solo método POST
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501,
				'name' => 'error',
				'message' => 'Método no permitido'
			);

			throw new CakeException($response);
		}

		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		$token      = @$this->request->query['token']; // GET
		$tipoEstado = @$this->request->data['type']; // POST
		$chofer     = @$this->request->data['driver']; // POST

		$log = array(
			'Log' => array(
				'administrador' => 'App Nodriza',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($this->request)
			)
		);

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		$tiposPermitidos = array(
			'shipped', // En transito
			'delivered',
			'enviado', // Enviado por carrier
			'despacho_interno', // Despacho transporte interno, selecciona chofer (Enviado)
			'despacho_externo', // Despacho transporte externo, seleccionar transportista usado (Enviado)
			'despacho_transito', // Despacho en transito, selecciona chofer (En transito)
			'retiro_en_tienda', // Adjuntar foto carnet (Retiro en tienda)
			'entrega_domicilio', // Transporte interno, adjunta foto carnet (Entregado),
			'entrega_agencia', // Transporte entrega en agencia, selecciona transporte y agrega ID de seguimiento 
		);

		# No vacios
		if (empty($id) || empty($token) || empty($tipoEstado)) {
			$response = array(
				'code'    => 503, 
				'message' => 'Empty value'
			);

			throw new CakeException($response);
		}

		# Tipos de cambios de estados disponibles
		if (!in_array($tipoEstado, $tiposPermitidos)) {
			$response = array(
				'code'    => 504, 
				'message' => 'Invalid value for type param'
			);

			throw new CakeException($response);
		}


		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			throw new CakeException($response);
		}

		# Detalles de la venta
		$venta = $this->Venta->obtener_venta_por_id($id);

		$this->Venta->id                       = $id;
		ClassRegistry::init('VentaEstado')->id = $venta['Venta']['venta_estado_id'];
		ClassRegistry::init('Tienda')->id      = $venta['Venta']['tienda_id'];

		if (!empty($this->request->data['Venta']['marketplace_id'])) {
			ClassRegistry::init('Marketplace')->id = $venta['Venta']['marketplace_id'];				
		}
				
		$notificar        = ClassRegistry::init('VentaEstado')->field('notificacion_cliente');
		$esPrestashop     = (empty($venta['Venta']['marketplace_id'])) ? true : false;
		$estado_actual    = $venta['Venta']['venta_estado_id'];
		$estado_nuevo     = '';
		$estado_nuevo_arr = array();
		$id_externo       = $venta['Venta']['id_externo'];
		$plantillaEmail   = ClassRegistry::init('VentaEstadoCategoria')->field('plantilla', array('id' => ClassRegistry::init('VentaEstado')->field('venta_estado_categoria_id')));
		
		$esMercadolibre = false;
		$esLinio        = false;

		# Verificamos el canal de la venta
		if (!empty($venta['Venta']['marketplace_id'])) {
			switch ( ClassRegistry::init('Marketplace')->field('marketplace_tipo_id') ) {
				case 1: // Linio
					$esLinio      = true;
					$apiurllinio  = ClassRegistry::init('Marketplace')->field('api_host');
					$apiuserlinio = ClassRegistry::init('Marketplace')->field('api_user');
					$apikeylinio  = ClassRegistry::init('Marketplace')->field('api_key');
					break;
				
				case 2: // Meli
					$esMercadolibre = true;
					break;
			}
		}

		$apiurlprestashop = ClassRegistry::init('Tienda')->field('apiurl_prestashop');
		$apikeyprestashop = ClassRegistry::init('Tienda')->field('apikey_prestashop');
		
		#$this->Venta->save($venta);
		#$enviado = $this->notificar_cambio_estado($id, $plantillaEmail, $estado_nuevo);

		# Validamos que sea venta parcial o ocmpleta
		$total_agendado = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_en_espera'));

		# Prestashop
		if ( $esPrestashop && !empty($apiurlprestashop) && !empty($apikeyprestashop)) {
			# Para la consola se carga el componente on the fly!
			if ($this->shell) {
				$this->Prestashop = $this->Components->load('Prestashop');
			}
			# Cliente Prestashop
			$this->Prestashop->crearCliente( $apiurlprestashop, $apikeyprestashop );

			switch ($tipoEstado) {
				case 'despacho_interno':
					# Obtenemos el estado de enviado
					$estado_nuevo     = ($total_agendado) ? 'Parcialmente enviado' : 'Enviado';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);

					#  Necesita recibir un chofer
					if (empty($chofer)) {
						$response = array(
							'code'    => 512, 
							'message' => 'Driver is required'
						);

						throw new CakeException($response);
					}

					# Guardar chofer
					$this->Venta->saveField('chofer_email', $chofer);	

					# Guardar fecha de envio
					$this->Venta->saveField('fecha_enviado', date('Y-m-d H:i:s'));	
					
					break;
				case 'entrega_domicilio':
					# Obtenemos el estado de entregado
					$estado_nuevo     = ($total_agendado) ? 'Entregado Parcial' : 'Entregado';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
					
					# Si se adjunta foto del carnet del receptor
					if (isset($this->request->form['carnet'])) {

						$imagenes = array($this->request->form['carnet']);

						$erroresImagen = $this->validarTamanoTipoImagenes($imagenes);
						
						if (!empty($erroresImagen)) {
							$response = array(
								'code'    => 513, 
								'message' => 'Errors: ' . implode(' - ', $erroresImagen)
							);

							throw new CakeException($response);
						}

						# Guardamos
						if (!$this->Venta->saveField('ci_receptor', $this->request->form['carnet'])) {
							$response = array(
								'code'    => 512, 
								'message' => 'Can´t save c.i photo'
							);

							throw new CakeException($response);
						}

						# Guardamos fecha entrega
						$this->Venta->saveField('fecha_entregado', date('Y-m-d H:i:s'));

					}

					break;
				case 'retiro_en_tienda':
					# Obtenemos estado de entregado
					$estado_nuevo     = ($total_agendado) ? 'Entregado Parcial' : 'Entregado';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);

					# Si se adjunta foto del carnet del receptor
					if (isset($this->request->form['carnet'])) {

						$imagenes = array($this->request->form['carnet']);

						$erroresImagen = $this->validarTamanoTipoImagenes($imagenes);
						
						if (!empty($erroresImagen)) {
							$response = array(
								'code'    => 513, 
								'message' => 'Errors: ' . implode(' - ', $erroresImagen)
							);

							throw new CakeException($response);
						}

						# Guardamos
						if (!$this->Venta->saveField('ci_receptor', $this->request->form['carnet'])) {
							$response = array(
								'code'    => 512, 
								'message' => 'Can´t save c.i photo'
							);

							throw new CakeException($response);
						}

						# Guardamos fecha entrega
						$this->Venta->saveField('fecha_entregado', date('Y-m-d H:i:s'));

					}

					break;
				case 'despacho_externo':
					# Obtenemos el estado de enviado
					$estado_nuevo     = ($total_agendado) ? 'Parcialmente enviado' : 'Enviado';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);

					if (!isset($this->request->data['carrier'])){
						$response = array(
							'code'    => 404, 
							'message' => 'Carrier is required'
						);

						throw new CakeException($response);
					}

					if (!ClassRegistry::init('Transporte')->exists($this->request->data['carrier'])) {
						$response = array(
							'code'    => 404, 
							'message' => 'Carrier not found'
						);

						throw new CakeException($response);
					}

					$dataToSave = array(
						'Venta' => array(
							'id' => $venta['Venta']['id'],
							'fecha_enviado' => date('Y-m-d H:i:s')
						),
						'Transporte' => array(
							array(
								'transporte_id'   => $this->request->data['carrier'],
								'cod_seguimiento' => (isset($this->request->data['tracking'])) ? $this->request->data['tracking'] : 'No ingresado' ,
								'created'         => date('Y-m-d H:i:s')
							)
						)
					);

					# Guardamos los códigos de seguimiento
					if (!$this->Venta->saveAll($dataToSave)) {
						$response = array(
							'code'    => 404, 
							'message' => 'Can´t save carrier info. Pease try again'
						);

						throw new CakeException($response);
					}

					break;
				case 'despacho_transito':
					# Obtenemos el estado de enviado
					$estado_nuevo     = 'En transito';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);

					#  Necesita recibir un chofer
					if (empty($chofer)) {
						$response = array(
							'code'    => 512, 
							'message' => 'Driver is required'
						);

						throw new CakeException($response);
					}

					# Guardar chofer
					$this->Venta->saveField('chofer_email', $chofer);

					# Guardar fecha de envio
					$this->Venta->saveField('fecha_transito', date('Y-m-d H:i:s'));	

					break;
				case 'entrega_agencia':

					# Obtenemos el estado de enviado
					$estado_nuevo     = ($total_agendado) ? 'Parcialmente enviado' : 'Enviado';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
					
					break;
				case 'shipped':
					# Obtenemos el estado de enviado
					$estado_nuevo     = 'En transito';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
					break;
				case 'enviado':
					# Obtenemos el estado de enviado
					$estado_nuevo     = ($total_agendado) ? 'Parcialmente enviado' : 'Enviado';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
					break;
				case 'delivered':
					# Obtenemos estado de entregado
					$estado_nuevo     = ($total_agendado) ? 'Entregado Parcial' : 'Entregado';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
					break;
			}

			# Asignamos el nuevo estado a la venta intenra
			$venta['Venta']['venta_estado_id'] = $estado_nuevo_arr['VentaEstado']['id'];

		# Linio
		}elseif ( $esLinio && !empty($apiurllinio) && !empty($apiuserlinio) && !empty($apikeylinio)) {
			switch ($tipoEstado) {
				case 'despacho_externo':
					# Obtenemos el estado de enviado
					$estado_nuevo     = 'ready_to_ship';
					$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estado_nuevo);
					break;
			}

			# Asignamos el nuevo estado a la venta intenra
			$venta['Venta']['venta_estado_id'] = $estado_nuevo_arr['VentaEstado']['id'];
			
		# Meli
		}elseif ( $esMercadolibre && !empty($apiurl) && !empty($apikey)) {
			
			# Nada, meli cambia sus estados

		}else{
			$response = array(
				'code'    => 404, 
				'message' => 'State not allowed'
			);

			throw new CakeException($response);
		}

		$this->request->data['Venta']['estado_anterior'] = $estado_actual;
		$this->request->data['Venta']['venta_estado_id'] = $venta['Venta']['venta_estado_id'];
		
		$responsable = ClassRegistry::init('Token')->obtener_propietario_token($token);

		try {
			
			$cambiar_estado = $this->cambiarEstado($id, $venta['Venta']['id_externo'], $venta['Venta']['venta_estado_id'], $venta['Venta']['tienda_id'], $venta['Venta']['marketplace_id'], '', '', $responsable);
		
		} catch (Exception $e) {

			$response = array(
				'code'    => 506, 
				'message' => $e->getMessage()
			);

			throw new CakeException($response);
		}

		# Guardamos el nuevo estado
		if ($cambiar_estado) {
			
			$this->set(array(
	            'response' => true,
	            '_serialize' => array('response')
	        ));

		}else{
			$response = array(
				'code'    => 510, 
				'message' => 'Can´t save state in local Database'
			);

			throw new CakeException($response);
		}

	}


	/**
	 * [api_registrar_seguimiento description]
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function api_registrar_seguimiento($id = '')
	{
		# Sólo método post
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501,
				'name' => 'error',
				'message' => 'Método no permitido'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			throw new CakeException($response);
		}

		# No hay codigos de seguimiento
		if (!isset($this->request->data['Tracking']) || empty($this->request->data['Tracking'])) {
			$response = array(
				'code'    => 507, 
				'message' => 'Tracking array is required'
			);

			throw new CakeException($response);
		}

		# Detalles de la venta
		$venta       = $this->preparar_venta($id);
		
		$transportes = $this->request->data['Tracking'];
		
		$token       = $this->request->query['token'];


		$error = 0;

		# Validamos que cada array tenga ambos campos con información
		foreach ($transportes as $it => $transporte) {
			if (!isset($transporte['transportista'])
				|| empty($transporte['transportista'])
				|| !isset($transporte['seguimiento'])
				|| empty($transporte['seguimiento'])
				|| !ClassRegistry::init('Transporte')->exists($transporte['transportista']) ) {
				$error = 1;
			}
		}

		if ($error) {
			$response = array(
				'code'    => 508, 
				'message' => 'Carrier and tracking code are required or carrier doesn´t exist'
			);

			throw new CakeException($response);
		}

		$dataToSave = array(
			'Venta' => array(
				'id' => $venta['Venta']['id']
			)
		);

		foreach ($transportes as $it => $t) {
			$dataToSave['Transporte'][$it]['transporte_id']   = $t['transportista'];
			$dataToSave['Transporte'][$it]['cod_seguimiento'] = $t['seguimiento'];
			$dataToSave['Transporte'][$it]['created']         = date('Y-m-d H:i:s');
		}

		# Guardamos los códigos de seguimiento
		if ($this->Venta->saveAll($dataToSave)) {

			# Hacemos un POST para cambiar el estado correspondiente
			App::uses('HttpSocket', 'Network/Http');
			$socket			= new HttpSocket();
			
			$request		= $socket->post(Router::url('/api/ventas/change_state/'.$this->Venta->id.'.json?&token='.$token, true), array(
				'type' => 'entrega_agencia'
			));
			
			$request->body = json_decode($request->body, true);
			
			if (isset($request->body['response']) && $request->body['response'] == true) {
				$respuesta = true;
			}else{
				$response = array(
					'code'    => $request->body['code'], 
					'message' => $request->body['message']
				);

				throw new CakeException($response);
			}

		}else{
			$response = array(
				'code'    => 509, 
				'name' => 'error',
				'message' => 'No fue posible guardar los n° de seguimiento'
			);

			throw new CakeException($response);
		}

		$this->set(array(
            'response' => $respuesta,
            '_serialize' => array('response')
        ));
		
	}


	/**
	 * ! Metodo obsoleto y en desuzo
	 * [api_picking_venta description]
	 * @param  string $id [description]
	 * @return [type]     [description]
	 */
	public function api_picking_venta($id = '')
	{
		# Sólo método post
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501,
				'name' => 'error',
				'message' => 'Método no permitido'
			);

			throw new CakeException($response);
		}

		// parámetro mágico que permite ingresar sin estar autenticado
		if (isset($this->request->query['tadah'])) {
			$token = ClassRegistry::init('Token')->crear_token(1);
			$this->request->query['token'] = $token['token'];
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			throw new CakeException($response);
		}

		if (!isset($this->request->data['Detail'])) {
			$response = array(
				'code'    => 518, 
				'name' => 'error',
				'message' => 'Productos vendidos son obligatorios'
			);

			throw new CakeException($response);
		}

		$log = array(
			'Log' => array(
				'administrador' => 'App Nodriza Picking',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($this->request)
			)
		);

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->save($log);

		$venta = $this->preparar_venta($id);

		$productosConfirmados = $this->request->data['Detail'];

		$detalles = array();

		foreach ($productosConfirmados as $ip => $producto) {
			
			if (!isset($producto['id']) || !isset($producto['quantity'])) {
				continue;
			}

			if (count(Hash::extract($venta['VentaDetalle'], '{n}[id='.$producto['id'].']')) == 0)  {
				continue;
			}


			$detalles[$ip]['VentaDetalle'] = Hash::extract($venta['VentaDetalle'], '{n}[id='.$producto['id'].']')[0];
			$detalles[$ip]['VentaDetalle']['cantidad_preparada'] = $producto['quantity'];
			
		}

		# Existen detalles
		if (empty($detalles)) {
			$response = array(
				'code'    => 519,
				'name'    => 'error',
				'message' => 'No existen productos para procesar. Verifique la información e intente nuevamente.'
			);

			throw new CakeException($response);
		}

		# Cantidad preparada debe ser igual a la reservada
		if (array_sum(Hash::extract($detalles, '{n}.VentaDetalle.cantidad_preparada')) != array_sum(Hash::extract($detalles, '{n}.VentaDetalle.cantidad_reservada'))) {
			$response = array(
				'code'    => 519,
				'name' 	  => 'error',
				'message' => 'La cantidad de productos confirmados es diferente a la cantidad reservada. Verifique la información e intente nuevamente.'
			);

			throw new CakeException($response);
		}
		

		$html_tr = '';

		foreach ($detalles as $idd => $detalle) {
			# Pedido validado por app
			$detalles[$idd]['VentaDetalle']['confirmado_app']  = 1;

			$d = $detalle['VentaDetalle'];
			$d['confirmado_app'] = 1;
			
			$confirmar = 1;

			$v             =  new View();
			$v->autoRender = false;
			$v->output     = '';
			$v->layoutPath = '';
			$v->layout     = '';
			$v->set(compact('d', 'confirmar'));	

			$html_tr = $v->render('/Elements/ventas/tr-producto-modal');

		}
		
		if (!empty($detalles)) {
			ClassRegistry::init('VentaDetalle')->saveMany($detalles);
		}

		$this->Venta->id = $id;
		$this->Venta->saveField('picking_fecha_termino', date('Y-m-d H:i:s'));

		$this->set(array(
			'response'   => true,
			'tr' => $html_tr,
			'_serialize' => array('response', 'tr')
        ));
		
	}


	/**
	 * Usado por el webhook configurado en Linio
	 * Ej actualizar: https://sistemasdev.nodriza.cl/api/ventas/linio/actualizar/1
	 * Ej crear: https://sistemasdev.nodriza.cl/api/ventas/linio/crear/1
	 * Ref: https://sellerapi.sellercenter.net/docs/entities-payload-definition
	 * @param  string $tipo           crear o actualizar
	 * @param  [type] $marketplace_id ID del marketplace
	 * @return
	 */
	public function api_venta_linio($tipo = 'crear', $marketplace_id)
	{	

		if (!ClassRegistry::init('Marketplace')->exists($marketplace_id)) {
			echo json_encode(array(
				'code' => 404,
				'created' => false,
				'message' => 'Marketplace no encontrado'
			));

			exit;
		}


		$respuesta = array(
			'code' => 500,
			'created' => false,
			'message' => 'Error inexplicable'
		);

		#{"event":"onOrderCreated","payload":{"OrderId":1833030}}

		$log = array();

		if ($tipo == 'crear') {

			$log[] = array(
				'Log' => array(
					'administrador' => 'Linio Webhook Crear',
					'modulo' => 'Ventas',
					'modulo_accion' => json_encode($this->request->data)
				)
			);

			$accion = $this->crear_venta_linio($marketplace_id, $this->request->data['payload']['OrderId']);

			if ($accion) {
				
				$respuesta['code'] = 200;
				$respuesta['created'] = true;
				$respuesta['message'] = 'Venta #'. $this->request->data['payload']['OrderId'].' creada con éxito';

			}

		}

		if ($tipo == 'actualizar') {

			# {"event":"onOrderItemsStatusChanged","payload":{"OrderId":1824977,"OrderItemIds":["1701255"],"NewStatus":"ready_to_ship"}}

			$log[] = array(
				'Log' => array(
					'administrador' => 'Linio Webhook Actualizar',
					'modulo' => 'Ventas',
					'modulo_accion' => json_encode($this->request->data)
				)
			);

			$venta = $this->Venta->find('first', array(
				'conditions' => array(
					'Venta.id_externo' => $this->request->data['payload']['OrderId']
				),
				'fields' => array(
					'Venta.id', 'Venta.id_externo', 'Venta.venta_estado_id', 'Venta.estado_anterior', 'Venta.venta_estado_responsable'
				)
			));


			if (empty($venta)) {
				
				$accion = $this->crear_venta_linio($marketplace_id, $this->request->data['payload']['OrderId']);

				if ($accion) {
					
					$respuesta['code'] = 200;
					$respuesta['created'] = true;
					$respuesta['message'] = 'Venta #'. $this->request->data['payload']['OrderId'].' creada con éxito';

				}

			}else{

				$accion = $this->actualizar_venta_linio($marketplace_id, $this->request->data['payload']['OrderId'], $venta, $this->request->data['payload']['NewStatus']);
				
				if ($accion) {
					
					$respuesta['code'] = 200;
					$respuesta['created'] = true;
					$respuesta['message'] = 'Venta #'. $this->request->data['payload']['OrderId'].' actualizada con éxito';

				}

			}

		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		echo json_encode($respuesta);
		exit;
		
	}


	/**
	 * Enpoint: /api/ventas/meli/:tipo/:marketplace_id
	 * Ref: https://developers.mercadolibre.cl/es_ar/productos-recibe-notificaciones
	 * @param  string $tipo           crear/actualizar: Meli no hace diferencia por sus tipos de notificaciones, así que por ahora se usa solo crear tanto para crear y como para actualizar.
	 * @param  [type] $marketplace_id Id del marketplace en el sistema
	 * @return json
	 */
	public function api_venta_meli($tipo = 'crear', $marketplace_id)
	{	
		if (!ClassRegistry::init('Marketplace')->exists($marketplace_id)) {
			echo json_encode(array(
				'code' => 404,
				'created' => false,
				'message' => 'Marketplace no encontrado'
			));

			exit;
		}


		# Solo método POST
		if (!$this->request->is('post')) {
			$respuesta = array(
				'code'    => 501,
				'name' => 'error',
				'message' => 'Método no permitido'
			);

			throw new CakeException($respuesta);
		}

		$respuesta = array(
			'code' => 500,
			'created' => false,
			'message' => 'Error inexplicable'
		);

		$log = array();

		$id_venta = str_replace('/orders/', '', $this->request->data['resource']);

		#Vemos si existe en la BD
		$existe = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id_externo'     => $id_venta,
				'Venta.marketplace_id' => $marketplace_id
			),
			'fields' => array(
				'Venta.id'
			)
		));

		if (!empty($existe)) {

			$accion = $this->actualizar_venta_meli($marketplace_id, $id_venta);

			$log[] = array(
				'Log' => array(
					'administrador' => 'Meli Notification Actualizar',
					'modulo' => 'Ventas',
					'modulo_accion' => json_encode($this->request->data)
				)
			);

		}else{

			$log[] = array(
				'Log' => array(
					'administrador' => 'Meli Notification Crear',
					'modulo' => 'Ventas',
					'modulo_accion' => json_encode($this->request->data)
				)
			);

			$accion = $this->crear_venta_meli($marketplace_id, $id_venta);
		
		}

		if ($accion) {
			
			$respuesta['code'] = 200;
			$respuesta['created'] = true;
			$respuesta['message'] = 'Venta #'. $id_venta . ' creada/actualizada con éxito';

		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		echo json_encode($respuesta);
		exit;
	}


	/**
	 * Enpoint  /api/ventas/prestashop/:tienda_id.json
	 * @param  int $tienda_id identificador de la tienda a la cual se relacionan las ventas
	 * @return json
	 */
	public function api_venta_prestashop($tienda_id)
	{	

		if (!ClassRegistry::init('Tienda')->exists($tienda_id)) {
			$response = array(
				'code' => 404,
				'created' => false,
				'message' => 'Tienda no encontrada'
			);

			throw new CakeException($response);
		}

		# Solo método POST
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501,
				'name' => 'error',
				'message' => 'Método no permitido'
			);

			throw new CakeException($response);
		}

		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		$log = array();

		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop rest',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($this->request->data)
			)
		);

		$tokeninfo = ClassRegistry::init('Token')->obtener_propietario_token_full($this->request->query['token']);

		$log[] = array(
			'Log' => array(
				'administrador' => 'Prestashop rest - propietario - ' . $this->request->data['id_externo'],
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($tokeninfo)
			)
		);

		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id_externo' => $this->request->data['id_externo']
			),
			'fields' => array('Venta.id')
		));

		$accion = false;

		$respuesta = array(
			'code' => 500,
			'created' => false,
			'message' => 'Error inexplicable'
		);

		if (empty($venta)) {
			$accion = $this->crear_venta_prestashop($tienda_id, $this->request->data['id_externo'], $this->request->data['nuevo_estado']);
		}else{
			$accion = $this->actualizar_venta_prestashop($tienda_id, $this->request->data['id_externo'], $this->request->data['nuevo_estado']);
		}

		if ($accion) {
			$respuesta['code'] = 200;
			$respuesta['created'] = true;
			$respuesta['message'] = 'Venta #'. $this->request->data['id_externo'] . ' creada/actualizada con éxito';

		}else{
			throw new CakeException($respuesta);
		}

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);
		
		$this->set(array(
			'response'   => $respuesta,
			'_serialize' => array('response')
        ));

	}


	/**
	 * 
	 */
	public function api_notificar_stockout($id)
	{
		# Sólo método Get
		if (!$this->request->is('get')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only GET request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			throw new CakeException($response);
		}

		$qry = array(
			'conditions' => array(
				'Venta.id' => $id
			),
			'contain' => array(
				'OrdenCompra' => array(
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.id'
						)
					),
					'fields' => array(
						'OrdenCompra.id'
					)
				),
				'VentaCliente' => array(
					'fields' => array(
						'VentaCliente.id',
						'VentaCliente.nombre',
						'VentaCliente.apellido',
						'VentaCliente.email'
					)
				),
				'VentaEstado' => array(
					'VentaEstadoCategoria' => array(
						'fields' => array(
							'VentaEstadoCategoria.id',
							'VentaEstadoCategoria.venta',
							'VentaEstadoCategoria.envio',
							'VentaEstadoCategoria.rechazo',
							'VentaEstadoCategoria.cancelado',
							'VentaEstadoCategoria.final'
						)
					),
					'fields' => array(
						'VentaEstado.id',
						'VentaEstado.venta_estado_categoria_id',
					)
				),
				'VentaDetalle' => array(
					'VentaDetalleProducto' => array(
						'fields' => array(
							'VentaDetalleProducto.id', 
							'VentaDetalleProducto.nombre',
							'VentaDetalleProducto.codigo_proveedor'
						)
					),
					'fields' => array(
						'VentaDetalle.id',
						'VentaDetalle.venta_detalle_producto_id',
						'VentaDetalle.notificado_stockout',
						'VentaDetalle.cantidad'
					)
				),
				'Tienda' => array(
					'fields' => array(
						'Tienda.id',
						'Tienda.nombre',
						'Tienda.logo',
						'Tienda.mandrill_apikey',
						'Tienda.url',
						'Tienda.direccion'
					)
				)
			),
			'joins' => array(
				array(
					'table' => 'rp_venta_detalles',
					'alias' => 'vd',
					'type' => 'INNER',
					'conditions' => array(
						'vd.venta_id = Venta.id',
						'vd.cantidad_anulada < vd.cantidad'
					)
				),
				array(
					'table' => 'rp_orden_compras_venta_detalle_productos',
					'alias' => 'oc_productos',
					'type' => 'INNER',
					'conditions' => array(
						'oc_productos.venta_detalle_producto_id = vd.venta_detalle_producto_id',
						'oc_productos.estado_proveedor' => array('stockout')
					)
				),
				array(
					'table' => 'rp_ventas',
					'alias' => 'ventas',
					'type' => 'INNER',
					'conditions' => array(
						'ventas.id = vd.venta_id'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.venta_cliente_id',
				'Venta.venta_estado_id',
				'Venta.tienda_id',
				'Venta.id_externo'
			)
		);

		$venta = $this->Venta->find('first', $qry);
		
		if (empty($venta)) {
			$response = array(
				'code'    => 521, 
				'name' => 'error',
				'message' => 'No corresponde notificación'
			);

			throw new CakeException($response);
		}

		# Debe ser un pago aceptado
		if (!$venta['VentaEstado']['VentaEstadoCategoria']['venta'] || $venta['VentaEstado']['VentaEstadoCategoria']['envio'] || $venta['VentaEstado']['VentaEstadoCategoria']['final']) {
			$response = array(
				'code'    => 522, 
				'name' => 'error',
				'message' => 'No permite notificación'
			);

			throw new CakeException($response);
		}


		foreach ($venta['VentaDetalle'] as $iv => $vd) {

			$estado_producto = array_unique(Hash::extract($venta['OrdenCompra'], '{n}.VentaDetalleProducto.{n}.OrdenComprasVentaDetalleProducto[venta_detalle_producto_id='.$vd['venta_detalle_producto_id'].'].estado_proveedor'));
			$estado_nota     = array_unique(Hash::extract($venta['OrdenCompra'], '{n}.VentaDetalleProducto.{n}.OrdenComprasVentaDetalleProducto[venta_detalle_producto_id='.$vd['venta_detalle_producto_id'].'].nota_proveedor'));

			# Si no se fuerza la notificación se quitan los proudctos ya notificados
			if (!isset($this->request->query['force'])
				&& $vd['notificado_stockout']) {
				unset($venta['VentaDetalle'][$iv]);
				continue;
			}

			# si no hay un estado de productos tambien se quitan
			if (empty($estado_producto)) {
				unset($venta['VentaDetalle'][$iv]);
				continue;
			}

			# solo notificamos stockout
			if ($estado_producto[0] != 'stockout') {
				unset($venta['VentaDetalle'][$iv]);
				continue;
			}

			$venta['VentaDetalle'][$iv] = array_replace_recursive($venta['VentaDetalle'][$iv], array(
				'estado_proveedor' => $estado_producto[0],
				'estado_nota' => $estado_nota[0] 
			));
		}	

		if (empty($venta['VentaDetalle'])) {
			$response = array(
				'code'    => 522, 
				'name' => 'error',
				'message' => 'No permite notificación'
			);

			throw new CakeException($response);
		}


		# Creamos el token del cliente de 4 dias para responder el email
		$token = ClassRegistry::init('Token')->crear_token_cliente($venta['Venta']['venta_cliente_id'], $venta['Venta']['tienda_id'], 96);

		/**
		 * Clases requeridas
		 */
		$this->View           = new View();
		$this->View->viewPath = 'Ventas' . DS . 'emails';
		$this->View->layout   = 'backend' . DS . 'emails';
		
		$url = obtener_url_base();

		/**
		 * Correo a ventas
		 */
		$this->View->set(compact('venta', 'token', 'url'));
		$html = $this->View->render('notificar_stockout_cliente');
		
		$mandrill_apikey = $venta['Tienda']['mandrill_apikey'];
		
		if (empty($mandrill_apikey)) {
			$response = array(
				'code'    => 523, 
				'name' => 'error',
				'message' => 'Mandrill apikey not found'
			);

			throw new CakeException($response);
		}

		$mandrill = $this->Components->load('Mandrill');
		
		$mandrill->conectar($mandrill_apikey);

		$asunto = '['.$venta['Tienda']['nombre'].'] Venta #' . $venta['Venta']['id'] . ' - Hay productos sin stock';
		
		if (Configure::read('ambiente') == 'dev') {
			$asunto = '['.$venta['Tienda']['nombre'].'-DEV] Venta #' . $venta['Venta']['id'] . ' - Hay productos sin stock';
		}

		$remitente = array(
			'email' => 'no-reply@nodriza.cl',
			'nombre' => 'Ventas ' . $venta['Tienda']['nombre']
		);

		$destinatarios = array(
			array(
				'email' => trim($venta['VentaCliente']['email']),
				'name' => $venta['VentaCliente']['nombre'] . ' ' . $venta['VentaCliente']['apellido']
			)
		);

		$enviado = $mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);

		$this->set(array(
            'notificado' => $enviado,
            '_serialize' => array('notificado')
        ));
	}


	/**
	 * Permite cambiar el estado de picking de una venta
	 * @param int $id Id de la venta
	 *
	 * @return mixed
	 */
	public function api_set_picking_estado($id)
	{
		# Sólo método post
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only POST request allow'
			);

			throw new CakeException($response);
		}


		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		# No existe venta
		$this->Venta->id = $id;
		if (!$this->Venta->exists()) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			throw new CakeException($response);
		}

		$log = array();
		
		if (empty($this->request->data['Venta']['estado']))
		{
			$response = array(
				'code'    => 501, 
				'name' => 'error',
				'message' => 'estado es Requerido'
			);

			throw new CakeException($response);
		}

		if ($this->request->data['Venta']['estado'] == 'en_revision' 
			&& !isset($this->request->data['Venta']['picking_motivo_revision']))
		{
			$response = array(
				'code'    => 501, 
				'name' => 'error',
				'message' => 'picking_motivo_revision es Requerido'
			);

			throw new CakeException($response);
		}

		$venta = array(
			'Venta' => array(
				'id' => $id,
				'picking_estado' => $this->request->data['Venta']['estado']
			)
		);

		if (isset($this->request->data['Venta']['picking_motivo_revision']))
		{
			$venta = array_replace_recursive($venta, array(
				'Venta' => array(
					'picking_motivo_revision' => $this->request->data['Venta']['picking_motivo_revision']
				)
			));
		}
		
		if ($this->Venta->save($venta))
		{	

			$usuario = ClassRegistry::init('Token')->obtener_propietario_token($this->request->query['token']);

			# Registrar log
			$log[] = array(
				'Log' => array(
					'administrador' => $usuario,
					'modulo' => 'Ventas',
					'modulo_accion' => sprintf('Se cambia estado picking venta id %d: ', $id, json_encode($this->request->data))
				)
			);

			# Generamos la etiqueta externa si corresponde
			if ($venta['Venta']['picking_estado'] == 'empaquetando')
			{	
				$metodo_envios = new MetodoEnviosController();
				$metodo_envios->generar_etiqueta_envio_externo($id);
			}

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);

			$this->set(array(
				'response' => array(
					'code' => 200,
					'name' => 'success',
					'message' => 'Cambio de estado realizado con éxito',
					'data' => array()
				),
				'_serialize' => array('response')
			));
		}
		else 
		{
			$response = array(
				'code'    => 500, 
				'name' => 'error',
				'message' => 'No fue posible cambiar el estado'
			);
	
			throw new CakeException($response);
		}
		
	}


	public function api_cambiar_estado_por_envios($id)
	{
		# Sólo método post
		if (!$this->request->is('post')) {
			$response = array(
				'code'    => 501, 
				'message' => 'Only POST request allow'
			);

			throw new CakeException($response);
		}

		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		if (!isset($this->request->data['estado'])) {
			$response = array(
				'code'    => 502, 
				'name' => 'error',
				'message' => 'estado requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 505, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		# No existe venta
		if (!$this->Venta->exists($id)) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Venta no encontrada'
			);

			throw new CakeException($response);
		}

		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id' => $id
			),
			'contain' => array(
				'MetodoEnvio' => array(
					'fields' => array('MetodoEnvio.dependencia')
				),
				'Transporte'
			),
			'fields' => array(
				'Venta.id',
				'Venta.id_externo',
				'Venta.tienda_id',
				'Venta.marketplace_id'
			)
		));


		$estado_nuevo_arr = array();

		# Cambio de estado para boosmap
		if ($venta['MetodoEnvio']['dependencia'] == 'boosmap' && !empty($venta['Transporte']))
		{
			# obtenemos el estado homologado
			$estadoNombre = $this->Boosmap->obtener_estado_nombre($this->request->data['estado']);
			$estado_nuevo_arr = ClassRegistry::init('VentaEstado')->obtener_estado_por_nombre($estadoNombre);
			
		}


		# Si el estado nuevo viene vacio no actualizamos
		if (empty($estado_nuevo_arr))
		{
			$response = array(
				'code'    => 501, 
				'name' => 'error',
				'message' => 'No es necesario actualizar'
			);

			throw new CakeException($response);
		}

		/*try {
			$cambiar_estado = $this->cambiarEstado($id, $venta['Venta']['id_externo'], $this->request->data['Venta']['venta_estado_id'], $this->request->data['Venta']['tienda_id'], $this->request->data['Venta']['marketplace_id'], '', '', $this->Session->read('Auth.Administrador.nombre'));
		} catch (Exception $e) {
			$respuesta['code'] = 506;
			$respuesta['message'] = $e->getMessage();
			echo json_encode($respuesta);
			exit;
		}*/

	}



	public function admin_actualizar_venta_por_envios($id)
	{
		if ($this->actualizar_venta_por_envios($id, $this->Auth->user('email')))
		{
			$this->Session->setFlash('Venta gestionada y/o actualizada con éxito.', null, array(), 'success');
		}
		else
		{
			$this->Session->setFlash('No fue posible actualizar la venta por el estado de envios.', null, array(), 'warning');
		}

		$this->redirect($this->referer('/', true));
	}


	/**
	 * Método encargado de actualizar una venta según sus estados de envios válidos
	 * 
	 * @param int $id  Identificador de la venta
	 * 
	 * @return bool
	 */
	public function actualizar_venta_por_envios($id, $responsable = '')
	{	
		$log = array();

		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id' => $id
			),
			'contain' => array(
				'Transporte' => array(
					'fields' => array(
						'Transporte.id'
					)
				)
			),
			'fields' => array(
				'Venta.id',
				'Venta.id_externo',
				'Venta.tienda_id',
				'Venta.marketplace_id'
			)
		));
		
		$historicos = array();

		$log[] = array(
			'Log' => array(
				'administrador' => 'Auto',
				'modulo' => 'Ventas',
				'modulo_accion' => 'comienza proceso de actualización de venta ' . $id . ' por concepto de estados de envio'
			)
		);

		foreach ($venta['Transporte'] as $it => $t) 
		{	
			$ultimo_estado = ClassRegistry::init('EnvioHistorico')->find('first', array(
				'conditions' => array(
					'EnvioHistorico.transporte_venta_id' => $t['TransportesVenta']['id'],
					'EnvioHistorico.notificado' => 0
				),
				'contain' => array(
					'EstadoEnvio' => array(
						'EstadoEnvioCategoria'
					)
				),
				'joins' => array(
					array(
						'table' => 'rp_estado_envios',
						'alias' => 'ee',
						'type' => 'INNER',
						'conditions' => array(
							'ee.id = EnvioHistorico.estado_envio_id'
						)
					),
					array(
						'table' => 'rp_estado_envio_categorias',
						'alias' => 'eec',
						'type' => 'INNER',
						'conditions' => array(
							'eec.id = ee.estado_envio_categoria_id',
							'eec.actualizar_venta'
						)
					)
				),
				'order' => array('EnvioHistorico.created' => 'desc')
			));

			if (!$ultimo_estado)
			{
				continue;
			}

			$historicos[] = $ultimo_estado;

		}

		if (empty($historicos)){

			$log[] = array(
				'Log' => array(
					'administrador' => 'Auto',
					'modulo' => 'Ventas',
					'modulo_accion' => 'No registran cambios de envio: ' . $id
				)
			);

			ClassRegistry::init('Log')->saveMany($log);

			return false;
		}
		
		foreach ($historicos as $ih => $h) 
		{	
			if ($h['EstadoEnvio']['EstadoEnvioCategoria']['actualizar_venta'])
			{	
				$estado_actualizado = false;

				try {
					$estado_actualizado = $this->cambiarEstado($id, $venta['Venta']['id_externo'], $h['EstadoEnvio']['EstadoEnvioCategoria']['venta_estado_id'], $venta['Venta']['tienda_id'], $venta['Venta']['marketplace_id'], '', '', $h['EnvioHistorico']['canal'], $h['EnvioHistorico']['created']);
				} catch (Exception $e) {
					$log[] = array(
						'Log' => array(
							'administrador' => 'Auto',
							'modulo' => 'Ventas',
							'modulo_accion' => sprintf('Error #%d: %s', $id, $e->getMessage())
						)
					);
				}

				if ($estado_actualizado)
				{	
					# Registramos que este envio está notificado o ya cambió estado
					ClassRegistry::init('EnvioHistorico')->id = $h['EnvioHistorico']['id'];
					ClassRegistry::init('EnvioHistorico')->saveField('notificado', 1);
					ClassRegistry::init('EnvioHistorico')->clear();

					$log[] = array(
						'Log' => array(
							'administrador' => 'Auto',
							'modulo' => 'Ventas',
							'modulo_accion' => sprintf('Venta #%d actualizada: %s', $id, json_encode($h))
						)
					);
				}
				else
				{
					$log[] = array(
						'Log' => array(
							'administrador' => 'Auto',
							'modulo' => 'Ventas',
							'modulo_accion' => sprintf('Venta #%d no actualizada: %s', $id, json_encode($h))
						)
					);
				}
			}
		}

		$log[] = array(
			'Log' => array(
				'administrador' => 'Auto',
				'modulo' => 'Ventas',
				'modulo_accion' => 'Finaliza proceso de actualización por envios: ' . $id
			)
		);

		ClassRegistry::init('Log')->saveMany($log);

		return true;

	}


	public function admin_generar_historico_envios()
	{
		$ventas = $this->Venta->obtener_ventas_con_envios_sin_historico();
		
		if (empty($ventas))
		{
			$this->Session->setFlash('No hay ventas que actualizar.', null, array(), 'info');
		}

		$total = 0;

		foreach ($ventas as $venta) 
		{
			if ($this->actualizar_estados_envios($venta['Venta']['id']))
			{
				$total++;
			}
		}

		$this->Session->setFlash(sprintf('Se procesaron un total de %d ventas', $total), null, array(), 'success');

		$this->redirect(array('action' => 'index'));

	}

	

	/**
	 * Obtiene las ventas con envio que necesitan ser actualziadas
	 * 
	 * @return array Arreglo con las ventas actualizadas y/o procesadas
	 */
	public function actualizar_ventas_por_envios()
	{
		$ventas = $this->Venta->obtener_ventas_con_envios();
		
		if (empty($ventas))
		{
			return false;
		}

		$ventas_actualizadas = array();

		foreach ($ventas as $iv => $venta) {

			# Actualizamos los envios de las ventas
			$this->actualizar_estados_envios($venta['Venta']['id']);

			# Actualizamos las ventas por sus nuevos envios
			if ($this->actualizar_venta_por_envios($venta['Venta']['id'], 'Demonio'))
			{
				$ventas_actualizadas[] = $venta;
			}
		}

		return $ventas_actualizadas;

	}


	/**
	 * Obtiene y actualiza los estados de los envios de una venta dado su ID
	 * 
	 * @param int $id Identificador de la venta
	 * 
	 * @return bool
	 */
	public function actualizar_estados_envios($id)
	{
		$venta = $this->Venta->obtener_venta_por_id($id);

		# Registro de estados para Boosmap
		if ($venta['MetodoEnvio']['dependencia'] == 'boosmap' && $venta['MetodoEnvio']['generar_ot']) {
			$this->Boosmap = $this->Components->load('Boosmap');
			# Creamos cliente boosmap
			$this->Boosmap->crearCliente($venta['MetodoEnvio']['boosmap_token']);

			# Obtenemos y registramos los estados de los envios
			return $this->Boosmap->registrar_estados($venta['Venta']['id']);
		}

		# Registro de estados para Starken
		if ($venta['MetodoEnvio']['dependencia'] == 'starken' && $venta['MetodoEnvio']['generar_ot']) {
			$this->Starken = $this->Components->load('Starken');
			# Obtenemos y registramos los estados de los envios
			return $this->Starken->registrar_estados($venta['Venta']['id']);
		}

		# Registro de estados para BlueExpress
		if ($venta['MetodoEnvio']['dependencia'] == 'blueexpress' && $venta['MetodoEnvio']['generar_ot']) {
			
			$this->BlueExpress = $this->Components->load('BlueExpress');
			# Obtenemos y registramos los estados de los envios
			return $this->BlueExpress->registrar_estados($venta['Venta']['id']);
		}

		return false;
	}


	/**
	 * Actualizar el estado de los envios admin
	 * 
	 * @param int $id Ide de la venta
	 * 
	 * @return redirect
	 */
	public function admin_actualizar_estados_envios($id)
	{
		if ($this->actualizar_estados_envios($id))
		{
			$this->Session->setFlash('Estados de los envios actualizados con éxito.', null, array(), 'success');
		}
		else
		{
			$this->Session->setFlash('No fue posible actualizar los estados de los envios.', null, array(), 'warning');
		}

		$this->redirect($this->referer('/', true));
	}

	
	/**
	 * api_cambiar_estado_desde_warehouse
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function api_cambiar_estado_desde_warehouse($id)
	{	
		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 401, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		if (!isset($this->request->data['estado_venta_id']))
		{
			$response = array(
				'code'    => 401, 
				'name' => 'error',
				'message' => 'estado_venta_id es requerido'
			);

			throw new CakeException($response);
		}

		# Validamos que el estado recibido sea de logistica
		if (!ClassRegistry::init('VentaEstado')->estado_mueve_bodega($this->request->data['estado_venta_id']))
		{
			$response = array(
				'code'    => 401, 
				'name' => 'error',
				'message' => 'estado_venta_id debe ser de tipo logistico'
			);

			throw new CakeException($response);
		}

		$tokeninfo = ClassRegistry::init('Token')->obtener_propietario_token_full($this->request->query['token']);
		
		# Body
		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id' => $id
			)
		));

		try {
			$cambiar_estado = $this->cambiarEstado($id, $venta['Venta']['id_externo'], $this->request->data['estado_venta_id'], $venta['Venta']['tienda_id'], $venta['Venta']['marketplace_id'], '', '', $tokeninfo['Administrador']['email']);
		} catch (Exception $e) {
			
			$response = array(
				'code'    => 500, 
				'name' => 'error',
				'message' => $e->getMessage()
			);

			throw new CakeException($response);
		}

		$respuesta = array(
			'code' => 401,
			'message' => 'No fue posible cambiar el estado',
			'body' => array()
		);

		if ($cambiar_estado) 
		{
			$respuesta = array(
				'code' => 200,
				'message' => 'Estado actualizado con éxito',
				'body' => array()
			);
		}

		$this->set(array(
            'response' => $respuesta,
            '_serialize' => array('response')
		));
	}
	/**
	 * api_cambiar_estado_desde_warehouse_v2
	 * Se cambia estado a venta segun se hayan terminado de embalar todos los productos de esta
	 * Los estados se tomaran según el metodo de envio
	 * @param  mixed $id de la Venta
	 * @return void
	 */
	public function api_cambiar_estado_desde_warehouse_v2($id)
	{	
		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 401, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}
						
		$tokeninfo = ClassRegistry::init('Token')->obtener_propietario_token_full($this->request->query['token']);
		
		
		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.id' => $id
			),
			'contain' =>
			[
				'VentaDetalle',
				'MetodoEnvio' =>[ 'fields' => ['MetodoEnvio.embalado_venta_estado_parcial_id','MetodoEnvio.embalado_venta_estado_id','MetodoEnvio.consolidacion_venta_estado_id','MetodoEnvio.retiro_local']]
			]
		));
		$logs 		  		  		= [];

		// * Se consultan embalajes finalizados para validar que estado colocar a la venta
		$embalajesExceptoCancelados = $this->WarehouseNodriza->ObtenerEmbalajesVenta($id);	

		$logs[] = [
			'Log' =>
			[
				'administrador' => "Procesando cambio estado vid {$id}",
				'modulo'        => 'VentasController api_cambiar_estado_desde_warehouse_v2',
				'modulo_accion' => json_encode([
					"venta"							=> $venta,
					"embalajesExceptoCancelados"	=> $embalajesExceptoCancelados,
					"tokeninfo"						=> $tokeninfo
				])
			]
		];	
		
		$cambiar_estado 	  		= false;
		$nuevo_estado   	  		= null;
	
		try {

			// *Si hay embalajes distinto a la bodega de la venta se considera el estado en consolidacion
			$embalajes_en_otras_bodegas 			= count(Hash::extract($embalajesExceptoCancelados['response']['body'], "{n}[bodega_id!={$venta['Venta']['bodega_id']}]"));
			$cantidad	       						= array_sum(Hash::extract($venta['VentaDetalle'], "{n}.cantidad")) - array_sum(Hash::extract($venta['VentaDetalle'], "{n}.cantidad_anulada"));
			$cantidad_embalada 						= array_sum(Hash::extract($embalajesExceptoCancelados['response']['body'], "{n}.embalaje_producto.{n}.cantidad_embalada"));

			$cantidadEmbalajes						= count($embalajesExceptoCancelados['response']['body']);
			// $ExisteEmbalajes_listo_para_trasladar 	= count(Hash::extract($embalajesExceptoCancelados['response']['body'], "{n}[estado=listo_para_trasladar]"));
			// $ExisteEmbalajes_en_traslado_a_bodega 	= count(Hash::extract($embalajesExceptoCancelados['response']['body'], "{n}[estado=en_traslado_a_bodega]"));
			$cantidad_pendiente_entrega 			= array_sum(Hash::extract($venta['VentaDetalle'], "{n}.cantidad_pendiente_entrega"));
			$no_han_finalizado						= count(Hash::extract($embalajesExceptoCancelados['response']['body'], "{n}[fecha_finalizado=/^$/]"));
			
			if ($embalajes_en_otras_bodegas > 0 && $cantidadEmbalajes >= 1 && $cantidad_pendiente_entrega > 0 && $no_han_finalizado != 0) {

					// * Se valida que metodo tenga el estado a cambiar
					if (is_null($venta['MetodoEnvio']['consolidacion_venta_estado_id'])) {
	
						$logs[] = [
							'Log' =>
							[
								'administrador' => "Problemas para actualizar vid {$id}",
								'modulo'        => 'VentasController',
								'modulo_accion' => "Metodo de envio {$venta['Venta']['metodo_envio_id']} no tiene configurado 'consolidación', valor actual Null"
							]
						];

					}else{
	
						$nuevo_estado   = $venta['MetodoEnvio']['consolidacion_venta_estado_id'];
						$cambiar_estado = $this->cambiarEstado($id, $venta['Venta']['id_externo'], $nuevo_estado, $venta['Venta']['tienda_id'], $venta['Venta']['marketplace_id'], '', '', $tokeninfo['Administrador']['email']);
					}
			}else{

				if ($cantidad != $cantidad_embalada) {

					// * Se valida que metodo tenga el estado a cambiar
					if (is_null($venta['MetodoEnvio']['embalado_venta_estado_parcial_id'])) {

						$logs[] = [
							'Log' =>
							[
								'administrador' => "Problemas para actualizar vid {$id}",
								'modulo'        => 'VentasController',
								'modulo_accion' => "Metodo de envio {$venta['Venta']['metodo_envio_id']} no tiene configurado 'estado parcial', valor actual Null"
							]
						];
					}else{

						$nuevo_estado   = $venta['MetodoEnvio']['embalado_venta_estado_parcial_id'];
						$cambiar_estado = $this->cambiarEstado($id, $venta['Venta']['id_externo'], $nuevo_estado, $venta['Venta']['tienda_id'], $venta['Venta']['marketplace_id'], '', '', $tokeninfo['Administrador']['email']);
					}

				} else {

					if (is_null($venta['MetodoEnvio']['embalado_venta_estado_id'])) {

						$logs[] = [
							'Log' =>
							[
								'administrador' => "Problemas para actualizar vid {$id}",
								'modulo'        => 'VentasController',
								'modulo_accion' => "Metodo de envio {$venta['Venta']['metodo_envio_id']} no tiene configurado 'estado completo', valor actual Null"
							]
						];
					
					}else{

						$nuevo_estado   = $venta['MetodoEnvio']['embalado_venta_estado_id'];
						$cambiar_estado = $this->cambiarEstado($id, $venta['Venta']['id_externo'],$nuevo_estado, $venta['Venta']['tienda_id'], $venta['Venta']['marketplace_id'], '', '', $tokeninfo['Administrador']['email']);
					}

				}

			}
			
		} catch (Exception $e) {

			$cambiar_estado = false;
		}


		if ($cambiar_estado) {
			$logs[] = [
				'Log' =>
				[
					'administrador' => "Se actualiza estado vid {$id}",
					'modulo'        => 'VentasController',
					'modulo_accion' => "Estado original {$venta['Venta']['venta_estado_id']} | Estado después {$nuevo_estado}"
				]
			];
		}
			
		if ($logs) {
			ClassRegistry::init('Log')->saveMany($logs);
		}
	
		$respuesta = array(
			'code'    => 200,
			'message' => 'Estado actualizado con éxito',
			'body'    => array()
		);
		
		$this->set(array(
            'response'   => $respuesta,
            '_serialize' => array('response')
		));
	}

	
	/**
	 * admin_crear_embalaje_masivo
	 *
	 * @return void
	 */
	public function admin_crear_embalaje_masivo()
	{
		$ventas = $this->Venta->find('all', array(
			'conditions' => array(
				'Venta.picking_estado' => 'empaquetar'
			),
			'fields' => array(
				'Venta.id'
			)
		));

		$procesadas = 0;

		foreach ($ventas as $v) 
		{
			$this->WarehouseNodriza->procesar_embalajes($v['Venta']['id']);
			$procesadas++;
		
		}

		$this->Session->setFlash(sprintf('%d ventas procesadas', $procesadas), null, array(), 'success');
		$this->redirect($this->referer('/', true));
	}

	public function admin_regenerar_etiqueta($ot)
	{

		$transportes_venta =  ClassRegistry::init('TransportesVenta')->find('first',
		[
			'conditions' => [
				'TransportesVenta.id' 		=> $ot,
		],
			'fields' => [
				'TransportesVenta.venta_id',
				'TransportesVenta.cod_seguimiento'
			]
		]);
		
		$venta_id= $transportes_venta['TransportesVenta']['venta_id'];

		$venta = ClassRegistry::init('Venta')->find('first',[
			'conditions'=>[
				'Venta.id' => $venta_id
			],
			'fields'=>['id'],
			'contain' => array(
				'MetodoEnvio' => array(
					'fields' => array(
						'MetodoEnvio.dependencia'
					)
				)
				
			)
		]);
		
		switch ($venta['MetodoEnvio']['dependencia']) {
			case 'blueexpress':
				$etiqueta = $this->BlueExpress->regenerar_etiqueta($transportes_venta['TransportesVenta']['cod_seguimiento'],$venta_id);
				break;
			case 'boosmap':
				$etiqueta = $this->Boosmap->regenerar_etiqueta($transportes_venta,$venta_id);
				break;
			default:

				$this->Session->setFlash("La dependencia {$venta['MetodoEnvio']['dependencia']} no tiene configurada regenerar etiqueta", null, array(), 'danger');
				$this->redirect(array('action' => 'view', $venta_id, 'controller' => 'ventas'));

				break;
		}

		if (!empty($etiqueta['url'])) {

			$url_etiqueta = $etiqueta['url'];

			if(ClassRegistry::init('TransportesVenta')->exists($ot) && ClassRegistry::init('Venta')->exists($venta_id)){
				
				ClassRegistry::init('TransportesVenta')->id = $ot;
				ClassRegistry::init('Venta')->id			= $venta_id;

				if(ClassRegistry::init('TransportesVenta')->saveField('etiqueta',$url_etiqueta) && ClassRegistry::init('Venta')->saveField('etiqueta_envio_externa',$url_etiqueta)){
				
					$this->Session->setFlash('Se creo etiqueta', null, array(), 'success');
					$this->redirect(array('action' => 'view', $venta_id ,'controller' => 'ventas'));
				}
			}
		}

		$this->Session->setFlash('No se pudo crear su etiqueta', null, array(), 'danger');
		$this->redirect(array('action' => 'view', $venta_id, 'controller' => 'ventas'));
	}


	/**	
	 * Obtiene los estados de despacho de la venta
	 * @param int $id Identificador de la venta
	 * @return json
	 */
	public function api_getSeguimiento($id)
	{	
		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 401, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		$estados = ClassRegistry::init('TransportesVenta')->find('all', array(
			'conditions' => array(
				'TransportesVenta.venta_id' => $id
			),
			'contain' => array(
				'EnvioHistorico'=>['order' => array('EnvioHistorico.created' => 'ASC'),],
			),
			
		));

		$respuesta = array(
			'code' => 404,
			'message' => 'No se encontraron resultados',
			'body' => $estados
		);

		if ($estados) 
		{
			$respuesta = array(
				'code' => 200,
				'message' => 'Se encontraron resultados',
				'body' => $estados
			);
		}

		$this->set(array(
            'response' => $respuesta,
            '_serialize' => array('response')
		));
	}

	/**	
	 * Obtiene los estados de despacho de la venta
	 * @param text $red Referencia de la venta
	 * @return json
	 */
	public function api_getSeguimientoByRef($ref)
	{	
		# Existe token
		if (!isset($this->request->query['token'])) {
			$response = array(
				'code'    => 401, 
				'name' => 'error',
				'message' => 'Token requerido'
			);

			throw new CakeException($response);
		}

		# Validamos token
		if (!ClassRegistry::init('Token')->validar_token($this->request->query['token'])) {
			$response = array(
				'code'    => 404, 
				'name' => 'error',
				'message' => 'Token de sesión expirado o invalido'
			);

			throw new CakeException($response);
		}

		$venta = $this->Venta->find('first', array(
			'conditions' => array(
				'Venta.referencia' => trim($ref)
			),
			'fields' => array(
				'Venta.id'
			)
		));

		$estados = ClassRegistry::init('TransportesVenta')->find('all', array(
			'conditions' => array(
				'TransportesVenta.venta_id' => $venta['Venta']['id']
			),
			'contain' => array(
				'Transporte' => array(
					'fields' => array(
						'Transporte.nombre'
					)
				),
				'EnvioHistorico'=>[
					'EstadoEnvio' => array(
						'EstadoEnvioCategoria'
					),
					'order' => array('EnvioHistorico.created' => 'ASC')
				],
			),
			
		));

		$respuesta = array(
			'code' => 404,
			'message' => 'No se encontraron resultados',
			'body' => $estados
		);

		if ($estados) 
		{
			$respuesta = array(
				'code' => 200,
				'message' => 'Se encontraron resultados',
				'body' => $estados
			);
		}

		$this->set(array(
            'response' => $respuesta,
            '_serialize' => array('response')
		));
	}

}
