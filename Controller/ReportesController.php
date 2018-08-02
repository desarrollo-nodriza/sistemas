<?php
App::uses('AppController', 'Controller');
class ReportesController extends AppController
{
	public function admin_index()
	{
		$reportes = $this->Reporte->find('list', array('conditions' => array('Reporte.activo' => 1)));

		BreadcrumbComponent::add('Reportes ');

		$graficos	= $this->Reporte->Grafico->find('list', array('conditions' => array('Grafico.activo' => 1)));

		$this->set(compact('reportes', 'graficos'));
	}

	public function admin_all()
	{
		$this->paginate		= array(
			'recursive'			=> 1
		);
		BreadcrumbComponent::add('Todos los reportes ');

		$reportes	= $this->paginate();

		$this->set(compact('reportes'));
	}

	public function admin_add()
	{
		if ( $this->request->is('post') )
		{
			$this->Reporte->create();
			if ( $this->Reporte->save($this->request->data) )
			{
				$this->Session->setFlash('Registro agregado correctamente.', null, array(), 'success');
				$this->redirect(array('action' => 'index'));
			}
			else
			{
				$this->Session->setFlash('Error al guardar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
			}
		}

		BreadcrumbComponent::add('Reportes ', '/reportes');
		BreadcrumbComponent::add('Agregar ');

		$tiendas	= $this->Reporte->Tienda->find('list', array('conditions' => array('Tienda.activo' => 1)));
		$graficos	= $this->Reporte->Grafico->find('list', array('conditions' => array('Grafico.activo' => 1)));
		$this->set(compact('tiendas', 'graficos'));
	}

	public function admin_edit($id = null)
	{
		if ( ! $this->Reporte->exists($id) )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		if ( $this->request->is('post') || $this->request->is('put') )
		{
			if ( $this->Reporte->save($this->request->data) )
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
			$this->request->data	= $this->Reporte->find('first', array(
				'conditions'	=> array('Reporte.id' => $id),
				'contain'	=> array('Tienda', 'Grafico')
			));
		}

		BreadcrumbComponent::add('Reportes ', '/reportes');
		BreadcrumbComponent::add('Editar ');

		$tiendas	= $this->Reporte->Tienda->find('list', array('conditions' => array('Tienda.activo' => 1)));
		$graficos	= $this->Reporte->Grafico->find('list', array('conditions' => array('Grafico.activo' => 1)));
		$this->set(compact('tiendas', 'graficos'));
	}

	public function admin_delete($id = null)
	{
		$this->Reporte->id = $id;
		if ( ! $this->Reporte->exists() )
		{
			$this->Session->setFlash('Registro inválido.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

		$this->request->onlyAllow('post', 'delete');
		if ( $this->Reporte->delete() )
		{
			$this->Session->setFlash('Registro eliminado correctamente.', null, array(), 'success');
			$this->redirect(array('action' => 'index'));
		}
		$this->Session->setFlash('Error al eliminar el registro. Por favor intenta nuevamente.', null, array(), 'danger');
		$this->redirect(array('action' => 'index'));
	}

	public function admin_exportar()
	{
		$datos			= $this->Reporte->find('all', array(
			'recursive'				=> -1
		));
		$campos			= array_keys($this->Reporte->_schema);
		$modelo			= $this->Reporte->alias;

		$this->set(compact('datos', 'campos', 'modelo'));
	}


	/**
	 *  Generar reporte
	 */
	public function admin_generate () {

		if ( $this->request->is('post') )
		{
			if ( ! isset($this->request->data['Reporte']) || empty($this->request->data['Reporte']['id_reporte']) || empty($this->request->data['Reporte']['f_inicio']) || empty($this->request->data['Reporte']['f_final']) || empty($this->request->data['Reporte']['id_grafico'])) {
				$this->Session->setFlash('No se puede generar el reporte, verifique los campos e intente nuevamente.', null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}

			$this->Reporte->id = $this->request->data['Reporte']['id_reporte'];

			if ( ! $this->Reporte->exists() )
			{
				$this->Session->setFlash('No existe reporte.', null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}

			// Obtenemos información del reporte y de la tienda
			$reporte =  $this->Reporte->find('first', array(
				'conditions'	=> array('Reporte.id' => $this->request->data['Reporte']['id_reporte']),
				'contain'	=> array('Tienda')
				));

			// Obtenemos los gráficos
			$graficos = $this->Reporte->Grafico->find('all', array(
				'conditions' => array(
					'Grafico.id' => $this->request->data['Reporte']['id_grafico'],
					'Grafico.activo' => 1
					)
				));

			if ( empty($graficos) ) {
				$this->Session->setFlash('No es posible generar el reporte. No se encontró el/los gráficos seleccionados.', null, array(), 'danger');
				$this->redirect(array('action' => 'index'));
			}

			$fecha_inicial = sprintf("'%s'", $this->request->data['Reporte']['f_inicio']);
			$fecha_final = sprintf("'%s'", $this->request->data['Reporte']['f_final']);
			$prefijo = $reporte['Tienda']['prefijo'];

			$queryReporte = array();
			$resultReporte = array();
			$nuevoResult = array();
			// Sobreescribimos la configuración de la base d e datos a utilizar
			ClassRegistry::init('Orders')->useDbConfig = $reporte['Tienda']['configuracion'];
			$this->Conection = ClassRegistry::init('Orders');

			// Armamos las queries para cada gráfico
			foreach ($graficos as $indice => $grafico) {
				$grafico['Grafico']['descipcion'] = str_replace('[*START_DATE*]', $fecha_inicial, $grafico['Grafico']['descipcion']);
				$grafico['Grafico']['descipcion'] = str_replace('[*FINISH_DATE*]', $fecha_final, $grafico['Grafico']['descipcion']);
				$grafico['Grafico']['descipcion'] = str_replace('[*PREFIX*]', $prefijo, $grafico['Grafico']['descipcion']);

				$queryReporte[] = $grafico['Grafico']['descipcion'];
				$resultReporte[$grafico['Grafico']['slug']] = $this->Conection->query($queryReporte[$indice]);
			}


			BreadcrumbComponent::add('Reportes ', '/reportes');
			BreadcrumbComponent::add($reporte['Reporte']['nombre']);

			$data = $this->request->data['Reporte'];
			// Listado de graficos para enviar a la vista en formato "Json"
			$data['graficos'] = implode(',', $data['id_grafico']);
			$data['graficos'] =  '"' . str_replace(',','","', $data['graficos']) . '"';

			// Limpiamos el array $data
			unset($data['id_grafico']);

			$this->set(compact('resultReporte', 'reporte' , 'data' , 'graficos'));
			
		}else{
			$this->Session->setFlash('No es posible generar el reporte.', null, array(), 'danger');
			$this->redirect(array('action' => 'index'));
		}

	}


	/**
	 * Retorna el resultado de una query en formato json
	 * @return Json de información 
	 */
	public function admin_get_query_result_json() {
		if ($this->request->is('post')) {

			if (empty($this->request->data)) {
				echo "La solicitud no puede ser nula";
				exit;
			}

			if (empty($this->request->data['graficos']) || empty($this->request->data['reporte']) || empty($this->request->data['f_inicio']) || empty($this->request->data['f_final'])) {
				echo "La solicitud no puede ser nula";
				exit;
			}

			(int) $reporteId = $this->request->data['reporte'];
			(string) $f_inicio = $this->request->data['f_inicio'];
			(string) $f_final = $this->request->data['f_final'];
			(array) $graficos = $this->request->data['graficos'];


			$this->Reporte->id = $reporteId;

			if ( ! $this->Reporte->exists() )
			{
				echo "No existe reporte";
				exit;
			}

			// Obtenemos información del reporte y de la tienda
			$reporte =  $this->Reporte->find('first', array(
				'conditions'	=> array('Reporte.id' => $reporteId),
				'contain'	=> array('Tienda')
				));

			// Obtenemos los gráficos
			$graficos = $this->Reporte->Grafico->find('all', array(
				'conditions' => array(
					'Grafico.id' => $graficos,
					'Grafico.activo' => 1
					)
				));

			if ( empty($graficos) ) {
				echo "No es posible generar el reporte. No se encontró el/los gráficos seleccionados.";
				exit;
			}

			$fecha_inicial = sprintf("'%s'", $f_inicio);
			$fecha_final = sprintf("'%s'", $f_final);
			$prefijo = $reporte['Tienda']['prefijo'];

			$queryReporte = array();
			$resultReporte = array();
			$nuevoResult = array();

			// Sobreescribimos la configuración de la base de datos a utilizar
			ClassRegistry::init('Orders')->useDbConfig = $reporte['Tienda']['configuracion'];

			// Armamos las queries y ejecutamos para cada gráfico
			foreach ($graficos as $indice => $grafico) {
				$grafico['Grafico']['descipcion'] = str_replace('[*START_DATE*]', $fecha_inicial, $grafico['Grafico']['descipcion']);
				$grafico['Grafico']['descipcion'] = str_replace('[*FINISH_DATE*]', $fecha_final, $grafico['Grafico']['descipcion']);
				$grafico['Grafico']['descipcion'] = str_replace('[*PREFIX*]', $prefijo, $grafico['Grafico']['descipcion']);

				$queryReporte[] = $grafico['Grafico']['descipcion'];

				$resultReporte[$grafico['Grafico']['slug']] = ClassRegistry::init('Orders')->query($queryReporte[$indice]);

			}

			print_r(json_encode($resultReporte));
			exit;


		}
		
	}
}
