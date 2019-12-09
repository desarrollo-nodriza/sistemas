<!-- Modal crear cliente -->
<div class="modal fade" id="modalCrearCliente" tabindex="-1" role="dialog" aria-labelledby="modalCrearClienteLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?= $this->Form->create('VentaCliente', array('class' => 'form-horizontal js-formulario js-ajax-form', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete' => false))); ?>
      
		<?=$this->Form->hidden('access_token', array('value' => $token)); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalCrearClienteLabel"><i class="fa fa-user-plus"></i> Crear cliente</h4>
      </div>
      <div class="modal-body">
      	
      		<div class="table-responsive">
				<table class="table table-bordered">	
					<tr>
						<th>Tipo cliente</th>
						<td><?=$this->Form->select('tipo_cliente', array('persona' => 'Persona', 'empresa' => 'Empresa'), array('label' => false, 'class' => 'form-control js-cliente-tipo', 'default' => 'persona', 'empty' => false))?></td>
					</tr>
					<tr>
						<th>Nombres</th>
						<td><?=$this->Form->input('nombre', array('class' => 'form-control not-blank', 'placeholder' => 'Ingrese nombres del cliente'))?></td>
					</tr>
					<tr>
						<th>Apellidos</th>
						<td><?=$this->Form->input('apellido', array('class' => 'form-control', 'placeholder' => 'Ingrese apellidos del cliente'))?></td>
					</tr>
					<tr>
						<th>Rut/Pasaporte</th>
						<td><?=$this->Form->input('rut', array('class' => 'form-control is-rut not-blank', 'placeholder' => 'Ingrese Rut/Pasaporte del cliente'))?></td>
					</tr>
					<tr>
						<th>Email</th>
						<td><?=$this->Form->input('email', array('class' => 'form-control is-email not-blank', 'placeholder' => 'Ingrese email del cliente'))?></td>
					</tr>
					<tr>
						<th>Fono (opcional)</th>
						<td><?=$this->Form->input('telefono', array('class' => 'form-control is-number', 'placeholder' => 'Ingrese fono del cliente'))?></td>
					</tr>
					<tr class="hidden">
						<th>Giro comercial</th>
						<td><?=$this->Form->input('giro_comercial', array('class' => 'form-control js-giro-comercial', 'placeholder' => 'Ingrese giro comercial'))?></td>
					</tr>
				</table>
      		</div>

      		<div class="alert alert-danger hidden">
				<span id="error-mensaje-cliente"></span>
			</div>

			<div class="alert alert-success hidden">
				<span id="success-mensaje-cliente"></span>
			</div>
      	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Crear cliente</button>
      </div>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>
<!-- Fin modal cliente -->

<?= $this->Html->script(array(
	'/backend/js/clientes')); ?>
<?= $this->fetch('script'); ?>