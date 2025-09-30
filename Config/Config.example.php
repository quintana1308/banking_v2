<?php 
// Archivo de configuración de ejemplo para Banking ADN
// Copiar este archivo como Config.php y configurar los valores apropiados

// Configuración de base de datos
const DB_HOST = "localhost";
const DB_NAME = "nombre_base_datos";
const DB_USER = "usuario_db";
const DB_PASSWORD = "password_db";
const DB_CHARSET = "utf8";

// URLs del sistema
const BASE_URL = "http://localhost/banking_v2";
const MEDIA_URL = BASE_URL."/Assets";

// Configuración de PDF.co API
const PDFCO_API_KEY = "tu_api_key_de_pdfco_aqui";

// Otras configuraciones
const COMPANY = "Banking ADN";
const CURRENCY = "VES";

// Configuración de sesión
const SESSION_TIME = 30; // minutos

// Configuración de archivos
const MAX_FILE_SIZE = 10485760; // 10MB en bytes
const ALLOWED_FILE_TYPES = ['pdf', 'xls', 'xlsx', 'txt'];

// Configuración de logs
const ENABLE_LOGS = true;
const LOG_PATH = "logs/";

?>
