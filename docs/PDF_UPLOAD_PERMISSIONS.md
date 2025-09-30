# Sistema de Permisos de Subida PDF por Empresa

## Descripción
Sistema que controla qué empresas pueden subir archivos PDF al sistema Banking ADN. Por defecto, ninguna empresa tiene permisos hasta que se habiliten explícitamente.

## Instalación

### 1. Ejecutar Script SQL
```sql
-- Ejecutar en la base de datos
ALTER TABLE `empresa` 
ADD COLUMN `pdf_upload_enabled` TINYINT(1) NOT NULL DEFAULT 0 
COMMENT 'Permite subida de archivos PDF (0=No, 1=Sí)';
```

### 2. Habilitar Empresas Específicas
```sql
-- Habilitar empresa con ID 1
UPDATE empresa SET pdf_upload_enabled = 1 WHERE id = 1;

-- Habilitar múltiples empresas
UPDATE empresa SET pdf_upload_enabled = 1 WHERE id IN (1, 2, 3);
```

## Funcionamiento

### Validación Automática
- Al subir un archivo PDF, el sistema verifica automáticamente los permisos
- Si la empresa NO tiene permisos: se muestra error y se elimina el archivo
- Si la empresa SÍ tiene permisos: continúa el procesamiento normal

### Mensaje de Error
```
"Su empresa no tiene permisos para subir archivos PDF. Contacte al administrador del sistema."
```

## Gestión de Permisos

### Consultar Estado Actual
```sql
SELECT id, name, pdf_upload_enabled, status 
FROM empresa 
ORDER BY name;
```

### Habilitar Permisos
```sql
UPDATE empresa SET pdf_upload_enabled = 1 WHERE id = [ID_EMPRESA];
```

### Deshabilitar Permisos
```sql
UPDATE empresa SET pdf_upload_enabled = 0 WHERE id = [ID_EMPRESA];
```

### Habilitar Todas las Empresas Activas
```sql
UPDATE empresa SET pdf_upload_enabled = 1 WHERE status = 1;
```

## Seguridad

- **Seguridad por defecto**: Nuevas empresas NO pueden subir PDFs
- **Control granular**: Permiso individual por empresa
- **Validación temprana**: Se verifica antes de procesar el archivo
- **Limpieza automática**: Archivos se eliminan si no hay permisos

## Archivos Modificados

1. **TransaccionModel.php**: Método `checkPdfUploadPermission()`
2. **Transaccion.php**: Validación en `setTransaction()`
3. **Base de datos**: Nueva columna `pdf_upload_enabled`

## Logs y Monitoreo

El sistema registra en logs cuando una empresa intenta subir PDFs sin permisos. Revisar logs del sistema para detectar intentos no autorizados.

## Futuras Mejoras

- Interfaz administrativa para gestionar permisos
- Logs específicos de intentos de subida sin permisos  
- Notificaciones automáticas al administrador
- Permisos por tipo de archivo (PDF, Excel, etc.)
