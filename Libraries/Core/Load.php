<?php 
	$controller = ucwords($controller);
	$controllerFile = "Controllers/".$controller.".php";
	if(file_exists($controllerFile))
	{
		require_once($controllerFile);
		$controller = new $controller();
		if(method_exists($controller, $method))
		{
			$controller->{$method}($params);
		}else{
			// Método no existe - mostrar error 404
			require_once("Controllers/Errors.php");
			$errorController = new Errors();
			$errorController->notFound();
		}
	}else{
		// Controlador no existe - mostrar error 404
		require_once("Controllers/Errors.php");
		$errorController = new Errors();
		$errorController->notFound();
	}

 ?>