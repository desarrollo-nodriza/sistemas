<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'Onestock', array('file' => 'Onestock/Onestock.php'));


class OnestockComponent extends Component
{
	private $onestock;

	public function crearCliente($apiurl_onestock, $cliente_id_onestock, $onestock_correo, $onestock_clave, $token_onestock)
	{
		$this->onestock = new Onestock($apiurl_onestock, $cliente_id_onestock, $onestock_correo, $onestock_clave, $token_onestock);
	}

	public function conexion_api_onestock($tienda_id = null)
	{

		$conditions = [
			"Tienda.id" => $tienda_id
		];
		$conditions = array_filter($conditions);
		$conditions = array_merge($conditions, [
			"Tienda.apiurl_onestock !="     => '',
			"Tienda.cliente_id_onestock !=" => '',
			"Tienda.onestock_correo !="     => '',
			"Tienda.onestock_clave !="      => '',
		]);

		return ClassRegistry::init('Tienda')->find('first', [
			'fields' => [
				'Tienda.id',
				'Tienda.apiurl_onestock',
				'Tienda.cliente_id_onestock',
				'Tienda.onestock_correo',
				'Tienda.onestock_clave',
				'Tienda.token_onestock',
				'Tienda.stock_default',
			],
			'conditions' => $conditions
		]);
	}

	public function obtenerProductoOneStock($producto_id)
	{
		return $this->onestock->obtenerProductoOneStock($producto_id);
	}

	public function obtenerProductosClienteOneStock()
	{

		$response 		= $this->onestock->obtenerProductosClienteSinPaginacionOneStock();

		$sinStock 		= [];
		$conStock 		= [];
		$ids_sin_stock 	= [];
		$ids_con_stock 	= [];

		if ($response['code'] == 200) {

			foreach ($response['response']['productos'] as $producto) {

				if (isset($producto['producto_info']['mi_id'])) {


					foreach ($producto['detalle_proveedores'] as $proveedore) {

						$info = [
							'id'                    => $producto['producto_info']['mi_id'],
							'fecha_modificacion'    => $proveedore['fecha_modificacion'],
							'proveedor_id'          => $proveedore['id'],
							'disponible'            => $proveedore['disponible'] ?? false,
							'stock'                 => $proveedore['stock'] ?? 0,
							'binario'				=> $proveedore['tipo_stock'] == 'binario' ? true : false
						];

						if ($proveedore['disponible']) {

							$conStock[] 		=	$info;
							$ids_con_stock[] 	= $producto['producto_info']['mi_id'];
							continue;
						} else {

							if (!$producto['disponible']) {

								$sinStock[] 		= $info;
								$ids_sin_stock[] 	= $producto['producto_info']['mi_id'];
							}
						}
					}
				}
			}
		}

		return ['sinStock' => $sinStock, 'conStock' => $conStock, 'ids_con_stock' => $ids_con_stock, 'ids_sin_stock' => $ids_sin_stock, 'token' => $response['token'], 'response' => $response];
	}
	public function obtenerProductosClienteSinPaginacionOneStock()
	{
		return $this->onestock->obtenerProductosClienteSinPaginacionOneStock();
	}
}
