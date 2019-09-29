<!-- Modal -->
<div class="modal fade" id="modal-venta-manual" tabindex="-1" role="dialog" aria-labelledby="modal-venta-manual-label">
  <div class="modal-dialog modal-lg" role="document">
    <?= $this->Form->create('Venta', array('url' => array('controller' => 'ventas', 'action' => 'obtener_venta_manual'), 'inputDefaults' => array('div' => false, 'label' => false))); ?>
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title" id="modal-venta-manual-label">Obtener venta desde canal</h4>
      </div>
      <div class="modal-body">
        <div class="form-group col-xs-12 col-md-4">
          <?= $this->Form->label('id_externo', 'Idenitificador de la venta en el canal')?>
          <?= $this->Form->input('id_externo', array('class' => 'form-control not-blank', 'placeholder' => 'Ej: 443324, 355544222, 98896543734')); ?>
        </div>
        <div class="form-group col-xs-12 col-md-4">
          <?= $this->Form->label('tienda_id', 'Seleccione la tienda')?>
          <?= $this->Form->select('tienda_id', $tiendas, array('class' => 'form-control not-blank', 'empty' => false)); ?>
        </div>
        <div class="form-group col-xs-12 col-md-4">
          <?= $this->Form->label('marketplace_id', 'Marketplace (opcional)')?>
          <?= $this->Form->select('marketplace_id', $marketplaces, array('class' => 'form-control not-blank', 'empty' => 'Seleccione marketplace')); ?>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cancelar</button>
        <button type="submit" class="btn btn-primary">Obtener/actualizar</button>
      </div>
    </div>
    <?= $this->Form->end(); ?>
  </div>
</div>