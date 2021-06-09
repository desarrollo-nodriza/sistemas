<?php
App::uses('AppController', 'Controller');
class EmbalajeWarehousesController extends AppController
{	

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
	

	/**
	 * admin_index
	 *
	 * @return void
	 */
	public function admin_index()
	{	
		$paginate = array(); 
    	$conditions = array();

		// Filtrado de dtes por formulario
		if ( $this->request->is('post') ) 
		{
			$this->filtrar('embalajeWarehouses', 'index');
		}

		$paginate = array_replace_recursive($paginate, array(
			'limit' => 20,
			'order' => array('EmbalajeWarehouse.fecha_creacion' => 'ASC'),
			'contain' => array(
				'Venta',
				'Bodega' => array(
					'fields' => array(
						'Bodega.id',
						'Bodega.nombre'
					)
				),
				'MetodoEnvio' => array(
					'fields' => array(
						'MetodoEnvio.id',
						'MetodoEnvio.nombre'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.id',
						'Marketplace.nombre'
					)
				),
				'Comuna' => array(
					'fields' => array(
						'Comuna.id',
						'Comuna.nombre'
					)
				)
			)
		));

		# filtramos
		if ( isset($this->request->params['named']) ) 
		{
			foreach ($this->request->params['named'] as $campo => $valor) 
			{
				switch ($campo) 
				{
					case "id" :
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'EmbalajeWarehouse.id' => trim($valor)
							)
						));
						break;

					case "venta_id" :
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'EmbalajeWarehouse.venta_id' => trim($valor)
							)
						));
						break;

					case "estado" :
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'EmbalajeWarehouse.estado' => trim($valor)
							)
						));
						break;

					case "bodega_id" :
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'EmbalajeWarehouse.bodega_id' => trim($valor)
							)
						));
						break;
					
					case "marketplace_id" :
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'EmbalajeWarehouse.marketplace_id' => trim($valor)
							)
						));
						break;

					case "comuna_id" :
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'EmbalajeWarehouse.comuna_id' => trim($valor)
							)
						));
						break;

					case "prioritario" :
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'EmbalajeWarehouse.prioritario' => ($valor == 'si') ? 1 : 0
							)
						));
						break;

					case "fecha_desde" :
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'EmbalajeWarehouse.fecha_creacion >=' => trim($valor)
							)
						));
						break;

					case "fecha_hasta" :
						$paginate = array_replace_recursive($paginate, array(
							'conditions' => array(
								'EmbalajeWarehouse.fecha_creacion <=' => trim($valor) . ' 23:59:59'
							)
						));
						break;
				}
			}
		}

		$this->paginate = $paginate;
		
		BreadcrumbComponent::add('Embalajes ');

		$embalajes	= $this->paginate();

		$estados = $this->EmbalajeWarehouse->obtener_estados();
		$bodegas = $this->EmbalajeWarehouse->Bodega->find('list', array('conditions' => array('activo' => 1)));
		$marketplaces = $this->EmbalajeWarehouse->Marketplace->find('list', array('conditions' => array('activo' => 1)));
		$comunas = $this->EmbalajeWarehouse->Comuna->find('list');
		$prioritarios = array('no' => 'No', 'si' => 'Si');

		$this->set(compact('embalajes', 'estados', 'bodegas', 'marketplaces', 'comunas', 'prioritarios'));
	}

    
    /**
     * admin_view
     *
     * @param  mixed $id
     * @return void
     */
    public function admin_view($id)
	{	
		if ( ! $this->EmbalajeWarehouse->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

        $embalaje	= $this->EmbalajeWarehouse->find('first', array(
            'conditions'	=> array('EmbalajeWarehouse.id' => $id),
			'contain' => array(
				'EmbalajeProductoWarehouse' => array(
					'VentaDetalle',
					'VentaDetalleProducto'
				),
				'Venta' => array(
					'VentaDetalle' => array(
						'VentaDetalleProducto' => array(
							'fields' => array(
								'VentaDetalleProducto.nombre'
							)
						)
					),
					'VentaEstado' => array(
						'VentaEstadoCategoria'
					)
				),
				'Bodega' => array(
					'fields' => array(
						'Bodega.nombre'
					)
				),
				'MetodoEnvio' => array(
					'fields' => array(
						'MetodoEnvio.nombre'
					)
				),
				'Comuna' => array(
					'fields' => array(
						'Comuna.nombre'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.nombre'
					)
				)
			)
        ));

		$embalado_por = ClassRegistry::init('Administrador')->find('first', array(
			'conditions' => array(
				'id' => $embalaje['EmbalajeWarehouse']['responsable_id_procesando']
			),
			'fields' => array(
				'id',
				'nombre',
				'email'
			)
		));

		$finalizado_por = ClassRegistry::init('Administrador')->find('first', array(
			'conditions' => array(
				'id' => $embalaje['EmbalajeWarehouse']['responsable_id_procesando']
			),
			'fields' => array(
				'id',
				'nombre',
				'email'
			)
		));

		$cancelado_por = ClassRegistry::init('Administrador')->find('first', array(
			'conditions' => array(
				'id' => $embalaje['EmbalajeWarehouse']['responsable_id_procesando']
			),
			'fields' => array(
				'id',
				'nombre',
				'email'
			)
		));


		BreadcrumbComponent::add('Embalajes ', '/embalajeWarehouses');
		BreadcrumbComponent::add('Ver embalaje');

		$this->set(compact('embalaje', 'embalado_por', 'finalizado_por', 'cancelado_por'));

	}


	public function admin_review($id)
	{
		if ( ! $this->EmbalajeWarehouse->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ($this->request->is('post'))
		{	
			$this->request->data['EmbalajeWarehouse']['estado'] = 'listo_para_embalar';
			$this->request->data['EmbalajeWarehouse']['ultima_modifacion'] = date('Y-m-d H:i:s');
			
			if ($this->EmbalajeWarehouse->save($this->request->data))
			{
				$this->Session->setFlash('Embalaje procesado con éxito.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Embalaje no pudo ser procesado. Intente nuevamente.', null, array(), 'warning');
			}
		}

		$embalaje	= $this->EmbalajeWarehouse->find('first', array(
            'conditions'	=> array('EmbalajeWarehouse.id' => $id),
			'contain' => array(
				'EmbalajeProductoWarehouse' => array(
					'VentaDetalle',
					'VentaDetalleProducto'
				),
				'Venta' => array(
					'VentaDetalle' => array(
						'VentaDetalleProducto' => array(
							'fields' => array(
								'VentaDetalleProducto.nombre'
							)
						)
					),
					'VentaEstado' => array(
						'VentaEstadoCategoria'
					)
				),
				'Bodega' => array(
					'fields' => array(
						'Bodega.nombre'
					)
				),
				'MetodoEnvio' => array(
					'fields' => array(
						'MetodoEnvio.nombre'
					)
				),
				'Comuna' => array(
					'fields' => array(
						'Comuna.nombre'
					)
				),
				'Marketplace' => array(
					'fields' => array(
						'Marketplace.nombre'
					)
				)
			)
        ));

		if ($embalaje['EmbalajeWarehouse']['estado'] != 'en_revision')
		{
			$this->Session->setFlash('El embalaje no está disponible para revisión.', null, array(), 'warning');
			$this->redirect(array('action' => 'index'));
		}

		$embalado_por = ClassRegistry::init('Administrador')->find('first', array(
			'conditions' => array(
				'id' => $embalaje['EmbalajeWarehouse']['responsable_id_procesando']
			),
			'fields' => array(
				'id',
				'nombre',
				'email'
			)
		));

		$finalizado_por = ClassRegistry::init('Administrador')->find('first', array(
			'conditions' => array(
				'id' => $embalaje['EmbalajeWarehouse']['responsable_id_procesando']
			),
			'fields' => array(
				'id',
				'nombre',
				'email'
			)
		));

		$cancelado_por = ClassRegistry::init('Administrador')->find('first', array(
			'conditions' => array(
				'id' => $embalaje['EmbalajeWarehouse']['responsable_id_procesando']
			),
			'fields' => array(
				'id',
				'nombre',
				'email'
			)
		));


		BreadcrumbComponent::add('Embalajes ', '/embalajeWarehouses');
		BreadcrumbComponent::add('Revisar embalaje');

		$this->set(compact('embalaje', 'embalado_por', 'finalizado_por', 'cancelado_por'));
	}


		
	/**
	 * admin_cancelar
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function admin_cancelar($id)
	{	
		$this->EmbalajeWarehouse->id = $id;

		if ( ! $this->EmbalajeWarehouse->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->EmbalajeWarehouse->cancelar_embalaje($id, $this->Auth->user('id')) )
		{
			$this->Session->setFlash('Embalaje cancelado con éxito.', null, array(), 'success');
		}
		else
		{
			$this->Session->setFlash('No fue posible cancelar el embalaje.', null, array(), 'warning');
		}
	
		$this->redirect(array('action' => 'index'));
	}


	public function admin_prioritario($id, $prioritario = 1)
	{	
		$this->EmbalajeWarehouse->id = $id;

		if ( ! $this->EmbalajeWarehouse->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->embalaje_prioritario($id, $prioritario) )
		{
			$this->Session->setFlash('Embalaje actualizado con éxito.', null, array(), 'success');
		}
		else
		{
			$this->Session->setFlash('No fue posible actualizar el embalaje.', null, array(), 'warning');
		}
	
		$this->redirect($this->referer('/', true));
	}

	
	/**
	 * Actualiza campo prioritario y notifica a firebase
	 *
	 * @param  mixed $id id del embalaje
	 * @param  mixed $prioritario 1|0
	 * @return bool
	 */
	public function embalaje_prioritario($id, $prioritario)
	{	
		$this->EmbalajeWarehouse->id = $id;
		if ( $this->EmbalajeWarehouse->saveField('prioritario', $prioritario) )
		{	
			if ($prioritario)
			{
				$mensaje = sprintf('¡Embalaje #%d cambió a urgente!', $id);
			}
			else
			{
				$mensaje = sprintf('Embalaje #%d cambió a urgencia normal', $id);
			}

			$this->Firebase = $this->Components->load('Firebase');
			$this->Firebase->NotificacionFirebase($mensaje);

			return true;
		}
		else
		{
			return false;
		}
	}


	/**
	 * Genera la etiqueta de envio intenra y retorna la url púbica y absoluta del archivo.
	 * @param  int 		$id Identificador del embalaje
	 * @param  string $orientacion horizontal/vertical
	 * @return [type]        [description]
	 */
	public function obtener_etiqueta_envio_interna_url($id, $venta = array(), $orientacion = 'horizontal')
	{	
		# Componentes
		$this->Etiquetas = $this->Components->load('Etiquetas');
		$this->LAFFPack = $this->Components->load('LAFFPack');

		# Bultos máximo de 2 metros
		$volumenMaximo = (float) 8000000;

		$embalaje = $this->EmbalajeWarehouse->find('first', array(
			'conditions' => array(
				'EmbalajeWarehouse.id' => $id
			),
			'contain' => array(
				'EmbalajeProductoWarehouse' => array(
					'ProductoWarehouse',
					'VentaDetalleProducto'
				)
			)
		));
		
		if (empty($venta))
		{
			# Detalles de la venta
			$venta = ClassRegistry::init('Venta')->find('first', array(
				'conditions' => array(
					'Venta.id' => $embalaje['EmbalajeWarehouse']['venta_id']
				),
				'contain' => array(
					'VentaCliente',
					'MetodoEnvio' => array(
						'fields' => array(
							'MetodoEnvio.nombre'
						)
					),
					'VentaMensaje',
					'Mensaje',
					'Comuna',
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
					)
				)
			));
		}

		# Algoritmo LAFF para ordenamiento de productos
		$paquetes = $this->LAFFPack->obtener_bultos_venta_por_embalaje($embalaje, $volumenMaximo);

		# Almacenarmos las etiquetas
		$etiquetas = array();


		$canal_venta = '';

		if ($venta['Venta']['venta_manual'])
		{
			$canal_venta = 'POS de venta';
		}
		else if ($venta['Venta']['marketplace_id'])
		{
			$canal_venta = $venta['Marketplace']['nombre'];
		}
		else
		{
			$canal_venta = $venta['Tienda']['nombre'];
		}

		$mensajes = array();

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
				'mensaje' => $mensaje['mensaje']
			);
		}

		# Agrupamos para ordenar
		foreach ($mensajes as $im => $mensaje3) 
		{
			$auxFechas[$im] = $mensaje3['fecha'];
		}

		# Ordenamos los mensajes por fecha
		array_multisort($auxFechas, SORT_DESC, $mensajes);

		# formeteamos el mensaje a texto
		$msjTexto = '';
		foreach ($mensajes as $valor) 
		{
			$msjTexto .= $valor['mensaje'];
		}
		
		$archivos = array();

		foreach ($paquetes as $paquete) 
		{
			$etiquetaArr = array(
				'venta' => array(
					'id' => $venta['Venta']['id'],
					'metodo_envio' => $venta['MetodoEnvio']['nombre'],
					'canal' => $canal_venta,
					'externo' => $venta['Venta']['id_externo'],
					'medio_de_pago' => $venta['MedioPago']['nombre'],
					'fecha_venta' => $venta['Venta']['fecha_venta']
				),
				'embalaje' => array(
					'id' => $paquete['paquete']['embalaje_id']
				),
				'transportista' => array(
					'nombre' => ($venta['Transporte']) ? $venta['Transporte'][0]['nombre'] : '',
				),
				'destinatario' => array(
					'nombre' => $venta['VentaCliente']['nombre'] . ' ' . $venta['VentaCliente']['apellido'],
					'rut' => formato_rut($venta['VentaCliente']['rut']),
					'fono' => $venta['VentaCliente']['telefono'],
					'email' => $venta['VentaCliente']['email'],
					'direccion' => $venta['Venta']['direccion_entrega'] . ' ' . $venta['Venta']['numero_entrega']  . ', ' . $venta['Venta']['otro_entrega'],
					'comuna' => $venta['Comuna']['nombre']
				),
				'bulto' => array(
					'referencia' => $paquete['paquete']['embalaje_id'],
					'peso' => $paquete['paquete']['weight'],
					'ancho' => (int) $paquete['paquete']['width'],
					'alto' => (int) $paquete['paquete']['height'],
					'largo' => (int) $paquete['paquete']['length'],
					'n_items' => count($paquete['items'])
				),
				'mensajes' => array(
					'texto' => $msjTexto
				),
				'pdf' => array(
					'dir' => 'EmbalajeWarehouse/' . $paquete['paquete']['embalaje_id']
				)
			);

			$archivos[] = $this->Etiquetas->generarEtiquetaInterna($etiquetaArr);
		}

		#unimos
		return ($archivos) ? $archivos[0] : array();

	}
}