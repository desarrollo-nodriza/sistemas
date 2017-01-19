<?php
App::uses('Model', 'Model');
class AppModel extends Model
{
	public $recursive		= -1;
	public $actsAs			= array('Containable');


	/**
	 * VALIDACION -- REPETIR CLAVE
	 */
	function repetirClave($data)
	{
		return ($this->data[$this->name]['clave'] === $this->data[$this->name]['repetir_clave']);
	}


	/**
	 * VALIDACION -- VALIDA RUT CHILENO
	 */
	public function rutChileno($data = array(), $dv = null)
	{
		$rut		= $rutcalc = preg_replace('/[^\da-z]/i', '', current($data));
		if ( ! $dv )
		{
			$dv			= substr($rut, -1);
			$rut		= $rutcalc = substr_replace($rut, '', -1);
		}
		else
		{
			$dv			= $this->data[$this->name][$dv];
		}

		if ( ! $rut || ! is_numeric($rut) || strlen($rut) < 6 || strlen($dv) != 1 )
		{
			return false;
		}

		$suma		= 1;
		for ( $x = 0; $rutcalc != 0; $rutcalc /= 10 )
		{
			$suma	= ($suma + $rutcalc % 10 * (9 - $x++ % 6)) % 11;
		}
		$dvcalc		= chr($suma ? $suma + 47 : 75);

		return (strtolower($dvcalc) == strtolower($dv));
	}


	/**
	 * VALIDACION -- VALIDA QUE UNA LLAVE FORANEA EXISTA EN EL MODELO ASOCIADO
	 */
	public function validateForeignKey($data = array())
	{
		$associations	= array_map(
			create_function('$v', 'return $v["foreignKey"];'),
			$this->belongsTo
		);
		$aliases		= array();
		foreach ( $associations as $model => $foreignKey )
		{
			if ( ! array_key_exists($foreignKey, $aliases) )
			{
				$aliases[$foreignKey] = array();
			}
			array_push($aliases[$foreignKey], $model);
		}
		foreach ( $aliases[key($data)] as $model )
		{
			$count	= $this->{$model}->find('count', array(
				'conditions'	=> array("{$model}.{$this->{$model}->primaryKey}" => current($data)),
				'recursive'		=> -1
			));
			if ( $count == 1 )
			{
				return true;
			}
		}
		return false;
	}
}
