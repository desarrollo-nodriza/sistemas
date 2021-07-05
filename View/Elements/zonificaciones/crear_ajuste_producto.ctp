<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?= __('Ubicación');?></th>
                <th><?= __('Cantidad actual');?></th>
                <th><?= __('Cantidad a ajustar');?></th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody class="">
            <tr class="hidden clone-tr">
                <td style="width: 500px">
                    <?=$this->Form->select('999.id', $ubicaciones, array('empty' => 'Seleccione Ubicación', 'class' =>[ 'form-control', 'mi-selector'],'style'=>"width: 400px")); ?>
                </td>
                <td align="center">
                    <?='-';?>
                </td>
                <td>
                    <?=$this->Form->input('999.cantidad', array('type'=> "text", 'class' => 'is-number form-control', 'min' => 0));?>
                </td>
                <td valign="center" align="center">
                    <button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
                </td>
            </tr>
                <? foreach ($zonificaciones as $key => $zonificacion ) : ?>
                   
                        <tr>
                            <td>
                                <?= $this->Form->input(sprintf('%d.id', $key), array('default'=>$zonificacion['Ubicacion']['id'])); 
                                    echo($zonificacion['Ubicacion']['Zona']['nombre'].' - '.$zonificacion['Ubicacion']['columna'].' - '.$zonificacion['Ubicacion']['fila']);
                                ?>
                            </td>
                            <td>
                                <?=  $zonificacion[0]['cantidad'];?>
                            </td>										
                            <td>
                                <?=$this->Form->input(sprintf('%d.cantidad', $key), array('type'=> "text", 'class' => 'is-number form-control', 'min' => 0));?>
                            </td>
                            <td align="center">
                                <?='-';?>
                            </td>	
                            
                        </tr>

                    
                <? endforeach; ?>
        </tbody>
    </table>
</div>

