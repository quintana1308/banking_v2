<?php 

class Login extends Controllers{
	public function __construct()
	{
		parent::__construct();
		if($_SESSION){
			header('Location: '.base_url().'/home');
            exit;
		}
	}

	public function login()
	{
		$data['page_tag'] = "Banking ADN";
		$data['page_title'] = "Banking ADN";
		$data['page_name'] = "login";
		$data['page_functions_js'] = "functions_login.js";

		$this->views->getView($this,"login",$data);
	}

	public function loginUser(){
		
		
        if($_POST){
			if(empty($_POST['txtUsername']) || empty($_POST['txtPassword'])){
				$arrResponse = array('status' => false, 'msg' => 'Error de datos' );
			}else{
				$strUsername  =  strtolower(strClean($_POST['txtUsername']));
				$strPassword = hash('SHA256', $_POST['txtPassword']);
				
				$requestUser = $this->model->loginUser($strUsername, $strPassword);

				if(empty($requestUser)){
					$arrResponse = array('status' => false, 'msg' => 'El usuario o la contraseña es incorrecto.' ); 
				}else{
					$arrData = $requestUser;
					if($arrData['status'] == 1){
						$_SESSION['idUser'] = $arrData['id'];
						$_SESSION['typeUser'] = $arrData['type'];
						$_SESSION['login'] = true;
						$_SESSION['user_token'] = bin2hex(random_bytes(32));
						$_SESSION['rol'] = $arrData['id_rol'];

						$arrData = $this->model->sessionLogin($_SESSION['idUser'], $_SESSION['rol']);
						$arrResponse = array('status' => true, 'msg' => $arrData);
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Usuario inactivo.');
					}
				}
			}
			echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		}
		die();
	}

}
?>