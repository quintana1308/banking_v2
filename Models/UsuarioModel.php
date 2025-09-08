<?php 

	class UsuarioModel extends Mysql
	{	


		public function __construct()
		{
			parent::__construct();
		}
		
		public function getUsuarios(){

			$sql = "SELECT * FROM usuario";
			$request = $this->select_all($sql);

			return $request;
		}

		public function getEnterpriseUser($idUser)
		{
			$sql = "SELECT e.*
				FROM empresa e
				JOIN usuario_empresa ue ON ue.enterprise_id = e.id
				WHERE ue.user_id = $idUser";
						
			$request = $this->select_all($sql);

			return $request;
		}

        public function infoUsuario($id)
        {
            $sql = "SELECT * FROM usuario WHERE ID = '$id'";
			$request = $this->select($sql);

			return $request;
        }

		public function updateUsuario($id, $nombre, $username, $enterprise)
		{
			$sql = "UPDATE usuario SET name = ?, username = ?, id_enterprise = ? WHERE id = '$id'";
			$valueArray = array($nombre, $username, $enterprise);
			$request = $this->update($sql, $valueArray);

			return $request;
		}

		public function updatePassword($id, $password)
		{
			$hashedPassword = hash('SHA256', $password);

			$sql = "UPDATE usuario SET `CLAVE` = ? WHERE ID = '$id'";
			$valueArray = array($hashedPassword);
			$request = $this->update($sql, $valueArray);

			return $request;
		}
		
		
		public function sessionLogin(int $iduser, int $rol){
		
		$sql = "SELECT u.username, r.name AS NOMBRE_ROL, e.id AS id_enterprise, e.rif, e.token, e.table, r.id AS ID_ROL, e.name as enterpriseName
			FROM usuario u
			INNER JOIN rol r ON r.id = u.id_rol
			INNER JOIN empresa e ON e.id = u.id_enterprise
			WHERE u.id = $iduser";

		$request = $this->select($sql);
				
		unset($_SESSION['userData']);
				
		$_SESSION['userData'] = $request;
		return $request;	
		
	}


	}	


 ?>