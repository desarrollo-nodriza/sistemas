<?php 

App::uses('Controller', 'Controller');
App::uses('OrdenCompraFacturasController', 'Controller');

class RecepcionarComprasShell extends AppShell {

	public function main() 
	{	
        
		$conf = ClassRegistry::init('Tienda')->tienda_principal(
			array(
				'Tienda.sii_public_key',
				'Tienda.sii_private_key',
				'Tienda.libredte_token'
			)
		);

		$log = array();

		$log[] = array('Log' => array(
			'administrador' => 'Demonio',
			'modulo' => 'DteCompra',
			'modulo_accion' => 'Inicia proceso de acuse de recibo: ' . date('Y-m-d H:i:s')
		));

		if (empty($conf['Tienda']['sii_public_key'])
			|| empty($conf['Tienda']['sii_private_key'])
			|| empty($conf['Tienda']['libredte_token'])) {
			
                $log[] = array('Log' => array(
				'administrador' => 'Demonio',
				'modulo' => 'DteCompra',
				'modulo_accion' => 'Error: La tienda no está configurada para recepcionar las compras desde el SII.'
			));

			ClassRegistry::init('Log')->saveMany($log);

			return;
		}

		# Buscamos las facturas que no han sido recepcionadas
        $dtes = ClassRegistry::init('OrdenCompraFactura')->find('all', array(
            'conditions' => array(
                'OrdenCompraFactura.tipo_documento' => 33 // Sólo facturas
            ),
            'joins' => array(
                array(
					'table' => 'dte_compras',
					'alias' => 'DteCompra',
					'type'  => 'INNER',
					'conditions' => array(
                        'OrdenCompraFactura.folio = DteCompra.folio',
                        'OrdenCompraFactura.emisor = DteCompra.rut_emisor',
                        'DteCompra.estado' => 'PENDIENTE'
                    )
                ),
                array(
					'table' => 'proveedores',
					'alias' => 'pp',
					'type'  => 'inner',
					'conditions' => array(
                        'OrdenCompraFactura.proveedor_id = pp.id',
                        'pp.aceptar_dte' => 1
                    )
				)
            ),
            'contain' => array(
                'Proveedor' => array(
                    'fields' => array(
                        'Proveedor.id',
                        'Proveedor.aceptar_dte',
						'Proveedor.margen_aceptar_dte'
					)
				),
				'OrdenCompra' => array(
					'fields' => array(
						'OrdenCompra.id',
						'OrdenCompra.tienda_id',
						'OrdenCompra.bodega_id'
					),
					'Tienda' => array(
						'fields' => array(
							'Tienda.id', 
							'Tienda.rut'
						)
					),
					'Bodega' => array(
						'fields' => array(
							'Bodega.id',
							'Bodega.nombre'
						)
					)
				)
            ),
            'fields' => array(
                'OrdenCompraFactura.id',
                'OrdenCompraFactura.folio',
                'OrdenCompraFactura.proveedor_id',
				'OrdenCompraFactura.orden_compra_id',
				'OrdenCompraFactura.monto_facturado',
                'DteCompra.*'
            )
        ));

		$controller = new OrdenCompraFacturasController();
		$result = $controller->recepcionar_dte_compra($conf['Tienda']['libredte_token'], $conf['Tienda']['sii_public_key'], $conf['Tienda']['sii_private_key'], $dtes);
        
		
		return true;
	}


}