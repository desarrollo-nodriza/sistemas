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
	 * @param  Array 	$data 	Datos que contendrá el gráfico
	 * @return Array    		Arreglo preparado para el gráfico
	 */
	public function admin_formatoGrafico($data) {

		// Armamos array por fechas, separando tiendas con su valor total en ventas
		$nuevoArray = array();
		foreach ($data as $key => $value) {
			$nuevoArray[$value['Fecha']][strtolower(Inflector::slug($value['tienda'],'_'))] = $value['Total'];
		}

		// Ordenamos el array para ser tomado en el javascript
		$nuevoArray2 = array();
		$count = 0;
		foreach ($nuevoArray as $key => $value) {
			// Agregamos el total de los comercios al arreglo
			$total = 0;

			$nuevoArray2[$count]['y'] = $key;
			foreach ($value as $key => $value) {
				$nuevoArray2[$count][$key] = $value;
				$total = $total + $value;
			}
			$nuevoArray2[$count]['total'] = $total;
			$count++;
		}

		return  $nuevoArray2;
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
		$suma = 0;
		foreach ($arrayElementos as $elemento) :
			$suma = $suma + $elemento[$indice];
		endforeach;
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
	public function admin_get_all_sales($f_inicio = null, $f_final = null, $group_by = '', $json = false) {
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
			'conditions' => array('Grafico.slug' => 'total_ventas_del_periodo'),
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
			case 'hora':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m-%d %H:00:00") AS Fecha';
				$group_by = 'GROUP BY HOUR(Orden.date_add) ORDER BY Orden.date_add ASC';
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
			case 'hora':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m-%d %H:00:00") AS Fecha';
				$group_by = 'GROUP BY HOUR(Orden.date_add) ORDER BY Orden.date_add ASC';
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
			case 'hora':
				$group_by_col = 'DATE_FORMAT(Orden.date_add, "%Y-%m-%d %H:00:00") AS Fecha';
				$group_by = 'GROUP BY HOUR(Orden.date_add) ORDER BY Orden.date_add ASC';
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

	public function admin_dashboard() {
		BreadcrumbComponent::add('');

		// Obtener ventas de los comercios
		$ventas = $this->admin_get_all_sales();
		// Obtener descuentos e los comercios
		$descuentos = $this->admin_get_all_discount();
		// Obtener pedidos de los comercios
		$pedidos = $this->admin_get_all_orders();


		// Obtener total de ventas de los comercios
		$sumaVentas = $this->admin_get_total_sum($ventas, 'Total');
		// Obtener el total de descuentos e los comercios
		$sumaDescuentos = $this->admin_get_total_sum($descuentos, 'Total');
		// Obtener el total de pedidos e los comercios
		$sumaPedidos = $this->admin_get_total_sum($pedidos, 'Total', false);
		
		$this->set(compact('sumaVentas','ventas', 'sumaDescuentos', 'descuentos', 'sumaPedidos', 'pedidos'));

	}

}
