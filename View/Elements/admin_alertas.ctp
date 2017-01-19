<? if ( $flash = $this->Session->flash('flash') ) : ?>
<div class="alert alert-info">
	<a class="close" data-dismiss="alert">&times;</a>
	<?= $flash; ?>
</div>
<? endif; ?>

<? if ( $danger = $this->Session->flash('danger') ) : ?>
<div class="alert alert-danger">
	<a class="close" data-dismiss="alert">&times;</a>
	<?= $danger; ?>
</div>
<? endif; ?>

<? if ( $success = $this->Session->flash('success') ) : ?>
<div class="alert alert-success">
	<a class="close" data-dismiss="alert">&times;</a>
	<?= $success; ?>
</div>
<? endif; ?>
