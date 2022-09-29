<? if ($productos['items']) : ?>
<table class="table" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; margin-bottom: 16px; max-width: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
  width="100%">
  <tbody>
  <? foreach ($productos['items'] as $ivd => $producto) : ?>
    <tr>
      <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top"><?=$producto['nombre'];?></td>
      <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" class="text-right" valign="top" align="right"> <span style="color: #F55A00; font-size: 12px; margin-right: 5px; line-height: 24px; position: relative; vertical-align: top">(x<?=$producto['cantidad']?>)</span> <?=($producto['precio']) ? CakeNumber::currency(monto_bruto($producto['precio']), 'CLP') : '';?></td>
    </tr>
  <? endforeach; ?>

  <? if ($productos['totales']) : ?>
    <? if ($productos['totales']['envio']) : ?>  
        <tr>
        <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
            Despacho
        </td>
        <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
            <h4 class="text-right" style="color: inherit; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 8px; margin-top: 0; text-align: right; vertical-align: baseline;"><?=CakeNumber::currency($productos['totales']['envio'], 'CLP');?></h4>
        </td>
        </tr>
    <? endif; ?>

    <? if ($productos['totales']['descuento']) : ?>
        <tr>
        <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
            Descuento
        </td>
        <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
            <h4 class="text-right" style="color: inherit; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 8px; margin-top: 0; text-align: right; vertical-align: baseline;"> - <?=CakeNumber::currency($productos['totales']['descuento'], 'CLP');?></h4>
        </td>
        </tr>
    <? endif; ?>
    
    <? if ($productos['totales']['total']) : ?>
    <tr>
      <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
        Total
      </td>
      <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
        <h4 class="text-right" style="color: inherit; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 8px; margin-top: 0; text-align: right; vertical-align: baseline;"><strong><?=CakeNumber::currency($productos['totales']['total'], 'CLP');?></strong></h4>
      </td>
    </tr>
    <? endif; ?>
<? endif; ?>

  </tbody>
</table>
<? endif; ?>