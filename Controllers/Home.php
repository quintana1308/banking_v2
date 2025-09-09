<?php 

class Home extends Controllers{

	public function __construct()
	{	
		
		
	
	
		parent::__construct();
		if(empty($_SESSION['login']))
		{	
			header('Location: '.base_url().'/login');
			exit();
		}
	}

	public function home()
	{	
		$data['page_functions_js'] = "functions_home.js";
		$data['transaccion'] = $this->model->getTransaccion();
		$data['countTransaccion'] = $this->model->getCountTransaccion();
		$data['countIngresos'] = $this->model->getCountIngresos();
		$data['countEgresos'] = $this->model->getCountEgresos();
		$data['countBank'] = $this->model->getCountBank();
		$data['enterprise'] = $this->model->getEnterprise();
		$data['bank'] = $this->model->getBank();

		$this->views->getView($this,"home", $data);
	}
	
}
?>