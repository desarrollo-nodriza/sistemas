<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html>

<head>
  <meta charset="utf-8">

  <style>
    .ExternalClass {
      width: 100%;
    }
    /* Forces Outlook.com to display emails at full width */

    .ExternalClass,
    .ExternalClass p,
    .ExternalClass span,
    .ExternalClass font,
    .ExternalClass td,
    .ExternalClass div {
      line-height: 100%;
    }
    /* Forces Outlook.com to display normal line spacing, here is more on that: http://www.emailonacid.com/forum/viewthread/43/ */

    .imageFix {
      display: block;
    }

    a {
      text-decoration: none;
    }

    @media screen and (max-width: 600px) {
      table.row th.col-lg-1,
      table.row th.col-lg-2,
      table.row th.col-lg-3,
      table.row th.col-lg-4,
      table.row th.col-lg-5,
      table.row th.col-lg-6,
      table.row th.col-lg-7,
      table.row th.col-lg-8,
      table.row th.col-lg-9,
      table.row th.col-lg-10,
      table.row th.col-lg-11,
      table.row th.col-lg-12 {
        display: block;
        width: 100% !important;
      }
      .d-mobile {
        display: block !important;
      }
      .d-desktop {
        display: none !important;
      }
    }

    @media yahoo {
      .d-mobile {
        display: none !important;
      }
      .d-desktop {
        display: block !important;
      }
    }
  </style>
</head>

<body style="-moz-box-sizing: border-box; -ms-text-size-adjust: 100%; -webkit-box-sizing: border-box; -webkit-text-size-adjust: 100%; Margin: 0; border: 0; box-sizing: border-box; font-family: Helvetica, Arial, sans-serif; font-size: 16px; font-weight: normal; height: 100%; line-height: 24px; margin: 0; min-width: 100%; outline: 0; padding: 0; width: 100%;">
  <div class="bg-light" style="background-color: #f8f9fa;">
    <table class="container" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" width="100%">
      <tbody>
        <tr>
          <td align="center" style="border-collapse: collapse; border-spacing: 0px; font-size: 16px; line-height: 24px; margin: 0; padding: 0px 16px 0 16px;">
            <!--[if (gte mso 9)|(IE)]>
          <table align="center">
            <tbody>
              <tr>
                <td width="600">
        <![endif]-->
            <table align="center" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; max-width: 600px; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" width="100%">
              <tbody>
                <tr>
                  <td style="border-collapse: collapse; border-spacing: 0px; font-size: 16px; line-height: 24px; margin: 0;">

                    <div class="mb-4 mt-4" style="margin-bottom: 24px; margin-top: 24px;">
                      <table class="card mb-4" style="border: 1px solid #dee2e6; border-collapse: separate !important; border-radius: 4px; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; overflow: hidden;" border="0"
                        cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="100%">
                        <tbody>
                          <tr>
                            <td style="border-collapse: collapse; border-spacing: 0px; font-size: 16px; line-height: 24px; margin: 0;" width="100%">
                              <div style="background: white; border-top: 5px solid #294D99;">
                                <table class="card-body" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" width="100%">
                                  <tbody>
                                    <tr>
                                      <td style="border-collapse: collapse; border-spacing: 0px; font-size: 16px; line-height: 24px; margin: 0; padding: 20px;" width="100%">
                                        <div>

                                          <!-- Logos -->
                                          <table align="center" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; margin-top: 20px;" border="0" cellpadding="0" cellspacing="0">
                                            <tbody>
                                              <tr>
                                                <td align="center" valign="middle" style="border-collapse: collapse; border-spacing: 0px; font-size: 16px; line-height: 24px; margin: 0;">
                                                  <img class="align-center" width="100" height="100" src="https://www.toolmania.cl/themes/toolmania/assets/img/logo-toolmania.png" style="border: 0 none; height: auto; line-height: 100%; outline: none; text-decoration: none;">
                                                </td>
                                              </tr>
                                            </tbody>
                                          </table>
                                          <!-- /Logos -->


                                          <!-- Titulo -->
                                          <div style="text-align: center;">
                                            <img src="https://sistemasdev.nodriza.cl/img/toolmania/iconos/delivery-truck.png" style="max-width: 100%; margin-top: 40px;" width="60">
                                          </div>
                                          <h4 class="text-center" style="color: inherit; font-size: 24px; font-weight: 500; line-height: 26.4px; margin-top: 10px; margin-bottom: 30px; text-align: center; vertical-align: baseline;">Tu compra ha sido enviada</h4>
                                          <!-- /Titulo -->


                                          <!-- Venta ID -->
                                          <h4 class="text-center" style="color: inherit; font-size: 33px; font-weight: 700; line-height: 36px; margin-top: 30px; margin-bottom: 30px; text-align: center; vertical-align: baseline;">N° Venta #<?=$venta['Venta']['id'];?></h4>
                                          <!-- /Venta ID -->
                        
                                          <? if (!empty($venta['Transporte'])) : ?>

                                          <table class="hr" style="border: 0; border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tbody>
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; font-size: 16px; line-height: 24px; margin: 0; padding: 16px 0px;" width="100%">
                                                  <table style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                      <tr>
                                                        <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #dddddd; font-size: 16px; line-height: 24px; margin: 0;" width="100%" height="1px"></td>
                                                      </tr>
                                                    </tbody>
                                                  </table>
                                                </td>
                                              </tr>
                                            </tbody>
                                          </table>

                                          <!-- Titulo seguimiento -->
                                          <h5 class="text-center" style="color: inherit; font-size: 20px; font-weight: 700; line-height: 22px; margin-bottom: 20px; margin-top: 10px; text-align: center; vertical-align: baseline;">N° de Seguimiento</h5>
                                          <!-- /titulo tus productos -->


                                          <!-- Parrafo -->
                                          <p class="text-muted text-center" style="color: #636c72; font-size: 16px; font-weight: 500; line-height: 22px; margin-bottom: 30px; margin-top: 25px; text-align: center; vertical-align: baseline;">
                                            Hemos entregado su pedido al transportista correspondiente. Puede ver más detalles a continuación.
                                          </p>
                                          <!-- /Parrafo -->
                                          
                                          <table class="table" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; margin-bottom: 16px; max-width: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                                            width="100%">
                                            <thead>
                                              <th style="font-size: 12px;" align="left">Transportista</th>
                                              <th style="font-size: 12px;" align="left">N° de seguimiento</th>
                                              <th style="font-size: 12px;" align="left">Plazo entrega aprox</th>
                                              <th style="font-size: 12px;" align="left">Seguimiento</th>
                                            </thead>
                                            <tbody style="border-top: 1px solid #e9ecef;">
                                            <? foreach ($venta['Transporte'] as $it => $t) : ?>
                                              <tr style="border-bottom: 1px solid #e9ecef;">
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 12px; line-height: 14px; margin: 0; padding: 15px 5px;" valign="center" align="left"><?=$t['nombre']; ?></td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 12px; line-height: 14px; margin: 0; padding: 15px 5px;" valign="center" align="left"><?=$t['TransportesVenta']['cod_seguimiento']; ?></td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 12px; line-height: 14px; margin: 0; padding: 15px 5px;" valign="center" align="left"><?=(!empty($t['TransportesVenta']['entrega_aprox'])) ? $t['TransportesVenta']['entrega_aprox'] : $t['tiempo_entrega']; ?></td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 12px; line-height: 14px; margin: 0; padding: 15px 5px;" valign="center" align="left"><a href="<?=$t['url_seguimiento']; ?>">Ir a la página</a></td>
                                              </tr>
                                            <? endforeach; ?>
                                            </tbody>
                                          </table>
                                          <? endif; ?>

                                          <!-- Parrafo -->
                                          <p class="text-muted text-center" style="color: #636c72; font-size: 16px; font-weight: 500; line-height: 22px; margin-bottom: 10px; margin-top: 25px; text-align: center; vertical-align: baseline;">Sí tienes alguna duda escríbenos a <a href="mailto:ventas@toolmania.cl" style="color: #636c72; font-size: 16px; font-weight: 600; line-height: 22px; margin-bottom: 10px; margin-top: 15px;">ventas@toolmania.cl</a> o llámanos al <br> <a style="color: #636c72; font-size: 16px; font-weight: 600; line-height: 22px; margin-bottom: 10px; margin-top: 15px;" href="tel:+56 2 2379 2188">(2) 2379 2188</a></p>
                                          <!-- /Parrafo -->
                                   

                                          <table class="hr" style="border: 0; border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" width="100%">
                                            <tbody>
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; font-size: 16px; line-height: 24px; margin: 0; padding: 16px 0px;" width="100%">
                                                  <table style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" width="100%">
                                                    <tbody>
                                                      <tr>
                                                        <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #dddddd; font-size: 16px; line-height: 24px; margin: 0;" width="100%" height="1px"></td>
                                                      </tr>
                                                    </tbody>
                                                  </table>
                                                </td>
                                              </tr>
                                            </tbody>
                                          </table>


                                          <!-- Titulo Dirección entrega -->
                                          <h5 class="text-center" style="color: inherit; font-size: 20px; font-weight: 700; line-height: 22px; margin-bottom: 20px; margin-top: 10px; text-align: center; vertical-align: baseline;">Dirección de entrega</h5>
                                          <!-- /titulo Dirección entrega -->

                                          <!-- Entrega-->
                                          <table class="table" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; margin-bottom: 16px; max-width: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                                            width="100%">
                                            <tbody>
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  Dirección
                                                </td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" class="text-right" valign="top" align="right">
                                                  <?=$venta['Venta']['direccion_entrega'];?> <?=$venta['Venta']['numero_entrega'];?> <?=$venta['Venta']['otro_entrega'];?>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  Comuna
                                                </td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" class="text-right" valign="top" align="right">
                                                  <?=$venta['Venta']['comuna_entrega'];?>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  Nombre del receptor informado
                                                </td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" class="text-right" valign="top" align="right">
                                                  <?=$venta['Venta']['nombre_receptor'];?>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  Fono del receptor informado
                                                </td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" class="text-right" valign="top" align="right">
                                                  <?=$venta['Venta']['fono_receptor'];?>
                                                </td>
                                              </tr>
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  Email del receptor informado
                                                </td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" class="text-right" valign="top" align="right">
                                                  <?=$venta['VentaCliente']['email'];?>
                                                </td>
                                              </tr>
                                            </tbody>
                                          </table>
                                          <!-- /Entrega -->


                                          <!-- Titulo tus productos -->
                                          <h5 class="text-center" style="color: inherit; font-size: 20px; font-weight: 700; line-height: 22px; margin-bottom: 20px; margin-top: 10px; text-align: center; vertical-align: baseline;">Tus productos</h5>
                                          <!-- /titulo tus productos -->
                                          

                                          <!-- Productos y totales-->
                                          <table class="table" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; margin-bottom: 16px; max-width: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                                            width="100%">
                                            <tbody>
                                            <? foreach ($venta['VentaDetalle'] as $ivd => $producto) : ?>
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top"><?=$producto['VentaDetalleProducto']['nombre'];?></td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" class="text-right" valign="top" align="right"> <span style="color: #F55A00; font-size: 12px; margin-right: 5px; line-height: 24px; position: relative; vertical-align: top">(x<?=$producto['cantidad']?>)</span> <?=CakeNumber::currency(($producto['precio']*1.19), 'CLP');?></td>
                                              </tr>
                                            <? endforeach; ?>
                                              
                                            <? if ($venta['Venta']['costo_envio'] > 0) : ?>  
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  Despacho
                                                </td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  <h4 class="text-right" style="color: inherit; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 8px; margin-top: 0; text-align: right; vertical-align: baseline;"><?=CakeNumber::currency($venta['Venta']['costo_envio'], 'CLP');?></h4>
                                                </td>
                                              </tr>
                                            <? endif; ?>

                                            <? if ($venta['Venta']['descuento'] > 0) : ?>
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  Descuento
                                                </td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  <h4 class="text-right" style="color: inherit; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 8px; margin-top: 0; text-align: right; vertical-align: baseline;"> - <?=CakeNumber::currency($venta['Venta']['descuento'], 'CLP');?></h4>
                                                </td>
                                              </tr>
                                            <? endif; ?>

                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  Total
                                                </td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border-top: 1px solid #e9ecef; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" valign="top">
                                                  <h4 class="text-right" style="color: inherit; font-size: 16px; font-weight: 500; line-height: 24px; margin-bottom: 8px; margin-top: 0; text-align: right; vertical-align: baseline;"><strong><?=CakeNumber::currency($venta['Venta']['total'], 'CLP');?></strong></h4>
                                                </td>
                                              </tr>
                                            </tbody>
                                          </table>
                                          <!-- / Productos y totales -->
    

                                        </div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>

                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    

                    <!-- Links de interés -->
                    <div class="mb-4" style="margin-bottom: 24px; ">
                       
                        <table class="table" style="border-collapse: collapse; border-spacing: 0px; border-radius: 4px; overflow: hidden; font-family: Helvetica, Arial, sans-serif; margin-bottom: 16px; max-width: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                          width="100%">
                          <tbody>
                            <tr>
                              <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 30px; margin: 0; padding: 0; width: 50%;" valign="top">
                                <a href="http://<?=$venta['Tienda']['url'];?>" style="width: 100%; display: block; line-height: 50px; background-color: #F55A00; color: #fff; border: 0; text-align: center;">Seguir comprando</a>
                              </td>
                              <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 30px; margin: 0; padding: 0; width: 50%;" valign="top">
                                <a href="http://<?=$venta['Tienda']['url'];?>/index.php?controller=order-detail&id_order=<?=$venta['Venta']['id_externo']; ?>" style="width: 100%; display: block; line-height: 50px; background-color: #2445A2; color: #fff; border: 0; text-align: center;">Ver mi compra</a>
                              </td>
                            </tr>
                          </tbody>
                        </table>
                        
                    </div>
                    <!-- /Links de interés -->


                    <!-- Información de la tienda -->
                    <table class="card mb-4" style="border: 1px solid #dee2e6; border-collapse: separate !important; border-radius: 4px; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; overflow: hidden; margin-bottom: 24px;" border="0"
                        cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="100%">
                      <tbody>
                        <tr>
                          <td style="border-bottom: 0; border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 20px" align="left">

                            
                              <font color="636c72" font-size="13" style="font-size: 13px; line-height: 16px;"><?=$venta['Tienda']['direccion'];?><br>
                              <a style="color: #636c72; line-height: 26px; width: 100%;" href="tel:+56 2 2379 2188">+56 2 2379 2188</a><br>
                              <a style="color: #636c72; line-height: 26px; width: 100%;" href="mailto:ventas@toolmania.cl">ventas@toolmania.cl</a><br>
                              <a style="color: #636c72; line-height: 26px; width: 100%;" href="#">Lun-Vie 09:00-14:00 y 15:00-18:30</a><br>
                              <a style="color: #636c72; line-height: 26px; width: 100%;" href="https://www.toolmania.cl/content/3-terminos-y-condiciones">Centro de ayuda</a><br>
                              </font>
                                                                                    
                          </td>
                          <td class="text-right" style="border-bottom: 0; border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; width: 70%" align="right">
                            <a href="https://www.google.cl/maps/place/ToolMania/@-33.4466135,-70.6142545,17z/data=!4m12!1m6!3m5!1s0x9662cf845db35c19:0xb602780387d2a780!2sToolMania!8m2!3d-33.4467433!4d-70.613246!3m4!1s0x9662cf845db35c19:0xb602780387d2a780!8m2!3d-33.4467433!4d-70.613246">
                              <img src="https://sistemasdev.nodriza.cl/img/toolmania/toolmania-maps.png" style="max-width: 100%;" width="300">
                            </a>
                          </td>
                        </tr>
                      </tbody>
                    </table>
                    <!-- /Información de la tienda -->


                    <!-- RRSS -->
                    <div class="mb-4" style="margin-bottom: 24px;">
                      <table class="card w-100 mb-4" style="border: 0px solid #dee2e6; border-collapse: separate !important; border-radius: 4px; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt; overflow: hidden;"
                        border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff" width="100%">
                        <tbody>
                          <tr>
                            <td style="border-collapse: collapse; border-spacing: 0px; font-size: 16px; line-height: 24px; margin: 0;" width="100%">
                              <div>
                                <table class="card-body" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" width="100%">
                                  <tbody>
                                    <tr>
                                      <td style="border-collapse: collapse; border-spacing: 0px; font-size: 16px; line-height: 24px; margin: 0; padding: 0px;" width="100%">
                                        <div>
                                          <table class="table" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; margin-bottom: 0px; max-width: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                                            width="100%">
                                            <tbody>
                                              <tr>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border: 0; margin: 0; padding: 0px; line-height: 0;" >
                                                  <a href="#" style="position: relative; height: auto; padding: 0; margin: 0; display: block;"><img src="https://sistema.nodriza.cl/img/toolmania/facebook.jpg" alt="Facebook Toolmania" width="300" style="max-width: 100%"></a>
                                                </td>
                                                <td style="border-collapse: collapse; border-spacing: 0px; border: 0; margin: 0; padding: 0px; line-height: 0;" >
                                                  <a href="#" style="position: relative; height: auto; padding: 0; margin: 0; display: block;"><img src="https://sistema.nodriza.cl/img/toolmania/twitter.jpg" alt="Twitter Toolmania" width="300" style="max-width: 100%"></a>
                                                </td>
                                              </tr>
                                            </tbody>
                                          </table>
                                        </div>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </div>
                            </td>
                          </tr>
                        </tbody>
                      </table>
                    </div>
                    <!-- /RRSS -->
                  

                    <div class="mt-4" style="margin-top: 24px;">
                      <div class="mt-4 mb-4 w-100 text-center" style="margin-top: 24px; margin-bottom: 24px; width: 100%; text-align: center; ">
                        <font style="font-size: 14px; color: #636c72" color="636c72"><i>NO RESPONDA ÉSTE EMAIL YA QUE ES GENERADO AUTOMÁTICAMENTE</i>
                      </div>
                    </div>



                  </td>
                </tr>
              </tbody>
            </table>
            <!--[if (gte mso 9)|(IE)]>
                </td>
              </tr>
            </tbody>
          </table>
        <![endif]-->
          </td>
        </tr>
      </tbody>
    </table>

  </div>

</body>

</html>