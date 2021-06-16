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
                                            <img src="<?=$url?>img/toolmania/iconos/alert.png" style="max-width: 100%; margin-top: 40px;" width="50">
                                          </div>
                                          <h4 class="text-center" style="color: inherit; font-size: 24px; font-weight: 500; line-height: 26.4px; margin-top: 10px; margin-bottom: 30px; text-align: center; vertical-align: baseline;">¡UPS! Hay productos agotados en <?=$venta['Tienda']['nombre'];?></h4>
                                          <!-- /Titulo -->


                                          <!-- Venta ID -->
                                          <h4 class="text-center" style="color: inherit; font-size: 33px; font-weight: 700; line-height: 36px; margin-top: 30px; margin-bottom: 30px; text-align: center; vertical-align: baseline;">N° Venta #<?=$venta['Venta']['id'];;?></h4>
                                          <!-- /Venta ID -->
                                          
                                          <!-- Sub titulo -->
                                          <p class="text-muted text-center" style="color: #636c72; font-size: 16px; font-weight: 500; line-height: 22px; margin-bottom: 10px; margin-top: 0px; text-align: center; vertical-align: baseline;">Te informamos que hay productos sin stock en tu pedido.</p>
                                          <!-- /Sub titulo -->


                                          <br />                                          


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

                                          <!-- Titulo tus productos -->
                                          <h5 class="text-center" style="color: inherit; font-size: 20px; font-weight: 700; line-height: 22px; margin-bottom: 20px; margin-top: 10px; text-align: center; vertical-align: baseline;">Detalle de los productos</h5>
                                          <!-- /titulo tus productos -->
                                          

                                          <!-- Productos y totales-->
                                          <table class="table" style="border-collapse: collapse; border-spacing: 0px; font-family: Helvetica, Arial, sans-serif; margin-bottom: 16px; max-width: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                                            width="100%">
                                            <tbody>
                                            <? foreach ($venta['VentaDetalle'] as $ivd => $producto) : ?>
                                              <? if ($producto['estado_proveedor'] == 'stockout') : ?>
                                                <tr>
                                                  <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px; width: 60%;" valign="top"><?=$producto['VentaDetalleProducto']['nombre'];?></td>
                                                  <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 24px; margin: 0; padding: 12px;" class="text-right" valign="top" align="right"> <span style="color: #F55A00; font-size: 12px; margin-right: 5px; line-height: 24px; position: relative; vertical-align: top">(x<?=$producto['cantidad']?>)</span> Sin stock</td>
                                                </tr>
                                              <? endif; ?>
                                            <? endforeach; ?>
                                            </tbody>
                                          </table>
                                          <!-- / Productos y totales -->

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

                                          <!-- Titulo tus productos -->
                                          <h5 class="text-center" style="color: inherit; font-size: 20px; font-weight: 700; line-height: 22px; margin-bottom: 30px; margin-top: 10px; text-align: center; vertical-align: baseline;">¿Qué deseas hacer con estos productos?</h5>
                                          <!-- /titulo tus productos -->

                                          <table class="table" style="border-collapse: collapse; border-spacing: 0px; border-radius: 4px; overflow: hidden; font-family: Helvetica, Arial, sans-serif; margin-bottom: 16px; max-width: 100%; mso-table-lspace: 0pt; mso-table-rspace: 0pt;" border="0" cellpadding="0" cellspacing="0" bgcolor="#ffffff"
                                            width="100%">
                                            <tbody>
                                              
                                              <tr style="width: 100%;border: 1px solid #DBDBDB; height: 100px;background-color: #F3F3F2; box-shadow: 1px 3px 5px 2px #dcdcdc;margin-bottom: 20px; border-radius: 5px;">
                                                <td valign="center" align="center" style="width: 20%;border-right: 1px solid #DBDBDB;">
                                                  <a href="<?=$url?>cliente/quick_message?access_token=<?=$token['token'];?>&venta_id=<?=$venta['Venta']['id'];?>&tipo=cambio" style="height: 100px;">
                                                    <img src="<?=$url?>img/toolmania/iconos/arrows.png" style="width: 35px;height: auto;">
                                                  </a>
                                                </td>
                                                <td valign="center" align="left" style="width: 80%; padding-left: 15px; padding-right: 15px; line-height: 15px;">
                                                  <a href="<?=$url?>cliente/quick_message?access_token=<?=$token['token'];?>&venta_id=<?=$venta['Venta']['id'];?>&tipo=cambio" style="font-size: 12px; color: #636c72;height: 100px;">Haz Click aquí si deseas <span style="font-weight: bold;">cambiar</span> el/los productos por otra opción similar.</a>
                                                </td>
                                              </tr>                                                   
                                          
                                              <tr style="width: 100%;border: 1px solid #DBDBDB; height: 100px;background-color: #F3F3F2; box-shadow: 1px 3px 5px 2px #dcdcdc;margin-bottom: 20px; border-radius: 5px;">
                                                <td valign="center" align="center" style="width: 20%;border-right: 1px solid #DBDBDB;">
                                                  <a href="<?=$url?>cliente/quick_message?access_token=<?=$token['token'];?>&venta_id=<?=$venta['Venta']['id'];?>&tipo=procesar" style="height: 100px;">
                                                    <img src="<?=$url?>img/toolmania/iconos/delivery-truck.png" style="width: 35px;height: auto;">
                                                  </a>
                                                </td>
                                                <td valign="center" align="left" style="width: 80%; padding-left: 15px; padding-right: 15px; line-height: 15px;">
                                                  <a href="<?=$url?>cliente/quick_message?access_token=<?=$token['token'];?>&venta_id=<?=$venta['Venta']['id'];?>&tipo=procesar" style="font-size: 12px; color: #636c72;height: 100px;">Haz Click aquí si deseas que solo enviemos los productos que <span style="font-weight: bold;">si tienen stock</span>. Anularemos los que no están disponibles y te devolveremos el dinero.</a>
                                                </td>
                                              </tr>                                                   
                                                
                                              <tr style="width: 100%;border: 1px solid #DBDBDB; height: 100px;background-color: #F3F3F2; box-shadow: 1px 3px 5px 2px #dcdcdc;margin-bottom: 20px; border-radius: 5px;">
                                                <td valign="center" align="center" style="width: 20%;border-right: 1px solid #DBDBDB;">
                                                  <a href="<?=$url?>cliente/quick_message?access_token=<?=$token['token'];?>&venta_id=<?=$venta['Venta']['id'];?>&tipo=cancelar" style="height: 100px;">
                                                    <img src="<?=$url?>img/toolmania/iconos/error.png" style="width: 35px;height: auto;">
                                                  </a>
                                                </td>
                                                <td valign="center" align="left" style="width: 80%; padding-left: 15px; padding-right: 15px; line-height: 15px;">
                                                  <a href="<?=$url?>cliente/quick_message?access_token=<?=$token['token'];?>&venta_id=<?=$venta['Venta']['id'];?>&tipo=cancelar" style="font-size: 12px; color: #636c72;height: 100px;">Haz Click  aquí si deseas <span style="font-weight: bold;">anular</span> toda la compra.</a>
                                                </td>
                                              </tr>  
                                              
                                            </tbody>
                                          </table>
                                          
                                          <p class="text-muted text-center" style="color: #636c72; font-size: 16px; font-weight: 500; line-height: 22px; margin-bottom: 10px; margin-top: 25px; text-align: center; vertical-align: baseline;">Sí tienes alguna duda escríbenos a <a href="mailto:ventas@toolmania.cl" style="color: #636c72; font-size: 16px; font-weight: 600; line-height: 22px; margin-bottom: 10px; margin-top: 15px;">ventas@toolmania.cl</a> o llámanos al <br><a style="color: #636c72; font-size: 16px; font-weight: 600; line-height: 22px; margin-bottom: 10px; margin-top: 15px;" href="tel:+56 2 2379 2188">(2) 2379 2188</a></p>

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
                              <td style="border-collapse: collapse; border-spacing: 0px; border-top: 0; font-size: 16px; line-height: 30px; margin: 0; padding: 0; width: 100%;" valign="top">
                                <a href="http://<?=$venta['Tienda']['url'];?>" style="width: 100%; display: block; line-height: 50px; background-color: #F55A00; color: #fff; border: 0; text-align: center;">Seguir comprando</a>
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
                            <a href="https://www.google.cl/maps/@-33.4481791,-70.8488174,17z">
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