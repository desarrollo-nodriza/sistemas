<?php
App::uses('AppController', 'Controller');
class SociosController extends AppController
{	
	function beforeFilter() {
	    parent::beforeFilter();

	    if (isset($this->request->params['socio'])) {
	    	#$this->Auth->allow('prisync');	
	    }
	}

	public function admin_index()
	{
		$this->paginate		= array(
			'recursive'  => 0,
			'conditions' => array('Socio.tienda_id' => $this->Session->read('Tienda.id')),
			'contain'    => array('Tienda')
		);

		BreadcrumbComponent::add('Socios ');

		$socios	= $this->paginate();
		$this->set(compact('socios'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{	
			$this->Socio->create();
			if ( $this->Socio->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Socios ', 'socios');
		BreadcrumbComponent::add('Agregar ');

		# cambiamos el datasource de las modelos externos
		$this->cambiarConfigDB($this->tiendaConf($this->Session->read('Tienda.id')));

		$fabricantes	= $this->Socio->Fabricante->find('list');
		$this->set(compact('fabricantes'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Socio->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{	
			if ( $this->Socio->save($this->request->data) )
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

			# cambiamos el datasource de las modelos externos
			$this->cambiarConfigDB($this->tiendaConf($this->Session->read('Tienda.id')));

			$this->request->data	= $this->Socio->find('first', array(
				'conditions'	=> array('Socio.id' => $id),
				'contain' => array('Fabricante')
			));

			BreadcrumbComponent::add('Socios ', 'socios');
			BreadcrumbComponent::add('Editar ');

			$fabricantes	= $this->Socio->Fabricante->find('list');
			$this->set(compact('fabricantes'));
		}
	}

	public function admin_delete($id = null)
	{
		$this->Socio->id = $id;
		if ( ! $this->Socio->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Socio->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Socio->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Socio->_schema);
		$modelo			= $this->Socio->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	/**
	 * Arma un arreglo con los nombres de los competidores
	 * @param  array  $data [description]
	 * @return [type]       [description]
	 */
	private function prepararTabla($data = array())
	{
		$competidores = array();
		$respuesta = array();

		foreach ($data as $ip => $producto) {

			foreach ($producto['PrisyncRuta'] as $ic => $competidor) {
				$url = parse_url($competidor['url']);
				if (isset($url['host']) && !empty($url['host'])) {
					$competidores[] = $url['host'];
					if ($competidor['price'] > 0) {
						$data[$ip]['PrisyncProducto'][$url['host'] . '_id']        = $competidor['id'];
						$data[$ip]['PrisyncProducto'][$url['host'] . '_price']     = $competidor['price'];
						$data[$ip]['PrisyncProducto'][$url['host'] . '_old']       = $competidor['old_price'];
						$data[$ip]['PrisyncProducto'][$url['host'] . '_available'] = $competidor['in_stock'];
					}elseif (count($producto['PrisyncRuta']) > 1){
						unset($producto['PrisyncRuta'][$ic]);
					}
				}
			}

			# Minimo valor
			$data[$ip]['PrisyncProducto']['min_price'] = min(Hash::extract($producto['PrisyncRuta'], '{n}.price'));
		}

		$respuesta['competidores'] = array_unique($competidores);
		$respuesta['productos'] = $data;
		
		return $respuesta;

	}

	/**
	 * API Socios
	 *  Permite acceder al método por un usuario dado unico
	 */
	public function socio_prisync()
	{	
		ini_set('memory_limit', '-1');

		$tienda_id = $this->Auth->user('tienda_id');
		$usuario   = $this->Auth->user('usuario');
		
		# Comprobamos la tienda
		$tienda = $this->tiendaConf($tienda_id);

		if (!empty($tienda)) {

			# cambiamos el datasource de las modelos externos
			$this->cambiarConfigDB($tienda);

			# Obtenemos el socio, sus fabricantes y sus productos
			$socio = $this->Socio->find('first', array(
				'fields' => array(
					'Socio.usuario',
					'Socio.nombre',
					'Socio.email',
					'Socio.created'
				),
				'conditions' => array(
					'Socio.usuario' => $usuario,
					'Socio.activo' => 1
				),
				'contain' => array(
					'Fabricante' => array(
						'fields' => array(
							'Fabricante.id_manufacturer',
							'Fabricante.name'
						),
						'Productotienda' => array(
							'fields' => array(
								'Productotienda.id_product',
								'Productotienda.reference'
							)
						)
					)
				)
			));

			# Mensaje en caso de que no exista el socio
			if (empty($socio)) {
				$out = array(
					'code' => 404,
					'message' => 'No existe el socio consultado'
				);

				$this->layout = 'ajax';

				$out = str_replace('"', '\\\"', $out);
				header('Content-Type: application/json; charset=utf-8');
				echo json_encode($out, JSON_UNESCAPED_UNICODE);
				exit;	
			}

			# Buscamos los productos de la tabla Prisync que tengan 
			# relación con la referencia de los productos del fabricante.
			$productosSocioReferencia = Hash::extract($socio, 'Fabricante.{n}.Productotienda.{n}.reference'); 
			
			$prisyncProductos = ClassRegistry::init('PrisyncProducto')->find('all', array(
				'conditions' => array(
					'PrisyncProducto.internal_code' => $productosSocioReferencia
				),
				'contain' => array(
					'PrisyncRuta'
				)
			));

			$productos = $this->prepararTabla($prisyncProductos);
			#prx($productos);
			$this->layout = 'socio';

			$this->set(compact('socio', 'productos' ,'prisyncProductos'));

		}else{

			$out = array(
				'code' => 500,
				'message' => 'La petición no se ejecutó correctamente'
			);

			$this->layout = 'ajax';

			$out = str_replace('"', '\\\"', $out);
			header('Content-Type: application/json; charset=utf-8');
			echo json_encode($out, JSON_UNESCAPED_UNICODE);
			exit;	
		}	
	}


	public function socio_login()
	{	
		if ( $this->request->is('post') )
		{
			if ($this->Auth->login()) {
	            return $this->redirect($this->Auth->redirect());
	        } else {
	        	$this->Session->setFlash('Nombre de usuario y/o clave incorrectos.', null, array(), 'danger');
	        }
	    }
	    
	    $this->layout = 'login';
	}


	public function socio_logout()
	{	
		$this->redirect($this->Auth->logout());
	}


	public function obtener_historico($id = null, $f_inicio = null, $f_final = null, $group_by = null)
	{
		$jsonArray = array();

		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final) || $f_inicio == 'undefined' || $f_final == 'undefined' ) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		//Normalizar fechas
		$f_inicio = sprintf("'%s'", $f_inicio);
		$f_final = sprintf("'%s'", $f_final);

		if (is_null($group_by) || empty($group_by) || $group_by == 'undefined') {
			$group_by = 'dia';
		}

		$query = array(
			'conditions' => array(
				'PrisyncHistorico.ruta_id' => $id,
				'PrisyncHistorico.created BETWEEN ' . $f_inicio . ' AND ' . $f_final
			)
		);

		switch ($group_by) {
			case 'dia':
				$query = array_replace_recursive($query, array(
					'fields' => array(
						'DATE_FORMAT(PrisyncHistorico.created, "%Y-%m-%d") AS Fecha',
						'PrisyncHistorico.precio'
					),
					'group' => array('DAY(PrisyncHistorico.created)'),
					'order' => array('PrisyncHistorico.created' => 'DESC')
				));
				break;
			case 'semana':

				$query = array_replace_recursive($query, array(
					'fields' => array(
						'DATE_FORMAT(PrisyncHistorico.created, "%Y-%m-%d") AS Fecha',
						'PrisyncHistorico.precio'
					),
					'group' => array('WEEK(PrisyncHistorico.created)'),
					'order' => array('PrisyncHistorico.precio' => 'DESC')
				));

				break;
		}
		#prx($query);
		$precios = ClassRegistry::init('PrisyncHistorico')->find('all', $query);

		#prx($precios);
		

		# Normalizamos
		foreach ($precios as $ip => $precio) {
			$jsonArray[$ip]['y'] = $precio[0]['Fecha'];
			$jsonArray[$ip]['a'] = round($precio['PrisyncHistorico']['precio']);
		}

		echo json_encode($jsonArray);
		exit;
		prx($precios);
	}
}
