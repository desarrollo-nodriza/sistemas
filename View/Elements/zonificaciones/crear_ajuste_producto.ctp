<div class="table-responsive">
    <table class="table table-bordered">
        <thead>
            <tr>
                <th><?= __('Ubicación');?></th>
                <th><?= __('Cantidad actual');?></th>
                <th><?= __('Cantidad a ajustar');?></th>
                <th><?= __('Precio');?></th>
                <th><?= __('Glosa');?></th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody >
           
                <? foreach ($zonificaciones as $key => $zonificacion ) : ?>
                    <? if ($zonificacion[0]['cantidad']!=0) : ?>
                        <tr>
                            <td align="center" style="width: 400px">
                                <?= $this->Form->input(sprintf('%d.id', $key), array('default'=>$zonificacion['Ubicacion']['id'])); 
                                    echo($zonificacion['Ubicacion']['Zona']['Bodega']['nombre'] . ' ' . $zonificacion['Ubicacion']['Zona']['nombre'].' - '.$zonificacion['Ubicacion']['columna'].' - '.$zonificacion['Ubicacion']['fila']);
                                ?>
                            </td>
                           <td align="center">
                                <?=  $zonificacion[0]['cantidad'];?>
                            </td>										
                           <td align="center">
                                <?=$this->Form->input(sprintf('%d.cantidad', $key), array('type'=> "text", 'class' => 'is-number form-control', 'min' => 0));?>
                            </td>
                           <td align="center">
                                <div class="input-group">
                                    <?=$this->Form->input(sprintf('%d.costo', $key), array( 'class' => 'js-costo-pmp form-control  is-number' )); ?>
                                    <span class="input-group-addon js-usar-pmp" data-value="<?= $PMP[$zonificacion['Ubicacion']['Zona']['bodega_id']];?>" data-toggle="tooltip" title="PMP del producto: <?=$this->Number->currency($PMP[$zonificacion['Ubicacion']['Zona']['bodega_id']], 'CLP');?>"><span class="fa fa-check"></span> Usar PMP</span>
                                </div>	
                               
                            </td>	
                           <td align="center" style="width: 400px">
                                <div class="form-group">
                                    <?echo $this->Form->checkbox(sprintf('%d.manual', $key), array('hiddenField' => true,'class' => 'js-manual'));?>
                                    
                                   <label for="manual"> Escribir glosa manual</label><br>
                                    <?=$this->Form->select(sprintf('%d.glosa', $key), $movimientos, array(
                                        'class' => 'form-control js-input-select',
                                        'empty' => 'Seleccione',
                                     
                                    ))?>
                                    <?=$this->Form->input(sprintf('%d.glosa_manual', $key), array('type'=> "text", 'class' => 'form-control js-input-manual hidden',));?>
                                   
                                </div>
                            </td>		
                            <td align="center">
                                <?='-';?>
                            </td>	
                            
                        </tr>

                        <? endif; ?>
                <? endforeach; ?>
                
                <? for ($i=1; $i <= 20; $i++): ?>

                <? $key = count($zonificaciones)+$i ;?>
                    <tr class="hidden clone-tr">
                    <td style="width: 400px">
                        <?=$this->Form->select(sprintf('%d.id', $key), $ubicaciones, array(
                            'empty' => 'Seleccione Ubicación', 
                            'class' =>[ 'form-control', 'mi-selector'],
                            'style'=>"width: 400px"
                            // 'data-live-search' => true
                            )
                            ); ?>
                    </td>
                    <td align="center">
                        <?='-';?>
                    </td>
                    <td align="center">
                        <?=$this->Form->input(sprintf('%d.cantidad', $key), array('type'=> "text", 'class' => 'is-number form-control', 'min' => 0));?>
                    </td>
                    <td align="center">
                                <div class="input-group">
                                    <?=$this->Form->input(sprintf('%d.costo', $key), array( 'class' => 'js-costo-pmp form-control  is-number' )); ?>
                                    <span class="input-group-addon js-usar-pmp" data-value="<?= $PMP[1];?>" data-toggle="tooltip" title="PMP del producto: <?=$this->Number->currency($PMP[1], 'CLP');?>"><span class="fa fa-check"></span> Usar PMP</span>
                                </div>	
                               
                            </td>	
                    <td align="center" style="width: 400px">
                        <div class="form-group">
                            <?echo $this->Form->checkbox(sprintf('%d.manual', $key), array('hiddenField' => true,'class' => 'js-manual'));?>
                            
                            <label for="manual"> Escribir glosa manual</label><br>
                            <?=$this->Form->select(sprintf('%d.glosa', $key), $movimientos, array(
                                'class' => 'form-control js-input-select',
                                'empty' => 'Seleccione',
                               
                            ))?>
                            <?=$this->Form->input(sprintf('%d.glosa_manual', $key), array('type'=> "text", 'class' => 'form-control js-input-manual hidden',));?>
                            
                        </div>
                    </td>		
                    <td valign="center" align="center">
                        <button class="remove_tr btn-danger"><i class="fa fa-minus"></i></button>
                    </td>
                </tr>
                <? endfor; ?>
               
        </tbody>
    </table>
</div>

