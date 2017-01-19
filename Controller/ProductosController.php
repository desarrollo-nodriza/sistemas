<?php

App::uses('AppController', 'Controller');

class ProductosController extends AppController

{

	public function admin_index()

	{	

		$conditions = array();

		// Condicones para super administrador
		if ( $this->Auth->user('admin') == 0 ) {
			$conditions = array('Producto.activo' => 1);
		}

		$this->paginate		= array(

			'recursive'			=> 0,
			'limit'	=> 1000,
			'conditions' => $conditions

		);


		$productos	= $this->paginate();

		$this->set(compact('productos'));

	}



	public function admin_add()

	{

		if ( $this->request->is('post') )

		{

			$this->Producto->create();

			if ( $this->Producto->save($this->request->data) )

			{
				
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');

				$this->redirect(array('action' => 'index'));

			}

			else

			{

				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');

			}

		}

		

		$categorias	= $this->Producto->Categoria->find('list', array('conditions' => array('Categoria.activo' => 1)));

		$this->set(compact('categorias'));

	}



	public function admin_edit($id = null)

	{

		if ( ! $this->Producto->exists($id) )

		{
			
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');

			$this->redirect(array('action' => 'index'));

		}



		if ( $this->request->is('post') || $this->request->is('put') )

		{	

			/**

			* Se eliminan las relaciones categorias-productos para volver a agregarlas 

			*/

			$this->Producto->CategoriasProducto->deleteAll(

				array(

					'CategoriasProducto.producto_id' => $id,

				)

           	);



			if ( $this->Producto->save($this->request->data) )

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

			$this->request->data	= $this->Producto->find('first', array(

				'conditions'	=> array('Producto.id' => $id),

				'contain'		=> array('Categoria')

			));

		}

		$categorias	= $this->Producto->Categoria->find('list', array('conditions' => array('Categoria.activo' => 1)));

		$this->set(compact('categorias'));

	}



	public function admin_delete($id = null)

	{

		$this->Producto->id = $id;

		if ( ! $this->Producto->exists() )

		{

			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');

			$this->redirect(array('action' => 'index'));

		}



		$this->request->onlyAllow('post', 'delete');

		if ( $this->Producto->delete() )

		{

			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');

			$this->redirect(array('action' => 'index'));

		}

		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');

		$this->redirect(array('action' => 'index'));

	}



	public function admin_exportar()

	{

		$datos			= $this->Producto->find('all', array(

			'recursive'				=> -1

		));

		$campos			= array_keys($this->Producto->_schema);

		$modelo			= $this->Producto->alias;



		$this->set(compact('datos', 'campos', 'modelo'));

	}





	/**

	* Función que desactiva un producto

	* @param $id 	Integer 	Identificador del producto

	*/

	public function admin_desactivar($id = null)

	{

		$this->Producto->id = $id;

		if ( ! $this->Producto->exists() )

		{

			$this->Session->setFlash('Producto no existe.', null, array(), 'danger');

			$this->redirect(array('action' => 'index'));

		}

		

		if ( $this->Producto->saveField('activo', 0) )

		{

			$this->Session->setFlash('Producto desactivado correctamente.', null, array(), 'success');

			$this->redirect(array('action' => 'index'));

		}

		$this->Session->setFlash('Error al desactivar el producto. Por favor intenta nuevamente.', null, array(), 'danger');

		$this->redirect(array('action' => 'index'));

	}





	/**

	* Función que activa un producto

	* @param $id 	Integer 	Identificador del producto

	*/

	public function admin_activar($id = null)

	{

		$this->Producto->id = $id;

		if ( ! $this->Producto->exists() )

		{

			$this->Session->setFlash('Producto no existe.', null, array(), 'danger');

			$this->redirect(array('action' => 'index'));

		}



		

		if ( $this->Producto->saveField('activo', 1) )

		{

			$this->Session->setFlash('Producto activado correctamente.', null, array(), 'success');

			$this->redirect(array('action' => 'index'));

		}

		$this->Session->setFlash('Error al eliminar el Producto. Por favor intenta nuevamente.', null, array(), 'danger');

		$this->redirect(array('action' => 'index'));

	}

}

