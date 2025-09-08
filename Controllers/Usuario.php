<?php 

class Usuario extends Controllers{

	public function __construct()
	{
		parent::__construct();
		if(empty($_SESSION['login']))
		{	
			header('Location: '.base_url().'/login');
			exit();
		}
	}

	public function usuario()
	{	
		$idUser = $_SESSION['idUser'];
		$data['page_functions_js'] = "functions_user.js";
		$data['getEnterprisesUser'] = $this->model->getEnterpriseUser($idUser);
		$data['infoUser'] = $this->model->infoUsuario($idUser);

		$this->views->getView($this,"gestor", $data);
	}

    public function edit()
    {
        // Verificar si el parámetro 'id' está presente en la URL
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        // Validar que el ID sea válido antes de usarlo
        if ($id === null || $id <= 0) {
            die("ID no válido");
        }

        $data['infoUsuario'] = $this->model->infoUsuario($id);
		$data['functions'] = 'functions_usuario.js';
        $this->views->getView($this,"edit", $data);  
    }

	public function updateUsuario()
	{	

		if($_POST){
			if(empty($_POST['name']) || empty($_POST['username']) || empty($_POST['enterprise'])){
				$arrResponse = array('status' => false, 'msg' => 'Error de datos' );
			}else{

				$id = $_SESSION['idUser'];
				$name = $_POST['name'];
				$username = $_POST['username'];
				$enterprise = $_POST['enterprise'];

				$res = $this->model->updateUsuario($id, $name, $username, $enterprise);
				if($res){
					
					 $this->model->sessionLogin($_SESSION['idUser'], $_SESSION['rol']);
					$arrResponse = array('status' => true, 'msg' => 'El usuario se ha actualizado correctamente');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Hubo un error al actualizar el usuario');
				}
				
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function changePassword()
    {
        // Verificar si el parámetro 'id' está presente en la URL
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        // Validar que el ID sea válido antes de usarlo
        if ($id === null || $id <= 0) {
            die("ID no válido");
        }

        $data['infoUsuario'] = $this->model->infoUsuario($id);
		$data['functions'] = 'functions_usuario.js';
        $this->views->getView($this,"changePassword", $data);  
    }

	public function updatePassword()
	{	

		if($_POST){
			if(empty($_POST['password'])){
				$arrResponse = array('status' => false, 'msg' => 'Error de datos' );
			}else{

				$id = $_POST['id'];
				$password = $_POST['password'];

				$res = $this->model->updatePassword($id, $password);
				if($res){
					$arrResponse = array('status' => true, 'msg' => 'La contraseña se ha actualizado correctamente');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Hubo un error al cambiar la contraseña');
				}
				
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

}
?>