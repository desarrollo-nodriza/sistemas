<nav class="navbar navbar-default">
  <div class="container-fluid">
    <div class="navbar-header">
      <a class="navbar-brand" href="">
        <?=$this->Html->image('../backend/img/logo-grey.png', array('alt' => 'Nodriza Spa', 'class' => 'img-responsive'));?>
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
									<? if (isset($producto['PrisyncProducto'][$competidor])) : ?>
										<td class="<?= ($producto['PrisyncProducto']['min_price'] == $producto['PrisyncProducto'][$competidor]) ? 'success' : '' ; ?>" data-toggle="tooltip" data-placement="top" title="Precio anterior: <?=CakeNumber::currency($producto['PrisyncProducto'][$competidor . '_old'], 'CLP');?>"><?=CakeNumber::currency($producto['PrisyncProducto'][$competidor], 'CLP');?></td>
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