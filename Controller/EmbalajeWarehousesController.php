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


    public function admin_view($id = null)
	{	
		if ( ! $this->EmbalajeWarehouse->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

        $this->request->data	= $this->EmbalajeWarehouse->find('first', array(
            'conditions'	=> array('EmbalajeWarehouse.id' => $id),
			'contain' => array(
				'EmbalajeProductoWarehouse' => array(
					'VentaDetalle',
					'VentaDetalleProducto'
				),
				'Venta' => array(
					'VentaDetalle'
				),
				'Bodega'
			)
        ));
		prx($this->request->data);
		BreadcrumbComponent::add('Embalajes ', '/embalajes');
		BreadcrumbComponent::add('Ver ');
	}
}