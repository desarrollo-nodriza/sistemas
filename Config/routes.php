<?php

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
