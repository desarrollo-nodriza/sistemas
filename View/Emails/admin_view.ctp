<div class="page-title">
	<h2><span class="fa fa-envelope"></span> Newsletter</h2>
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
								<td><?=$this->request->data['Email']['html'];?></td>
							</tr>
							<tr>
								<th>Html newlletter</th>
								<td><?=h($this->request->data['Email']['html']);?></td>
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