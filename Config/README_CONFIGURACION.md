# Configuración de API Key PDF.co

## Pasos para configurar la API Key:

### 1. Editar el archivo Config.php
Abre el archivo `c:\xampp\htdocs\banking_v2\Config\Config.php` y agrega la siguiente línea:

```php
// Configuración de PDF.co API
const PDFCO_API_KEY = "adnlean.com@gmail.com_SfBGojnr2FuvXSWOHxuu8MzeyudrwopxbuyaxhvbWpUSXreGnto0giAxCbuucJGV";
```

### 2. Si no existe Config.php
Si el archivo Config.php no existe, copia el archivo `Config.example.php` como `Config.php` y edita los valores:

```bash
cp Config.example.php Config.php
```

### 3. Ejemplo de configuración completa
```php
<?php 
// Configuración de PDF.co API
const PDFCO_API_KEY = "adnlean.com@gmail.com_SfBGojnr2FuvXSWOHxuu8MzeyudrwopxbuyaxhvbWpUSXreGnto0giAxCbuucJGV";

// Otras configuraciones existentes...
const DB_HOST = "localhost";
const DB_NAME = "tu_base_datos";
// etc...
?>
```

## Verificación
Una vez configurado, el sistema usará automáticamente la API key desde la configuración en lugar del valor hardcodeado.

## Seguridad
- El archivo Config.php está en .gitignore para proteger las credenciales
- Nunca subas Config.php al repositorio
- Usa Config.example.php como referencia para nuevas instalaciones
