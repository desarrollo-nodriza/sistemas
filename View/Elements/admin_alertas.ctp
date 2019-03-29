<? if ( $flash = $this->Session->flash('flash') ) : ?>
<div class="alert alert-info">
	<a class="close" data-dismiss="alert">&times;</a>
	<?= $flash; ?>
</div>
<? endif; ?>

<? if ( $warning = $this->Session->flash('warning') ) : ?>
<div class="alert alert-warning">
  <a class="close" data-dismiss="alert">&times;</a>
  <?= $warning; ?>
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

<div id="modalVacio" class="modal fade" tabindex="-1" role="dialog">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title"></h4>
      </div>
      <div class="modal-body">
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Cerrar</button>
      </div>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="loader"><i class="fa fa-refresh fa-spin fa-3x fa-fw"></i></div>
