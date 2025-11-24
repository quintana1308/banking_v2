-- Script para agregar la columna comment a la tabla usuario si no existe
-- y configurar permisos de comentarios

-- Verificar si la columna comment existe, si no, agregarla
ALTER TABLE `usuario` 
ADD COLUMN IF NOT EXISTS `comment` INT(11) DEFAULT 0 
COMMENT 'Permiso para crear comentarios (0=No, 1=Sí)';

-- Actualizar la descripción de la columna si ya existe
ALTER TABLE `usuario` 
MODIFY COLUMN `comment` INT(11) DEFAULT 0 
COMMENT 'Permiso para crear comentarios (0=No, 1=Sí)';

-- Ejemplo: Dar permisos de comentarios a administradores (rol 1)
-- Descomenta la siguiente línea si quieres dar permisos automáticamente a administradores
-- UPDATE `usuario` SET `comment` = 1 WHERE `id_rol` = 1 AND `status` = 1;

-- Ejemplo: Dar permisos de comentarios a un usuario específico
-- Reemplaza 'username' con el nombre de usuario real
-- UPDATE `usuario` SET `comment` = 1 WHERE `username` = 'admin' AND `status` = 1;

-- Verificar usuarios con permisos de comentarios
SELECT id, name, username, id_rol, comment, status 
FROM usuario 
WHERE status = 1 
ORDER BY id_rol, name;
