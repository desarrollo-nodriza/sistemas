
<div class="page-title">
	<h2><span class="fa fa-list-ol"></span> Ajustar stock de ubicación</h2>
</div>

<?= $this->Form->create('Zonificacion', array('inputDefaults' => array('div' => false, 'label' => false), 'class' => 'js-validate-producto')); ?>
<div class="page-content-wrap">
	
	<div class="row">
		<div class="col-xs-12">
			
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><i class="fa fa-arrows" aria-hidden="true"></i> Ajustar inventario</h3>
					<ul class="panel-controls">
                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">
					<?=$this->element('zonificaciones/crear_ajuste_producto', array('zonificaciones' => $zonificaciones,"ubicaciones"=>$ubicaciones)); ?>
				</div>
				<div class="panel-footer">
					<div class="col-xs-12">
						<div class="pull-left">
							<?= $this->Html->link('<i class="fa fa-shopping-bag"></i> Ir al producto', array('controller' => 'ventaDetalleProductos', 'action' => 'edit',$id), array('class' => 'btn btn-primary btn-buscar btn-block', 'rel' => 'tooltip', 'title' => 'Ir al producto', 'escape' => false)); ?>
						</div>
						<div class="pull-right">
							<?= $this->Form->button('<i class="fa fa-send" aria-hidden="true"></i> Ajustar', array('type' => 'submit', 'escape' => false, 'class' => 'btn btn-buscar btn-success btn-block start-loading-then-redirect')); ?>
						</div>
					</div>
				</div>
				
			</div>
		</div>
	</div>
</div>
<?= $this->Form->end(); ?>

