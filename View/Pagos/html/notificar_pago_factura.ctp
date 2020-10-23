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
                  <P style="FONT-SIZE: 16px; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a8a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly; text-align: center;" align=center>
                    Estimados/as <?=$proveedor['Proveedor']['nombre'];?>, se ha creado un pago desde Toolmania Spa.
                  </P>
                  <h3 style="text-align: center; font-size: 30px; font-weight: bold; COLOR: #a8a7a7; FONT-FAMILY: Arial, Helvetica, sans-serif;">Total pagado <?=CakeNumber::currency($pago['Pago']['monto_pagado'], 'CLP')?> <br> N° del pago #<?=$pago['Pago']['id']; ?></h3>
                  <P style="FONT-SIZE: 16px; MARGIN-BOTTOM: 1em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;text-align: center;" align=center>A continuación pude ver el detalle de las facturas relacionadas:</P>
                  <TABLE class=rtable style="WIDTH: 100%;border: 1px solid #a8a7a7;margin-bottom: 30px;" cellSpacing=0 cellPadding=0 align=left>
                    <TH style="FONT-SIZE: 15px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;border: 1px solid;">Folio factura</TH>
                    <TH style="FONT-SIZE: 15px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;border: 1px solid;">Monto facturado</TH>
                    <TH style="FONT-SIZE: 15px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;border: 1px solid;">Monto pagado a la fecha</TH>
                  <? foreach ($pago['OrdenCompraFactura'] as $if => $f) : ?>
                    <TR>
                      <TD style="FONT-SIZE: 16px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;text-align: center;border: 1px solid;padding: 5px;">
                        <?= $f['folio'];?>
                      </TD>
                      <TD style="FONT-SIZE: 16px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;text-align: center;border: 1px solid;padding: 5px;">
                        <?= CakeNumber::currency($f['monto_facturado'], 'CLP');?>
                      </TD>
                      <TD style="FONT-SIZE: 16px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;text-align: center;border: 1px solid;padding: 5px;">
                        <?= CakeNumber::currency($f['monto_pagado'], 'CLP');?>
                      </TD>
                    </TR>
                  <? endforeach; ?>
                  </TABLE>
                  <BR>
                  <? if (!empty($pagosRelacionados)) : ?>
                  <P style="FONT-SIZE: 16px; MARGIN-BOTTOM: 1em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;text-align: center;" align=center>Aquí puede ver el detalle del/los pagos configurados en nuestro sistema:</P>
                  <TABLE class=rtable style="WIDTH: 100%;border: 1px solid #a8a7a7;margin-bottom: 30px;" cellSpacing=0 cellPadding=0 align=left>
                    <TH style="FONT-SIZE: 15px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;border: 1px solid;">N° del pago</TH>
                    <TH style="FONT-SIZE: 15px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;border: 1px solid;">M de pago</TH>
                    <TH style="FONT-SIZE: 15px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;border: 1px solid;">Monto del pago</TH>
                    <TH style="FONT-SIZE: 15px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;border: 1px solid;">Promesa de pago</TH>
                    <TH style="FONT-SIZE: 15px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;border: 1px solid;">Estado</TH>
                  <? foreach ($pagosRelacionados as $if => $p) : ?>
                    <TR>
                      <TD style="FONT-SIZE: 16px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;text-align: center;border: 1px solid;padding: 5px;">
                        <?= $p['Pago']['id'];?>
                      </TD>
                      <TD style="FONT-SIZE: 16px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;text-align: center;border: 1px solid;padding: 5px;">
                        <?= $p['Moneda']['nombre'];?>
                      </TD>
                      <TD style="FONT-SIZE: 16px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;text-align: center;border: 1px solid;padding: 5px;">
                        <?= CakeNumber::currency($p['Pago']['monto_pagado'], 'CLP');?>
                      </TD>
                      <TD style="FONT-SIZE: 16px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;text-align: center;border: 1px solid;padding: 5px;">
                        <?= $p['Pago']['fecha_pago']; ?>
                      </TD>
                      <TD style="FONT-SIZE: 16px; MARGIN-BOTTOM: 0.5em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly;text-align: center;border: 1px solid;padding: 5px;">
                        <?= ($p['Pago']['pagado']) ? 'Pagado' : 'No pagado' ; ?>
                      </TD>
                    </TR>
                  <? endforeach; ?>
                  </TABLE>
                  <? endif; ?>
                  <BR/>
                  <P style="FONT-SIZE: 14px; MARGIN-BOTTOM: 1em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #a7a7a7; LINE-HEIGHT: 155%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align=left>Atte Equipo de Nodriza Spa.</P>
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
                  <P style="FONT-SIZE: 10px; MARGIN-BOTTOM: 1em; FONT-FAMILY: Arial, Helvetica, sans-serif; MARGIN-TOP: 0px; COLOR: #7c7c7c; LINE-HEIGHT: 125%; BACKGROUND-COLOR: transparent; mso-line-height-rule: exactly" align=left>Seamos conciente de nuestro impacto ambiental. Imprime este email slo si es absolutamente necesario.</P>
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
