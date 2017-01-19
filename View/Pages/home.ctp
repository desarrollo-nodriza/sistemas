<? App::uses('Debugger', 'Utility'); ?>
<div class="container" style="padding: 30px 0;">

	<div class="hero-unit" style="padding: 20px 0px;">
		<h1>Sistema Newsletter Nodriza Spa</h1>
		<p>
			<?= $this->Html->link('Entrar al newsletter', '/admin', array('class' => 'btn btn-primary btn-block')); ?>
		</p>
	</div>

	<? if ( Configure::read('debug') > 0 ) Debugger::checkSecurityKeys(); ?>

	<!--
	<div class="alert alert-danger">
		<?= __d('cake_dev', 'URL rewriting is not properly configured on your server.'); ?>
		<br>
		1) <a target="_blank" href="http://book.cakephp.org/2.0/en/installation/url-rewriting.html">Help me configure it</a>
		<br>
		2) <a target="_blank" href="http://book.cakephp.org/2.0/en/development/configuration.html#cakephp-core-configuration">I don't / can't use URL rewriting</a>
	</div>
	-->

	<div class="alert alert-success"><?= __d('cake_dev', 'Versión CakePHP es %s', Configure::version()); ?></div>

	<? if ( version_compare(PHP_VERSION, '5.3.0', '>=') ) : ?>
	<div class="alert alert-success"><?= __d('cake_dev', '<i class="icon-ok"></i> La version de PHP es <code>%s</code> (CakePHP requiere la version <code>5.3.0</code> o superior.', PHP_VERSION); ?></div>
	<? else : ?>
	<div class="alert alert-danger"><?= __d('cake_dev', '<i class="icon-ban-circle"></i> La version de PHP es muy baja. CakePHP requiere al menos la version <code>5.3.0</code>.'); ?></div>
	<? endif; ?>


	<? if ( is_writable(TMP) ) : ?>
	<div class="alert alert-success"><?= __d('cake_dev', '<i class="icon-ok"></i> El directorio temporal tiene permisos de escritura.'); ?></div>
	<? else : ?>
	<div class="alert alert-danger"><?= __d('cake_dev', '<i class="icon-ban-circle"></i> El directorio temporal NO tiene permisos de escritura. <code>chmod -R 0777 ' . TMP . '</code>'); ?></div>
	<? endif; ?>


	<? $settings = Cache::settings(); ?>
	<? if ( ! empty($settings) ) : ?>
	<div class="alert alert-success"><?= __d('cake_dev', '<i class="icon-ok"></i> El motor de cache utilizado es %s. Para cambiarlo edita la configuracion en <code>APP/Config/core.php</code>', "<em>{$settings['engine']}Engine</em>"); ?></div>
	<? else : ?>
	<div class="alert alert-danger"><?= __d('cake_dev', '<i class="icon-ban-circle"></i> El motor de cache NO esta en funcionamiento. Verifica su configuración en <code>APP/Config/core.php</code>'); ?></div>
	<? endif; ?>


	<? $filePresent = null; ?>
	<? if ( file_exists(APP . 'Config' . DS . 'database.php') ) : ?>
	<div class="alert alert-success"><?= __d('cake_dev', '<i class="icon-ok"></i> El archivo de configuración de base de datos está presente.'); ?></div>
	<? $filePresent = true; else : ?>
	<div class="alert alert-danger">
		<?= __d('cake_dev', '<i class="icon-ban-circle"></i> El archivo de configuración de base de datos NO está presente.'); ?>
		<br>
		<?= __d('cake_dev', 'Renombra el archivo <code>APP/Config/database.php.default</code> a <code>APP/Config/database.php</code>'); ?>
		</div>
	<? endif; ?>


	<?
	if ( isset($filePresent) ) :
		App::uses('ConnectionManager', 'Model');
		try
		{
			$connected = ConnectionManager::getDataSource('default');
		}
		catch ( Exception $connectionError )
		{
			$connected	= false;
			$errorMsg	= $connectionError->getMessage();
			if ( method_exists($connectionError, 'getAttributes') )
			{
				$attributes = $connectionError->getAttributes();
				if ( isset($errorMsg['message']) )
					$errorMsg .= '<br />' . $attributes['message'];
			}
		}
	?>
		<? if ( $connected && $connected->isConnected() ) : ?>
		<div class="alert alert-success"><?= __d('cake_dev', '<i class="icon-ok"></i> La conexión a la base de datos se realizó correctamente.'); ?></div>
		<? else : ?>
		<div class="alert alert-danger">
			<?= __d('cake_dev', '<i class="icon-ban-circle"></i>  La conexión a la base de datos NO se realizó correctamente.'); ?>
			<br>
			<?= $errorMsg; ?>
		</div>
		<? endif; ?>
	<? endif; ?>


	<? App::uses('Validation', 'Utility'); ?>
	<? if ( ! Validation::alphaNumeric('cakephp') ) : ?>
	<div class="alert alert-danger"><?= __d('cake_dev', '<i class="icon-ban-circle"></i> PCRE no se compiló con soporte Unicode support. Recompila PCRE con soporte Unicode agregando el parametro <code>--enable-unicode-properties</code> al configurar.'); ?></div>
	<? endif; ?>


	<? if ( CakePlugin::loaded('DebugKit') ) : ?>
	<div class="alert alert-success"><?= __d('cake_dev', '<i class="icon-ok"></i> Plugin DebugKit presente.'); ?></div>
	<? else : ?>
	<div class="alert alert-danger"><?= __d('cake_dev', '<i class="icon-ban-circle"></i> Plugin DebugKit no instalado. Permite inspeccionar y debugear distintos aspectos de la aplicación. Descargalo desde %s', $this->Html->link('github', 'https://github.com/cakephp/debug_kit')); ?></div>
	<? endif; ?>
</div>
