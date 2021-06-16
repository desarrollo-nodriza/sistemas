<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'PhpSpreadsheet', array('file' => 'PhpSpreadsheet/vendor/autoload.php'));

class ZonasController extends AppController
{
    public $helpers = array('Html','Form');


	public function filtrar($controlador = '', $accion = '')
    {
    	$redirect = array(
    		'controller' => $controlador,
    		'action' => $accion
    		);

		foreach ($this->request->data['Filtro'] as $campo => $valor) {
			if ($valor != '') {
				$redirect[$campo] = str_replace('/', '-', $valor);
			}
		}
		
    	$this->redirect($redirect);

    }

    public function admin_index()
    {
        $filtro =[];


		if ( $this->request->is('post') ) { 
			$this->filtrar('zonas', 'index');
		}
		
		if ( isset($this->request->params['named']) ) {

			$inputs = $this->request->params['named'];
			
			$filtro = [
				'id' 			=> $inputs['id']		?? null,
				'bodega_id' 	=> $inputs['bodega_id']	?? null,
				'nombre LIKE' 	=> ($inputs['nombre']??null)  ? '%'.$inputs['nombre'].'%': null,
				'tipo' 			=> $inputs['tipo']		?? null,
				'activo' 		=> $inputs['activo']	?? null,
			];
			
			$filtro = array_filter($filtro,function($v, $k) {
				return $v === false || $v === true  || $v != ''  || $v != null ;
			}, ARRAY_FILTER_USE_BOTH);
		}

		
		$this->paginate		= array(
			'recursive'	=> 0,
            'limit' => 20,
			'order' => array('id' => 'DESC'),
			'conditions'=> $filtro
		);
		

		$bodegas = ClassRegistry::init('Bodega')->find('list');
		$tipos = [
			'recepcion'		=>'Recepcion',
			'inventario'	=>'Inventario',
			'picking' 		=>'Picking'
		];
		BreadcrumbComponent::add('Zonas');

		$zonas	= $this->paginate();
		$this->set(compact('zonas','bodegas','tipos'));
    }

    public function admin_add()
	{
		if ( $this->request->is('post') )
		{	
			
			$date = date("Y-m-d H:i:s");
			$this->Zona->create();
			$data =$this->request->data;
			$data=$data['Zona'];
			$data = 
			[
				'nombre'			=>$data['nombre'],
				'tipo'				=>$data['tipo'],
				'bodega_id'			=>$data['bodega_id'],
				'activo'			=>$data['activo'],
				"fecha_creacion"	=>$date,
				"ultima_modifacion"	=>$date
				
			];

			if ( $this->Zona->save($data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Zonas de bodega ', '/zonas');
		BreadcrumbComponent::add('Crear ');

		$bodegas	= 	ClassRegistry::init('Bodega')->find('list');
		$tipos = ['recepcion'=>'recepcion','inventario'=>'inventario','picking'=>'picking'];

		$this->set(compact('bodegas','tipos'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Zona->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			$date = date("Y-m-d H:i:s");
			$data =$this->request->data;
			$data=$data['Zona'];
			$data = 
			[
				'id'				=>$data['id'],
				'nombre'			=>$data['nombre'],
				'tipo'				=>$data['tipo'],
				'bodega_id'			=>$data['bodega_id'],
				'activo'			=>$data['activo'],
				"ultima_modifacion"	=>$date
				
			];

			if ( $this->Zona->save($data) )
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
			$zona	= $this->Zona->find('first', array(
				'conditions'	=> array('Zona.id' => $id)
			));
		}

		$bodegas	= 	ClassRegistry::init('Bodega')->find('list');
		$tipos = ['recepcion'=>'recepcion','inventario'=>'inventario','picking'=>'picking'];

       
		BreadcrumbComponent::add('Zonas de bodega ', '/zonas');
		BreadcrumbComponent::add('Editar ');
		

		$this->set(compact('zona','bodegas','tipos'));
	}

}