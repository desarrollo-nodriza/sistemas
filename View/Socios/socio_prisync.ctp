<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="">
        <?=$this->Html->image(sprintf('Tienda/%d/%s', $this->Session->read('Auth.Socio.Tienda.id'), $this->Session->read('Auth.Socio.Tienda.logo')), array('alt' => 'Nodriza Spa', 'class' => 'img-responsive logo-socios'));?>
      </a> 
    </div>
    <div class="collapse navbar-collapse"> <p class="navbar-text navbar-right"><?=date('d/m/Y');?> - <?= $this->Html->link('<span class="fa fa-sign-out"></span> Cerrar Sesión', array('action' => 'logout'), array('class' => 'btn btn-default btn-xs', 'escape' => false)); ?></p></div>
  </div>
</nav>
<div class="row">
	<div class="col-xs-12">
		<h2>Bienvenid@ <?=$socio['Socio']['nombre'];?></h2>
		<p>En ésta plataforma encontrará la información de sus productos y las diferencias de precios en los distintos caneles de venta en internet.</p>
		<p><b>¡IMPORTANTE!</b> La información se actualiza automáticamente a las 09:00 y a las 14:00 de todos los días.</p>
	</div>
</div>

<div class="row">
	<div class="col-xs-12">
		<!-- START DEFAULT DATATABLE -->
        <div class="panel panel-default">
            <div class="panel-body">
                <div class="table-responsive">
                    <table class="table table-striped table-bordered datatable">
                        <thead>
                            <tr>
                                <th>Modelo</th>
                            	<? foreach($productos['competidores'] as $ic => $competidor) : ?>
								<th><?=$competidor;?></th>
                            	<? endforeach ?>
                            </tr>
                        </thead>
                        <tbody>
                        	<? foreach ($productos['productos'] as $ip => $producto) : ?>
                        	<tr>
                        		<td data-toggle="tooltip" data-placement="top" title="<?=$producto['PrisyncProducto']['name'];?>"><?= (empty($producto['PrisyncProducto']['internal_code'])) ? $producto['PrisyncProducto']['name'] : $producto['PrisyncProducto']['internal_code'] ;?></td>
                        		<? foreach($productos['competidores'] as $ic => $competidor) : ?>
									<? if (isset($producto['PrisyncProducto'][$competidor . '_price'])) : ?>
										<td data-titulo="<?=$competidor;?> - <?=$producto['PrisyncProducto']['name']; ?>" data-competidor="<?=$producto['PrisyncProducto'][$competidor . '_id'];?>" class="js-mostrar-grafico <?= ($producto['PrisyncProducto']['min_price'] == $producto['PrisyncProducto'][$competidor . '_price']) ? 'success' : '' ; ?>" ><?=($producto['PrisyncProducto'][$competidor  . '_available']) ? '<span>' : '<span class="text-red"><i class="fa fa-close"></i>' ;?> <?=CakeNumber::currency($producto['PrisyncProducto'][$competidor . '_price'], 'CLP');?></span></td>
									<? else : ?>
										<td>--</td>
									<? endif; ?>
                            	<? endforeach ?>
                        	</tr>	
                        	<? endforeach;?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- END DEFAULT DATATABLE -->
	</div>
</div>

<div id="modalSocios" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog modal-lg" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body form">
        <div class="row">
            <div class="col-xs-12 col-sm-6 form-group">
                <div class="input-group full-input-group">
                    <input id="fechaInicial" class="form-control datepicker" type="text" value="<?=date('Y-m-01');?>">
                    <span class="input-group-addon add-on"> - </span>
                    <input id="fechaFinal" class="form-control datepicker" type="text" value="<?=date('Y-m-t');?>">
                </div>
            </div>
            <div class="col-xs-12 col-sm-3 form-group">
                <select id="agrupado" class="form-control">
                    <option value="dia">Día</option>
                    <option value="semana">Semana</option>
                    <!--<option value="mes">Mes</option>
                    <option value="anno">Año</option>-->
                </select>
            </div>
            <div class="col-xs-12 col-sm-3 form-group">
                <input type="hidden" id="competidor">
                <button id="procesarGrafico" class="btn btn-success btn-block"><i class="fa fa-refresh"></i> Confirmar</button>
            </div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div id="graficoHistorico"></div>
            </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default btnCerrarModal" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->