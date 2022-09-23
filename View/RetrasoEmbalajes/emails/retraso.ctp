<?=$this->element('emails/public/head'); ?>

<?=$this->element('emails/public/logos'); ?>

<!-- Titulo -->
<?=$this->element('emails/public/titulo', array(
  'imagen' => array(
    'url' => 'https://sistemasdev.nodriza.cl/img/toolmania/iconos/clock.png',
    'style' => '',
    'width' => '50'
  ),
  'titulo' => array(
    'texto' => 'Lo sentimos mucho 😞',
    'style' => '',
    'custom_class' => ''
  )
)); ?>
<!-- /Titulo -->

<!-- Venta info -->
<?=$this->element('emails/public/venta_info', array(
  'titulo' => array(
    'texto' => 'Referencia #' . $venta['Venta']['referencia'],
    'style' => '',
    'custom_class' => ''
  ),
  'subtitulo' => array(
    'texto' => 'N° de venta #' . $venta['Venta']['id'],
    'style' => '',
    'custom_class' => ''
  )
)); ?>
<!-- /Venta info -->

<!-- Parrafo -->
<?=$this->element('emails/public/parrafo', array(
  'parrafo' => array(
    'texto' => 'Te notificaremos vía email cuando tu pedido esté listo para ser retirado en nuestra tienda o para ser enviado a la dirección que nos indicaste.',
    'style' => '',
    'custom_class' => ''
  )
)); ?>
<!-- /Parrafo -->

<?=$this->element('emails/public/separador');?>

    
<?=$this->element('emails/public/footer', array(
  'footer' => array(
    'tienda_url' => '',
    'bodega_direccion' => '',
    'bodega_fono' => ''
  )
)); ?>