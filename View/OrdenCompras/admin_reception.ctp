<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Recepción de productos</h2>
</div>

<?= $this->Form->create('OrdenCompra', array('class' => 'form-horizontal js-validate-oc js-recepcion', 'type' => 'file',  'data-valid' => false, 'data-id' => $this->request->data['OrdenCompra']['id'],'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->hidden('url_retorno', array('value' => $url_retorno)); ?>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h3 class="panel-title text-uppercase"><i class="fa fa-file"></i> OC para <b><?=$this->request->data['Proveedor']['nombre'];?></b></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive">
						<table class="table table-bordered js-clone-wrapper">
							<thead>
								<th>ID</th>
								<th>Código</th>
								<th>Descripción</th>
								<th>Cantidad pedida</th>
								<th>Cantidad recibida <br><small>(ya en stock)</small></th>
								<th>Cantidad entrante</th>
								<th>Bodega</th>
								<!--<th>Recibido</th>-->
							</thead>
							<tboby class="">
							<? foreach ($this->request->data['VentaDetalleProducto'] as $ipp => $data) : ?>	
								
								<tr>
									<td>
										<?=$this->Form->hidden(sprintf('%d.VentaDetalleProducto.id', $ipp), array('value' => $data['id']));?>
										<?=$data['id'];?>
									</td>
									<td><?=$data['OrdenComprasVentaDetalleProducto']['codigo'];?></td>
									<td><?=$data['OrdenComprasVentaDetalleProducto']['descripcion'];?></td>
									<td><?=$data['OrdenComprasVentaDetalleProducto']['cantidad'];?></td>
									<td><?=$data['OrdenComprasVentaDetalleProducto']['cantidad_recibida'];?></td>

									<? if ($data['OrdenComprasVentaDetalleProducto']['cantidad'] > $data['OrdenComprasVentaDetalleProducto']['cantidad_recibida'] ) : ?>

									<td><?=$this->Form->input(sprintf('%d.Bodega.0.cantidad', $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-cantidad-recibida', 'max' => $data['OrdenComprasVentaDetalleProducto']['cantidad'] , 'value' => $data['OrdenComprasVentaDetalleProducto']['cantidad'] - $data['OrdenComprasVentaDetalleProducto']['cantidad_recibida']));?></td>
									
									<? else : ?>

									<td><?=$this->Form->input(sprintf('%d.Bodega.0.cantidad', $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-cantidad-recibida', 'readonly' => true, 'max' => $data['OrdenComprasVentaDetalleProducto']['cantidad'] , 'value' => 0));?></td>
									
									<? endif; ?>
									
									<? if (!empty(Hash::extract($data, 'Bodega.{n}.BodegasVentaDetalleProducto.bodega_id'))) : ?>
									
									<td><?=$this->Form->select(sprintf('%d.Bodega.0.bodega_id', $ipp), $bodegas, array('class' => 'form-control not-blank', 'default' => Hash::extract($data, 'Bodega.{n}.BodegasVentaDetalleProducto.bodega_id')[0], 'empty' => 'Seleccione'));?></td>
									<? else : ?>
									<td><?=$this->Form->select(sprintf('%d.Bodega.0.bodega_id', $ipp), $bodegas, array('class' => 'form-control not-blank', 'default' => 1, 'empty' => 'Seleccione'));?></td>
									<? endif; ?>
									<!--<td><?=$this->Form->checkbox(sprintf('%d.VentaDetalleProducto.recibido', $ipp), array('checked' => true, 'class' => 'icheckbox'));?></td>-->
								</tr>
								
							<? endforeach; ?>
							
							</tboby>
						</table>
					</div>
				</div>
			</div>
		</div>
	</div>


	<div class="row">
		<div class="col-xs-12">
			<div class="panel panel-info">
				<div class="panel-heading">
					<h5 class="panel-title"><i class="fa fa-file" aria-hidden="true"></i> <?=__('Documentos');?></h5>
					<ul class="panel-controls">
                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<caption><?= __('Agregue los folios y tipos de documentos'); ?></caption>
							<thead>
								<tr>
									<th><?= __('Folio');?></th>
									<th><?= __('Tipo');?></th>
									<th><?= __('Comentario');?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody class="">
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->input('OrdenComprasDocumento.999.folio', array('type' => 'text', 'disabled' => true, 'class' => 'form-control is-number not-blank', 'placeholder' => 'Ej: 4433')); ?>
									</td>
									<td>
										<?= $this->Form->select('OrdenComprasDocumento.999.tipo', array('factura' => 'Factura', 'nota de credito' => 'Nota de crédito'), array('disabled' => true, 'class' => 'form-control not-blank', 'default' => 1, 'empty' => false)); ?>
									</td>	
									<td>
										<?= $this->Form->input('OrdenComprasDocumento.999.comentario', array('type' => 'textarea', 'disabled' => true, 'class' => 'form-control', 'placeholder' => 'Agregue un comentario (opcional)')); ?>
									</td>							
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>

								<? if (!empty($this->request->data['OrdenCompra']['meta_dtes'])) :  ?>
								<? foreach($this->request->data['OrdenCompra']['meta_dtes'] as $ip => $dte) : ?>
								<tr>
									<td>
										<?= $this->Form->input(sprintf('OrdenComprasDocumento.%d.folio', $ip), array('type' => 'text', 'class' => 'form-control is-number not-blank', 'placeholder' => 'Ej:  4433', 'value' => $dte['folio'])); ?>
									</td>
									<td>
										<?= $this->Form->select(sprintf('OrdenComprasDocumento.%d.tipo', $ip), array('factura' => 'Factura', 'nota de credito' => 'Nota de crédito'), array('class' => 'form-control not-blank', 'default' => $dte['tipo'], 'empty' => false)); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('OrdenComprasDocumento.%d.comentario', $ip), array('type' => 'textarea', 'class' => 'form-control', 'placeholder' => 'Agregue un comentario (opcional)', 'value' => $dte['comentario'])); ?>
									</td>
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>
								<? endforeach; ?>
								<? endif; ?>
								
							</tbody>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<button type="submit" class="btn btn-success esperar-carga" autocomplete="off" data-loading-text="Espera un momento..."><i class="fa fa-check"></i> Guardar cambios</button>
						<?= $this->Html->link('Volver', $url_retorno, array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<?= $this->Form->end(); ?>
