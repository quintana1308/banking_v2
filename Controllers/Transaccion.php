<?php 
	include dirname(__DIR__) . '/vendor/autoload.php';
	use PhpOffice\PhpSpreadsheet\Spreadsheet;
	use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
	use PhpOffice\PhpSpreadsheet\Style\Fill;
	use PhpOffice\PhpSpreadsheet\Style\Border;
	use PhpOffice\PhpSpreadsheet\Style\Alignment;
	use PhpOffice\PhpSpreadsheet\IOFactory;
	
	// Incluir el modelo de comentarios
	require_once dirname(__DIR__) . '/Models/CommentModel.php';

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
		
		// Verificar permisos de comentarios del usuario
		$commentModel = new CommentModel();
		$data['can_comment'] = $commentModel->canUserComment($_SESSION['idUser']);
		
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
			'dateFrom' => $_GET['dateFrom'] ?? '',
			'dateTo'   => $_GET['dateTo']   ?? '',
			'estado'   => $_GET['estado']    ?? '',
			'monto'    => $_GET['monto']     ?? '',
		];

		$arrData = $this->model->getTransaction($filters);
		
		// Agregar información de permisos y comentarios para cada registro
		$canDelete = canDeleteTransactions(); // Calcular una sola vez
		
		try {
			// Solo buscar comentarios si hay transacciones
			$commentsMap = [];
			if (!empty($arrData)) {
				$commentModel = new CommentModel();
				$transactionIds = array_column($arrData, 'id');
				$commentsMap = $commentModel->getMultipleTransactionComments($transactionIds);
			}
			
			// Aplicar permisos y comentarios de forma eficiente
			foreach ($arrData as &$row) {
				$row['can_delete'] = $canDelete;
				$row['has_comment'] = $commentsMap[$row['id']] ?? false;
			}
		} catch (Exception $e) {
			// Si hay error con comentarios, continuar sin ellos
			error_log("Error al obtener comentarios: " . $e->getMessage());
			foreach ($arrData as &$row) {
				$row['can_delete'] = $canDelete;
				$row['has_comment'] = false;
			}
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

			if ($token !== '' && $rif !== '' && $opcion !== '' && $desde !== '' && $hasta !== '') {
				if($opcion != 'movimientosTotal'){
					if($cuenta !== ''){
						$getTransaction = $this->model->getTransactionEndPoint($token, $rif, $bd, $cuenta, $opcion, $desde, $hasta);
					}else{
						$arrResponse = ['status' => false, 'msg' => 'Campos requeridos faltantes'];
					}
				}else{
					$cuenta = '';
					$getTransaction = $this->model->getTransactionEndPoint($token, $rif, $bd, $cuenta, $opcion, $desde, $hasta);
				}
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
			
			if (!in_array($fileExt, ['pdf', 'xls', 'xlsx', 'txt', 'csv'])) {
				$arrResponse = array('status' => false, 'msg' => 'Formato de archivo no soportado.' );
				echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
				die();
			}

			if ($fileExt === 'pdf') {

				if (!move_uploaded_file($tmpName, $uploadPath)) {
					$arrResponse = array('status' => true, 'msg' => 'No se pudo guardar el archivo en el servidor.' );
					echo json_encode($arrResponse,JSON_UNESCAPED_UNICODE);
					die();
				}

				// ============================================
				// PASO 1: INICIALIZAR LOGGING DE ARCHIVO PDF
				// ============================================
				
				// Extraer información del banco y cuenta
				$bancoParts = explode('.', $banco);
				$bancoId = $bancoParts[0] ?? null;
				$bancoPrefijo = $bancoParts[1] ?? null;
				
				// ============================================
				// VALIDAR PERMISOS DE SUBIDA PDF POR EMPRESA
				// ============================================
				
				$id_enterprise = $_SESSION['userData']['id_enterprise'];
				$empresaPermisos = $this->model->checkPdfUploadPermission($id_enterprise);

				if (!$empresaPermisos) {
					// Eliminar archivo subido ya que no tiene permisos
					if (file_exists($uploadPath)) {
						unlink($uploadPath);
					}
					
					echo json_encode([
						'status' => false, 
						'msg' => 'Su empresa no tiene permisos para subir archivos PDF. Contacte al administrador del sistema.'
					], JSON_UNESCAPED_UNICODE);
					die();
				}

				// Configurar API Key de PDF.co desde configuración
				$apiKey = $this->getPdfcoApiKey();

				// PASO 1.1: Consultar balance inicial de créditos
				$balanceInfo = $this->model->getCurrentCreditsBalance($apiKey);
				$tokensIniciales = ($balanceInfo && isset($balanceInfo['remainingCredits'])) ? $balanceInfo['remainingCredits'] : 0;

				// PASO 1.2: Crear registro inicial de log
				$logData = [
					'filename' => $archivo['name'],                          // Nombre original del archivo
					'file_size' => $archivo['size'],                        // Tamaño en bytes
					'upload_date' => date('Y-m-d'),                         // Fecha actual
					'upload_time' => date('H:i:s'),                         // Hora actual
					'id_bank' => $bancoId,                                  // ID del banco
					'bank_account' => $banco,                               // Cuenta completa "id.prefijo"
					'id_enterprise' => $_SESSION['userData']['id_enterprise'], // ID de la empresa
					'id_user' => $_SESSION['idUser'],               // ID del usuario
					'tokens_iniciales' => $tokensIniciales,                 // Créditos disponibles
					'status' => 'uploading'                                 // Estado inicial
				];

				$logId = $this->model->insertPdfUploadLog($logData);
				
				if (!$logId) {
					error_log("Error: No se pudo crear registro de log para archivo PDF");
				}

				// ============================================
				// PASO 2: PROCESAR ARCHIVO CON PDF.CO
				// ============================================
				
				// Generar URL pública del archivo subido para que PDF.co pueda accederlo
				$baseUrl = rtrim(base_url(), '/');
				$fileUrl = $baseUrl . '/' . $fileName;
				
				$url = "https://api.pdf.co/v1/ai-invoice-parser";
				$params = ["url" => $fileUrl];
				
				// Configurar cURL para envío a PDF.co
				$curl = curl_init();
				curl_setopt($curl, CURLOPT_HTTPHEADER, [
					"x-api-key: $apiKey",
					"Content-type: application/json"
				]);
				curl_setopt($curl, CURLOPT_URL, $url);
				curl_setopt($curl, CURLOPT_POST, true);
				curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
				curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($params));

				$result = curl_exec($curl);

				if (curl_errno($curl) === 0) {
					$status_code = curl_getinfo($curl, CURLINFO_HTTP_CODE);

					if ($status_code == 200) {
						$json = json_decode($result, true);

						if (!isset($json["error"]) || $json["error"] == false) {

							// ============================================
							// PASO 3: ACTUALIZAR LOG CON DATOS INICIALES
							// ============================================
							
							// Actualizar log con job_id inicial (sin tokens del response)
							if ($logId && isset($json['jobId'])) {
								$updateData = [
									'job_id' => $json['jobId'],
									'status' => 'processing'
								];
								
								// Solo agregar duración si está disponible
								if (isset($json['duration'])) {
									$updateData['processing_duration'] = $json['duration'];
								}
								
								$this->model->updatePdfUploadLog($logId, $updateData);
							}
							
							$jobId = $json["jobId"];

							// ============================================
							// PASO 4: MONITOREAR PROGRESO DEL TRABAJO
							// ============================================
							
							do {
								$response = $this->CheckJobStatus($jobId, $apiKey);
								
								if ($response['status'] === "success") {
									
									// ============================================
									// PASO 5: PROCESAR DATOS EXITOSOS
									// ============================================
									
									$resultUrl = $response['url'];
									$parsedJson = file_get_contents($resultUrl);
									$data = json_decode($parsedJson, true);

									// PASO 5.1: Consultar balance final de créditos para mayor exactitud
									$balanceInfoFinal = $this->model->getCurrentCreditsBalance($apiKey);
									$tokensFinales = ($balanceInfoFinal && isset($balanceInfoFinal['remainingCredits'])) ? $balanceInfoFinal['remainingCredits'] : 0;
									
									// Calcular tokens consumidos con mayor precisión
									$tokensConsumidos = max(0, $tokensIniciales - $tokensFinales);
									
									// PASO 5.2: Actualizar log con datos finales exitosos y tokens precisos
									if ($logId) {
										$finalUpdateData = [
											'page_count' => $response['pageCount'] ?? 0,
											'api_response_url' => $resultUrl,
											'tokens_restantes' => $tokensFinales,
											'tokens_consumidos' => $tokensConsumidos,
											'status' => 'success'
										];
										$this->model->updatePdfUploadLog($logId, $finalUpdateData);
									}

									// PASO 5.3: Procesar movimientos según el banco
									$movimientosFormat = [];
									switch ($bancoPrefijo) {
										case 'BCR': $movimientosFormat = $this->bancoBancaribe($data); break;
										case 'TSR': $movimientosFormat = $this->bancoTesoro($data); break;
										//case 'BCO': $movimientosFormat = $this->bancoBanesco($anio, $mes, $data); break;
										//case 'VNZ': $movimientosFormat = $this->bancoVenezuela($data); break;
										//case 'MRC': $movimientosFormat = $this->bancoMercantil($anio, $data); break;
										//case 'BNC': $movimientosFormat = $this->bancoBnc($data); break;
										case 'PRV': $movimientosFormat = $this->bancoProvincial($data); break;

									}
									
									// PASO 5.4: Validar estructura de movimientos
									if (!is_array($movimientosFormat) || !isset($movimientosFormat['mov']) || !is_array($movimientosFormat['mov'])) {
										// Consultar balance final incluso en caso de error para registro preciso
										$balanceInfoFinal = $this->model->getCurrentCreditsBalance($apiKey);
										$tokensFinales = ($balanceInfoFinal && isset($balanceInfoFinal['remainingCredits'])) ? $balanceInfoFinal['remainingCredits'] : 0;
										$tokensConsumidos = max(0, $tokensIniciales - $tokensFinales);
										
										// Actualizar log con error de estructura y tokens precisos
										if ($logId) {
											$this->model->updatePdfUploadLog($logId, [
												'tokens_restantes' => $tokensFinales,
												'tokens_consumidos' => $tokensConsumidos,
												'status' => 'error',
												'error_message' => 'Banco/prefijo no soportado o estructura de datos inválida. Prefijo: ' . (string)$bancoPrefijo
											]);
										}
										
										unlink($uploadPath);
										echo json_encode([
											'status' => false,
											'msg' => 'Banco/prefijo no soportado o no se obtuvieron movimientos del archivo PDF. Prefijo: ' . (string)$bancoPrefijo
										], JSON_UNESCAPED_UNICODE);
										die();
									}

									// PASO 5.5: Insertar movimientos en la base de datos
									$inserted = $this->model->insertTransaction($anio, $mes, $bancoId, $movimientosFormat['mov']);

									// Eliminar archivo temporal
									unlink($uploadPath);

									// PASO 5.6: Preparar respuesta final
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

								} elseif ($response['status'] === "working") {
									// Trabajo aún en progreso, esperar antes de verificar nuevamente
									sleep(3);
									
								} elseif ($response['status'] === "failed") {
									// ============================================
									// MANEJO DE ERROR: TRABAJO FALLIDO
									// ============================================
									
									// Consultar balance final para registro preciso incluso en fallo
									$balanceInfoFinal = $this->model->getCurrentCreditsBalance($apiKey);
									$tokensFinales = ($balanceInfoFinal && isset($balanceInfoFinal['remainingCredits'])) ? $balanceInfoFinal['remainingCredits'] : 0;
									$tokensConsumidos = max(0, $tokensIniciales - $tokensFinales);
									
									// Actualizar log con error de procesamiento y tokens precisos
									if ($logId) {
										$this->model->updatePdfUploadLog($logId, [
											'tokens_restantes' => $tokensFinales,
											'tokens_consumidos' => $tokensConsumidos,
											'status' => 'error',
											'error_message' => 'Error en PDF.co: Trabajo fallido durante el procesamiento'
										]);
									}
									
									// Eliminar archivo temporal
									if (file_exists($uploadPath)) {
										unlink($uploadPath);
									}
									
									echo json_encode([
										'success' => false,
										'msg' => 'Error al leer el archivo bancario.'
									]);
									die();
									
								} else {
									// Estado desconocido, salir del bucle
									break;
								}
							} while (true);
							
						} else {
							// ============================================
							// MANEJO DE ERROR: RESPUESTA CON ERROR DE API
							// ============================================
							
							// Consultar balance final para registro preciso incluso con error de API
							$balanceInfoFinal = $this->model->getCurrentCreditsBalance($apiKey);
							$tokensFinales = ($balanceInfoFinal && isset($balanceInfoFinal['remainingCredits'])) ? $balanceInfoFinal['remainingCredits'] : 0;
							$tokensConsumidos = max(0, $tokensIniciales - $tokensFinales);
							
							// Actualizar log con error de API y tokens precisos
							if ($logId) {
								$errorMessage = isset($json["message"]) ? $json["message"] : "Error desconocido en API";
								$this->model->updatePdfUploadLog($logId, [
									'tokens_restantes' => $tokensFinales,
									'tokens_consumidos' => $tokensConsumidos,
									'status' => 'error',
									'error_message' => 'Error de API PDF.co: ' . $errorMessage
								]);
							}
							
							// Eliminar archivo temporal
							if (file_exists($uploadPath)) {
								unlink($uploadPath);
							}
							
							echo json_encode([
								'status' => false,
								'msg' => 'Error en la API de procesamiento: ' . (isset($json["message"]) ? $json["message"] : "Error desconocido")
							], JSON_UNESCAPED_UNICODE);
							die();
						}
						
					} else {
						// ============================================
						// MANEJO DE ERROR: CÓDIGO HTTP INCORRECTO
						// ============================================
						
						// Actualizar log con error HTTP
						if ($logId) {
							$this->model->updatePdfUploadLog($logId, [
								'status' => 'error',
								'error_message' => "Error HTTP $status_code en PDF.co"
							]);
						}
						
						// Eliminar archivo temporal
						if (file_exists($uploadPath)) {
							unlink($uploadPath);
						}
						
						echo json_encode([
							'status' => false,
							'msg' => "Error del servidor de procesamiento (HTTP $status_code)"
						], JSON_UNESCAPED_UNICODE);
						die();
					}
					
				} else {
					// ============================================
					// MANEJO DE ERROR: ERROR DE CURL
					// ============================================
					
					// Actualizar log con error de conexión
					if ($logId) {
						$this->model->updatePdfUploadLog($logId, [
							'status' => 'error',
							'error_message' => 'Error de conexión cURL: ' . curl_error($curl)
						]);
					}
					
					// Eliminar archivo temporal
					if (file_exists($uploadPath)) {
						unlink($uploadPath);
					}
					
					echo json_encode([
						'status' => false,
						'msg' => 'Error de conexión con el servicio de procesamiento'
					], JSON_UNESCAPED_UNICODE);
					die();
				}
				
				curl_close($curl);

				// Eliminar archivo si ocurrió algún error después de haberlo subido
				if (file_exists($uploadPath)) {
					unlink($uploadPath);
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

			} else if($fileExt === 'csv'){
				
				//CONDICION IF DEL CSV (INICIO)
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
					case 'BAC': $movimientosFormat = $this->procesarCsvBancaribe($uploadPath); break;
					
				}

				// Validación de estructura antes de acceder a ['mov']
				if (!is_array($movimientosFormat) || !isset($movimientosFormat['mov']) || !is_array($movimientosFormat['mov'])) {
					// Eliminar archivo temporal y responder error claro
					unlink($uploadPath);
					echo json_encode([
						'status' => false,
						'msg' => 'Banco/prefijo no soportado o no se obtuvieron movimientos del archivo (Csv). Prefijo: ' . (string)$bancoPrefijo
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
					case 'BAC': $movimientosFormat = $this->procesarExcelBancaribe($uploadPath); break;
					
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
	 * Detecta automáticamente el formato de fecha y la convierte a Y-m-d
	 * Soporta formatos: d/m/Y, m/d/Y, d-m-Y, m-d-Y
	 */
	private function detectarFormatoFecha($fechaStr) {
		$fechaStr = trim($fechaStr);

		// Lista de formatos posibles a probar
		$formatos = [
			'd/m/Y',  // 17/11/2025
			'm/d/Y',  // 11/17/2025
			'd-m-Y',  // 17-11-2025
			'm-d-Y',  // 11-17-2025
			'Y-m-d',  // 2025-11-17 (ya en formato correcto)
			'Y/m/d'   // 2025/11/17
		];
		
		foreach ($formatos as $formato) {
			$fechaObj = DateTime::createFromFormat($formato, $fechaStr);
			if ($fechaObj !== false) {
				// Verificar que la fecha sea válida comparando con el string original
				$fechaFormateada = $fechaObj->format($formato);
				if ($fechaFormateada === $fechaStr) {
					return $fechaObj->format('Y-m-d');
				}
			}
		}
		
		// Si ningún formato funciona, intentar con strtotime como último recurso
		$timestamp = strtotime($fechaStr);
		if ($timestamp !== false) {
			return date('Y-m-d', $timestamp);
		}
		
		// Si todo falla, registrar error y retornar fecha actual
		error_log("Error al parsear fecha: " . $fechaStr);
		return date('Y-m-d'); // Fecha actual como fallback
	}

	/**
	 * Procesa la referencia bancaria según el formato específico
	 * @param string $descripcion Descripción del movimiento (ej: TRAV0014228714000020968)
	 * @param string $columnaAlternativa Columna alternativa (ej: 0000139740)
	 * @param string $fecha Fecha del movimiento
	 * @return string Referencia procesada
	 */
	private function procesarReferenciaBancaria($descripcion, $columnaAlternativa, $fecha)
	{
		// Limpiar la descripción
		$descripcion = trim($descripcion);
		
		// Procesar diferentes formatos de referencias bancarias
	
	// Formato 1: TRA + [V/J/E] + cedula + referencia
	if (strpos($descripcion, 'TRA') === 0) {
		// Estructura: TRA + [V/J/E] + [00/000] + cedula + referencia
		// Ejemplos: 
		// - TRAV0030019249000017555 (TRA + V00 + 30019249 + 000017555)
		// - TRAJ00014228714000020968 (TRA + J000 + 14228714 + 000020968)
		// - TRAE0012345678000098765 (TRA + E00 + 12345678 + 000098765)
		
		$patron = '/^TRA([VJE])0*(\d+)(\d{9})$/';
		if (preg_match($patron, $descripcion, $matches)) {
			$tipoPersona = $matches[1]; // V, J o E
			$cedula = $matches[2];      // cedula (ej: 30019249)
			$referencia = $matches[3];  // referencia (ej: 000017555)
			
			// Formato: dmYFVCedula
			$fechaFormateada = date('dmY', strtotime($fecha));
			return $fechaFormateada . 'F' . $tipoPersona . $cedula;
		}
	}
	
	// Formato 2: TPBW + [V/J/E] + cedula + referencia (ignorar referencia)
	if (strpos($descripcion, 'TPBW') === 0) {
		// Estructura: TPBW + espacio + [V/J/E] + [00/000] + cedula + espacio + referencia
		// Ejemplo: TPBW V0010962793 01080
		
		$patron = '/^TPBW\s+([VJE])0*(\d+)\s+\d+$/';
		if (preg_match($patron, $descripcion, $matches)) {
			$tipoPersona = $matches[1]; // V, J o E
			$cedula = $matches[2];      // cedula (ej: 10962793)
			
			// Formato: dmYFVCedula
			$fechaFormateada = date('dmY', strtotime($fecha));
			return $fechaFormateada . 'F' . $tipoPersona . $cedula;
		}
	}
	
	// Formato 3: CR.I/REC + codigo + [V/J/E] + cedula
	if (strpos($descripcion, 'CR.I/REC') === 0) {
		// Estructura: CR.I/REC + espacio + codigo + espacio + [V/J/E] + [0/00] + cedula
		// Ejemplo: CR.I/REC 0105 V010762773
		
		$patron = '/^CR\.I\/REC\s+\d+\s+([VJE])0*(\d+)$/';
		if (preg_match($patron, $descripcion, $matches)) {
			$tipoPersona = $matches[1]; // V, J o E
			$cedula = $matches[2];      // cedula (ej: 10762773)
			
			// Formato: dmYFVCedula
			$fechaFormateada = date('dmY', strtotime($fecha));
			return $fechaFormateada . 'F' . $tipoPersona . $cedula;
		}
	}
	
	// Si no coincide con ningún formato especial, usar columna alternativa
		// Limpiar comilla inicial y remover ceros a la izquierda: '0000139740 -> 139740
		$columnaLimpia = ltrim($columnaAlternativa, "'"); // Remover comilla inicial
		$referencia = ltrim($columnaLimpia, '0'); // Remover ceros a la izquierda
		return $referencia ?: '0'; // Si queda vacío, usar '0'
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
	//PROCESO DE BANCO BANCARIBE (PDF)
	private function bancoBancaribe($data)
	{	
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['transactions'];
		$movimientos_transformados = [];
		$totalMovimientos = 0;

		foreach ($movimientos as $key => $item) {
			
			if($item['operation_date'] == ''){
				continue;
			}
			// Convertir fecha de DD/MM/YYYY a YYYY-MM-DD
			$fecha = DateTime::createFromFormat('d-M-Y', $item['date'])->format('Y-m-d');
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
	
	//PROCESO DE BANCO SOFITASA (PDF)
	private function bancoSofitasa($data)
	{
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['account_statement']['transactions'];
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
	
	//PROCESO DE BANCO TESORO (PDF)
	private function bancoTesoro($data)
	{	
		// Puedes hacer un print_r si estás debuggeando:
		$movimientos = $data['transactions'];
		$movimientos_transformados = [];
		$totalMovimientos = 0;

		foreach ($movimientos as $key => $item) {
			
			if($item['operation_date'] == ''){
				continue;
			}
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

	//------ PROCESO ARCHIVOS EN EXCEL -------//

	//PROCESO DE BANCO VENEZUELA (EXCEL)
	private function procesarExcelVenezuela($filePath)
	{

		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();
			
			foreach ($rows as $fila) {

				if (count($fila) == 13) {
					$result = $this->procesarExcelVenezuela3($filePath);
					return $result;
				}else if (count($fila) > 8) {
					$result = $this->procesarExcelVenezuela2($filePath);
					return $result;
				}else if (count($fila) == 8){
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
				
				$fecha = $this->detectarFormatoFecha($fila[0]);

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

	private function procesarExcelVenezuela3($filePath){

		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			$movimientos_transformados = [];
			$totalMovimientos = 0;

			// Asume que la primera fila son los encabezados
			for ($i = 6; $i < count($rows); $i++) {
				$fila = $rows[$i];

				if ($fila[2] == 'SALDO INICIAL') {
					continue;
				}
				
				$fecha = $this->detectarFormatoFecha($fila[3]);

				if ($fila[4] == 'NC') {
					$monto = $this->parseEuropeanNumber($fila[5]);
				} else {
					$monto = $this->parseEuropeanNumber($fila[6]);
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
		for ($i = 6; $i < count($rows); $i++) {
			$fila = $rows[$i];
			
			if ($fila[2] == 'Descripción') {
				continue; 
			}

			if ($fila[2] == 'SALDO INICIAL') {
				break; 
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

				$fecha = DateTime::createFromFormat('m/d/Y', $fila[1])->format('Y-m-d');
				//$fecha = $this->detectarFormatoFecha($fila[1]);

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

	//PROCESO DE BANCO BANCARIBE (EXCEL)
	private function procesarExcelBancaribe($filePath)
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

				$fecha = $this->detectarFormatoFecha($fila[0]);

				$amount = $this->parseEuropeanNumber($fila[4]);

				// Determinar el monto correcto
				if ($fila[3] == 'D') {
					$monto = '-'.$amount;
				} else {
					$monto = $amount;
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
					$fecha = DateTime::createFromFormat('m/d/Y', $fila[0])->format('Y-m-d');
					//$fecha = $this->detectarFormatoFecha($fila[0]);
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


				$fecha = DateTime::createFromFormat('m/d/Y', $fila[0])->format('Y-m-d');
				//$fecha = $this->detectarFormatoFecha($fila[0]);
				
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
			
			foreach ($rows as $fila) {
				if (count($fila) == 19) {
					$result = $this->procesarExcelBnc1($filePath);
					return $result;
				}else{
					$result = $this->procesarExcelBnc2($filePath);
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

	//PROCESO DE BANCO BNC - FORMATO 1(EXCEL)
	private function procesarExcelBnc1($filePath)
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

				//$fecha = DateTime::createFromFormat('d/m/Y', $fila[1])->format('Y-m-d');
				$fecha = $this->detectarFormatoFecha($fila[1]);
				if($fila[2] != ''){

					
					$debit = $this->parseEuropeanNumber($fila[13]);
					$credit = $this->parseEuropeanNumber($fila[15]);

					$referencia = $fila[12];
				}else{

				

					$debit = $this->parseEuropeanNumber($fila[11]);
					$credit = $this->parseEuropeanNumber($fila[13]);

					$referencia = $fila[10];
				}

				if ($credit == 0) {
					$monto = $debit;
				} else {
					$monto = $credit;
				}
		
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

	//PROCESO DE BANCO BNC - FORMATO 2(EXCEL)
	private function procesarExcelBnc2($filePath)
	{	
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			$movimientos_transformados = [];
			$totalMovimientos = 0;

			for ($i = 15; $i < count($rows); $i++) {
				$fila = $rows[$i];
				if($i == 15){
					if($fila[1] == 'Fecha'){
						$positionRef = 10;
						$positiondebit = 11;
						$positioncredit = 13;
					}else{
						$positionRef = 9;
						$positiondebit = 10;
						$positioncredit = 12;
					}
					break;
				}
				
			}
			
			// Asume que la primera fila son los encabezados
			for ($i = 15; $i < count($rows); $i++) {
				
				$fila = $rows[$i];
				if ($fila[1] == 'Totales') {
					continue; 
				}
				if ($fila[1] == 'Fecha') {
					continue; 
				}
				if ($fila[1] == '') {
					continue; 
				}
				if ($fila[13] == 'Saldo') {
					continue; 
				}
				if ($fila[13] == '') {
					continue; 
				}

				$fecha = DateTime::createFromFormat('Y/m/d', $fila[1])->format('Y-m-d');
				//$fecha = $this->detectarFormatoFecha($fila[0]);

				$debit = $this->parseEuropeanNumber($fila[$positiondebit]);
				$credit = $this->parseEuropeanNumber($fila[$positioncredit]);

				if ($credit == 0) {
					$monto = $debit;
				} else {
					$monto = $credit;
				}
		
				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $fila[$positionRef],  // Ej: "123456"
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

				$fecha = $this->detectarFormatoFecha($fila[0]);
				
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

			foreach ($rows as $fila) {
				if (count($fila) == 7) {
					$result = $this->procesarExcelProvincial2($filePath);
					return $result;
				}else if (count($fila) == 5){
					$result = $this->procesarExcelProvincial1($filePath);
					return $result;
				}
			}

		} catch (Exception $e) {
			if (file_exists($filePath)) unlink($filePath);
			echo json_encode([
				'success' => false,
				'msg' => 'Archivo Excel esta dañado y/o formato no permitido.'
			]);
			die();
		}
	}

	private function procesarExcelProvincial1($filePath)
	{	
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			$movimientos_transformados = [];
			$totalMovimientos = 0;
			
			for ($i = 1; $i < 2; $i++) {
				
				$fila = $rows[$i];
				if($fila[1] != ''){
					$result = $this->procesarExcelProvincialSub1($filePath);
					return $result;
					break;
				}else{
					$result = $this->procesarExcelProvincialSub2($filePath);
					return $result;
					break;
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

	private function procesarExcelProvincialSub1($filePath)
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
				
				if($fila[0] == ''){
					continue;
				}

					$fecha = DateTime::createFromFormat('m/d/Y', $fila[0])->format('Y-m-d');
					//$fecha = $this->detectarFormatoFecha($fila[0]);

					$amount = $this->parseEuropeanNumber($fila[3]);

					//$reference = str_replace(["'", '"'], '', preg_replace('/^.?([VJE])0(\d+).*$/', '\1\2', $fila[1]));
					$reference = $this->procesarReferenciaBancaria($fila[2], $fila[1], $fecha);
					// Ajusta los índices [0], [1], [2] según el orden de tus columnas
					$movimientos_transformados[] = [
						'fecha'      => $fecha,  // Ej: "2024-01-01"
						'referencia' => $reference,  // Ej: "123456"
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

	private function procesarExcelProvincialSub2($filePath)
	{	
		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			$movimientos_transformados = [];
			$totalMovimientos = 0;
			// Asume que la primera fila son los encabezados
			for ($i = 10; $i < count($rows); $i++) {
				$fila = $rows[$i];

				if($fila[0] == ''){
					continue;
				}
				if($fila[0] == 'Saldo Inicial '){
					break;
				}

					$fnew = ltrim($fila[0]);
					$fecha = DateTime::createFromFormat('d-m-Y', $fnew)->format('Y-m-d');
					//$fecha = DateTime::createFromFormat('d-m-Y', trim($fila[0]))->format('Y-m-d');
					//$fecha = $this->detectarFormatoFecha($fila[0]);

					$amount = $this->parseEuropeanNumber($fila[4]);

					$fileFormat = ltrim($fila[2], "'");
					//$reference = str_replace(["'", '"'], '', preg_replace('/^.?([VJE])0(\d+).*$/', '\1\2', $fila[1]));
					$reference = $this->procesarReferenciaBancaria($fileFormat, $fila[3], $fecha);
					// Ajusta los índices [0], [1], [2] según el orden de tus columnas
					$movimientos_transformados[] = [
						'fecha'      => $fecha,  // Ej: "2024-01-01"
						'referencia' => $reference,  // Ej: "123456"
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

	

	//PROCESO DE BANCO PROVINCIAL (EXCEL - PAGO MOVIL)
	private function procesarExcelProvincial2($filePath)
	{	

		try {
			$spreadsheet = IOFactory::load($filePath);
			$sheet = $spreadsheet->getActiveSheet();
			$rows = $sheet->toArray();

			$movimientos_transformados = [];
			$totalMovimientos = 0;

			// Asume que la primera fila son los encabezados
			for ($i = 20; $i < count($rows); $i++) {
				$fila = $rows[$i];
				
				if($fila[0] == ''){
					continue;
				}

				//$fecha = $this->detectarFormatoFecha($fila[0]);
				$fecha = DateTime::createFromFormat('m/d/Y', $fila[0])->format('Y-m-d');

				$amount = $this->parseEuropeanNumber($fila[5]);

				// Procesar referencia según el formato específico
				$reference = $this->procesarReferenciaBancaria($fila[4], $fila[3], $fecha);
				// Ajusta los índices [0], [1], [2] según el orden de tus columnas
				$movimientos_transformados[] = [
					'fecha'      => $fecha,  // Ej: "2024-01-01"
					'referencia' => $reference,  // Ej: "123456"
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

	//------ PROCESO ARCHIVOS EN CSV -------//

	//PROCESO DE BANCO BANCARIBE (CSV)
	private function procesarCsvBancaribe($filePath)
	{	
		
		try {

			if (($handle = fopen($filePath, "r")) !== false) {
				
				$movimientos_transformados = [];
				$totalMovimientos = 0;
				$lineNumber = 0;
				
				while (($data = fgetcsv($handle, 1000, ';')) !== false) {
					$lineNumber++;
					
					// Saltar la primera línea que contiene información de la cuenta
					if ($lineNumber == 1) {
						continue;
					}
					
					// Validar que tenga al menos 9 columnas (estructura completa)
					if (count($data) >= 9) {
						
						// Extraer datos de cada columna
						$fechaRaw = trim($data[0]);      // 18/11/2025
						$referencia = trim($data[1]);    // 8,21707E+11
						$descripcion = trim($data[2]);   // DEPOSITO A TERCEROS
						$tipo = trim($data[3]);          // D o C
						$montoRaw = trim($data[4]);      // 554.730,00
						
						// Validar que los campos obligatorios no estén vacíos
						if (empty($fechaRaw) || empty($referencia) || empty($montoRaw) || empty($tipo)) {
							continue;
						}
						
						// Convertir fecha de dd/mm/yyyy a yyyy-mm-dd
						$fecha = DateTime::createFromFormat('d/m/Y', $fechaRaw)->format('Y-m-d');
						
						// Procesar el monto usando la función parseEuropeanNumber
						$amount = $this->parseEuropeanNumber($montoRaw);
						
						// Determinar el signo del monto según el tipo
						if ($tipo == 'D') {
							// Débito: monto negativo
							$monto = '-' . $amount;
						} else {
							// Crédito: monto positivo
							$monto = $amount;
						}
						
						$movimientos_transformados[] = [
							'fecha'      => $fecha,
							'referencia' => $referencia,
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
				'msg' => 'Archivo CSV esta dañado.'
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

	// ============================================
	// MÉTODOS PARA GESTIÓN DE COMENTARIOS
	// ============================================

	/**
	 * Obtener comentario de una transacción
	 * Method: GET
	 * Params: conciliation_id, empresa_id
	 */
	public function getComment()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
			echo json_encode(['status' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
			die();
		}

		$conciliationId = intval($_GET['conciliation_id'] ?? 0);
		$userId = $_SESSION['idUser'] ?? 0;

		if ($conciliationId <= 0) {
			echo json_encode(['status' => false, 'message' => 'ID de transacción inválido'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// Instanciar modelo de comentarios
		$commentModel = new CommentModel();

		// Obtener información de la transacción y empresa
		$transactionInfo = $commentModel->getTransactionWithEnterprise($conciliationId);
		if (!$transactionInfo) {
			echo json_encode(['status' => false, 'message' => 'Transacción no encontrada o sin acceso'], JSON_UNESCAPED_UNICODE);
			die();
		}

		$empresaId = $transactionInfo['transaction']['id_enterprise'];

		// Obtener comentario existente
		$comment = $commentModel->getTransactionComment($conciliationId, $empresaId);

		if ($comment) {
			echo json_encode([
				'status' => true,
				'has_comment' => true,
				'comment' => $comment
			], JSON_UNESCAPED_UNICODE);
		} else {
			echo json_encode([
				'status' => true,
				'has_comment' => false,
				'can_comment' => $commentModel->canUserComment($userId)
			], JSON_UNESCAPED_UNICODE);
		}
		die();
	}


    public function createComment()
	{
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo json_encode(['status' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
			die();
		}

		$postdata = file_get_contents("php://input");
		$request = json_decode($postdata, true);

		if (!is_array($request)) {
			echo json_encode(['status' => false, 'message' => 'JSON inválido'], JSON_UNESCAPED_UNICODE);
			die();
		}

		$conciliationId = intval($request['conciliation_id'] ?? 0);
		$description = trim($request['description'] ?? '');
		$userId = $_SESSION['idUser'] ?? 0;

		// Validaciones básicas
		if ($conciliationId <= 0 || empty($description)) {
			echo json_encode(['status' => false, 'message' => 'Datos incompletos'], JSON_UNESCAPED_UNICODE);
			die();
		}

		if (strlen($description) > 1000) {
			echo json_encode(['status' => false, 'message' => 'El comentario es demasiado largo (máx 1000 caracteres)'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// Instanciar modelo de comentarios
		$commentModel = new CommentModel();

		// Verificar permisos del usuario
		if (!$commentModel->canUserComment($userId)) {
			echo json_encode(['status' => false, 'message' => 'No tienes permisos para crear comentarios'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// Obtener información de la transacción y empresa
		$transactionInfo = $commentModel->getTransactionWithEnterprise($conciliationId);
		if (!$transactionInfo) {
			echo json_encode(['status' => false, 'message' => 'Transacción no encontrada o sin acceso'], JSON_UNESCAPED_UNICODE);
			die();
		}

		$empresaId = $transactionInfo['transaction']['id_enterprise'];

		// Verificar si ya existe un comentario
		$existingComment = $commentModel->getTransactionComment($conciliationId, $empresaId);
		if ($existingComment) {
			echo json_encode(['status' => false, 'message' => 'Esta transacción ya tiene un comentario'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// Crear el comentario
		$commentId = $commentModel->createComment($description);
		if (!$commentId) {
			echo json_encode(['status' => false, 'message' => 'Error al crear el comentario'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// Crear la relación
		$relationId = $commentModel->createConciliationComment($conciliationId, $empresaId, $commentId, $userId);
		if (!$relationId) {
			echo json_encode(['status' => false, 'message' => 'Error al asociar el comentario con la transacción'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// Obtener el comentario completo para la respuesta
		$newComment = $commentModel->getTransactionComment($conciliationId, $empresaId);

		echo json_encode([
			'status' => true,
			'message' => 'Comentario creado exitosamente',
			'comment' => $newComment
		], JSON_UNESCAPED_UNICODE);
		die();
	}

	/**
	 * Actualizar un comentario existente
	 */
	public function updateComment()
	{
		// Verificar que sea una petición POST
		if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
			echo json_encode(['status' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// Obtener datos del POST
		$rawInput = file_get_contents('php://input');
		$input = json_decode($rawInput, true);

		if (json_last_error() !== JSON_ERROR_NONE) {
			echo json_encode(['status' => false, 'message' => 'Error en formato JSON'], JSON_UNESCAPED_UNICODE);
			die();
		}

		$commentId = intval($input['comment_id'] ?? 0);
		$description = trim($input['description'] ?? '');
		$userId = intval($_SESSION['idUser'] ?? 0);

		// Validaciones básicas
		if ($commentId <= 0) {
			echo json_encode(['status' => false, 'message' => 'ID de comentario inválido'], JSON_UNESCAPED_UNICODE);
			die();
		}

		if (empty($description)) {
			echo json_encode(['status' => false, 'message' => 'La descripción del comentario es obligatoria'], JSON_UNESCAPED_UNICODE);
			die();
		}

		if (strlen($description) > 1000) {
			echo json_encode(['status' => false, 'message' => 'El comentario es demasiado largo (máximo 1000 caracteres)'], JSON_UNESCAPED_UNICODE);
			die();
		}

		if ($userId <= 0) {
			echo json_encode(['status' => false, 'message' => 'Usuario no válido'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// Instanciar modelo de comentarios
		$commentModel = new CommentModel();

		// Verificar permisos del usuario
		if (!$commentModel->canUserComment($userId)) {
			echo json_encode(['status' => false, 'message' => 'No tienes permisos para editar comentarios'], JSON_UNESCAPED_UNICODE);
			die();
		}

		// Actualizar el comentario (incluye validación de propietario)
		$updated = $commentModel->updateComment($commentId, $description, $userId);
		
		if (!$updated) {
			echo json_encode(['status' => false, 'message' => 'No se pudo actualizar el comentario. Verifica que seas el propietario.'], JSON_UNESCAPED_UNICODE);
			die();
		}

		echo json_encode([
			'status' => true,
			'message' => 'Comentario actualizado exitosamente'
		], JSON_UNESCAPED_UNICODE);
		die();
	}

	/**
	 * Exportar transacciones filtradas a Excel
	 * Exporta exactamente los datos que están visibles en la tabla con filtros aplicados
	 */
	public function exportToExcel()
	{
		// Terminar y limpiar TODOS los buffers de salida
		while (ob_get_level()) {
			ob_end_clean();
		}
		
		try {
			// Verificar que sea una petición POST
			if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
				http_response_code(405);
				header('Content-Type: application/json');
				echo json_encode(['status' => false, 'message' => 'Método no permitido'], JSON_UNESCAPED_UNICODE);
				exit();
			}

			// Verificar si PhpSpreadsheet está disponible
			if (!class_exists('PhpOffice\PhpSpreadsheet\Spreadsheet')) {
				header('Content-Type: application/json');
				echo json_encode(['status' => false, 'message' => 'PhpSpreadsheet no está instalado'], JSON_UNESCAPED_UNICODE);
				exit();
			}

			// Obtener filtros del POST (los mismos que usa el DataTable)
			$rawInput = file_get_contents('php://input');
			$input = json_decode($rawInput, true);
			
			if (json_last_error() !== JSON_ERROR_NONE) {
				error_log("Error JSON en exportToExcel: " . json_last_error_msg() . " - Input: " . $rawInput);
				header('Content-Type: application/json');
				echo json_encode(['status' => false, 'message' => 'Error al procesar los filtros JSON: ' . json_last_error_msg()], JSON_UNESCAPED_UNICODE);
				exit();
			}

			$filters = [
				'bank'     => $input['bank']     ?? '',
				'account'  => $input['account']  ?? '',
				'reference'=> $input['reference']?? '',
				'dateFrom' => $input['dateFrom'] ?? '',
				'dateTo'   => $input['dateTo']   ?? '',
				'estado'   => $input['estado']   ?? '',
				'monto'    => $input['monto']    ?? '',
			];

			// Log de filtros para debug
			error_log("Filtros para exportar: " . json_encode($filters));

			// Obtener los datos usando el mismo método que el DataTable
			$arrData = $this->model->getTransaction($filters);
			
			// Log de cantidad de datos
			error_log("Datos obtenidos para exportar: " . count($arrData) . " registros");

			if (empty($arrData)) {
				header('Content-Type: application/json');
				echo json_encode(['status' => false, 'message' => 'No hay datos para exportar con los filtros aplicados'], JSON_UNESCAPED_UNICODE);
				exit();
			}

			// Crear nuevo spreadsheet
			$spreadsheet = new Spreadsheet();
			$sheet = $spreadsheet->getActiveSheet();

			// Configurar encabezados
			$headers = ['Nº', 'BANCO', 'CUENTA', 'REFERENCIA', 'FECHA', 'MONTO', 'RESPONSABLE', 'ASIGNADO', 'ESTADO'];
			$sheet->fromArray($headers, null, 'A1');

			// Aplicar estilos a los encabezados
			$headerStyle = [
				'font' => [
					'bold' => true,
					'color' => ['rgb' => 'FFFFFF'],
					'size' => 12
				],
				'fill' => [
					'fillType' => Fill::FILL_SOLID,
					'startColor' => ['rgb' => '667eea']
				],
				'alignment' => [
					'horizontal' => Alignment::HORIZONTAL_CENTER,
					'vertical' => Alignment::VERTICAL_CENTER
				],
				'borders' => [
					'allBorders' => [
						'borderStyle' => Border::BORDER_THIN,
						'color' => ['rgb' => '000000']
					]
				]
			];
			$sheet->getStyle('A1:I1')->applyFromArray($headerStyle);

			// Log del primer registro para debug
			if (!empty($arrData)) {
				error_log("Primer registro de datos: " . json_encode($arrData[0]));
			}

			// Agregar datos
			$row = 2;
			foreach ($arrData as $index => $data) {
				try {
					// Formatear fecha de forma más segura
					$formattedDate = '';
					if (!empty($data['date'])) {
						if (strpos($data['date'], '-') !== false) {
							$dateParts = explode('-', $data['date']);
							if (count($dateParts) === 3) {
								$formattedDate = $dateParts[2] . '/' . $dateParts[1] . '/' . $dateParts[0];
							} else {
								$formattedDate = $data['date'];
							}
						} else {
							$formattedDate = $data['date'];
						}
					}

					// Formatear monto de forma más segura
					$formattedAmount = '';
					if (isset($data['amount']) && $data['amount'] !== '') {
						if (is_numeric($data['amount'])) {
							$formattedAmount = number_format((float)$data['amount'], 2, '.', ',');
						} else {
							$formattedAmount = $data['amount'];
						}
					}

					// Determinar estado de forma más segura
					$estado = 'Desconocido';
					
					// Verificar diferentes posibles nombres del campo de estado
					$statusValue = null;
					if (isset($data['status'])) {
						$statusValue = $data['status'];
					} elseif (isset($data['estado'])) {
						$statusValue = $data['estado'];
					} elseif (isset($data['id_status'])) {
						$statusValue = $data['id_status'];
					} elseif (isset($data['status_id'])) {
						$statusValue = $data['status_id'];
					}
					
					// Colores de fondo según estado
					$backgroundColor = 'FFFFFF'; // Blanco por defecto
					
					if ($statusValue !== null) {
						switch ((int)$statusValue) {
							case 1:
								$estado = 'No Conciliado';
								$backgroundColor = 'FFEBEE'; // Rojito suave
								break;
							case 2:
								$estado = 'Conciliado';
								$backgroundColor = 'E8F5E8'; // Verdecito suave
								break;
							case 3:
								$estado = 'Parcial';
								$backgroundColor = 'FFF8E1'; // Amarillito suave
								break;
							case 4:
								$estado = 'Asignado';
								$backgroundColor = 'E3F2FD'; // Azulito suave
								break;
							default:
								$estado = 'Desconocido (' . $statusValue . ')';
								$backgroundColor = 'F5F5F5'; // Gris suave
						}
					} else {
						// Log para debug: mostrar las claves disponibles
						error_log("Claves disponibles en datos: " . implode(', ', array_keys($data)));
					}

					$rowData = [
						$index + 1,                           // Nº
						$data['bank'] ?? '',                  // BANCO
						$data['account'] ?? '',               // CUENTA
						$data['reference'] ?? '',             // REFERENCIA
						$formattedDate,                       // FECHA
						$formattedAmount,                     // MONTO
						$data['responsible'] ?? '',           // RESPONSABLE
						$data['assigned'] ?? '',              // ASIGNADO
						$estado                               // ESTADO
					];

					$sheet->fromArray($rowData, null, 'A' . $row);
					
					// Aplicar color de fondo a la fila según el estado
					$rowRange = 'A' . $row . ':I' . $row;
					$rowStyle = [
						'fill' => [
							'fillType' => Fill::FILL_SOLID,
							'startColor' => ['rgb' => $backgroundColor]
						]
					];
					$sheet->getStyle($rowRange)->applyFromArray($rowStyle);
					
					$row++;
					
				} catch (Exception $e) {
					error_log("Error procesando fila $index: " . $e->getMessage() . " - Datos: " . json_encode($data));
					// Continuar con la siguiente fila
					continue;
				}
			}
			
			error_log("Filas procesadas exitosamente: " . ($row - 2));

			// Aplicar estilos a los datos
			$dataRange = 'A2:I' . ($row - 1);
			$dataStyle = [
				'borders' => [
					'allBorders' => [
						'borderStyle' => Border::BORDER_THIN,
						'color' => ['rgb' => 'CCCCCC']
					]
				],
				'alignment' => [
					'vertical' => Alignment::VERTICAL_CENTER
				]
			];
			$sheet->getStyle($dataRange)->applyFromArray($dataStyle);

			// Ajustar ancho de columnas
			foreach (range('A', 'I') as $col) {
				$sheet->getColumnDimension($col)->setAutoSize(true);
			}

			// Configurar propiedades del documento
			$spreadsheet->getProperties()
				->setCreator('Banking ADN')
				->setTitle('Reporte de Transacciones')
				->setSubject('Exportación de transacciones bancarias')
				->setDescription('Reporte generado desde Banking ADN con filtros aplicados')
				->setKeywords('transacciones bancarias excel reporte')
				->setCategory('Reportes');

			// Generar nombre de archivo con timestamp
			$timestamp = date('Y-m-d_H-i-s');
			$filename = "transacciones_" . $timestamp . ".xlsx";

			// Log antes de generar el archivo
			error_log("Iniciando generación del archivo Excel...");
			
			// Limpiar cualquier salida adicional antes de enviar headers
			while (ob_get_level()) {
				ob_end_clean();
			}
			
			// Configurar headers para descarga
			header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
			header('Content-Disposition: attachment;filename="' . $filename . '"');
			header('Cache-Control: max-age=0');
			header('Cache-Control: max-age=1');
			header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
			header('Last-Modified: ' . gmdate('D, d M Y H:i:s') . ' GMT');
			header('Cache-Control: cache, must-revalidate');
			header('Pragma: public');

			error_log("Headers configurados, creando writer...");

			// Crear writer y enviar archivo
			$writer = new Xlsx($spreadsheet);
			
			error_log("Writer creado, guardando archivo...");
			
			$writer->save('php://output');
			
			error_log("Archivo guardado exitosamente");
			
			// Limpiar memoria
			$spreadsheet->disconnectWorksheets();
			unset($spreadsheet);
			
			exit();

		} catch (Exception $e) {
			error_log("Error en exportToExcel: " . $e->getMessage());
			http_response_code(500);
			header('Content-Type: application/json');
			echo json_encode([
				'status' => false, 
				'message' => 'Error al generar el archivo Excel: ' . $e->getMessage()
			], JSON_UNESCAPED_UNICODE);
			exit();
		} catch (Error $e) {
			error_log("Error fatal en exportToExcel: " . $e->getMessage());
			http_response_code(500);
			header('Content-Type: application/json');
			echo json_encode([
				'status' => false, 
				'message' => 'Error fatal al generar el archivo Excel: ' . $e->getMessage()
			], JSON_UNESCAPED_UNICODE);
			exit();
		}
	}
}
?>