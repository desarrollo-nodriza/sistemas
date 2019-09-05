<script type="text/javascript">
	window.onbeforeunload = function(e) {
	  return 'Recuerde guardar el PDF antes de salir.';
	};
</script>

<iframe src="<?=$url;?>/Pdf/OrdenCompra/<?=$oc['OrdenCompra']['id'];?>/<?=$oc['OrdenCompra']['pdf'];?>" frameborder="0" style="width: 100%; height: 100vh;"></iframe>