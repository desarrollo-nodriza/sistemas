<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'PhpSpreadsheet', array('file' => 'PhpSpreadsheet/vendor/autoload.php'));

class ZonasController extends AppController
{
    public $helpers = array('Html','Form');

    public function admin_index()
    {
        $this->paginate		= array(
			'recursive'	=> 0,
            // 'limit'     => 1
		);

		BreadcrumbComponent::add('Zonas');

		$zonas	= $this->paginate();
		$this->set(compact('zonas'));
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