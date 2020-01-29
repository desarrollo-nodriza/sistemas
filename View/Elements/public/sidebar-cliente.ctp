<div class="sidebar-header">
    <img src="<?=obtener_url_base();?>webroot/img/toolmania/logo.png" class="img-fluid d-block mx-auto logo-sidebar">
</div>

<ul class="list-unstyled components">

	<p class="d-flex justiy-content-bewteen align-items-center flex-column">
		<img src="https://source.unsplash.com/60x60/?yellow" class="d-flex rounded-circle mb-2">
		Hola <?=$this->Session->read('Auth.Cliente.nombre'); ?>
		<span class="text-light font-weight-light">Último acceso: <?= $this->Session->read('Auth.Cliente.ultimo_acceso'); ?></span>
	</p>
    
    <li class="<?= ($this->Html->menuActivo(array('controller' => 'ventaClientes', 'action' => 'dashboard')) ? 'active' : ''); ?>">
        <?=$this->Html->link('<i class="fa fa-home mr-2"></i> Dashboard', array('controller' => 'ventaClientes', 'action' => 'dashboard'), array('class' => '', 'escape' => false));?>
    </li>
    <li class="<?= ($this->Html->menuActivo(array('controller' => 'ventas', 'action' => 'compras')) ? 'active' : ''); ?>">
        <?=$this->Html->link('<i class="fa fa-shopping-bag mr-2"></i> Mis compras', array('controller' => 'ventas', 'action' => 'compras'), array('class' => '', 'escape' => false));?>
    </li>
    <li class="<?= ($this->Html->menuActivo(array('controller' => 'prospectos', 'action' => 'cotizaciones')) ? 'active' : ''); ?>">
        <?=$this->Html->link('<i class="fa fa-file-alt mr-2"></i> Mis cotizaciones', array('controller' => 'prospectos', 'action' => 'cotizaciones'), array('class' => '', 'escape' => false));?>
    </li>
</ul>

<div class="sidebar-footer">
	<ul class="list-unstyled d-flex justify-content-center">
		<li><?=$this->Html->link('<i class="fas fa-power-off"></i>', array('action' => 'logout'), array('class' => 'text-light logout', 'escape' => false));?></li>
	</ul>
</div>