<?php 

class Bank extends Controllers{

	public function __construct()
	{	
		parent::__construct();
		if(empty($_SESSION['login']))
		{	
			header('Location: '.base_url().'/login');
			exit();
		}
		
		// Verificar permisos de acceso al módulo de cuentas bancarias
		requireModuleAccess('cuentas_bancarias');
	}

	//MOSTRAR VISTA DE CUENTAS BACARIAS 
	public function bank()
	{	
		$data['page_functions_js'] = "functions_bank.js";
		$this->views->getView($this,"bank", $data);
	}

	//OBTENER UN LISTADO DE TODAS LAS CUENTAS BACARIAS 
	public function getBanks()
	{	
		$banks = $this->model->getBanks();
		echo json_encode($banks,JSON_UNESCAPED_UNICODE);
	}

	//VISTA PARA CREAR UNA CUENTA BACARIA
	public function new()
	{	
		$data['enterprise'] = $this->model->getEnterprise();
		$data['page_functions_js'] = "functions_bank.js";
		$this->views->getView($this,"new", $data);
	}

	//VISTA PARA EDITAR UNA CUENTA BACARIA
	public function edit($id)
	{	
		$data['page_functions_js'] = "functions_bank.js";
		$data['enterprise'] = $this->model->getEnterprise();
		$data['bank'] = $this->model->getBank($id);
		$this->views->getView($this, "edit", $data);
	}

	//PROCESO DE CREAR UNA CUENTA BACARIA
	public function setBank()
	{	
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return $this->jsonResponse(false, 'Método no permitido');
		}

		$name = $_POST['name'] ?? null;
		$account = $_POST['account'] ?? null;
		$id_bank = $_POST['id_bank'] ?? null;
		$id_enterprise = $_POST['id_enterprise'] ?? null;
		$prefix = $_POST['prefix'] ?? null;

		$response = $this->model->setBank($name, $account, $id_bank, $id_enterprise, $prefix);
		if($response){
			echo json_encode([
				'status' => true,
				'message' => 'La cuenta bancaria se ha creado de manera correcta.'
			]);
			die();
		}else{
			echo json_encode([
				'status' => false,
				'message' => 'No se pudo crear la cuenta bancaria.'
			]);
			die();
		}
	}

	//PROCESO PARA ACTUALIZAR UNA CUENTA BANCARIA
	public function updateBank()
	{	
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			return $this->jsonResponse(false, 'Método no permitido');
		}
		$id = $_POST['id'] ?? null;
		$name = $_POST['name'] ?? null;
		$account = $_POST['account'] ?? null;
		$id_bank = $_POST['id_bank'] ?? null;
		$id_enterprise = $_POST['id_enterprise'] ?? null;
		$prefix = $_POST['prefix'] ?? null;

		$response = $this->model->updateBank($id, $name, $account, $id_bank, $id_enterprise, $prefix);
		if($response){
			echo json_encode([
				'status' => true,
				'message' => 'La cuenta bancaria se ha actualizado de manera correcta.'
			]);
			die();
		}else{
			echo json_encode([
				'status' => false,
				'message' => 'No se pudo actualizar la cuenta bancaria.'
			]);
			die();
		}
	}

	//PROCESO PARA ELIMINAR UNA CUENTA BANCARIA
	public function deleteBank($id)
	{		
		$response = $this->model->deleteBank($id);
		if($response){
			echo json_encode([
				'status' => true,
				'message' => 'La cuenta bancaria se '.$response.' de manera exitosa'
			]);
			die();
		}else{
			echo json_encode([
				'status' => false,
				'message' => 'No se pudo desactivar/activar la cuenta bancaria.'
			]);
			die();
		}
		
	}
	
}
