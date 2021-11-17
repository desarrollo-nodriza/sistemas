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

					# Ordenamos los proveedores desde el con mayor stock al menor
					$cStock = array_column($producto['detalle_proveedores'], 'stock');
					array_multisort($cStock, SORT_DESC, $producto['detalle_proveedores']);

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
							break;
						} else {

							if (!$producto['disponible']) {

								$sinStock[] 		= $info;
								$ids_sin_stock[] 	= $producto['producto_info']['mi_id'];
							}
						}
					}
				}
			}
		}else{

			$tienda = ClassRegistry::init('Tienda')->tienda_principal(array(
				'mandrill_apikey', 'nombre'
			));

			$mandrill_apikey = $tienda['Tienda']['mandrill_apikey'];

			if (!empty($mandrill_apikey)) 
			{
				$mandrill = $this->Components->load('Mandrill');
				$mandrill->conectar($mandrill_apikey);	

				$asunto = '[Nodriza Spa-'.rand(100,10000).'] Onestock dejó de actualizar';
			
				if (Configure::read('ambiente') == 'dev') 
				{
					$asunto = '[Nodriza Spa-'.rand(100,10000).'-DEV] Onestock dejó de actualizar';
				}

				$remitente = array(
					'email' => 'no-reply@nodriza.cl',
					'nombre' => 'Nodriza Spa'
				);

				$destinatarios = array(
					array('email' => 'cristian.rojas@nodriza.cl')
				);

				$html = '<h1>Sistema dejó de actualizar con onestock</h1>';

				$mandrill->enviar_email($html, $asunto, $remitente, $destinatarios);
			}

		}

		return ['sinStock' => $sinStock, 'conStock' => $conStock, 'ids_con_stock' => $ids_con_stock, 'ids_sin_stock' => $ids_sin_stock, 'token' => $response['token'], 'response' => $response];
	}
	public function obtenerProductosClienteSinPaginacionOneStock()
	{
		return $this->onestock->obtenerProductosClienteSinPaginacionOneStock();
	}


	public function obtener_producto($id, $stock_default = 10)
	{	
		$result = $this->onestock->obtenerProductoOneStock($id);
		return $result;
		if ($result['code'] != 200)
		{
			return array();
		}

		$item = $result['respuesta'];

		$stock_global = 0;

		foreach($result['detalle_proveedores'] as $i => $prov)
		{	
			$stock = $prov['stock'];

			if ($prov['tipo_stock'] == 'binario')
			{
				$stock = ($prov['stock'] > 0) ? $stock_default : 0;
			}

			$stock_global = $stock_global + $stock;
		}

		$item['stock_global'] = $stock_global;

		return $item;
	}
}
