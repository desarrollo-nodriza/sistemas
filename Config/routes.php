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



Router::parseExtensions('json');

CakePlugin::routes();

Router::connect('/', array('controller' => 'pages', 'action' => 'dashboard', 'admin' => true));


/**
 * Socios
 */
Router::connect('/socio', array('controller' => 'socios', 'action' => 'prisync', 'socio' => true, 'prefix' => 'socio'));
Router::connect('/socio/login', array('controller' => 'socios', 'action' => 'login', 'socio' => true, 'prefix' => 'socio'));
Router::connect('/socio/logout', array('controller' => 'socios', 'action' => 'logout', 'socio' => true, 'prefix' => 'socio'));
Router::connect(
    '/socio/historico/:id/:fechai/:fechaf/:agrupar', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'socios', 
        'action' => 'obtener_historico',
        'socio' => false,
        'prefix' => null),
    array(
        'pass' => array('id', 'fechai', 'fechaf', 'agrupar'),
        'id' => '[0-9]+'
    )
);
Router::connect(
    '/socio/comparativa/:id/:mandatory', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'socios', 
        'action' => 'obtener_comparativa',
        'socio' => false,
        'prefix' => null),
    array(
        'pass' => array('id', 'mandatory'),
        'id' => '[0-9]+',
        'mandatory' => '[a-z]+'
    )
);


Router::connect(
    '/socio/comparativa/', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'socios', 
        'action' => 'obtener_comparativa',
        'socio' => false,
        'prefix' => null)
);
/*Router::connect(
    '/socio/:tienda/:usuario', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'socios', 
        'action' => 'login',
        'socio' => true,
        'prefix' => 'socio'),
    array(
        'pass' => array('tienda', 'usuario'),
        'tienda' => '[0-9]+',
        'usuario' => '[a-z0-9-_]+'
    )
);*/


/**
* MELI
*/
Router::connect(
    '/meli/:tienda/:plantilla/:producto', // E.g. /blog/3-CakePHP_Rocks
    array(
    	'controller' => 'mercado_libres', 
    	'action' => 'get',
    	'meli' => true,
        'prefix' => 'meli'),
    array(
        'pass' => array('tienda', 'plantilla', 'producto'),
        'tienda' => '[0-9]+',
        'plantilla' => '[0-9]+',
        'producto' => '[0-9]+'
    )
);

/**
 * Kanasta
 */
Router::connect('/knasta', array('controller' => 'productotiendas', 'action' => 'feed', 'knasta' => true, 'prefix' => 'knasta'));
Router::connect(
    '/knasta/:tienda', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'productotiendas', 
        'action' => 'feed',
        'knasta' => true,
        'prefix' => 'knasta'),
    array(
        'pass' => array('tienda'),
        'tienda' => '[a-z]+'
    )
);


/**
 * Prisync
 */
Router::connect('/prisyncProductos/obtener_productos', array('controller' => 'prisyncProductos', 'action' => 'obtener_productos', 'admin' => false));


/**
 * Google
 */
Router::connect('/google', array('controller' => 'productotiendas', 'action' => 'feed', 'google' => true, 'prefix' => 'google'));
Router::connect(
    '/google/:tienda', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'productotiendas', 
        'action' => 'feed',
        'google' => true,
        'prefix' => 'google'),
    array(
        'pass' => array('tienda'),
        'tienda' => '[a-z]+'
    )
);

Router::connect('/login', array('controller' => 'administradores', 'action' => 'login', 'admin' => true));
Router::connect('/logout', array('controller' => 'administradores', 'action' => 'logout', 'admin' => true));


Router::connect('/seccion/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Remover /admin
 */
Router::connect('/:controller', array('admin' => true, 'prefix' => 'admin'));
Router::connect('/:controller/:action/*', array('admin' => true, 'prefix' => 'admin'));





require CAKE . 'Config' . DS . 'routes.php';
