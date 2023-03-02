<?php
App::uses('AppModel', 'Model');
class Proveedor extends AppModel
{
	/**
	 * CONFIGURACION DB
	 */
	public $displayField	= 'nombre';


	public $hasMany = array(
		/*'PrecioEspecificoProveedor' => array(
			'className'				=> 'PrecioEspecificoProveedor',
			'foreignKey'			=> 'proveedor_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),*/
		'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'foreignKey'			=> 'proveedor_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'OrdenCompraFactura' => array(
			'className'				=> 'OrdenCompraFactura',
			'foreignKey'			=> 'proveedor_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'Saldo' => array(
			'className'				=> 'Saldo',
			'foreignKey'			=> 'proveedor_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'Token' => array(
			'className'				=> 'Token',
			'foreignKey'			=> 'proveedor_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'FrecuenciaGenerarOC' => array(
			'className'				=> 'FrecuenciaGenerarOC',
			'foreignKey'			=> 'proveedor_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		),
		'TipoEntregaProveedorOC' => array(
			'className'				=> 'TipoEntregaProveedorOC',
			'foreignKey'			=> 'proveedor_id',
			'dependent'				=> false,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'exclusive'				=> '',
			'finderQuery'			=> '',
			'counterQuery'			=> ''
		)
	);

	public $hasAndBelongsToMany = array(
		'VentaDetalleProducto' => array(
			'className'				=> 'VentaDetalleProducto',
			'joinTable'				=> 'proveedores_venta_detalle_productos',
			'foreignKey'			=> 'proveedor_id',
			'associationForeignKey'	=> 'venta_detalle_producto_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		'Moneda' => array(
			'className'				=> 'Moneda',
			'joinTable'				=> 'monedas_proveedores',
			'foreignKey'			=> 'proveedor_id',
			'associationForeignKey'	=> 'moneda_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'width'					=> 'MonedasProveedor',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),
		/*'Marca' => array(
			'className'				=> 'Marca',
			'joinTable'				=> 'proveedores_marcas',
			'foreignKey'			=> 'proveedor_id',
			'associationForeignKey'	=> 'marca_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		),*/
		/*'OrdenCompra' => array(
			'className'				=> 'OrdenCompra',
			'joinTable'				=> 'orden_compras_proveedores',
			'foreignKey'			=> 'proveedor_id',
			'associationForeignKey'	=> 'orden_compra_id',
			'unique'				=> true,
			'conditions'			=> '',
			'fields'				=> '',
			'order'					=> '',
			'limit'					=> '',
			'with'					=> 'OrdenComprasProveedor',
			'offset'				=> '',
			'finderQuery'			=> '',
			'deleteQuery'			=> '',
			'insertQuery'			=> ''
		)*/
		'ReglasGenerarOC' => array(
			'className'				=> 'ReglasGenerarOC',
			'joinTable'				=> 'reglas_proveedores',
			'foreignKey'			=> 'proveedor_id',
			'associationForeignKey'	=> 'regla_generar_oc_id',
			'unique'				=> true,
		),

	);


	private static $tipo_email = array(
		'validador'    => 'Valida la oc',
		'destinatario' => 'Envio simple',
		'copia'        => 'Enviar copia',
		'copia oculta' => 'Enviar copia oculta',
		'pago'		   => 'Notificar pago de factura'
	);

	public function afterFind($results, $primary = false)
	{


		# Convertimos meta emails en un "Modelo"
		foreach ($results as $key => $val) {

			if (isset($val['Proveedor']['meta_emails'])) {
				$results[$key]['Proveedor']['meta_emails'] = json_decode($results[$key]['Proveedor']['meta_emails'], true);
			}
		}
		return $results;
	}


	public function obtener_tipo_email($tipo = '')
	{
		if (!empty($tipo)) {
			return self::$tipo_email[$tipo];
		} else {
			return self::$tipo_email;
		}
	}


	/**
	 * permite_api_oc
	 *
	 * @param  mixed $id
	 * @return void
	 */
	public function permite_api_oc($id)
	{
		return $this->find('count', array(
			'conditions' => array(
				'Proveedor.id' => $id,
				'Proveedor.oc_via_api' => 1
			)
		));
	}
	
	/**
	 * actualizar_tiempo_despacho_proveedor
	 *   Se calcula y actualiza la moda de cada proveedor y se le configura en el atributo tiempo_despacho
	 *
	 * @param  int $mes
	 * @param  bool $excepto_manual
	 * @return bool
	 */
	public function actualizar_tiempo_despacho_proveedor($mes = 1, $excepto_manual = 1)
	{
		$tienda_id = ClassRegistry::init('Tienda')->tienda_principal()['Tienda']['id'];
		// * TIMESTAMPDIFF(DAY, vd.created, rbvdp.fecha) => La diferencia en dias entre ka creacion de la venta y la rececpion de un producto de la OC
		// * select count(id) from rp_feriados where feriado BETWEEN vd.created and rbvdp.fecha) => si existen dias feriados entre las fechas anterior se cuentan y luego se restan a la cantidad anterior
		/*	
			(select TIMESTAMPDIFF(DAY, vd.created, rbvdp.fecha) - (select count(id) from rp_feriados where feriado BETWEEN vd.created and rbvdp.fecha) 
						tiempo_despacho	
						from rp_venta_detalles vd
									inner join rp_orden_compras_ventas rocv
											on vd.venta_id = rocv.venta_id
									inner join rp_orden_compras roc on rocv.orden_compra_id = roc.id
									inner join rp_bodegas_venta_detalle_productos rbvdp on roc.id = rbvdp.orden_compra_id
						where roc.estado = 'recepcion_completa'
							and roc.proveedor_id = Proveedor.id
							and TIMESTAMPDIFF(MONTH, rbvdp.fecha, Now()) = $mes
						group by tiempo_despacho
						order by count(*) desc
						limit 1
		*/ //** Sacamos la moda => Se agrupan las diferencias de días y se ordena de mayor a menos, luego solo obtemos el primero. Esa es la moda del proveedor...
		
		// * (select rt.tiempo_despacho from rp_tiendas rt where rt.id = $tienda_id) => Si el proveedor no tiene moda se setea un valor por defecto que viene de la tienda principal

		//* where = Proveedor.despacho_manual != $excepto_manual => según la condicion incluimos a los proveedores que tengan despacho manual.
		
		$actualizar_tiempo_despacho_proveedor = $this->query("
		select Proveedor.id
			, Proveedor.nombre
			, ifnull((select TIMESTAMPDIFF(DAY, vd.created, rbvdp.fecha) - (select count(id) from rp_feriados where feriado BETWEEN vd.created and rbvdp.fecha) 
					tiempo_despacho	
					from rp_venta_detalles vd
								inner join rp_orden_compras_ventas rocv
										on vd.venta_id = rocv.venta_id
								inner join rp_orden_compras roc on rocv.orden_compra_id = roc.id
								inner join rp_bodegas_venta_detalle_productos rbvdp on roc.id = rbvdp.orden_compra_id
					where roc.estado = 'recepcion_completa'
						and roc.proveedor_id = Proveedor.id
						and TIMESTAMPDIFF(MONTH, rbvdp.fecha, Now()) = $mes
					group by tiempo_despacho
					order by count(*) desc
					limit 1),(select rt.tiempo_despacho from rp_tiendas rt where rt.id = $tienda_id)) tiempo_despacho
		from rp_proveedores Proveedor
		where Proveedor.despacho_manual != $excepto_manual
 		");

		$data = array_map(function ($data) {
			return ['Proveedor' => [
				'id' 				=> $data['Proveedor']['id'],
				'tiempo_despacho' 	=> $data[0]['tiempo_despacho'],
			]];
		}, $actualizar_tiempo_despacho_proveedor);

		$this->saveAll($data);

		return true;
		
	}
}
