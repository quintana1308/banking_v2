# 🔐 Sistema de Permisos Banking ADN - Documentación Completa

## 📋 Resumen del Sistema

El sistema de permisos implementado permite controlar el acceso de usuarios basado en roles, con filtros automáticos por empresa para garantizar que cada usuario solo vea los datos correspondientes a las empresas que tiene asignadas.

## 👥 Roles del Sistema

| Rol ID | Nombre | Descripción | Permisos |
|--------|--------|-------------|----------|
| **1** | Administrador | Control total del sistema | ✅ Todas las empresas<br>✅ Todos los módulos<br>✅ Gestión de usuarios<br>✅ Eliminar transacciones |
| **2** | Soportista | Acceso limitado por empresas | ✅ Solo empresas asignadas<br>❌ Gestión de empresas<br>❌ Gestión de usuarios<br>⚠️ Eliminar según permiso |
| **3** | Cliente | Acceso básico por empresas | ✅ Solo empresas asignadas<br>❌ Gestión de empresas<br>❌ Gestión de usuarios<br>⚠️ Eliminar según permiso |

## 🏗️ Arquitectura del Sistema

### 1. Helper Central de Permisos
**Archivo:** `Helpers/PermissionsHelper.php`

```php
// Verificar acceso a módulos
PermissionsHelper::hasModuleAccess('transacciones');

// Obtener empresas del usuario
PermissionsHelper::getUserEnterprises();

// Generar filtros SQL automáticos
PermissionsHelper::getEnterpriseWhereCondition('tabla.id_enterprise');

// Redireccionar si no tiene permisos
PermissionsHelper::requireModuleAccess('empresas');
```

### 2. Funciones Helper Globales
```php
// Disponibles en todas las vistas
hasModuleAccess('modulo');
canViewAllEnterprises();
canDeleteTransactions();
getRoleName();
requireModuleAccess('modulo');
```

## 🔄 Flujo de Autenticación

### Login Process (`LoginModel.php`)
1. **Validación de credenciales** → Usuario y contraseña
2. **Carga de datos básicos** → Información del usuario y rol
3. **Carga de empresas:**
   - **Admin (rol 1):** Empresa principal del campo `id_enterprise`
   - **Roles 2-3:** Múltiples empresas desde tabla `usuario_empresa`
4. **Almacenamiento en sesión** → `$_SESSION['userData']`

### Estructura de Sesión
```php
$_SESSION['userData'] = [
    'ID_ROL' => 1|2|3,
    'USUARIO' => 'username',
    'id_enterprise' => 123,
    'user_enterprises' => [1, 3, 5], // Solo para roles 2-3
    'delete_mov' => 0|1,
    // ... otros campos
];
```

## 🛡️ Restricciones por Módulo

### Dashboard
- **Todos los roles:** ✅ Acceso permitido
- **Filtros:** Datos solo de empresas asignadas (roles 2-3)

### Transacciones
- **Todos los roles:** ✅ Acceso permitido
- **Filtros:** Movimientos solo de empresas asignadas
- **Eliminación:** Según permiso `delete_mov`

### Cuentas Bancarias
- **Todos los roles:** ✅ Acceso permitido
- **Filtros:** Cuentas solo de empresas asignadas

### Empresas
- **Solo Admin:** ✅ Acceso completo
- **Roles 2-3:** ❌ Redirección a `/error/permisos`

### Gestión de Usuarios
- **Solo Admin:** ✅ Acceso completo
- **Roles 2-3:** ❌ Redirección a `/error/permisos`

## 🔍 Filtros Automáticos por Empresa

### Implementación en Modelos
```php
// Ejemplo en HomeModel.php
public function getBank() {
    $whereCondition = PermissionsHelper::getEnterpriseWhereCondition('b.id_enterprise');
    $sql = "SELECT * FROM banco b WHERE $whereCondition";
    return $this->select_all($sql);
}
```

### Condiciones SQL Generadas
- **Admin (rol 1):** `WHERE 1=1` (ve todo)
- **Roles 2-3:** `WHERE tabla.id_enterprise IN (1,3,5)` (solo empresas asignadas)
- **Sin empresas:** `WHERE 1=0` (no ve nada)

## 🎨 Interfaz de Usuario

### Menú Dinámico (`sidebar.php`)
```php
<?php if(hasModuleAccess('empresas')) { ?>
    <li><a href="/enterprise">Empresas</a></li>
<?php } ?>

<?php if(hasModuleAccess('usuarios')) { ?>
    <li><a href="/usuario/usuarios">Gestión de Usuarios</a></li>
<?php } ?>
```

### Página de Error de Permisos
- **Ruta:** `/error/permisos`
- **Vista:** `Views/Errors/permisos.php`
- **Diseño:** Futurista consistente con el sistema

## 🔧 Implementación en Controladores

### Verificación Automática
```php
// En constructor de controladores
public function __construct() {
    parent::__construct();
    
    // Verificar login
    if(empty($_SESSION['login'])) {
        header('Location: '.base_url().'/login');
        exit();
    }
    
    // Verificar permisos del módulo
    requireModuleAccess('nombre_modulo');
}
```

## 📊 Base de Datos

### Tabla `usuario_empresa`
```sql
CREATE TABLE usuario_empresa (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT,
    enterprise_id INT,
    FOREIGN KEY (user_id) REFERENCES usuario(id),
    FOREIGN KEY (enterprise_id) REFERENCES empresa(id)
);
```

### Campos Importantes en `usuario`
- `id_rol`: Rol del usuario (1=Admin, 2=Soportista, 3=Cliente)
- `id_enterprise`: Empresa principal (para admin)
- `delete_mov`: Permiso para eliminar transacciones (0|1)
- `status`: Estado del usuario (1=Activo, 0=Inactivo)

## 🚀 Casos de Uso

### Escenario 1: Usuario Administrador
```php
// Puede acceder a todo
hasModuleAccess('empresas'); // true
hasModuleAccess('usuarios'); // true
canViewAllEnterprises(); // true
getUserEnterprises(); // 'ALL'
```

### Escenario 2: Usuario Soportista
```php
// Acceso limitado
hasModuleAccess('transacciones'); // true
hasModuleAccess('empresas'); // false
canViewAllEnterprises(); // false
getUserEnterprises(); // [1, 3, 5]
```

### Escenario 3: Usuario Cliente
```php
// Acceso básico
hasModuleAccess('transacciones'); // true
hasModuleAccess('usuarios'); // false
canDeleteTransactions(); // Según delete_mov
getUserEnterprises(); // [2]
```

## 🔒 Seguridad Implementada

1. **Verificación en Constructores:** Todos los controladores verifican permisos
2. **Filtros SQL Automáticos:** Previenen acceso no autorizado a datos
3. **Redirección Automática:** Usuarios sin permisos van a página de error
4. **Validación de Empresas:** Solo ve datos de empresas asignadas
5. **Menú Dinámico:** Solo muestra opciones permitidas

## 🛠️ Mantenimiento

### Agregar Nuevo Módulo
1. Definir en `PermissionsHelper::hasModuleAccess()`
2. Agregar verificación en constructor del controlador
3. Actualizar menú en `sidebar.php`

### Cambiar Permisos de Usuario
1. Actualizar `id_rol` en tabla `usuario`
2. Gestionar empresas en tabla `usuario_empresa`
3. Configurar `delete_mov` según necesidades

## ✅ Estado del Sistema

- **✅ Implementación Completa:** Todos los componentes funcionando
- **✅ Seguridad Validada:** Restricciones aplicadas correctamente
- **✅ Interfaz Adaptativa:** Menú y vistas según permisos
- **✅ Filtros Automáticos:** SQL seguro por empresa
- **✅ Documentación:** Sistema completamente documentado

---

**Desarrollado para Banking ADN** - Sistema de Permisos Basado en Roles v1.0
