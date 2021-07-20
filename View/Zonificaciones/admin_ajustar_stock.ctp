
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
                        <li><a href="#" class="boton clone-boton" ><span class="fa fa-plus"></span></a></li>
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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script type="text/javascript">
	$(document).on('click', '.clone-boton', function(){
	let clone_tr = document.getElementsByClassName("clone-tr");
	console.log(clone_tr.length);

	if (clone_tr.length>0) {
		let elementoremoveClass = clone_tr.item(0);
		elementoremoveClass.removeAttribute('class')
	}
	
	let clone_tr2 = document.getElementsByClassName("clone-tr");
	console.log(clone_tr2.length);
	});

	jQuery(document).ready(function($){
		$(document).ready(function() {
			$('.mi-selector').select2();
		});
	});
</script>