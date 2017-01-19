<?php
Router::connect('/', array('controller' => 'pages', 'action' => 'dashboard', 'admin' => true));


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
