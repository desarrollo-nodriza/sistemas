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
    '/api/ventas/enviame_webhook', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'Ventas', 
        'action' => 'enviame_webhook',
        'api' => true,
        'prefix' => 'api')
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





Router::parseExtensions('json');