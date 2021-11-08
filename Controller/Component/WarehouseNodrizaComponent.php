<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'WarehouseNodriza', array('file' => 'WarehouseNodriza/WarehouseNodriza.php'));

class WarehouseNodrizaComponent extends Component
{
    private $WarehouseNodriza;
   

    public function crearCliente()
    {
        $BX_TOKEN = ClassRegistry::init('Token')->crear_token(CakeSession::read('Auth.Administrador.id') ?? 1);
        $this->WarehouseNodriza = new WarehouseNodriza($BX_TOKEN['token'] ?? '', Configure::read('ambiente'));
    }

    public function CambiarCancelado_V2($venta_id, $responsable_id_cancelado, $devolucion, $motivo_cancelado)
    {

        $this->crearCliente();
        return $this->WarehouseNodriza->CambiarCancelado_V2($venta_id, $responsable_id_cancelado, $devolucion, $motivo_cancelado);
    }

    public function RecrearEmbalajesPorItemAnulados($venta_id)
    {

        $bodega = ClassRegistry::init('Bodega')->obtener_bodega_principal();

        $venta = ClassRegistry::init('Venta')->find('first', array(
            'conditions' => array(
                'Venta.id' => $venta_id
            ),
            'contain' => array(
                'VentaDetalle' => array(
                    'fields' => array(
                        'VentaDetalle.venta_detalle_producto_id as producto_id',
                        'VentaDetalle.cantidad as cantidad_a_embalar',
                        'VentaDetalle.cantidad_anulada',
                        'VentaDetalle.id as detalle_id',
                    )
                )
            ),
            'fields' => array(
                'Venta.id as venta_id',
                'Venta.marketplace_id',
                'Venta.metodo_envio_id',
                'Venta.comuna_id',
                'Venta.prioritario',
                'Venta.fecha_venta'
            )
        ));

        $nuevo_embalaje                = $venta['Venta'];
        $nuevo_embalaje['responsable'] = CakeSession::read('Auth.Administrador.id') ?? 1;
        $nuevo_embalaje['bodega_id']   = $bodega['Bodega']['id'];
        $nuevo_embalaje['productos']   = $venta['VentaDetalle'];
        $nuevo_embalaje                = array_filter($nuevo_embalaje);

        $this->crearCliente();
        return $this->WarehouseNodriza->RecrearEmbalajesPorItemAnulados($nuevo_embalaje);
    }

    public function CambiarCancelado($embalaje_id, $motivo_cancelado)
    {

        $this->crearCliente();
        $embalajes[] = [
            'id'                       => $embalaje_id,
            'responsable_id_cancelado' => CakeSession::read('Auth.Administrador.id') ?? 1,
            'motivo_cancelado'         => $motivo_cancelado
        ];
        return $this->WarehouseNodriza->CambiarCancelado($embalajes);
    }


    public function procesar_embalajes($id)
    {

        $logs = array();
        $venta = ClassRegistry::init('Venta')->find('first', array(
            'conditions' => array(
                'Venta.id' => $id
            ),
            'contain' => array(
                'VentaDetalle' => array(
                    'EmbalajeProductoWarehouse' => array(
                        'EmbalajeWarehouse'
                    )
                )
            ),
            'fields' => array(
                'Venta.id',
                'Venta.metodo_envio_id',
                'Venta.marketplace_id',
                'Venta.comuna_id',
                'Venta.fecha_venta',
                'Venta.venta_estado_id',
                'Venta.administrador_id',
                'Venta.picking_estado',
                'Venta.prioritario'
            )
        ));

        $logs[] = array(
            'Log' => array(
                'administrador' => 'Inicia embalaje venta ' . $id,
                'modulo'        => 'WarehouseNodrizaComponent',
                'modulo_accion' => json_encode($venta)
            )
        );

        $bodega = ClassRegistry::init('Bodega')->obtener_bodega_principal();

        $dte_valido = ClassRegistry::init('Dte')->obtener_dte_valido_venta($id);
        // prx([$venta, $dte_valido]);
        switch ($venta['Venta']['picking_estado']) {
            case 'no_definido':

                # si hay un embalaje creado se cancela siempre y cuando sean las 
                # unidades del embalaje las que se quitan de la reserva
                $response = $this->RecrearEmbalajesPorItemAnulados($id);
                $logs[] = array(
                    'Log' => array(
                        'administrador' => "Recrear embalajes vid {$id}",
                        'modulo'        => 'WarehouseNodrizaComponent',
                        'modulo_accion' => json_encode(['Respuesta warehouse: ' => $response])
                    )
                );
                break;

            case 'empaquetar':

                # si el estado de la venta no es pagado no pasa
                if (!ClassRegistry::init('VentaEstado')->es_estado_pagado($venta['Venta']['venta_estado_id'])) {
                    break;
                }

                # Embalaje

                $embalaje = [
                    'venta_id'         => $venta['Venta']['id'],
                    'bodega_id'        => $bodega['Bodega']['id'],
                    'metodo_envio_id'  => $venta['Venta']['metodo_envio_id'],
                    'comuna_id'        => $venta['Venta']['comuna_id'],
                    'prioritario'      => ($venta['Venta']['prioritario']) ? 1 : 0,
                    'fecha_venta'      => $venta['Venta']['fecha_venta'],
                    'responsable'      => CakeSession::read('Auth.Administrador.id') ?? 1,
                    'productos'        => []

                ];
                if (isset($venta['Venta']['marketplace_id'])) {
                    $embalaje['marketplace_id'] = $venta['Venta']['marketplace_id'];
                }

                # Asignamos los productos al embalaje
                foreach ($venta['VentaDetalle'] as $d) {

                    $cantidad_a_embalar = $d['cantidad_reservada'];

                    if (!empty($d['EmbalajeProductoWarehouse'])) {
                        foreach ($d['EmbalajeProductoWarehouse'] as $emp) {

                            if ($emp['EmbalajeWarehouse']['estado'] == 'cancelado' || $emp['EmbalajeWarehouse']['estado'] == 'finalizado') {
                                continue;
                            }

                            $cantidad_a_embalar = $cantidad_a_embalar - $emp['cantidad_a_embalar'];
                        }
                    }

                    # Agregamos el item al nuevo embalaje
                    if ($cantidad_a_embalar > 0) {
                        $embalaje['productos'][] = array(
                            'producto_id' => $d['venta_detalle_producto_id'],
                            'detalle_id' => $d['id'],
                            'cantidad_a_embalar' => $cantidad_a_embalar
                        );
                    }
                }
                // prx([$embalaje, $dte_valido]);

                # si hay productos para embalar y tiene dte válido pasa a embalaje
                if (!empty($embalaje['productos']) && $dte_valido) {

                    $response = $this->CrearPedido($embalaje);

                    $logs[] = array(
                        'Log' => array(
                            'administrador' => 'Crear embalaje venta ' . $id,
                            'modulo'        => 'WarehouseNodrizaComponent',
                            'modulo_accion' => json_encode(['Respuesta warehouse: ' => $response])
                        )
                    );
                }

                break;

            case 'empaquetando':

                break;
            case 'empaquetado':

                break;
        }

        ClassRegistry::init('Log')->saveMany($logs);

        return;
    }

    public function OrdenTransporteEmbalajes($orden_transporte)
    {
        $this->crearCliente();
        return $this->WarehouseNodriza->OrdenTransporteEmbalajes($orden_transporte);
    }

    public function CrearPedido($embalaje)
    {
        $this->crearCliente();
        return $this->WarehouseNodriza->CrearPedido($embalaje);
    }
}
