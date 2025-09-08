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

			$sql = "SELECT * FROM $table ORDER BY id DESC LIMIT 10";
			$request = $this->select_all($sql);
			return $request;
		}

		public function getCountTransaccion()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];

			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);

			$table = $requestEnterprise['table'];

			$sql = "SELECT count(*) as total FROM $table";
			$request = $this->select($sql);
			return $request;
		}

		public function getCountEnterprise()
		{
			$sql = "SELECT count(*) as total FROM empresa WHERE status = 1";
			$request = $this->select($sql);
			return $request;
		}

		public function getCountBank()
		{
			$sql = "SELECT count(*) as total FROM banco WHERE status = 1";
			$request = $this->select($sql);
			return $request;
		}

		public function getEnterprise()
		{
			$sql = "SELECT * FROM empresa ORDER BY id DESC LIMIT 10";
			$request = $this->select_all($sql);
			return $request;
		}

		public function getBank()
		{
			$sql = "SELECT b.id, b.account, b.name, e.name as enterprise 
					FROM banco b
					INNER JOIN empresa e ON e.id = b.id_enterprise ORDER BY id DESC LIMIT 10";
			$request = $this->select_all($sql);
			return $request;
		}

		public function getCountIngresos()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];

			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);

			$table = $requestEnterprise['table'];

			$sql = "SELECT count(*) as total FROM $table WHERE amount > 0";
			$request = $this->select($sql);
			return $request;
		}

		public function getCountEgresos()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];

			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);

			$table = $requestEnterprise['table'];

			$sql = "SELECT count(*) as total FROM $table WHERE amount < 0";
			$request = $this->select($sql);
			return $request;
		}

		public function getSumaIngresos()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];

			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);

			$table = $requestEnterprise['table'];

			$sql = "SELECT COALESCE(SUM(amount), 0) as total FROM $table WHERE amount > 0";
			$request = $this->select($sql);
			return $request;
		}

		public function getSumaEgresos()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];

			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);

			$table = $requestEnterprise['table'];

			$sql = "SELECT COALESCE(ABS(SUM(amount)), 0) as total FROM $table WHERE amount < 0";
			$request = $this->select($sql);
			return $request;
		}
	}	


?>
