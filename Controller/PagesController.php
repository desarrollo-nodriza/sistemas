<?php
App::uses('AppController', 'Controller');
class PagesController extends AppController
{
	public $name = 'Pages';
	public $uses = array();

	public function display()
	{
		$path	= func_get_args();
		$count	= count($path);
		if ( ! $count )
			$this->redirect('/');

		$page	= $subpage = $title_for_layout = null;

		if ( ! empty($path[0]) )
			$page = $path[0];

		if ( ! empty($path[1]) )
			$subpage = $path[1];

		if ( ! empty($path[$count - 1]) )
			$title_for_layout = Inflector::humanize($path[$count - 1]);

		$this->set(compact('page', 'subpage', 'title_for_layout'));
		$this->render(implode('/', $path));
	}

	/**
	 * Función que arma el arreglo con un formato especifico para los gráficos
	 * @param  Array 	$datos 	Datos que contendrá el gráfico
	 * @return Array    		Arreglo preparado para el gráfico
	 */
	public function admin_formatoGrafico($datos = array(), $indiceValor = '', $indiceNombre = '') {

		$res = array(
		#	'data', Formato [{y : '2001', a : '11122', b : '11222', c...}, {y : '2001', a : '11122', b : '11222', c...}]
		#	'colors', # Formato ['#2B40BC', '#4EAEEA', '#A479EF']
		#	'xkey', # Indica cual es el indice para el eje X: ej: y
		#	'ykeys', # Indica cual/es serán los indices valor ej: ['a', 'b', 'c'] 
		#	'labels', # Nombre para el/los valores del eje y ej: ['2019', '2211', 'Hola']
		#	'lineColors' # Formato ['#2B40BC', '#4EAEEA', '#A479EF']
		);
		
		$ventasCanales = array();
		foreach ($datos as $id => $dato) {

			if (empty($dato))
					continue;

			if (empty($dato['fecha']))
				continue;

			$ventasCanales[$dato['fecha']][$dato[$indiceNombre]] = $dato[$indiceValor];
			
		}

		# Armamos el json para la gráfica
		$res = array(
			'xkeys' => 'y'
		);
		if (!empty($ventasCanales)) {
			
			$pos = 0;
			foreach ($ventasCanales as $fecha => $canal) {
				if (empty($canal))
					continue;

				$res['data'][$pos]['y'] = $fecha;

				$posCanal = 0;
				foreach ($canal as $nombre => $cantidad) {
					$res['data'][$pos][abecedario($posCanal, true)] = $cantidad;
					$res['labels'][$posCanal] = $nombre;
					$res['ykeys'][$posCanal] = abecedario($posCanal, true);
					$res['lineColors'][$posCanal] = '#'.random_color();
					$posCanal++;		
				}	
				$pos++;
			}


		}

		return $res;

		# Armamos el json para la gráfica
		$res['xkey'] = 'y';

		if (!empty($datos)) {
			
			$pos = 0;
			foreach ($datos as $indice => $canal) {
				if (empty($canal))
					continue;

				if (empty($canal['fecha']))
					continue;

				$res['data'][$pos]['y']                    = $canal['fecha'];
				
				$res['labels'][$pos]                       = $canal[$indiceNombre];
				$res['ykeys'][$pos]                        = abecedario($pos, true);
				$res['lineColors'][$pos]                   = '#'.random_color();
				$pos++;
			}

		}
		
		return $res;
	}

	/**
	 * Obtiene una lista de las tiendas activas
	 * @return 		json 	Listado de  tiendas
	 */
	public function admin_get_shops_list () {
		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array('Tienda.activo' => 1),
			'fields' => array('Tienda.id', 'Tienda.nombre')));

		echo json_encode($tiendas);
		exit;
	}

	/**
	 * Calcula el monto total (descuentos, ventas, pedidos) de los comercios
	 * @param  Array 	$arrayElementos Array con la información de las tiendas
	 * @param  String 	$indice         Nombre del índice que contiene el valor a sumar
	 * @return String                 Monto sumado
	 */
	public function admin_get_total_sum( $arrayElementos, $indice , $money = true) {
		
		$suma = array_sum(Hash::extract($arrayElementos, sprintf('{n}.%s', $indice)));

		if ($money) {
			App::uses('CakeNumber', 'Utility');
			return CakeNumber::currency($suma, 'CLP');;
		}else{
			return $suma;
		}
		
	}


	/**
	 * Función que muestra las ventas de todas las tiendas registradas segun periodo de tiempo
	 * @param 	$f_inicio 	String 		Fecha inicial
	 * @param 	$_f_final 	String 		Fecha Final
	 * @param 	$json 		Boolean		Semáforo que determina si retornará un array o un json
	 * @return 	Array || Json
	 */
	public function admin_obtener_ventas( $f_inicio = null, $f_final = null, $group_by = null, $json = false)
	{
		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		# Obtenemos los prestashops
		$prestashops = Hash::extract(ClassRegistry::init('Tienda')->find('all', array(	
			'conditions' => array('Tienda.activo' => 1),
			'fields' => array(
				'Tienda.nombre AS canal_nombre',
				'Tienda.id AS canal_id',
			)
		)), '{n}.Tienda' );

		$prestashops = Hash::insert($prestashops, '{n}', array('tipo' => 'prestashop'));

		# Obtenemos los marketplaces
		$marketplaces = Hash::extract(ClassRegistry::init('Marketplace')->find('all', array(
			'conditions' => array(
				'Marketplace.activo' => 1
			),
			'fields' => array(
				'Marketplace.nombre AS canal_nombre',
				'Marketplace.id AS canal_id'
			)
		)), '{n}.Marketplace' );

		$marketplaces = Hash::insert($marketplaces, '{n}', array('tipo' => 'marketplace'));

		# Unificamos los canales de ventas
		$canales = array_merge_recursive($prestashops, $marketplaces);

		# Guardamos los resultados
		$ventas = array();

		# Obtenemos la venta por cada canal
		foreach ($canales as $ic => $canal) {
			
			$qry['joins'] = array(
					array(
						'table' => 'rp_venta_estados',
				        'alias' => 'VentaEstado',
				        'type' => 'INNER',
				        'conditions' => array(
				            'VentaEstado.id = Venta.venta_estado_id',
				        )
				    ),
				    array(
						'table' => 'rp_venta_estado_categorias',
				        'alias' => 'VentaEstadoCategoria',
				        'type' => 'INNER',
				        'conditions' => array(
				            'VentaEstadoCategoria.id = VentaEstado.venta_estado_categoria_id',
				            'VentaEstadoCategoria.venta' => 1
				        )
				    )
				);


			# Agrupar ventas por criterio
			$group_by_col = '';

			switch ($group_by) {
				case 'anno':
					
					$qry['fields'] = array(
						'DATE_FORMAT(Venta.fecha_venta, "%Y") AS Fecha',
						'ROUND(SUM(Venta.total)) As Total',
						'ROUND(SUM(Venta.descuento)) As Descuento',
						'ROUND(SUM(Venta.costo_envio)) As Transporte',
						'COUNT(Venta.id) As Cantidad'
					);

					$qry['group'] = 'YEAR(Venta.fecha_venta)';
					$qry['order'] = array('Venta.fecha_venta' => 'ASC');

					
					break;
				case 'mes':

					$qry['fields'] = array(
						'DATE_FORMAT(Venta.fecha_venta, "%Y-%m") AS Fecha',
						'ROUND(SUM(Venta.total)) As Total',
						'ROUND(SUM(Venta.descuento)) As Descuento',
						'ROUND(SUM(Venta.costo_envio)) As Transporte',
						'COUNT(Venta.id) As Cantidad'
					);

					$qry['group'] = 'MONTH(Venta.fecha_venta)';
					$qry['order'] = array('Venta.fecha_venta' => 'ASC');

					break;
				case 'dia':

					$qry['fields'] = array(
						'DATE_FORMAT(Venta.fecha_venta, "%Y-%m-%d") AS Fecha',
						'ROUND(SUM(Venta.total)) As Total',
						'ROUND(SUM(Venta.descuento)) As Descuento',
						'ROUND(SUM(Venta.costo_envio)) As Transporte',
						'COUNT(Venta.id) As Cantidad'
					);

					$qry['group'] = 'DAY(Venta.fecha_venta)';
					$qry['order'] = array('Venta.fecha_venta' => 'ASC');

					break;
				case 'hora':

					$qry['fields'] = array(
						'DATE_FORMAT(Venta.fecha_venta, "%Y-%m-%d %H:00:00") AS Fecha',
						'ROUND(SUM(Venta.total)) As Total',
						'ROUND(SUM(Venta.descuento)) As Descuento',
						'ROUND(SUM(Venta.costo_envio)) As Transporte',
						'COUNT(Venta.id) As Cantidad'
					);

					$qry['group'] = 'HOUR(Venta.fecha_venta)';
					$qry['order'] = array('Venta.fecha_venta' => 'ASC');

					break;
				
				default:
					$qry['fields'] = array(
						'DATE_FORMAT(Venta.fecha_venta, "%Y-%m") AS Fecha',
						'ROUND(SUM(Venta.total)) As Total',
						'ROUND(SUM(Venta.descuento)) As Descuento',
						'ROUND(SUM(Venta.costo_envio)) As Transporte',
						'COUNT(Venta.id) As Cantidad'
					);

					$qry['group'] = 'MONTH(Venta.fecha_venta)';
					$qry['order'] = array('Venta.fecha_venta' => 'ASC');
					break;
			}


			# condicionar segun canal de venta
			switch ($canal['tipo']) {
				case 'prestashop':
					
					$qry['conditions'] = array(
						'Venta.tienda_id' => $canal['canal_id'],
						'Venta.marketplace_id' => null,
						'Venta.fecha_venta BETWEEN ? AND ?' => array($f_inicio, $f_final)
					);
					
					break;
				
				case 'marketplace':

					$qry['conditions'] = array(
						'Venta.marketplace_id' => $canal['canal_id'],
						'Venta.fecha_venta BETWEEN ? AND ?' => array($f_inicio, $f_final)
					);
					
					break;
			}
			
			$venta = ClassRegistry::init('Venta')->find('all', $qry);

			if (empty($venta)) {
				
				$venta[0][0]['Total']      = 0;
				$venta[0][0]['Fecha']      = null;
				$venta[0][0]['Cantidad']   = 0;
				$venta[0][0]['Descuento']  = 0;
				$venta[0][0]['Transporte'] = 0;

			}

			foreach ($venta as $iv => $v) {
				$ventas[$ic.$iv]['tienda']     = $canal['canal_nombre'];
				$ventas[$ic.$iv]['total']      = $v[0]['Total'];
				$ventas[$ic.$iv]['fecha']      = $v[0]['Fecha'];;
				$ventas[$ic.$iv]['cantidad']   = $v[0]['Cantidad'];;
				$ventas[$ic.$iv]['descuento']  = $v[0]['Descuento'];;
				$ventas[$ic.$iv]['transporte'] = $v[0]['Transporte'];;
			}
		}
		
		if ($json) {
			echo json_encode($this->admin_formatoGrafico($ventas, 'total', 'tienda'));
			exit;
		}else{ 
			return $ventas;
		}	

	}



	/**
	 * Función que muestra la cantidad de ventas de todas las tiendas registradas segun periodo de tiempo
	 * @param 	$f_inicio 	String 		Fecha inicial
	 * @param 	$_f_final 	String 		Fecha Final
	 * @param 	$json 		Boolean		Semáforo que determina si retornará un array o un json
	 * @return 	Array || Json
	 */
	public function admin_cantidad_ventas( $f_inicio = null, $f_final = null, $group_by = null, $json = false)
	{
		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		$canales = $this->admin_obtener_ventas($f_inicio, $f_final, $group_by);
		
		$resultado = array();	

		foreach ($canales as $ic => $canal) {
			$resultado[$ic]['tienda'] = $canal['tienda'];
			$resultado[$ic]['fecha'] = $canal['fecha'];
			$resultado[$ic]['cantidad'] = $canal['cantidad'];
		}
		
		if ($json) {
			echo json_encode($this->admin_formatoGrafico($resultado, 'cantidad', 'tienda'));
			exit;
		}else{ 
			return $resultado;
		}	

	}



	public function admin_obtener_descuentos($f_inicio = null, $f_final = null, $group_by = '', $json = false)
	{
		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		$canales = $this->admin_obtener_ventas($f_inicio, $f_final, $group_by);
		
		$resultado = array();	

		foreach ($canales as $ic => $canal) {
			$resultado[$ic]['tienda'] = $canal['tienda'];
			$resultado[$ic]['fecha'] = $canal['fecha'];
			$resultado[$ic]['descuento'] = $canal['descuento'];
		}
		
		if ($json) {
			echo json_encode($this->admin_formatoGrafico($resultado, 'descuento', 'tienda'));
			exit;
		}else{ 
			return $resultado;
		}
	}



	public function admin_obtener_transporte($f_inicio = null, $f_final = null, $group_by = '', $json = false)
	{
		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		$canales = $this->admin_obtener_ventas($f_inicio, $f_final, $group_by);
		
		$resultado = array();	

		foreach ($canales as $ic => $canal) {
			$resultado[$ic]['tienda'] = $canal['tienda'];
			$resultado[$ic]['fecha'] = $canal['fecha'];
			$resultado[$ic]['transporte'] = $canal['transporte'];
		}
		
		if ($json) {
			echo json_encode($this->admin_formatoGrafico($resultado, 'transporte', 'tienda'));
			exit;
		}else{ 
			return $resultado;
		}
	}



	/**
	 * Función que otiene los descuentos de todas las tiendas registradas segun periodo de tiempo
	 * @param 	$f_inicio 	String 		Fecha inicial
	 * @param 	$_f_final 	String 		Fecha Final
	 * @param 	$json 		Boolean		Semáforo que determina si retornará un array o un json
	 * @return 	Array || Json
	 */
	public function admin_get_all_discount ($f_inicio = null, $f_final = null, $group_by = '', $json = false) {
		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		//Normalizar fechas
		$f_inicio = sprintf("'%s'", $f_inicio);
		$f_final = sprintf("'%s'", $f_final);

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array('activo' => 1)
			));

		// Aloja toda la info retornada de las queries
		$arrayResultado = array();
		$arrayQuery = null;

		// La query
		$query = ClassRegistry::init('Grafico')->find('first', array(
			'conditions' => array('Grafico.slug' => 'total_descuentos_del_periodo'),
			'fields' => array('Grafico.descipcion')
			));

		if (empty($query)) {
			return;
		}

		// Agrupar
		$group_by_col = '';

		
		switch ($group_by) {
			case 'anno':
				$group_by_col = 'DATE_FORMAT(Venta.fecha_venta, "%Y") AS Fecha';
				$group_by = 'GROUP BY YEAR(Venta.fecha_venta) ORDER BY Venta.fecha_venta ASC';
				break;
			case 'mes':
				$group_by_col = 'DATE_FORMAT(Venta.fecha_venta, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Venta.fecha_venta) ORDER BY Venta.fecha_venta ASC';
				break;
			case 'dia':
				$group_by_col = 'DATE_FORMAT(Venta.fecha_venta, "%Y-%m-%d") AS Fecha';
				$group_by = 'GROUP BY DAY(Venta.fecha_venta) ORDER BY Venta.fecha_venta ASC';
				break;
			case 'hora':
				$group_by_col = 'DATE_FORMAT(Venta.fecha_venta, "%Y-%m-%d %H:00:00") AS Fecha';
				$group_by = 'GROUP BY HOUR(Venta.fecha_venta) ORDER BY Venta.fecha_venta ASC';
				break;
			
			default:
				$group_by_col = 'DATE_FORMAT(Venta.fecha_venta, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Venta.fecha_venta) ORDER BY Venta.fecha_venta ASC';
				break;
		}
		

		// Rango de fechas
		$query['Grafico']['descipcion'] = str_replace('[*START_DATE*]', $f_inicio, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*FINISH_DATE*]', $f_final, $query['Grafico']['descipcion']);
		// campo group
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY_COL*]', $group_by_col, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY*]', $group_by, $query['Grafico']['descipcion']);


		// Armamos la query por tiendas
		foreach ($tiendas as $indice => $tienda) :
			$arrayQuery = str_replace('[*PREFIX*]', $tienda['Tienda']['prefijo'], $query['Grafico']['descipcion']);
			
			// Sobreescribimos la configuración de la base de datos a utilizar
			///ClassRegistry::init('Venta')->useDbConfig = $tienda['Tienda']['configuracion'];
			$OCPagadas = ClassRegistry::init('Venta');
			try {
				$arrayResultado[$indice] = $OCPagadas->query($arrayQuery);
				foreach ($arrayResultado[$indice] as $indx => $val) {
					$arrayResultado[$indice][$indx][0]['tienda'] = $tienda['Tienda']['nombre'];
				}
			} catch (Exception $e) {
				$arrayResultado[$indice] = $e->getMessage();
			}
		endforeach;
		
		$arrayResultado = Hash::extract($arrayResultado, '{n}.{n}.{n}');
		if ($json) {
			echo json_encode($this->admin_formatoGrafico($arrayResultado));
			exit;
		}else{
			return $arrayResultado;
		}	
	}

	/**
	 * Función que otiene los descuentos de todas las tiendas registradas segun periodo de tiempo
	 * @param 	$f_inicio 	String 		Fecha inicial
	 * @param 	$_f_final 	String 		Fecha Final
	 * @param 	$json 		Boolean		Semáforo que determina si retornará un array o un json
	 * @return 	Array || Json
	 */
	public function admin_get_all_orders ($f_inicio = null, $f_final = null, $group_by = '', $json = false) {
		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		//Normalizar fechas
		$f_inicio = sprintf("'%s'", $f_inicio);
		$f_final = sprintf("'%s'", $f_final);

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array('activo' => 1)
			));

		// Aloja toda la info retornada de las queries
		$arrayResultado = array();
		$arrayQuery = null;

		// La query
		$query = ClassRegistry::init('Grafico')->find('first', array(
			'conditions' => array('Grafico.slug' => 'pedidos_del_periodo'),
			'fields' => array('Grafico.descipcion')
			));

		if (empty($query)) {
			return;
		}

		// Agrupar
		$group_by_col = '';

		
		switch ($group_by) {
			case 'anno':
				$group_by_col = 'DATE_FORMAT(Venta.fecha_venta, "%Y") AS Fecha';
				$group_by = 'GROUP BY YEAR(Venta.fecha_venta) ORDER BY Venta.fecha_venta ASC';
				break;
			case 'mes':
				$group_by_col = 'DATE_FORMAT(Venta.fecha_venta, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Venta.fecha_venta) ORDER BY Venta.fecha_venta ASC';
				break;
			case 'dia':
				$group_by_col = 'DATE_FORMAT(Venta.fecha_venta, "%Y-%m-%d") AS Fecha';
				$group_by = 'GROUP BY DAY(Venta.fecha_venta) ORDER BY Venta.fecha_venta ASC';
				break;
			case 'hora':
				$group_by_col = 'DATE_FORMAT(Venta.fecha_venta, "%Y-%m-%d %H:00:00") AS Fecha';
				$group_by = 'GROUP BY HOUR(Venta.fecha_venta) ORDER BY Venta.fecha_venta ASC';
				break;
			
			default:
				$group_by_col = 'DATE_FORMAT(Venta.fecha_venta, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Venta.fecha_venta) ORDER BY Venta.fecha_venta ASC';
				break;
		}
		

		// Rango de fechas
		$query['Grafico']['descipcion'] = str_replace('[*START_DATE*]', $f_inicio, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*FINISH_DATE*]', $f_final, $query['Grafico']['descipcion']);
		// campo group
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY_COL*]', $group_by_col, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY*]', $group_by, $query['Grafico']['descipcion']);

		// Armamos la query por tiendas
		foreach ($tiendas as $indice => $tienda) :
			$arrayQuery = str_replace('[*PREFIX*]', $tienda['Tienda']['prefijo'], $query['Grafico']['descipcion']);
			
			// Sobreescribimos la configuración de la base de datos a utilizar
			//ClassRegistry::init('Orders')->useDbConfig = $tienda['Tienda']['configuracion'];
			$OCPagadas = ClassRegistry::init('Venta');
			try {
				$arrayResultado[$indice] = $OCPagadas->query($arrayQuery); 
				foreach ($arrayResultado[$indice] as $indx => $val) {
					$arrayResultado[$indice][$indx][0]['tienda'] = $tienda['Tienda']['nombre'];
				}
			} catch (Exception $e) {
				$arrayResultado[$indice] = $e->getMessage();
			}

		endforeach;
	
		$arrayResultado = Hash::extract($arrayResultado, '{n}.{n}.{n}');
		if ($json) {
			echo json_encode($this->admin_formatoGrafico($arrayResultado));
			exit;
		}else{
			return $arrayResultado;
		}	
	}

	/**
	 * Funciona que obtiene y prepara le información de los productos vendidos para ser mostrados en el gráfico
	 * @param  String  	$f_inicio 		Fecha inicial
	 * @param  String  	$f_final  		Fecha final
	 * @param  Integer  $tienda   		Identificador de la tienda
	 * @param  boolean $json     		Semaforo json
	 * @param  string  $group_by 		Condicion de GROUP
	 * @param  integer $limite   		Cantidad de elemenos a obtener
	 * @return Array || Json
	 */
	public function admin_top_products ($f_inicio = null, $f_final = null, $tienda = null, $json = true, $group_by = '', $limite = 0) {
		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		//Normalizar fechas
		$f_inicio = sprintf("'%s'", $f_inicio);
		$f_final = sprintf("'%s'", $f_final);

		if (is_null($tienda) || empty($tienda)) {
			return false;
		}

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array('activo' => 1, 'id' => $tienda)
			));

		// Aloja toda la info retornada de las queries
		$arrayResultado = array();
		$arrayQuery = null;

		// La query
		$query = ClassRegistry::init('Grafico')->find('first', array(
			'conditions' => array('Grafico.slug' => 'top_productos'),
			'fields' => array('Grafico.descipcion')
			));

		if (empty($query)) {
			return;
		}

		// Agrupar
		$group_by_col = '';
		
		switch ($group_by) {
			case 'anno':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y") AS Fecha';
				$group_by = 'GROUP BY YEAR(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			case 'mes':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			case 'dia':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m-%d") AS Fecha';
				$group_by = 'GROUP BY DAY(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			
			default:
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
		}
		

		// Rango de fechas
		$query['Grafico']['descipcion'] = str_replace('[*START_DATE*]', $f_inicio, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*FINISH_DATE*]', $f_final, $query['Grafico']['descipcion']);
		// campo group
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY_COL*]', $group_by_col, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY*]', $group_by, $query['Grafico']['descipcion']);

		// Cantidad de elementos
		if ($limite > 0 && is_integer($limite)) {
			$query['Grafico']['descipcion'] = str_replace('[*LIMIT*]', sprintf('LIMIT %s', $limite), $query['Grafico']['descipcion']);
		}else{
			$query['Grafico']['descipcion'] = str_replace('[*LIMIT*]', '', $query['Grafico']['descipcion']);
		}



		// Armamos la query por tiendas
		foreach ($tiendas as $indice => $tienda) :
			$arrayQuery = str_replace('[*PREFIX*]', $tienda['Tienda']['prefijo'], $query['Grafico']['descipcion']);
			
			// Sobreescribimos la configuración de la base de datos a utilizar
			ClassRegistry::init('Orders')->useDbConfig = $tienda['Tienda']['configuracion'];
			$OCPagadas = ClassRegistry::init('Orders');
			try {
				$arrayResultado[$indice] = $OCPagadas->query($arrayQuery);
				foreach ($arrayResultado[$indice] as $indx => $val) {
					$arrayResultado[$indice][$indx][0]['tienda'] = $tienda['Tienda']['nombre'];
				}
			} catch (Exception $e) {
				$arrayResultado[$indice] = $e->getMessage();
			}
		endforeach;

		$arrayResultado = Hash::extract($arrayResultado, '{n}.{n}');

		if ($json) {
			echo json_encode($arrayResultado);
			exit;
		}else{
			return $arrayResultado;
		}
	}

	/**
	 * Funciona que obtiene y prepara le información de las marcas con productos mas vendidos para ser mostrados en el gráfico
	 * @param  String  	$f_inicio 		Fecha inicial
	 * @param  String  	$f_final  		Fecha final
	 * @param  Integer  $tienda   		Identificador de la tienda
	 * @param  boolean $json     		Semaforo json
	 * @param  string  $group_by 		Condicion de GROUP
	 * @param  integer $limite   		Cantidad de elemenos a obtener
	 * @return Array || Json
	 */
	public function admin_top_brands ($f_inicio = null, $f_final = null, $tienda = null, $json = true, $group_by = '', $limite = 0) {
		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		//Normalizar fechas
		$f_inicio = sprintf("'%s'", $f_inicio);
		$f_final = sprintf("'%s'", $f_final);

		if (is_null($tienda) || empty($tienda)) {
			return false;
		}

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array('activo' => 1, 'id' => $tienda)
			));

		// Aloja toda la info retornada de las queries
		$arrayResultado = array();
		$arrayQuery = null;

		// La query
		$query = ClassRegistry::init('Grafico')->find('first', array(
			'conditions' => array('Grafico.slug' => 'top_marcas'),
			'fields' => array('Grafico.descipcion')
			));

		if (empty($query)) {
			return;
		}

		// Agrupar
		$group_by_col = '';
		
		switch ($group_by) {
			case 'anno':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y") AS Fecha';
				$group_by = 'GROUP BY YEAR(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			case 'mes':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			case 'dia':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m-%d") AS Fecha';
				$group_by = 'GROUP BY DAY(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			
			default:
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
		}
		

		// Rango de fechas
		$query['Grafico']['descipcion'] = str_replace('[*START_DATE*]', $f_inicio, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*FINISH_DATE*]', $f_final, $query['Grafico']['descipcion']);
		// campo group
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY_COL*]', $group_by_col, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY*]', $group_by, $query['Grafico']['descipcion']);

		// Cantidad de elementos
		if ($limite > 0 && is_integer($limite)) {
			$query['Grafico']['descipcion'] = str_replace('[*LIMIT*]', sprintf('LIMIT %s', $limite), $query['Grafico']['descipcion']);
		}else{
			$query['Grafico']['descipcion'] = str_replace('[*LIMIT*]', '', $query['Grafico']['descipcion']);
		}

		// Armamos la query por tiendas
		foreach ($tiendas as $indice => $tienda) :
			$arrayQuery = str_replace('[*PREFIX*]', $tienda['Tienda']['prefijo'], $query['Grafico']['descipcion']);
			
			// Sobreescribimos la configuración de la base de datos a utilizar
			ClassRegistry::init('Orders')->useDbConfig = $tienda['Tienda']['configuracion'];
			$OCPagadas = ClassRegistry::init('Orders');
			try {
				$arrayResultado[$indice] = $OCPagadas->query($arrayQuery);
				foreach ($arrayResultado[$indice] as $indx => $val) {
					$arrayResultado[$indice][$indx][0]['tienda'] = $tienda['Tienda']['nombre'];
				}
			} catch (Exception $e) {
				$arrayResultado[$indice] = $e->getMessage();
			}
		endforeach;

		$arrayResultado = Hash::extract($arrayResultado, '{n}.{n}');


		if ($json) {
			echo json_encode($arrayResultado);
			exit;
		}else{
			return $arrayResultado;
		}
	}

	/**
	 * Funciona que obtiene y prepara le información de las marcas con productos mas vendidos para ser mostrados en el gráfico
	 * @param  String  	$f_inicio 		Fecha inicial
	 * @param  String  	$f_final  		Fecha final
	 * @param  Integer  $tienda   		Identificador de la tienda
	 * @param  boolean $json     		Semaforo json
	 * @param  string  $group_by 		Condicion de GROUP
	 * @param  integer $limite   		Cantidad de elemenos a obtener
	 * @return Array || Json
	 */
	public function admin_top_customers ($f_inicio = null, $f_final = null, $tienda = null, $json = true, $group_by = '', $limite = 0) {
		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		//Normalizar fechas
		$f_inicio = sprintf("'%s'", $f_inicio);
		$f_final = sprintf("'%s'", $f_final);

		if (is_null($tienda) || empty($tienda)) {
			return false;
		}

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array('activo' => 1, 'id' => $tienda)
			));

		// Aloja toda la info retornada de las queries
		$arrayResultado = array();
		$arrayQuery = null;

		// La query
		$query = ClassRegistry::init('Grafico')->find('first', array(
			'conditions' => array('Grafico.slug' => 'top_clientes'),
			'fields' => array('Grafico.descipcion')
			));

		if (empty($query)) {
			return;
		}

		// Agrupar
		$group_by_col = '';
		
		switch ($group_by) {
			case 'anno':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y") AS Fecha';
				$group_by = 'GROUP BY YEAR(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			case 'mes':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			case 'dia':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m-%d") AS Fecha';
				$group_by = 'GROUP BY DAY(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			
			default:
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
		}
		

		// Rango de fechas
		$query['Grafico']['descipcion'] = str_replace('[*START_DATE*]', $f_inicio, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*FINISH_DATE*]', $f_final, $query['Grafico']['descipcion']);
		// campo group
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY_COL*]', $group_by_col, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY*]', $group_by, $query['Grafico']['descipcion']);

		// Cantidad de elementos
		if ($limite > 0 && is_integer($limite)) {
			$query['Grafico']['descipcion'] = str_replace('[*LIMIT*]', sprintf('LIMIT %s', $limite), $query['Grafico']['descipcion']);
		}else{
			$query['Grafico']['descipcion'] = str_replace('[*LIMIT*]', '', $query['Grafico']['descipcion']);
		}

		// Armamos la query por tiendas
		foreach ($tiendas as $indice => $tienda) :
			$arrayQuery = str_replace('[*PREFIX*]', $tienda['Tienda']['prefijo'], $query['Grafico']['descipcion']);
			
			// Sobreescribimos la configuración de la base de datos a utilizar
			ClassRegistry::init('Orders')->useDbConfig = $tienda['Tienda']['configuracion'];
			$OCPagadas = ClassRegistry::init('Orders');
			try {
				$arrayResultado[$indice] = $OCPagadas->query($arrayQuery);
				foreach ($arrayResultado[$indice] as $indx => $val) {
					$arrayResultado[$indice][$indx][0]['tienda'] = $tienda['Tienda']['nombre'];
				}
			} catch (Exception $e) {
				$arrayResultado[$indice] = $e->getMessage();
			}
		endforeach;

		$arrayResultado = Hash::extract($arrayResultado, '{n}.{n}.{n}');

		if ($json) {
			echo json_encode($arrayResultado);
			exit;
		}else{
			return $arrayResultado;
		}
	}

	/**
	 * Función que crea una array con el ticket promedio según los 
	 * parámetros de ventas y pedidos
	 * @param  array  $ventas  Ventas total de cada comercio
	 * @param  array  $pedidos Pedidos total de cada comercio
	 * @return array          Ticket promedio por comercio
	 */
	public function admin_tickets($ventas = array()) {
		
		$promedio = array();

		foreach ($ventas as $canal => $venta) {
			$promedio[$canal]['tienda'] = $venta['tienda'];
			$promedio[$canal]['total'] = ($venta['cantidad'] > 0) ? $venta['total'] / $venta['cantidad'] : 0;	
		}
		return $promedio;
	}


	public function admin_sales_by_brands ($f_inicio = null, $f_final = null, $tienda = '', $tabla = false, $group_by = '', $limite = 0) {
		if (is_null($f_inicio) || is_null($f_final) || empty($f_inicio) || empty($f_final)) {
			$f_inicio = date('Y-m-01 00:00:00');
			$f_final = date('Y-m-t 23:59:59');
		}else{
			$f_inicio = sprintf('%s 00:00:00', $f_inicio);
			$f_final = sprintf('%s 23:59:59', $f_final);
		}

		//Normalizar fechas
		$f_inicio = sprintf("'%s'", $f_inicio);
		$f_final = sprintf("'%s'", $f_final);

		if (is_null($tienda) || empty($tienda)) {
			return false;
		}

		$tiendas = ClassRegistry::init('Tienda')->find('all', array(
			'conditions' => array('activo' => 1, 'id' => $tienda)
			));

		// Aloja toda la info retornada de las queries
		$arrayResultado = array();
		$arrayQuery = null;

		// La query
		$query = ClassRegistry::init('Grafico')->find('first', array(
			'conditions' => array('Grafico.slug' => 'tabla_marcas'),
			'fields' => array('Grafico.descipcion')
			));

		if (empty($query)) {
			return;
		}

		// Agrupar
		$group_by_col = '';
		
		switch ($group_by) {
			case 'anno':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y") AS Fecha';
				$group_by = 'GROUP BY YEAR(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			case 'mes':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			case 'dia':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m-%d") AS Fecha';
				$group_by = 'GROUP BY DAY(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
			
			default:
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m") AS Fecha';
				$group_by = 'GROUP BY MONTH(Orden.date_add) ORDER BY Orden.date_add ASC';
				break;
		}

		// Rango de fechas
		$query['Grafico']['descipcion'] = str_replace('[*START_DATE*]', $f_inicio, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*FINISH_DATE*]', $f_final, $query['Grafico']['descipcion']);
		// campo group
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY_COL*]', $group_by_col, $query['Grafico']['descipcion']);
		$query['Grafico']['descipcion'] = str_replace('[*GROUP_BY*]', $group_by, $query['Grafico']['descipcion']);

		// Cantidad de elementos
		if ($limite > 0 && is_integer($limite)) {
			$query['Grafico']['descipcion'] = str_replace('[*LIMIT*]', sprintf('LIMIT %s', $limite), $query['Grafico']['descipcion']);
		}else{
			$query['Grafico']['descipcion'] = str_replace('[*LIMIT*]', '', $query['Grafico']['descipcion']);
		}

		// Armamos la query por tiendas
		foreach ($tiendas as $indice => $tienda) :
			$arrayQuery = str_replace('[*PREFIX*]', $tienda['Tienda']['prefijo'], $query['Grafico']['descipcion']);
			
			// Sobreescribimos la configuración de la base de datos a utilizar
			ClassRegistry::init('Orders')->useDbConfig = $tienda['Tienda']['configuracion'];
			$OCPagadas = ClassRegistry::init('Orders');
			try {
				$arrayResultado[$indice] = $OCPagadas->query($arrayQuery);
				foreach ($arrayResultado[$indice] as $indx => $val) {
					$arrayResultado[$indice][$indx][0]['tienda'] = $tienda['Tienda']['nombre'];
				}
			} catch (Exception $e) {
				$arrayResultado[$indice] = $e->getMessage();
			}
		endforeach;

		$arrayResultado = Hash::extract($arrayResultado, '{n}.{n}');
		
		if ($tabla) {
			$tablaHtml = '';
			$totalVendidoMarcas = 0; $totalCantidadVendido = 0; $totalPorcentaje = 0; $descuentos = 0; $despachos = 0;

			foreach ($arrayResultado as $linea) {
				$tablaHtml.= '<tr>';
				$tablaHtml.= '<td>' . $linea['Fabricante']['Marca'] . '</td>';
				$tablaHtml.= '<td>' . $linea[0]['Total'] . '%</td>';
				$tablaHtml.= '<td>' . $linea[0]['Cantidad'] . '</td>';
				$tablaHtml.= '<td>' . CakeNumber::currency($linea[0]['PrecioVenta'], 'CLP') . '</td>';
				$tablaHtml.= '</tr>';

				$totalVendidoMarcas = $totalVendidoMarcas + $linea[0]['PrecioVenta']; 
				$totalCantidadVendido = $totalCantidadVendido + $linea[0]['Cantidad']; 
				$totalPorcentaje = $totalPorcentaje + $linea[0]['Total'];
				$descuentos = $linea[0]['Descuentos'];
				$despachos = $linea[0]['Despachos'];
			}

			$tablaHtml .= '<tr><td><b>Totales</b></td><td><b>'. $totalPorcentaje .'%<b></td><td><b>' . $totalCantidadVendido . '</b></td><td><b>' . CakeNumber::currency($totalVendidoMarcas, 'CLP') . '</b></td></tr>';
			$tablaHtml .= '<tr><td colspan="3"><b>Descuentos</b></td><td><b>- ' . CakeNumber::currency($descuentos, 'CLP') . '<b></td></tr>';
			$tablaHtml .= '<tr><td colspan="3"><b>Despachos</b></td><td><b>+ ' . CakeNumber::currency($despachos, 'CLP') . '<b></td></tr>';
			$tablaHtml .= '<tr><td colspan="3"><b>Total Ventas</b></td><td><b>' . CakeNumber::currency(($totalVendidoMarcas - $descuentos + $despachos), 'CLP') . '</b></td></tr>';
			echo $tablaHtml;
			exit;
		}else{
			return $arrayResultado;
		}
	}


	public function admin_obtener_resultados_prisync()
	{
		$productos = ClassRegistry::init('PrisyncProducto')->find('all', array(
			'contain' => array(
				'PrisyncRuta'
			)
		));

		if (empty($productos)) {
			$response = array(
				'code'    => 200,
				'message' => 'No se encontraron productos',
				'value'   => 0
			);

			#echo json_encode($response);
			return array();
			exit;
		}

		$resultados		= array();
		$productosAlta  = array();
		$productosBaja  = array();
		$productosIgual = array();
		$contAlta       = 0;
		$contBaja       = 0;
		$contIgual      = 0;

		$competidores = array();
		$micompania = 'toolmania';

		foreach ($productos as $ip => $producto) {
			foreach ($producto['PrisyncRuta'] as $ir => $competidor) {
				$url      = parse_url($competidor['url']);
				$compania = explode('.', str_replace('www.', '', $url['host']));
				
				$competidores[$ip][$compania[0]]['url']          = $compania[0];
				$competidores[$ip][$compania[0]]['product_id'] 	= $producto['PrisyncProducto']['id'];
				$competidores[$ip][$compania[0]]['product_name'] = $producto['PrisyncProducto']['name'];
				$competidores[$ip][$compania[0]]['product_code'] = $producto['PrisyncProducto']['internal_code'];
				$competidores[$ip][$compania[0]]['price']        = $competidor['price'];
				
			}	
		}


		if (!empty($competidores)) {
			
			$base = (int) 0;
			
			foreach ($competidores as $ic => $competidor) {

				if (array_key_exists($micompania, $competidor)) {
					
					$base = $competidor[$micompania]['price'];

					foreach ($competidor as $ico => $comp) {
						if ($ico != $micompania) {
							if ($comp['price'] > $base) {
								$contBaja                    = $contBaja + 1;
								$resultados[$ico]['Alto']['total'] = $contBaja;		
							}

							if ($comp['price'] < $base) {
								$contAlta                    = $contAlta + 1;
								$resultados[$ico]['Bajo']['total'] = $contAlta;		
							}

							if ($comp['price'] == $base) {
								$contIgual                    = $contIgual + 1;
								$resultados[$ico]['Igual']['total'] = $contIgual;		
							}
						}
					}
				}
							
			}
			
		}

		$response = array(
			'code'    => 200,
			'message' => 'Resultados de la operación',
			'value'   => $resultados
		);

		#echo json_encode($response);
		return $resultados;	
		exit;
	}


	public function admin_dashboard() {
		BreadcrumbComponent::add('Dashboard');

		// Obtener ventas de los comercios
		#$ventas = $this->admin_get_all_sales(); 
		$ventas = $this->admin_obtener_ventas();

		$metricas_oc = ClassRegistry::init('OrdenCompra')->obtener_metricas();

		// Obtener descuentos e los comercios
		$descuentos = $this->admin_get_all_discount();
		// Obtener pedidos de los comercios
		$pedidos = $this->admin_cantidad_ventas();
		// Calculamos Tickets promedios
		$tickets = $this->admin_tickets($ventas);
		// Tabla de ventas por fabricante
		$tablaMarcas = $this->admin_sales_by_brands('','', $this->Session->read('Tienda.id') );

		$tablaMarcas = array();

		// Obtener total de ventas de los comercios
		$sumaVentas = $this->admin_get_total_sum($ventas, 'total');
		// Obtener el total de descuentos e los comercios
		$sumaDescuentos = $this->admin_get_total_sum($descuentos, 'descuento');
		// Obtener el total de pedidos e los comercios
		$sumaPedidos = $this->admin_get_total_sum($pedidos, 'cantidad', false);
		// Prisync
		$prisync = $this->admin_obtener_resultados_prisync();
		
		$this->set(compact('sumaVentas','ventas', 'sumaDescuentos', 'descuentos', 'sumaPedidos', 'pedidos', 'tickets', 'tablaMarcas', 'prisync', 'metricas_oc'));

	}

}
