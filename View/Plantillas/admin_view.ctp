<div class="page-title">
	<h2><span class="fa fa-file"></span> Plantillas</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Plantilla <?=$this->request->data['Plantilla']['nombre'];?></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th>Html</th>
								<td><?=$this->request->data['Plantilla']['html'];?></td>
							</tr>
						</table>

						<div class="pull-right">
							<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>