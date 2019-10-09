<TABLE class="rtable mainTable" cellSpacing=0 cellPadding=0 width="100%" bgColor=#f3f3f3>
  <TR>
    <TD style="FONT-SIZE: 0px; HEIGHT: 20px; LINE-HEIGHT: 0">&#160;</TD>
  </TR>
  <TR>
    <TD vAlign=top>
      <TABLE class=rtable style="WIDTH: 600px; MARGIN: 0px auto" cellSpacing=0 cellPadding=0 width=600 align=center border=0>
        <TR>
          <TD style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; BORDER-BOTTOM: medium none; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; PADDING-LEFT: 0px; BORDER-LEFT: medium none; PADDING-RIGHT: 0px; BACKGROUND-COLOR: #1d2127">
            <TABLE class=rtable style="WIDTH: 100%" cellSpacing=0 cellPadding=0 align=left>
              <TR style="HEIGHT: 10px">
                <TD style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; WIDTH: 100%; VERTICAL-ALIGN: middle; BORDER-BOTTOM: medium none; PADDING-BOTTOM: 10px; TEXT-ALIGN: center; PADDING-TOP: 10px; PADDING-LEFT: 15px; BORDER-LEFT: medium none; PADDING-RIGHT: 15px; BACKGROUND-COLOR: transparent">
                  <TABLE cellSpacing=0 cellPadding=0 align=center border=0>
                    <TR>
                      <TD style="PADDING-BOTTOM: 2px; PADDING-TOP: 2px; PADDING-LEFT: 2px; PADDING-RIGHT: 2px" align=center>
                        <TABLE cellSpacing=0 cellPadding=0 border=0>
                          <TR>
                            <TD style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; BACKGROUND-COLOR: transparent">
                              <img src="<?= $url; ?>/webroot/img/nodrizablanco.png" vspace=0 hspace=0 border=0 class="rimg" style="MAX-WIDTH: 135px; BORDER-TOP: medium none; BORDER-RIGHT: medium none; BORDER-BOTTOM: medium none; BORDER-LEFT: medium none; DISPLAY: block; BACKGROUND-COLOR: transparent"/>
                            </TD>
                          </TR>
                        </TABLE>
                      </TD>
                    </TR>
                  </TABLE>
                </TD>
              </TR>
            </TABLE>
          </TD>
        </TR>
        <TR>
          <TD style="BORDER-TOP: #dbdbdb 1px solid; BORDER-RIGHT: #dbdbdb 1px solid; BORDER-BOTTOM: #dbdbdb 1px solid; PADDING-BOTTOM: 0px; PADDING-TOP: 0px; PADDING-LEFT: 0px; BORDER-LEFT: #dbdbdb 1px solid; PADDING-RIGHT: 0px; BACKGROUND-COLOR: #feffff">
            <TABLE class=rtable style="WIDTH: 100%" cellSpacing=0 cellPadding=0 align=left>
              <TR style="HEIGHT: 20px">
                <TD style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; WIDTH: 100%; VERTICAL-ALIGN: top; BORDER-BOTTOM: medium none; PADDING-BOTTOM: 0px; TEXT-ALIGN: center; PADDING-TOP: 35px; PADDING-LEFT: 15px; BORDER-LEFT: medium none; PADDING-RIGHT: 15px; BACKGROUND-COLOR: #feffff">
                  <P style="FONT-SIZE: 16px; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a8a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align=left>
                    <STRONG>Existen ventas con productos que están en stockout en el proveedor.</STRONG>
                  </P>
                  <P style="FONT-SIZE: 12px; MARGIN-BOTTOM: 1em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align=left>
                    Ahora, debe avisarle a los clientes para poder gestionar el cambio o devolución del producto.
                  </P>
                  
                  <table class=rtable style="WIDTH: 100%; margin-bottom: 30px;" cellSpacing=0 cellPadding=0 align=left>
                    <caption style="border: 1px solid #dbdbdb;padding: 10px;text-align: left; font-weight: 600;">Listado de productos con stockout</caption>
                    <thead>
                      <tr>
                        <th style="border: 1px solid #dbdbdb;padding: 10px;text-align: center;">Nombre del producto</th>
                        <th style="border: 1px solid #dbdbdb;padding: 10px;text-align: center;">Cantidad disponible en proveedor</th>
                        <!--<th style="border: 1px solid #dbdbdb;padding: 10px;text-align: center;">Cantidad vendida</th>-->
                      </tr>
                    </thead>
                    <tbody>
                    <? foreach ($productos as $p) : ?>
                      <tr>
                        <td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left;"><?= $p['descripcion']; ?></td>
                        <td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left;"><?= $p['cantidad']; ?></td>
                        <!--<td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left;"><?= array_sum(Hash::extract($ventas, 'VentaDetalle.{n}[venta_detalle_producto_id='.$p['venta_detalle_producto_id'].'].cantidad')); ?></td>-->
                      </tr>
                    <? endforeach; ?>
                    </tbody>
                  </table>

                  <table class=rtable style="WIDTH: 100% margin-bottom: 30px;" cellSpacing=0 cellPadding=0 align=left>
                    <caption style="border: 1px solid #dbdbdb;padding: 10px;text-align: left; font-weight: 600;">Listado de ventas relacionadas</caption>
                  <? foreach ($ventas as $iv => $venta) : ?>
                    <tr>
                      <td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left; font-weight: 600;">Identificador de la venta</td>
                      <td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left;">Id #<?= $venta['Venta']['id']?> - Id externo #<?= $venta['Venta']['id_externo']; ?></td>
                      <td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left; font-weight: 600;">Canal de venta</td>
                      <td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left;"><?= (empty($venta['Venta']['markeplace_id'])) ? $venta['Tienda']['nombre'] : $venta['Marketplace']['nombre']; ?></td>
                      <td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left;"><a href="<?= $url; ?>/ventas/view/<?=$venta['Venta']['id']; ?>" target="_blank">Ir a la venta</a></td>
                    </tr>
                    <!--<tr>
                      <td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left;"> Items</td>
                      <td colspan="4">
                        <? foreach ($venta['VentaDetalle'] as $id => $p) : ?>
                          <table style="width: 100%;">
                            <tr>
                              <td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left;"><?= $p['VentaDetalleProducto']['nombre']; ?> - <?= $p['VentaDetalleProducto']['codigo_proveedor']; ?></td>
                              <td style="border: 1px solid #dbdbdb;padding: 10px;text-align: left;">cant vendida: <?= $p['cantidad']; ?></td>
                            </tr>
                          </table>
                        <? endforeach; ?>
                      </td>
                    </tr>-->
                  <? endforeach; ?>
                    <tr>
                      <td></td>
                    </tr>
                  </table>


                  <P style="FONT-SIZE: 12px; margin-top: 30px; MARGIN-BOTTOM: 1em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align=left>Atte Equipo de Nodriza Spa.</P>
                </TD>
              </TR>
            </TABLE>
          </TD>
        </TR>
        <TR>
          <TD style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; BORDER-BOTTOM: medium none; PADDING-BOTTOM: 1px; PADDING-TOP: 1px; PADDING-LEFT: 0px; BORDER-LEFT: medium none; PADDING-RIGHT: 0px; BACKGROUND-COLOR: transparent">
            <TABLE class=rtable style="WIDTH: 100%" cellSpacing=0 cellPadding=0 align=left>
              <TR style="HEIGHT: 10px">
                <TD style="BORDER-TOP: medium none; BORDER-RIGHT: medium none; WIDTH: 100%; VERTICAL-ALIGN: top; BORDER-BOTTOM: medium none; PADDING-BOTTOM: 1px; TEXT-ALIGN: center; PADDING-TOP: 10px; PADDING-LEFT: 15px; BORDER-LEFT: medium none; PADDING-RIGHT: 15px; BACKGROUND-COLOR: transparent">
                  <P style="FONT-SIZE: 10px; MARGIN-BOTTOM: 1em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #7c7c7c; LINE-HEIGHT: 125%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align=left>Por favor no conteste este email, ya que es generado autom&#225;ticamente por nuestro sistema.</P>
                  <P style="FONT-SIZE: 10px; MARGIN-BOTTOM: 1em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #7c7c7c; LINE-HEIGHT: 125%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align=left>&#169;2019 Nodriza Spa.Todos los derechos reservados.</P>
                </TD>
              </TR>
            </TABLE>
          </TD>
        </TR>
      </TABLE>
    </TD>
  </TR>
  <TR>
    <TD style="FONT-SIZE: 0px; HEIGHT: 8px; LINE-HEIGHT: 0">&#160;
    </TD>
  </TR>
</TABLE>
