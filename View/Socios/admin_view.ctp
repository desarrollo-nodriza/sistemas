<div class="socios view">
<h2><?php echo __('Socio'); ?></h2>
	<dl>
		<dt><?php echo __('Id'); ?></dt>
		<dd>
			<?php echo h($socio['Socio']['id']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Usuario'); ?></dt>
		<dd>
			<?php echo h($socio['Socio']['usuario']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Clave'); ?></dt>
		<dd>
			<?php echo h($socio['Socio']['clave']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Nombre'); ?></dt>
		<dd>
			<?php echo h($socio['Socio']['nombre']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Email'); ?></dt>
		<dd>
			<?php echo h($socio['Socio']['email']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Activo'); ?></dt>
		<dd>
			<?php echo h($socio['Socio']['activo']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Created'); ?></dt>
		<dd>
			<?php echo h($socio['Socio']['created']); ?>
			&nbsp;
		</dd>
		<dt><?php echo __('Modified'); ?></dt>
		<dd>
			<?php echo h($socio['Socio']['modified']); ?>
			&nbsp;
		</dd>
	</dl>
</div>
<div class="actions">
	<h3><?php echo __('Actions'); ?></h3>
	<ul>
		<li><?php echo $this->Html->link(__('Edit Socio'), array('action' => 'edit', $socio['Socio']['id'])); ?> </li>
		<li><?php echo $this->Form->postLink(__('Delete Socio'), array('action' => 'delete', $socio['Socio']['id']), array('confirm' => __('Are you sure you want to delete # %s?', $socio['Socio']['id']))); ?> </li>
		<li><?php echo $this->Html->link(__('List Socios'), array('action' => 'index')); ?> </li>
		<li><?php echo $this->Html->link(__('New Socio'), array('action' => 'add')); ?> </li>
	</ul>
</div>
