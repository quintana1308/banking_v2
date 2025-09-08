<?php 

	class LogSessionModel extends Mysql
	{	


		public function __construct()
		{
			parent::__construct();
		}
		
		public function getLogSession(){

			$sql = "SELECT * FROM log_session";
			$request = $this->select_all($sql);

			return $request;
		}

		public function infoLogSession($id)
		{
			$sql = "SELECT e.rif AS RIF_EMPRESA, 
						e.name AS NOMBRE_EMPRESA, 
						e.CLIENTE AS CLIENTE_EMPRESA,
						l.USUARIO AS LOG_USUARIO,
						l.FECHA AS LOG_FECHA,
						l.IP AS LOG_IP,
						l.SISTEMA_OPERATIVO AS LOG_SISTEMA_OPERATIVO,
						l.VERSION AS LOG_VERSION,
						l.EXE AS LOG_EXE 
					FROM log_session l 
					INNER JOIN enterprise e ON e.RIF = l.RIF WHERE l.ID = '$id'";
			$request = $this->select($sql);

			return $request;
		}


	}	


 ?>
