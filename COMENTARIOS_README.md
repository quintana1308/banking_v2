# Sistema de Comentarios - Banking ADN

## 📋 Configuración de Permisos

### Activar Permisos de Comentarios para Usuarios

Para que un usuario pueda crear comentarios en las transacciones, debe tener la columna `comment = 1` en la tabla `usuario`.

#### Opción 1: Desde la Base de Datos (SQL)

```sql
-- Dar permisos a un usuario específico
UPDATE usuario SET comment = 1 WHERE username = 'nombre_usuario' AND status = 1;

-- Dar permisos a todos los administradores (rol 1)
UPDATE usuario SET comment = 1 WHERE id_rol = 1 AND status = 1;

-- Verificar usuarios con permisos
SELECT id, name, username, id_rol, comment, status 
FROM usuario 
WHERE comment = 1 AND status = 1;
```

#### Opción 2: Desde el Sistema de Gestión de Usuarios

Si tienes un módulo de gestión de usuarios en el sistema, puedes agregar un checkbox para activar/desactivar los permisos de comentarios.

## 🔒 Validaciones de Seguridad

### Backend (PHP)
- ✅ Verificación en `CommentModel::canUserComment()`
- ✅ Validación en `Transaccion::createComment()`
- ✅ Verificación de acceso por empresa
- ✅ Límite de 1000 caracteres
- ✅ Un solo comentario por transacción

### Frontend (JavaScript)
- ✅ Botón de comentario solo visible si `canComment = true`
- ✅ Modal muestra mensaje si no tiene permisos
- ✅ Validación de caracteres en tiempo real

## 🎯 Funcionamiento

### Para Usuarios CON Permisos (`comment = 1`)
1. Ve botón de comentario activo en cada transacción
2. Puede crear comentarios nuevos
3. Puede ver comentarios existentes

### Para Usuarios SIN Permisos (`comment = 0`)
1. Ve ícono deshabilitado (comment-slash)
2. No puede crear comentarios
3. Puede ver comentarios existentes (solo lectura)

## 📊 Estructura de Base de Datos

```sql
-- Tabla usuario (campo agregado)
ALTER TABLE usuario ADD comment INT(11) DEFAULT 0;

-- Tabla de comentarios
CREATE TABLE comment (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  description TEXT
);

-- Tabla de relaciones
CREATE TABLE conciliation_comment (
  id INT(11) AUTO_INCREMENT PRIMARY KEY,
  conciliation_id INT(11),
  empresa_id INT(11),
  comment_id INT(11),
  user_id INT(11),
  created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

## 🚀 Activación Rápida

Para activar rápidamente los permisos de comentarios:

```sql
-- Ejecutar en la base de datos
UPDATE usuario SET comment = 1 WHERE id_rol = 1; -- Administradores
-- O para un usuario específico:
UPDATE usuario SET comment = 1 WHERE id = [ID_DEL_USUARIO];
```

Luego recargar la página del listado de transacciones.
