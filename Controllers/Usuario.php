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

	public function usuarios()
	{
		$data['page_functions_js'] = "functions_usuarios.js";
		$this->views->getView($this,"usuarios", $data);
	}

	public function getUsuarios()
	{
		$usuarios = $this->model->getUsuarios();
		
		for($i = 0; $i < count($usuarios); $i++){
			$usuarios[$i]['type_text'] = $usuarios[$i]['type'] == 1 ? 'Normal' : 'Especial';
			$usuarios[$i]['delete_mov_text'] = $usuarios[$i]['delete_mov'] == 1 ? 'Sí' : 'No';
			$usuarios[$i]['status_text'] = $usuarios[$i]['status'] == 1 ? 'Activo' : 'Inactivo';
			
			// Botones según el status del usuario
			if($usuarios[$i]['status'] == 1) {
				// Usuario activo - mostrar editar y desactivar
				$usuarios[$i]['options'] = '<div class="text-center">
					<button class="btn-action btn-edit" onClick="editUsuario('.$usuarios[$i]['id'].')" title="Editar">
						<i class="fas fa-pencil-alt"></i>
					</button>
					<button class="btn-action btn-delete" onClick="deleteUsuario('.$usuarios[$i]['id'].')" title="Desactivar">
						<i class="fas fa-user-slash"></i>
					</button>
				</div>';
			} else {
				// Usuario inactivo - mostrar solo activar
				$usuarios[$i]['options'] = '<div class="text-center">
					<button class="btn-action btn-activate" onClick="activateUsuario('.$usuarios[$i]['id'].')" title="Activar">
						<i class="fas fa-user-check"></i>
					</button>
				</div>';
			}
		}

		echo json_encode($usuarios, JSON_UNESCAPED_UNICODE);
		die();
	}

	public function newUsuario()
	{
		$data['page_functions_js'] = "functions_usuarios.js";
		$data['roles'] = $this->model->getRoles();
		$data['empresas'] = $this->model->getEmpresas();
		$this->views->getView($this,"new", $data);
	}

	public function editUsuario()
	{
		$id = isset($_GET['id']) ? intval($_GET['id']) : null;

		if ($id === null || $id <= 0) {
			header('Location: '.base_url().'/usuario/usuarios');
			exit();
		}

		$data['page_functions_js'] = "functions_usuarios.js";
		$data['usuario'] = $this->model->infoUsuario($id);
		$data['roles'] = $this->model->getRoles();
		$data['empresas'] = $this->model->getEmpresas();
		$this->views->getView($this,"edit_usuario", $data);
	}

	public function setUsuario()
	{
		if($_POST){
			if(empty($_POST['name']) || empty($_POST['username']) || empty($_POST['password']) || 
			   empty($_POST['id_rol']) || empty($_POST['id_enterprise']) || empty($_POST['type']) || 
			   !isset($_POST['delete_mov'])){
				$arrResponse = array('status' => false, 'msg' => 'Todos los campos son obligatorios');
			}else{
				// Verificar si el username ya existe
				$usernameExists = $this->model->checkUsernameExists($_POST['username']);
				if($usernameExists){
					$arrResponse = array('status' => false, 'msg' => 'El nombre de usuario ya existe');
				}else{
					$name = $_POST['name'];
					$username = $_POST['username'];
					$password = $_POST['password'];
					$id_rol = $_POST['id_rol'];
					$id_enterprise = $_POST['id_enterprise'];
					$type = $_POST['type'];
					$delete_mov = $_POST['delete_mov'];

					$res = $this->model->insertUsuario($name, $username, $password, $id_rol, $id_enterprise, $type, $delete_mov);
					if($res){
						$arrResponse = array('status' => true, 'msg' => 'Usuario creado correctamente');
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Error al crear el usuario');
					}
				}
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function updateUsuarioAdmin()
	{
		if($_POST){
			if(empty($_POST['id']) || empty($_POST['name']) || empty($_POST['username']) || 
			   empty($_POST['id_rol']) || empty($_POST['id_enterprise']) || empty($_POST['type']) || 
			   !isset($_POST['delete_mov'])){
				$arrResponse = array('status' => false, 'msg' => 'Todos los campos son obligatorios');
			}else{
				$id = $_POST['id'];
				// Verificar si el username ya existe (excluyendo el usuario actual)
				$usernameExists = $this->model->checkUsernameExists($_POST['username'], $id);
				if($usernameExists){
					$arrResponse = array('status' => false, 'msg' => 'El nombre de usuario ya existe');
				}else{
					$name = $_POST['name'];
					$username = $_POST['username'];
					$id_rol = $_POST['id_rol'];
					$id_enterprise = $_POST['id_enterprise'];
					$type = $_POST['type'];
					$delete_mov = $_POST['delete_mov'];

					$res = $this->model->updateUsuarioAdmin($id, $name, $username, $id_rol, $id_enterprise, $type, $delete_mov);
					if($res){
						$arrResponse = array('status' => true, 'msg' => 'Usuario actualizado correctamente');
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Error al actualizar el usuario');
					}
				}
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function delUsuario()
	{
		if($_POST){
			if(empty($_POST['id'])){
				$arrResponse = array('status' => false, 'msg' => 'Error de datos');
			}else{
				$id = $_POST['id'];
				$res = $this->model->deleteUsuario($id);
				if($res){
					$arrResponse = array('status' => true, 'msg' => 'Usuario desactivado correctamente');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al desactivar el usuario');
				}
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
	}

	public function activateUsuario()
	{
		if($_POST){
			if(empty($_POST['id'])){
				$arrResponse = array('status' => false, 'msg' => 'Error de datos');
			}else{
				$id = $_POST['id'];
				$res = $this->model->activateUsuario($id);
				if($res){
					$arrResponse = array('status' => true, 'msg' => 'Usuario activado correctamente');
				}else{
					$arrResponse = array('status' => false, 'msg' => 'Error al activar el usuario');
				}
			}
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		}
		die();
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