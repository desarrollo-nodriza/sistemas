<?php
Router::connect('/', array('controller' => 'pages', 'action' => 'dashboard', 'admin' => true));

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

Router::connect('/login', array('controller' => 'administradores', 'action' => 'login', 'admin' => true));
Router::connect('/logout', array('controller' => 'administradores', 'action' => 'logout', 'admin' => true));

Router::connect('/seccion/*', array('controller' => 'pages', 'action' => 'display'));

/**
 * Remover /admin
 */
Router::connect('/:controller', array('admin' => true, 'prefix' => 'admin'));
Router::connect('/:controller/:action/*', array('admin' => true, 'prefix' => 'admin'));


CakePlugin::routes();
require CAKE . 'Config' . DS . 'routes.php';
