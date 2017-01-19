<?php
App::uses('AppController', 'Controller');
App::uses('CakeEmail', 'Network/Email');
class PlantillasController extends AppController
{
	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'			=> 0
		);

		BreadcrumbComponent::add('Plantillas ');

		$plantillas	= $this->paginate();
		$this->set(compact('plantillas'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{  	/// Slug 
			$this->request->data['Plantilla']['slug'] = strtolower(Inflector::slug($this->request->data['Plantilla']['nombre']));

			$html = $this->request->data['Plantilla']['html'];

			$url_imagenes = Router::url('/', true) . 'img/' . $this->request->data['Plantilla']['slug'] . '/';
			
			// Plantilla Toolmania
			if ($this->request->data['Plantilla']['slug'] == 'toolmania') {
				$logo_tool 		= $url_imagenes . 'logo.png';
				$logo_facebook 	= $url_imagenes . 'facebook.png';
				$boton 			= $url_imagenes . 'btn-ver-mas.png';
				$footer_1		= $url_imagenes . 'footer-1.png';
				$footer_2		= $url_imagenes . 'footer-2.png';
				$footer_3		= $url_imagenes . 'footer-3.png';
				$footer_4		= $url_imagenes . 'footer-4.png';

				$html = str_replace('[**url_logo_facebook**]', $logo_facebook, $html);
				$html = str_replace('[**url_logo_toolmania**]', $logo_tool, $html);
				$html = str_replace('[**url_boton**]', $boton, $html);
				$html = str_replace('[**url_footer_1**]', $footer_1, $html);
				$html = str_replace('[**url_footer_2**]', $footer_2, $html);
				$html = str_replace('[**url_footer_3**]', $footer_3, $html);
				$html = str_replace('[**url_footer_4**]', $footer_4, $html);	

			}

			$this->request->data['Plantilla']['html'] = $html;

			$this->Plantilla->create();
			if ( $this->Plantilla->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}

			BreadcrumbComponent::add('Plantillas ', '/plantillas');
			BreadcrumbComponent::add('Agregar ');
		}
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Plantilla->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	

			// Slug 
			$this->request->data['Plantilla']['slug'] = strtolower(Inflector::slug($this->request->data['Plantilla']['nombre']));

			$html = $this->request->data['Plantilla']['html'];

			$url_imagenes = Router::url('/', true) . 'img/' . $this->request->data['Plantilla']['slug'] . '/';
			
			// Plantilla Toolmania
			if ($this->request->data['Plantilla']['slug'] == 'toolmania') {
				$logo_tool 		= $url_imagenes . 'logo.png';
				$logo_facebook 	= $url_imagenes . 'facebook.png';
				$boton 			= $url_imagenes . 'btn-ver-mas.png';
				$footer_1		= $url_imagenes . 'footer-1.png';
				$footer_2		= $url_imagenes . 'footer-2.png';
				$footer_3		= $url_imagenes . 'footer-3.png';
				$footer_4		= $url_imagenes . 'footer-4.png';

				$html = str_replace('[**url_logo_facebook**]', $logo_facebook, $html);
				$html = str_replace('[**url_logo_toolmania**]', $logo_tool, $html);
				$html = str_replace('[**url_boton**]', $boton, $html);
				$html = str_replace('[**url_footer_1**]', $footer_1, $html);
				$html = str_replace('[**url_footer_2**]', $footer_2, $html);
				$html = str_replace('[**url_footer_3**]', $footer_3, $html);
				$html = str_replace('[**url_footer_4**]', $footer_4, $html);	

			}

			$this->request->data['Plantilla']['html'] = $html;

			
			if ( $this->Plantilla->save($this->request->data) )
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
			$this->request->data	= $this->Plantilla->find('first', array(
				'conditions'	=> array('Plantilla.id' => $id)
			));
		}
		BreadcrumbComponent::add('Plantillas ', '/plantillas');
		BreadcrumbComponent::add('Editar ');
	}

	public function admin_view($id = null)
	{
		if ( ! $this->Plantilla->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		
			$this->request->data	= $this->Plantilla->find('first', array(
				'conditions'	=> array('Plantilla.id' => $id)
			));

		BreadcrumbComponent::add('Plantillas ', '/plantillas');
		BreadcrumbComponent::add('Ver ');

	}

	public function admin_delete($id = null)
	{
		$this->Plantilla->id = $id;
		if ( ! $this->Plantilla->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Plantilla->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Plantilla->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Plantilla->_schema);
		$modelo			= $this->Plantilla->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}
}
