<?php 
	
class LoginModel extends Mysql
{
	private $intIdUsuario;
	private $strUsuario;
	private $strPassword;

	public function __construct()
	{
		parent::__construct();
	}	

	public function loginUser(string $usuario, string $password)
	{	
		
		$this->strUsuario = $usuario;
		$this->strPassword = $password;
		
		$sql = "SELECT id, `status`, id_rol, `type` FROM usuario WHERE 
				username = '$this->strUsuario' and 
				`password` = '$this->strPassword' and 
				`status` != 0 ";
		$request = $this->select($sql);
		return $request;
	}

	public function sessionLogin(int $iduser, int $rol){
		
		// Obtener datos básicos del usuario
			$sql = "SELECT u.username, u.delete_mov, r.name AS NOMBRE_ROL, r.id AS ID_ROL, u.id_enterprise
				FROM usuario u
				INNER JOIN rol r ON r.id = u.id_rol
				WHERE u.id = $iduser";

		$userData = $this->select($sql);

		if(empty($userData)) {
			return false;
		}

		// Para todos los roles, usar la empresa asignada en la tabla usuario
		$sqlEnterprise = "SELECT id AS id_enterprise, rif, token, `table`, `name` as enterpriseName 
						FROM empresa 
						WHERE id = " . $userData['id_enterprise'];
		$enterpriseData = $this->select($sqlEnterprise);
		
		if($enterpriseData) {
			$userData = array_merge($userData, $enterpriseData);
		}

		// Para roles 2 y 3, obtener lista de empresas disponibles desde usuario_empresa
		if($rol == 2 || $rol == 3) {
			$sqlUserEnterprises = "SELECT ue.enterprise_id, e.name as enterpriseName
								FROM usuario_empresa ue
								INNER JOIN empresa e ON e.id = ue.enterprise_id
								WHERE ue.user_id = $iduser AND e.status = 1";
			$userEnterprises = $this->select_all($sqlUserEnterprises);
			
			if(!empty($userEnterprises)) {
				// Guardar array de IDs de empresas disponibles para el usuario
				$userData['user_enterprises'] = array_column($userEnterprises, 'enterprise_id');
			} else {
				// Si no tiene empresas en usuario_empresa, solo puede acceder a su empresa asignada
				$userData['user_enterprises'] = [$userData['id_enterprise']];
			}
		} else {
			// Administradores tienen acceso a todas las empresas
			$userData['user_enterprises'] = [$userData['id_enterprise']];
		}

		// Agregar campo USUARIO para compatibilidad con el sistema existente
		$userData['USUARIO'] = $userData['username'];

		$_SESSION['userData'] = $userData;
		return $userData;	
		
	}

}

?>