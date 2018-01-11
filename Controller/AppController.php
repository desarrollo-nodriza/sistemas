<?php
App::uses('Controller', 'Controller');
//App::uses('FB', 'Facebook.Lib');
class AppController extends Controller
{
	public $helpers		= array(
		'Session', 'Html', 'Form', 'PhpExcel'
		//, 'Facebook.Facebook'
	);
	public $components	= array(
		'Session',
		'Auth'		=> array(
			'loginAction'		=> array('controller' => 'administradores', 'action' => 'login', 'admin' => true),
			'loginRedirect'		=> '/',
			'logoutRedirect'	=> '/',
			'authError'			=> 'No tienes permisos para entrar a esta sección.',
			'authenticate'		=> array(
				'Form'				=> array(
					'userModel'			=> 'Usuario',
					'fields'			=> array(
						'username'			=> 'email',
						'password'			=> 'clave'
					)
				)
			)
		),
		'Google'		=> array(
			'applicationName'		=> 'Newsletter Nodriza',
			'developerKey'			=> 'cristian.rojas@nodriza.cl',
			'clientId'				=> '1376469050-ckai861jm571qcguj2ohgepgb605uu2l.apps.googleusercontent.com',
			'clientSecret'			=> 'Kfmh_BoEMaD6nbMHSfA8CEyW',
			//'redirectUri'			=> Router::url(array('controller' => 'administradores', 'action' => 'google', 'admin' => false), true)),
			'approvalPrompt'		=> 'auto',
			'accessType'			=> null,//'offline',
			'scopes'				=> array('profile', 'email')
		),
		'DebugKit.Toolbar',
		'RequestHandler',
		'Breadcrumb' => array(
			'crumbs'		=> array(
				array('', null),
				array('Inicio', '/'),
			)
		),
		'Chilexpress.GeoReferencia',
		'Chilexpress.Tarificacion',
		'Chilexpress.Ot'
		//'Facebook.Connect'	=> array('model' => 'Usuario'),
		//'Facebook'
	);

	public function beforeFilter()
	{	

		# Geo rferencia
		#prx($this->ejemploGeolocalizacionChilexpress());


		#Tarificacion
		# prx($this->ejemploTarificacion());
		# 
		# OT

		/*try {
			$resultado = $this->Ot->generarOt(3,
			3,
			'RENCA',
			22106942,
			'123456789',
			'Compra1',
			10000,
			0,
			'Mario Moyano',
			'mmoyano@chilexpress.cl',
			'84642291',
			'Alexis Erazo',
			'aerazo@chilexpress.cl',
			'84642291',
			'PENALOLEN',
			'Camino de las Camelias',
			'7909',
			'Casa 33',
			'PUDAHUEL',
			'Jose Joaquin Perez',
			'1376',
			'Piso 2',
			5,
			1,
			1,
			1);
		} catch (Exception $e) {
			$resultado = $e;
		}*/
		
		#prx($resultado);
		/**
		 * Layout administracion y permisos publicos
		 */
		if ( ! empty($this->request->params['admin']) )
		{
			$this->layoutPath				= 'backend';
			AuthComponent::$sessionKey		= 'Auth.Administrador';
			$this->Auth->authenticate['Form']['userModel']		= 'Administrador';
		}
		else
		{
			AuthComponent::$sessionKey	= 'Auth.Usuario';
			$this->Auth->allow();
		}

		/**
		 * OAuth Google
		 */
		$this->Google->cliente->setRedirectUri(Router::url(array('controller' => 'administradores', 'action' => 'login'), true));
		$this->Google->oauth();
		
		if ( ! empty($this->request->query['code']) && $this->request->params['controller'] == 'administradores' && $this->request->params['action'] == 'admin_login')
		{
			$this->Google->oauth->authenticate($this->request->query['code']);
			$this->Session->write('Google', array(
				'code'		=> $this->request->query['code'],
				'token'		=> $this->Google->oauth->getAccessToken()
			));
		}

		if ( $this->Session->check('Google.token') )
		{
			$this->Google->cliente->setAccessToken($this->Session->read('Google.token'));
		}

		/**
		 * Logout FB
		 */
		/*
		if ( ! isset($this->request->params['admin']) && ! $this->Connect->user() && $this->Auth->user() )
			$this->Auth->logout();
		*/

		/**
		 * Detector cliente local
		 */
		$this->request->addDetector('localip', array(
			'env'			=> 'REMOTE_ADDR',
			'options'		=> array('::1', '127.0.0.1'))
		);

		/**
		 * Detector entrada via iframe FB
		 */
		$this->request->addDetector('iframefb', array(
			'env'			=> 'HTTP_REFERER',
			'pattern'		=> '/facebook\.com/i'
		));

		/**
		 * Cookies IE
		 */
		header('P3P:CP="IDC DSP COR ADM DEVi TAIi PSA PSD IVAi IVDi CONi HIS OUR IND CNT"');

		/**
		 * Cambiar tienda
		 */ 
		$this->cambioTienda();

		// Configuración de tablas externas
		$this->cambiarConfigDB($this->tiendaConf($this->Session->read('Tienda.id')));

	}

	/**
	 * Guarda el usuario Facebook
	 */
	public function beforeFacebookSave()
	{
		if ( ! isset($this->request->params['admin']) )
		{
			$this->Connect->authUser['Usuario']		= array_merge(array(
				'nombre_completo'	=> $this->Connect->user('name'),
				'nombre'			=> $this->Connect->user('first_name'),
				'apellido'			=> $this->Connect->user('last_name'),
				'usuario'			=> $this->Connect->user('username'),
				'clave'				=> $this->Connect->authUser['Usuario']['password'],
				'email'				=> $this->Connect->user('email'),
				'sexo'				=> $this->Connect->user('gender'),
				'verificado' 		=> $this->Connect->user('verified'),
				'edad'				=> $this->Session->read('edad')
			), $this->Connect->authUser['Usuario']);
		}

		return true;
	}

	public function beforeRender(){

		$avatar = $this->obtenerAvatar();

		// Capturar permisos de usuario
		try {
			$permisos = $this->hasPermission();
		} catch (Exception $e) {
			$permisos = $e;
		}
		
		// Permisos públicos
		if ( is_object($permisos) && $permisos->getCode() != 66 ) {
			$this->Session->setFlash($permisos->getMessage(), null, array(), 'danger');
			$this->redirect('/');
		}
		
		$modulosDisponibles = $this->getModuleByRole();

		// Camino de migas
		$breadcrumbs	= BreadcrumbComponent::get();
		if ( ! empty($breadcrumbs) && count($breadcrumbs) > 2 ) {
			$this->set(compact('breadcrumbs'));
		}

		// Tiendas
		$tiendasList = $this->obtenerTiendas();
		
		$showDashboard = getDashboard($this->Auth->user('rol_id'));
		
		$this->set(compact('avatar', 'modulosDisponibles', 'permisos', 'tiendasList', 'showDashboard'));
	}


	private function obtenerTiendas() {
		$tiendas = ClassRegistry::init('Tienda')->find('list', array(
			'conditions' => array('Tienda.activo' => 1)
			));

		if (empty($tiendas)) {
			return array( 0 => 'No existen tiendas');
		}

		return $tiendas;
	}


	/**
	* Función que permite obtener el avatar de un administrador
	* @return  		array()
	*/
	private function obtenerAvatar(){
		return ClassRegistry::init('Administrador')->find('first', array(
			'fields' => array(
				'google_imagen'), 
			'conditions' => array(
				'id' => $this->Auth->user('id') 
			)
		));
	}

	/**
	* Functión que determina si el usuario tien permisos para editar, 
	* eliminar y agregar dentro de los módulos.
	* @return 	Array 	$permisosControladorActual 	Arreglo con infromación del acceso al módulo.
	*/ 
	public function hasPermission()
	{
		$jsonPermisos = ClassRegistry::init('Rol')->find('first', array('conditions' => array('Rol.id' => $this->Auth->user('rol_id')), 'fields' => array('permisos')));

		if (empty($jsonPermisos)) {
			return false;
		}

		if (empty($jsonPermisos['Rol']['permisos']) && $this->request->params['action'] != 'admin_login' && $this->request->params['action'] != 'admin_logout') {
		 	throw new Exception('Falta Json con información de permisos.', 11);
		}

		if ( $this->request->params['action'] == 'admin_login' || $this->request->params['action'] == 'admin_logout' ) {
			throw new Exception('Acceso público.', 66);
		}

		$json = json_decode( $jsonPermisos['Rol']['permisos'], true );

		$controladorActual = $this->request->params['controller'];

		$accionActual = $this->request->params['action'];

		

		if( ! array_key_exists($controladorActual, $json) ){
			throw new Exception('No existe el controlador en el json.', 12);
		}

		$permisosControladorActual = $json[$controladorActual];
	
		if( empty($permisosControladorActual) ) {
			throw new Exception('No existe información de permisos del controlador.', 13);
		}else {
			return $permisosControladorActual;
		}	
	}

	/**
	 * Function que determina el Rol del usuario y controla el acceos a los módulos
	 * @return array $data  Lista de módulos disponibles para le usuario.
	 */
	public function getModuleByRole(){
		$modulos = ClassRegistry::init('Modulo')->find('all', array(
				'conditions' => array('parent_id' => NULL, 'Modulo.activo' => 1),
				'joins' => array(
					array(
						'table' => 'modulos_roles',
			            'alias' => 'md',
			            'type'  => 'INNER',
			            'conditions' => array(
			                'md.modulo_id = Modulo.id',
			                'md.rol_id' => $this->Auth->user('rol_id')
			            )
					)
				),
				'fields' => array('Modulo.id', 'Modulo.parent_id', 'Modulo.nombre', 'Modulo.url', 'Modulo.icono')));
		$data = array();
		foreach ($modulos as $padre) {
			$data[] = array(
				'nombre' => $padre['Modulo']['nombre'],
				'icono'	 => $padre['Modulo']['icono'],
				'url'	 => $padre['Modulo']['url'],
				'hijos' => ClassRegistry::init('Modulo')->find(
					'all', array(
						'conditions' => array('Modulo.parent_id' => $padre['Modulo']['id'], 'Modulo.activo' => 1 ),
						'contain' => array('Rol'),
						'joins' => array(
							array(
								'table' => 'modulos_roles',
					            'alias' => 'md',
					            'type'  => 'INNER',
					            'conditions' => array(
					                'md.modulo_id = Modulo.id',
					                'md.rol_id' => $this->Auth->user('rol_id')
					            )
							)
						),
						'fields' => array('Modulo.id', 'Modulo.parent_id', 'Modulo.nombre', 'Modulo.url', 'Modulo.icono')
					)
				)
			);
		}
		return $data;
	}


	/**
	 * Función que lista las categorías disponibles
	 * @return array()
	 */
	public function getCategoriesList() {
		$categorias = ClassRegistry::init('Categoria')->find('list', array('conditions' => array('Categoria.activo' => 1)));
		return $categorias;
	}
	

	/**
	* Calular IVA
	* @param 	$precio 	num 	Valor del producto
	* @param 	$iva 		bool 	Valor del IVA
	* @return 	Integer 	Valor calculado
	*/
	public function precio($precio = null, $iva = null) {
		if ( !empty($precio) && !empty($iva)) {
			// Se quitan los 00
			$iva = intval($iva);

			//Calculamos valor con IVA
			$precio = ($precio + round( ( ($precio*$iva) / 100) ) );

			return round($precio);
		}
	}


	public function precio_neto($precio = null, $iva = 19)
	{
		if (!is_null($precio)) {

			$iva = ($iva / 100) +1;

			return round( $precio / $iva );
		}

		return;
	}


	public function precio_bruto($precio = null, $iva = 19)
	{
		if (!is_null($precio)) {

			$iva = ($iva / 100) +1;

			return round( $precio * $iva );
		}
		
		return;
	}

	/**
	* Función que verifica si la url tiene el guión final y el http
	* de lo contrario lo agregar
	* @param 	$txt 	String 		Texto a formatear
	* @return 	$txt 	String 		Texto formateado
	*/
	public function formatear_url($txt = null, $ssl = false) 
	{
		if (!empty($txt)) {
			
			$largo_url = strlen($txt);

			if ( substr($txt, 0, 7) != 'http://' && substr($txt, 0, 8) != 'https://' ) {
				if ($ssl) {
					$txt = 'https://' . $txt;
				}else{
					$txt = 'http://' . $txt;
				}
			}

			if ( substr($txt, ($largo_url - 1), 1) != '/' ) {
				$txt = $txt . '/';
			}

		}

		return $txt;
	}

	/**
	 * Functión que permite cambiar la configuración de los modelos de BD externos
	 * @param  string  	$tiendaConf  	Nombre de la configuración de BD a utilizar
	 * @return void
	 */
	public function cambiarConfigDB( $tiendaConf = '' ) {
    	// Cambiamos la configuración de la base de datos
		ClassRegistry::init('Productotienda')->useDbConfig 		= $tiendaConf;
		ClassRegistry::init('TaxRulesGroup')->useDbConfig 		= $tiendaConf;
		ClassRegistry::init('TaxRule')->useDbConfig 			= $tiendaConf;
		ClassRegistry::init('Tax')->useDbConfig 				= $tiendaConf;
		ClassRegistry::init('TaxLang')->useDbConfig 			= $tiendaConf;
		ClassRegistry::init('Lang')->useDbConfig 				= $tiendaConf;
		ClassRegistry::init('SpecificPrice')->useDbConfig 		= $tiendaConf;
		ClassRegistry::init('SpecificPricePriority')->useDbConfig 		= $tiendaConf;
		ClassRegistry::init('Cliente')->useDbConfig 			= $tiendaConf;
		ClassRegistry::init('Clientedireccion')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('Paise')->useDbConfig 				= $tiendaConf;
		ClassRegistry::init('PaisIdioma')->useDbConfig 			= $tiendaConf;
		ClassRegistry::init('Region')->useDbConfig 				= $tiendaConf;
		ClassRegistry::init('Orden')->useDbConfig 				= $tiendaConf;
		ClassRegistry::init('OrdenDetalle')->useDbConfig 		= $tiendaConf;
		ClassRegistry::init('OrdenEstado')->useDbConfig 		= $tiendaConf;
		ClassRegistry::init('OrdenEstadoIdioma')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('ProductotiendaIdioma')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('Especificacion')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('EspecificacionIdioma')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('EspecificacionProductotienda')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('EspecificacionValor')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('EspecificacionValorIdioma')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('EspecificacionValorProductotienda')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('ClienteHilo')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('ClienteMensaje')->useDbConfig 	= $tiendaConf;
		ClassRegistry::init('Empleado')->useDbConfig 	= $tiendaConf;
    }
	
	/**
	 * Functión que permite cambiar la configuración de los modelos de BD externos
	 * @param  string  	$tiendaConf  	Nombre de la configuración de BD a utilizar
	 * @return void
	 */
	/*public function cambiarConfigDB( $modelos = array() ) {

		if (SessionComponent::check('Tienda') && !empty($modelos)) {

			# Buscamos la config de la tienda
			$tienda = ClassRegistry::init('Tienda')->find('first', array(
				'conditions' => array(
					'Tienda.id' => SessionComponent::read('Tienda.id')
					)
				));

			# Virificar existencia de la tienda
			if (empty($tienda)) {
				return false;
			}

			# Verificar que la tienda esté configurada
			if (empty($tienda['Tienda']['prefijo']) || empty($tienda['Tienda']['prefijo']) || empty($tienda['Tienda']['configuracion'])) {
				return false;
			}

			# Cambiamos el datasource de los modelos
			foreach ($modelos as $modelo) {
				ClassRegistry::init($modelo)->useDbConfig = $tienda['Tienda']['configuracion'];
			}
			
			return true;
		}

    }*/

	public function tiendaConf( $tienda_id = '') {
		$tiendaConf = ClassRegistry::init('Tienda')->find('first', array('conditions' => array(
				'Tienda.id' => $tienda_id,
			),
			'fields' => array('configuracion')
		));

		if (!empty($tiendaConf)) {
			return $tiendaConf['Tienda']['configuracion'];
		}

		return false;
	}

	public function limpiarDirecciones( $cliente = array() ) {
		
		if (empty($cliente)) {
			return false;
		}
		
		# Sorry for this
		foreach ($cliente as $indice => $valor) {

			foreach ($valor['Clientedireccion'] as $ix => $direccion) {
				
				# Verificamos si viene con dirección
				if ( empty($direccion['alias']) || empty($direccion['address1']) || empty($direccion['id_country']) || empty($direccion['id_state']) ) {
					unset($cliente[$indice]['Clientedireccion']);
				}else {
					# Actualizamos el valor de update
					$cliente[$indice]['Clientedireccion'][$ix]['date_upd'] = date('Y-m-d H:i:s');
					
					if ( isset($direccion['id_address']) && empty($direccion['id_address']) ) {
						unset($cliente[$indice]['Clientedireccion'][$ix]['id_address']);

						# Se agregan campos predeterminados de la tabla
						$cliente[$indice]['Clientedireccion'][$ix]['date_add'] = date('Y-m-d H:i:s');
						
					}
				}

				if ( !isset($direccion['utilizar_check']) || !$direccion['utilizar_check'] ) {
					unset($cliente[$indice]['Clientedireccion']);
				}else{
					unset($cliente[$indice]['Clientedireccion'][$ix]['utilizar_check']);
				}	
			}

			if (empty($cliente[$indice])) {
				unset($cliente[$indice]);
			}
		}
		
		if (!empty($cliente)) {
			return $cliente;
		}
		return false;
	}


	private function cambioTienda() {
		# si es una peticioón post
		if (isset($this->request->data['Tienda']['tienda']) ) {

			# Tema de la tienda
			$tienda = ClassRegistry::init('Tienda')->find('first', array(
				'conditions' => array('Tienda.id' => $this->request->data['Tienda']['tienda'])
				));

			# Método actual
			$action = str_replace(sprintf('%s_', $this->request->params['prefix']), '', $this->request->params['action']);
			
			# Redireccionamos a mismo
			# Si tiene parámetros se redirecciona al index del controllador actual
			if ( !empty($this->request->params['pass']) ) {

				# Cambiamos Session Tienda
				$this->Session->write('Tienda.id', $tienda['Tienda']['id']);
				$this->Session->write('Tienda.tema', $tienda['Tienda']['tema']);
				
				# Redireccionamos
				$this->redirect(array('action' => 'index'));
			}

			# Cambiamos Session Tienda
			$this->Session->write('Tienda.id', $tienda['Tienda']['id']);
			$this->Session->write('Tienda.tema', $tienda['Tienda']['tema']);

			$this->redirect(array('action' => $action));
		}

	}

	public function calcularDescuento($monto = '', $descuento = '') {
		if ( ! empty($monto) && ! empty($descuento) ) {
			$descuento = $descuento / 100;
			
			$monto = $monto - ( $monto * $descuento);
			
			return round($monto);
		}
	}

	public function tiendaInfo( $tienda_id = '') {
		
		$tiendaConf = ClassRegistry::init('Tienda')->find('first', array('conditions' => array(
				'Tienda.id' => $tienda_id,
			)
		));

		# Virificar existencia de la tienda
		if (empty($tiendaConf['Tienda'])) {
			throw new Exception('La tienda seleccionada no existe', 400);
			
			$this->Session->setFlash('La tienda seleccionada no existe' , null, array(), 'danger');
			$this->redirect('/');
		}else{

			$semaforo = true;

			# Verificar que la tienda esté configurada
			foreach ($tiendaConf['Tienda'] as $campo => $valor) {
				if (empty($valor) && $campo != 'principal') {
					$semaforo = false;
					throw new Exception('La tienda no está configurada correctamente. Verifiquela y vuelva a intentarlo', 400);
				}
			}

			# Si la tienda está correctamente configurada se devuelve su información
			if ($semaforo) {
				return $tiendaConf;
			}
		}
	}

	public function verificarTienda() {
		try {
			$tienda = $this->tiendaInfo($this->Session->read('Tienda.id'));
		} catch (Exception $e) {
			$this->Session->setFlash($e->getMessage() , null, array(), 'danger');
			$this->redirect(array('controller' => $this->request->params['controller'], '/'));
		}

		if (!empty($tienda)) {
			$this->Session->write($tienda);
		}
	}


	/**
	 * Método que agrega un datasource a los modelos pasados en el arreglo, según la ´tienda que se esté trabajando.
	 * @param  array  $modelos Nombres de los modelos
	 * @return void
	 */
	public function cambiarDatasource( $modelos = array() ) {

		foreach ($modelos as $instancia) {
			ClassRegistry::init($instancia)->useDbConfig = $this->Session->read('Tienda.configuracion');
		}
		
	}

	public function rutSinDv($rut = '') {
		if (!empty($rut)) {
			$posGuion = strpos($rut, '-');
			if ($posGuion) {
				$rut = substr($rut, 0, $posGuion);	
			}else{
				$rut = substr($rut, -1);
			}
			return str_replace('.', '', $rut);
		}
		return $rut;
	}





	public function ejemploGeolocalizacionChilexpress()
	{
		$resultado = array();

		# TST Chilexpress
		 
		
		/* Obtener regiones */
		try {
			$resultado['Regiones'] = $this->GeoReferencia->obtenerRegiones();
		} catch (Exception $e) {
			$resultado['Regiones'] = $e;
		}
		

		/* Obtener coberturas */
		try {
			$resultado['Coberturas'] = $this->GeoReferencia->obtenerCoberturas('3', 'RM');
		} catch (Exception $e) {
			$resultado['Coberturas'] = $e;
		}

		/* Obtener calles */
		try {
			$resultado['Calles'] = $this->GeoReferencia->obtenerCalles('SANTIAGO CENTRO', 'Vicuña mackenna');
		} catch (Exception $e) {
			$resultado['Calles'] = $e;
		}
		
		/* Obtener numeración calle  */
		try {
			$resultado['Numeracion'] = $this->GeoReferencia->obtenerNumeracionCalles('175309', 1725);
		} catch (Exception $e) {
			$resultado['Numeracion'] = $e;
		}

		/* Validar direcciones  */
		try {
			$resultado['Vdireccion'] = $this->GeoReferencia->validarDireccion('SANTIAGO CENTRO', 'Avenida vicuña mackenna', '' , 1725, '', '');
		} catch (Exception $e) {
			$resultado['Vdireccion'] = $e;
		}


		/* Consultar oficinas chilexpress por comuna */
		try {
			$resultado['OficinasC'] = $this->GeoReferencia->obtenerDireccionOficinasComuna('Curico');
		} catch (Exception $e) {
			$resultado['OficinasC'] = $e;
		}

		/* Consultar oficinas chilexpress  por región */
		try {
			$resultado['OficinasR'] = $this->GeoReferencia->obtenerDireccionOficinasRegion('R7');
		} catch (Exception $e) {
			$resultado['OficinasR'] = $e;
		}
		

		return $resultado;
	}


	public function ejemploTarificacion()
	{
		try {
			# Origen - Destino - Peso - Alto - Ancho - Largo
			$resultado = $this->Tarificacion->obtenerTarifaPaquete('STGO', 'CURI', 3.5, 40, 40, 50);
		} catch (Exception $e) {
			$resultado = $e;
		}

		return $resultado;
	}

}
