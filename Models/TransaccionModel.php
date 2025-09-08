<?php

	require_once("Libraries/Core/Mysql.php");

	class TransaccionModel extends Mysql
	{	


		public function __construct()
		{
			parent::__construct();	
		}
		
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

				$where .= " AND m.bank LIKE '%$bank%'";
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
			
			$sql = "SELECT m.id, b.name as bank, m.account, m.responsible, m.reference, m.date, m.amount, 
						m.status_id, s.name as status_name, s.description as status_description, 
						u.id as id_user, u.name as name_user
					FROM $table m
					LEFT JOIN usuario u ON u.id = m.assignment
					LEFT JOIN banco b ON b.id_bank = m.bank
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

			$sql = "SELECT b.id_bank AS bank, b.account, m.reference, m.`date`, m.amount, m.responsible
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

		//REALIZAR AUTOCONSOLIDACION Y PINTAR MOVIMIENTOS - VERSIÓN CORREGIDA CON REGLAS DE CONCILIACIÓN
	public function getTransactionConciliation($movimientos)
	{	

		$logFile = 'movimientos.log';
    	$fechaActual = date('Y-m-d H:i:s');
    	$logEntry = "$fechaActual - " . json_encode($movimientos) . "\n";
    	file_put_contents($logFile, $logEntry, FILE_APPEND);

		// Validación de entrada
		if (empty($movimientos) || !is_array($movimientos)) {
			return ['completos' => 0, 'parciales' => 0, 'sin_coincidencia' => 0];
		}

		// Resetear registros consolidados en el rango de fechas antes de procesar
		$this->resetearRegistrosConsolidados($movimientos);

		$totalCompleto = 0;
		$totalParcial = 0;
		$totalSinCoincidencia = 0;

		// Procesar movimientos usando lógica de conciliación correcta
		foreach ($movimientos as $mov) {
			// Validar datos del movimiento
			if (!$this->validarMovimiento($mov)) {
				$totalSinCoincidencia++;
				continue;
			}

			$rif = $mov['rif'];
			$token = $mov['token'];
			$bank = $mov['bank'];
			$account = $mov['account'];
			$refRecibida = $mov['reference'];
			$fechaRecibida = $mov['date'];
			$montoRecibido = abs(floatval($mov['amount']));
			
			// Obtener tabla empresa
			$table = $this->obtenerTablaEmpresa($rif, $token);
			
			if (!$table) {
				$totalSinCoincidencia++;
				continue;
			}
			
			// Buscar registro y evaluar coincidencias
			$resultadoConciliacion = $this->evaluarConciliacion($table, $bank, $account, $fechaRecibida, $montoRecibido, $refRecibida, $rif, $token);
			
			if ($resultadoConciliacion['registro']) {
				$status_id = $resultadoConciliacion['status_id'];
				
				// Contar según el estatus asignado
				if ($status_id == 2) {
					$totalCompleto++;
				} elseif ($status_id == 3) {
					$totalParcial++;
				} else {
					$totalSinCoincidencia++;
				}
				
				// Actualizar registro
				$this->actualizarStatusRegistro($table, $resultadoConciliacion['registro']['id'], $status_id);
			} else {
				$totalSinCoincidencia++;
			}
		}

		$resultadoFinal = [
			'completos' => $totalCompleto,
			'parciales' => $totalParcial,
			'sin_coincidencia' => $totalSinCoincidencia
		];

		// Registrar resultado del procesamiento en log
		$this->registrarLogMovimientos($resultadoFinal, 'RESULTADO');

		return $resultadoFinal;
	}

		// MÉTODOS AUXILIARES PARA LA CONCILIACIÓN OPTIMIZADA
		private function validarMovimiento($mov) {
			$camposRequeridos = ['bank', 'account', 'rif', 'token', 'reference', 'date', 'amount'];
			
			foreach ($camposRequeridos as $campo) {
				if (!isset($mov[$campo]) || empty($mov[$campo])) {
					return false;
				}
			}
			
			// Validar formato de fecha
			if (!preg_match('/^\d{8}$/', $mov['date'])) {
				return false;
			}
			
			// Validar que amount sea numérico
			if (!is_numeric($mov['amount'])) {
				return false;
			}
			
			return true;
		}

		private function obtenerReglasBanco($bank, $account, $rif, $token) {
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

		private function obtenerTablaEmpresa($rif, $token) {
			$sqlTable = "SELECT `table` FROM empresa WHERE rif = '$rif' AND token = '$token'";
			$requestEnterprise = $this->select($sqlTable);
			
			return isset($requestEnterprise['table']) ? $requestEnterprise['table'] : null;
		}

		private function buscarRegistrosCandidatos($table, $bank, $account, $fechaRecibida) {
			// Búsqueda optimizada: buscar por fecha y cuenta, filtrar solo registros pendientes
			$sqlBusqueda = "SELECT c.id, c.amount, c.reference, c.date, c.status_id, c.account
							FROM $table c
							WHERE (c.date = STR_TO_DATE('$fechaRecibida', '%Y%m%d') 
								   OR c.account = '$account')
							AND c.status_id = 1
							ORDER BY c.date DESC, c.id ASC";
			
			return $this->select_all($sqlBusqueda);
		}

		private function encontrarMejorCoincidencia($registros, $montoRecibido, $refRecibida, $fechaRecibida) {

			$mejorCoincidencia = null;
			$maxCoincidencias = 0;
			
			foreach ($registros as $reg) {
				$coincidencias = 0;
				
				// Verificar monto (comparación exacta)
				$montoBD = abs(floatval($reg['amount']));
				if ($montoRecibido == $montoBD) {
					$coincidencias++;
				}
				
				// Verificar referencia (comparación exacta)
				$refBD = $reg['reference'];
				if ($refRecibida == $refBD) {
					$coincidencias++;
				}
				
				// Verificar fecha
				$fechaformat = DateTime::createFromFormat('Y-m-d', $reg['date'])->format('Ymd');
				if ($fechaformat == $fechaRecibida) {
					$coincidencias++;
				}
				
				// Actualizar mejor coincidencia
				if ($coincidencias > $maxCoincidencias) {
					$maxCoincidencias = $coincidencias;
					$mejorCoincidencia = $reg;
				}
			}
			
			return [
				'registro' => $mejorCoincidencia,
				'coincidencias' => $maxCoincidencias
			];
		}

		private function determinarStatus($coincidencias) {
			if ($coincidencias == 3) {
				return 2; // auto_reconciled
			} elseif ($coincidencias == 2) {
				return 3; // partial_match
			} else {
				return 4; // no_match
			}
		}

		private function actualizarStatusRegistro($table, $id, $status_id) {
			$sqlUpdate = "UPDATE $table SET status_id = ? WHERE id = '$id'";
			$valueArray = [$status_id];
			return $this->update($sqlUpdate, $valueArray);
		}

		// RESETEAR REGISTROS CONSOLIDADOS EN RANGO DE FECHAS
		private function resetearRegistrosConsolidados($movimientos) {
			// Obtener fechas únicas y empresas de los movimientos
			$fechasEmpresas = [];
			
			foreach ($movimientos as $mov) {
				if (!$this->validarMovimiento($mov)) {
					continue;
				}
				
				$rif = $mov['rif'];
				$token = $mov['token'];
				$fecha = $mov['date'];
				
				$claveEmpresa = "{$rif}_{$token}";
				
				if (!isset($fechasEmpresas[$claveEmpresa])) {
					$fechasEmpresas[$claveEmpresa] = [
						'rif' => $rif,
						'token' => $token,
						'fechas' => []
					];
				}
				
				if (!in_array($fecha, $fechasEmpresas[$claveEmpresa]['fechas'])) {
					$fechasEmpresas[$claveEmpresa]['fechas'][] = $fecha;
				}
			}
			
			// Resetear registros por empresa y fechas
			foreach ($fechasEmpresas as $empresa) {
				$table = $this->obtenerTablaEmpresa($empresa['rif'], $empresa['token']);
				
				if (!$table) {
					continue;
				}
				
				// Crear condición WHERE para las fechas
				$condicionesFecha = [];
				foreach ($empresa['fechas'] as $fecha) {
					$condicionesFecha[] = "c.date = STR_TO_DATE('$fecha', '%Y%m%d')";
				}
				
				if (!empty($condicionesFecha)) {
					$whereFechas = implode(' OR ', $condicionesFecha);
					
					// Resetear registros consolidados (status 2, 3, 4) a pending (status 1)
					$sqlReset = "UPDATE $table c 
								 SET c.status_id = 1 
								 WHERE ($whereFechas) 
								 AND c.status_id IN (2, 3, 4)";
					
					$this->update_massive($sqlReset);
				}
			}
		}

		//OBTENER TODAS LAS EMPRESAS SEGUN SU ID
		public function getEnterprise($id)
		{
			$sql = "SELECT * FROM empresa WHERE id = '$id'";
			$requestEnterprise = $this->select($sql);
			
			return $requestEnterprise;
		}

		//VALIDO LOS MOVIMIENTOS CONSOLIDADOS Y NO CONSOLIDADOS Y QUE COINCIDIERON
		public function validateConciliation($movimientos)
		{			
			$totalCompleto = 0;
			$totalParcial = 0;
			$totalSinCoincidencia = 0;
			
			$json = json_decode($movimientos, true);
	
			foreach ($json['msg'] as $mov) {
				$bank = $mov['bank'];
				$account = $mov['account'];
				$rif = $mov['rif'];
				$token = $mov['token'];
				$refRecibida = $mov['reference'];
				$fechaRecibida = $mov['date'];
				//$montoRecibido = floatval($mov['amount']);
				$montoRecibido = abs(floatval($mov['amount']));
				
				// Obtener reglas desde banco
				$sqlReglas = "SELECT concibanc_monto AS monto, concibanc_reference AS reference 
							  FROM banco b
							  INNER JOIN empresa e ON e.id = b.id_enterprise
							  WHERE b.id_bank = '$bank' 	
							  AND b.account = '$account' 
							  AND e.rif = '$rif'
							  AND e.token = '$token'";
				
				
				
				$reglas = $this->select($sqlReglas);
				
				$reglaMonto = isset($reglas['monto']) ? floatval($reglas['monto']) : null;
				$reglaReferencia = isset($reglas['reference']) ? intval($reglas['reference']) : null;
				
				$sqlTable = "SELECT * FROM empresa WHERE rif = '$rif' AND token = '$token'";
				$requestEnterprise = $this->select($sqlTable);
				$table = $requestEnterprise['table'];

				// Buscar todos los posibles registros existentes con mismo banco, cuenta y fecha
				$sqlBusqueda = "SELECT c.id, c.amount, c.reference, c.date FROM $table c
								INNER JOIN banco b ON b.account = c.account
								WHERE c.date = '$fechaRecibida'
									OR (b.id_bank = '$bank' 
									AND b.account = '$account')";
				
				
				/*$sqlBusqueda = "SELECT * FROM conciliation_donbodegon 
								WHERE date = STR_TO_DATE('$fechaRecibida', '%Y%m%d')
								OR (bank = '$bank' 
								AND account = '$account')";*/
				$registros = $this->select_all($sqlBusqueda);
				
				$mejorCoincidencia = null;
				$maxCoincidencias = 0;
				
				
				
				foreach ($registros as $reg) {
					
					$coincidencias = 0;
					$montoBD = abs(floatval($reg['amount']));
					$diffMonto = abs($montoRecibido - $montoBD);
					
					
					$coincideMonto = ($reglaMonto === null) ? ($montoRecibido == $montoBD) : ($diffMonto <= $reglaMonto);
					if ($coincideMonto) $coincidencias++;
					
					// Verificar referencia
					$refBD = $reg['reference'];
					$coincideReferencia = false;
						
					if ($reglaReferencia === null) {
						$coincideReferencia = ($refRecibida == $refBD);
					} else {
						$ultimosRecibida = substr($refRecibida, -$reglaReferencia);
						$ultimosBD = substr($refBD, -$reglaReferencia);
						$coincideReferencia = ($ultimosRecibida == $ultimosBD);
					}
					
					if ($coincideReferencia) $coincidencias++;
					
					if($reg['date'] == $fechaRecibida) $coincidencias++;
					
					// Verificar fecha (ya está en la condición del SELECT)
					if ($coincidencias > $maxCoincidencias) {
						$maxCoincidencias = $coincidencias;
						$mejorCoincidencia = $reg;
					}
				}
				
				// Actualizar la fila si hubo coincidencias
				if ($mejorCoincidencia) {
					
					$id = $mejorCoincidencia['id'];
					
					if ($maxCoincidencias == 3) {
						$totalCompleto++;
						$status_id = 2; // auto_reconciled
					} elseif ($maxCoincidencias == 2) {				
						$totalParcial++;
						$status_id = 3; // partial_match
					} else {
						$status_id = 1; // no_match
					}
					
					$sqlUpdate = "UPDATE $table
								  SET status_id = ?
								  WHERE id = '$id' AND status_id = 1";
					$valueArray = array($status_id);
					$this->update($sqlUpdate, $valueArray);
				}else{
					$totalSinCoincidencia++;
				}
			}
			
			return [
				'status' => true,
				'completos' => $totalCompleto,
				'parciales' => $totalParcial,
				'sin_coincidencia' => $totalSinCoincidencia
			];
		}

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

	// MÉTODOS AUXILIARES MEJORADOS PARA LA CONCILIACIÓN
	private function agruparMovimientosPorEmpresa($movimientos) {
		$grupos = [];
		
		foreach ($movimientos as $mov) {
			// Validar datos del movimiento
			if (!$this->validarMovimiento($mov)) {
				continue;
			}
			
			$claveEmpresa = $mov['rif'] . '_' . $mov['token'];
			
			if (!isset($grupos[$claveEmpresa])) {
				$grupos[$claveEmpresa] = [
					'datos' => [
						'rif' => $mov['rif'],
						'token' => $mov['token']
					],
					'movimientos' => []
				];
			}
			
			$grupos[$claveEmpresa]['movimientos'][] = $mov;
		}
		
		return $grupos;
	}

	private function procesarMovimientosEmpresa($table, $movimientos) {
		$totalCompleto = 0;
		$totalParcial = 0;
		$totalSinCoincidencia = 0;
		
		// Obtener todos los registros candidatos de una sola vez
		$registrosCandidatos = $this->obtenerRegistrosCandidatosOptimizado($table, $movimientos);
		
		// Crear índices para búsqueda rápida
		$indiceRegistros = $this->crearIndiceRegistros($registrosCandidatos);
		
		foreach ($movimientos as $mov) {
			$bank = $mov['bank'];
			$account = $mov['account'];
			$refRecibida = $mov['reference'];
			$fechaRecibida = $mov['date'];
			$montoRecibido = abs(floatval($mov['amount']));
			
			// Buscar mejor coincidencia usando el índice
			$mejorCoincidencia = $this->encontrarMejorCoincidenciaOptimizada(
				$indiceRegistros, 
				$montoRecibido, 
				$refRecibida, 
				$fechaRecibida,
				$account
			);
			
			// Procesar resultado
			if ($mejorCoincidencia['registro']) {
				$coincidencias = $mejorCoincidencia['coincidencias'];
				
				if ($coincidencias == 3) {
					$totalCompleto++;
					$status_id = 2; // auto_reconciled
				} elseif ($coincidencias >= 2) {
					$totalParcial++;
					$status_id = 3; // partial_match
				} else {
					$totalSinCoincidencia++;
					$status_id = 1; // no_match
				}
				
				// Actualizar registro y removerlo del índice para evitar duplicados
				$this->actualizarStatusRegistro($table, $mejorCoincidencia['registro']['id'], $status_id);
				$this->removerRegistroDelIndice($indiceRegistros, $mejorCoincidencia['registro']['id']);
			} else {
				$totalSinCoincidencia++;
			}
		}
		
		return [
			'completos' => $totalCompleto,
			'parciales' => $totalParcial,
			'sin_coincidencia' => $totalSinCoincidencia
		];
	}

	private function obtenerRegistrosCandidatosOptimizado($table, $movimientos) {
		// Extraer fechas y cuentas únicas
		$fechasFormateadas = [];
		$cuentas = [];
		
		foreach ($movimientos as $mov) {
			// Convertir fecha de YYYYMMDD a YYYY-MM-DD para la consulta
			$fechaFormateada = substr($mov['date'], 0, 4) . '-' . substr($mov['date'], 4, 2) . '-' . substr($mov['date'], 6, 2);
			$fechasFormateadas[] = "'" . $fechaFormateada . "'";
			$cuentas[] = "'" . $mov['account'] . "'";
		}
		
		$fechasStr = implode(',', array_unique($fechasFormateadas));
		$cuentasStr = implode(',', array_unique($cuentas));
		
		// Consulta simplificada compatible con MariaDB
		$sqlBusqueda = "SELECT c.id, c.amount, c.reference, c.date, c.status_id, c.account
						FROM $table c
						WHERE c.status_id = 1
						AND (
							DATE(c.date) IN ($fechasStr)
							OR c.account IN ($cuentasStr)
						)
						ORDER BY c.date DESC, c.id ASC";
	
		return $this->select_all($sqlBusqueda);
	}

	private function crearIndiceRegistros($registros) {
		$indice = [
			'por_fecha' => [],
			'por_cuenta' => [],
			'por_referencia' => [],
			'por_monto' => [],
			'todos' => []
		];
		
		foreach ($registros as $reg) {
			$id = $reg['id'];
			$indice['todos'][$id] = $reg;
			
			// Índice por fecha
			$fechaFormateada = $this->formatearFechaParaComparacion($reg['date']);
			if ($fechaFormateada) {
				$indice['por_fecha'][$fechaFormateada][] = $id;
			}
			
			// Índice por cuenta
			$indice['por_cuenta'][$reg['account']][] = $id;
			
			// Índice por referencia
			$indice['por_referencia'][$reg['reference']][] = $id;
			
			// Índice por monto
			$montoAbs = abs(floatval($reg['amount']));
			$indice['por_monto'][$montoAbs][] = $id;
		}
		
		return $indice;
	}

	private function encontrarMejorCoincidenciaOptimizada($indiceRegistros, $montoRecibido, $refRecibida, $fechaRecibida, $cuentaRecibida) {
		$candidatos = [];
		
		// Buscar candidatos por fecha exacta (mayor prioridad)
		if (isset($indiceRegistros['por_fecha'][$fechaRecibida])) {
			foreach ($indiceRegistros['por_fecha'][$fechaRecibida] as $id) {
				$candidatos[$id] = ($candidatos[$id] ?? 0) + 3; // Mayor peso para fecha
			}
		}
		
		// Buscar candidatos por monto exacto
		if (isset($indiceRegistros['por_monto'][$montoRecibido])) {
			foreach ($indiceRegistros['por_monto'][$montoRecibido] as $id) {
				$candidatos[$id] = ($candidatos[$id] ?? 0) + 2; // Peso medio para monto
			}
		}
		
		// Buscar candidatos por referencia exacta
		if (isset($indiceRegistros['por_referencia'][$refRecibida])) {
			foreach ($indiceRegistros['por_referencia'][$refRecibida] as $id) {
				$candidatos[$id] = ($candidatos[$id] ?? 0) + 2; // Peso medio para referencia
			}
		}
		
		// Buscar candidatos por cuenta
		if (isset($indiceRegistros['por_cuenta'][$cuentaRecibida])) {
			foreach ($indiceRegistros['por_cuenta'][$cuentaRecibida] as $id) {
				$candidatos[$id] = ($candidatos[$id] ?? 0) + 1; // Menor peso para cuenta
			}
		}
		
		// Si no hay candidatos preseleccionados, evaluar todos
		if (empty($candidatos)) {
			foreach ($indiceRegistros['todos'] as $id => $reg) {
				$candidatos[$id] = 0;
			}
		}
		
		$mejorCoincidencia = null;
		$maxCoincidencias = 0;
		
		// Evaluar candidatos
		foreach ($candidatos as $id => $pesoInicial) {
			if (!isset($indiceRegistros['todos'][$id])) {
				continue; // Registro ya procesado
			}
			
			$reg = $indiceRegistros['todos'][$id];
			$coincidencias = $this->calcularCoincidenciasExactas($reg, $montoRecibido, $refRecibida, $fechaRecibida);
			
			// Usar peso inicial como desempate
			$puntuacionTotal = $coincidencias * 10 + $pesoInicial;
			
			if ($coincidencias > $maxCoincidencias || 
				($coincidencias == $maxCoincidencias && $puntuacionTotal > ($maxCoincidencias * 10))) {
				$maxCoincidencias = $coincidencias;
				$mejorCoincidencia = $reg;
			}
		}
		
		return [
			'registro' => $mejorCoincidencia,
			'coincidencias' => $maxCoincidencias
		];
	}

	private function calcularCoincidenciasExactas($reg, $montoRecibido, $refRecibida, $fechaRecibida) {
		$coincidencias = 0;
		
		// Verificar monto (comparación exacta)
		$montoBD = abs(floatval($reg['amount']));
		if ($montoRecibido == $montoBD) {
			$coincidencias++;
		}
		
		// Verificar referencia (comparación exacta)
		$refBD = $reg['reference'];
		if ($refRecibida == $refBD) {
			$coincidencias++;
		}
		
		// Verificar fecha (mejorada)
		$fechaFormateada = $this->formatearFechaParaComparacion($reg['date']);
		if ($fechaFormateada && $fechaFormateada == $fechaRecibida) {
			$coincidencias++;
		}
		
		return $coincidencias;
	}

	private function formatearFechaParaComparacion($fechaBD) {
		// Intentar múltiples formatos de fecha
		$formatos = ['Y-m-d', 'Y-m-d H:i:s', 'd/m/Y', 'm/d/Y'];
		
		foreach ($formatos as $formato) {
			$fecha = DateTime::createFromFormat($formato, $fechaBD);
			if ($fecha !== false) {
				return $fecha->format('Ymd');
			}
		}
		
		// Si es timestamp
		if (is_numeric($fechaBD)) {
			return date('Ymd', $fechaBD);
		}
		
		return null;
	}

	private function removerRegistroDelIndice(&$indiceRegistros, $id) {
		// Remover de todos los índices
		unset($indiceRegistros['todos'][$id]);
		
		foreach ($indiceRegistros['por_fecha'] as &$ids) {
			$ids = array_filter($ids, function($regId) use ($id) {
				return $regId != $id;
			});
		}
		
		foreach ($indiceRegistros['por_cuenta'] as &$ids) {
			$ids = array_filter($ids, function($regId) use ($id) {
				return $regId != $id;
			});
		}
		
		foreach ($indiceRegistros['por_referencia'] as &$ids) {
			$ids = array_filter($ids, function($regId) use ($id) {
				return $regId != $id;
			});
		}
		
		foreach ($indiceRegistros['por_monto'] as &$ids) {
			$ids = array_filter($ids, function($regId) use ($id) {
				return $regId != $id;
			});
		}
	}

		private function buscarRegistroConReglas($table, $bank, $account, $fechaRecibida, $montoRecibido, $refRecibida) {

			// Obtener reglas de diferencial y referencia usando el método existente
			$reglas = $this->obtenerReglasBanco($bank, $account, '', '');
			$diferencialBs = isset($reglas['monto']) ? floatval($reglas['monto']) : 0;
			$digitosReferencia = isset($reglas['referencia']) ? intval($reglas['referencia']) : 0;
			
			// Formatear fecha - detectar formato automáticamente
			$fechaFormateada = $fechaRecibida;
			if (strlen($fechaRecibida) == 8 && is_numeric($fechaRecibida)) {
				// Formato YYYYMMDD -> YYYY-MM-DD
				$fechaFormateada = substr($fechaRecibida, 0, 4) . '-' . substr($fechaRecibida, 4, 2) . '-' . substr($fechaRecibida, 6, 2);
			} elseif (strpos($fechaRecibida, '/') !== false) {
				// Formato YYYY/MM/DD -> YYYY-MM-DD
				$fechaFormateada = str_replace('/', '-', $fechaRecibida);
			}
			// Si ya está en formato YYYY-MM-DD, se mantiene igual
			
			// Construir condiciones de búsqueda
			$condicionMonto = "";
			if ($diferencialBs > 0) {
				$montoMin = $montoRecibido - $diferencialBs;
				$montoMax = $montoRecibido + $diferencialBs;
				$condicionMonto = "CASE WHEN ABS(c.amount) BETWEEN $montoMin AND $montoMax THEN 1 ELSE 0 END";
			} else {
				// Sin reglas de diferencial, comparación exacta (0 puntos si no coincide exactamente)
				$condicionMonto = "CASE WHEN ABS(c.amount) = $montoRecibido THEN 1 ELSE 0 END";
			}
			
			$condicionReferencia = "";
			if ($digitosReferencia > 0) {
				$ultimosDigitos = substr($refRecibida, -$digitosReferencia);
				$condicionReferencia = "CASE WHEN RIGHT(c.reference, $digitosReferencia) = '$ultimosDigitos' THEN 1 ELSE 0 END";
			} else {
				$condicionReferencia = "CASE WHEN c.reference = '$refRecibida' THEN 1 ELSE 0 END";
			}
			
			// Consulta SQL con sistema de puntuación (al menos 2 de 3 coincidencias)
			$sqlBusqueda = "SELECT c.id, c.amount, c.reference, c.date, c.status_id, c.account,
							(CASE WHEN DATE(c.date) = '$fechaFormateada' THEN 1 ELSE 0 END) as coincide_fecha,
							($condicionMonto) as coincide_monto,
							($condicionReferencia) as coincide_referencia,
							((CASE WHEN DATE(c.date) = '$fechaFormateada' THEN 1 ELSE 0 END) + 
							($condicionMonto) + 
							($condicionReferencia)) as puntuacion_total
							FROM $table c
							WHERE c.status_id = 1
							AND c.account = '$account'
							HAVING puntuacion_total >= 2
							ORDER BY puntuacion_total DESC, c.date DESC, c.id ASC
							LIMIT 1";

			return $this->select($sqlBusqueda);
		}

	// NUEVA FUNCIÓN PARA EVALUAR CONCILIACIÓN SEGÚN LAS REGLAS ESPECIFICADAS
	private function evaluarConciliacion($table, $bank, $account, $fechaRecibida, $montoRecibido, $refRecibida, $rif, $token) {
		// Obtener reglas de diferencial y referencia
		$reglas = $this->obtenerReglasBanco($bank, $account, $rif, $token);
		$diferencialBs = isset($reglas['monto']) ? floatval($reglas['monto']) : null;
		$digitosReferencia = isset($reglas['referencia']) ? intval($reglas['referencia']) : null;
		
		// Formatear fecha para comparación
		$fechaFormateada = $this->formatearFechaParaBusqueda($fechaRecibida);
		
		// Buscar registros candidatos
		$sqlBusqueda = "SELECT c.id, c.amount, c.reference, c.date, c.status_id, c.account
						FROM $table c
						WHERE c.status_id = 1
						AND c.account = '$account'
						ORDER BY c.date DESC, c.id ASC";
		
		$registros = $this->select_all($sqlBusqueda);
		
		$mejorCoincidencia = null;
		$mejorStatus = 1; // Por defecto no_match
		
		foreach ($registros as $reg) {
			// Evaluar cada tipo de coincidencia
			$coincideReferencia = $this->evaluarCoincidenciaReferencia($refRecibida, $reg['reference'], $digitosReferencia);
			$coincideMonto = $this->evaluarCoincidenciaMonto($montoRecibido, abs(floatval($reg['amount'])), $diferencialBs);
			$coincideFecha = $this->evaluarCoincidenciaFecha($fechaRecibida, $reg['date']);
			
			// Aplicar reglas de conciliación
			$status = $this->determinarStatusPorCoincidencias($coincideReferencia, $coincideMonto, $coincideFecha);
			
			// Si encontramos una coincidencia válida (estatus 2 o 3), la tomamos
			if ($status == 2 || $status == 3) {
				$mejorCoincidencia = $reg;
				$mejorStatus = $status;
				break; // Tomamos la primera coincidencia válida
			}
		}
		
		return [
			'registro' => $mejorCoincidencia,
			'status_id' => $mejorStatus
		];
	}
	
	// EVALUAR COINCIDENCIA DE REFERENCIA CON REGLAS DE DÍGITOS
	private function evaluarCoincidenciaReferencia($refRecibida, $refBD, $digitosReferencia) {
		if ($digitosReferencia === null || $digitosReferencia <= 0) {
			// Sin reglas de dígitos, comparación exacta
			return $refRecibida == $refBD;
		} else {
			// Con reglas de dígitos, comparar últimos N dígitos
			$ultimosRecibida = substr($refRecibida, -$digitosReferencia);
			$ultimosBD = substr($refBD, -$digitosReferencia);
			return $ultimosRecibida == $ultimosBD;
		}
	}
	
	// EVALUAR COINCIDENCIA DE MONTO CON REGLAS DE DIFERENCIAL
	private function evaluarCoincidenciaMonto($montoRecibido, $montoBD, $diferencialBs) {
		if ($diferencialBs === null || $diferencialBs <= 0) {
			// Sin reglas de diferencial, comparación exacta
			return $montoRecibido == $montoBD;
		} else {
			// Con reglas de diferencial, verificar si está dentro del rango
			$diferencia = abs($montoRecibido - $montoBD);
			return $diferencia <= $diferencialBs;
		}
	}
	
	// EVALUAR COINCIDENCIA DE FECHA
	private function evaluarCoincidenciaFecha($fechaRecibida, $fechaBD) {
		$fechaFormateada = $this->formatearFechaParaBusqueda($fechaRecibida);
		$fechaBDFormateada = $this->formatearFechaParaComparacion($fechaBD);
		return $fechaFormateada == $fechaBDFormateada;
	}
	
	// DETERMINAR ESTATUS SEGÚN LAS REGLAS DE CONCILIACIÓN ESPECIFICADAS
	private function determinarStatusPorCoincidencias($coincideReferencia, $coincideMonto, $coincideFecha) {
		// Reglas según tu especificación:
		// Coincidan:
		// - La referencia es igual, monto igual y fecha igual = estatus 2
		// - La referencia es igual, el monto es igual, la fecha es diferente = estatus 3
		// - La referencia es igual, el monto es diferente la fecha es igual = estatus 3
		
		// No coincidan:
		// - La referencia es igual, el monto y la fecha son diferentes = estatus 1
		// - La referencia es diferente = estatus 1
		
		if (!$coincideReferencia) {
			return 1; // no_match - referencia diferente
		}
		
		if ($coincideReferencia && $coincideMonto && $coincideFecha) {
			return 2; // auto_reconciled - todo coincide
		}
		
		if ($coincideReferencia && ($coincideMonto || $coincideFecha)) {
			return 3; // partial_match - referencia igual + (monto igual O fecha igual)
		}
		
		return 1; // no_match - referencia igual pero monto Y fecha diferentes
	}
	
	// FORMATEAR FECHA PARA BÚSQUEDA EN BD
	private function formatearFechaParaBusqueda($fechaRecibida) {
		if (strlen($fechaRecibida) == 8 && is_numeric($fechaRecibida)) {
			// Formato YYYYMMDD -> YYYY-MM-DD
			return substr($fechaRecibida, 0, 4) . '-' . substr($fechaRecibida, 4, 2) . '-' . substr($fechaRecibida, 6, 2);
		} elseif (strpos($fechaRecibida, '/') !== false) {
			// Formato YYYY/MM/DD -> YYYY-MM-DD
			return str_replace('/', '-', $fechaRecibida);
		}
		// Si ya está en formato YYYY-MM-DD, se mantiene igual
		return $fechaRecibida;
	}

	private function registrarLogMovimientos($movimientos, $tipo) {
		$logFile = 'movimientos.log';
		$fechaActual = date('Y-m-d H:i:s');
		$logEntry = "$fechaActual - $tipo - " . json_encode($movimientos) . "\n";
		file_put_contents($logFile, $logEntry, FILE_APPEND);
	}
}
?>
