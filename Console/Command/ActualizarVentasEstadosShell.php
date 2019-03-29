<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('VentasController', 'Controller');

class ActualizarVentasEstadosShell extends AppShell {
	
	public function main() {

		$this->out('Inicia actualizar estado ventas no atendidas: ' . date('Y-m-d H:i:s'));
		$this->hr();

		$controller = new VentasController(new CakeRequest(), new CakeResponse());
		$controller->shell = true;
		$controller->admin_verificar_conexion_meli();
		$controller->actualizar_ventas_anteriores();

		$this->hr();
		$this->out('Finaliza actualizar estado ventas anteriores: ' . date('Y-m-d H:i:s'));
		$this->hr();
		$this->hr();


		$this->hr();
		$this->out('Inicia ventas_estados_revertidas: ' . date('Y-m-d H:i:s'));
		$this->hr();
		$this->hr();
		# revertir el stock virtual del producto segun su estado
		$controller->ventas_estados_revertidas();
		$this->hr();
		$this->out('Finaliza ventas_estados_revertidas: ' . date('Y-m-d H:i:s'));
		$this->hr();
		$this->hr();

		$this->hr();
		$this->out('Inicia ventas_estados_atendidos: ' . date('Y-m-d H:i:s'));
		$this->hr();
		$this->hr();
		
		# Marca las ventas como atendidas segun su estado.
		$controller->ventas_estados_atendidos();

		$this->hr();
		$this->out('Finaliza actualizar estado ventas no atendidas: ' . date('Y-m-d H:i:s'));
		$this->hr();
		$this->hr();
	}

}