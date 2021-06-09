<?php

Class Zonificacion extends AppModel {

	/**
	 * Set Cake config DB
	 */
    public $useDbConfig = 'warehouse';
	public $useTable = 'zonificaciones';
	public $primaryKey = 'id';

	public $belongsTo = array(
        'Ubicacion' => array(
            'className' => 'Ubicacion',
            'foreignKey' => 'ubicacion_id'
        ),
        'VentaCliente' => array(
            'className' => 'VentaCliente',
            'foreignKey' => 'responsable_id'
        ),
        'VentaDetalleProducto' => array(
            'className' => 'VentaDetalleProducto',
            'foreignKey' => 'producto_id'
        )
    );


    public function crearEntradaParcialZonificacion($embalaje_id, $producto_id, $movimiento)
	{

        $embalaje_producto = ClassRegistry::init('EmbalajeProductoWarehouse')->find('first',[
            'conditions'=>[
                'embalaje_id'   => $embalaje_id,
                'producto_id'   => $producto_id
            ],
            'contain' => [
                'EmbalajeWarehouse' 
                ]
            ,
        ]);

        $persistir = [];
        if ($embalaje_producto) {

            if ($embalaje_producto['EmbalajeProductoWarehouse']['cantidad_embalada']!= 0) {

                $producto_id        =$embalaje_producto['EmbalajeProductoWarehouse']['producto_id'];
                
                $zonificaciones     = ClassRegistry::init('Zonificacion')->find('all',[
                    'fields' => array('Zonificacion.* , SUM(cantidad) as cantidad'),
                    'conditions'=>[
                        'embalaje_id'   => $embalaje_id,
                        'producto_id'   => $producto_id,
                        'movimiento'    => 'embalaje'
                    ],
                    'group' => array('ubicacion_id'),
                ]);

                $date = date("Y-m-d H:i:s");
                
                // Se recorre las zonificaciones de donde se saco un mismo producto
                foreach ($zonificaciones as $zonificacion) {

                    $ubicacion_id       = $zonificacion['Zonificacion']['ubicacion_id'];
                    $cantidad           = $zonificacion[0]['cantidad']*-1;
                    $validar_movimiento = $this->ValidarPorMovimiento($embalaje_id,$producto_id,$ubicacion_id,$cantidad);

                    if ($validar_movimiento) {
                        $persistir [] =
                        [
                            "ubicacion_id"          => $ubicacion_id,
                            "producto_id"           => $producto_id,
                            "cantidad"              => $cantidad,
                            "responsable_id"        => $zonificacion['Zonificacion']['responsable_id'],
                            "embalaje_id"           => $embalaje_id,
                            "movimiento"            => $movimiento,
                            "fecha_creacion"        => $date,
                            "ultima_modifacion"     => $date
                        ]; 
                    }
                
                }
            }
        }

        // Se zonifican los productos

        if ($persistir) {
            ClassRegistry::init('Zonificacion')->create();
            if (ClassRegistry::init('Zonificacion')->saveMany($persistir))
            {
                return $persistir;
                
            }
        }
        
        return $persistir;

	}

    public function crearEntradaVentaCanceladaZonificacion($embalaje_id)
	{

        $embalaje_productos = ClassRegistry::init('EmbalajeProductoWarehouse')->find('all',[
            'conditions'=>[
                'embalaje_id'   => $embalaje_id,
            ]
        ]);
        $persistir = [];
        // Se recorre cada producto del embalaje
        foreach ($embalaje_productos as $embalaje_producto) {
            
            // se valida que el producto haya sido embalado
            if ($embalaje_producto['EmbalajeProductoWarehouse']['cantidad_embalada']!= 0) {
                
                $producto_id        =$embalaje_producto['EmbalajeProductoWarehouse']['producto_id'];
                
                $zonificaciones     = ClassRegistry::init('Zonificacion')->find('all',[
                    'fields' => array('Zonificacion.* , SUM(cantidad) as cantidad'),
                    'conditions'=>[
                        'embalaje_id'   => $embalaje_id,
                        'producto_id'   => $producto_id,
                        'movimiento'    => 'embalaje'
                    ],
                    'group' => array('ubicacion_id'),
                ]);

                $date = date("Y-m-d H:i:s");
                
                // Se recorre las zonificaciones de donde se saco un mismo producto
                foreach ($zonificaciones as $zonificacion) {

                    $ubicacion_id       = $zonificacion['Zonificacion']['ubicacion_id'];
                    $cantidad           = $zonificacion[0]['cantidad']*-1;
                    $validar_movimiento = $this->ValidarPorMovimiento($embalaje_id,$producto_id,$ubicacion_id,$cantidad);

                    if ($validar_movimiento) {
                        $persistir [] =
                        [
                            "ubicacion_id"          => $ubicacion_id,
                            "producto_id"           => $producto_id,
                            "cantidad"              => $cantidad,
                            "responsable_id"        => $zonificacion['Zonificacion']['responsable_id'],
                            "embalaje_id"           => $embalaje_id,
                            "movimiento"            => 'venta_cancelada',
                            "fecha_creacion"        => $date,
                            "ultima_modifacion"     => $date
                        ]; 
                    }
                
                }
               
            }
            
        }
        
        // Se zonifican los productos
        if ($persistir) {
            ClassRegistry::init('Zonificacion')->create();
            if (ClassRegistry::init('Zonificacion')->saveMany($persistir))
            {
                return $persistir;
                
            }
        }
        
        return $persistir;


	}

    private function ValidarPorMovimiento($embalaje_id,$producto_id,$ubicacion_id,$cantidad)
    {
        $venta_cancelada = ClassRegistry::init('Zonificacion')->find('all',[
            'fields' => array('Zonificacion.*,  SUM(cantidad) as cantidad'),
            'conditions'=>[
                'embalaje_id'   => $embalaje_id,
                'producto_id'   => $producto_id,
                'ubicacion_id'  => $ubicacion_id,
                'movimiento'    =>'venta_cancelada'
            ],
            'group' => array('ubicacion_id'),
        ]);

        $devolucion = ClassRegistry::init('Zonificacion')->find('all',[
            'fields' => array('Zonificacion.*,  SUM(cantidad) as cantidad'),
            'conditions'=>[
                'embalaje_id'   => $embalaje_id,
                'producto_id'   => $producto_id,
                'ubicacion_id'  => $ubicacion_id,
                'movimiento'    =>'devolucion'
              
            ],
            'group' => array('ubicacion_id'),
        ]);

        $garantia = ClassRegistry::init('Zonificacion')->find('all',[
            'fields' => array('Zonificacion.*,  SUM(cantidad) as cantidad'),
            'conditions'=>[
                'embalaje_id'   => $embalaje_id,
                'producto_id'   => $producto_id,
                'ubicacion_id'  => $ubicacion_id,
                'movimiento'    =>'garantia'
                
            ],
            'group' => array('ubicacion_id'),
        ]);
        
        $validar_movimiento = true;
        
        // Se valida que no se hayan devuelto stock
        if ($venta_cancelada) {
            $venta_cancelada = $venta_cancelada[0];
            if ($venta_cancelada['Zonificacion']['cantidad'] == ($cantidad)) {
               $validar_movimiento = false;
            }
        }

        if ($garantia) {
            $garantia = $garantia[0];
            if ($garantia['Zonificacion']['cantidad'] == ($cantidad)) {
                $validar_movimiento = false;
            }
        }

        if ($devolucion) {
            $devolucion = $devolucion[0];
            if ($devolucion['Zonificacion']['cantidad'] == ($cantidad)) {
                $validar_movimiento = false;
            }
        }

        return $validar_movimiento;
    }

	
	
}