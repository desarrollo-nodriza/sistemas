<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'PhpSpreadsheet', array('file' => 'PhpSpreadsheet/vendor/autoload.php'));

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\IOFactory;

use function PHPSTORM_META\map;

class ZonificacionesController extends AppController
{
	
	// Reubicar
    public function admin_mover_de_ubicacion($id )
	{	
		if ( ! ClassRegistry::init('VentaDetalleProducto')->find('first', array('conditions' => array('id' => $id))) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index' ,'controller' => 'ventaDetalleProductos'));
		}
		
		$ubicacion = ClassRegistry::init('Ubicacion')->find('all', array(
            'conditions' => array('Ubicacion.activo' => 1),
            'fields' => array('id', 'fila','columna','Zona.nombre', 'Zona.bodega_id'),
			'contain' => ['Zona' => ['Bodega']],
            'order' => array('Zona.nombre ASC'),
        ));
		$zonificaciones = $this->Zonificacion->find('all', array(
            'fields' => array('Zonificacion.*','SUM(Zonificacion.cantidad) as cantidad','Ubicacion.*'),
			'conditions' => array(
				'producto_id' 	=> $id,
				'movimiento !='	=> 'garantia'
				
            ),
            'contain' => array('Ubicacion' => ['Zona' => ['Bodega']]),
            'group' => array('ubicacion_id'),
		));
		
		$this->TieneZonificacion($zonificaciones,$id);
		
        $ubicaciones= [];
        $persistir= [];
       
        foreach ($ubicacion as $value) {
            $ubicaciones[$value['Ubicacion']['id']] =  $value['Zona']['Bodega']['nombre'] . ' ' . $value['Zona']['nombre'].' - '.$value['Ubicacion']['columna'].' - '.$value['Ubicacion']['fila'];
        }
        
		if ( $this->request->is('post') || $this->request->is('put')) {

            $date = date("Y-m-d H:i:s");
			foreach ($this->request->data['Zonificacion'] as $key => $valor) {

                if (trim($valor['cantidad']) !='' && trim($valor['ubicacion_id']) !='') {
                    $persistir []=
                    [
                        "ubicacion_id"          => $valor['ubicacion_id'],
                        "producto_id"           => $id,
                        "cantidad"              => $valor['cantidad'],
                        "responsable_id"        => $this->Auth->user('id'),
                        "antigua_ubicacion_id"  => $valor['id'],
                        "movimiento"            => 'ubicacion',
                        "fecha_creacion"        => $date,
                        "ultima_modifacion"     => $date
                    ]; 

                    $persistir [] =
                    [
                        "ubicacion_id"          => $valor['id'],
                        "producto_id"           => $id,
                        "cantidad"              => ($valor['cantidad']* -1),
                        "responsable_id"        => $this->Auth->user('id'),
                        "nueva_ubicacion_id"    => $valor['ubicacion_id'],
                        "movimiento"            => 'ubicacion',
                        "fecha_creacion"        => $date,
                        "ultima_modifacion"     => $date
                    ]; 
                }

			}
			
			if (!$persistir)
            {
            	$this->Session->setFlash('Asegurate de haber seleccionado una nueva ubicación e indicado cantidad a mover', null, array(), 'danger');
				
			}else{

				$this->Zonificacion->create();
         
				if ( $this->Zonificacion->saveMany($persistir) )
				{
					$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
					$this->redirect(array('action' => 'edit', $id ,'controller' => 'ventaDetalleProductos'));
				}
				else
				{
					$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
				}
			}
            
		}

	
		BreadcrumbComponent::add('Editar Producto', '/ventaDetalleProductos/edit/'.$id);
		BreadcrumbComponent::add('Movimientos de Ubicación');
		
		$this->set(compact('zonificaciones','ubicaciones'));

	}

    public function admin_exportar_stock_ubicacion($id )
	{		
		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
		set_time_limit(-1);
		ini_set('memory_limit', -1);

		$datos = [];

        $zonificaciones = $this->Zonificacion->find('all', array(
            'fields' => array('*','SUM(Zonificacion.cantidad) as cantidad'),
			'conditions' => array(
				'producto_id' 	=> $id,
				'movimiento !='	=> 'garantia'
            ),
            'contain' => array('Ubicacion'=>[
				'Zona' => [
					'Bodgea'
				]
			],'VentaDetalleProducto'),
            'group' => array('ubicacion_id'),
		));
       
		$this->TieneZonificacion($zonificaciones,$id);

		foreach ($zonificaciones as $valor)
		{	
			if ($valor[0]['cantidad']>0) {
				$datos[] = 
				array(
					'producto_id'       => $id,
					'nombre'			=> $valor['VentaDetalleProducto']['nombre'],
					'ubicacion_origen'  => $valor['Zonificacion']['ubicacion_id'],
					'nombre_ubicacion'	=> $valor['Ubicacion']['Zona']['Bodega']['nombre'] . ' ' . $valor['Ubicacion']['Zona']['nombre'].' - '.$valor['Ubicacion']['columna'].' - '.$valor['Ubicacion']['fila'],
					'cantidad'          => $valor[0]['cantidad'],
					'ubicacion_destino' => '',
					'cantidad_a_mover'  => ''
				);
			}
			
		}	
        $campos = array('producto_id','nombre','ubicacion_origen','nombre_ubicacion','cantidad','ubicacion_destino', 'cantidad_a_mover');

	
		
		$this->set(compact('datos', 'campos','id'));

	}

    public function admin_reubicacion_masiva($id )
	{	
		$tipoPermitido = array(
			'xlsx',
			'xls',
			'csv'
		);

		$datos = array();

		if ( $this->request->is('post') || $this->request->is('put')) {
            

			ini_set('max_execution_time', 0);
			ini_set('post_max_size', '1G');
			ini_set('memory_limit', -1);
			ini_set('max_input_vars', 1000000);
			
			if ($this->request->data['Zonificacion']['archivo']['error'] == 0 ) {

                
				# Reconocer cabecera e idenitficador
				if ($this->request->data['Zonificacion']['archivo']['error'] != 0) {
					$this->Session->setFlash('El archivo contiene errores o está dañado.', null, array(), 'danger');
					$this->redirect(array('action' => 'edit', $id ,'controller' => 'ventaDetalleProductos'));
				}

				$ext = pathinfo($this->request->data['Zonificacion']['archivo']['name'], PATHINFO_EXTENSION);
              
				if (!in_array($ext, $tipoPermitido)) {
                    
					$this->Session->setFlash('El formato '.$ext.' no es válido. Los formatos permitidos son: ' . implode($tipoPermitido,[ ',']), null, array(), 'danger');
					$this->redirect(array('action' => 'edit', $id ,'controller' => 'ventaDetalleProductos'));
				}

				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->data['Zonificacion']['archivo']['tmp_name']);
                
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                
				
				if (isset($sheetData[1])) {
                    
					foreach ($sheetData[1] as $k => $cabecera) {
						$datos['options'][$k] = $cabecera;
					}						
				}
				
				$datos['data'] = $sheetData;

				if (!$datos['data']) {

					$this->Session->setFlash($this->crearAlertaUl([], 'No hay datos a porcesar en excel'), null, array(), 'danger');
					$this->redirect(array('action' => 'reubicacion_masiva', $id));
					
				}
				$date = date("Y-m-d H:i:s");
				$persistir= [];
				$cantidad_es_mayor =[];
				$existe_ubicacion =[];
				foreach ($datos['data'] as $key => $value) {
					$existe = true;
					if ($key != 1 ) {
						
						foreach ($value as $data) {
							
							if (!$data) {
								$existe = false;
							}
						}

						if ($existe) {

							$ubicacion = ClassRegistry::init('Ubicacion')->find('first', array(
								'conditions' => array(
									'activo' 	=> 1,
									'id'		=> $value['F']
								),
								'fields' => array('id', 'fila','columna'),
								'order' => array('fila ASC'),
							));

							if (!$ubicacion) {
								$existe_ubicacion[]= "¡La ubicación con id ".$value['F']." no existe!";
							}else{

								$zonificaciones = $this->Zonificacion->find('all', array(
									'fields' => array('Zonificacion.*','SUM(Zonificacion.cantidad) as cantidad','Ubicacion.*'),
									'conditions' => array(
										'producto_id' 	=> $id,
										'ubicacion_id' 	=> $value['C'],
										'movimiento !='	=> 'garantia'
									),
									'contain' => array('Ubicacion'),
									'group' => array('ubicacion_id'),
								));

								$zonificacion=  $zonificaciones[0];
								if ($zonificacion[0]['cantidad'] < $value['G'] ) {
									$cantidad_es_mayor[]= "'Cantidad a Mover' es mayor a 'Cantidad' existente en sistema en la fila ".$key." del excel";
								}
							}
					
						}
					}
				}

				if ($existe_ubicacion ) {

					$this->Session->setFlash($this->crearAlertaUl($existe_ubicacion, 'Errores encontrados'), null, array(), 'danger');
					$this->redirect(array('action' => 'reubicacion_masiva', $id));
				}
				if ($cantidad_es_mayor ) {

					$this->Session->setFlash($this->crearAlertaUl($cantidad_es_mayor, 'Errores encontrados'), null, array(), 'danger');
					$this->redirect(array('action' => 'reubicacion_masiva', $id));
				}
				

				foreach ($datos['data'] as $key => $value) {

					$existe = true;

					if ($key != 1 ) {
						
						foreach ($value as $data) {
							
							if (!$data) {
								$existe = false;
							}
						}

						if ($existe) {
							$persistir []=
							[
								"ubicacion_id"          => $value['F'],
								"producto_id"           => $id,
								"cantidad"              => $value['G'],
								"responsable_id"        => $this->Auth->user('id'),
								"antigua_ubicacion_id"  => $value['C'],
								"movimiento"            => 'ubicacion',
								"fecha_creacion"        => $date,
								"ultima_modifacion"     => $date
							]; 

							$persistir [] =
							[
								"ubicacion_id"          => $value['C'],
								"producto_id"           => $id,
								"cantidad"              => ($value['G']* -1),
								"responsable_id"        => $this->Auth->user('id'),
								"nueva_ubicacion_id"    => $value['F'],
								"movimiento"            => 'ubicacion',
								"fecha_creacion"        => $date,
								"ultima_modifacion"     => $date
							];

						}
					}
				}
				if (!$persistir) {
					$this->Session->setFlash('Asegurate de haber indicado cantidad a ajustar', null, array(), 'danger');
					$this->redirect(array('action' => 'reubicacion_masiva', $id));
				}
				
				$this->Zonificacion->create();
         
				if ( $this->Zonificacion->saveMany($persistir) )
				{
					$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
					$this->redirect(array('action' => 'edit', $id ,'controller' => 'ventaDetalleProductos'));
				}
				else
				{
					$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
				}
			}

		}
        
		BreadcrumbComponent::add('Editar Producto', '/ventaDetalleProductos/edit/'.$id);
		BreadcrumbComponent::add('Reubicar stock');

		$this->set(compact('id'));

	}

	public function admin_reubicacion_masivamente()
	{	

		$tipoPermitido = array(
			'xlsx',
			'xls',
			'csv'
		);

		$datos = array();

		if ( $this->request->is('post') || $this->request->is('put')) {
            

			ini_set('max_execution_time', 0);
			ini_set('post_max_size', '1G');
			ini_set('memory_limit', -1);
			ini_set('max_input_vars', 1000000);
			
			if ($this->request->data['Zonificacion']['archivo']['error'] == 0 ) {

                
				# Reconocer cabecera e idenitficador
				if ($this->request->data['Zonificacion']['archivo']['error'] != 0) {
					$this->Session->setFlash('El archivo contiene errores o está dañado.', null, array(), 'danger');
					$this->redirect(array('action' => 'index' ,'controller' => 'ventaDetalleProductos'));
				}

              

				$ext = pathinfo($this->request->data['Zonificacion']['archivo']['name'], PATHINFO_EXTENSION);
              
				if (!in_array($ext, $tipoPermitido)) {
                    
					$this->Session->setFlash('El formato '.$ext.' no es válido. Los formatos permitidos son: ' . implode($tipoPermitido, [',']), null, array(), 'danger');
					$this->redirect(array('action' => 'index' ,'controller' => 'ventaDetalleProductos'));
				}

               

				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->data['Zonificacion']['archivo']['tmp_name']);
                
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                
				
				if (isset($sheetData[1])) {
                    
					foreach ($sheetData[1] as $k => $cabecera) {
						$datos['options'][$k] = $cabecera;
					}						
				}
				
				$datos['data'] = $sheetData;

				if (!$datos['data']) {

					$this->Session->setFlash($this->crearAlertaUl([], 'No hay datos a procesar en excel'), null, array(), 'danger');
					$this->redirect(array('action' => 'reubicacion_masivamente'));
					
				}
				$date = date("Y-m-d H:i:s");
				$persistir= [];
				$cantidad_es_mayor =[];
				$existe_ubicacion =[];
				foreach ($datos['data'] as $key => $value) {
					$existe = true;
					if ($key != 1 ) {
						
						foreach ($value as $data) {
							
							if (!$data) {
								$existe = false;
							}
						}

						if ($existe) {

							$ubicacion = ClassRegistry::init('Ubicacion')->find('first', array(
								'conditions' => array(
									'activo' 	=> 1,
									'id'		=> $value['F']
								),
								'fields' => array('id', 'fila','columna'),
								'order' => array('fila ASC'),
							));

							if (!$ubicacion) {
								$existe_ubicacion[]= "¡La ubicación con id ".$value['F']." no existe!";
							}else{

							 $zonificaciones = $this->Zonificacion->find('all', array(
								'fields' => array('Zonificacion.*','SUM(Zonificacion.cantidad) as cantidad','Ubicacion.*'),
								'conditions' => array(
									'producto_id' 	=> $value['A'],
									'ubicacion_id' 	=> $value['C'],
									'movimiento !='	=> 'garantia'
								),
								'contain' => array('Ubicacion'),
								'group' => array('ubicacion_id'),
							));

							$zonificacion=  $zonificaciones[0];
							if ($zonificacion[0]['cantidad'] < $value['G'] ) {
								$cantidad_es_mayor[]= "'Cantidad a Mover' es mayor a 'Cantidad' existente en sistema en la fila ".$key." del excel";
							}
						}

							
							

						}
					}

					
				}

				if ($existe_ubicacion ) {

					$this->Session->setFlash($this->crearAlertaUl($existe_ubicacion, 'Errores encontrados'), null, array(), 'danger');
					$this->redirect(array('action' => 'reubicacion_masivamente'));
				}
				if ($cantidad_es_mayor ) {

					$this->Session->setFlash($this->crearAlertaUl($cantidad_es_mayor, 'Errores encontrados'), null, array(), 'danger');
					$this->redirect(array('action' => 'reubicacion_masivamente'));
				}
				

				foreach ($datos['data'] as $key => $value) {

					$existe = true;

					if ($key != 1 ) {
						
						foreach ($value as $data) {
							
							if (!$data) {
								$existe = false;
							}
						}

						if ($existe) {
							$persistir []=
							[
								"ubicacion_id"          => $value['F'],
								"producto_id"           => $value['A'],
								"cantidad"              => $value['G'],
								"responsable_id"        => $this->Auth->user('id'),
								"antigua_ubicacion_id"  => $value['C'],
								"movimiento"            => 'ubicacion',
								"fecha_creacion"        => $date,
								"ultima_modifacion"     => $date
							]; 

							$persistir [] =
							[
								"ubicacion_id"          => $value['C'],
								"producto_id"           => $value['A'],
								"cantidad"              => ($value['G']* -1),
								"responsable_id"        => $this->Auth->user('id'),
								"nueva_ubicacion_id"    => $value['F'],
								"movimiento"            => 'ubicacion',
								"fecha_creacion"        => $date,
								"ultima_modifacion"     => $date
							];

						}
					}
				}
				
				$this->Zonificacion->create();
         
				if ( $this->Zonificacion->saveMany($persistir) )
				{
					$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
					$opciones = array(
						'action' 		=> 	'index' ,
						'controller' 	=> 	'ventaDetalleProductos',
						'id'			=>	$this->request->params['named']['id']??null,
						'nombre'		=>	$this->request->params['named']['nombre']??null,
						'marca'			=>	$this->request->params['named']['marca']??null,
						'proveedor'		=>	$this->request->params['named']['proveedor']??null,
						'existencia'	=>	$this->request->params['named']['existencia']??null,
					);
					$opciones=array_filter($opciones);
					$this->redirect($opciones);
					
				}
				else
				{
					$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
				}
			}

		}
        
		
		BreadcrumbComponent::add('Productos', '/ventaDetalleProductos/index');
		BreadcrumbComponent::add('Reubicar stock');

		$this->set([]);

	}

	public function admin_exportar_stock_productos_ubicacion()
	{		
		$opciones = array(); 
		
		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
		set_time_limit(-1);
		ini_set('memory_limit', -1);
		
		$datos 		= [];

		$zonificaciones = $this->Zonificacion->find('all', array(
			'fields' => array('SUM(Zonificacion.cantidad) as cantidad', 'Zonificacion.producto_id', 'Zonificacion.ubicacion_id'),
			'conditions' => array(
				'Zonificacion.movimiento !='	=> 'garantia'
			),
			'contain' => array(
				'Ubicacion' =>array(
					'Zona' => array(
						'Bodega' => array(
							'fields' => array(
								'Bodega.id',
								'Bodega.nombre'
							)
						),
						'fields' => array(
							'Zona.id',
							'Zona.bodega_id',
							'Zona.nombre'
						)
					),
					'fields' => array(
						'Ubicacion.id',
						'Ubicacion.fila',
						'Ubicacion.columna',
						'Ubicacion.zona_id'
					) 
				),
				'VentaDetalleProducto' => array(
					'fields' => array(
						'VentaDetalleProducto.id',
						'VentaDetalleProducto.nombre'
					)
				)
			),
			'joins' => array(
				array(
					'table' => 'zonas',
					'alias' => 'z',
					'type' => 'INNER',
					'conditions' => array(
						'Ubicacion.Zona_id = z.id',
						'z.bodega_id IN' => Hash::extract($this->Auth->user('Bodega'), '{n}.id')
					)
				),
			),
			'having' => array('SUM(Zonificacion.cantidad) > 0'),
			'group' => array('Zonificacion.ubicacion_id', 'Zonificacion.producto_id'),
			'order' => 'Zonificacion.producto_id'
		));
		
		foreach ($zonificaciones as $valor)
		{	
			$datos[] = array(
				'producto_id'       => $valor['VentaDetalleProducto']['id'],
				'nombre'			=> $valor['VentaDetalleProducto']['nombre'],
				'ubicacion_origen'  => $valor['Ubicacion']['id'],
				'nombre_ubicacion'	=> $valor['Ubicacion']['Zona']['Bodega']['nombre'].' ' . $valor['Ubicacion']['Zona']['nombre'].' - '.$valor['Ubicacion']['columna'].' - '.$valor['Ubicacion']['fila'],
				'cantidad'          => $valor[0]['cantidad'],
				'ubicacion_destino' => '',
				'cantidad_a_mover'  => ''
			);
		}	
		
		$this->TieneZonificacion($zonificaciones);
		
        $campos = array('producto_id','nombre','ubicacion_origen','nombre_ubicacion','cantidad','ubicacion_destino', 'cantidad_a_mover');
		$this->set(compact('datos', 'campos'));

	}

	// Ajustar
	public function admin_ajustar_stock($id)
	{
		if ( ! ClassRegistry::init('VentaDetalleProducto')->find('first', array('conditions' => array('id' => $id))) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index' ,'controller' => 'ventaDetalleProductos'));
			
		}
		$ubicacion = ClassRegistry::init('Ubicacion')->find('all', array(
            'conditions' => array('Ubicacion.activo' => 1),
            'fields' => array('id', 'fila','columna','Zona.nombre', 'Zona.bodega_id'),
			'contain' => ['Zona' => ['Bodega' => [
				'fields' => [
					'Bodega.id',
					'Bodega.nombre',
				]
			]]],
			'joins' => array(
				array(
					'table' => 'zonas',
					'alias' => 'z',
					'type' => 'INNER',
					'conditions' => array(
						'Ubicacion.Zona_id = z.id',
						'z.bodega_id IN' => Hash::extract($this->Auth->user('Bodega'), '{n}.id')
					)
				),
			),
            'order' => array('Zona.nombre ASC'),
        ));

		$zonificaciones = $this->Zonificacion->find('all', array(
            'fields' => array('*','SUM(Zonificacion.cantidad) as cantidad'),
			'conditions' => array(
				'producto_id' 	=> $id,
				'movimiento !='	=> 'garantia'
				
            ),
            'contain' => array(
				'Ubicacion' => [
					'Zona' => [
						'Bodega'
					]
				]),
			'joins' => array(
				array(
					'table' => 'zonas',
					'alias' => 'z',
					'type' => 'INNER',
					'conditions' => array(
						'Ubicacion.Zona_id = z.id',
						'z.bodega_id IN' => Hash::extract($this->Auth->user('Bodega'), '{n}.id')
					)
				),
			),
            'group' => array('ubicacion_id'),
		));
		
		foreach ($ubicacion as $value) {
            $ubicaciones[$value['Ubicacion']['id']] =  $value['Zona']['Bodega']['nombre'] . ' ' . $value['Zona']['nombre'].' - '.$value['Ubicacion']['columna'].' - '.$value['Ubicacion']['fila'];
        }
		
		if ( $this->request->is('post') || $this->request->is('put')) {
			
            $this->PersistirABaseDato($this->request->data['Zonificacion'],$id);
		
		}
		
		$bodegas = ClassRegistry::init('Bodega')->find('list'); 
		foreach ($bodegas as $key => $value) {
			$PMP[$key]= ClassRegistry::init('Pmp')->obtener_pmp($id, $key);
		}
		
		$movimientos_sinfiltrar = ClassRegistry::init('TipoMovimiento')->find('all',[
			'fields' =>['TipoMovimiento.glosa_tipo_movimiento'],
			'conditions' => array(
				'TipoMovimiento.tipo_movimiento' => 'AJ'
            ),

		]);
	
		$movimientos_filtrado =Hash::extract($movimientos_sinfiltrar, '{*}.{*}.glosa_tipo_movimiento');
		
		foreach ($movimientos_filtrado as $key => $value) {
			$movimientos[$value ]=$value;
		}
		
		BreadcrumbComponent::add('Editar Producto', '/ventaDetalleProductos/edit/'.$id);
		BreadcrumbComponent::add('Ajuste de Inventario');
		
		$this->set(compact('zonificaciones','id','ubicaciones','PMP','movimientos'));
	}

	public function admin_ajustar_stock_masiva( $id )
	{	
		$tipoPermitido = array(
			'xlsx',
			'xls',
			'csv'
		);

		$datos = array();

		if ( $this->request->is('post') || $this->request->is('put')) {
            

			ini_set('max_execution_time', 0);
			ini_set('post_max_size', '1G');
			ini_set('memory_limit', -1);
			ini_set('max_input_vars', 1000000);
			
			if ($this->request->data['Zonificacion']['archivo']['error'] == 0 ) {

                
				# Reconocer cabecera e idenitficador
				if ($this->request->data['Zonificacion']['archivo']['error'] != 0) {
					$this->Session->setFlash('El archivo contiene errores o está dañado.', null, array(), 'danger');
					$this->redirect(array('action' => 'edit', $id ,'controller' => 'ventaDetalleProductos'));
				}

              

				$ext = pathinfo($this->request->data['Zonificacion']['archivo']['name'], PATHINFO_EXTENSION);
              
				if (!in_array($ext, $tipoPermitido)) {
                    
					$this->Session->setFlash('El formato '.$ext.' no es válido. Los formatos permitidos son: ' . implode($tipoPermitido, [',']), null, array(), 'danger');
					$this->redirect(array('action' => 'edit', $id ,'controller' => 'ventaDetalleProductos'));
				}

               

				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->data['Zonificacion']['archivo']['tmp_name']);
                
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                
				
				if (isset($sheetData[1])) {
                    
					foreach ($sheetData[1] as $k => $cabecera) {
						$datos['options'][$k] = $cabecera;
					}						
				}
				
				$datos['data'] = $sheetData;

				if (!$datos['data']) {

					$this->Session->setFlash($this->crearAlertaUl([], 'No hay datos a porcesar en excel'), null, array(), 'danger');
					$this->redirect(array('action' => 'ajustar_stock_masiva', $id));
					
				}

				$date = date("Y-m-d H:i:s");
				$valores_excel		= [];
				$existe_ubicacion 	= [];	
				
				foreach ($datos['data'] as $key => $value) {
					
					if ($key == 1 ) {
						continue;
					}

					if (is_null($value['A']) || is_null($value['D']) || is_null($value['G'])) {
						if ($value['G']!==0 ) {
							continue;
						}
					}

					$ubicacion = ClassRegistry::init('Ubicacion')->find('first', array(
						'conditions' => array(
							'activo' 	=> 1,
							'id'		=> $value['D']
						),
						'fields' => array('id', 'fila','columna'),
						'order' => array('fila ASC'),
					));

					if (!$ubicacion) {
						$existe_ubicacion[]= "¡La ubicación con id ".$value['D']." no existe!";
					}else {

						$valores_excel [] = 
						[
							'id'		=> $value['D'],
							'cantidad'	=> $value['G'],
							'glosa'		=> $value['I'],
							'costo'		=> $value['H']
						];
					}
					
				}

				if (!$valores_excel ) {
					$this->Session->setFlash('Asegurate de haber indicado cantidad a ajustar', null, array(), 'danger');
					$this->redirect(array('action' => 'ajustar_stock_masiva', $id));
				}

				if ($existe_ubicacion ) {

					$this->Session->setFlash($this->crearAlertaUl($existe_ubicacion, 'Errores encontrados'), null, array(), 'danger');
					$this->redirect(array('action' => 'ajustar_stock_masiva', $id));
				}

				$this->PersistirABaseDato($valores_excel,$id);
			
			}

		}
        
		BreadcrumbComponent::add('Editar Producto', '/ventaDetalleProductos/edit/'.$id);
		BreadcrumbComponent::add('Ajustar stock');

		$this->set(compact('id'));  

	}

	public function admin_exportar_stock_ajuste( $id )
	{		
		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
		set_time_limit(-1);
		ini_set('memory_limit', -1);

		$datos = [];

        $zonificaciones = $this->Zonificacion->find('all', array(
            'fields' => array('*','SUM(Zonificacion.cantidad) as cantidad'),
			'conditions' => array(
				'producto_id' 	=> $id,
				'movimiento !='	=> 'garantia'
            ),
            'contain' =>[
				'Ubicacion'=>[
					'Zona' => [
						'Bodega'
					]
				],
				'VentaDetalleProducto'],
            'group' => array('ubicacion_id'),
		));

		$bodegas = ClassRegistry::init('Bodega')->find('list'); 
		foreach ($bodegas as $key => $value) {
			$PMP[$key]= ClassRegistry::init('Pmp')->obtener_pmp($id, $key);
		}

		$movimientos_sinfiltrar = ClassRegistry::init('TipoMovimiento')->find('all',[
			'fields' =>['TipoMovimiento.glosa_tipo_movimiento'],
			'conditions' => array(
				'TipoMovimiento.tipo_movimiento' => 'AJ'
            ),

		]);
		$movimientos			= '';
		$movimientos_filtrado 	=	Hash::extract($movimientos_sinfiltrar, '{*}.{*}.glosa_tipo_movimiento');
		foreach ($movimientos_filtrado as $key => $value) {
			$movimientos	=	$movimientos .' < '.$value.' > ';
		}

		foreach ($zonificaciones as $valor)
		{	
			if ($valor[0]['cantidad']>0) {
				$datos[] = 
				array(
					'producto_id'       			=> $id,
					'referencia'       				=> $valor['VentaDetalleProducto']['codigo_proveedor'],
					'nompre_del_producto'			=> $valor['VentaDetalleProducto']['nombre'],
					'ubicacion_id'  				=> $valor['Zonificacion']['ubicacion_id'],
					'nombre_ubicacion'				=> $valor['Ubicacion']['Zona']['Bodega']['nombre'] . ' ' . $valor['Ubicacion']['Zona']['nombre'].' - '.$valor['Ubicacion']['columna'].' - '.$valor['Ubicacion']['fila'],
					'cantidad_actual'				=> $valor[0]['cantidad'],
					'indique_cantidad_a_ajustar'  	=> '',
					'precio'						=> $PMP[$valor['Ubicacion']['Zona']['bodega_id']]==0?'':$PMP[$valor['Ubicacion']['Zona']['bodega_id']],
					'glosa'							=> '',
					'glosas_definidas'				=> $movimientos
				);
				$movimientos='';
			}
			
		}
		if (!$zonificaciones) {
			$producto = ClassRegistry::init('VentaDetalleProducto')->find('first',[
				'conditions' => array(
					'VentaDetalleProducto.id' => $id,
				),
			]); 
			$datos[] = 
				array(
					'producto_id'       			=> $id,
					'referencia'       				=> $producto['VentaDetalleProducto']['codigo_proveedor'],
					'nompre_del_producto'			=> $producto['VentaDetalleProducto']['nombre'],
					'ubicacion_id'  				=> '',
					'nombre_ubicacion'				=> '',
					'cantidad_actual'				=> '',
					'indique_cantidad_a_ajustar'  	=> '',
					'precio'						=> '',
					'glosa'							=> '',
					'glosas_definidas'				=> $movimientos
				);
		}

		$campos = array('producto_id','referencia','nompre_del_producto','ubicacion_id','nombre_ubicacion','cantidad_actual', 'indique_cantidad_a_ajustar','precio','glosa','glosas_que_puedes_usar_pero_no_son_obligatorias');
	
		
		$this->set(compact('datos', 'campos','id'));

	}

	public function admin_ajustar_masivamente()
	{	

		$tipoPermitido = array(
			'xlsx',
			'xls',
			'csv'
		);

		$datos = array();

		if ( $this->request->is('post') || $this->request->is('put')) {
            

			ini_set('max_execution_time', 0);
			ini_set('post_max_size', '1G');
			ini_set('memory_limit', -1);
			ini_set('max_input_vars', 1000000);
			
			if ($this->request->data['Zonificacion']['archivo']['error'] == 0 ) {

                
				# Reconocer cabecera e idenitficador
				if ($this->request->data['Zonificacion']['archivo']['error'] != 0) {
					$this->Session->setFlash('El archivo contiene errores o está dañado.', null, array(), 'danger');
					$this->redirect(array('action' => 'index' ,'controller' => 'ventaDetalleProductos'));
				}

              

				$ext = pathinfo($this->request->data['Zonificacion']['archivo']['name'], PATHINFO_EXTENSION);
              
				if (!in_array($ext, $tipoPermitido)) {
                    
					$this->Session->setFlash('El formato '.$ext.' no es válido. Los formatos permitidos son: ' . implode($tipoPermitido, [',']), null, array(), 'danger');
					$this->redirect(array('action' => 'index' ,'controller' => 'ventaDetalleProductos'));
				}

               

				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->data['Zonificacion']['archivo']['tmp_name']);
                
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
                
				
				if (isset($sheetData[1])) {
                    
					foreach ($sheetData[1] as $k => $cabecera) {
						$datos['options'][$k] = $cabecera;
					}						
				}
				
				$datos['data'] = $sheetData;

				if (!$datos['data']) {

					$this->Session->setFlash($this->crearAlertaUl([], 'No hay datos a porcesar en excel'), null, array(), 'danger');
					$this->redirect(array('action' => 'ajustar_masivamente'));
					
				}
				$date = date("Y-m-d H:i:s");
				$persistir			= [];
				$persistir2			= [];
				$existe_ubicacion 	= [];
				$valores_excel		= [];
				
				foreach ($datos['data'] as $key => $value) {

					if ($key == 1 ) {
						continue;
					}
					
					if (is_null($value['D']) ) {
							continue;
					}

					if (is_null($value['G'])) {
						if ($value['G']!= 0 ) {
							continue;
						}
							
					}
					
					$ubicacion = ClassRegistry::init('Ubicacion')->find('first', array(
						'conditions' => array(
							'activo' 	=> 1,
							'id'		=> $value['D']
						),
						'fields' => array('id', 'fila','columna'),
						'order' => array('fila ASC'),
					));

					if (!$ubicacion) {
						$existe_ubicacion[]= "¡La ubicación con id ".$value['D']." no existe!";
					}else {

						$valores_excel [] = 
						[
							'id'			=> $value['D'],
							'cantidad'		=> $value['G'],
							'producto_id'	=> $value['A'],
							'glosa'			=> $value['I'],
							'costo'			=> $value['H']
						];
					}

				}
				
				if ($existe_ubicacion ) {

					$this->Session->setFlash($this->crearAlertaUl($existe_ubicacion, 'Errores encontrados'), null, array(), 'danger');
					$this->redirect(array('action' => 'ajustar_masivamente'));
				}
				
				$date = date("Y-m-d H:i:s");
				
				foreach ($valores_excel as $valor) {
				
					$PrepararInfoPersistir = $this->PrepararInfoPersistir($valor,$valor['producto_id'],$date);
					$persistir	[] = $PrepararInfoPersistir['persistir'];
					$persistir2	[] = $PrepararInfoPersistir['persistir2'];
				}
			
				$persistir	= array_filter($persistir);
				$persistir2	= array_filter($persistir2);
				$persistir2 = array_unique($persistir2 , SORT_REGULAR );
				
				if (!$persistir)
				{
					$this->Session->setFlash('1) Asegurate de haber indicado cantidad a ajustar 2) Asegurate que cantidad sea distinta a la ya existente 3) Asegurate de haber indicado una glosa 4) Asegurate de haber indicado una ubicación id', null, array(), 'danger');
					
					
				}else{

					$infoPersistirInventario=[];
					if ( $this->Zonificacion->saveMany($persistir) )
					{
						foreach ($persistir2 as $value) {
							$infoPersistirInventario[] = $this->PrepararInfoPersistirInventario($value);
						}

						if ($infoPersistirInventario) {
							
							$resultado = ClassRegistry::init('Bodega')->ajustarInventarioMasivo($infoPersistirInventario);
							$resultado_json = json_encode($resultado,true);
							$this->Session->setFlash('Zonificacion se ajusto correctamente. Bodega : '.$resultado_json, null, array(), 'danger');
							$opciones = array(
								'action' 		=> 	'index' ,
								'controller' 	=> 	'ventaDetalleProductos',
								'id'			=>	$this->request->params['named']['id']??null,
								'nombre'		=>	$this->request->params['named']['nombre']??null,
								'marca'			=>	$this->request->params['named']['marca']??null,
								'proveedor'		=>	$this->request->params['named']['proveedor']??null,
								'existencia'	=>	$this->request->params['named']['existencia']??null,
							);
							$opciones=array_filter($opciones);
							$this->redirect($opciones);
		
							
						}
						
					
					}
					
					$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
					
				}
			}

		}
        
		
		BreadcrumbComponent::add('Productos', '/ventaDetalleProductos/index');
		BreadcrumbComponent::add('Ajustar stock');

		$this->set([]);

	}

	public function admin_exportar_stock_productos_ajustar()
	{		
		$opciones = array(); 
		
		# Aumentamos el tiempo máxmimo de ejecución para evitar caídas
		set_time_limit(-1);
		ini_set('memory_limit', -1);
		
		$datos 		= [];

		$id_stock_disponible = [];
		
		foreach ($this->Auth->user('Bodega') as $bodega)
		{
			$id_stock_disponible[$bodega['id']] = ClassRegistry::init('VentaDetalleProducto')->obtener_productos_con_stock_disponible($bodega['id']);
		}

		$opciones = array_replace_recursive($opciones, array(
			'order' => array('VentaDetalleProducto.id_externo' => 'DESC'),
			'contain' => array(
				'Marca' => array(
					'fields' => array(
						'Marca.id', 'Marca.nombre'
					)
				),
			),
			'conditions' => array(
				'VentaDetalleProducto.id' => Hash::extract($id_stock_disponible, '{n}.id')
			),
			'fields' => array(
				'VentaDetalleProducto.id', 
				'VentaDetalleProducto.nombre', 
				'VentaDetalleProducto.codigo_proveedor',
			),
			)
		);
		
		# Filtrar
		if ( isset($this->request->params['named']) ) {
			foreach ($this->request->params['named'] as $campo => $valor) {
				switch ($campo) {
					case 'id':
						$opciones = array_replace_recursive($opciones, array(
							'conditions' => array('VentaDetalleProducto.id_externo' => str_replace('%2F', '/', urldecode($valor) ) )));
						break;
					case 'nombre':
						$opciones = array_replace_recursive($opciones, array(
							'conditions' => array('VentaDetalleProducto.nombre LIKE' => '%'.trim(str_replace('%2F', '/', urldecode($valor) )).'%')));
						break;
					case 'marca':
						$opciones = array_replace_recursive($opciones, array(
							'conditions' => array('VentaDetalleProducto.marca_id' => $valor)));
						break;
					case 'proveedor':
						$opciones = array_replace_recursive($opciones, array(
							'joins'=> array(
								array(
									'alias' => 'Proveedor',
									'table' => 'proveedores_venta_detalle_productos',
									'type' => 'INNER',
									'conditions' => array(
										'Proveedor.venta_detalle_producto_id = VentaDetalleProducto.id',
										'Proveedor.proveedor_id' => $valor
									)
								),
							)));
						break;
					case 'existencia':
					
						if ($valor == 'en_existencia')
						{
							$opciones = array_replace_recursive($opciones, array(
								'conditions' => array(
									'VentaDetalleProducto.id' => Hash::extract($id_stock_disponible, '{n}.{n}.id')
								)
							));
						}
						
						break;
				}
			}
		}
		
		$productos 	= ClassRegistry::init('VentaDetalleProducto')->find('all',$opciones);
		
		$movimientos = $this->Movimientos();
		
		# Filtramos por las bodegas que tiene acceso el usuario
		$bodegas = ClassRegistry::init('Bodega')->find('list', array(
			'conditions' => array(
				'id IN' => Hash::extract($this->Auth->user('Bodega'), '{n}.id'), 
				'activo' => 1
			)
		)); 
		
        foreach ($productos as $producto) {
			$zonificaciones = $this->Zonificacion->find('all', array(
				'fields' => array('Zonificacion.producto_id', 'Zonificacion.ubicacion_id','SUM(Zonificacion.cantidad) as cantidad'),
				'conditions' => array(
					'producto_id' 	=> $producto['VentaDetalleProducto']['id'],
					'movimiento !='	=> 'garantia'
				),
				'contain' =>[
					'Ubicacion'=>[
						'Zona' => [
							'Bodega' => [
								'fields' => [
									'Bodega.id',
									'Bodega.nombre'
								]
							],
							'fields' => [
								'Zona.id',
								'Zona.bodega_id',
								'Zona.nombre'
							]
						],
						'fields' => [
							'Ubicacion.id',
							'Ubicacion.zona_id',
							'Ubicacion.fila',
							'Ubicacion.columna'
						]
					]],
				'joins' => array(
					array(
						'table' => 'zonas',
						'alias' => 'z',
						'type' => 'INNER',
						'conditions' => array(
							'Ubicacion.Zona_id = z.id',
							'z.bodega_id IN' => Hash::extract($this->Auth->user('Bodega'), '{n}.id')
						)
					),
				),
				'group' => array('ubicacion_id'),
			));
			
			foreach ($zonificaciones as $valor)
			{	
				# pmp por bodega de la ubicación
				$PMP = ClassRegistry::init('Pmp')->obtener_pmp($producto['VentaDetalleProducto']['id'], $valor['Ubicacion']['Zona']['bodega_id']);
				
				if ($valor[0]['cantidad']>0) {
					$datos[] = 
					array(
						'producto_id'       			=> $producto['VentaDetalleProducto']['id'],
						'referencia'       				=> $producto['VentaDetalleProducto']['codigo_proveedor'],
						'nompre_del_producto'			=> $producto['VentaDetalleProducto']['nombre'],
						'ubicacion_id'  				=> $valor['Zonificacion']['ubicacion_id'],
						'nombre_ubicacion'				=> $valor['Ubicacion']['Zona']['Bodega']['nombre'] . ' ' . $valor['Ubicacion']['Zona']['nombre'].' - '.$valor['Ubicacion']['columna'].' - '.$valor['Ubicacion']['fila'],
						'cantidad_actual'				=> $valor[0]['cantidad'],
						'indique_cantidad_a_ajustar'  	=> '',
						'precio'						=> $PMP,
						'glosa'							=> '',
						'glosas_definidas'				=> $movimientos
					);
				}
								
			}

			if (!$zonificaciones ) {

				# pmp global
				$PMP = ClassRegistry::init('Pmp')->obtener_pmp($producto['VentaDetalleProducto']['id']);
				
				$datos[] = 
					array(
						'producto_id'       			=> $producto['VentaDetalleProducto']['id'],
						'referencia'       				=> $producto['VentaDetalleProducto']['codigo_proveedor'],
						'nompre_del_producto'			=> $producto['VentaDetalleProducto']['nombre'],
						'ubicacion_id'  				=> '',
						'nombre_ubicacion'				=> '',
						'cantidad_actual'				=> '',
						'indique_cantidad_a_ajustar'  	=> '',
						'precio'						=> $PMP,
						'glosa'							=> '',
						'glosas_definidas'				=> $movimientos
					);
			}

		}	
	
		$campos = array('producto_id','referencia','nompre_del_producto','ubicacion_id','nombre_ubicacion','cantidad_actual', 'indique_cantidad_a_ajustar','precio','glosa','glosas_que_puedes_usar_pero_no_son_obligatorias');
		$this->set(compact('datos', 'campos'));

	}


	// funciones utilidad

	private function PrepararInfoPersistir($valor, $id,$date){
		
		
		
		$persistir		= [];
		$persistir2		= [];
		$validar_glosa 	= true;
		$glosa			= $valor['glosa']??'';
		
		if (isset($valor['manual'])) {

			if ($valor['manual']==1) {

				$glosa =trim($valor['glosa_manual']);

				if (trim($valor['glosa_manual']) == '') {
					$validar_glosa = false;
				}

			}else
			{
				$glosa =trim($valor['glosa']);

				if (trim($valor['glosa']) == '') {
					$validar_glosa = false;
				}
			}
		}else
		{
			if (trim($glosa) == '') {
				$validar_glosa = false;
			}
		}

		
		if (trim($valor['cantidad']) !='' && $validar_glosa) {
		
			$zonificacion = $this->Zonificacion->find('all', array(
				'conditions' => array(
					'Zonificacion.producto_id' 		=> $id,
					'Zonificacion.ubicacion_id' 	=> $valor['id'],
					'Zonificacion.movimiento !='	=> 'garantia'
				),
				'contain' => array(
					'Ubicacion' => array(
						'Zona' => array(
							'fields' => array(
								'Zona.id',
								'Zona.bodega_id',
								'Zona.nombre'
							)
						),
						'fields' => array(
							'Ubicacion.id',
							'Ubicacion.zona_id',
							'Ubicacion.fila',
							'Ubicacion.columna',
						)
					) 
				),
				'fields' => array(
					'Zonificacion.id',
					'Zonificacion.ubicacion_id',
					'Zonificacion.cantidad'
				)
			));
			
			
			if (count($zonificacion)>0) {
			
				$cantidad = $valor['cantidad'] - array_sum(Hash::extract($zonificacion, '{n}.Zonificacion.cantidad'));

				if ($cantidad == 0) {

					return [
						'persistir'		=> $persistir,
						'persistir2'	=> $persistir2
					];

				}
				$persistir =
				[
					"ubicacion_id"          => $valor['id'],
					"producto_id"           => $id,
					"cantidad"              => $cantidad,
					"responsable_id"        => $this->Auth->user('id'),
					"movimiento"            => 'ajuste',
					"glosa"            		=> $glosa,
					"fecha_creacion"        => $date,
					"ultima_modifacion"     => $date
				];

				$persistir2 =
				[
					'id_producto'	=> $id,
					'bodega_id'    	=> $zonificacion[0]['Ubicacion']['Zona']['bodega_id'],
					'glosa'        	=> $glosa,
					'pmp'			=> $valor['costo'],
				];
				
			}else{

				$bodega = ClassRegistry::init('Ubicacion')->find('first', 
				array(
					'fields' => array('Zona.bodega_id'),
					'conditions' => array('Ubicacion.id' =>$valor['id']),
					'contain' =>  [ 'Zona' ],
				));
				
				$cantidad = $valor['cantidad'];
				if ($cantidad == 0) {

					return [
						'persistir'		=> $persistir,
						'persistir2'	=> $persistir2
					];

				}
				$persistir =
				[
					"ubicacion_id"          => $valor['id'],
					"producto_id"           => $id,
					"cantidad"              => $cantidad,
					"responsable_id"        => $this->Auth->user('id'),
					"movimiento"            => 'ajuste',
					"glosa"            		=> $glosa,
					"fecha_creacion"        => $date,
					"ultima_modifacion"     => $date
				];

				$persistir2 =
				[
					'id_producto'	=> $id,
					'bodega_id'    	=> $bodega['Zona']['bodega_id'],
					'glosa'        	=> $glosa,
					'pmp'			=> $valor['costo'],
				];

				
			}
		}
	
		return [
			'persistir'		=> $persistir,
			'persistir2'	=> $persistir2
		];
	}

	private function PrepararInfoPersistirInventario($value){

		$persistir2		= [];
		
		$zonificacion = $this->Zonificacion->find('all', array(
			'fields' => array('SUM(Zonificacion.cantidad) as cantidad'),
			'conditions' => array(
				'Zonificacion.producto_id' 		=> $value['id_producto'] ,
				'Zonificacion.movimiento !=' 	=> 'garantia'
				
			),
			'contain' => ['Ubicacion'] ,
			'joins'      => array(
				array(
					'table' => 'zonas',
					'alias' => 'Zona',
					'type' => 'INNER',
					'conditions' => array(
						'Ubicacion.Zona_id = Zona.id',
						'Zona.bodega_id' => $value['bodega_id']
					)
				),
			
		)));
		#sino hay nada zonificado no se ajusta el inventario
		if (is_null( $zonificacion[0][0]['cantidad'])) {
			return $persistir2;
		}
		$pmp = trim($value['pmp']);

		if (trim($value['pmp'])=='') {
			$pmp = ClassRegistry::init('Pmp')->obtener_pmp($value['id_producto'], $value['bodega_id']);
			$pmp = ($pmp == 0)?ClassRegistry::init('VentaDetalleProducto')->obtener_precio_costo($value['id_producto']):$pmp;
		}

		$cantidad = $zonificacion[0][0]['cantidad'];

		$persistir2 =
		[
			'id_producto'	=> $value['id_producto'],
			'bodega_id'     => $value['bodega_id'],			
			'cantidad'      => $cantidad,
			'glosa'			=> $value['glosa'],			
			'precio'        => $pmp			
		];
		

		return $persistir2;
	}

	private function TieneZonificacion($zonificaciones, $id= null)
	{
		if (!$zonificaciones) {
		
			$this->Session->setFlash('El o los productos no se encuentran Zonificados. Antes de realizar alguna acción prefiera Zonificar.', null, array(), 'danger');
			if ( is_null($id)) {

				$this->redirect(array('action' => 'index' ,'controller' => 'ventaDetalleProductos'));
				
			}

			$this->redirect(array('action' => 'edit', $id ,'controller' => 'ventaDetalleProductos'));

			
		}

		return ;
	}
	
	private function PersistirABaseDato($ajuste, $id)
	{

		$date = date("Y-m-d H:i:s");

		foreach ($ajuste as $valor) {
			
			if (!is_null($valor['cantidad'])) {
				$PrepararInfoPersistir = $this->PrepararInfoPersistir($valor,$id,$date);
				$persistir	[] = $PrepararInfoPersistir['persistir'];
				$persistir2	[] = $PrepararInfoPersistir['persistir2'];
			}
			
		}
		$persistir	= array_filter($persistir);
		$persistir2	= array_filter($persistir2);
		$persistir2 = array_unique($persistir2 , SORT_REGULAR );
		
		if (!$persistir)
		{
			$this->Session->setFlash('1) Asegurate de haber indicado cantidad a ajustar 2) Asegurate que cantidad sea distinta a la ya existente 3) Asegurate de haber indicado una glosa', null, array(), 'danger');
			
		}else{

			$this->Zonificacion->create();
			$infoPersistirInventario=[];
			
			if ( $this->Zonificacion->saveMany($persistir) )
			{
				foreach ($persistir2 as $value) {
					$infoPersistirInventario[] = $this->PrepararInfoPersistirInventario($value);
				}

				if ($infoPersistirInventario) {
					
					$resultado = ClassRegistry::init('Bodega')->ajustarInventarioMasivo($infoPersistirInventario);
					$resultado_json = json_encode($resultado,true);
					$this->Session->setFlash('Zonificacion se ajusto correctamente. Bodega : '.$resultado_json, null, array(), 'danger');
					$this->redirect(array('action' => 'edit', $id ,'controller' => 'ventaDetalleProductos'));
				}
				
			}
			
			$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			
		}


	}

	private function Movimientos()
	{
		$movimientos_sinfiltrar = ClassRegistry::init('TipoMovimiento')->find('all',[
			'fields' =>['TipoMovimiento.glosa_tipo_movimiento'],
			'conditions' => array(
				'TipoMovimiento.tipo_movimiento' => 'AJ'
			),

		]);
		$movimientos			= '';
		$movimientos_filtrado 	=	Hash::extract($movimientos_sinfiltrar, '{*}.{*}.glosa_tipo_movimiento');
		foreach ($movimientos_filtrado as $key => $value) {
			$movimientos	=	$movimientos .' < '.$value.' > ';
		}
		return $movimientos;
	}

	private function PMP($bodegas ,$id){

		foreach ($bodegas as $key => $value) {
			$PMP[$key]= ClassRegistry::init('Pmp')->obtener_pmp($id, $key);
			
		}
		return $PMP;
	}

}