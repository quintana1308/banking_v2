<?php 
	spl_autoload_register(function($class){
		if(file_exists("Libraries/".'Core/'.$class.".php")){
			require_once("Libraries/".'Core/'.$class.".php");
		}
	});

	// Incluir helpers del sistema
	require_once("Helpers/Helpers.php");
	require_once("Helpers/PermissionsHelper.php");
 ?>