<?php 
if ($this->request->params['formato'] == 'xml') { 
	print_r($salida);
	
}elseif ($this->request->params['formato'] == 'json') {
	
	echo json_encode($xmlArray, JSON_UNESCAPED_UNICODE);
	
}



?>