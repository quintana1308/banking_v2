<?php 

class Logout extends Controllers{

	public function __construct()
	{
		parent::__construct();
		if(empty($_SESSION['login']))
		{	
			header('Location: '.base_url().'/login');
			exit();
		}
	}

	public function logout()
	{	
		session_destroy(); // Destruir la sesión
		header("Location: ".base_url(). "/login"); // Redireccionar a la página de login
		exit;
	}


	

}
?>