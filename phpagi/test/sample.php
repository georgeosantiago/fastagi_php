<?php
	// Parametros del PHP FastAGI bootstrap
	$request = $fastagi->request;
		
	$fastagi->verbose('Genial, el servidor FastAGI ha sido llamado!');	
	$fastagi->verbose('parametros='.$request['agi_arg_1']);
		
	$fastagi->set_variable("test", "1111");
	$fastagi->stream_file('custom/saludo');
	
	$valor=$fastagi->get_variable("campo1");
	$fastagi->verbose('->campo1:'.$valor["data"]);	

?>