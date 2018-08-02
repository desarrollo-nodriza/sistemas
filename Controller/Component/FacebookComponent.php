<?php
App::uses('Component', 'Controller');
class FacebookComponent extends Component
{
	/**
	 * Controlador padre
	 * @var Controller
	 */
	var $controller;


	/**
	 * Inicializa el componente
	 *
	 * @return			void
	 */
	public function initialize(Controller $controller)
	{
		$this->controller	= $controller;
	}


	/**
	 * Redirige funciones que no existan en el componente directamente a la libreria FB
	 *
	 * @param			string			$function			Nombre de la funcion llamada
	 * @param			array			$arguments			Arreglo de argumentos pasados
	 * @return			mixed								Retorno de la funcion llamada
	 */
	public function __call($function = '', $arguments = array())
	{
		return call_user_func_array(array('FB', $function), $arguments);
	}


	/**
	 * Verifica si el usuario actual ha concedido los permisos configurados en la aplicacion
	 *
	 * @return			boolean								False si no ha concedido uno o mas permisos
	 */
	public function checkPermissions()
	{
		$permissions		= Configure::read('Facebook.scope');
		if ( ! $permissions || empty($permissions) )
			return true;
		$permissions		= explode(',', $permissions);

		$granted		= FB::api('/me/permissions');
		if ( ! is_array($granted) || ! isset($granted['data'], $granted['data'][0]) )
			return false;

		$granted		= array_keys($granted['data'][0]);

		foreach ( $permissions as $permission )
		{
			if ( ! in_array($permission, $granted) )
				return false;
		}

		return true;
	}


	/**
	 * Retorna la URL de login contra Facebook. Agrega los permisos si estan configurados
	 *
	 * @param			array			$params				Parametros para construir la URL
	 * @return			string								URL de login en Facebook
	 */
	public function loginUrl($params = array())
	{
		if ( Configure::read('Facebook.scope') )
			$params['scope']	= Configure::read('Facebook.scope');

		return FB::getLoginUrl($params);
	}


	/**
	 * Retorna la URL de login contra Facebook. Devuelve al fanpage configurado en Config/facebook.php
	 *
	 * @return			string								URL de login
	 */
	public function tabUrl()
	{
		$fanpage			= current(array_keys(Configure::read('Facebook.fanpage')));
		$fanpage_id			= current(Configure::read('Facebook.fanpage'));
		$app_id				= Configure::read('Facebook.appId');
		$redirect_uri		= "http://www.facebook.com/pages/{$fanpage}/{$fanpage_id}?id={$fanpage_id}&sk=app_{$app_id}";
		return $redirect_uri;
	}


	/**
	 * Retorna la URL de login contra Facebook. Devuelve al fanpage configurado en Config/facebook.php
	 *
	 * @return			string								URL de login
	 */
	public function loginUrlTab()
	{
		$redirect_uri		= (! Configure::read('Facebook.dev') ? $this->tabUrl() : Router::url('/', true));
		return $this->loginUrl(array('redirect_uri' => $redirect_uri));
	}


	/**
	 * Checkea si el usuario actual es fan de los fanpages dados
	 *
	 * @param			array			$fanpages_id		Arreglo con los IDs de fanpages a verificar
	 * @return			boolean								False si el usuario no es fan de uno o mas fanpages
	 */
	public function checkFan($fanpages_id = array())
	{
		if ( ! $fanpages_id	)
			$fanpages_id		= Configure::read('Facebook.fan');

		if ( ! $fanpages_id	)
			return true;

		foreach ( $fanpages_id as $fanpage_id )
		{
			$like		= FB::api("/me/likes/{$fanpage_id}");
			if ( ! is_array($like) || ! isset($like['data']) || empty($like['data']) )
				return false;
		}

		return true;
	}


	/**
	 * Verifica si el usuario activo es fan del tab
	 *
	 * @return			boolean								False si el usuario no es fan
	 */
	public function tabFan()
	{
		$signed_request		= FB::getSignedRequest();

		if ( $this->controller->request->is('localip') && ( ! is_array($signed_request) || ! isset($signed_request['page']) ) )
			return true;

		return (is_array($signed_request) && isset($signed_request['page']) && $signed_request['page']['liked']);
	}
}
