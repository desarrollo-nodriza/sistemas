<?php
Router::connect('/chilexpress', array('plugin' => 'chilexpress', 'controller' => 'chilexpress', 'action' => 'index', 'admin' => true));
Router::connect('/chilexpress/tracking', array('plugin' => 'chilexpress', 'controller' => 'chilexpress', 'action' => 'tracking', 'admin' => false, 'pass' => array('ot')));
Router::connect('/chilexpress/tracking/:ot', array('plugin' => 'chilexpress', 'controller' => 'chilexpress', 'action' => 'tracking', 'admin' => false, 'pass' => array('ot')));