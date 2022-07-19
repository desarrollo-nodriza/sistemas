<?php
App::uses('AppController', 'Controller');
App::uses('MetodoEnviosController', 'Controller');
App::uses('VentaDetalleProductosController', 'Controller');
App::uses('VentasController', 'Controller');
App::uses('OrdenComprasController', 'Controller');
App::uses('ContactosController', 'Controller');


class PruebasController extends AppController
{

  public $components = array(
    'Starken',
    'Conexxion',
    'Boosmap',
    'BlueExpress',
    'WarehouseNodriza',
  );

  public function api_pruebas()
  {
    $OrdenComprasController = new OrdenComprasController();
    prx($OrdenComprasController->RecorrerProveedor());
  }
}
