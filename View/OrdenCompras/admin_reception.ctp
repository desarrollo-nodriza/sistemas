<div class="page-title">
	<h2><i class="fa fa-list" aria-hidden="true"></i> Recepción de productos</h2>
</div>

<?= $this->Form->create('OrdenCompra', array('class' => 'form-horizontal js-validate-oc js-recepcion', 'type' => 'file',  'data-valid' => false, 'data-id' => $this->request->data['OrdenCompra']['id'],'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
<?= $this->Form->hidden('url_retorno', array('value' => $url_retorno)); ?>
<?= $this->Form->hidden('rut_proveedor', array('value' => $this->request->data['Proveedor']['rut_empresa'])); ?>
<?= $this->Form->hidden('rut_tienda', array('value' => $this->request->data['Tienda']['rut'])); ?>
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
								<th>Precio uni</th>
								<th>Total</th>
								<th>Cantidad pedida</th>
								<th>Cantidad recibida <br><small>(ya en stock)</small></th>
								<th>Cantidad a ingresar</th>
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
									<td><?=CakeNumber::currency(($data['OrdenComprasVentaDetalleProducto']['total_neto'] / $data['OrdenComprasVentaDetalleProducto']['cantidad']), 'CLP');?></td>
									<td><?=CakeNumber::currency($data['OrdenComprasVentaDetalleProducto']['total_neto'] , 'CLP');?></td>
									<td><?=$data['OrdenComprasVentaDetalleProducto']['cantidad'];?></td>
									<td><?=$data['OrdenComprasVentaDetalleProducto']['cantidad_recibida'];?></td>

									<? if ($data['OrdenComprasVentaDetalleProducto']['cantidad'] > $data['OrdenComprasVentaDetalleProducto']['cantidad_recibida'] ) : ?>

									<td><?=$this->Form->input(sprintf('%d.Bodega.0.cantidad', $ipp), array('type' => 'text', 'class' => 'form-control not-blank is-number js-cantidad-recibida', 'max' => $data['OrdenComprasVentaDetalleProducto']['cantidad']));?></td>
									
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
					<h5 class="panel-title"><i class="fa fa-file" aria-hidden="true"></i> <?=__('Facturas');?></h5>
					<ul class="panel-controls">
                        <li><a href="#" class="copy_tr"><span class="fa fa-plus"></span></a></li>
                    </ul>
				</div>
				<div class="panel-body">

					<div class="table-responsive">
						<table class="table table-bordered">
							<caption><?= __('Agregue los folios de las facturas recibidas para ésta OC'); ?></caption>
							<thead>
								<tr>
									<th><?= __('Tipo de documento');?></th>
									<th><?= __('Folio del decumento');?></th>
									<th><?= __('Nota interna del documento');?></th>
									<th>Acciones</th>
								</tr>
							</thead>
							<tbody class="">
								<tr class="hidden clone-tr">
									<td>
										<?= $this->Form->select('OrdenCompraFactura.999.tipo_documento', $tipo_documento , array( 'disabled' => true, 'class' => 'form-control not-blank', 'empty' => 'Seleccione tipo documento')); ?>
									</td>
									<td>
										<?= $this->Form->input('OrdenCompraFactura.999.folio', array('type' => 'text', 'disabled' => true, 'class' => 'form-control is-number not-blank', 'placeholder' => 'Ej: 4433')); ?>
									</td>	
									<td>
										<?= $this->Form->input('OrdenCompraFactura.999.nota', array('type' => 'textarea', 'disabled' => true, 'class' => 'form-control', 'placeholder' => 'Agregue un nota (opcional)')); ?>
									</td>							
									<td valign="center">
										<button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
									</td>
								</tr>

								<? if (!empty($this->request->data['OrdenCompraFactura'])) :  ?>
								<? foreach($this->request->data['OrdenCompraFactura'] as $ip => $dte) : ?>
								<tr>
									<td>
										<?=$this->Form->hidden(sprintf('OrdenCompraFactura.%d.id', $ip), array('value' => $dte['id'])); ?>
										<?= $this->Form->select(sprintf('OrdenCompraFactura.%d.tipo_documento', $ip), $tipo_documento, array('class' => 'form-control not-blank', 'empty' => 'Seleccione tipo documento', 'default' => $dte['tipo_documento'])); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('OrdenCompraFactura.%d.folio', $ip), array('type' => 'text', 'class' => 'form-control is-number not-blank', 'placeholder' => 'Ej:  4433', 'value' => $dte['folio'])); ?>
									</td>
									<td>
										<?= $this->Form->input(sprintf('OrdenCompraFactura.%d.nota', $ip), array('type' => 'textarea', 'class' => 'form-control', 'placeholder' => 'Agregue un nota (opcional)', 'value' => $dte['nota'])); ?>
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
