-- Optimización de rendimiento para el sistema de comentarios
-- Este script crea índices para mejorar la velocidad de consultas

-- Índice compuesto para conciliation_comment (consulta principal)
CREATE INDEX IF NOT EXISTS idx_conciliation_comment_performance 
ON conciliation_comment (conciliation_id, empresa_id);

-- Índice para usuario_empresa (consulta de permisos)
CREATE INDEX IF NOT EXISTS idx_usuario_empresa_performance 
ON usuario_empresa (user_id, enterprise_id);

-- Índice para usuario (consulta de roles)
CREATE INDEX IF NOT EXISTS idx_usuario_role_enterprise 
ON usuario (id, id_rol, id_enterprise);

-- Estadísticas de optimización
ANALYZE TABLE conciliation_comment;
ANALYZE TABLE usuario_empresa;
ANALYZE TABLE usuario;
