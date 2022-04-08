<?php

App::uses('Component', 'Controller');
App::import('Vendor', 'WarehouseNodriza', array('file' => 'WarehouseNodriza/WarehouseNodriza.php'));

class WarehouseNodrizaComponent extends Component
{
    private $WarehouseNodriza;


    public function crearCliente()
    {

        $BX_TOKEN = !empty(CakeSession::read('Auth.Administrador.token.token')) ? ['token' => CakeSession::read('Auth.Administrador.token.token')]  : ClassRegistry::init('Token')->crear_token(CakeSession::read('Auth.Administrador.id') ?? 1);
        $this->WarehouseNodriza = new WarehouseNodriza($BX_TOKEN['token'] ?? '', Configure::read('ambiente'));
        // $this->WarehouseNodriza = new WarehouseNodriza($BX_TOKEN['token'] ?? '', 'local');
    }

    public function CambiarCancelado_V2($venta_id, $responsable_id_cancelado, $devolucion, $motivo_cancelado)
    {

        $this->crearCliente();
        return $this->WarehouseNodriza->CambiarCancelado_V2($venta_id, $responsable_id_cancelado, $devolucion, $motivo_cancelado);
    }

    public function RecrearEmbalajesPorItemAnulados($venta_id)
    {

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
                'Venta.fecha_venta',
                'Venta.bodega_id'
            )
        ));

        $nuevo_embalaje                = $venta['Venta'];
        $nuevo_embalaje['responsable'] = CakeSession::read('Auth.Administrador.id') ?? 1;
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
            'motivo_cancelado'         => $motivo_cancelado,
            'devolucion'               => true
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
                    ),
                    'VentaDetallesReserva' => [
                        'fields' => [
                            'VentaDetallesReserva.venta_detalle_id',
                            'VentaDetallesReserva.venta_detalle_producto_id',
                            'VentaDetallesReserva.cantidad_reservada',
                            'VentaDetallesReserva.bodega_id'
                        ]
                    ]
                ),
                'MetodoEnvio' => ['fields' => ['MetodoEnvio.retiro_local']],
                'Bodega'  => ['fields' => ['Bodega.nombre', 'Bodega.comuna_id']],
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
                'Venta.prioritario',
                'Venta.bodega_id',
                'Venta.nota_interna'
            )
        ));


        $logs[] = array(
            'Log' => array(
                'administrador' => "Procesando vid $id para Warehouse",
                'modulo'        => 'WarehouseNodrizaComponent',
                'modulo_accion' => json_encode($venta)
            )
        );

        switch ($venta['Venta']['picking_estado']) {

            case 'no_definido':
                # si hay un embalaje creado se cancela siempre y cuando sean las 
                # unidades del embalaje las que se quitan de la reserva
                $response = $this->CambiarCancelado_V2($id, CakeSession::read('Auth.Administrador.id') ?? 1, true, "La VID {$venta['Venta']['id']} ha sido cancelada.");
                $logs[] = array(
                    'Log' => array(
                        'administrador' => "Cancelar embalajes vid {$id} en Warehouse",
                        'modulo'        => 'WarehouseNodrizaComponent',
                        'modulo_accion' => json_encode(['Respuesta warehouse: ' => $response])
                    )
                );
                break;

            case 'empaquetar':

                $bodega_principal   = ClassRegistry::init('Bodega')->obtener_bodega_principal()['Bodega']['id'];
                $dte_valido         = ClassRegistry::init('Dte')->obtener_dte_valido_venta($id);

                # si el estado de la venta no es pagado no pasa
                if (!ClassRegistry::init('VentaEstado')->es_estado_pagado($venta['Venta']['venta_estado_id'])) {
                    break;
                }

                $bodegas_activas = ClassRegistry::init('Bodegas')->find(
                    'list',
                    ['conditions' => ['Bodegas.activo' => true]]
                );

                $reservas_separadas_por_bodega = [];

                // * Extraemos solo los productos que fueron reservados en otras bodegas
                foreach ($bodegas_activas as $key => $value) {
                    $reservas_separadas_por_bodega[$key] = Hash::extract($venta['VentaDetalle'], "{n}.VentaDetallesReserva.{n}[bodega_id={$key}]");
                }

                // * Ya que se consultan todas las bodegas se filtra aquellas bodegas que no tuvieron reserva en stock, para recorrer solo las que corresponde
                $reservas_separadas_por_bodega = array_filter($reservas_separadas_por_bodega);

                $logs[] = array(
                    'Log' => array(
                        'administrador' => "Reservas por bodega",
                        'modulo'        => 'WarehouseNodrizaComponent',
                        'modulo_accion' => json_encode($reservas_separadas_por_bodega)
                    )
                );

                // * Al recorrer se crean embalajes de acuerdo a la bodega
                foreach ($reservas_separadas_por_bodega as $bodega_id => $productos_por_bodegas) {
                    $embalaje = [];


                    if ($venta['MetodoEnvio']['retiro_local']) {
                        // * Si la bodega del embalaje es distinta al de la venta con "retiro en tienda", se solicita que el embalaje sea trasladado para su eventual retiro.

                        $trasladar_a_otra_bodega    = $bodega_id != $venta['Venta']['bodega_id'];
                        $bodega_id_para_trasladar   = ($bodega_id != $venta['Venta']['bodega_id'] ? $venta['Venta']['bodega_id'] : null);
                    } else {
                        // * Si la bodega del embalaje es creada en otra bodega que no sea la principal y la venta posee metodo de envio con "despacho a domicilio", debe ser trasladado a la bodega principal

                        $trasladar_a_otra_bodega    = $bodega_principal != $bodega_id;
                        $bodega_id_para_trasladar   = ($bodega_principal != $bodega_id ? $bodega_principal : null);
                    }

                    $embalaje = [
                        'venta_id'                  => $venta['Venta']['id'],
                        'bodega_id'                 => $bodega_id,
                        'trasladar_a_otra_bodega'   => $trasladar_a_otra_bodega,
                        'bodega_id_para_trasladar'  => $bodega_id_para_trasladar,
                        'metodo_envio_id'           => $venta['Venta']['metodo_envio_id'],
                        'comuna_id'                 => $venta['Venta']['comuna_id']  ?? $venta['Bodega']['comuna_id'],
                        'prioritario'               => ($venta['Venta']['prioritario']) ? 1 : 0,
                        'fecha_venta'               => $venta['Venta']['fecha_venta'],
                        'responsable'               => CakeSession::read('Auth.Administrador.id') ?? 1,
                        'productos'                 => []
                    ];

                    if (isset($venta['Venta']['marketplace_id'])) {
                        $embalaje['marketplace_id'] = $venta['Venta']['marketplace_id'];
                    }

                    // ! Verificamos Si el total del producto ya fue embalada, si falta, se crea un embalaje con lo que falta
                    foreach ($productos_por_bodegas as $d) {

                        $cantidad_a_embalar = $d['cantidad_reservada'] - (array_sum(Hash::extract($venta['VentaDetalle'], "{n}.EmbalajeProductoWarehouse.{n}[detalle_id={$d['venta_detalle_id']}].cantidad_a_embalar")) - array_sum(Hash::extract($venta['VentaDetalle'], "{n}.EmbalajeProductoWarehouse.{n}[detalle_id={$d['venta_detalle_id']}].cantidad_embalada")));

                        # Agregamos el item al nuevo embalaje
                        if ($cantidad_a_embalar > 0) {

                            $embalaje['productos'][] = array(
                                'producto_id'        => $d['venta_detalle_producto_id'],
                                'detalle_id'         => $d['venta_detalle_id'],
                                'cantidad_a_embalar' => $cantidad_a_embalar,
                                'fecha_creacion'     => date('Y-m-d H:i:s'),
                                'ultima_modifacion'  => date('Y-m-d H:i:s')
                            );
                        }
                    }

                    # si hay productos para embalar y tiene dte válido pasa a embalaje
                    if (!empty($embalaje['productos']) && $dte_valido) {

                        $response = $this->CrearPedido($embalaje);

                        if ($response['code'] == 200) {

                            $logs[] = array(
                                'Log' => array(
                                    'administrador' => "Se han creado embalajes para la vid {$id} bodega {$embalaje['bodega_id']} en Warehouse",
                                    'modulo'        => 'WarehouseNodrizaComponent',
                                    'modulo_accion' => json_encode(['Respuesta warehouse: ' => $response])
                                )
                            );

                            if ($trasladar_a_otra_bodega) {

                                ClassRegistry::init('Bodega')->id   = $bodega_id_para_trasladar;
                                $nombre_bodega                      = ClassRegistry::init('Bodega')->field('nombre');
                                $embalaje_id                        = $response['response']['body']['id'];

                                try {

                                    $nota = [
                                        'venta_id'          => $venta['Venta']['id'],
                                        'nombre'            => "Trasladar",
                                        'descripcion'       => "El embalaje {$embalaje_id} requiere ser trasladado a la bodega {$nombre_bodega}.",
                                        'id_usuario'        => CakeSession::read('Auth.Administrador.id') ?? 1,
                                        'nombre_usuario'    => CakeSession::read('Auth.Administrador.nombre') ?? 'Automatico',
                                        'mail_usuario'      => CakeSession::read('Auth.Administrador.email') ?? "cristian.rojas@nodriza.cl",
                                        'embalajes'         => [
                                            ["id_embalaje" => $embalaje_id]
                                        ]
                                    ];

                                    $crearNotaDespacho = $this->crearNotaDespacho($nota);

                                    $logs[] = array(
                                        'Log' => array(
                                            'administrador' => "Crear Notas",
                                            'modulo'        => 'WarehouseNodrizaComponent',
                                            'modulo_accion' => json_encode(['Respuesta warehouse: ' => $crearNotaDespacho])
                                        )
                                    );

                                    if ($response['code'] != 200) {

                                        try {
                                            $nota = "{$venta['Venta']['nota_interna']} - El embalaje {$embalaje_id} requiere ser trasladado a la bodega {$nombre_bodega}.";
                                        } catch (\Throwable $th) {
                                            $nota = "{$venta['Venta']['nota_interna']} - El embalaje requiere ser trasladado a la bodega {$nombre_bodega}.";
                                        }

                                        ClassRegistry::init('Venta')->save([
                                            'Venta' =>
                                            [
                                                'nota_interna' => $nota,
                                                'id'           => $venta['Venta']['id']
                                            ]
                                        ]);
                                    }
                                } catch (\Throwable $th) {

                                    try {
                                        $nota = "{$venta['Venta']['nota_interna']} - El embalaje {$embalaje_id} requiere ser trasladado a la bodega {$nombre_bodega}.";
                                    } catch (\Throwable $th) {
                                        $nota = "{$venta['Venta']['nota_interna']} - El embalaje requiere ser trasladado a la bodega {$nombre_bodega}.";
                                    }

                                    ClassRegistry::init('Venta')->save([
                                        'Venta' =>
                                        [
                                            'nota_interna' => $nota,
                                            'id'           => $venta['Venta']['id']
                                        ]
                                    ]);
                                }
                            }
                        } else {

                            $logs[] = array(
                                'Log' => array(
                                    'administrador' => "No se han podido crear embalajes para la vid {$id} bodega {$embalaje['bodega_id']} en Warehouse",
                                    'modulo'        => 'WarehouseNodrizaComponent',
                                    'modulo_accion' => json_encode(['Respuesta warehouse: ' => $response])
                                )
                            );
                        };
                    } else {

                        $logs[] = array(
                            'Log' => array(
                                'administrador' => "no se logro crear embalajes para la vid {$id} bodega {$embalaje['bodega_id']} en Warehouse",
                                'modulo'        => 'WarehouseNodrizaComponent',
                                'modulo_accion' => json_encode(
                                    [
                                        'Existen productos para embalar?'   => !empty($embalaje['productos']) ? 'si' : 'no',
                                        'Productos'                         => $embalaje['productos'],
                                        'Existen DTE valido para embalar?'  => $dte_valido ? 'si' : 'no',
                                        'DTE'                               => $dte_valido,

                                    ]
                                )
                            )
                        );
                    }
                }

                break;

            case 'empaquetando':

                break;
            case 'empaquetado':

                break;

            default:

                $logs[] = array(
                    'Log' => array(
                        'administrador' => "Vid $id no requirío ser procesada",
                        'modulo'        => 'WarehouseNodrizaComponent',
                        'modulo_accion' => null
                    )
                );
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

    public function ObtenerEvidencia($embalaje)
    {
        $this->crearCliente();
        return $this->WarehouseNodriza->ObtenerEvidencia($embalaje);
    }

    public function CrearEntradaSalidaZonificacion($zonificacion)
    {
        $this->crearCliente();
        return $this->WarehouseNodriza->CrearEntradaSalidaZonificacion($zonificacion);
    }

    public function ObtenerEmbalajesVenta($venta_id)
    {
        $this->crearCliente();
        return $this->WarehouseNodriza->ObtenerEmbalajesVenta($venta_id);
    }


    public function ObtenerEmbalajesVentaV2($venta_id, $filtro = [])
    {
        $this->crearCliente();
        return $this->WarehouseNodriza->ObtenerEmbalajesVentaV2($venta_id, $filtro);
    }

    public function ObtenerNotasDespacho($filtro = [])
    {
        $this->crearCliente();
        return $this->WarehouseNodriza->ObtenerNotasDespacho($filtro);
    }

    public function crearNotaDespacho($data = [])
    {
        $this->crearCliente();
        return $this->WarehouseNodriza->crearNotaDespacho($data);
    }


    public function eliminarNotaDespacho($id)
    {
        $this->crearCliente();
        return $this->WarehouseNodriza->eliminarNotaDespacho($id);
    }

    public function CambiarEstadoAEnTrasladoABodega($embalaje_id, $responsable_id_en_traslado_a_bodega)
    {
        $body =
            [
                "id"                                    => $embalaje_id,
                "responsable_id_en_traslado_a_bodega"   => $responsable_id_en_traslado_a_bodega,
            ];
        $this->crearCliente();
        return $this->WarehouseNodriza->CambiarEstadoAEnTrasladoABodega($body);
    }
    public function RecepcionarEmbalajeTrasladado($embalaje_id, $responsable)
    {
        $body =
            [
                "id"            => $embalaje_id,
                "responsable"   => $responsable,
            ];
        $this->crearCliente();
        return $this->WarehouseNodriza->RecepcionarEmbalajeTrasladado($body);
    }
}
