<?php 
App::uses('Component', 'Controller');
App::import('Vendor', 'Enviame', array('file' => 'Enviame/Enviame.php'));
App::import('Vendor', 'LAFFPack', array('file' => 'Enviame/LAFFPack.php'));

class EnviameComponent extends Component
{	

	public $Enviame;
	public $LAFFPack;

	/**
	 * 
	 * 	
	 * 	Para la consola se carga el componente on the fly!
		$enviame = $this->Components->load('Enviame');

		$enviame->conectar('a77c7937c0d5e6631a6494c87cfc0567', 620);

		prx($enviame->obtener_bodegas());
	 */

	public function conectar($apikey, $empresa)
	{
		$this->Enviame = new Enviame($apikey, $empresa);
		$this->LAFFPack = new LAFFPack();
	}


	public function LAFFinit()
	{
		$this->LAFFPack = new LAFFPack();
	}


	public function obtener_bodegas()
	{
		return $this->Enviame->ver_bodegas();
	}

	public function crearEnvio($venta = array())
	{	
		
		# Primero verificamos que el peso total del envio no supere el limite puesto en la configuracion de envíame
		# 
		# Se calcula el volumen máximo para armar distintos paquetes
		$pesoTotal     = array_sum(Hash::extract($venta['VentaDetalle'], '{n}.VentaDetalleProducto.peso'));
		$pesoMaximo    = $venta['Tienda']['peso_enviame'];
		$volumenMaximo = $venta['Tienda']['volumen_enviame'];
		$bodega 	   = $venta['Tienda']['bodega_enviame'];
		
		if ($pesoTotal >= $pesoMaximo || $volumenMaximo == 0) {
			return 'Error de pesos';
		}		
		
		$paquetes = $this->obtener_bultos_venta($venta, $volumenMaximo);
		
		# dimensiones de todos los paquetes unificado
		$largoTotal = array_sum(Hash::extract(Hash::extract($paquetes, '{n}.paquete'), '{n}.length'));
		$anchoTotal = array_sum(Hash::extract(Hash::extract($paquetes, '{n}.paquete'), '{n}.width'));
		$altoTotal  = array_sum(Hash::extract(Hash::extract($paquetes, '{n}.paquete'), '{n}.height'));

		$shipping_order = array(
			'imported_id' => $venta['Venta']['id'],
			'order_price' => round($venta['Venta']['total'], 0),
			'n_packages'  => count($paquetes),
			'content_description' => 'Bulto preparado por ' . $venta['Venta']['picking_email'] . ' para la venta ID-EX #' . $venta['Venta']['id_externo'],
			'type' => 'delivery',
			'weight' => $pesoTotal,
			'volume' => $this->calcular_volumen( $largoTotal, $anchoTotal, $altoTotal)
		);

		$shipping_destination = array(
			'customer' => array(
				'name' => $venta['Venta']['nombre_receptor'],
				'phone' => str_replace(' - ', '', $venta['Venta']['fono_receptor']),
				'email' => $venta['VentaCliente']['email']
			),
			'delivery_address' => array(
				'home_adress' => array(
					'place' => $venta['Venta']['comuna_entrega'],
					'full_address' => $venta['Venta']['direccion_entrega']
				)
			)
		);

		$shipping_origin = array(
			'warehouse_code' => $bodega
		);

		$carrier = array(
			'carrier_code' => '',
			'tracking_number' => ''
		);

		$logs = array();

		$resultado = $this->Enviame->crear_envio_como_empresa($shipping_order, $shipping_destination, $shipping_origin, $carrier);
		
		$log[] = array(
			'Log' => array(
				'administrador' => 'Picking Enviame',
				'modulo' => 'Ventas',
				'modulo_accion' => json_encode($resultado)
			)
		);

		if ($resultado['httpCode'] >= 300 || empty($resultado['body'])) {

			ClassRegistry::init('Log')->create();
			ClassRegistry::init('Log')->saveMany($log);

			return 'Codigo respusta: ' . $resultado['httpCode'];
		}

		$enviameRes = to_array($resultado)['body']['data'];

		$carrier_name = $enviameRes['carrier'] . ' (Enviame)';
		$carrier_opt = array(
			'Transporte' => array(
				'codigo' => $enviameRes['carrier']
			)
		);
		
		# Se guarda la información del tracking en la venta
		$nwVenta = array(
			'Venta' => array(
				'id' => $venta['Venta']['id'],
				'etiqueta_envio_externa' => $enviameRes['label']['PDF']
			),
			'Transporte' => array(
				0 => array(
					'transporte_id'   => ClassRegistry::init('Transporte')->obtener_transporte_por_nombre($carrier_name, true, $carrier_opt),
					'cod_seguimiento' => $enviameRes['tracking_number'],
					'etiqueta'        => $enviameRes['label']['PDF']
				)
			)
		);

		ClassRegistry::init('Log')->create();
		ClassRegistry::init('Log')->saveMany($log);

		return ClassRegistry::init('Venta')->saveAll($nwVenta);

	}


	/**
	 * Calcula a aproximacion de bltos que se deberían armar en base a los itemes
	 * @param  array $venta         Detalle de la venta
	 * @param  float $volumenMaximo volumen máximo para cada paquete
	 * @return array
	 */
	public function obtener_bultos_venta($venta, $volumenMaximo)
	{	
		$bultos = array();

		foreach ($venta['VentaDetalle'] as $ivd => $d) {
			
			$alto  = $d['VentaDetalleProducto']['alto'];
			$ancho = $d['VentaDetalleProducto']['ancho'];
			$largo = $d['VentaDetalleProducto']['largo'];
			$peso  = $d['VentaDetalleProducto']['peso'];

			$volumen = $this->calcular_volumen($alto, $ancho, $largo);

			$caja = array(
				'id'     => $d['VentaDetalleProducto']['id'],
				'width'  => $ancho,
				'height' => $alto,
				'length' => $largo,
				'weight' => $peso
			);

			$unico = rand(1000, 100000);
			
			if ($volumen > $volumenMaximo) {
				$bultos[$d['venta_id'] . $unico]['cajas'][] = $caja;
				$bultos[$d['venta_id'] . $unico]['total_items'] = 1;
			}else{
				$bultos[$d['venta_id']]['cajas'][] = $caja;
				$bultos[$d['venta_id']]['total_items'] = (isset($bultos[$d['venta_detalle_producto_id']]['total_items'])) ? $bultos[$d['venta_detalle_producto_id']]['total_items'] + 1 : 1;
			}

		}
		
		$resultado = array();
		
		foreach ($bultos as $ib => $b) {
			$resultado[$ib]['paquete'] = $this->obtenerDimensionesPaquete($b['cajas']);
			$resultado[$ib]['paquete']['weight'] = array_sum(Hash::extract($b['cajas'], '{n}.weight'));
			$resultado[$ib]['items'] = $b['cajas'];
		}

		return $resultado;
	}


	/**
	 * [calcular_volumen description]
	 * @param  float $largo cm
	 * @param  float $ancho cm
	 * @param  float $alto  cm
	 * @return float
	 */
	public function calcular_volumen($alto, $ancho, $largo)
	{	
		return (float) round( ($largo/100) * ($ancho/100) * ($alto/100), 2);
	}


	/**
	 * [calcular_peso_volumetrico description]
	 * @param  [type]  $largo           cm
	 * @param  [type]  $ancho           cm
	 * @param  [type]  $alto            cm
	 * @param  integer $tasa_conversion dada por el transportista
	 * @return int
	 */
	public function calcular_peso_volumetrico($largo, $ancho, $alto, $tasa_conversion = 4000)
	{
		return (int) $this->calcular_volumen($largo, $ancho, $alto) / $tasa_conversion;
	}


	/**
	 * Crea una arreglo con las cajas de los productos segun sis dimensiones
	 * @param  array  $productos 	Listado de productos
	 * @return array
	 */
	public function obtenerCajasProductos($productos = array(), $modelo = '')
	{	
		foreach ($productos as $ip => $producto) {
			$product_width  = (float) $producto[$modelo]['ancho'];
			$product_height = (float) $producto[$modelo]['alto'];
			$product_length  = (float) $producto[$modelo]['largo'];
			
			$values = array(
                $product_width,
                $product_height,
                $product_length
            );
            
            sort($values);

            $cajas[$producto[$modelo]['id']] = array_combine(array('height', 'width', 'length'), $values);
		}

		return $cajas;
	}


	/**
	 * [obtenerDimensionesPaquete description]
	 * @param  array  $cajas [description]
	 * @return [type]        [description]
	 */
	public function obtenerDimensionesPaquete($cajas = array())
	{	
		$this->LAFFPack->pack($cajas);
        
        # Se obtienen las dimensiones del paquete
        $paquete = $this->LAFFPack->get_container_dimensions();

        return $paquete;
        
	}


/*

{
	"shipping_order"       : {
			"imported_id"		: "1112222-3333",
	        "order_price"         : "1000",
	        "n_packages"          : "1",
	        "content_description" : "descripcion de contenido",
	        "type"                : "delivery",
	        "weight"              : "0.1",
	        "volume"              : "5.001"
	},
	"shipping_destination" : {
	    "customer"         : {
	        "name"  : "John Doe",
	        "phone" : "11111111111",
	        "email" :"john@doe.com"
	    },
	    "delivery_address" : {
	        "home_address" : {
	            "place"        : "Providencia",
	            "full_address" : "Av. Andres Bello 2447 Local 4153"
	        }
	    }
	},
	"shipping_origin" : {
		"warehouse_code" : "cod_bod"
	},
	"carrier" : {
		"carrier_code" : "BLX",
		"tracking_number": "xxx1111"
	}
}

*/

}