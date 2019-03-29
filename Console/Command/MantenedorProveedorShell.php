<?php 

App::uses('CakeRequest', 'Network');
App::uses('CakeResponse', 'Network');
App::uses('Controller', 'Controller');
App::uses('ProveedoresController', 'Controller');

class MantenedorProveedorShell extends AppShell {

	public function main() {

		$this->out('Inicia actualización de proveedores base: ' . date('Y-m-d H:i:s'));
		$this->hr();

		$proveedoresController = new ProveedoresController(new CakeRequest(), new CakeResponse());

		$this->out($proveedoresController->actualizar_proveedores_base());
		$this->hr();
		$this->out('Finaliza actualización de proveedores base: ' . date('Y-m-d H:i:s'));
		$this->hr();
		$this->hr();

	}

}