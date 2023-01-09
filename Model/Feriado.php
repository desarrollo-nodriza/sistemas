<?php
App::uses('AppModel', 'Model');

class Feriado extends AppModel
{

	public $useTable = 'feriados';


	
	/**
	 * actualizar_feriados_sabado_domingo
	 *
	 * @param  string $inicio string de fecha fecha Y-m-d
 	 * @param  string $fin string de fecha fecha Y-m-d
	 * @return bool
	 */
	public function actualizar_feriados_sabado_domingo($inicio = null, $fin = null)
	{
		$inicio 					= $inicio ?? date("Y") . "-01-01";
		$fin		 				= $fin ?? (date("Y") + 1) . "-01-01";
	
		$feriados_sabado_domingo 	= $this->calcular_feriados_sabado_domingo($inicio, $fin);
		$feriados 					= $feriados_sabado_domingo['feriados'];
		$holidays 					= $feriados_sabado_domingo['holidays'];

		$feriados_existentes 		= Hash::extract(ClassRegistry::init('Feriado')->find('all', [
			'conditions' => [
				'Feriado.feriado' => $holidays
			]
		]), "{n}.Feriado.feriado");

		$feriados_persistir = array_filter(
			$feriados,
			function ($v, $k) use ($feriados_existentes) {
				return !in_array($v['feriado'], $feriados_existentes);
			},
			ARRAY_FILTER_USE_BOTH
		);

		if ($feriados_persistir) {
			ClassRegistry::init('Feriado')->create();
			ClassRegistry::init('Feriado')->saveAll($feriados_persistir);
		}
		return true;

	}

	
	/**
	 * calcular_feriados_sabado_domingo
	 *
	 * @param  string $inicio string de fecha fecha Y-m-d
	 * @param  string $fin string de fecha fecha Y-m-d
	 * @return array
	 */
	public function calcular_feriados_sabado_domingo($inicio, $fin)
	{

		$start 		= new DateTime($inicio);
		$end 		= new DateTime($fin);
		$period 	= new DatePeriod($start, new DateInterval('P1D'), $end);
		$holidays 	= [];
		$feriados 	= [];

		foreach ($period as $dt) {
			$curr = $dt->format('D');
			if ($curr == 'Sat' || $curr == 'Sun') {
				$holidays[] = $dt->format('Y-m-d');
				$feriados[] = [
					'feriado'		=> $dt->format('Y-m-d'),
					'descripcion'	=> $curr == 'Sat' ? 'Sábado' : 'Domingo',
				];
			}
		}

		return [
			'holidays' => $holidays,
			'feriados' => $feriados,
		];
		
	}

}
