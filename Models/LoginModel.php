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
		
		$sql = "SELECT u.username, r.name AS NOMBRE_ROL, e.id AS id_enterprise, e.rif, e.token, e.table, r.id AS ID_ROL, e.name as enterpriseName
			FROM usuario u
			INNER JOIN rol r ON r.id = u.id_rol
			INNER JOIN empresa e ON e.id = u.id_enterprise
			WHERE u.id = $iduser";

		$request = $this->select($sql);

		$_SESSION['userData'] = $request;
		return $request;	
		
	}

}

?>