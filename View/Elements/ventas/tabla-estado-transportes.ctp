<? foreach ($venta['Transporte'] as $ivt => $t) : ?>
<div class="panel panel-primary">
    <div class="panel-heading">
        <h3 class="panel-title"><?=$t['nombre'];?> - N° <?=$t['TransportesVenta']['cod_seguimiento']; ?> | Embalaje <?=$t['TransportesVenta']['embalaje_id']; ?></h3>
    </div>
    <div class="panel-body panel-body-table">
        <div class="table-responsive">
            <table class="table table-stripped table-bordered table-bordered">
                <th>Id</th>
                <th>Fecha</th>
                <th>Estado</th>
                <th>Descripción</th>
                <th>Tipo</th>
                <th>Estado relacionado</th>
            <? foreach ($t['TransportesVenta']['EnvioHistorico'] as $estado) : ?>
                <tr>
                    <td><?=$estado['EnvioHistorico']['id']; ?></td>
                    <td><?=$estado['EnvioHistorico']['created']; ?></td>
                    <td><label class="label label-<?=$estado['EstadoEnvio']['EstadoEnvioCategoria']['clase']??'info';?>"><?=$estado['EstadoEnvio']['nombre']; ?></label></td>
                    <td><?=$estado['EnvioHistorico']['leyenda']??'-'; ?></td>
                    <td><?=$estado['EstadoEnvio']['EstadoEnvioCategoria']['nombre']??'-';?></td>
                    <td>
                    <? if ($estado['EstadoEnvio']['EstadoEnvioCategoria']['venta_estado_id']??false) : ?>
                        <span data-toggle="tooltip" data-placement="top" title="" data-original-title="<?=$estado['EstadoEnvio']['EstadoEnvioCategoria']['VentaEstado']['nombre'];?>" class="btn btn-xs btn-<?= $estado['EstadoEnvio']['EstadoEnvioCategoria']['VentaEstado']['VentaEstadoCategoria']['estilo']; ?>">
                            <?= $estado['EstadoEnvio']['EstadoEnvioCategoria']['VentaEstado']['VentaEstadoCategoria']['nombre']; ?>
                        </span>
                    <? else : ?>
                        --
                    <? endif; ?>
                    </td>
                </tr>
            <? endforeach; ?>
            </table>
        </div>
    </div>
</div>
<? endforeach; ?>