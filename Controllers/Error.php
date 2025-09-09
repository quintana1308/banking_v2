<?php 

	class Errors extends Controllers{
		public function __construct()
		{
			parent::__construct();
		}

		public function notFound()
		{
			$this->views->getView($this,"error");
		}

		public function permisos()
		{
			$data['page_title'] = "Acceso Denegado";
			$data['error_title'] = "Acceso Denegado";
			$data['error_message'] = "No tienes permisos para acceder a este módulo.";
			$data['error_code'] = "403";
			$this->views->getView($this,"permisos", $data);
		}
	}

	$notFound = new Errors();
	$notFound->notFound();
 ?>