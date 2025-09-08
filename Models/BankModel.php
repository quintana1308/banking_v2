<?php

	require_once("Libraries/Core/Mysql2.php");

	class BankModel extends Mysql
	{	


		public function __construct()
		{
			parent::__construct();
			
		}
	
		public function getEnterprise()
		{
			$sql = "SELECT * FROM empresa";

			$request = $this->select_all($sql);

			return $request;
		}

        public function getBanks()
		{
			$sql = "SELECT b.id, b.name, b.account, e.name as enterprise, b.id_bank, b.banco, b.status FROM banco b
                    INNER JOIN empresa e ON e.id = b.id_enterprise";

			$request = $this->select_all($sql);

			return $request;
		}

		public function getBank($id)
		{
			$sql = "SELECT * FROM banco WHERE id = $id";

			$request = $this->select($sql);

			return $request;
		}

		public function updateBank($id, $name, $account, $id_bank, $id_enterprise, $prefix)
		{	
			$sql = "UPDATE banco SET id_bank = ?, id_enterprise = ?, account = ?, name = ?, banco = ?
					WHERE id = ?";
			$valueArray = array($id_bank, $id_enterprise, $account, $name, $prefix, $id);
			$request = $this->update($sql, $valueArray);
			return $request;
		}

		public function setBank($name, $account, $id_bank, $id_enterprise, $prefix)
		{	
			$sql = "INSERT INTO banco (id_bank, id_enterprise, account, `name`, banco)
           			 VALUES (?,?,?,?,?)";
			$valueArray = array($id_bank, $id_enterprise, $account, $name, $prefix);
			$request = $this->insert($sql, $valueArray);
			return $request;
		}


		public function deleteBank($id)
		{	
			$selectSql = "SELECT * FROM banco WHERE id = $id";
			$request = $this->select($selectSql);

			if($request['status'] == 1){
				$status = 0;
			}else{
				$status = 1;
			}

			$sql = "UPDATE banco SET status = ? WHERE id = ?";
			$valueArray = array($status, $id);
			$request = $this->update($sql, $valueArray);

			if($request){
				if($status == 1){
					$response = 'activado';
				}else{
					$response = 'desactivado';
				}
				return $response;
			}else{
				return $request;
			}
			
		}

	}	


?>
