<form id="oneclick" action="<?= $url; ?>" method="POST">
	<input type="hidden" name="TBK_TOKEN" value="<?= $token; ?>">
</form>
<script type="text/javascript">
document.getElementById('oneclick').submit();
</script>
