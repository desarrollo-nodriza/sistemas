<?php

App::uses('AppController', 'Controller');
App::import('Vendor', 'PhpSpreadsheet', array('file' => 'PhpSpreadsheet/vendor/autoload.php'));
App::uses('CakePdf', 'Plugin/CakePdf/Pdf');
App::import('Vendor', 'PDFMerger', array('file' => 'PDFMerger/PDFMerger.php'));

class UbicacionesController extends AppController
{
    public $helpers = array('Html','Form');
	

    public function admin_index()
    {

		$filtro =[];
		
		if ( isset($this->request->data['Filtro']) ) {

			$inputs = $this->request->data['Filtro'];
			
			$filtro = [
				'id' 				=> $inputs['id']		?? null,
				'zona_id' 			=> $inputs['zona_id']	?? null,
				'fila LIKE' 		=> (trim($inputs['fila']) != '' )  ? '%'.$inputs['fila'].'%': null,
				'columna LIKE' 		=> (trim($inputs['columna']) != '' )  ? '%'.$inputs['columna'].'%': null,
				'Ubicacion.activo' 	=> $inputs['activo']	?? null,
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

		$zonas = ClassRegistry::init('Zona')->find('list');

		BreadcrumbComponent::add('Ubicaciones');

		$ubicaciones	= $this->paginate();
		$this->set(compact('ubicaciones', 'zonas'));
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

	
	/**
	 * admin_creacion_masiva
	 *
	 * @return void
	 */
	public function admin_creacion_masiva()
	{
		$tipoPermitido = array(
			'xlsx',
			'xls',
			'csv'
		);

		$datos = array();

		if ( $this->request->is('post') || $this->request->is('put')) {

			ini_set('max_execution_time', 0);
			ini_set('memory_limit', -1);
			
			if ($this->request->data['Ubicacion']['archivo']['error'] == 0 ) {
				# Reconocer cabecera e idenitficador
				if ($this->request->data['Ubicacion']['archivo']['error'] != 0) {
					$this->Session->setFlash('El archivo contiene errores o está dañado.', null, array(), 'danger');
					$this->redirect(array('action' => 'creacionMasivaUbicaciones'));
				}

				$ext = pathinfo($this->request->data['Ubicacion']['archivo']['name'], PATHINFO_EXTENSION);

				if (!in_array($ext, $tipoPermitido)) {
					$this->Session->setFlash('El formato '.$ext.' no es válido. Los formatos permitidos son: ' . implode($tipoPermitido, ','), null, array(), 'danger');
					$this->redirect(array('action' => 'creacionMasivaUbicaciones'));
				}


				$spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($this->request->data['Ubicacion']['archivo']['tmp_name']);
				$sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);
				
				if (isset($sheetData[1])) {
					foreach ($sheetData[1] as $k => $cabecera) {
						$datos['options'][$k] = $cabecera;
					}						
				}
				
				$datos['data'] = $sheetData;
				
				$this->Session->write('creacionMasivaUbicaciones', $datos);

			}else{

				$dataToSave = array();	

				foreach ($this->request->data['Indice'] as $a => $i) {
					if (empty($i)) {
						unset($this->request->data['Indice'][$a]);
					}
				}

				# Se obtienen los índices para cada elemento
				$columna_zona_nombre = array_search('zona', $this->request->data['Indice']); 
				$columna_zona_bodega     = array_search('bodega_id', $this->request->data['Indice']);
				$columna_zona_tipo       = array_search('tipo', $this->request->data['Indice']);
				$columna_zona_activo       = array_search('activo', $this->request->data['Indice']);
				$columna_ubicacion_fila       = array_search('ubicacion_fila', $this->request->data['Indice']);
				$columna_ubicacion_columna       = array_search('ubicacion_columna', $this->request->data['Indice']);
				$columna_ubicacion_alto       = array_search('ubicacion_alto', $this->request->data['Indice']);
				$columna_ubicacion_ancho       = array_search('ubicacion_ancho', $this->request->data['Indice']);
				$columna_ubicacion_profundidad       = array_search('ubicacion_profundida', $this->request->data['Indice']);
				$columna_ubicacion_mts_cubicos       = array_search('ubicacion_mts_cubicos', $this->request->data['Indice']);
				$columna_ubicacion_activo       = array_search('ubicacion_activo', $this->request->data['Indice']);

				if (empty($columna_zona_nombre) || empty($columna_zona_bodega) || empty($columna_ubicacion_fila) || empty($columna_ubicacion_columna)) 
				{
					$this->Session->setFlash('Falta indicar la columna de zona, bodega, ubicacion_fila, ubicacion_columna.', null, array(), 'danger');
					$this->redirect(array('action' => 'creacionMasivaUbicaciones'));
				}

				$data = $this->Session->read('creacionMasivaUbicaciones.data');
				$result = array();

				
				if (!empty($data)) 
				{	
					foreach ($data as $indice => $valor) 
					{	
						if (empty($valor[$columna_zona_nombre]) || $indice == 1) 
						{
							continue;
						}

						if (!ClassRegistry::init('Bodega')->exists($valor[$columna_zona_bodega])) 
						{
							continue;
						}

						if (empty($valor[$columna_ubicacion_fila]) || empty($valor[$columna_ubicacion_columna]))
						{
							continue;
						}

						$ubicacion = [];

						# Verificamos la existyencia de la zona en la bodega dada
						$zona = ClassRegistry::init('Zona')->find('first', array(
							'conditions' => array(
								'Zona.nombre' => trim($valor[$columna_zona_nombre]),
								'Zona.bodega_id' => $valor[$columna_zona_bodega]
							)
						));

						# si existe la zona, solo agregamos el id, de lo conteario se crea.
						if (!empty($zona))
						{	
							$ubicacion['Zona']['id'] = $zona['Zona']['id'];


							$ubi = $this->Ubicacion->find('first', array(
								'conditions' => array(
									'Ubicacion.zona_id' => $zona['Zona']['id'],
									'Ubicacion.fila' => $valor[$columna_ubicacion_fila],
									'Ubicacion.columna' => $valor[$columna_ubicacion_columna]
								)
							));

							# Si existe la ubicación le agregamos el ID
							if (!empty($ubi))
							{
								$ubicacion['Ubicacion'][$indice]['id'] = $ubi['Ubicacion']['id'];
							}
						}
						else
						{
							$ubicacion['Zona']['bodega_id'] = $valor[$columna_zona_bodega];
							$ubicacion['Zona']['nombre'] = trim($valor[$columna_zona_nombre]);
							$ubicacion['Zona']['tipo'] = (isset($valor[$columna_zona_tipo])) ? $valor[$columna_zona_tipo] : 'inventario' ;
							$ubicacion['Zona']['activo'] = (isset($valor[$columna_zona_activo])) ? (int) $valor[$columna_zona_activo] : (int) 1 ;
							$ubicacion['Zona']['fecha_creacion'] = date('Y-m-d H:i:s');
							$ubicacion['Zona']['ultima_modifacion'] = date('Y-m-d H:i:s');
						}

						# Creamos la ubicación
						$ubicacion['Ubicacion'][$indice]['fila'] = $valor[$columna_ubicacion_fila];
						$ubicacion['Ubicacion'][$indice]['columna'] = $valor[$columna_ubicacion_columna];
						$ubicacion['Ubicacion'][$indice]['alto'] = (isset($valor[$columna_ubicacion_alto])) ? (float) $valor[$columna_ubicacion_alto] : (float) 0.00;
						$ubicacion['Ubicacion'][$indice]['ancho'] = (isset($valor[$columna_ubicacion_ancho])) ? (float) $valor[$columna_ubicacion_ancho] : (float) 0.00;
						$ubicacion['Ubicacion'][$indice]['profundidad'] = (isset($valor[$columna_ubicacion_profundidad])) ? (float) $valor[$columna_ubicacion_profundidad] : (float) 0.00;
						$ubicacion['Ubicacion'][$indice]['mts_cubicos'] = (isset($valor[$columna_ubicacion_mts_cubicos])) ? (float) $valor[$columna_ubicacion_mts_cubicos] : (float) 0.00;
						$ubicacion['Ubicacion'][$indice]['activo'] = (isset($valor[$columna_ubicacion_activo])) ? (int) $valor[$columna_ubicacion_activo] : (int) 1;
						$ubicacion['Ubicacion'][$indice]['fecha_creacion'] = date('Y-m-d H:i:s');
						$ubicacion['Ubicacion'][$indice]['ultima_modifacion'] = date('Y-m-d H:i:s');
						
						# Si existe la zona solo creamos la ubicacion
						if (ClassRegistry::init('Zona')->saveAll($ubicacion))
						{
							$result['procesados'][] = $ubicacion;
						}
						else
						{
							$result['errores'][] = sprintf('Ubicación %s-%s de la Zona %s no pudo ser guardada.', $valor[$columna_ubicacion_columna], $valor[$columna_ubicacion_fila]);
						}

					}
				}
				
				if (empty($result)) 
				{
					$this->Session->setFlash('No se encontraron valores para crear.', null, array(), 'warning');
					$this->redirect(array('action' => 'creacionMasivaUbicaciones'));
				}
				
				if (isset($result['errores'])) 
				{
					$this->Session->setFlash($this->crearAlertaUl($result['errores'], 'Errores encontrados'), null, array(), 'danger');
				}

				if (isset($result['procesados'])) 
				{
					$this->Session->setFlash(sprintf('%d ubicaciones registradas con éxito.', count($result['procesados'])), null, array(), 'success');
				}

				$this->Session->delete('creacionMasivaUbicaciones');

			}

		}

		$columnas = array(
			'zona' => 'Nombre de la zona', 
			'bodega_id' => 'ID de bodega', 
			'tipo' => 'Tipo de zona',
			'activo' => 'Estado de la zona',
			'ubicacion_fila' => 'Ubicación Fila',
			'ubicacion_columna' => 'Ubicación Columna',
			'ubicacion_alto' => 'Ubicación alto (cm)',
			'ubicacion_ancho' => 'Ubicación ancho (cm)',
			'ubicacion_profundida' => 'Ubicación profundidad (cm)',
			'ubicacion_mts_cubicos' => 'Ubicación mts',
			'ubicacion_activo' => 'Estado de la ubicación'
		);

		BreadcrumbComponent::add('Ubicaciones', '/ubicaciones');
		BreadcrumbComponent::add('Creación masiva');

		$this->set(compact('columnas'));
	}


	public function admin_exportar()
	{	
		$datos = $this->Ubicacion->find('all');

		$campos			= array_keys($this->Ubicacion->_schema);

		$this->set(compact('campos', 'datos'));
	}

	public function admin_crear_etiqueta_qr()
	{	
		ini_set('max_execution_time', 0);
		ini_set('memory_limit', -1);

		$ubicaciones = $this->Ubicacion->find('all', array(
			'conditions' => array(
				'Ubicacion.activo' => 1
			),
			'joins' => array(
				array(
					'table' => 'zonas',
					'alias' => 'zona',
					'type' => 'INNER',
					'conditions' => array(
						'zona.id = Ubicacion.zona_id',
						'zona.activo' => 1
					)
				),
			),
			'fields' => array(
				'Ubicacion.id'
			)
		));

		if (empty($ubicaciones))
		{
			$this->Session->setFlash('No hay ubicaciones activas para exportar', null, array(), 'warning');
			$this->redirect($this->referer('/', true));
		}

		$pdfs = [];

		foreach ($ubicaciones as $ubicacion) 
		{
			$pdf = $this->crear_etiqueta_qr($ubicacion['Ubicacion']['id']);
			$pdfs[] = $pdf['path'];
		}

		if (empty($pdfs))
		{
			$this->Session->setFlash('No se puede generar los QRs', null, array(), 'warning');
			$this->redirect($this->referer('/', true));
		}

		# Unimos
		$archivo_final = $this->unir_documentos($pdfs);
		$pdf_resultado = [];

		if (!empty($archivo_final['result'])) {
			foreach ($archivo_final['result'] as $ir => $url) {
				$pdf_resultado[] = '<a href="'.$url['document'].'" class="link" download><i class="fa fa-download"></i> Descargar PDF QRs </a>';
			}
		}

		if (!empty($pdf_resultado)) {
			$this->Session->setFlash($this->crearAlertaUl($pdf_resultado, 'Descargas disponibles'), null, array(), 'success');	
		}
		else
		{
			$this->Session->setFlash('No fue posible generar los pdfs de los qrs', null, array(), 'warning');	
		}

		$this->redirect($this->referer('/', true));
	
	}


	public function admin_qr_ubicacion($ubicacion_id)
	{
		if (!$this->Ubicacion->exists($ubicacion_id))
		{
			$this->Session->setFlash('No existe la ubicación indicada.', null, array(), 'warning');
			$this->redirect($this->referer('/', true));
		}

		$ubicacion = $this->Ubicacion->find('first', array(
			'conditions' => array(
				'Ubicacion.id' => $ubicacion_id
			),
			'contain' => array(
				'Zona' => array(
					'Bodega' => array(
						'fields' => array(
							'Bodega.id',
							'Bodega.nombre'
						)
					),
					'fields' => array(
						'Zona.id',
						'Zona.nombre'
					)
				)
			),
			'fields' => array(
				'Ubicacion.id',
				'Ubicacion.fila',
				'Ubicacion.columna'
			)
		));

		$this->pdfConfig = array(
			'download' => false,
			'filename' => 'ubicacion_' . $ubicacion_id .'.pdf'
		);
		
		$tamano = '500x500';

		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
			'id',
			'logo'
		));

		# Creamos la etiqueta de despacho interna
		$logo = FULL_BASE_URL . '/webroot/img/' . $tienda['Tienda']['logo']['path'] ;

		$this->set(compact('ubicacion', 'tamano', 'logo'));

	}

	
	/**
	 * crear_etiqueta_qr
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function crear_etiqueta_qr($id)
	{
		if (!$this->Ubicacion->exists($id))
		{
			return false;
		}

		$ubicacion = $this->Ubicacion->find('first', array(
			'conditions' => array(
				'Ubicacion.id' => $id
			),
			'contain' => array(
				'Zona' => array(
					'Bodega' => array(
						'fields' => array(
							'Bodega.id',
							'Bodega.nombre'
						)
					),
					'fields' => array(
						'Zona.id',
						'Zona.nombre'
					)
				)
			),
			'fields' => array(
				'Ubicacion.id',
				'Ubicacion.fila',
				'Ubicacion.columna'
			)
		));

		$vista = new View();
		$vista->layoutPath = 'pdf';
		$vista->viewPath   = 'Ubicaciones/pdf';
		$vista->output     = '';
		$vista->layout     = 'default';
		
		$tamano = '500x500';

		$tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
			'id',
			'logo'
		));

		# Creamos la etiqueta de despacho interna
		$logo = FULL_BASE_URL . '/webroot/img/' . $tienda['Tienda']['logo']['path'] ;

		$vista->set(compact('ubicacion', 'tamano', 'logo'));
		
		$vista = $vista->render('qr_ubicacion');	
	
		return $this->generar_pdf($vista, $id);

	}

	
	/**
	 * generar_pdf
	 *
	 * @param  mixed $html
	 * @param  mixed $ubicacion_id
	 * @param  mixed $orientacion
	 * @param  mixed $tamano
	 * @return void
	 */
	public function generar_pdf($html = '', $ubicacion_id = '', $orientacion = 'potrait', $tamano = 'A4') 
	{

		$rutaAbsoluta = APP . 'webroot' . DS . 'Pdf' . DS . 'Ubicacion' . DS . $ubicacion_id . '.pdf';

		try {
			$this->CakePdf = new CakePdf();
			$this->CakePdf->orientation($orientacion);
			$this->CakePdf->pageSize($tamano);
			@$this->CakePdf->write($rutaAbsoluta, true, $html);	
		} catch (Exception $e) { 
			return array();
		}

		# Ruta para guardar en la Base de datos
		$archivo = Router::url('/', true) . 'Pdf/Ubicacion/' . $ubicacion_id . '.pdf';

		return array('public' => $archivo, 'path' => $rutaAbsoluta);

	}


		
	/**
	 * unir_documentos
	 *
	 * @param  mixed $archivos
	 * @return void
	 */
	public function unir_documentos($archivos = array())
	{
		$pdfs       = array();
		$limite     = 500;
		$lote = 0;
		$ii = 1;

		foreach ($archivos as $i => $archivo) {

			if (file_exists($archivo)) {
				$pdfs[$lote][$ii] = $archivo;

				if ($ii%$limite == 0) {
					$lote++;
				}	
			}

			$ii++;
		}

		if (!is_dir(APP . 'webroot' . DS. 'Pdf' .DS. 'Ubicacion' . DS . 'Masivo')) {
			@mkdir(APP . 'webroot' . DS. 'Pdf' .DS. 'Ubicacion' . DS . 'Masivo', 0775);
		}

		# Se procesan por Lotes de 500 documentos para no volcar la memoria
		foreach ($pdfs as $ip => $lote) {
			$pdf = new PDFMerger;
			foreach ($lote as $id => $document) {
				$pdf->addPDF($document, 'all');	
			}
			try {
				
				$pdfname = 'documentos-' . date('YmdHis') .'.pdf';

				$res = $pdf->merge('file', APP . 'webroot' . DS. 'Pdf' .DS. 'Ubicacion' . DS . 'Masivo' . DS . $pdfname);
				if ($res) {
					$resultados['result'][]['document'] = Router::url('/', true) . 'Pdf/Ubicacion/' . 'Masivo' . '/' . $pdfname;
				}

			} catch (Exception $e) {
				$resultados['errors']['messages'][] = $e->getMessage();
			}
		}

		return $resultados;
	}
}