<nav class="navbar d-md-none navbar-light bg-light">
	<button class="navbar-toggler p-0 text-dark" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
    	<i class="fas fa-bars"></i>
  	</button>

	<a class="navbar-brand mr-0 p-0" href="#">
		<img src="<?=obtener_url_base();?>webroot/img/toolmania/logo.png" class="img-fluid d-block mx-auto logo-navbar">
	</a>
  
  	<?=$this->Html->link('<i class="fas fa-power-off"></i>', array('action' => 'logout'), array('class' => 'nav-link logout p-0 text-dark', 'escape' => false));?>

	<div class="collapse navbar-collapse" id="navbarNav">
		<ul class="navbar-nav mr-auto">
			<li class="nav-item <?= ($this->Html->menuActivo(array('controller' => 'ventaClientes', 'action' => 'dashboard')) ? 'active' : ''); ?>">
			    <?=$this->Html->link('<i class="fa fa-home mr-3"></i> Dashboard', array('controller' => 'ventaClientes', 'action' => 'dashboard'), array('class' => 'nav-link px-3 py-2 text-light', 'escape' => false));?>
			</li>
			<li class="nav-item <?= ($this->Html->menuActivo(array('controller' => 'ventas', 'action' => 'compras')) ? 'active' : ''); ?>">
		        <?=$this->Html->link('<i class="fa fa-shopping-bag mr-3"></i> Mis compras', array('controller' => 'ventas', 'action' => 'compras'), array('class' => 'nav-link px-3 py-2 text-light', 'escape' => false));?>
		    </li>
		    <li class="nav-item <?= ($this->Html->menuActivo(array('controller' => 'prospectos', 'action' => 'cotizaciones')) ? 'active' : ''); ?>">
		        <?=$this->Html->link('<i class="fa fa-file-alt mr-3"></i> Mis cotizaciones', array('controller' => 'prospectos', 'action' => 'cotizaciones'), array('class' => 'nav-link px-3 py-2 text-light', 'escape' => false));?>
		    </li>
		</ul>
	</div>
</nav>