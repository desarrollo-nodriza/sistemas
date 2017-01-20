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

	/**
	 * Traducir Mes a Español
	 * @param 	String 	Mes a traducir
	 * @return  String 	Mes traducido	
	 */
	public function translateMonth( $mes = null ) {

		if (!empty($mes)) {

			$mesesAEspañol = array(
				'January' => 'Enero',
				'Feruary' => 'Febrero',
				'March' => 'Marzo',
				'April' => 'Abril',
				'May' => 'Mayo',
				'June'=> 'Junio',
				'July' => 'Julio',
				'August' => 'Agosto',
				'September' => 'Septiembre',
				'October' => 'Octubre',
				'November' => 'Noviembre',
				'December' => 'Diciembre'
			);

			if ( array_key_exists($mes, $mesesAEspañol) ) {
				$mes = $mesesAEspañol[$mes];
				return $mes;
			}else{
				return $mes;
			}
		}
	}
}
