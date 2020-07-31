<!-- Modal crear Dirección -->
<div class="modal fade" id="modalCrearDireccion" tabindex="-1" role="dialog" aria-labelledby="modalCrearDireccionLabel">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <?= $this->Form->create('Direccion', array('id' => 'DireccionAdminAddForm', 'class' => 'form-horizontal js-formulario js-ajax-form', 'data-id' => '', 'type' => 'file', 'inputDefaults' => array('label' => false, 'div' => false, 'class' => 'form-control', 'autocomplete' => false))); ?>
      
		<?=$this->Form->hidden('access_token', array('value' => $token)); ?>
		<?=$this->Form->hidden('venta_cliente_id'); ?>
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modalCrearDireccionLabel"><i class="fa fa-user-plus"></i> Crear/Editar Dirección</h4>
      </div>
      <div class="modal-body">
      	
      		<div class="table-responsive">
				<table class="table table-bordered">	
					<tr>
						<th>Alias</th>
						<td><?=$this->Form->input('alias', array('class' => 'form-control not-blank', 'placeholder' => 'Casa/Trabajo/Oficina'))?></td>
					</tr>
					<tr>
						<th>Calle/Pasaje</th>
						<td><?=$this->Form->input('calle', array('class' => 'form-control not-blank', 'placeholder' => 'Ingrese calle'))?></td>
					</tr>
					<tr>
						<th>Número</th>
						<td><?=$this->Form->input('numero', array('class' => 'form-control not-blank', 'placeholder' => 'Ingrese n° casa/edificio/block'))?></td>
					</tr>
					<tr>
						<th>Departamento/oficina (opcional)</th>
						<td><?=$this->Form->input('depto', array('class' => 'form-control', 'placeholder' => 'Ingrese departamento'))?></td>
					</tr>
					<tr>
						<th>Comuna</th>
						<td><?=$this->Form->select('comuna_id', $comunas, array('class' => 'form-control not-blank select', 'data-live-search' => true, 'empty' => 'Seleccione'))?></td>
					</tr>
				</table>
      		</div>

      		<div class="alert alert-danger hidden">
				<span id="error-mensaje-direccion"></span>
			</div>

			<div class="alert alert-success hidden">
				<span id="success-mensaje-direccion"></span>
			</div>
      	
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary"><i class="fa fa-plus"></i> Aceptar</button>
      </div>
      <?= $this->Form->end(); ?>
    </div>
  </div>
</div>
<!-- Fin modal cliente -->

<?= $this->Html->script(array(
	'/backend/js/direcciones')); ?>
<?= $this->fetch('script'); ?>