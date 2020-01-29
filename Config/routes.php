<?php

include APP . 'Config' . DS . 'api_routes.php';

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

Router::connect(
    '/socio/oc/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'ordenCompras', 
        'action' => 'validate_supplier',
        'socio' => false,
        'prefix' => null
    ),
     array(
        'pass' => array('id'),
        'id' => '[0-9]+',
    )
);


Router::connect(
    '/socio/oc/pdf/:id/:proveedor', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'ordenCompras', 
        'action' => 'view_oc_pdf',
        'socio' => false,
        'prefix' => null
    ),
     array(
        'pass' => array('id', 'proveedor'),
        'id' => '[0-9]+',
        'proveedor' => '[0-9]+',
    )
);

/**
 * Clientes
 */
Router::connect('/cliente', array('controller' => 'ventaClientes', 'action' => 'dashboard', 'cliente' => true, 'prefix' => 'cliente'));
Router::connect('/cliente/login', array('controller' => 'ventaClientes', 'action' => 'login', 'cliente' => true, 'prefix' => 'cliente'));
Router::connect('/cliente/logout', array('controller' => 'ventaClientes', 'action' => 'logout', 'cliente' => true, 'prefix' => 'cliente'));


Router::connect(
    '/cliente/dashboard', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'ventaClientes', 
        'action' => 'dashboard',
        'cliente' => true,
        'prefix' => 'cliente'
    )
);

Router::connect(
    '/cliente/sended', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'ventaClientes', 
        'action' => 'sended',
        'cliente' => true,
        'prefix' => 'cliente'
    )
);

Router::connect(
    '/cliente/sendFailed', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'ventaClientes', 
        'action' => 'sendFailed',
        'cliente' => true,
        'prefix' => 'cliente'
    )
);

Router::connect(
    '/cliente/authorization', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'ventaClientes', 
        'action' => 'authorization',
        'cliente' => true,
        'prefix' => 'cliente'
    )
);

Router::connect(
    '/cliente/mis-compras', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'ventas', 
        'action' => 'compras',
        'cliente' => true,
        'prefix' => 'cliente'
    )
);

Router::connect(
    '/cliente/mis-compras/:id', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'ventas', 
        'action'     => 'ver',
        'cliente'    => true,
        'prefix'     => 'cliente'
    ),
    array(
        'pass' => array('id'),
        'id'   => '[0-9]+'
    )
);

Router::connect(
    '/cliente/:controller/:action/', // E.g. /blog/3-CakePHP_Rocks
    array(
        'cliente' => true,
        'prefix' => 'cliente'
    )
);

Router::connect(
    '/cliente/:controller/:action/*', // E.g. /blog/3-CakePHP_Rocks
    array(
        'cliente' => true,
        'prefix' => 'cliente'
    )
);



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


Router::connect(
    '/feed/google/:tienda/:feed', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'campanas', 
        'action' => 'feed',
        'google' => true,
        'prefix' => 'google'),
    array(
        'pass' => array('tienda', 'feed'),
        'tienda' => '[0-9]+',
        'feed' => '[0-9]+',
    )
);


Router::connect(
    '/Campana/google/:tienda/:feed', // E.g. /blog/3-CakePHP_Rocks
    array(
        'controller' => 'campanas', 
        'action' => 'feed',
        'google' => true,
        'prefix' => 'google'),
    array(
        'pass' => array('tienda', 'feed'),
        'tienda' => '[0-9]+',
        'feed' => '[0-9]+',
    )
);

Router::connect('/login', array('controller' => 'administradores', 'action' => 'login2', 'admin' => true));
Router::connect('/logout', array('controller' => 'administradores', 'action' => 'logout', 'admin' => true));


Router::connect('/seccion/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Remover /admin
 */
Router::connect('/:controller', array('admin' => true, 'prefix' => 'admin'));
Router::connect('/:controller/:action/*', array('admin' => true, 'prefix' => 'admin'));



require CAKE . 'Config' . DS . 'routes.php';
