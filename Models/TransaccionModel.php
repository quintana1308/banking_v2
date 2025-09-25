<?php

	require_once("Libraries/Core/Mysql.php");

	class TransaccionModel extends Mysql
	{	


		public function __construct()
		{
			parent::__construct();	
		}

		// ============================================
		// FUNCIONES PRINCIPALES DE TRANSACCIONES
		// ============================================
		
		//OBTENER LISTADO DE MOVIMIENTOS
		public function getTransaction($filters = [])
		{

			
			$id_enterprise = $_SESSION['userData']['id_enterprise'];
			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);
			$table = $requestEnterprise['table'];
			
			$where = "WHERE 1=1"; // base segura para concatenar
			if (!empty($filters['bank'])) {
				
				$bank = $filters['bank'];

				$sqlBanks = "SELECT id_bank FROM banco WHERE id_enterprise = $id_enterprise AND `name` = '$bank'";
				$requestBanks = $this->select_all($sqlBanks);
				$id_bank = $requestBanks[0]['id_bank'];

				$where .= " AND m.bank LIKE '%$id_bank%'";
			}
			if (!empty($filters['account'])) {
				$account = $filters['account'];
				$where .= " AND m.account LIKE '%$account%'";
			}
			if (!empty($filters['reference'])) {
				$reference = $filters['reference'];
				$where .= " AND m.reference LIKE '%$reference%'";
			}
			if (!empty($filters['date'])) {
				$date = $filters['date'];
				$where .= " AND m.date LIKE '%$date%'";
			}
			
			// 👇 NUEVA LÓGICA DEL FILTRO DE CONSOLIDACIÓN CON STATUS_ID
			if (!empty($filters['estado'])) {
				switch ($filters['estado']) {
					case 'no_conciliados':
						$where .= " AND m.status_id = 1"; // auto_reconciled
						break;

					case 'conciliados':
						$where .= " AND m.status_id = 2"; // auto_reconciled
						break;

					case 'parcial':
						$where .= " AND m.status_id = 3"; // partial_match
						break;

					case 'asignados':
						$where .= " AND m.status_id = 4"; // manual_assigned
						break;
				}
			}

			// Obtener los IDs de los bancos que pertenecen a la empresa del usuario
			/*$sqlBanks = "SELECT id_bank FROM banco WHERE id_enterprise = $id_enterprise";
			$requestBanks = $this->select_all($sqlBanks);

			if (empty($requestBanks)) {
				return []; // Si no hay bancos para esta empresa, retornar array vacío
			}

			// Crear lista de IDs de bancos para la consulta IN
			$bankIds = array_column($requestBanks, 'id_bank');
			$bankIdsString = implode(',', $bankIds);*/
			
			$sql = "SELECT m.id, b.name as bank, m.account, m.responsible, m.reference, m.date, m.amount, 
						m.status_id, s.name as status_name, s.description as status_description, 
						u.id as id_user, u.name as name_user
					FROM $table m
					LEFT JOIN usuario u ON u.id = m.assignment
					LEFT JOIN banco b ON b.id_bank = m.bank AND b.`account` = m.`account`
					LEFT JOIN transaction_status s ON s.id = m.status_id
					$where";
					
			return $this->select_all($sql);
		}

		//OBTENGO TODAS LAS CUENTAS BANCARIAS
		public function getAccounts()
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];
			$sql = "SELECT * FROM banco WHERE id_enterprise = $id_enterprise AND `status` = 1";

			$request = $this->select_all($sql);

			return $request;
		}

		//API PARA DEVOLVER DATA A SISTEMA ADN (AUTOCONCILIACION)
		public function getTransactionEndPoint($token, $rif, $bd, $cuenta, $opcion, $desde, $hasta)
		{	
			
			$sqlTable = "SELECT * FROM empresa 
						WHERE `rif` = '$rif' 
						AND token = '$token'
						AND (bd = '' OR  IF('$bd' = '',1, bd = '$bd'))";
			$request = $this->select($sqlTable);
			$table = $request['table'];
			
			

			if($opcion == 'movimientosRangoFecha'){
				$where = "  b.account = '$cuenta'
				            AND m.date >= '$desde' AND m.date <= '$hasta'";
			}else{
				$where = " m.date BETWEEN '$desde' AND '$hasta'";
			}

			$sql = "SELECT b.id_bank AS bank, b.account, m.reference, m.`date`, m.amount, m.responsible, m.id_insert
				FROM $table m
				INNER JOIN banco b ON b.account = m.account
				LEFT JOIN conciliation_anthony mm
				ON mm.account = m.account
				AND mm.reference = m.reference
				AND mm.`date` = m.`date`
				AND mm.amount > 0
				WHERE $where";

				
		
			$request = $this->select_all($sql);

			return $request;

		}

	/**
	 * FUNCIÓN PRINCIPAL DE CONCILIACIÓN AUTOMÁTICA
	 * 
	 * Procesa un array de movimientos bancarios del sistema externo (DATA A)
	 * y los concilia con los registros de la base de datos local (DATA B)
	 * 
	 * @param array $movimientos Array de movimientos con estructura:
	 *   - rif: RIF de la empresa
	 *   - token: Token de autenticación
	 *   - bank: ID del banco
	 *   - account: Número de cuenta
	 *   - reference: Referencia del movimiento
	 *   - date: Fecha en formato YYYYMMDD
	 *   - amount: Monto del movimiento
	 *   - autocon: Indicador de conciliación automática (0 o 1)
	 *   - coincidence: Indicador de coincidencia parcial (0 o 1)
	 * 
	 * @return array Resultado con contadores de conciliación
	 */
	public function getTransactionConciliation($movimientos)
	{	
		// ==========================================
		// PASO 1: LOGGING Y VALIDACIÓN INICIAL
		// ==========================================
		
		// Registrar movimientos recibidos en log para auditoría
		$logFile = 'movimientos.log';
    	$fechaActual = date('Y-m-d H:i:s');
    	$logEntry = "$fechaActual - MOVIMIENTOS_RECIBIDOS - " . json_encode($movimientos) . "\n";
    	file_put_contents($logFile, $logEntry, FILE_APPEND);

		// Validar que se recibieron movimientos válidos
		if (empty($movimientos) || !is_array($movimientos)) {
			return ['completos' => 0, 'parciales' => 0, 'sin_coincidencia' => 0];
		}

		// ==========================================
		// PASO 2: PREPARACIÓN DEL PROCESO
		// ==========================================
		
		// Resetear todos los registros consolidados en las fechas de los movimientos
		// Esto permite reprocesar conciliaciones en caso de reenvío de datos
		$this->resetearRegistrosConsolidados($movimientos);

		// Inicializar contadores de resultados
		$totalCompleto = 0;        // Estatus 2: Conciliación completa
		$totalParcial = 0;         // Estatus 3: Conciliación parcial
		$totalSinCoincidencia = 0; // Estatus 1: Sin conciliación

		// ==========================================
		// PASO 3: PROCESAMIENTO DE CADA MOVIMIENTO
		// ==========================================
		
		foreach ($movimientos as $mov) {
			// PASO 3.1: Validar estructura del movimiento
			if (!$this->validarMovimiento($mov)) {
				$totalSinCoincidencia++;
				continue; // Saltar movimiento inválido
			}

			// PASO 3.2: Extraer datos del movimiento
			$rif = $mov['rif'];                           // RIF de la empresa
			$token = $mov['token'];                       // Token de autenticación
			$bank = $mov['bank'];                         // ID del banco
			$account = $mov['account'];                   // Número de cuenta
			$refRecibida = $mov['reference'];             // Referencia del movimiento
			$fechaRecibida = $mov['date'];                // Fecha (YYYYMMDD)
			$montoRecibido = abs(floatval($mov['amount'])); // Monto (siempre positivo)
			
			// PASO 3.3: Obtener tabla de la empresa
			$table = $this->obtenerTablaEmpresa($rif, $token);
			
			if (!$table) {
				$totalSinCoincidencia++;
				continue; // Empresa no encontrada
			}
			
			// PASO 3.4: Extraer indicadores de conciliación del sistema externo
			// Estos valores determinan el estatus final:
			// autocon=1, coincidence=0 → Estatus 2 (Conciliado)
			// autocon=0, coincidence=1 → Estatus 3 (Parcial)
			// autocon=0, coincidence=0 → Estatus 1 (No conciliado)
			$autocon = isset($mov['autocon']) ? intval($mov['autocon']) : 0;
			$coincidence = isset($mov['coincidence']) ? intval($mov['coincidence']) : 0;

			$resultadoConciliacion = $this->evaluarConciliacion(
				$table, $bank, $account, $fechaRecibida, 
				$montoRecibido, $refRecibida, $rif, $token, 
				$autocon, $coincidence
			);
			
			// PASO 3.5: Buscar registro similar en la base de datos
			/*$resultadoConciliacion = $this->evaluarConciliacion(
				$table, $bank, $account, $fechaRecibida, 
				$montoRecibido, $refRecibida, $rif, $token, 
				$autocon, $coincidence
			);*/
			
			// PASO 3.6: Procesar resultado de la conciliación
			if (/*$resultadoConciliacion['registro']*/true) {
				// Se encontró un registro similar
				$status_id = $resultadoConciliacion['status_id'];
				
				// Incrementar contadores según el estatus asignado
				if ($status_id == 2) {
					$totalCompleto++;        // Conciliación completa
				} elseif ($status_id == 3) {
					$totalParcial++;         // Conciliación parcial
				} else {
					$totalSinCoincidencia++; // Sin conciliación
				}
				
				// Actualizar el estatus en la base de datos
				$this->actualizarStatusRegistro($table, $resultadoConciliacion['registro']['id'], $status_id);
			} else {
				// No se encontró registro similar
				$totalSinCoincidencia++;
			}
		}

		// ==========================================
		// PASO 4: PREPARAR Y RETORNAR RESULTADO
		// ==========================================
		
		$resultadoFinal = [
			'completos' => $totalCompleto,           // Total de conciliaciones completas
			'parciales' => $totalParcial,            // Total de conciliaciones parciales
			'sin_coincidencia' => $totalSinCoincidencia // Total sin conciliar
		];


		return $resultadoFinal;
	}

	/**
	 * VALIDAR ESTRUCTURA DE UN MOVIMIENTO
	 * 
	 * Verifica que el movimiento tenga todos los campos requeridos
	 * y que los datos estén en el formato correcto
	 * 
	 * @param array $mov Movimiento a validar
	 * @return bool True si es válido, False si no
	 */
	private function validarMovimiento($mov) {
		// Campos obligatorios que debe tener cada movimiento
		$camposRequeridos = ['bank', 'account', 'rif', 'token', 'reference', 'date', 'amount'];
		
		// Verificar que todos los campos estén presentes y no vacíos
		foreach ($camposRequeridos as $campo) {
			if (!isset($mov[$campo]) || empty($mov[$campo])) {
				return false; // Campo faltante o vacío
			}
		}
		
		// Validar formato de fecha (debe ser YYYYMMDD - 8 dígitos)
		if (!preg_match('/^\d{8}$/', $mov['date'])) {
			return false; // Formato de fecha inválido
		}
		
		// Validar que el monto sea numérico
		if (!is_numeric($mov['amount'])) {
			return false; // Monto no numérico
		}
		
		return true; // Movimiento válido
	}

	/**
	 * OBTENER REGLAS DE CONCILIACIÓN DEL BANCO
	 * 
	 * Consulta las reglas configuradas para un banco específico:
	 * - concibanc_monto: Diferencial permitido en el monto (ej: 100 = ±100 Bs)
	 * - concibanc_reference: Número de dígitos a comparar en la referencia (ej: 4 = últimos 4 dígitos)
	 * 
	 * @param string $bank ID del banco
	 * @param string $account Número de cuenta
	 * @param string $rif RIF de la empresa
	 * @param string $token Token de autenticación
	 * @return array Reglas de conciliación ['monto' => float|null, 'referencia' => int|null]
	 */
	private function obtenerReglasBanco($bank, $account, $rif, $token) {
		// Consultar reglas de conciliación configuradas para este banco
		$sqlReglas = "SELECT concibanc_monto AS monto, concibanc_reference AS reference 
					  FROM banco b
					  INNER JOIN empresa e ON e.id = b.id_enterprise
					  WHERE b.id_bank = '$bank' 	
					  AND b.account = '$account' 
					  AND e.rif = '$rif'
					  AND e.token = '$token'";

		$reglas = $this->select($sqlReglas);
		
		return [
			'monto' => isset($reglas['monto']) ? floatval($reglas['monto']) : null,
			'referencia' => isset($reglas['reference']) ? intval($reglas['reference']) : null
		];
	}

	/**
	 * OBTENER NOMBRE DE LA TABLA DE MOVIMIENTOS DE UNA EMPRESA
	 * 
	 * Cada empresa tiene su propia tabla de movimientos bancarios.
	 * Esta función obtiene el nombre de esa tabla usando RIF y token.
	 * 
	 * @param string $rif RIF de la empresa
	 * @param string $token Token de autenticación
	 * @return string|null Nombre de la tabla o null si no se encuentra
	 */
	private function obtenerTablaEmpresa($rif, $token) {
		// Buscar la tabla asociada a la empresa
		$sqlTable = "SELECT `table` FROM empresa WHERE rif = '$rif' AND token = '$token'";
		$requestEnterprise = $this->select($sqlTable);
		
		return isset($requestEnterprise['table']) ? $requestEnterprise['table'] : null;
	}




	/**
	 * ACTUALIZAR ESTATUS DE UN REGISTRO EN LA BASE DE DATOS
	 * 
	 * Cambia el status_id de un movimiento específico:
	 * - 1: No conciliado
	 * - 2: Conciliado (completo)
	 * - 3: Conciliación parcial
	 * - 4: Asignado manualmente
	 * 
	 * @param string $table Nombre de la tabla
	 * @param int $id ID del registro
	 * @param int $status_id Nuevo estatus (1-4)
	 * @return bool Resultado de la actualización
	 */
	private function actualizarStatusRegistro($table, $id, $status_id) {
		$sqlUpdate = "UPDATE $table SET status_id = ? WHERE id = '$id'";
		$valueArray = [$status_id];
		return $this->update($sqlUpdate, $valueArray);
	}

	/**
	 * RESETEAR REGISTROS CONSOLIDADOS EN RANGO DE FECHAS
	 * 
	 * Antes de procesar nuevos movimientos, resetea todos los registros
	 * que ya estaban conciliados en las fechas de los movimientos recibidos.
	 * Esto permite reprocesar conciliaciones si se reenvían datos.
	 * 
	 * @param array $movimientos Array de movimientos a procesar
	 * @return void
	 */
	private function resetearRegistrosConsolidados($movimientos) {
		// Agrupar fechas por empresa para optimizar consultas
		$fechasEmpresas = [];
		
		// PASO 1: Extraer fechas únicas por empresa
		foreach ($movimientos as $mov) {
			if (!$this->validarMovimiento($mov)) {
				continue; // Saltar movimientos inválidos
			}
			
			$rif = $mov['rif'];
			$token = $mov['token'];
			$fecha = $mov['date'];
			
			// Crear clave única para la empresa
			$claveEmpresa = "{$rif}_{$token}";
			
			// Inicializar array para nueva empresa
			if (!isset($fechasEmpresas[$claveEmpresa])) {
				$fechasEmpresas[$claveEmpresa] = [
					'rif' => $rif,
					'token' => $token,
					'fechas' => []
				];
			}
			
			// Agregar fecha si no existe
			if (!in_array($fecha, $fechasEmpresas[$claveEmpresa]['fechas'])) {
				$fechasEmpresas[$claveEmpresa]['fechas'][] = $fecha;
			}
		}
		
		// PASO 2: Resetear registros por empresa
		foreach ($fechasEmpresas as $empresa) {
			$table = $this->obtenerTablaEmpresa($empresa['rif'], $empresa['token']);
			
			if (!$table) {
				continue; // Empresa no encontrada
			}
			
			// PASO 3: Construir condiciones de fecha para SQL
			$condicionesFecha = [];
			foreach ($empresa['fechas'] as $fecha) {
				// Convertir YYYYMMDD a formato de fecha SQL
				$condicionesFecha[] = "c.date = STR_TO_DATE('$fecha', '%Y%m%d')";
			}
			
			if (!empty($condicionesFecha)) {
				$whereFechas = implode(' OR ', $condicionesFecha);
				
				// PASO 4: Resetear registros consolidados a estado pendiente
				// Cambia estatus 2,3,4 → 1 (pendiente) para permitir reprocesamiento
				$sqlReset = "UPDATE $table c 
							 SET c.status_id = 1 
							 WHERE ($whereFechas) 
							 AND c.status_id IN (2, 3, 4)";
				
				$this->update_massive($sqlReset);
			}
		}
	}

		// ============================================
		// FUNCIONES DE GESTIÓN DE TRANSACCIONES
		// ============================================

		//LE ASIGNO AL MOVIMIENTO UN USUARIO RESPONSABLE Y/O A CARGO
		public function updateAsignacion($id, $userId)
		{
			$id_enterprise = $_SESSION['userData']['id_enterprise'];
			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$request = $this->select($sqlTable);
			$table = $request['table'];
			
			$query = "UPDATE $table SET assignment = ?, status_id = ? WHERE id = ?";
			$arrData = array($userId, 4, $id); // 4 = manual_assigned
			
			$resp = $this->update($query, $arrData); // ajusta según tu modelo base
		
			return $resp;
		}

		//OBTENGO LOS BANCOS DEPENDIENTE DE LA ID DE LA EMPRESA
		public function getBank($id_enterprise)
		{
			$sql = "SELECT * FROM banco WHERE id_enterprise = $id_enterprise AND `status` = 1";

			$request = $this->select_all($sql);

			return $request;
		}

		//GENERAR ID_INSERT ÚNICO PARA CADA MOVIMIENTO CON SUFIJO INCREMENTAL PARA DUPLICADOS
	private function generateIdInsert($anio, $mes, $banco, $movimiento, $sufijo = 1)
	{
		// Crear hash único basado en los datos del movimiento específico
		$userId = $_SESSION['userData']['id'] ?? 0;
		$id_enterprise = $_SESSION['userData']['id_enterprise'] ?? 0;
		
		// Crear hash basado en fecha, referencia y monto del movimiento
		$movimientoKey = $movimiento['fecha'] . '_' . $movimiento['referencia'] . '_' . $movimiento['monto'];
		$movimientoHash = md5($movimientoKey);
		
		// Generar ID base único para este tipo de movimiento
		$uniqueString = $id_enterprise . '_' . $userId . '_' . $anio . '_' . $mes . '_' . $banco . '_' . $movimientoHash;
		$idBase = abs(crc32($uniqueString));
		
		// Agregar sufijo para diferenciar duplicados
		// Formato: idBase + sufijo (ej: 123456789 + 1 = 1234567891)
		return intval($idBase . $sufijo);
	}

		//INSERTO LOS MOVIMIENTOS QUE OBTENGO DEL ARCHIVO SUBIDO POR EL CLIENTE
	public function insertTransaction($anio, $mes, $banco, $movimientos)
	{	
		$errorInsert = 0;
		$id_enterprise = $_SESSION['userData']['id_enterprise'];

		$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
		$request = $this->select($sqlTable);

		$table = $request['table'];

		$sqlBanco = "SELECT * FROM banco WHERE id = $banco";
		$selectBanco = $this->select($sqlBanco);
		
		$limpioAccount = preg_replace('/\s+/', '', $selectBanco['account']);
		$limpioNameBank = preg_replace('/\s+/', ' ', trim($selectBanco['id_bank']));

		$sqlCountStart = "SELECT COUNT(*) AS total FROM $table";
		$resquestCountStart = $this->select($sqlCountStart);
		$totalStart = $resquestCountStart['total'];
		
		// Contador para movimientos duplicados
		$contadorDuplicados = [];
		
		// Array para construir los VALUES del INSERT
		$valuesRows = [];
		
		foreach ($movimientos as $key => $mov) {
			
			// Extraer año y mes desde la fecha del movimiento
			$fechaMovimiento = DateTime::createFromFormat('Y-m-d', $mov['fecha']);
			$anioMov = (int)$fechaMovimiento->format('Y');
			$mesMov = (int)$fechaMovimiento->format('m');
			
			$limpioreference = preg_replace('/\s+/', '', $mov['referencia']);
			
			if ($anioMov == $anio && $mesMov == $mes) {
				
				// Crear clave única para identificar movimientos duplicados
				$claveMovimiento = $mov['fecha'] . '|' . $limpioreference . '|' . $mov['monto'];
				
				// Incrementar contador para esta combinación de fecha, referencia y monto
				if (!isset($contadorDuplicados[$claveMovimiento])) {
					$contadorDuplicados[$claveMovimiento] = 1;
				} else {
					$contadorDuplicados[$claveMovimiento]++;
				}
				
				// Generar ID único con sufijo incremental para duplicados
				$sufijo = $contadorDuplicados[$claveMovimiento];
				$idInsert = $this->generateIdInsert($anio, $mes, $banco, $mov, $sufijo);
				
				// Escapar valores para evitar SQL injection usando addslashes
				$escapedBank = "'" . addslashes($limpioNameBank) . "'";
				$escapedAccount = "'" . addslashes($limpioAccount) . "'";
				$escapedReference = "'" . addslashes($limpioreference) . "'";
				$escapedDate = "'" . addslashes($mov['fecha']) . "'";
				$escapedAmount = floatval($mov['monto']);
				$escapedResponsible = "'API'";
				$escapedIdInsert = intval($idInsert);
				
				// Construir fila de valores para el INSERT
				$valuesRows[] = "($escapedBank, $escapedAccount, $escapedReference, $escapedDate, $escapedAmount, $escapedResponsible, $escapedIdInsert)";
			}
		}

		// Ejecutar insert masivo solo si hay datos para insertar
		if (!empty($valuesRows)) {
			// Construir consulta SQL completa con valores directos
			$sqlInsert = "INSERT IGNORE INTO $table (bank, account, reference, `date`, amount, responsible, id_insert) VALUES " . implode(',', $valuesRows);
			$request = $this->insert_massive($sqlInsert);
			
			if (!$request) {
				$errorInsert = 1;
			}
		}
		
		$sqlCountEnd = "SELECT COUNT(*) AS total FROM $table";
		$resquestCountEnd = $this->select($sqlCountEnd);
		$totalEnd = $resquestCountEnd['total'];

		// Retornar información más detallada
		$insertedRecords = $totalEnd - $totalStart;
		$totalProcessed = count($valuesRows);
		$duplicatesSkipped = $totalProcessed - $insertedRecords;

		return [
			'status' => ($errorInsert == 0) ? 'success' : 'error',
			'total_processed' => $totalProcessed,
			'inserted' => $insertedRecords,
			'duplicates_skipped' => $duplicatesSkipped,
			'error' => $errorInsert
		];
	}		

		//ACTUALIZO LOS DATOS DE LOS MOVIMIENTOS PERMITIDOS EN MI DATATABLE
		public function updateFieldById($id, $field, $value)
		{
			$allowed = ['reference', 'date', 'amount'];
			if (!in_array($field, $allowed)) return false;

			$id_enterprise = $_SESSION['userData']['id_enterprise'];

			$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
			$requestEnterprise = $this->select($sqlTable);

			$table = $requestEnterprise['table'];

			$sql = "UPDATE $table SET $field = ? WHERE id = ?";
			$arrData = array($value, $id);
			return $this->update($sql, $arrData);
		}	

		// ============================================
		// FUNCIONES AUXILIARES Y UTILIDADES
		// ============================================

		//BUSCAR LISTADO DE CLIENTES
		public function buscarClientes($search)
		{
    		$sql = "SELECT id, `name` FROM cliente WHERE `name` LIKE ?";
    		$valueArray = array('%' . $search . '%');
    		return $this->select_all($sql, $valueArray);
		}

		//OBTENER TODOS LOS STATUS DISPONIBLES
	public function getTransactionStatus()
	{
		$sql = "SELECT id, name, description, color FROM transaction_status ORDER BY id";
		return $this->select_all($sql);
	}

	/**
	 * Eliminar una transacción específica
	 */
	public function deleteTransaction($transactionId)
	{
		$id_enterprise = $_SESSION['userData']['id_enterprise'];

		$sqlTable = "SELECT * FROM empresa WHERE id = $id_enterprise";
		$requestEnterprise = $this->select($sqlTable);

		if (empty($requestEnterprise)) {
			return false;
		}

		$table = $requestEnterprise['table'];

		// Verificar que la transacción existe y pertenece a un banco de la empresa
		$sqlCheck = "SELECT t.id FROM $table t 
					INNER JOIN banco b ON b.id_bank = t.bank 
					WHERE t.id = $transactionId AND b.id_enterprise = $id_enterprise";
		$exists = $this->select($sqlCheck);

		if (empty($exists)) {
			return false; // La transacción no existe o no pertenece a la empresa
		}

		// Eliminar la transacción
		$sql = "DELETE FROM $table WHERE id = $transactionId";
		$request = $this->delete($sql);
		
		return $request;
	}

		// ============================================
		// FUNCIONES PRIVADAS DE CONCILIACIÓN
		// ============================================

	/**
	 * EVALUAR CONCILIACIÓN DE UN MOVIMIENTO - VERSIÓN MEJORADA
	 * 
	 * Busca el mejor registro coincidente usando un sistema de puntuación inteligente
	 * que maneja correctamente movimientos duplicados y diferentes tipos de coincidencias.
	 * 
	 * MEJORAS IMPLEMENTADAS:
	 * 1. Sistema de puntuación para determinar la mejor coincidencia
	 * 2. Manejo correcto de movimientos duplicados
	 * 3. Evaluación de TODOS los candidatos, no solo el primero
	 * 4. Priorización inteligente basada en calidad de coincidencia
	 * 
	 * SISTEMA DE PUNTUACIÓN:
	 * - Referencia exacta: 100 puntos
	 * - Referencia parcial (últimos N dígitos): 50 puntos  
	 * - Monto exacto: 100 puntos
	 * - Monto con diferencial permitido: 75 puntos
	 * - Fecha exacta: 100 puntos
	 * - Bonus por coincidencia perfecta (3/3): +50 puntos
	 * 
	 * @param string $table Tabla de movimientos de la empresa
	 * @param string $bank ID del banco
	 * @param string $account Número de cuenta
	 * @param string $fechaRecibida Fecha del movimiento (YYYYMMDD)
	 * @param float $montoRecibido Monto del movimiento
	 * @param string $refRecibida Referencia del movimiento
	 * @param string $rif RIF de la empresa
	 * @param string $token Token de autenticación
	 * @param int $autocon Indicador de conciliación automática (0 o 1)
	 * @param int $coincidence Indicador de coincidencia parcial (0 o 1)
	 * @return array ['registro' => array|null, 'status_id' => int]
	 */
	private function evaluarConciliacion($table, $bank, $account, $fechaRecibida, $montoRecibido, $refRecibida, $rif, $token, $autocon, $coincidence) {
		// ==========================================
		// PASO 1: OBTENER REGLAS DE CONCILIACIÓN
		// ==========================================
		
		$reglas = $this->obtenerReglasBanco($bank, $account, $rif, $token);
		$diferencialBs = isset($reglas['monto']) ? floatval($reglas['monto']) : null;
		$digitosReferencia = isset($reglas['referencia']) ? intval($reglas['referencia']) : null;
		$fechaFormateada = $this->formatearFechaParaBusqueda($fechaRecibida);
		
		// ==========================================
		// PASO 2: OBTENER TODOS LOS CANDIDATOS
		// ==========================================
		
		// Buscar TODOS los registros pendientes de la cuenta para evaluación completa
		$sqlCandidatos = "SELECT c.id, c.amount, c.reference, c.date, c.status_id, c.account
						  FROM $table c
						  WHERE c.status_id = 1
						  AND c.account = '$account'
						  ORDER BY c.date DESC, c.id ASC";
		
		$candidatos = $this->select_all($sqlCandidatos);
		
		if (empty($candidatos)) {
			return ['registro' => null, 'status_id' => 1];
		}
		
		// ==========================================
		// PASO 3: EVALUAR Y PUNTUAR CADA CANDIDATO
		// ==========================================
		
		$mejorCandidato = null;
		$mejorPuntuacion = 0;
		$candidatosEvaluados = [];
		
		foreach ($candidatos as $candidato) {
			$puntuacion = $this->calcularPuntuacionCoincidencia(
				$refRecibida, $montoRecibido, $fechaRecibida,
				$candidato['reference'], abs(floatval($candidato['amount'])), $candidato['date'],
				$digitosReferencia, $diferencialBs
			);
			
			// Registrar evaluación para debugging
			$candidatosEvaluados[] = [
				'id' => $candidato['id'],
				'referencia' => $candidato['reference'],
				'monto' => $candidato['amount'],
				'fecha' => $candidato['date'],
				'puntuacion' => $puntuacion['total']
			];
			
			// Actualizar mejor candidato si esta puntuación es superior
			if ($puntuacion['total'] > $mejorPuntuacion) {
				$mejorPuntuacion = $puntuacion['total'];
				$mejorCandidato = $candidato;
			}
		}
		
		// ==========================================
		// PASO 4: DETERMINAR ESTATUS FINAL
		// ==========================================
		
		
		// Solo considerar válida si tiene puntuación mínima (al menos 2 coincidencias)
		if ($mejorPuntuacion >= 150) { // Mínimo: 2 coincidencias parciales (50+75=125) + margen
			$statusFinal = $this->determinarStatusPorAutoconCoincidence($autocon, $coincidence);
			return [
				'registro' => $mejorCandidato,
				'status_id' => $statusFinal
			];
		}
		
		// No hay coincidencia suficiente
		return [
			'registro' => null,
			'status_id' => 1
		];
	}

	/**
	 * CALCULAR PUNTUACIÓN DE COINCIDENCIA ENTRE MOVIMIENTOS
	 * 
	 * Sistema de puntuación inteligente que evalúa la calidad de coincidencia
	 * entre un movimiento recibido y un candidato de la base de datos.
	 * 
	 * SISTEMA DE PUNTUACIÓN:
	 * ┌─────────────────────┬─────────────┬─────────────────────────────────┐
	 * │ Criterio            │ Puntuación  │ Descripción                     │
	 * ├─────────────────────┼─────────────┼─────────────────────────────────┤
	 * │ Referencia exacta   │    100      │ Referencias idénticas           │
	 * │ Referencia parcial  │     50      │ Últimos N dígitos coinciden     │
	 * │ Monto exacto        │    100      │ Montos idénticos                │
	 * │ Monto diferencial   │     75      │ Diferencia dentro del rango     │
	 * │ Fecha exacta        │    100      │ Fechas idénticas                │
	 * │ Bonus perfecto      │     50      │ Los 3 criterios coinciden       │
	 * └─────────────────────┴─────────────┴─────────────────────────────────┘
	 * 
	 * @param string $refRecibida Referencia del movimiento recibido
	 * @param float $montoRecibido Monto del movimiento recibido
	 * @param string $fechaRecibida Fecha del movimiento recibido (YYYYMMDD)
	 * @param string $refCandidato Referencia del candidato en BD
	 * @param float $montoCandidato Monto del candidato en BD
	 * @param string $fechaCandidato Fecha del candidato en BD
	 * @param int|null $digitosReferencia Regla de dígitos para referencia
	 * @param float|null $diferencialBs Regla de diferencial para monto
	 * @return array ['referencia' => int, 'monto' => int, 'fecha' => int, 'total' => int, 'detalles' => array]
	 */
	private function calcularPuntuacionCoincidencia($refRecibida, $montoRecibido, $fechaRecibida, $refCandidato, $montoCandidato, $fechaCandidato, $digitosReferencia, $diferencialBs) {
		$puntuacion = [
			'referencia' => 0,
			'monto' => 0, 
			'fecha' => 0,
			'total' => 0,
			'detalles' => []
		];
		
		// ==========================================
		// EVALUAR COINCIDENCIA DE REFERENCIA
		// ==========================================
		
		if ($refRecibida == $refCandidato) {
			// Coincidencia exacta completa
			$puntuacion['referencia'] = 100;
			$puntuacion['detalles']['referencia'] = 'Exacta completa';
		} elseif ($digitosReferencia !== null && $digitosReferencia > 0) {
			// Evaluar coincidencia parcial según reglas
			$ultimosRecibida = substr($refRecibida, -$digitosReferencia);
			$ultimosCandidato = substr($refCandidato, -$digitosReferencia);
			
			if ($ultimosRecibida == $ultimosCandidato) {
				$puntuacion['referencia'] = 50;
				$puntuacion['detalles']['referencia'] = "Parcial (últimos $digitosReferencia dígitos)";
			} else {
				$puntuacion['detalles']['referencia'] = 'No coincide';
			}
		} else {
			$puntuacion['detalles']['referencia'] = 'No coincide (sin reglas parciales)';
		}
		
		// ==========================================
		// EVALUAR COINCIDENCIA DE MONTO
		// ==========================================
		
		$diferenciaMonto = abs($montoRecibido - $montoCandidato);
		
		if ($diferenciaMonto == 0) {
			// Monto exacto
			$puntuacion['monto'] = 100;
			$puntuacion['detalles']['monto'] = 'Exacto';
		} elseif ($diferencialBs !== null && $diferencialBs > 0 && $diferenciaMonto <= $diferencialBs) {
			// Monto dentro del diferencial permitido
			$puntuacion['monto'] = 75;
			$puntuacion['detalles']['monto'] = "Diferencial permitido (±$diferencialBs Bs, diff: $diferenciaMonto)";
		} else {
			$puntuacion['detalles']['monto'] = "Diferencia: $diferenciaMonto Bs";
		}
		
		// ==========================================
		// EVALUAR COINCIDENCIA DE FECHA
		// ==========================================
		
		$fechaFormateada = $this->formatearFechaParaBusqueda($fechaRecibida);
		
		if ($fechaFormateada == $fechaCandidato) {
			$puntuacion['fecha'] = 100;
			$puntuacion['detalles']['fecha'] = 'Exacta';
		} else {
			$puntuacion['detalles']['fecha'] = "No coincide ($fechaFormateada vs $fechaCandidato)";
		}
		
		// ==========================================
		// CALCULAR PUNTUACIÓN TOTAL Y BONUS
		// ==========================================
		
		$puntuacion['total'] = $puntuacion['referencia'] + $puntuacion['monto'] + $puntuacion['fecha'];
		
		// Bonus por coincidencia perfecta (los 3 criterios con puntuación máxima)
		if ($puntuacion['referencia'] == 100 && $puntuacion['monto'] == 100 && $puntuacion['fecha'] == 100) {
			$puntuacion['total'] += 50;
			$puntuacion['detalles']['bonus'] = 'Coincidencia perfecta (+50)';
		}
		
		return $puntuacion;
	}

	
	/**
	 * EVALUAR COINCIDENCIA DE REFERENCIA
	 * 
	 * Compara las referencias según las reglas configuradas:
	 * - Si digitosReferencia = null: Comparación exacta completa
	 * - Si digitosReferencia = N: Compara solo los últimos N dígitos
	 * 
	 * Ejemplo: Si digitosReferencia = 4
	 * - Referencia A: "12345678" → Últimos 4: "5678"
	 * - Referencia B: "87655678" → Últimos 4: "5678"
	 * - Resultado: COINCIDE
	 * 
	 * @param string $refRecibida Referencia del sistema externo
	 * @param string $refBD Referencia de la base de datos
	 * @param int|null $digitosReferencia Número de dígitos a comparar (null = completa)
	 * @return bool True si coinciden, False si no
	 */
	private function evaluarCoincidenciaReferencia($refRecibida, $refBD, $digitosReferencia) {
		if ($digitosReferencia === null || $digitosReferencia <= 0) {
			// Sin reglas: comparación exacta de toda la referencia
			return $refRecibida == $refBD;
		} else {
			// Con reglas: comparar solo los últimos N dígitos
			$ultimosRecibida = substr($refRecibida, -$digitosReferencia);
			$ultimosBD = substr($refBD, -$digitosReferencia);
			return $ultimosRecibida == $ultimosBD;
		}
	}
	
	/**
	 * EVALUAR COINCIDENCIA DE MONTO
	 * 
	 * Compara los montos según las reglas de diferencial configuradas:
	 * - Si diferencialBs = null: Comparación exacta
	 * - Si diferencialBs = N: Acepta diferencia de ±N bolívares
	 * 
	 * Ejemplo: Si diferencialBs = 100
	 * - Monto A: 1500.00
	 * - Monto B: 1450.00
	 * - Diferencia: 50.00 (≤ 100) → COINCIDE
	 * 
	 * @param float $montoRecibido Monto del sistema externo
	 * @param float $montoBD Monto de la base de datos
	 * @param float|null $diferencialBs Diferencial permitido (null = exacto)
	 * @return bool True si coinciden, False si no
	 */
	private function evaluarCoincidenciaMonto($montoRecibido, $montoBD, $diferencialBs) {
		if ($diferencialBs === null || $diferencialBs <= 0) {
			// Sin reglas: comparación exacta del monto
			return $montoRecibido == $montoBD;
		} else {
			// Con reglas: verificar si la diferencia está dentro del rango permitido
			$diferencia = abs($montoRecibido - $montoBD);
			return $diferencia <= $diferencialBs;
		}
	}
	
	/**
	 * EVALUAR COINCIDENCIA DE FECHA
	 * 
	 * Compara las fechas normalizando ambos formatos:
	 * - Fecha recibida: YYYYMMDD → YYYY-MM-DD
	 * - Fecha BD: Varios formatos → YYYYMMDD
	 * 
	 * @param string $fechaRecibida Fecha del sistema externo (YYYYMMDD)
	 * @param string $fechaBD Fecha de la base de datos (varios formatos)
	 * @return bool True si coinciden, False si no
	 */
	private function evaluarCoincidenciaFecha($fechaRecibida, $fechaBD) {
		// Normalizar ambas fechas para comparación
		$fechaFormateada = $this->formatearFechaParaBusqueda($fechaRecibida);

		return $fechaFormateada == $fechaBD;
	}
	
	/**
	 * DETERMINAR ESTATUS SEGÚN AUTOCON Y COINCIDENCE
	 * 
	 * El sistema externo envía indicadores que determinan el estatus final:
	 * 
	 * REGLAS DE ESTATUS:
	 * ┌─────────┬─────────────┬─────────────────────────────────┐
	 * │ autocon │ coincidence │ Estatus │ Descripción           │
	 * ├─────────┼─────────────┼─────────┼─────────────────────────┤
	 * │    1    │      0      │    2    │ Conciliación completa  │
	 * │    0    │      1      │    3    │ Conciliación parcial   │
	 * │    0    │      0      │    1    │ No conciliado          │
	 * └─────────┴─────────────┴─────────┴─────────────────────────┘
	 * 
	 * @param int $autocon Indicador de conciliación automática (0 o 1)
	 * @param int $coincidence Indicador de coincidencia parcial (0 o 1)
	 * @return int Estatus final (1, 2 o 3)
	 */
	private function determinarStatusPorAutoconCoincidence($autocon, $coincidence) {
		if ($autocon == 1 && $coincidence == 0) {
			return 2; // Conciliación completa
		} elseif ($autocon == 0 && $coincidence == 1) {
			return 3; // Conciliación parcial
		} else {
			return 1; // No conciliado
		}
	}
	
	/**
	 * FORMATEAR FECHA PARA BÚSQUEDA EN BASE DE DATOS
	 * 
	 * Convierte diferentes formatos de fecha a formato SQL estándar (YYYY-MM-DD)
	 * 
	 * FORMATOS SOPORTADOS:
	 * - YYYYMMDD (20250701) → 2025-07-01
	 * - YYYY/MM/DD (2025/07/01) → 2025-07-01
	 * - YYYY-MM-DD (ya formateado) → sin cambios
	 * 
	 * @param string $fechaRecibida Fecha en formato original
	 * @return string Fecha en formato YYYY-MM-DD
	 */
	private function formatearFechaParaBusqueda($fechaRecibida) {
		if (strlen($fechaRecibida) == 8 && is_numeric($fechaRecibida)) {
			// Formato YYYYMMDD → YYYY-MM-DD
			return substr($fechaRecibida, 0, 4) . '-' . substr($fechaRecibida, 4, 2) . '-' . substr($fechaRecibida, 6, 2);
		} elseif (strpos($fechaRecibida, '/') !== false) {
			// Formato YYYY/MM/DD → YYYY-MM-DD
			return str_replace('/', '-', $fechaRecibida);
		}
		// Si ya está en formato YYYY-MM-DD, mantener igual
		return $fechaRecibida;
	}

}
?>
