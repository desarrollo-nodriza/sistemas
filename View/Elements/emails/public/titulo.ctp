<? if (!empty($imagen)) : ?>
<div style="text-align: center;">
    <img src="<?=($imagen['url']) ? $imagen['url'] : '' ; ?>" style="max-width: 100%; margin-top: 40px; <?=($imagen['style']) ? $imagen['style'] : ''; ?>" width="<?= ($imagen['width']) ? $imagen['width'] : '50'; ?>">
</div>
<? endif; ?>
<? if (!empty($titulo)) : ?>
<h4 class="text-center <?=($titulo['custom_class']) ? $titulo['custom_class'] : ''; ?>" style="color: inherit; font-size: 24px; font-weight: 500; line-height: 26.4px; margin-top: 0px; margin-bottom: 30px; text-align: center; vertical-align: baseline; <?=($titulo['style']) ? $titulo['style'] : ''; ?>"> <?=($titulo['texto']) ? $titulo['texto'] : '';?></h4>
<? endif; ?>