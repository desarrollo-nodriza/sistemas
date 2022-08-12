<?php

App::uses('Controller', 'Controller');
App::uses('VentasController', 'Controller');

class DteRechazadosShell extends AppShell
{

	public function main()
	{

		$VentasController = new VentasController();
		$VentasController->ProcesarDteRechazados();
		return true;
	}
}
