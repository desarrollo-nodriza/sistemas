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
			'loginRedirect'		=> '/emails',
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
		'Breadcrumb' => array(
			'crumbs'		=> array(
				array('', null),
				array('Inicio', '/'),
			)
		)
		//'Facebook.Connect'	=> array('model' => 'Usuario'),
		//'Facebook'
	);

	public function beforeFilter()
	{
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

		if ( ! empty($this->request->query['code']) && $this->Session->read('Google.code') != $this->request->query['code'] )
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
		if ( ! empty($breadcrumbs) && count($breadcrumbs) > 2 )
		{
			$this->set(compact('breadcrumbs'));
		}

		$this->set(compact('avatar', 'modulosDisponibles', 'permisos'));
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

	/**
	* Función que verifica si la url tiene el guión final y el http
	* de lo contrario lo agregar
	* @param 	$txt 	String 		Texto a formatear
	* @return 	$txt 	String 		Texto formateado
	*/
	public function formatear_url($txt = null) 
	{
		if (!empty($txt)) {
			
			$largo_url = strlen($txt);

			if ( substr($txt, 0, 7) != 'http://' && substr($txt, 0, 8) != 'https://' ) {
				$txt = 'http://' . $txt;
			}

			if ( substr($txt, ($largo_url - 1), 1) != '/' ) {
				$txt = $txt . '/';
			}

		}

		return $txt;
	}

	/**
	 * Functión que permite cambiar la configuración de los modelos de BD esternos
	 * @param  string  	$tiendaConf  	Nombre de la configuración de BD a utilizar
	 * @return void
	 */
	public function cambiarConfigDB( $tiendaConf = '' ) {
    	// Cambiamos la configuración de la base de datos
		ClassRegistry::init('Productotienda')->useDbConfig = $tiendaConf;
		ClassRegistry::init('TaxRulesGroup')->useDbConfig = $tiendaConf;
		ClassRegistry::init('TaxRule')->useDbConfig = $tiendaConf;
		ClassRegistry::init('Tax')->useDbConfig = $tiendaConf;
		ClassRegistry::init('SpecificPrice')->useDbConfig = $tiendaConf;
    }
	
}
