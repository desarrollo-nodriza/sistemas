<?php
App::uses('AppModel', 'Model');

class DteCompra extends AppModel
{

	public function existe_por_folio($folio, $tipo_dte, $rut)
	{
		$dte = $this->find('count', array(
			'conditions' => array(
				'DteCompra.folio' => $folio,
				'DteCompra.tipo_documento' => $tipo_dte,
				'DteCompra.rut_emisor' => $rut
			)
		));

		if ($dte > 0) {
			return true;
		}
		
		return false;
	}

	
	/**
	 * obtenerDte
	 *
	 * @param  mixed $folio
	 * @param  mixed $emisor
	 * @return void
	 */
	public function obtenerDte($folio, $emisor)
	{
		return $this->find('first', array(
			'conditions' => array(
				'DteCompra.folio' => $folio,
				'DteCompra.rut_emisor' => $emisor
			)
		));
	}

}