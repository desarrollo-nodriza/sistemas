<? if (isset($col)) : ?>
	<div class="row">
		<div class="col-xs-12">
<? endif; ?>
<p><small><?= $this->Paginator->counter('Página {:page} de {:pages}, mostrando {:current} resultados de {:count}.');?></small></p>
<? if (isset($col)) : ?>
		</div>
	</div>
<? endif; ?>