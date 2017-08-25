<div class="page-title">
	<h2><span class="fa fa-shopping-basket"></span> Mi cuenta Mercado Libre</h2>
	<div class="pull-right">
		<? if (!empty($url) && $this->Session->check('Meli.access_token')) : ?>
			<div class="btn-group">
	            <a href="#" data-toggle="dropdown" class="btn btn-warning dropdown-toggle" aria-expanded="false">Aplicación Desconectada <span class="caret"></span></a>
	            <ul class="dropdown-menu pull-right" role="menu">
	                <li><?= $this->Html->link('Conectar aplicación', $url, array('escape' => false)); ?></li>
	            </ul>
	        </div>
		<? else : ?>
			<div class="btn-group">
	            <a href="#" data-toggle="dropdown" class="btn btn-success dropdown-toggle" aria-expanded="false">Aplicación Conectada <span class="caret"></span></a>
	            <ul class="dropdown-menu pull-right" role="menu">
	                <li><?= $this->Html->link('Ver mi cuenta', array('action' => 'usuario'), array('escape' => false)); ?></li>
	                <li><?= $this->Html->link('Desconectar aplicación', array('action' => 'desconectar'), array('escape' => false)); ?></li>                                                    
	            </ul>
	        </div>
		<? endif; ?>
	</div>
</div>

<div class="page-content-wrap" id="meli-account">
	<div class="row">
	<? if(isset($totalVisitasMes['body']['total_visits'])) : ?>
		<div class="col-xs-12 col-sm-3">
            <a href="#" class="tile tile-primary">
                <?= $totalVisitasMes['body']['total_visits'] ?>
                <p>Visitas totales del mes</p>                            
                <div class="informer informer-default"><span class="fa fa-eye"></span></div>
            </a>
        </div>
    <? endif; ?>
	</div>
	<div class="row">
        <div class="col-xs-12">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <h3 class="panel-title">Histórico de Visitas</h3>
                    <?= $this->Form->create('Visitas', array('class' => 'form-horizontal', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control'))); ?>
                    <ul class="panel-controls">      
                        <li><label class="control-label">Rango </label></li>
                        <li>
                            <div class="input-group">
                                <?=$this->Form->input('f_inicio', array('class' => 'form-control datepicker'));?>
                                <span class="input-group-addon add-on"> - </span>
                                <?=$this->Form->input('f_final', array('class' => 'form-control datepicker'));?>
                            </div>
                        </li>
                        <li><label class="control-label">Agrupar</label></li>
                        <li>
                            <div class="input-group">
                                <?=$this->Form->select('agrupar', array('anno' => 'Año', 'mes' => 'Mes', 'dia' => 'Día', 'hora' => 'Hora'), array('empty' => false, 'class' => 'form-control'));?>
                            </div>
                        </li>
                        <li><a id="enviarFormularioVisitasMeli" href="#" class="panel-refresh"><span class="fa fa-refresh"></span></a></li>
                        <li><a href="#" class="panel-fullscreen"><span class="fa fa-expand"></span></a></li>
                        <li><a href="#" class="panel-collapse"><span class="fa fa-angle-down"></span></a></li>
                    </ul>
                    <?= $this->Form->end(); ?>
                </div>
                <div class="panel-body">
                    <div id="HistoricoVisitasMeli" style="height: 300px;">
                        
                    </div>
                </div>                             
            </div>
        </div>
    </div>
	<div class="row">
		<div class="col-xs-12 col-sm-8">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Información de la cuenta de Mercado libre</h3>
				</div>
				<div class="panel-body">
					<? if (!empty($miCuenta)) : ?>
					<div class="table-responsive">
						<table class="table table-striped">
							<tr>
								<th><label>ID</label></th>
								<td><?=$miCuenta['id'];?></td>
							</tr>
							<tr>
								<th><label>Usuario</label></th>
								<td><?=$miCuenta['nickname'];?></td>
							</tr>
							<tr>
								<th><label>Nombre</label></th>
								<td><?=$miCuenta['first_name'];?></td>
							</tr>
							<tr>
								<th><label>Apellido</label></th>
								<td><?=$miCuenta['last_name'];?></td>
							</tr>
							<tr>
								<th><label>Email</label></th>
								<td><?=$miCuenta['email'];?></td>
							</tr>
							<? if (!empty($miCuenta['logo'])) : ?>
							<tr>
								<th><label>Imagen</label></th>
								<td><?=$this->Html->image($miCuenta['logo'], array('class' => 'thumbnail img-responsive'));?></td>
							</tr>
							<? endif; ?>
							<tr>
								<th><label>Fecha registro</label></th>
								<td><?=$miCuenta['registration_date'];?></td>
							</tr>
						</table>
					</div>
					<? else : ?>
						<label class="label label-warning">Debe iniciar sesión para trabajar con Mercado libre</label>
					<? endif; ?>
				</div>
			</div>
		</div> <!-- end col -->
		<div class="col-xs-12 col-sm-4">
			<div class="panel panel-default">
				<div class="panel-heading">
					<h3 class="panel-title">Marcas/tiendas de Mercado libre</h3>
				</div>
				<div class="panel-body">
					<? if (isset($miMarcas['body']['brands']) && !empty($miMarcas['body']['brands'])) : ?>
					<div class="table-responsive">
						<table class="table table-striped">
							<thead>
								<th>Identificador</th>
								<th>Marca</th>
							</thead>
							<tbody>
								<? foreach($miMarcas['body']['brands'] as $ix => $marca) : ?>
								<tr>
									<td><?=$marca['official_store_id']?></td>
									<td><?=$marca['name']?></td>
								</tr>
								<? endforeach; ?>
							</tbody>
						</table>
					</div>
					<? else: ?>
						<label class="label label-warning">No tiene marcas asociadas</label>
					<? endif; ?>
				</div>
			</div>
		</div>
	</div> <!-- end row -->
</div>
