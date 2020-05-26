<div class="container">
  <div class="row">
    <div class="col-12">
      <div class="table-responsive">
        <table class="table table-striped table-hover">
            <caption><?=$this->element('public/contador-resultados-cliente'); ?> <?= $this->element('public/paginacion-cliente');?></caption>
            <thead>
              <tr>
                <th scope="col">N° cotización</th>                
                <th scope="col">Total cotizado</th>
                <th scope="col">Fecha creación</th>
                <th scope="col">Pdf</th>
              </tr>
            </thead>
            <tbody>
            <? if (!empty($cotizaciones)) : ?>
            <? foreach ($cotizaciones as $iv => $cotizacion) : ?>
              <tr>
                <th scope="row">#<?=$cotizacion['Cotizacion']['id'];?></th>
                <td><?=CakeNumber::currency($cotizacion['Cotizacion']['total_bruto'], 'CLP');?></td>
                <td><?=$cotizacion['Cotizacion']['fecha_cotizacion'];?></td>
                <td>
                <? if (!empty($cotizacion['Cotizacion']['archivo'])) : ?>
                  <a href="<?=$cotizacion['Cotizacion']['archivo'];?>" class="btn btn-sm btn-info btn-block" target="_blank"><i class="fa fa-file-pdf"></i> Descargar</a>
                <? else : ?>
                  --
                <? endif; ?>
                </td>
              </tr>
            <? endforeach; ?>
            <? else : ?>
              <tr>
                <th scope="row" colspan="6">No tienes cotizaciones relacionadas.</th>
              </tr>
            <? endif; ?>
            </tbody>
          </table>

      </div>
    </div>
  </div>
</div>