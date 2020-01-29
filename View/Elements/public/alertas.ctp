<? if ( $flash = $this->Session->flash('flash') ) : ?>
<div id="alert-wrapper">
	<div class="progress" style="height: 3px;">
	  <div class="progress-bar bg-info" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
	</div>

	<div class="alert alert-info rounded-0">
		<?= $flash; ?>
	</div>
</div>
<? endif; ?>

<? if ( $warning = $this->Session->flash('warning') ) : ?>
<div id="alert-wrapper">
	<div class="progress" style="height: 3px;">
	  <div class="progress-bar bg-warning" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
	</div>

	<div class="alert alert-warning text-center rounded-0">
	  <?= $warning; ?>
	</div>
</div>
<? endif; ?>

<? if ( $danger = $this->Session->flash('danger') ) : ?>
<div id="alert-wrapper">
	<div class="progress" style="height: 3px;">
	  <div class="progress-bar bg-danger" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
	</div>

	<div class="alert alert-danger rounded-0">
		<?= $danger; ?>
	</div>
</div>
<? endif; ?>

<? if ( $success = $this->Session->flash('success') ) : ?>
<div id="alert-wrapper">
	<div class="progress" style="height: 3px;">
	  <div class="progress-bar bg-success" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
	</div>

	<div class="alert alert-success rounded-0">
		<?= $success; ?>
	</div>
</div>
<? endif; ?>

<script type="text/javascript">
	
	$(window).on('load', function(){

		if ( $('.alert').length ) {

			var contador = 0;

			var progreso = setInterval(function(){				
				console.log(contador);
				$('.progress .progress-bar').css('width', contador + '%');

				contador = contador + 1;

			}, 50);

			setTimeout(function(){
				clearInterval(progreso);

				$('#alert-wrapper').animate({
					height: 0,
				}, 500, function(){
					$(this).remove();
				});
			}, 6000);

		}

	});

</script>