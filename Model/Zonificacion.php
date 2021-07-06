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
            'className'     => 'Ubicacion',
            'foreignKey'    => 'ubicacion_id'
        ),
        'Administrador' => array(
            'className'     => 'Administrador',
            'foreignKey'    => 'responsable_id'
        ),
        'VentaDetalleProducto' => array(
            'className'     => 'VentaDetalleProducto',
            'foreignKey'    => 'producto_id'
        )
    );
    
    
    public function crearEntradaParcialZonificacion($venta_id, $producto_id, $movimiento, $cantidad_devolver)
	{
        
        if ($cantidad_devolver < 1) {
           return  [];
        }

        $embalaje_productos = ClassRegistry::init('EmbalajeProductoWarehouse')->find('all',[
            'conditions' => array(
                'EmbalajeWarehouse.venta_id'            => $venta_id,
                'EmbalajeProductoWarehouse.producto_id' => $producto_id
            ),
            'contain' => array(
                'EmbalajeWarehouse'
            ),
            'fields' => array(
                'EmbalajeProductoWarehouse.id',
                'EmbalajeProductoWarehouse.cantidad_embalada',
                'EmbalajeWarehouse.id',
                'EmbalajeProductoWarehouse.producto_id'                        
            ),
            'order' => 'EmbalajeProductoWarehouse.cantidad_embalada desc'
        ]);

        return $this->PersistirDataV2($embalaje_productos,$movimiento,$cantidad_devolver);

	}

    public function crearEntradaVentaCanceladaZonificacion($embalaje_id)
	{
        
        $embalaje_productos = ClassRegistry::init('EmbalajeProductoWarehouse')->find('all',[
            'conditions' => array(
                'EmbalajeWarehouse.id'=> $embalaje_id,
            ),
            'contain' => array(
                'EmbalajeWarehouse'
            ),
            'fields' => array(
                'EmbalajeProductoWarehouse.id',
                'EmbalajeProductoWarehouse.cantidad_embalada',
                'EmbalajeWarehouse.id',
                'EmbalajeProductoWarehouse.producto_id'                        
            ),
            'order' => 'EmbalajeProductoWarehouse.cantidad_embalada desc'
        ]);

        return $this->PersistirDataV2($embalaje_productos,'venta_cancelada');

        

	}
    
    private function PersistirDataV2($embalaje_productos, $movimiento, $cantidad_devolver = null)
    {
        $persistir = [];
        $persistirEmbalajeProductoWarehouse = [];
        $leer_cantidad_embalada = true;
        
        if (!is_null($cantidad_devolver)) {
            $leer_cantidad_embalada = false;
            if ($cantidad_devolver < 1) {
                return  $persistir;
            }
        }
       

        if ($embalaje_productos) {

            foreach ($embalaje_productos as $embalaje_producto) {
               
                
                if ($leer_cantidad_embalada) {
                    $cantidad_devolver = $embalaje_producto['EmbalajeProductoWarehouse']['cantidad_embalada'];
                }
                
                // Verifica que el producto haya sido embalado
                if ($embalaje_producto['EmbalajeProductoWarehouse']['cantidad_embalada']!= 0) {
                    
                    $cantidad_ya_devuelta   = 0;
                    $embalaje_id            = $embalaje_producto['EmbalajeWarehouse']['id'];
                    
                    $zonificaciones = ClassRegistry::init('Zonificacion')->find('all',[
                        'fields' => array('*' ,'SUM(cantidad) as cantidad'),
                        'conditions'=>[
                            'embalaje_id'   => $embalaje_id,
                            'producto_id'   => $embalaje_producto['EmbalajeProductoWarehouse']['producto_id'],
                            'movimiento'    => ['embalaje','devolucion','venta_cancelada','garantia']
                        ],
                        'contain' => array(
                                     'Ubicacion' => 'Zona'
                                ),
                        'group' => array('producto_id'),
                        'order' => 'cantidad asc'
                    ]);
                    
                    $date = date("Y-m-d H:i:s");

                    // Se recorre las zonificaciones de donde se saco un mismo producto
                    foreach ($zonificaciones as $zonificacion) {

                        // Si ya se devolvieron cantidad a devolver se rompe el flujo y no sigue
                        if ($cantidad_devolver == 0) {
                            break;
                        }
                        
                        $bodega_id = $zonificacion['Ubicacion']['Zona']['bodega_id'];

                        $ubicacion_id = ClassRegistry::init('Ubicacion')->find('first',[
                            'fields' => array('Ubicacion.id'),
                            'conditions'=>[
                                'Zona.bodega_id'        => $bodega_id ,
                                'Ubicacion.devolucion'  => true
                            ],
                            'contain' => array('Zona')
                        ]);

                        

                        if ($ubicacion_id) {

                            $ubicacion_id       = $ubicacion_id['Ubicacion']['id'];

                        }else {

                            $ubicacion_id       = $zonificacion['Zonificacion']['ubicacion_id'];
                        }

                        // Si la cantidad a devolver es menor a la embalada se considera cantidad_devolver
                        if ($cantidad_devolver  < ($zonificacion[0]['cantidad']*-1)) {

                            $cantidad           = $cantidad_devolver;
                            $cantidad_devolver  = $cantidad_devolver - $cantidad_devolver;
                        }else {

                            $cantidad_devolver  = $cantidad_devolver + $zonificacion[0]['cantidad'];
                            $cantidad           = $zonificacion[0]['cantidad']*-1;
                        }

                        
                        $cantidad_ya_devuelta = $cantidad_ya_devuelta + $cantidad;
                        
                        $persistir [] =
                        [
                            "ubicacion_id"          => $ubicacion_id,
                            "producto_id"           => $embalaje_producto['EmbalajeProductoWarehouse']['producto_id'],
                            "cantidad"              => $cantidad,
                            "responsable_id"        => $zonificacion['Zonificacion']['responsable_id'],
                            "embalaje_id"           => $embalaje_id,
                            "movimiento"            => $movimiento,
                            "fecha_creacion"        => $date,
                            "ultima_modifacion"     => $date
                        ]; 
                    }

                    $persistirEmbalajeProductoWarehouse []= 
                    [
                        'id'                => $embalaje_producto['EmbalajeProductoWarehouse']['id'],
                        'cantidad_embalada' => ($embalaje_producto['EmbalajeProductoWarehouse']['cantidad_embalada'] - $cantidad_ya_devuelta)
                    ];
                }
                
            }
        }
       
        // Se zonifican los productos

        if ($persistir) {
            ClassRegistry::init('Zonificacion')->create();
            if (ClassRegistry::init('Zonificacion')->saveMany($persistir))
            {

                // En EmbalajeProductoWarehouse se inidica cantidad_embalada resultante
                foreach ($persistirEmbalajeProductoWarehouse as $EmbalajeProductoWarehouse) {
                    ClassRegistry::init('EmbalajeProductoWarehouse')->id = $EmbalajeProductoWarehouse['id'];
                    if (ClassRegistry::init('EmbalajeProductoWarehouse')->save(['cantidad_embalada' => $EmbalajeProductoWarehouse['cantidad_embalada']])) {

                    }
                }
                return $persistir;
            }
        }
        
        return $persistir;
    }

}