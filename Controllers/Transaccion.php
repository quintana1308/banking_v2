<?php 
	include dirname(__DIR__) . '/vendor/autoload.php';
	use PhpOffice\PhpSpreadsheet\IOFactory;

class Transaccion extends Controllers{

    /**
     * Controlador de Transacciones Bancarias
     * - Expone endpoints para listar, conciliar, asignar y cargar movimientos.
     * - Mantiene compatibilidad con rutas y modelos existentes (no se renombran métodos).
     */
		
	public function __construct()
	{	
		parent::__construct();
		$method = $_GET['url'] ?? ''; // obtener método actual
		if($method !== 'transaccion/getTransaction' && $method !== 'transaccion/getTransactionConciliation' && empty($_SESSION['login'])) {	
			header('Location: '.base_url().'/login');
			exit();
		}

		// Verificar permisos de acceso al módulo de transacciones
		if($method !== 'transaccion/getTransaction' && $method !== 'transaccion/getTransactionConciliation') {
			requireModuleAccess('transacciones');
		}
	}

	//FUNCIONES PARA SUERVISOR
	public function transaccion()
	{	
		$data['page_functions_js'] = "functions_transaction.js";
		$data['accounts'] = $this->model->getAccounts();
		$data['can_delete_transactions'] = canDeleteTransactions();
		$this->views->getView($this,"transaccion", $data);
	}

	//OBTENER UN LISTADO DE MOVIMIENTOS
	/**
	 * Obtener listado de movimientos con filtros por GET
	 * GET params: bank, account, reference, date, estado
	 * Return: JSON { data: [] }
	 */
	public function getMovimientos()
	{
		
		$filters = [
			'bank'     => $_GET['bank']     ?? '',
			'account'  => $_GET['account']  ?? '',
			'reference'=> $_GET['reference']?? '',
			'date'     => $_GET['date']     ?? '',
			'estado'   => $_GET['estado']    ?? '',
		];
		
		$arrData = $this->model->getTransaction($filters);
		
		// Agregar información de permisos para cada registro
		foreach ($arrData as &$row) {
			$row['can_delete'] = canDeleteTransactions();
		}
		
		echo json_encode(['data' => $arrData], JSON_UNESCAPED_UNICODE);
		die();
	}

	/**
	 * Eliminar una transacción específica
	 * Method: POST (JSON)
	 * Body: { id }
	 */
	public function deleteTransaction()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo json_encode(['status' => false, 'message' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// Verificar permisos de eliminación
		if (!canDeleteTransactions()) {
			echo json_encode(['status' => false, 'message' => 'No tienes permisos para eliminar transacciones.'], JSON_UNESCAPED_UNICODE);
			die();
		}

		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata, true);

		if (!isset($request['id']) || empty($request['id'])) {
			echo json_encode(['status' => false, 'message' => 'ID de transacción requerido.'], JSON_UNESCAPED_UNICODE);
			die();
		}

		$transactionId = intval($request['id']);
		$result = $this->model->deleteTransaction($transactionId);

		if ($result) {
			echo json_encode(['status' => true, 'message' => 'Transacción eliminada exitosamente.'], JSON_UNESCAPED_UNICODE);
		} else {
			echo json_encode(['status' => false, 'message' => 'Error al eliminar la transacción.'], JSON_UNESCAPED_UNICODE);
		}
		die();
	}
	
	//API PARA DEVOLVER DATA A SISTEMA ADN (AUTOCONCILIACION)
	/**
	 * API para devolver data a sistema ADN (Autoconciliación)
	 * Method: POST (JSON)
	 * Body: { token, rif, bd?, cuenta, opcion, desde, hasta }
	 */
	public function getTransaction(){
		// Verificación de método HTTP
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo json_encode(['status' => false, 'msg' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
			die();
		}

		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata, true);
		$arrResponse = ['status' => false, 'msg' => 'JSON inválido o vacío'];
		if (is_array($request)) {
			$token = $request['token'] ?? '';
			$rif = $request['rif'] ?? '';
			$bd = $request['bd'] ?? '';
			$cuenta = $request['cuenta'] ?? '';
			$opcion = $request['opcion'] ?? '';
			$desde = $request['desde'] ?? '';
			$hasta = $request['hasta'] ?? '';

			if ($token !== '' && $rif !== '' && $cuenta !== '' && $opcion !== '' && $desde !== '' && $hasta !== '') {
				$getTransaction = $this->model->getTransactionEndPoint($token, $rif, $bd, $cuenta, $opcion, $desde, $hasta);
				$arrResponse = array('status' => true, 'data' => $getTransaction );
			} else {
				$arrResponse = ['status' => false, 'msg' => 'Campos requeridos faltantes'];
			}
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}

	//REALIZAR AUTOCONSOLIDACION Y PINTAR MOVIMIENTOS
	/**
	 * Realiza autoconciliación y retorna movimientos conciliados
	 * Method: POST (JSON)
	 */
	public function getTransactionConciliation(){

		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo json_encode(['status' => false, 'msg' => 'Método no permitido.'], JSON_UNESCAPED_UNICODE);
			die();
		}

		$postdata = file_get_contents("php://input");
		$clean = preg_replace('/[\x{00}-\x{1F}\x{7F}\x{A0}]/u','', $postdata);
		$movimientos = json_decode($clean, true);
		$arrResponse = ['status' => false, 'msg' => 'JSON inválido o vacío'];

		if (is_array($movimientos)) {
			$getTransaction = $this->model->getTransactionConciliation($movimientos);
			$arrResponse = array('status' => true, 'info' => $getTransaction );
		}
		echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
		die();
	}
	
	//HACER CHEQUEO DE LOS MOVIMIENTOS CONSOLIDADOS
	/**
	 * Chequea movimientos conciliados contra servicio externo
	 * Method: POST
	 */
	public function checkTransaccion()
	{	
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$arrResponse = array('status' => false, 'msg' => 'Método no permitido.' );
			echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
			die();
		}

		$account = $_POST['filterAccount'] ?? '';
		if (strpos($account, '-') === false) {
			echo json_encode(['status' => false, 'msg' => 'Parámetro account inválido.'], JSON_UNESCAPED_UNICODE);
			die();
		}
		list($codigo_banco, $cuenta_banco) = explode('-', $account, 2);

		$id_enterprise = $_SESSION['userData']['id_enterprise'];
		$enterprise = $this->model->getEnterprise($id_enterprise);
		
		$url = "https://banking.apps-adn.com/";
		$params = [
					"rif" => $enterprise['rif'],
					"token" => $enterprise['token'],
					"bd" => $enterprise['bd'],
					"opcion" => 'consultarMovimientosConciliados',
					"cuenta" => $cuenta_banco,
					"codigoBanco" => $codigo_banco,
					"fuente" => 'IA'
				];

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HTTPHEADER, [
			"Content-type: application/json"
		]);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));

		$result = curl_exec($curl);
		$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		$curl_error = curl_error($curl);
		curl_close($curl);

		if ($curl_error) {
			$arrResponse = array('status' => false, 'msg' => 'Error en conexión: ' . $curl_error);
		} elseif ($status_code == 200) {
			$resp = $this->model->validateConciliation($result);

			if ($resp['status'] == true) {
				$arrResponse = array('status' => true, 'msg' => $resp);
			} else {
				$arrResponse = array('status' => false, 'msg' => 'Error al chequear movimientos');
			}
		} else {
			$arrResponse = array(
				'status' => false,
				'msg' => 'Error en respuesta del servidor',
				'codigo_http' => $status_code,
				'respuesta' => $result
			);
		}

		echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
		die();
		
	}

	//ASIGNO A UN USUARIO UN MOVIMIENTO
	/**
	 * Asigna una transacción a un usuario de sesión
	 * Method: POST (JSON { id })
	 */
	public function asignarUsuario()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo json_encode(['status' => false, 'message' => 'Método no permitido']);
			exit;
		}

		$json = json_decode(file_get_contents('php://input'), true);
		$id = intval($json['id'] ?? 0);
		$userId = $_SESSION['idUser'] ?? 0;

		if ($id <= 0 || $userId <= 0) {
			echo json_encode(['status' => false, 'message' => 'Datos inválidos para asignación']);
			exit;
		}

		$update = $this->model->updateAsignacion($id, $userId);
		if ($update) {
			echo json_encode(['status' => true, 'message' => 'Transacción asignada correctamente']);
		} else {
			echo json_encode(['status' => false, 'message' => 'No se pudo asignar la transacción']);
		}
		exit;
	}
	
	//MOSTRAR VISTA PARA SUBIR UN MOVIMIENTO
	/**
	 * Vista para subir un movimiento manualmente
	 */
	public function newTransaction()
	{	
		$data['page_functions_js'] = "functions_transaction.js";
		$data['years'] = range(2022, 2025);
		$data['months'] = [
			'Enero', 'Febrero', 'Marzo', 'Abril',
			'Mayo', 'Junio', 'Julio', 'Agosto',
			'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'
		];

		$data['currentYear'] = date('Y');
    	$data['currentMonth'] = date('n');

		$data['bank'] = $this->model->getBank($_SESSION['userData']['id_enterprise']);

		$this->views->getView($this,"new", $data);
	}
	
	//INSERTO LOS MOVIMIENTOS QUE OBTENGO DEL ARCHIVO SUBIDO POR EL CLIENTE
	/**
	 * Inserta movimientos obtenidos de archivo subido (PDF, XLS/XLSX, TXT)
	 * Method: POST (multipart/form-data)
	 */
	public function setTransaction()
	{	
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			$arrResponse = array('status' => false, 'msg' => 'Método no permitido.' );
		}

		$anio = $_POST['anio'] ?? null;
		$mes = $_POST['mes'] ?? null;
		$banco = $_POST['banco'] ?? null;
		$archivo = $_FILES['archive'] ?? null;
		// Validar archivo
		
		if ($archivo && $archivo['error'] === UPLOAD_ERR_OK) {
			// Validación adicional de tamaño (<= 10MB) y tipo MIME básico
			$maxSize = 10 * 1024 * 1024; // 10MB
			if ($archivo['size'] > $maxSize) {
				echo json_encode(['status' => false, 'msg' => 'Archivo demasiado grande (máx 10MB).'], JSON_UNESCAPED_UNICODE);
				die();
			}
			$mime = function_exists('mime_content_type') ? @mime_content_type($archivo['tmp_name']) : '';
			// Nota: validación ligera, no exhaustiva. Se mantiene la validación por extensión más abajo.
			$tmpName = $archivo['tmp_name'];
			$fileName = uniqid('F') . '' . basename($archivo['name']);
			$uploadDir = dirname(__DIR__, 1) . '/';
			$uploadPath = $uploadDir . $fileName;
			// Crear carpeta si no existe
			if (!file_exists($uploadDir)) {
				mkdir($uploadDir, 0777, true);
			}
			
			$fileExt = strtolower(pathinfo($archivo['name'], PATHINFO_EXTENSION));
			
			if (!in_array($fileExt, ['pdf', 'xls', 'xlsx', 'txt'])) {
				$arrResponse = array('status' => false, 'msg' => 'Formato de archivo no soportado.' );
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				die();
			}

			if ($fileExt === 'pdf') {
		
				if (move_uploaded_file($tmpName, $uploadPath)) {

					// Separar banco ID y prefijo
					$bancoParts = explode('.', $banco);
					$bancoId = $bancoParts[0] ?? null;
					$bancoPrefijo = $bancoParts[1] ?? null;

					// Seleccionar función por banco
					$movimientosFormat = null;

					$fileUrl = base_url() .'/'. $fileName;
					// API Key ahora se obtiene desde Config/Config.php (constante PDFCO_API_KEY)
					$apiKey = $this->getPdfcoApiKey();
					if ($apiKey === '') {
						// Limpia archivo temporal y retorna error claro si falta configuración
						unlink($uploadPath);
						echo json_encode([
							'status' => false,
							'msg' => 'Falta configurar la API Key (PDFCO_API_KEY) en Config/Config.php.'
						], JSON_UNESCAPED_UNICODE);
						die();
					}

					switch ($bancoPrefijo) {
						case 'BCM':
							$movimientosFormat = $this->bancoBancamiga($fileUrl, $apiKey, '30761');
							break;
						case 'BCT':
							$movimientosFormat = $this->bancoBicentenario($fileUrl, $apiKey, '30765');
							break;
					}

					// Validación de estructura antes de acceder a ['mov']
					if (!is_array($movimientosFormat) || !isset($movimientosFormat['mov']) || !is_array($movimientosFormat['mov'])) {
						// Eliminar archivo temporal y responder error claro
						unlink($uploadPath);
						echo json_encode([
							'status' => false,
							'msg' => 'Banco/prefijo no soportado o no se obtuvieron movimientos del archivo (PDF). Prefijo: ' . (string)$bancoPrefijo
						], JSON_UNESCAPED_UNICODE);
						die();
					}

					$inserted = $this->model->insertTransaction($anio, $mes, $bancoId, $movimientosFormat['mov']);

					// Eliminar archivo temporal
					unlink($uploadPath);

					if($inserted['status']){
						if($inserted['error'] == 1){
							$arrResponse = array('status' => false, 'msg' => 'Hubo un problema y no se subieron movimientos.' );
						}else{
							$arrResponse = array('status' => true, 'msg' => 'Se procesaron '.$inserted['total_processed'].' movimientos, Se insertaron '.$inserted['inserted'].' y se omitieron '.$inserted['duplicates_skipped'].'.');
						}
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Hubo un problema y no se subieron movimientos.' );
					}

					echo json_encode($arrResponse, JSON_UNESCAPED_UNICODE);
					die();
					
				} else {
					echo json_encode([
						'success' => false,
						'msg' => 'No se pudo guardar el archivo en el servidor.'
					]);
					die();
				}
				
			} else if($fileExt === 'txt'){
				
				if (!move_uploaded_file($tmpName, $uploadPath)) {
					$arrResponse = array('status' => true, 'msg' => 'No se pudo guardar el archivo en el servidor.' );
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
					die();
				}
				
				$bancoParts = explode('.', $banco);
				$bancoId = $bancoParts[0] ?? null;
				$bancoPrefijo = $bancoParts[1] ?? null;
								
				$movimientosFormat = null;
				switch ($bancoPrefijo) {
					case 'MRC': $movimientosFormat = $this->procesarTxtMercantil($uploadPath); break;
					case 'SFT': $movimientosFormat = $this->procesarTxtSofitasa($uploadPath); break;
				}

					// Validación de estructura antes de acceder a ['mov']
					if (!is_array($movimientosFormat) || !isset($movimientosFormat['mov']) || !is_array($movimientosFormat['mov'])) {
						// Eliminar archivo temporal y responder error claro
						unlink($uploadPath);
						echo json_encode([
							'status' => false,
							'msg' => 'Banco/prefijo no soportado o no se obtuvieron movimientos del archivo (TXT). Prefijo: ' . (string)$bancoPrefijo
						], JSON_UNESCAPED_UNICODE);
						die();
					}

					$inserted = $this->model->insertTransaction($anio, $mes, $bancoId, $movimientosFormat['mov']);
					// Eliminar archivo temporal
					unlink($uploadPath);

					if($inserted['status']){
						if($inserted['error'] == 1){
							$arrResponse = array('status' => false, 'msg' => 'Hubo un problema y no se subieron movimientos.' );
						}else{
							$arrResponse = array('status' => true, 'msg' => 'Se procesaron '.$inserted['total_processed'].' movimientos, Se insertaron '.$inserted['inserted'].' y se omitieron '.$inserted['duplicates_skipped'].'.');
						}
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Hubo un problema y no se subieron movimientos.' );
					}
				
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
					die();

			}else {
				//CONDICION IF DEL EXCEL (INICIO)
				if (!move_uploaded_file($tmpName, $uploadPath)) {
					$arrResponse = array('status' => true, 'msg' => 'No se pudo guardar el archivo en el servidor.' );
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
					die();
				}
							
	
				$bancoParts = explode('.', $banco);
				$bancoId = $bancoParts[0] ?? null;
				$bancoPrefijo = $bancoParts[1] ?? null;
				
				$movimientosFormat = [];
				switch ($bancoPrefijo) {
					case 'SFT': $movimientosFormat = $this->procesarExcelSofitasa($uploadPath); break;
					case 'BCM': $movimientosFormat = $this->procesarExcelBancamiga($uploadPath); break;
					case 'VNZ': $movimientosFormat = $this->procesarExcelVenezuela($uploadPath); break;
					case 'MRC': $movimientosFormat = $this->procesarExcelMercantil($uploadPath); break;
					case 'BCO': $movimientosFormat = $this->procesarExcelBanesco($uploadPath); break;
					case 'BPL': $movimientosFormat = $this->procesarExcelBanplus($uploadPath); break;
					case 'BNC': $movimientosFormat = $this->procesarExcelBnc($uploadPath); break;
					case 'PLZ': $movimientosFormat = $this->procesarExcelPlaza($uploadPath); break;
					case 'ACT': $movimientosFormat = $this->procesarExcelActivo($uploadPath); break;
					case 'TSR': $movimientosFormat = $this->procesarExcelTesoro($uploadPath); break;
					case 'PRV': $movimientosFormat = $this->procesarExcelProvincial($uploadPath); break;
				}
					// Validación de estructura antes de acceder a ['mov']
					if (!is_array($movimientosFormat) || !isset($movimientosFormat['mov']) || !is_array($movimientosFormat['mov'])) {
						// Eliminar archivo temporal y responder error claro
						unlink($uploadPath);
						echo json_encode([
							'status' => false,
							'msg' => 'Banco/prefijo no soportado o no se obtuvieron movimientos del archivo (Excel). Prefijo: ' . (string)$bancoPrefijo
						], JSON_UNESCAPED_UNICODE);
						die();
					}

					$inserted = $this->model->insertTransaction($anio, $mes, $bancoId, $movimientosFormat['mov']);
					
					// Eliminar archivo temporal
					unlink($uploadPath);
					
					if($inserted['status']){
						if($inserted['error'] == 1){
							$arrResponse = array('status' => false, 'msg' => 'Hubo un problema y no se subieron movimientos.' );
						}else{
							$arrResponse = array('status' => true, 'msg' => 'Se procesaron '.$inserted['total_processed'].' movimientos, Se insertaron '.$inserted['inserted'].' y se omitieron '.$inserted['duplicates_skipped'].'.');
						}
					}else{
						$arrResponse = array('status' => false, 'msg' => 'Hubo un problema y no se subieron movimientos.' );
					}
				
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
					die();
			}
		} else {
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo inválido o no enviado.'
			]);
			die();
		}
	}
	
	//CHEQUEO EL STATUS DE PETICION CUANDO EL ARCHIVO ES UN PDF
	/**
	 * Consulta estado de job en PDF.co
	 */
	private function CheckJobStatus($jobId, $apiKey)
	{
		// Create URL
		$url = "https://api.pdf.co/v1/job/check";

		$parameters = ["jobid" => $jobId];
		$data = json_encode($parameters);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HTTPHEADER, ["x-api-key: " . $apiKey, "Content-type: application/json"]);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($curl);
		$errno = curl_errno($curl);
		$error = curl_error($curl);
		$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($errno) {
			return ["status" => "error", "message" => $error];
		}

		if ($status_code == 200) {
			$json = json_decode($result, true);
			if (!isset($json["error"]) || $json["error"] == false) {
				// Retorna la respuesta completa si está bien
				return $json;
			} else {
				return ["status" => "error", "message" => $json["message"]];
			}
		} else {
			return ["status" => "error", "message" => $result];
		}
	}

	//ACTUALIZO LOS DATOS DE LOS MOVIMIENTOS PERMITIDOS EN MI DATATABLE
	/**
	 * Actualiza campos permitidos de un movimiento
	 * Method: POST (JSON)
	 * Campos permitidos: reference, date, amount
	 */
	public function updateField()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo json_encode(['status' => false, 'message' => 'Método no permitido']);
			return;
		}

		$json = json_decode(file_get_contents("php://input"), true);
		$id = $json['id'] ?? null;
		$field = $json['field'] ?? null;
		$value = $json['value'] ?? null;

		// Validar campo permitido
		$allowedFields = ['reference', 'date', 'amount'];
		if (!in_array($field, $allowedFields)) {
			echo json_encode(['status' => false, 'message' => 'Campo no permitido']);
			return;
		}

		// Validar existencia y tipo
		if (!$id || !$field || $value === null) {
			echo json_encode(['status' => false, 'message' => 'Datos incompletos']);
			return;
		}

		// Normalización y validación por tipo de campo
		switch ($field) {
			case 'amount':
				$value = $this->parseEuropeanNumber($value);
				break;
			case 'date':
				$dt = DateTime::createFromFormat('Y-m-d', (string)$value);
				if (!$dt) {
					echo json_encode(['status' => false, 'message' => 'Formato de fecha inválido (Y-m-d)']);
					return;
				}
				$value = $dt->format('Y-m-d');
				break;
			case 'reference':
				$value = trim((string)$value);
				break;
		}

		// Ejemplo de actualización
		$update = $this->model->updateFieldById($id, $field, $value);
		if ($update) {
			echo json_encode(['status' => true, 'message' => 'Campo actualizado correctamente']);
		} else {
			echo json_encode(['status' => false, 'message' => 'No se pudo actualizar']);
		}
	}

	//OBTENER LISTADO DE CLIENTES EN DATATABLE
	/**
	 * Listado de clientes para autocompletar DataTable
	 * GET: search
	 */
	public function listado()
	{
		$search = $_GET['search'] ?? '';
		$clientes = $this->model->buscarClientes($search);
		echo json_encode($clientes);
	}

	//PROCESO QUE FORMATEA EL PRECIO DE LOS MOVIMIENTOS
	private function parseEuropeanNumber($number) {
		$number = trim((string) $number);

		// Si contiene ambos símbolos: coma y punto
		if (strpos($number, ',') !== false && strpos($number, '.') !== false) {
			// Verificamos cuál está más cerca del final (probable decimal)
			if (strrpos($number, ',') > strrpos($number, '.')) {
				// Formato europeo: 1.234,56
				$number = str_replace('.', '', $number);  // quita miles
				$number = str_replace(',', '.', $number); // cambia decimal
			} else {
				// Formato mal exportado como 4,810.53 → debemos quitar la coma
				$number = str_replace(',', '', $number);  // quita miles
				// el punto decimal queda
			}
		}
		// Solo coma: formato europeo simple
		elseif (strpos($number, ',') !== false) {
			$number = str_replace(',', '.', $number);
		}
		// Solo punto: ya está bien

		return floatval($number);
	}

	/**
	 * Obtiene la API Key de PDF.co desde Config/Config.php (constante PDFCO_API_KEY).
	 * - Mantiene compatibilidad sin cambiar rutas ni lógica de negocio.
	 * - Si no está definida, retorna cadena vacía para manejo controlado.
	 */
	private function getPdfcoApiKey(): string
	{
		if (defined('PDFCO_API_KEY') && PDFCO_API_KEY !== '') {
			return PDFCO_API_KEY;
		}
		$cfg = dirname(__DIR__) . '/Config/Config.php';
		if (file_exists($cfg)) {
			@include_once $cfg;
			if (defined('PDFCO_API_KEY') && PDFCO_API_KEY !== '') {
				return PDFCO_API_KEY;
			}
		}
		return '';
	}
	
	//PROCESAR PETICION PARA PDF.CO CON PLANTILLA

	/**
	 * Realiza petición a PDF.co usando templateId y retorna rows parseados o error
	 */
	private function requestPDFTemplate($fileUrl, $apiKey, $templateId)
	{
		// Create URL
		$url = "https://api.pdf.co/v1/pdf/documentparser";

		$parameters = [
			"url" => $fileUrl,
			"outputFormat" => "JSON",
			"templateId" => $templateId,
			"async" => false,
			"inline" => "true",
			"password" => "",
			"profiles" => ""
		];


		$data = json_encode($parameters);

		$curl = curl_init();
		curl_setopt($curl, CURLOPT_HTTPHEADER, ["x-api-key: " . $apiKey, "Content-type: application/json"]);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

		$result = curl_exec($curl);
		$errno = curl_errno($curl);
		$error = curl_error($curl);
		$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);
		curl_close($curl);

		if ($errno) {
			return ["status" => "error", "message" => $error];
		}

		if ($status_code == 200) {
			$json = json_decode($result, true);
			if (!isset($json["error"]) || $json["error"] == false) {
				// Retorna la respuesta completa si está bien
				$rows = $json["body"]["objects"][0]["rows"] ?? [];
				return $rows;
			} else {
				return ["status" => "error", "message" => $json["message"]];
			}
		} else {
			return ["status" => "error", "message" => $result];
		}
	}
	
	//------ PROCESO ARCHIVOS EN PDF -------//

	//PROCESO DE BANCO SOFITASA (PDF)
	private function bancoSofitasa($data)
	{
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['transactions'];
		$movimientos_transformados = [];
		$totalMovimientos = 0;
		
		foreach ($movimientos as $key => $item) {
			// Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
			$fecha = DateTime::createFromFormat('d/m/Y', $item['date'])->format('Y-m-d');
			// Limpiar y convertir a número float para poder comparar correctamente
			$debit = $this->parseEuropeanNumber($item['debit']);
			$credit = $this->parseEuropeanNumber($item['credit']);

			// Determinar el monto correcto
			if ($credit == 0.00) {
				$monto = $debit;
			} else {
				$monto = $credit;
			}

			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['reference'],
				'monto'      => $monto, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}

		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}
	
	//PROCESO DE BANCO BICENTENARIO (PDF)
	private function bancoBicentenario($fileUrl, $apiKey, $templateId)
	{

		// Puedes hacer un print_r si estás debuggeando:
		$totalMovimientos = 0;

		$movimientos = $this->requestPDFTemplate($fileUrl, $apiKey, $templateId);
		$movimientos_transformados = [];
		$totalMovimientos = 0;

		

		foreach ($movimientos as $item) {
			// Validar que tenga los campos necesarios
			if (empty($item['fecha']['value']) || empty($item['referencia']['value'])) {
				continue; // Saltar si la fila no tiene datos clave
			}

			// Convertir fecha de DD-MM-YYYY a YYYY-MM-DD
			$fechaObj = DateTime::createFromFormat('d-m-Y', $item['fecha']['value']);
			$fecha = $fechaObj ? $fechaObj->format('Y-m-d') : null;

			if (!$fechaObj) continue;


			// Parsear montos europeos
			$debit  = isset($item['débito']['value']) ? $this->parseEuropeanNumber($item['débito']['value']) : 0.0;
			$credit = isset($item['crédito']['value']) ? $this->parseEuropeanNumber($item['crédito']['value']) : 0.0;

			// Determinar monto (negativo si es débito)
			$monto = $credit != 0.0 ? $credit : -$debit;

			// Agregar movimiento transformado
			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => trim($item['referencia']['value']),
				'monto'      => $monto
			];

			$totalMovimientos++;
		}

	

		return [
			'total' => $totalMovimientos,
			'mov' => $movimientos_transformados
		];
	}
	
	//PROCESO DE BANCO TESORO (PDF)
	private function bancoTesoro($data)
	{	
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['transactions'];
		$movimientos_transformados = [];
		$totalMovimientos = 0;
		
		foreach ($movimientos as $key => $item) {
			
			// Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
			$fecha = DateTime::createFromFormat('d/m/Y', $item['date'])->format('Y-m-d');
			// Limpiar y convertir a número float para poder comparar correctamente
			$debit = $this->parseEuropeanNumber($item['debit']);
			$credit = $this->parseEuropeanNumber($item['credit']);

			// Determinar el monto correcto
			if ($credit == 0.00) {
				$monto = '-'.$debit;
			} else {
				$monto = $credit;
			}

			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['reference'],
				'monto'      => $monto, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}
		
		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}
	
	//PROCESO DE BANCO BANCAMIGA (PDF)
	private function bancoBancamiga($fileUrl, $apiKey, $templateId)
	{

		// Puedes hacer un print_r si estás debuggeando:
		$totalMovimientos = 0;


		$movimientos = $this->requestPDFTemplate($fileUrl, $apiKey, $templateId);
		$movimientos_transformados = [];
		$totalMovimientos = 0;

		foreach ($movimientos as $item) {
			// Validar que tenga los campos necesarios
			if (empty($item['fecha']['value']) || empty($item['referencia']['value'])) {
				continue; // Saltar si la fila no tiene datos clave
			}

			// Convertir fecha de DD-MM-YYYY a YYYY-MM-DD
			$fechaObj = DateTime::createFromFormat('d-m-Y', $item['fecha']['value']);
			$fecha = $fechaObj ? $fechaObj->format('Y-m-d') : null;

			// Parsear montos europeos
			$debit  = isset($item['débito']['value']) ? $this->parseEuropeanNumber($item['débito']['value']) : 0.0;
			$credit = isset($item['crédito']['value']) ? $this->parseEuropeanNumber($item['crédito']['value']) : 0.0;

			// Determinar monto (negativo si es débito)
			$monto = $credit != 0.0 ? $credit : -$debit;

			// Agregar movimiento transformado
			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => trim($item['referencia']['value']),
				'monto'      => $monto
			];

			$totalMovimientos++;
		}

		return [
			'total' => $totalMovimientos,
			'mov' => $movimientos_transformados
		];
	}
	
	//PROCESO DE BANCO BANESCO (PDF)
	private function bancoBanesco($anio, $mes, $data)
	{	
		
		echo json_encode([
			'status' => false,
			'msg' => 'Formato(PDF) - Banco Banesco, desabilitado temporalmente.'
		]);
		die();
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['detalle_de_movimientos'];
		$movimientos_transformados = [];
		$totalMovimientos = 0;
		
		foreach ($movimientos as $key => $item) {
			
			// Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
			$fechaStr = sprintf('%04d-%02d-%02d', $anio, $mes, $item['dia']);
			$fecha = (new DateTime($fechaStr))->format('Y-m-d');
			
			if($item['cargos'] == ''){
				$abonos = $this->parseEuropeanNumber($item['abonos']);
				$monto = $abonos;
			}else{
				$cargos = $this->parseEuropeanNumber($item['cargos']);
				$monto = '-'.$cargos;
			}
			
			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['ref.'],
				'monto'      => $monto, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}
		
		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}
	
	//PROCESO DE BANCO VENEZUELA (PDF)
	private function bancoVenezuela($data)
	{	
		
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['transactions'];
		$movimientos_transformados = [];
		$totalMovimientos = 0;
		
		foreach ($movimientos as $key => $item) {
			
			if($item['Descripción'] == 'SALDO INICIAL'){
				continue;
			}
			// Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
			$fecha = DateTime::createFromFormat('d/m/Y', $item['Fecha'])->format('Y-m-d');
			// Limpiar y convertir a número float para poder comparar correctamente
			$debit = $this->parseEuropeanNumber($item['Débito']);
			$credit = $this->parseEuropeanNumber($item['Crédito']);
			
			// Determinar el monto correcto
			if ($credit == 0.00) {
				$monto = $debit;
			} else {
				$monto = $credit;
			}
			
			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['Referencia'],
				'monto'      => $monto, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}
		
		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}
	
	//PROCESO DE BANCO MERCANTIL (PDF)
	private function bancoMercantil($anio, $data)
	{	
		if (array_key_exists('header', $data)) {
			
			echo json_encode([
				'status' => false,
				'msg' => 'Formato(PDF) - Banco Mercantil, desabilitado temporalmente.'
			]);
			die();
			
			$result = $this->movMercantil1($data);
			return $result;
		} else if(array_key_exists('account_details', $data)){
			$result = $this->movMercantil2($data);
			return $result;
		} else {
			$result = $this->movMercantil3($anio, $data);
			return $result;
		}
		
	}
	
	//PROCESO DE BANCO MERCANTIL CASO 1 (PDF)
	private function movMercantil1($data)
	{	
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['transactions'];
		$movimientos_transformados = [];
		$totalMovimientos= 0;
		
		foreach ($movimientos as $key => $item) {
			
			// Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
			$fecha = DateTime::createFromFormat('d/m/Y', $item['date'])->format('Y-m-d');
			
			if($item['debits'] == ''){
				$credits = $this->parseEuropeanNumber($item['credits']);
				$monto = $credits;
			}else{
				$debits = $this->parseEuropeanNumber($item['debits']);
				$monto = '-'.$debits;
			}
			
			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['transaction_number'],
				'monto'      => $monto, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}
		
		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}
	
	//PROCESO DE BANCO MERCANTIL CASO 2 (PDF)
	private function movMercantil2($data)
	{	
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['transactions'];
		$movimientos_transformados = [];
		$totalMovimientos = 0;
		
		foreach ($movimientos as $key => $item) {
			
			// Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
			$fecha = DateTime::createFromFormat('d/m/Y', $item['date'])->format('Y-m-d');

			$amount = $this->parseEuropeanNumber($item['amount']);
			$monto = $amount;
			
			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['reference_number'],
				'monto'      => $monto, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}
		
		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}
	
	//PROCESO DE BANCO MERCANTIL CASO 3 (PDF)
	private function movMercantil3($anio, $data)
	{	
		// Puedes hacer un print_r si estás debuggeando:
		$movDebit1 = $data['operations_debited'];
		$movDebit2 = $data['other_debits'];
		$movCredit = $data['other_credits'];
		$movimientos_transformados = [];
		$totalMovimientos = 0;
		
		foreach ($movDebit1 as $key => $item) {
			
			// Convertir '13/03' en día y mes
			list($dia, $mes) = explode('/', $item['date']);

			// Armar fecha y convertirla a formato 'Y-m-d'
			$fechaStr = sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
			$fecha = (new DateTime($fechaStr))->format('Y-m-d');
			
			// Limpiar y convertir a número float para poder comparar correctamente
			$debit = $this->parseEuropeanNumber($item['amount']);
			
			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['number'],
				'monto'      => '-'.$debit, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}
		
		foreach ($movDebit2 as $key => $item) {
			
			// Convertir '13/03' en día y mes
			list($dia, $mes) = explode('/', $item['date']);

			// Armar fecha y convertirla a formato 'Y-m-d'
			$fechaStr = sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
			$fecha = (new DateTime($fechaStr))->format('Y-m-d');
			
			// Limpiar y convertir a número float para poder comparar correctamente
			$debit = $this->parseEuropeanNumber($item['amount']);
			
			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['number'],
				'monto'      => '-'.$debit, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}
		
		foreach ($movCredit as $key => $item) {
			
			// Convertir '13/03' en día y mes
			list($dia, $mes) = explode('/', $item['date']);

			// Armar fecha y convertirla a formato 'Y-m-d'
			$fechaStr = sprintf('%04d-%02d-%02d', $anio, $mes, $dia);
			$fecha = (new DateTime($fechaStr))->format('Y-m-d');
			
			// Limpiar y convertir a número float para poder comparar correctamente
			$credit = $this->parseEuropeanNumber($item['amount']);
			
			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['number'],
				'monto'      => $credit, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}

		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}
	
	//PROCESO DE BANCO BNC (PDF)
	private function bancoBnc($data)
	{
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['transactions'];
		$movimientos_transformados = [];
		$totalMovimientos = 0;
		
		foreach ($movimientos as $key => $item) {
			// Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
			$fecha = DateTime::createFromFormat('d/m/Y', $item['date'])->format('Y-m-d');
			// Limpiar y convertir a número float para poder comparar correctamente
			$debit = $this->parseEuropeanNumber($item['debit']);
			$credit = $this->parseEuropeanNumber($item['credit']);

			// Determinar el monto correcto
			if ($credit == 0.00) {
				$monto = $debit;
			} else {
				$monto = $credit;
			}

			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['reference'],
				'monto'      => $monto, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}
	
		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}
	
	//PROCESO DE BANCO PROVINCIAL (PDF)
	private function bancoProvincial($data)
	{
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['account_statement']['transactions'];
		$movimientos_transformados = [];
		$totalMovimientos = 0;
		
		foreach ($movimientos as $key => $item) {
			// Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
			$fecha = DateTime::createFromFormat('d-m-Y', $item['operation_date'])->format('Y-m-d');
			// Limpiar y convertir a número float para poder comparar correctamente
			$debit = $this->parseEuropeanNumber($item['charges']);
			$credit = $this->parseEuropeanNumber($item['credits']);
			
			// Determinar el monto correcto
			if (empty($credit)) {
				$monto = '-'.$debit;
			} else {
				$monto = $credit;
			}

			$movimientos_transformados[] = [
				'fecha'      => $fecha,
				'referencia' => $item['reference'],
				'monto'      => $monto, // O 'credit' si prefieres según la lógica
			];
			
			$totalMovimientos++;
		}

		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}	
	

	//------ PROCESO ARCHIVOS EN EXCEL -------//

	//PROCESO DE BANCO VENEZUELA (EXCEL)
	private function procesarExcelVenezuela($filePath)
	{
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			foreach ($rows as $fila) {
				if (count($fila) > 7) {
					$result = $this->procesarExcelVenezuela2($filePath);
					return $result;
				}else{
					$result = $this->procesarExcelVenezuela1($filePath);
					return $result;
				}
			}

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	private function procesarExcelVenezuela1($filePath){
		
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			$movimientos_transformados = [];
			$totalMovimientos = 0;
			
			// Asume que la primera fila son los encabezados
			for ($i = 1; $i < count($rows); $i++) {
				$fila = $rows[$i];


				if ($fila[2] == 'SALDO INICIAL') {
					continue;
				}
		
				$fecha = DateTime::createFromFormat('d/m/Y', $fila[0])->format('Y-m-d');

				$amount = $this->parseEuropeanNumber($fila[4]);

				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $fila[1],  // Ej: "123456"
					'monto'      => $amount,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}
			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	private function procesarExcelVenezuela2($filePath){
		
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			$movimientos_transformados = [];
			$totalMovimientos = 0;
			
			// Asume que la primera fila son los encabezados
			for ($i = 1; $i < count($rows); $i++) {
				$fila = $rows[$i];


				if ($fila[2] == 'SALDO INICIAL') {
					continue;
				}
				
		
				$fecha = DateTime::createFromFormat('d-m-Y H:i', $fila[0])->format('Y-m-d');

				if ($fila[7] == 'Nota de Crédito') {
					$monto = $this->parseEuropeanNumber($fila[4]);
				} else {
					$monto = $this->parseEuropeanNumber($fila[3]);
				}

				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $fila[1],  // Ej: "123456"
					'monto'      => $monto,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}
			
			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO SOFITASA (EXCEL)
	private function procesarExcelSofitasa($filePath)
	{	
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			$movimientos_transformados = [];
			$totalMovimientos = 0;
			
			// Asume que la primera fila son los encabezados
			for ($i = 16; $i < count($rows); $i++) {
				$fila = $rows[$i];
				
				if ($fila[1] == 'Totales') {
					break; // Termina el ciclo si la fecha está vacía
				}

				$fecha = DateTime::createFromFormat('d/m/Y', $fila[1])->format('Y-m-d');

				$debit = $this->parseEuropeanNumber($fila[13]);
				$credit = $this->parseEuropeanNumber($fila[15]);

				// Determinar el monto correcto
				if ($credit == 0) {
					$monto = $debit;
				} else {
					$monto = $credit;
				}

				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $fila[12],  // Ej: "123456"
					'monto'      => $monto,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO MERCANTIL (EXCEL)
	private function procesarExcelMercantil($filePath)
	{	
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			foreach ($rows as $fila) {
				if (count($fila) > 20) {
					$result = $this->processMercantilExcel1($rows);
					return $result;
				}else if(count($fila) == 6){
					$result = $this->processMercantilExcel3($rows);
					return $result;
				}else{
					$result = $this->processMercantilExcel2($rows);
					return $result;
				}
			}

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO MERCANTIL CASO 1 (EXCEL)
	private function processMercantilExcel1($rows)
	{	
		$movimientos_transformados = [];
		$totalMovimientos = 0;	
		// Asume que la primera fila son los encabezados
		for ($i = 10; $i < count($rows); $i++) {
			$fila = $rows[$i];
			
			if ($fila[3] == 'SALDO INICIAL') {
				break; // Termina el ciclo si la fecha está vacía
			}

			$fecha = DateTime::createFromFormat('d/m/Y', $fila[1])->format('Y-m-d');

			$amount = $this->parseEuropeanNumber($fila[4]);

			$movimientos_transformados[] = [
				'fecha'      => $fecha,  // Ej: "2024-01-01"
				'referencia' => $fila[2],  // Ej: "123456"
				'monto'      => $amount,  // Ej: "100.00"
			];
			
			$totalMovimientos++;
		}

		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}

	//PROCESO DE BANCO MERCANTIL 2 (EXCEL)
	private function processMercantilExcel2($rows)
	{	
		$movimientos_transformados = [];
		$totalMovimientos = 0;
		
		// Asume que la primera fila son los encabezados
		for ($i = 7; $i < count($rows); $i++) {
			$fila = $rows[$i];
			
			if ($fila[2] == 'SALDO INICIAL') {
				break; // Termina el ciclo si la fecha está vacía
			}

			$fecha = DateTime::createFromFormat('d/m/Y', $fila[0])->format('Y-m-d');

			$amount = $this->parseEuropeanNumber($fila[3]);

			$movimientos_transformados[] = [
				'fecha'      => $fecha,  // Ej: "2024-01-01"
				'referencia' => $fila[1],  // Ej: "123456"
				'monto'      => $amount,  // Ej: "100.00"
			];
			
			$totalMovimientos++;
		}

		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}

	//PROCESO DE BANCO MERCANTIL CASO 3 (EXCEL)
	private function processMercantilExcel3($rows)
	{	

		$movimientos_transformados = [];
		$totalMovimientos = 0;	
		// Asume que la primera fila son los encabezados
		for ($i = 1; $i < count($rows); $i++) {
			$fila = $rows[$i];

			if($fila[0] == ' '){
				continue;
			}else{
				$fecha = DateTime::createFromFormat('m/d/Y', $fila[0])->format('Y-m-d');
			}

			$debit = $this->parseEuropeanNumber($fila[3]);
			$credit = $this->parseEuropeanNumber($fila[4]);

			if ($credit == 0) {
				$monto = '-'.$debit;
			} else {
				$monto = $credit;
			}

			$movimientos_transformados[] = [
				'fecha'      => $fecha,  // Ej: "2024-01-01"
				'referencia' => $fila[1],  // Ej: "123456"
				'monto'      => $monto,  // Ej: "100.00"
			];
			
			$totalMovimientos++;
		}

		return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];
	}

	//PROCESO DE BANCO BANCAMIGA (EXCEL)
	private function procesarExcelBancamiga($filePath)
	{	
		
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			
			$movimientos_transformados = [];
			$totalMovimientos = 0;
			
			// Asume que la primera fila son los encabezados
			for ($i = 6; $i < count($rows); $i++) {
				$fila = $rows[$i];
				if (empty($fila[1])) {
					break; // Termina el ciclo si la fecha está vacía
				}

				$fecha = DateTime::createFromFormat('d/m/y', $fila[1])->format('Y-m-d');

				$debit = $this->parseEuropeanNumber($fila[4]);
				$credit = $this->parseEuropeanNumber($fila[5]);

				// Determinar el monto correcto
				if ($credit == 0) {
					$monto = '-'.$debit;
				} else {
					$monto = $credit;
				}

				$referencia = ltrim($fila[2], "'");

				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $referencia,  // Ej: "123456"
					'monto'      => $monto,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}
	
	//PROCESO DE BANCO BANESCO (EXCEL)
	private function procesarExcelBanesco($filePath)
	{	
		
		try {
	
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			$movimientos_transformados = [];
			$totalMovimientos = 0;

			// Asume que la primera fila son los encabezados
			for ($i = 1; $i < count($rows); $i++) {
				$fila = $rows[$i];
				
				if($fila[0] == 'Fecha'){
					continue;
				}else{
					$fecha = DateTime::createFromFormat('Y/m/d', $fila[0])->format('Y-m-d');
				}

				$amount = $this->parseEuropeanNumber($fila[3]);

				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $fila[1],  // Ej: "123456"
					'monto'      => $amount,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO BANPLUS (EXCEL)
	private function procesarExcelBanplus($filePath)
	{	
		
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();
			
			$movimientos_transformados = [];
			$totalMovimientos = 0;
			
			// Asume que la primera fila son los encabezados
			for ($i = 1; $i < count($rows); $i++) {
				$fila = $rows[$i];
				if ($fila[2] == 'Saldo Total') {
					continue; 
				}
				if ($fila[2] == 'Saldo Inicial') {
					continue; 
				}
				//$fecha = DateTime::createFromFormat('d/m/Y', $fila[0])->format('Y-m-d');
				$fecha = DateTime::createFromFormat('d/m/Y', $fila[0])->format('Y-m-d');
				
				
				$debit = $this->parseEuropeanNumber($fila[3]);
				$credit = $this->parseEuropeanNumber($fila[4]);

				// Determinar el monto correcto
				if ($credit == 0) {
					$monto = '-'.$debit;
				} else {
					$monto = $credit;
				}

				$referencia = ltrim($fila[1], "'");

				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $referencia,  // Ej: "123456"
					'monto'      => $monto,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO BNC (EXCEL)
	private function procesarExcelBnc($filePath)
	{	
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();
			
			$movimientos_transformados = [];
			$totalMovimientos = 0;
			
			// Asume que la primera fila son los encabezados
			for ($i = 16; $i < count($rows); $i++) {
				$fila = $rows[$i];
				if ($fila[1] == 'Totales') {
					continue; 
				}
				if ($fila[1] == '') {
					continue; 
				}
				//$fecha = DateTime::createFromFormat('d/m/Y', $fila[0])->format('Y-m-d');
				$fecha = DateTime::createFromFormat('d/m/Y', $fila[1])->format('Y-m-d');
				
				
				$debit = $this->parseEuropeanNumber($fila[13]);
				$credit = $this->parseEuropeanNumber($fila[15]);

				if ($credit == 0) {
					$monto = $debit;
				} else {
					$monto = $credit;
				}
		
				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $fila[12],  // Ej: "123456"
					'monto'      => $monto,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO PLAZA (EXCEL)
	private function procesarExcelPlaza($filePath)
	{	
		
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();
	
			$movimientos_transformados = [];
			$totalMovimientos = 0;

			// Asume que la primera fila son los encabezados
			for ($i = 2; $i < count($rows); $i++) {
				$fila = $rows[$i];

				//$fecha = DateTime::createFromFormat('d/m/Y', $fila[0])->format('Y-m-d');
				$fecha = DateTime::createFromFormat('d/m/Y', $fila[0])->format('Y-m-d');
				
				$debit = $this->parseEuropeanNumber($fila[3]);
				$credit = $this->parseEuropeanNumber($fila[4]);

				if ($credit == 0) {
					$monto = $debit;
				} else {
					$monto = $credit;
				}

				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $fila[1],  // Ej: "123456"
					'monto'      => $monto,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO ACTIVO (EXCEL)
	private function procesarExcelActivo($filePath)
	{	
		
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();
			
			$movimientos_transformados = [];
			$totalMovimientos = 0;

			// Asume que la primera fila son los encabezados
			for ($i = 1; $i < count($rows); $i++) {
				$fila = $rows[$i];

				$fecha = DateTime::createFromFormat('d/m/Y', $fila[0])->format('Y-m-d');
				
				$debit = $this->parseEuropeanNumber($fila[3]);
				$credit = $this->parseEuropeanNumber($fila[4]);

				if ($credit == 0) {
					$monto = '-'.$debit;
				} else {
					$monto = $credit;
				}

				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $fila[1],  // Ej: "123456"
					'monto'      => $monto,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO TESORO (EXCEL)
	private function procesarExcelTesoro($filePath)
	{	
		
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();
			
			$movimientos_transformados = [];
			$totalMovimientos = 0;

			// Asume que la primera fila son los encabezados
			for ($i = 4; $i < count($rows); $i++) {
				$fila = $rows[$i];

				$fecha = DateTime::createFromFormat('d/m/Y', $fila[1])->format('Y-m-d');
				
				$debit = $this->parseEuropeanNumber($fila[5]);
				$credit = $this->parseEuropeanNumber($fila[6]);

				if ($credit == 0) {
					$monto = '-'.$debit;
				} else {
					$monto = $credit;
				}

				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $fila[2],  // Ej: "123456"
					'monto'      => $monto,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO PROVINCIAL (EXCEL)
	private function procesarExcelProvincial($filePath)
	{	
		
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();
			
			$movimientos_transformados = [];
			$totalMovimientos = 0;

			// Asume que la primera fila son los encabezados
			for ($i = 4; $i < count($rows); $i++) {
				$fila = $rows[$i];

				$fecha = DateTime::createFromFormat('d/m/Y', $fila[1])->format('Y-m-d');
				
				$debit = $this->parseEuropeanNumber($fila[5]);
				$credit = $this->parseEuropeanNumber($fila[6]);

				if ($credit == 0) {
					$monto = '-'.$debit;
				} else {
					$monto = $credit;
				}

				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $fila[2],  // Ej: "123456"
					'monto'      => $monto,  // Ej: "100.00"
				];
				
				$totalMovimientos++;
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o absoleto.'
			]);
			die();
		}
	}

	//------ PROCESO ARCHIVOS EN TXT -------//

	//PROCESO DE BANCO MERCANTIL (TXT)
	private function procesarTxtMercantil($filePath)
	{	
		try {
			$handle = fopen($filePath, 'r');
			$linea = fgets($handle);
			fclose($handle);

			$linea = trim($linea);

			if (substr_count($linea, ',') > 3) {
				// Tipo 1: CSV
				$result = $this->procesarTxtMercantil1($filePath);
				return $result;
			} elseif (preg_match('/^(NC|ND|SF|SD)\s+\d{2}\/\d{2}\/\d{4}/', $linea)) {
				// Tipo 2: comienza con NC, ND, etc.
				$result = $this->procesarTxtMercantil2($filePath);
				return $result;
			} elseif (preg_match('/^0105\s+VES\s+\d{12}\s+\d{8}/', $linea)) {
				// Tipo 3: comienza con 0105 VES ... fecha
				$result = $this->procesarTxtMercantil3($filePath);
				return $result;
			} else {
				echo json_encode([
					'success' => false,
					'msg' => 'Formato txt no reconocido.'
				]);
				die();
			}
		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'El archivo TXT está dañado o su formato no es reconocido.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO MERCANTIL CASO 1 (TXT)
	private function procesarTxtMercantil1($filePath)
	{
		try {

			if (($handle = fopen($filePath, "r")) !== false) {
				
				$totalMovimientos = 0;
				while (($data = fgetcsv($handle, 1000, ',', '"')) !== false) {

					if (in_array($data[5], ['SI', 'SF'])) {
						continue; // Ignora este movimiento y pasa al siguiente
					}

					// Validamos que tenga al menos 10 columnas
					if (count($data) >= 8) {
						
						$fecha = DateTime::createFromFormat('dmY', $data[3])->format('Y-m-d');
						$amount = $this->parseEuropeanNumber($data[7]);

						if($data[5] == 'NC'){
							$monto = $amount;
						}else{
							$monto = '-'.$amount;
						}

						$movimientos_transformados[] = [
							'fecha'      => $fecha,
							'referencia' => $data[4],
							'monto'      => $monto
						];
						
						$totalMovimientos++;
					}
				}
				fclose($handle);
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo TXT esta dañado.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO MERCANTIL CASO 2 (TXT)
	private function procesarTxtMercantil2($filePath)
	{
		try {

			$lineas = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

			$movimientos_transformados = [];
			$totalMovimientos = 0;
			
			foreach ($lineas as $linea) {
				// Asegurarse de que tenga largo mínimo
				if (strlen(trim($linea)) < 70) continue;

				$type        = trim(substr($linea, 0, 5));

				if ($type == 'SI' || $type == 'SF') {
						continue; // Ignora este movimiento y pasa al siguiente
				}

				$fechaRaw    = trim(substr($linea, 5, 13));   // "12/06/2025"
				$referencia  = trim(substr($linea, 18, 18));  // 15-18 caracteres según el ejemplo
				$montoRaw    = trim(substr($linea, 84));      // desde el caracter 80 en adelante (ajustar si varía)
				
				$fecha = DateTime::createFromFormat('d/m/Y', $fechaRaw)->format('Y-m-d');

				$amount = $this->parseEuropeanNumber($montoRaw);

				$movimientos_transformados[] = [
					'fecha'       => $fecha,
					'referencia'  => $referencia,
					'monto'       => $amount
				];
				
				$totalMovimientos++;
			}
			
			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo TXT esta dañado'
			]);
			die();
		}
	}

	//PROCESO DE BANCO MERCANTIL CASO 3 (TXT)
	private function procesarTxtMercantil3($filePath)
	{
		try {

			$lineas = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
			$movimientos_transformados = [];
			$totalMovimientos = 0;
			
			foreach ($lineas as $linea) {
				// Dividir en tokens por uno o más espacios
				$tokens = preg_split('/\s+/', trim($linea));

				// Verifica que haya al menos 10 campos
				if (count($tokens) < 10) continue;

				// Extraer los últimos 3 valores
				$codigo   = array_pop($tokens);
				$saldoRaw = array_pop($tokens);
				$montoRaw = array_pop($tokens);

				// Convertir montos a float
				$amount = $this->parseEuropeanNumber($montoRaw);

				$tiposPermitidos = ['ND', 'NC'];
				$tipo = null;
				foreach ($tokens as $i => $val) {
					if (in_array($val, $tiposPermitidos)) {
						$tipo = $val;
						$posTipo = $i;
						break;
					}
				}

				if ($tipo === null) continue;


				// Separar los campos conocidos
				$fechaRaw   = $tokens[3] ?? '';
				$referencia = $tokens[4] ?? '';
				
				// Formatear fecha
				$fecha = DateTime::createFromFormat('dmY', $fechaRaw)->format('Y-m-d');

				// Si es ND, monto negativo
				if ($tipo === 'ND') {
					$amount *= -1;
				}

				$movimientos_transformados[] = [
					'fecha'       => $fecha,
					'referencia'  => $referencia,
					'monto'       => $amount,
				];
				
				$totalMovimientos++;
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo TXT esta dañado.'
			]);
			die();
		}
	}

	//PROCESO DE BANCO SOFITASA (TXT)
	private function procesarTxtSofitasa($filePath)
	{
		try {	
	
			$movimientos_transformados = [];
			$totalMovimientos = 0;
			$lineas = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

			foreach ($lineas as $linea) {
				// Obtener fecha desde los primeros 8 caracteres (ddmmaaaa)
				$fechaTexto = substr($linea, 0, 9); // Ejemplo: 13052025
				$fechaSinPrimerDigito = substr($fechaTexto, 1); // "19052025"

				$dia = substr($fechaSinPrimerDigito, 0, 2);
				$mes = substr($fechaSinPrimerDigito, 2, 2);
				$anio = substr($fechaSinPrimerDigito, 4, 4);

				$fecha = "$anio-$mes-$dia"; // "19-05-2025"

				$referencia = substr($linea, 10, 14);
				// Obtener el monto del movimiento
				// Buscamos el penúltimo grupo numérico con coma
				preg_match_all('/-?\d{1,},\d{2}/', $linea, $montoMatch);
				$montos = $montoMatch[0];

				if (count($montos) >= 3) {
					$montoDebitoBruto = $montos[count($montos) - 3];
       				 $montoCreditoBruto = $montos[count($montos) - 2];

					// Convertir ambos montos a número real
        			$montoDebito = $this->parseEuropeanNumber($montoDebitoBruto);
        			$montoCredito = $this->parseEuropeanNumber($montoCreditoBruto);

					if ($montoCredito == 0 && $montoDebito != 0) {
						$monto = $montoDebito;
					} else {
						$monto = $montoCredito;
					}

					$movimientos_transformados[] = [
						'fecha'       => $fecha,
						'referencia'  => $referencia,
						'monto'       => $monto,
					];
					
					$totalMovimientos++;
				}
			}

			return [
				'total' => $totalMovimientos,
				'mov' => $movimientos_transformados
				];

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo TXT esta dañado.'
			]);
			die();
		}

	}
}
?>