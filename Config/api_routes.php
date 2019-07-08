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
        'action' => 'add',
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



Router::parseExtensions('json');