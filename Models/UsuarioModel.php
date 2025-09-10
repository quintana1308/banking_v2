<?php 

	class UsuarioModel extends Mysql
	{	


		public function __construct()
		{
			parent::__construct();
		}
		
		public function getUsuarios(){
			$sql = "SELECT u.id, u.name, u.username, u.type, u.delete_mov, u.status,
					       r.name as rol_name, e.name as enterprise_name
					FROM usuario u
					LEFT JOIN rol r ON r.id = u.id_rol
					LEFT JOIN empresa e ON e.id = u.id_enterprise
					ORDER BY u.status DESC, u.name ASC";
			$request = $this->select_all($sql);

			return $request;
		}

		public function getRoles(){
			$sql = "SELECT id, name FROM rol ORDER BY name ASC";
			$request = $this->select_all($sql);
			return $request;
		}

		public function getEmpresas(){
			$sql = "SELECT id, name FROM empresa ORDER BY name ASC";
			$request = $this->select_all($sql);
			return $request;
		}

		public function insertUsuario($name, $username, $password, $id_rol, $id_enterprise, $type, $delete_mov){
			$hashedPassword = hash('SHA256', $password);
			
			$sql = "INSERT INTO usuario (name, username, password, id_rol, id_enterprise, type, delete_mov) 
					VALUES (?, ?, ?, ?, ?, ?, ?)";
			$valueArray = array($name, $username, $hashedPassword, $id_rol, $id_enterprise, $type, $delete_mov);
			$request = $this->insert($sql, $valueArray);

			return $request;
		}

		public function updateUsuarioAdmin($id, $name, $username, $id_rol, $id_enterprise, $type, $delete_mov){
			$sql = "UPDATE usuario SET name = ?, username = ?, id_rol = ?, id_enterprise = ?, type = ?, delete_mov = ? 
					WHERE id = ?";
			$valueArray = array($name, $username, $id_rol, $id_enterprise, $type, $delete_mov, $id);
			$request = $this->update($sql, $valueArray);

			return $request;
		}

		public function deleteUsuario($id){
			$sql = "UPDATE usuario SET status = 0 WHERE id = ?";
			$valueArray = array($id);
			$request = $this->update($sql, $valueArray);

			return $request;
		}

		public function activateUsuario($id){
			$sql = "UPDATE usuario SET status = 1 WHERE id = ?";
			$valueArray = array($id);
			$request = $this->update($sql, $valueArray);

			return $request;
		}

		public function checkUsernameExists($username, $excludeId = null){
			$sql = "SELECT id FROM usuario WHERE username = '$username'";
			
			
			if($excludeId !== null) {
				$sql .= " AND id != $excludeId";
			}
			
			$request = $this->select($sql);
			return !empty($request);
		}

		// Insertar usuario con múltiples empresas
		public function insertUsuarioWithEmpresas($name, $username, $password, $id_rol, $id_enterprise_principal, $type, $delete_mov, $empresas){
			// Iniciar transacción
			//$this->conexion->beginTransaction();

			try {
				// 1. Insertar usuario principal
				$password_hash = hash('sha256', $password);
				$sql = "INSERT INTO usuario (name, username, password, id_rol, id_enterprise, type, delete_mov) VALUES (?, ?, ?, ?, ?, ?, ?)";
				$valueArray = array($name, $username, $password_hash, $id_rol, $id_enterprise_principal, $type, $delete_mov);
				$userId = $this->insertID($sql, $valueArray);
				
				if($userId) {
					// 2. Insertar relaciones en usuario_empresa
					foreach($empresas as $enterpriseId) {
						$sqlEmpresa = "INSERT INTO usuario_empresa (user_id, enterprise_id) VALUES (?, ?)";
						$valueArrayEmpresa = array($userId, $enterpriseId);
						$this->insert($sqlEmpresa, $valueArrayEmpresa);
					}
					
					// Confirmar transacción
					//$this->conexion->commit();
					return $userId;
				} else {
					//$this->conexion->rollback();
					return false;
				}
			} catch (Exception $e) {
				//$this->conexion->rollback();
				return false;
			}
		}

		// Actualizar usuario con múltiples empresas
		public function updateUsuarioWithEmpresas($id, $name, $username, $id_rol, $empresas, $type, $delete_mov){
			// Iniciar transacción
			//$this->conexion->beginTransaction();
			
			try {

				// 1. Determinar empresa principal
				$currentEnterprise = $this->select("SELECT id_enterprise FROM usuario WHERE id = $id");
				$currentEnterpriseId = $currentEnterprise ? $currentEnterprise['id_enterprise'] : null;
				
				// Si la empresa actual no está en las seleccionadas, usar la primera seleccionada
				$newPrincipalEnterprise = in_array($currentEnterpriseId, $empresas) ? $currentEnterpriseId : $empresas[0];
				
				// 2. Actualizar usuario principal
				$sql = "UPDATE usuario SET `name` = ?, username = ?, id_rol = ?, id_enterprise = ?, `type` = ?, delete_mov = ? WHERE id = ?";
				$valueArray = array($name, $username, $id_rol, $newPrincipalEnterprise, $type, $delete_mov, $id);
				$request = $this->update($sql, $valueArray);

				if($request) {
					// 3. Eliminar relaciones existentes en usuario_empresa
					$sqlDelete = "DELETE FROM usuario_empresa WHERE user_id = $id";
					$this->delete($sqlDelete);
					
					// 4. Insertar nuevas relaciones
					foreach($empresas as $enterpriseId) {
						$sqlEmpresa = "INSERT INTO usuario_empresa (user_id, enterprise_id) VALUES (?, ?)";
						$valueArrayEmpresa = array($id, $enterpriseId);
						$this->insert($sqlEmpresa, $valueArrayEmpresa);
					}
					
					// Confirmar transacción
					//$this->conexion->commit();
					return true;
				} else {
					//$this->conexion->rollback();
					return false;
				}
			} catch (Exception $e) {
				//$this->conexion->rollback();
				return false;
			}
		}

		public function getUserEnterprises($userId){
			$sql = "SELECT ue.enterprise_id, e.name as enterprise_name, e.rif, e.token, e.table
					FROM usuario_empresa ue
					INNER JOIN empresa e ON e.id = ue.enterprise_id
					WHERE ue.user_id = $userId AND e.status = 1
					ORDER BY e.name ASC";
			$request = $this->select_all($sql);
			return $request;
		}

		// Actualizar empresa actual del usuario
		public function updateCurrentEnterprise($userId, $enterpriseId){
			$sql = "UPDATE usuario SET id_enterprise = ? WHERE id = ?";
			$valueArray = array($enterpriseId, $userId);
			$request = $this->update($sql, $valueArray);
			return $request;
		}

		// Obtener datos de una empresa específica
		public function getEnterpriseData($enterpriseId){
			$sql = "SELECT id, `name`, rif, token, `table` FROM empresa WHERE id = $enterpriseId";
			$request = $this->select($sql);
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