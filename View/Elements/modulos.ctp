<? foreach ($modulosDisponibles as $moduloPadre) { ?>
	<? if ( ! empty($moduloPadre['hijos']) ) { ?>
		<li class="xn-openable">
			<a class="xn-title">
		<? if (!empty($moduloPadre['icono'])) : ?>
			<span class="<?=$moduloPadre['icono'];?>"></span>
		<? endif; ?>
				<span class="xn-text"><?=$moduloPadre['nombre'];?></span>
			</a>
			<ul>
		<? foreach ($moduloPadre['hijos'] as $modulo) { ?>
			<li class="submenu <?= ($this->Html->menuActivo(array('controller' => strtolower($modulo['Modulo']['url']), 'action' => 'index')) ? 'active' : ''); ?>">
				<?= $this->Html->link(
					'<span class="'.$modulo['Modulo']['icono'].'"></span> '.$modulo['Modulo']['nombre'],
					array('controller' => strtolower($modulo['Modulo']['url']), 'action' => 'index'),
					array('escape' => false)
				); ?>
			</li>
		<? }?>
			</ul>
		</li>
	<? } ?>
<? } ?>
<script type="text/javascript">
	
	$('li.active').parent().parent().addClass('active');
		
</script>
