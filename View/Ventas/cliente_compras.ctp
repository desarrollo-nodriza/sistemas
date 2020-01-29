<div class="container">
  <div class="row">
    <div class="col-12">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
            <caption><?=$this->element('public/contador-resultados-cliente'); ?> <?= $this->element('public/paginacion-cliente');?></caption>
            <thead>
              <tr>
                <th scope="col">N° venta</th>
                <th scope="col">Referencia</th>
                <th scope="col">Monto</th>
                <th scope="col">Estado</th>
                <th scope="col" style="width: 125px;">Fecha compra</th>
                <th scope="col">Cant Productos</th>
                <th scope="col">Boleta/Factura</th>
                <th scope="col">Acciones</th>
              </tr>
            </thead>
            <tbody>
            <? if (!empty($ventas)) : ?>
            <? foreach ($ventas as $iv => $venta) : ?>
              <tr>
                <th scope="row">#<?=$venta['Venta']['id'];?></th>
                <td><?=$venta['Venta']['referencia'];?></td>
                <td><?=CakeNumber::currency($venta['Venta']['total'], 'CLP');?></td>
                <td><a data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$venta['VentaEstado']['nombre'];?>" class="btn btn-sm text-white btn-<?= h($venta['VentaEstado']['VentaEstadoCategoria']['estilo']); ?>"><?= h($venta['VentaEstado']['VentaEstadoCategoria']['nombre']); ?></a>&nbsp;</td>
                <td><?=$venta['Venta']['fecha_venta'];?></td>
                <td><?=array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad')) - array_sum(Hash::extract($venta['VentaDetalle'], '{n}.cantidad_anulada'));?></td>
                <td>
                <? if (!empty($venta['Dte'])) : ?>
                <? foreach ($venta['Dte'] as $dte) : ?>
                  <a href="<?=$dte['public'];?>" class="btn btn-sm btn-info btn-block" target="_blank"><i class="fa fa-file-pdf"></i> Descargar</a>
                <? endforeach; ?>
                <? endif; ?>  
                </td>
                <td><?=$this->Html->link('<i class="fa fa-eye mr-1"></i> Ver detalles', array('controller' => 'ventas', 'action' => 'ver', 'id' => $venta['Venta']['id']), array('escape' => false, 'class' => 'btn btn-sm btn-primary')); ?></td>
              </tr>
            <? endforeach; ?>
            <? else : ?>
              <tr>
                <th scope="row" colspan="6">No tienes compras relacionadas.</th>
              </tr>
            <? endif; ?>
            </tbody>
          </table>

      </div>
    </div>
  </div>
</div>