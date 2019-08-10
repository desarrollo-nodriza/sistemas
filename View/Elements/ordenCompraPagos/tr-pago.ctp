<tr data-id="<?=$pago['OrdenCompraPago']['id'];?>">

	<td>
		<!-- Hidden inputs -->
		<input type="hidden" name="data[OrdenCompraPago][<?=$index;?>][id]" value="<?=$pago['OrdenCompraPago']['id'];?>"/>
		<input type="hidden" name="data[OrdenCompraPago][<?=$index;?>][orden_compra_id]" value="<?=$pago['OrdenCompraPago']['orden_compra_id'];?>"/>
		<input type="hidden" name="data[OrdenCompraPago][<?=$index;?>][monto_real_pagado]" value="<?=$pago['OrdenCompraPago']['monto_real_pagado'];?>"/>

		<select class="form-control not-blank js-select-medio-pago" name="data[OrdenCompraPago][<?=$index;?>][moneda_id]"><?=$opcionesMoneda;?></select>


		<div class="modal fade" tabindex="-1" role="dialog" id="modal-orden-compra-pago-<?=$pago['OrdenCompraPago']['id'];?>">
		  <div class="modal-dialog" role="document">
		    <div class="modal-content">
		      <div class="modal-header">
		        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
		        <h4 class="modal-title"><i class="fa fa-file"></i> Facturas disponibles para asignar</h4>
		      </div>
		      <div class="modal-body">
		        <div class="row">
					<div class="col-xs-12 text-center">
						<h2>Monto disponible para asignar <span data-disponible-pagar="<?=$pago['OrdenCompraPago']['monto_pagado']; ?>" data-disponible-pagar-original="<?=$pago['OrdenCompraPago']['monto_pagado']; ?>" class="js-disponible-asignar"><?=CakeNumber::currency($pago['OrdenCompraPago']['monto_pagado'], 'CLP'); ?></span></h2>
					</div>
		        </div>
		        <div class="table-responsive">
					<table class="table table-bordered">
						<caption>Relacione el pago a las facturas correspondientes</caption>
						<th>Usar</th>
						<th>Tipo documento</th>
						<th>Folio</th>
						<th>OC relacionada</th>
						<th>Proveedor</th>
						<th>Monto facturado</th>
						<th>Monto pendinte pago</th>
						<tbody>
						<? foreach ($pago['OrdenCompra']['OrdenCompraFactura'] as $iocf => $ocf) : ?>
							<tr>
								<td>
									<input type="hidden" name="data[OrdenCompraPago][<?=$index;?>][OrdenCompraFactura][<?=$iocf;?>][id]" value="<?=$ocf['id']?>">
									<input type="hidden" class="js-factura-monto-pagar" name="data[OrdenCompraPago][<?=$index;?>][OrdenCompraFactura][<?=$iocf;?>][monto_pagado]">
									<input data-pendiente-pago="<?=$ocf['monto_facturado'] - $ocf['monto_pagado'];?>" data-pendiente-pago-original="<?=$ocf['monto_facturado'] - $ocf['monto_pagado'];?>" type="checkbox" class="js-select-factura" name="data[OrdenCompraPago][<?=$index;?>][OrdenCompraFactura][<?=$iocf;?>][orden_compra_factura_id]" value="<?=$ocf['id'];?>">
								</td>
								<td><?=$this->Html->tipoDocumento[$ocf['tipo_documento']];?></td>
								<td><?=$ocf['folio'];?></td>
								<td><?=$this->Html->link('#'.$ocf['orden_compra_id'], array('controller' => 'ordenCompras', 'action' => 'view', $ocf['orden_compra_id']), array('target' => '_blank'));?></td>
								<td><?=$pago['OrdenCompra']['Proveedor']['nombre']; ?></td>
								<td><?=CakeNumber::currency($ocf['monto_facturado'], 'CLP'); ?></td>
								<td class="js-pendiente-pago"><?=CakeNumber::currency($ocf['monto_facturado'] - $ocf['monto_pagado'], 'CLP'); ?></td>
							</tr>
						<? endforeach; ?>
						</tbody>
					</table>
		        </div>
		      </div>
		      <div class="modal-footer">
		        <button type="button" data-dismiss="modal" class="btn btn-primary">Ok</button>
		      </div>
		    </div><!-- /.modal-content -->
		  </div><!-- /.modal-dialog -->
		</div><!-- /.modal -->
	</td>

	<? if ($pago['Moneda']['tipo'] == 'esperar') : ?>
	<td>
		<input type="text" class="form-control not-blank hidden" name="data[OrdenCompraPago][<?=$index;?>][folio_factura]" value="<?=$pago['OrdenCompraPago']['folio_factura'];?>" placeholder="N° folio"/>
		<a type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal-orden-compra-pago-<?=$pago['OrdenCompraPago']['id'];?>" <?=($pago['OrdenCompraPago']['fecha_pago'] > date('Y-m-d')) ? 'disabled' : '' ; ?>><i class="fa fa-eye"></i> Facturas</a>
	</td>
	<? else : ?>
	<td>
		<input type="text" class="form-control hidden" name="data[OrdenCompraPago][<?=$index;?>][folio_factura]" value="<?=$pago['OrdenCompraPago']['folio_factura'];?>" placeholder="N° folio"/>
		<a type="button" class="btn btn-primary btn-block" data-toggle="modal" data-target="#modal-orden-compra-pago-<?=$pago['OrdenCompraPago']['id'];?>" <?=($pago['OrdenCompraPago']['fecha_pago'] > date('Y-m-d')) ? 'disabled' : '' ; ?>><i class="fa fa-eye"></i> Facturas</a>
	</td>
	<? endif; ?>

	<td>
		<input type="text" class="form-control not-blank" name="data[OrdenCompraPago][<?=$index;?>][identificador]" value="<?=$pago['OrdenCompraPago']['identificador'];?>" placeholder="N° transacción/cheque"/>
	</td>

	<td>
		<input type="text" class="form-control not-blank" name="data[OrdenCompraPago][<?=$index;?>][cuenta]" value="<?=$pago['OrdenCompraPago']['cuenta'];?>" placeholder="N° de cuenta bancaria"/>
	</td>

	<td>
		<input type="text" class="form-control is-number js-monto-pagado" name="data[OrdenCompraPago][<?=$index;?>][monto_pagado]" value="<?=$pago['OrdenCompraPago']['monto_pagado'];?>" placeholder="Monto pagado" readonly/>
	</td>

	<td>
		<input type="text" class="form-control is-number not-blank js-monto-pendiente" name="data[OrdenCompraPago][<?=$index;?>][monto_pendiente]" value="<?=$pago_pendiente;?>" max="<?=$pago_pendiente;?>" placeholder="Monto pendiente de pago"/>
	</td>

	<td>
		<input type="text" class="form-control datepicker js-agendar" name="data[OrdenCompraPago][<?=$index;?>][fecha_pago]" value="<?=$pago['OrdenCompraPago']['fecha_pago'];?>" placeholder="Ej: 2019-07-20"/>
	</td>

	<? if (!empty($pago['OrdenCompraPago']['adjunto'])) : ?>
	<td>
		<a class="btn btn-xs btn-primary" target="_blank" href="<?=Router::url( '/', true );?> 'webroot/img/<?= $pago['OrdenCompraPago']['adjunto']['path'];?>"><i class="fa fa-file-pdf-o"></i> Ver documento</a>
	</td>
	<? else : ?>
	<td>
		<input type="file" class="js-comprobante" name="data[OrdenCompraPago][<?=$index;?>][adjunto]" />
	</td>
	<? endif; ?>

	<td valign="center">
		<a target="_blank" href="<?=$result[$index]['url'];?>" class="ver-pago btn-info"><i class="fa fa-eye"></i></a><button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
	</td>

</tr>