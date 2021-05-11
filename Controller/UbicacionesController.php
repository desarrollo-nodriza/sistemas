<?php

App::uses('AppController', 'Controller');

class UbicacionesController extends AppController
{
    public $helpers = array('Html','Form');

    public function admin_index()
    {
        $this->paginate		= array(
			'recursive'	=> 0,
            // 'limit'     => 1
		);

		BreadcrumbComponent::add('Ubicaciones');

		$ubicaciones	= $this->paginate();
		$this->set(compact('ubicaciones'));
    }

    public function admin_add()
	{
		if ( $this->request->is('post') )
		{	
			
			$date = date("Y-m-d H:i:s");
			$this->Ubicacion->create();
			$data =$this->request->data;
			$data=$data['Ubicacion'];
			$data = 
			[
				'zona_id'				=>$data['zona_id'],
				'fila'			        =>$data['fila'],
				'columna'				=>$data['columna'],
				'alto'			        =>$data['alto'],
                'ancho'				    =>$data['ancho'],
				'profundidad'			=>$data['profundidad'],
                'mts_cubicos'			=>$data['mts_cubicos'],
				'activo'			    =>$data['activo'],
				"fecha_creacion"	    =>$date,
				"ultima_modifacion"	    =>$date
				
			];

			if ( $this->Ubicacion->save($data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		$zonas	= 	ClassRegistry::init('Zona')->find('list');
		

       
		BreadcrumbComponent::add('Ubicacion de bodega ', '/ubicaciones');
		BreadcrumbComponent::add('Crear ');
		

		$this->set(compact('zonas'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Ubicacion->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			$date = date("Y-m-d H:i:s");
			$data =$this->request->data;
			$data=$data['Ubicacion'];
			$data = 
			[
				'id'				    =>$data['id'],
                'zona_id'				=>$data['zona_id'],
				'fila'			        =>$data['fila'],
				'columna'				=>$data['columna'],
				'alto'			        =>$data['alto'],
                'ancho'				    =>$data['ancho'],
				'profundidad'			=>$data['profundidad'],
                'mts_cubicos'			=>$data['mts_cubicos'],
				'activo'			    =>$data['activo'],
				"ultima_modifacion"	    =>$date
				
			];

			if ( $this->Ubicacion->save($data) )
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
			$ubicacion	= $this->Ubicacion->find('first', array(
				'conditions'	=> array('Ubicacion.id' => $id)
			));
		}

		$zonas	= 	ClassRegistry::init('Zona')->find('list');
		

       
		BreadcrumbComponent::add('Ubicacion de bodega ', '/ubicaciones');
		BreadcrumbComponent::add('Editar ');
		

		$this->set(compact('ubicacion','zonas'));
	}
}