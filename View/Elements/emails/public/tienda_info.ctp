<? if ($tienda) : ?>
<table class="card mb-4" style="border: 1px solid #dee2e6; border-collapse: separate !important; border-radius: 4px; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; overflow: hidden; margin-bottom: 24px;" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="100%">
    <tbody>
    <tr>
        <td style="border-bottom: 0; border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 20px" align="left">

            <? if ($tienda['direccion']) : ?>
            <font color="636c72" font-size="13" style="font-size: 13px; line-height: 16px; font-weight: 800;">Casa matríz</font><br>
            <font color="636c72" font-size="13" style="font-size: 13px; line-height: 16px;"><?=$tienda['direccion'];?></font><br>
            <? endif; ?>
            <? if ($tienda['fono']) : ?>
            <a style="color: #636c72; font-size: 13px; line-height: 26px; width: 100%;" href="tel:<?=$tienda['fono'];?>"><?=$tienda['fono']?></a><br>
            <? endif; ?>
            <? if ($tienda['email']) : ?>
            <a style="color: #636c72; font-size: 13px; line-height: 26px; width: 100%;" href="mailto:<?=$tienda['email'];?>"><?=$tienda['email'];?></a><br>
            <? endif; ?>
            <? if ($tienda['horario_atencion']) : ?>
            <a style="color: #636c72; font-size: 13px; line-height: 26px; width: 100%;"><?=$tienda['horario_atencion']; ?></a><br>
            <? endif; ?>

            <a style="color: #636c72; font-size: 13px; line-height: 26px; width: 100%;" href="https://www.toolmania.cl/content/3-terminos-y-condiciones">Centro de ayuda</a><br>
                                                                
        </td>
        <td class="text-right" style="border-bottom: 0; border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; width: 70%" align="right">
        <? if ($tienda['mapa']) : ?>
        <a href="<?=$tienda['mapa']; ?>">
            <img src="https://sistemasdev.nodriza.cl/img/toolmania/toolmania-maps.png" style="max-width: 100%;" width="300">
        </a>
        <? endif; ?>
        </td>
    </tr>
    </tbody>
</table>
<? endif; ?>