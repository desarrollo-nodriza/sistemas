<div class="page-title">
	<h2><span class="fa fa-shopping-basket"></span> Mercado Libre Productos</h2>
</div>
<?= $this->Form->create('MercadoLibr', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->input('id'); ?>
<?= $this->Form->input('tienda_id', array('type' => 'hidden', 'value' => $this->Session->read('Tienda.id'))); ?>
<?= $this->Form->input('id_product', array('type' => 'hidden', 'class' => 'id-product')); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Editar Mercado Libre producto</h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th><?= $this->Form->label('mercado_libre_plantilla_id', 'Mercado libre plantilla'); ?></th>
								<td><?= $this->Form->select('mercado_libre_plantilla_id', $plantillas, array('class' => 'form-control', 'empty' => false)); ?></td>
							</tr>
							<tr>
								<th><?= $this->Form->label('nombre', 'Nombre'); ?></th>
								<td><?= $this->Form->input('nombre'); ?></td>
							</tr>
							<? if (!empty($producto)) : ?>
								<tr>
									<th><label>Producto Actual</label></th>
									<td><?=$producto['Productotienda']['reference'] . ' - ' . $producto['Lang'][0]['ProductotiendaIdioma']['name'];?></td>
								</tr>
								<tr>
									<th><label>Actualizar producto</label></th>
									<td><?= $this->Form->input('producto', array('type' => 'text', 'class' => 'form-control input-productos-buscar-meli')); ?></td>
								</tr>
							<? else : ?>
								<tr>
									<th><label>Producto</label></th>
									<td><?= $this->Form->input('producto', array('type' => 'text', 'class' => 'form-control input-productos-buscar-meli')); ?></td>
								</tr>
							<? endif; ?>
							<div id="jstree_default" class="push-down-20">
                                
                            </div>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<input type="submit" class="btn btn-primary esperar-carga" autocomplete="off" data-loading-text="Espera un momento..." value="Guardar cambios">
						<?= $this->Html->link('Cancelar', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>
<?= $this->Form->end(); ?>
<script type="text/javascript">
	

	$.extend({
		meli: {
			bind:function(){
				$jstree = $('#jstree_default')
					.jstree({
						"core" : {
						    "animation" : 0,
						    "check_callback" : true,
						    "themes" : { "stripes" : true },
						    'data' : {
						      'url' : function (node) {
						        return node.id === '#' ?
						          webroot + 'mercadoLibres/obtenerCategorias' :  webroot + 'mercadoLibres/obtenerCategoriasId';
						      },
						      'data' : function (node) {
						        return { 
						        	'id' : node.id };
						      },
						      "dataType" : "json"
						    }
						  }
					});
			},
			init:function(){
				if ( $('#jstree_default').length ) {
					$.meli.bind();
				}
			}
		}
	});
	$(document).ready(function(){
		$.meli.init();
	});
</script>