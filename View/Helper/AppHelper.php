<?php
App::uses('Helper', 'View');
class AppHelper extends Helper
{
	public function menuActivo($link = array())
	{
		if ( ! is_array($link) || empty($link) )
		{
			return false;
		}

		$action				= $this->request->params['action'];
		$controller			= $this->request->params['controller'];
		$prefix				= (isset($this->request->params['prefix']) ? $this->request->params['prefix'] : null);


		if ( $prefix && isset($this->request->params[$prefix]) && $this->request->params[$prefix] )
		{
			$tmp_action			= explode('_', $action);
			if ( $tmp_action[0] === $prefix )
			{
				array_shift($tmp_action);
				$action			= implode('_', $tmp_action);
			}
		}

		return (
			(isset($link['controller']) ? ($link['controller'] == $controller) : true) &&
			(isset($link['action']) ? ($link['action'] == $action) : true)
		);
	}
}
