<h3>Estimad@ <?=$datos['nombre_cliente']; ?></h3>
<p>Su pedido a sido entregado a <?=$datos['currier'];?> para su despacho</p>
<p>Para conocer dónde está su pedido debe:</p>
<ul>
	<li>Copiar su número de seguimiento <b><?=$datos['ot'];?></b></li>
	<li>Ingresar a <?=$datos['tracking_url'];?></li>
</ul>
<br>
<br>
<label>Atentamente <?=$datos['Tienda']['nombre'];?></label>