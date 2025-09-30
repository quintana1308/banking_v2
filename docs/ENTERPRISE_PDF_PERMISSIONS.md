# Sistema de Permisos PDF por Empresa - Banking ADN

## Descripción
Sistema que permite a los administradores controlar qué empresas pueden subir archivos PDF al sistema. Solo los usuarios con rol de administrador (ID_ROL = 1) pueden gestionar estos permisos.

## Funcionalidades Implementadas

### 1. **Control de Acceso**
- Solo administradores pueden ver y modificar permisos PDF
- Verificación de rol en controlador y vista
- Seguridad por defecto: nuevas empresas NO pueden subir PDFs

### 2. **Interfaz de Administración**
- Campo visual en formulario de edición de empresas
- Switch toggle futurista con colores dinámicos
- Texto que cambia según el estado (Habilitado/Deshabilitado)
- Solo visible para administradores

### 3. **Base de Datos**
- Nueva columna: `pdf_upload_enabled` TINYINT(1) DEFAULT 0
- Script SQL seguro que verifica existencia antes de crear
- Compatibilidad con empresas existentes

## Archivos Modificados

### **Modelo - EnterpriseModel.php**
```php
public function updateEnterprise($id, $name, $bd, $rif, $token, $pdf_upload_enabled = null)
```
- Parámetro opcional para mantener compatibilidad
- Actualización condicional del campo PDF

### **Controlador - Enterprise.php**
```php
// Solo los administradores pueden modificar permisos PDF
$pdf_upload_enabled = null;
if (isset($_POST['pdf_upload_enabled']) && $_SESSION['userData']['ID_ROL'] == 1) {
    $pdf_upload_enabled = (int)$_POST['pdf_upload_enabled'];
}
```
- Verificación de rol de administrador
- Procesamiento seguro del campo

### **Vista - Enterprise/edit.php**
```php
<?php if($_SESSION['userData']['ID_ROL'] == 1): // Solo administradores ?>
```
- Campo solo visible para administradores
- Switch toggle con diseño futurista
- JavaScript para cambio dinámico de estado

## Instalación

### 1. Ejecutar Script SQL
```bash
# Ejecutar el archivo SQL
mysql -u usuario -p nombre_bd < sql_updates/add_pdf_upload_permission.sql
```

### 2. Verificar Permisos de Usuario
Asegúrate de que el usuario tenga rol de administrador:
```sql
SELECT id, name, username, id_rol FROM usuario WHERE id_rol = 1;
```

## Uso del Sistema

### **Para Administradores:**
1. Ir a **Gestión → Empresas**
2. Hacer clic en **Editar** en cualquier empresa
3. Ver la sección **"Permisos de Subida PDF"** al final del formulario
4. Activar/desactivar el switch según necesidad
5. Guardar cambios

### **Estados del Switch:**
- **🔴 Deshabilitado**: La empresa NO puede subir PDFs
- **🟢 Habilitado**: La empresa SÍ puede subir PDFs

## Validación en Subida de Archivos

El sistema ya está integrado con la validación de subida:

```php
// En TransaccionModel.php
public function checkPdfUploadPermission($id_enterprise)
{
    $sql = "SELECT pdf_upload_enabled FROM empresa WHERE id = $id_enterprise AND status = 1";
    $request = $this->select($sql);
    return ($request && isset($request['pdf_upload_enabled']) && $request['pdf_upload_enabled'] == 1);
}
```

```php
// En Transaccion.php (Controller)
$empresaPermisos = $this->model->checkPdfUploadPermission($id_enterprise);
if (!$empresaPermisos) {
    // Error: empresa sin permisos
}
```

## Flujo Completo

```
1. Admin edita empresa → Activa permisos PDF
2. Usuario de esa empresa → Puede subir PDFs
3. Usuario de empresa sin permisos → Recibe error
4. Admin puede cambiar permisos en cualquier momento
```

## Seguridad

- ✅ **Verificación de rol**: Solo administradores
- ✅ **Validación en backend**: Doble verificación
- ✅ **Seguridad por defecto**: Permisos deshabilitados
- ✅ **Interfaz condicional**: Campo solo visible para admins

## Compatibilidad

- ✅ **Empresas existentes**: Mantienen funcionamiento normal
- ✅ **Usuarios no admin**: No ven el campo de permisos
- ✅ **Actualizaciones**: Parámetro opcional en modelo
- ✅ **Base de datos**: Script seguro de migración

## Próximas Mejoras

- [ ] Log de cambios de permisos
- [ ] Permisos por tipo de archivo
- [ ] Interfaz de gestión masiva
- [ ] Notificaciones automáticas
