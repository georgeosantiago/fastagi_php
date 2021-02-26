<?php

/*
[from-internal-custom]	

exten => 351,1,Noop(Extension 351)
same => n,Answer
same => n,Wait(1)
same => n,Set(campo1=valor1)
same => n,Playback(custom/saludo)
same => n,AGI(agi://192.168.0.52/test/sample.php,ejemplo1)

same => n,NoOp(campo1: ${campo1})
same => n,NoOp(test: ${test})

same => n,Hangup
*/

	// Parametros del PHP FastAGI bootstrap
	$request = $fastagi->request;
		
	$fastagi->verbose('Genial, el servidor FastAGI ha sido llamado!');	
	$fastagi->verbose('parametros='.$request['agi_arg_1']);
		
	$fastagi->set_variable("test", "1111");
	$fastagi->stream_file('custom/saludo');
	
	$valor=$fastagi->get_variable("campo1");
	$fastagi->verbose('->campo1:'.$valor["data"]);	

?>