<div class="page-title">
	<h2><span class="fa fa-envelope"></span> Último Html</h2>
</div>

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Newslletter <?=$this->request->data['Email']['nombre'];?></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table">
							<tr>
								<th>Newletter</th>
								<td><?= $this->request->data['Email']['ultimo_html']; ?></td>
							</tr>
							<tr>
								<th>Html newlletter <button type="button" data-clipboard-target="#htmlEmail" class="btn btn-sm btn-primary btn-copy btn-block" data-toggle="popover" data-trigger="manual" data-placement="right"><i class="fa fa-clipboard" aria-hidden="true"></i> Copiar Html</button></th>
								<td><textarea id="htmlEmail" class="form-control"><?=h($this->request->data['Email']['ultimo_html']);?></textarea></td>
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