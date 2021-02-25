<?php
	// Parametros del PHP FastAGI bootstrap
	$request = $fastagi->request;
		
	$fastagi->verbose('Genial, el servidor FastAGI ha sido llamado!');	
	$fastagi->verbose('parametros='.$request['agi_arg_1'].' '.$request['agi_arg_2']);
	

	$fastagi->set_variable("test", "1111");
	$fastagi->stream_file('custom/welcomecourse');
	
	$valor=$fastagi->get_variable("test");
	$fastagi->verbose('->test:'.$valor["data"]);	
	
?>