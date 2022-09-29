<? if(!empty($titulo)) : ?>
<h4 class="text-center <?=($titulo['custom_class']) ? $titulo['custom_class'] : ''; ?>" style="color: inherit; font-size: 33px; font-weight: 700; line-height: 36px; margin-top: 30px; margin-bottom: 15px; text-align: center; vertical-align: baseline; <?=($titulo['style']) ? $titulo['style'] : ''; ?>"><?=($titulo['texto']) ? $titulo['texto'] : ''; ?></h4>
<? endif; ?>

<? if(!empty($subtitulo)) : ?>
<h5 class="text-muted text-center <?=($subtitulo['custom_class']) ? $subtitulo['custom_class'] : ''; ?>" style="color: #636c72; font-size: 20px; font-weight: 700; line-height: 22px; margin-bottom: 30px; margin-top: 0px; text-align: center; vertical-align: baseline; <?=($subtitulo['style']) ? $subtitulo['style'] : ''; ?>"><?=($subtitulo['texto']) ? $subtitulo['texto'] : ''; ?></h5>
<? endif; ?>