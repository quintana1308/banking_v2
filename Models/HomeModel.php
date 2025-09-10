<?php

	require_once("Libraries/Core/Mysql2.php");

	class HomeModel extends Mysql
	{	
		public function __construct()
		{	
			parent::__construct();
		}

		public function getTransaccion()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];

			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);

			$table = $requestEnterprise['table'];

			// Obtener los IDs de los bancos que pertenecen a la empresa del usuario
			$sqlBanks = "SELECT id_bank FROM banco WHERE id_enterprise = $id_enterprise";
			$requestBanks = $this->select_all($sqlBanks);

			if (empty($requestBanks)) {
				return []; // Si no hay bancos para esta empresa, retornar array vacío
			}

			// Crear lista de IDs de bancos para la consulta IN
			$bankIds = array_column($requestBanks, 'id_bank');
			$bankIdsString = implode(',', $bankIds);

			// Filtrar transacciones por los bancos de la empresa
			$sql = "SELECT * FROM $table WHERE bank IN ($bankIdsString) ORDER BY id DESC LIMIT 10";
			$request = $this->select_all($sql);
			return $request;
		}

		public function getCountTransaccion()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];

			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);

			$table = $requestEnterprise['table'];

			// Obtener los IDs de los bancos que pertenecen a la empresa del usuario
			$sqlBanks = "SELECT id_bank FROM banco WHERE id_enterprise = $id_enterprise";
			$requestBanks = $this->select_all($sqlBanks);

			if (empty($requestBanks)) {
				return ['total' => 0]; // Si no hay bancos para esta empresa, retornar 0
			}

			// Crear lista de IDs de bancos para la consulta IN
			$bankIds = array_column($requestBanks, 'id_bank');
			$bankIdsString = implode(',', $bankIds);

			// Contar transacciones por los bancos de la empresa
			$sql = "SELECT count(*) as total FROM $table WHERE bank IN ($bankIdsString)";
			$request = $this->select($sql);
			return $request;
		}

		public function getCountBank()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];
			$sql = "SELECT count(*) as total FROM banco WHERE status = 1 AND id_enterprise = $id_enterprise";
			$request = $this->select($sql);
			return $request;
		}

		public function getEnterprise()
		{
			// Solo admin puede ver el listado de empresas
			if (!canViewEnterpriseList()) {
				return [];
			}
			
			$sql = "SELECT * FROM empresa WHERE status = 1 ORDER BY id DESC LIMIT 10";
			$request = $this->select_all($sql);
			return $request;
		}

		public function getBank()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];
			$sql = "SELECT b.id, b.account, b.name, e.name as enterprise 
					FROM banco b
					INNER JOIN empresa e ON e.id = b.id_enterprise 
					WHERE b.status = 1 AND b.id_enterprise = $id_enterprise
					ORDER BY b.id DESC LIMIT 10";
			$request = $this->select_all($sql);
			return $request;
		}

		public function getCountIngresos()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];

			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);

			$table = $requestEnterprise['table'];

			// Obtener los IDs de los bancos que pertenecen a la empresa del usuario
			$sqlBanks = "SELECT id_bank FROM banco WHERE id_enterprise = $id_enterprise";
			$requestBanks = $this->select_all($sqlBanks);

			if (empty($requestBanks)) {
				return ['total' => 0]; // Si no hay bancos para esta empresa, retornar 0
			}

			// Crear lista de IDs de bancos para la consulta IN
			$bankIds = array_column($requestBanks, 'id_bank');
			$bankIdsString = implode(',', $bankIds);
			
			$sql = "SELECT count(*) as total FROM $table WHERE amount > 0 AND bank IN ($bankIdsString)";
			$request = $this->select($sql);
			return $request;
		}

		public function getCountEgresos()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];

			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);

			$table = $requestEnterprise['table'];

			// Obtener los IDs de los bancos que pertenecen a la empresa del usuario
			$sqlBanks = "SELECT id_bank FROM banco WHERE id_enterprise = $id_enterprise";
			$requestBanks = $this->select_all($sqlBanks);

			if (empty($requestBanks)) {
				return ['total' => 0]; // Si no hay bancos para esta empresa, retornar 0
			}

			// Crear lista de IDs de bancos para la consulta IN
			$bankIds = array_column($requestBanks, 'id_bank');
			$bankIdsString = implode(',', $bankIds);
			
			$sql = "SELECT count(*) as total FROM $table WHERE amount < 0 AND bank IN ($bankIdsString)";
			$request = $this->select($sql);
			return $request;
		}
	}	


?>
