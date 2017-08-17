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

<div class="page-content-wrap">
	<div class="row">
		<div class="col-xs-12">
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
	</div> <!-- end row -->
</div>
