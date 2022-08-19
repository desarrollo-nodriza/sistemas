<?php

/**
 * API
 */

Router::connect(
    '/api/productos', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaDetalleProductos', 
        'action' => 'index',
        'api' => true,
        'prefix' => 'api')
);


Router::connect(
    '/api/producto/add', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaDetalleProductos', 
        'action' => 'crear',
        'api' => true,
        'prefix' => 'api')
);


Router::connect(
    '/api/producto/test', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaDetalleProductos', 
        'action' => 'test',
        'api' => true,
        'prefix' => 'api')
);


Router::connect(
    '/api/producto/view/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaDetalleProductos', 
        'action' => 'view',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


Router::connect(
    '/api/producto/view-by-reference/:sku', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaDetalleProductos', 
        'action' => 'view_by_reference',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('sku'),
        'id' => '[0-9-a-Z]+'
    )
);
Router::connect(
    '/api/producto/v2/view-by-reference', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaDetalleProductos', 
        'action' => 'view_by_reference2',
        'api' => true,
        'prefix' => 'api')
);

Router::connect(
    '/api/producto/edit/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaDetalleProductos', 
        'action' => 'edit',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


Router::connect(
    '/api/producto/update/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaDetalleProductos', 
        'action' => 'update',
        '[method]' => 'POST',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


Router::connect(
    '/api/producto/delete/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaDetalleProductos', 
        'action' => 'delete',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/producto/stock_disponible/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaDetalleProductos', 
        'action' => 'recuperar_stock',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


/**
 * Marcas
 */
Router::connect(
    '/api/marca/add', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Marcas', 
        'action' => 'crear',
        'api' => true,
        'prefix' => 'api')
);


/**
 * Administrador
 */
Router::connect(
    '/api/administradores/test', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Administradores', 
        'action' => 'test',
        'api' => true,
        'prefix' => 'api')
);

Router::connect(
    '/api/administradores/auth', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Administradores', 
        'action' => 'login',
        'api' => true,
        'prefix' => 'api'
    )
);
Router::connect(
    '/api/administradores/v2/auth', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Administradores', 
        'action' => 'v2_login',
        'api' => true,
        'prefix' => 'api'
    )
);


Router::connect(
    '/api/administradores/google_auth', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Administradores', 
        'action' => 'google_auth',
        'api' => true,
        'prefix' => 'api'
    )
);

Router::connect(
    '/api/administradores/v2/google_auth', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Administradores', 
        'action' => 'v2_google_auth',
        'api' => true,
        'prefix' => 'api'
    )
);


Router::connect(
    '/api/administradores/userinfo', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Administradores', 
        'action' => 'obtener_usuario',
        'api' => true,
        'prefix' => 'api'
    )
);


Router::connect(
    '/api/administradores/userbyprofile', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Administradores', 
        'action' => 'obtener_usuarios_por_perfil',
        'api' => true,
        'prefix' => 'api'
    )
);

Router::connect(
    '/api/administradores/validate', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Administradores', 
        'action' => 'validate_token',
        'api' => true,
        'prefix' => 'api'
    )
);

/**
 * Venta
 */
Router::connect(
    '/api/ventas/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'obtener_venta',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/ventas/ver/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'obtener_venta_bodega',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/ventas/ver/referencia', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'ver_por_referencia',
        'api' => true,
        'prefix' => 'api'
        )
);

Router::connect(
    '/api/ventas/exists/externo/:id_externo', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'venta_existe_externo',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id_externo'),
        'id_externo' => '[0-9]+'
    )
);


Router::connect(
    '/api/ventas/change_state/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'cambiar_estado',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


Router::connect(
    '/api/ventas/recibir-notificacion-entrega-embalaje/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'cambiar_estado_v2',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


Router::connect(
    '/api/ventas/add_tracking_code/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'registrar_seguimiento',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


Router::connect(
    '/api/ventas/picking/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'picking_venta',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


Router::connect(
    '/api/ventas/set_picking/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'set_picking_estado',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


Router::connect(
    '/api/ventas', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'index',
        'api' => true,
        'prefix' => 'api')
);


Router::connect(
    '/api/ventas/enviame_webhook', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'enviame_webhook',
        'api' => true,
        'prefix' => 'api')
);


Router::connect(
    '/api/ventas/stockout/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'notificar_stockout',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/ventas/:id/cambiar_estado_por_transportista', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'cambiar_estado_por_transportista',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


Router::connect(
    '/api/ventas/cambiar-estado-desde-warehouse/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'cambiar_estado_desde_warehouse',
        'api' => true,
        '[method]' => 'POST',
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/ventas/v2/cambiar-estado-desde-warehouse/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'cambiar_estado_desde_warehouse_v2',
        'api' => true,
        '[method]' => 'POST',
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);



Router::connect(
    '/api/ventas/seguimiento/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller'    => 'Ventas', 
        'action'        => 'getSeguimiento',
        'api'           => true,
        'prefix'        => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/ventas/seguimiento/ref/:referencia', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller'    => 'Ventas', 
        'action'        => 'getSeguimientoByRef',
        'api'           => true,
        'prefix'        => 'api'),
    array(
        'pass' => array('referencia'),
        'id' => '[0-9-a-Z]+'
    )
);


/**
 * Transporte
 */
Router::connect(
    '/api/transporte', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Transportes', 
        'action' => 'obtener_transportes',
        'api' => true,
        'prefix' => 'api'
    )
);


/**
 * Tienda
 */
Router::connect(
    '/api/tienda', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Tiendas', 
        'action' => 'obtener_tiendas',
        'api' => true,
        'prefix' => 'api'
    )
);


Router::connect(
    '/api/tienda/calcular_costo_envio/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Tiendas', 
        'action' => 'calcular_costo_envio',
        'api' => true,
        'prefix' => 'api'
    ),
    array(
        'pass' => array('id'),
        'id'   => '[0-9]+' // id de la tienda
    )
);

Router::parseExtensions('json');



/**
 * Linio webhook
 */
Router::connect(
    '/api/ventas/linio/:tipo/:marketplace_id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'venta_linio',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('tipo', 'marketplace_id'),
        'tipo' => 'crear|actualizar',
        'marketplace_id'   => '[0-9]+' // id del marketplace
    )
);


/**
 * Mercadolibre notificaciones
 */
Router::connect(
    '/api/ventas/meli/:tipo/:marketplace_id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'venta_meli',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('tipo', 'marketplace_id'),
        'tipo' => 'crear|actualizar',
        'marketplace_id'   => '[0-9]+' // id del marketplace
    )
);


/**
 * Prestashop endpoint
 */
Router::connect(
    '/api/ventas/prestashop/:tienda_id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'venta_prestashop',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('tienda_id'),
        'tienda_id'   => '[0-9]+' // id del marketplace
    )
);


/**
 *  Clientes
 */
Router::connect(
    '/api/clientes', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaClientes', 
        'action' => 'index',
        'api' => true,
        'prefix' => 'api')
);


Router::connect(
    '/api/clientes/add', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaClientes', 
        'action' => 'add',
        'api' => true,
        'prefix' => 'api')
);

Router::connect(
    '/api/clientes/view/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaClientes', 
        'action' => 'view',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


/**
 *  Direcciones
 */
Router::connect(
    '/api/direcciones', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Direcciones', 
        'action' => 'index',
        'api' => true,
        'prefix' => 'api')
);

Router::connect(
    '/api/direcciones/add', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Direcciones', 
        'action' => 'add',
        'api' => true,
        'prefix' => 'api')
);

Router::connect(
    '/api/direcciones/edit/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Direcciones', 
        'action' => 'edit',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/direcciones/view/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Direcciones', 
        'action' => 'view',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


/**
 * Pagos de facturas proveedor
 */
Router::connect(
    '/api/pagoproveedor/add', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Pagos', 
        'action' => 'add',
        'api' => true,
        'prefix' => 'api')
);

/**
 * Mensajes
 */
Router::connect(
    '/api/mensajes', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Mensajes', 
        'action' => 'index',
        'api' => true,
        'prefix' => 'api')
);

Router::connect(
    '/api/mensajes/add', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Mensajes', 
        'action' => 'add',
        'api' => true,
        'prefix' => 'api')
);

Router::connect(
    '/api/mensajes/delete/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Mensajes', 
        'action' => 'delete',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/mensajes/view/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Mensajes', 
        'action' => 'view',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


/**
 * Dte  compras
 */
Router::connect(
    '/api/compras/documentos', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'DteCompras', 
        'action' => 'index',
        'api' => true,
        'prefix' => 'api')
);

Router::connect(
    '/api/compras/facturacion/obtener', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'DteCompras', 
        'action' => 'obtener_compras',
        'api' => true,
        '[method]' => 'GET',
        'prefix' => 'api')
);

Router::connect(
    '/api/compras/facturacion/sincronizar', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'DteCompras', 
        'action' => 'sincronizar_compras',
        'api' => true,
        '[method]' => 'POST',
        'prefix' => 'api')
);

Router::connect(
    '/api/compras/facturacion/recepcionar', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'DteCompras', 
        'action' => 'recepcionar_dte_compras',
        '[method]' => 'POST',
        'api' => true,
        'prefix' => 'api')
);


Router::connect(
    '/api/facturas-oc/delete/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'OrdenCompraFacturas', 
        'action' => 'delete',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


/**
 * Orden compra
 */
Router::connect(
    '/api/ordenes-de-compra/zonificar/:bodega_id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'OrdenCompras', 
        'action' => 'zonificar',
        'api' => true,
        '[method]' => 'GET',
        'prefix' => 'api'
    ),
    array(
        'pass' => array('bodega_id'),
        'bodega_id' => '[0-9]+'
    )
);

Router::connect(
    '/api/ordenes-de-compra/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'OrdenCompras', 
        'action' => 'view',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


Router::connect(
    '/api/ordenes-de-compra/recepcionar/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'OrdenCompras', 
        'action' => 'reception',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/ordenes-de-compra/detalle/zonificar/:id_detalle', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'OrdenCompras', 
        'action' => 'detalle_zonificar',
        '[method]' => 'POST',
        'api' => true,
        'prefix' => 'api'
    ),
    array(
        'pass' => array('id_detalle'),
        'id_detalle' => '[0-9]+'
    )
);

Router::connect(
    '/api/ordenes-de-compra/validacion-externa', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'OrdenCompras', 
        'action' => 'obtener_oc_validacion_externa',
        '[method]' => 'GET',
        'api' => true,
        'prefix' => 'api'
    )
);

Router::connect(
    '/api/ordenes-de-compra/validacion-externa/actualizar', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'OrdenCompras', 
        'action' => 'actualizar_oc_validacion_externa',
        '[method]' => 'PUT',
        'api' => true,
        'prefix' => 'api'
    )
);


Router::connect(
    '/api/ordenes-de-compra/recepcionar/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'OrdenCompras', 
        'action' => 'reception',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


/**
 * Monedas
 */
Router::connect(
    '/api/monedas', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Monedas', 
        'action' => 'index',
        'api' => true,
        'prefix' => 'api')
);

Router::connect(
    '/api/monedas/view/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Monedas', 
        'action' => 'view',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);


/**
 * Métodos de envios
 */

Router::connect(
    '/api/metodo-envio/obtener', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'MetodoEnvios', 
        'action' => 'obtener_metodos',
        'api' => true,
        'prefix' => 'api')
);


Router::connect(
    '/api/metodo-envio/generar-etiqueta-externa/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'MetodoEnvios', 
        'action' => 'generar_etiqueta_externa',
        '[method]' => 'POST',
        'api' => true,
        'prefix' => 'api'),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

/**
 * MarketPlace
 */

Router::connect(
    '/api/marketplaces/obtener', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Marketplaces', 
        'action' => 'obtener_marketPlaces',
        'api' => true,
        'prefix' => 'api')
);


 /**
  * Comunas
  */
Router::connect(
    '/api/comuna', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Comunas', 
        'action' => 'obtener_comunas',
        'api' => true,
        'prefix' => 'api'
    )
);


 /**
  * Contactos
  */
Router::connect(
    '/api/contactos/add', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Contactos', 
        'action' => 'add',
        'api' => true,
        'prefix' => 'api'
    )
);

Router::connect(
    '/api/contactos/attend', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Contactos', 
        'action' => 'atender',
        'api' => true,
        'prefix' => 'api'
    )
);


/**
 * Venta estados
 */
Router::connect(
    '/api/venta-estado-embalaje', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'VentaEstados', 
        'action' => 'obtener_para_emabalaje',
        '[method]' => 'GET',
        'api' => true,
        'prefix' => 'api'
    )
);

/**
 * EmbalajeWarehouses
 */

Router::connect(
    '/api/notificar-embalaje-a-revisar', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller'    => 'EmbalajeWarehouses', 
        'action'        => 'notificar_embalaje_a_revisar',
        'api'           => true,
        'prefix'        => 'api')
);

Router::connect(
    '/api/proveedor/eliminar-frecuencia/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller'    => 'Proveedores',
        'action'        => 'delete_frecuencia',
        'api'           => true,
        'prefix'        => 'api'

    ),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/proveedor/eliminar-regla/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller'    => 'Proveedores',
        'action'        => 'delete_regla',
        'api'           => true,
        'prefix'        => 'api'

    ),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/proveedor/eliminar-configuracion/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller'    => 'Proveedores',
        'action'        => 'delete_configuracion',
        'api'           => true,
        'prefix'        => 'api'

    ),
    array(
        'pass' => array('id'),
        'id' => '[0-9]+'
    )
);

Router::connect(
    '/api/pruebas', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller'    => 'Pruebas', 
        'action'        => 'pruebas',
        'api'           => true,
        'prefix'        => 'api')
);
