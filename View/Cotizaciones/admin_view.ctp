<div class="page-title">
	<h2><span class="fa fa-lightbulb-o"></span> Ver Cotización</h2>
</div>
<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12 form-inline">
			<div class="form-group nombre-cotizacion">
				<label style="width: 100%"><?=$this->request->data['Cotizacion']['nombre'];?></label>
			</div>
		</div>
		<div class="col-xs-12">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title"><?=$this->Html->image($tienda['Tienda']['logo']['path'], array('class' => 'img-responsive logo-cotizacion', 'fullBase' => true));?></h3>
				</div>
				<div class="panel-body">
					<div class="table-responsive tabla-cotizacion">
						<table>
							<tr>
								<td style="width: 65%;">
									<table>
										<tr>
											<td colspan="2"><b style="font-size: 16px;"><?=$tienda['Tienda']['nombre_fantasia'];?></b></td>
										</tr>
										<tr>
											<td><b>Rut:</b></td><td><?=$tienda['Tienda']['rut'];?></td>
										</tr>
										<tr>	
											<td><b>Dirección:</b></td><td><?=$tienda['Tienda']['direccion'];?></td>
										</tr>	
											<td><b>Giro:</b></td><td><?=$tienda['Tienda']['giro'];?></td>
										<tr>	
											<td><b>Fono:</b></td><td><?=$tienda['Tienda']['fono'];?></td>
										</tr>
										<? if ( $this->request->data['Prospecto']['datos_bancarios'] && !empty($tienda['Tienda']['detalle_cuenta'])) : ?>
										<tr>
											<td><b>Datos para <br>la transerencia:</b></td><td><?=$this->Text->autoParagraph($tienda['Tienda']['detalle_cuenta']);?></td>
										</tr>
										<? endif; ?>
										<tr>	
											<td colspan="2"><a href="http://<?=$tienda['Tienda']['url'];?>" target="_blank"><?=$tienda['Tienda']['url'];?></a></td>
										</tr>
									</table>
								</td>
								<td style="vertical-align: middle; font-size: 30px; font-weight: bold; text-align: center;">
									Cotización <?=$this->request->data['Cotizacion']['id'];?>
								</td>
							</tr>
						</table>
						<table>
							<tr>
								<td style=" width: 65%;"><b>Datos de Cliente</b></td>
								<td><b>Condiciones comerciales de la oferta:</b></td>
							</tr>
							<tr>
								<td>
									<table>
										<tr>
											<td>Email:</td><td><?=$this->request->data['Cotizacion']['email_cliente'];?></td>
										</tr>
										<tr>
											<td>Nombre:</td><td><?=$this->request->data['Cotizacion']['nombre_cliente'];?></td>
										</tr>
										<tr>	
											<td>Teléfono:</td><td><?=$this->request->data['Cotizacion']['fono_cliente'];?></td>
										</tr>
										<tr>	
											<td>Dirección:</td><td><?=$this->request->data['Cotizacion']['direccion_cliente'];?></td>
										</tr>
										<tr>	
											<td>Asunto:</td><td><?=$this->request->data['Cotizacion']['asunto_cliente'];?></td>
										<? if (! empty($this->request->data['Cotizacion']['rut_empresa_cliente']) ) : ?>
											<tr>
												<td>Rut Empresa: </td><td><?=$this->request->data['Cotizacion']['rut_empresa_cliente'];?></td>
											</tr>
										<? endif; ?>
										<? if ( ! empty($this->request->data['Cotizacion']['nombre_empresa_cliente'])) : ?>
											<tr>
												<td>Empresa: </td><td><?=$this->request->data['Cotizacion']['nombre_empresa_cliente'];?></td>
											</tr>
										<? endif; ?>
										</tr>
									</table>
								</td>
								<td>
									<table>
										<tr>
											<td>Fecha:</td><td><?=$this->request->data['Cotizacion']['fecha_cotizacion'];?></td>
										</tr>
										<tr>
											<td>Oferta válida solo:</td><td><?=$this->request->data['ValidezFecha']['valor'];?></td>
										</tr>
										<tr>
											<td>Forma de pago:</td><td><?=$this->request->data['Moneda']['nombre'];?></td>
										</tr>
										<tr>
											<td>Envío:</td><td><?=$this->request->data['Cotizacion']['envio_cotizacion'];?></td>
										</tr>
										<tr>
											<td>Vendedor:</td><td><?=$this->request->data['Cotizacion']['vendedor'];?></td>
										</tr>
										<tr>
											<td>Email vendedor:</td><td><?=$this->request->data['Cotizacion']['email_vendedor'];?></td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
						<table>
							<tr>
								<td>Código</td>
								<td>Detalles del producto</td>
								<td>Cantidad</td>
								<td>Neto</td>
								<td>Total</td>
							</tr>
							<? foreach ($productos as $inx => $producto) : ?>
								<tr>
									<td><?=$producto['Productotienda']['reference'];?></td>
									<td><?=$producto['Lang'][0]['ProductotiendaIdioma']['name'];?></td>
									<td><?=$producto['Productotienda']['cantidad']; ?></td>
									<td><?=$producto['Productotienda']['precio_neto']?></td>
									<td><?=$producto['Productotienda']['total_neto']; ?></td>
								</tr>								
							<? endforeach; ?>
							<tr>
								<td colspan="4"><b>Total Neto</b></td><td><?=$this->request->data['Cotizacion']['total_neto'];?></td>
							</tr>
							<tr>
								<td colspan="4"><b>Descuento</b></td><td><?=$this->request->data['Cotizacion']['descuento'];?></td>
							</tr>
							<tr>
								<td colspan="4"><b>IVA</b></td><td><?=$this->request->data['Cotizacion']['iva'];?></td>
							</tr>
							<? if (!empty($this->request->data['Transporte']['id'])) : ?>
							<tr>
								<td colspan="4"><b><?= $this->request->data['Transporte']['nombre']; ?></b></td><td><?= $this->request->data['Cotizacion']['transporte'];?></td>
							</tr>
							<? endif; ?>
							<tr>
								<td colspan="4"><b>Total</b></td><td><?=$this->request->data['Cotizacion']['total_bruto'];?></td>
							</tr>
						</table>
					</div>
				</div>
				<div class="panel-footer">
					<div class="pull-right">
						<? if( ! empty($this->request->data['Cotizacion']['archivo']) )?>
						<a href="<?=$this->request->data['Cotizacion']['archivo'];?>" download class="btn btn-primary">Descargar en PDF</a>
						<?= $this->Html->link('Volver', array('action' => 'index'), array('class' => 'btn btn-danger')); ?>
					</div>
				</div>
			</div>
		</div> <!-- end col -->
	</div> <!-- end row -->
</div>