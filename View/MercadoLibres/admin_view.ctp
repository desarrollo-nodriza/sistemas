<div class="page-title">
	<h2><span class="fa fa-shopping-basket"></span> Mercado Libre Productos</h2>
</div>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Ver Html Mercado Libre Producto</h3>
				</div>
				<div class="panel-body">
					<div class="col-xs-12">
						<?=$html;?>
					</div>
					<div class="col-xs-12">
						<table class="table">
							<th><button type="button" data-clipboard-target="#htmlEmail" class="btn btn-sm btn-primary btn-copy btn-block" data-toggle="popover" data-trigger="manual" data-placement="right"><i class="fa fa-clipboard" aria-hidden="true"></i> Copiar Html</button></th>
								<td><textarea id="htmlEmail" class="form-control"><?=h($html);?></textarea></td>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>