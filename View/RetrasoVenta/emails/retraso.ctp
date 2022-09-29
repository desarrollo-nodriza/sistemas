<?= $this->element('emails/public/head'); ?>

<?= $this->element('emails/public/logos'); ?>

<!-- Titulo -->
<?= $this->element('emails/public/titulo', array(
  'imagen' => array(
    'url' => "$url/img/toolmania/iconos/triste.png",
    // 'url' => "https://cdn-icons-png.flaticon.com/512/742/742752.png",
    'style' => '',
    'width' => '50'
  ),
  'titulo' => array(
    'texto' => 'Lo sentimos mucho',
    'style' => '',
    'custom_class' => ''
  )
)); ?>
<!-- /Titulo -->

<!-- Venta info -->
<?= $this->element('emails/public/venta_info', array(
  'titulo' => array(
    'texto' => 'Referencia #' . $retraso['Venta']['referencia'],
    'style' => '',
    'custom_class' => ''
  ),
  'subtitulo' => array(
    'texto' => 'N° de venta #' . $retraso['Venta']['id'],
    'style' => '',
    'custom_class' => ''
  )
)); ?>
<!-- /Venta info -->

<!-- Parrafo -->
<?= $this->element('emails/public/parrafo', array(
  'parrafo' => array(
    'texto' => "Estimado/a <strong>{$retraso['VentaCliente']['nombre']}</strong>.<br>Lamentamos la demora en la recepción de tu pedido, estamos trabajando a toda máquina para agilizar la entrega lo antes posible.<br><br>De antemano te pedimos disculpas por los posibles problemas ocasionados.<br><br>Atte. Toolmania Spa.",
    'style' => '',
    'custom_class' => ''
  )
)); ?>
<!-- /Parrafo -->

<?= $this->element('emails/public/separador'); ?>


<?= $this->element('emails/public/footer', array(
  'footer' => array(
    'tienda_url'        => $tienda['Tienda']['url'],
    'bodega_direccion'  => $tienda['Tienda']['direccion'],
    'bodega_fono'       => $tienda['Tienda']['fono'],
  )
)); ?>

