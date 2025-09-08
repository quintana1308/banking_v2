<?php 

class LogSession extends Controllers{

	public function __construct()
	{
		parent::__construct();
		if(empty($_SESSION['login']))
		{	
			header('Location: '.base_url().'/login');
			exit();
		}
	}


	//FUNCIONES PARA SUERVISOR

	public function logSession()
	{	
        $data['getLogSession'] = $this->model->getLogSession();

		$this->views->getView($this,"list", $data);
	}

    public function show()
    {      
        // Verificar si el parámetro 'id' está presente en la URL
        $id = isset($_GET['id']) ? intval($_GET['id']) : null;

        // Validar que el ID sea válido antes de usarlo
        if ($id === null || $id <= 0) {
            die("ID no válido");
        }

        $data['infoLogSession'] = $this->model->infoLogSession($id);
        $this->views->getView($this,"show", $data);    
    }


	

}
?>