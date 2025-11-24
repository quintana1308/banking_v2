<?php 

class CommentModel extends Mysql
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Verificar si un usuario puede crear comentarios
     * @param int $userId
     * @return bool
     */
    public function canUserComment($userId)
    {
        $sql = "SELECT comment FROM usuario WHERE id = $userId AND status = 1";
        $request = $this->select($sql);
        return $request && intval($request['comment']) === 1;
    }

    /**
     * Verificar si una transacción ya tiene comentario
     * @param int $conciliationId
     * @param int $empresaId
     * @return array|false
     */
    public function getTransactionComment($conciliationId, $empresaId)
    {
        $sql = "SELECT cc.id, cc.conciliation_id, cc.empresa_id, cc.comment_id, cc.user_id, cc.created_at,
                       c.description, u.name as user_name
                FROM conciliation_comment cc
                INNER JOIN comment c ON cc.comment_id = c.id
                INNER JOIN usuario u ON cc.user_id = u.id
                WHERE cc.conciliation_id = $conciliationId AND cc.empresa_id = $empresaId";
        
        return $this->select($sql);
    }

    /**
     * Obtener información de empresa y transacción
     * @param int $conciliationId
     * @return array|false
     */
    public function getTransactionWithEnterprise($conciliationId)
    {
        // Obtener la empresa actual seleccionada por el usuario
        $userId = $_SESSION['idUser'] ?? 0;
        
        // Obtener el id_enterprise del usuario (empresa actualmente seleccionada)
        $sql = "SELECT id_enterprise FROM usuario WHERE id = $userId AND status = 1";
        $userInfo = $this->select($sql);
        
        if (!$userInfo || !$userInfo['id_enterprise']) {
            return false;
        }
        
        $enterpriseId = intval($userInfo['id_enterprise']);
        
        // Obtener información de la empresa seleccionada
        $enterprise = $this->getEnterpriseInfo($enterpriseId);
        if (!$enterprise || empty($enterprise['table'])) {
            return false;
        }

        $tableName = $enterprise['table'];
        
        // Validar nombre de tabla para prevenir inyección SQL
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $tableName)) {
            return false;
        }

        // Verificar si la transacción existe en la tabla de la empresa seleccionada
        $sql = "SELECT id, bank, account, reference, date, amount FROM `{$tableName}` WHERE id = $conciliationId";
        $transaction = $this->select($sql);
        
        if ($transaction) {
            // Retornar la transacción con la empresa seleccionada
            return [
                'transaction' => array_merge($transaction, ['id_enterprise' => $enterpriseId]),
                'enterprise' => $enterprise
            ];
        }

        return false;
    }


    /**
     * Crear un nuevo comentario
     * @param string $description
     * @return int|false ID del comentario creado
     */
    public function createComment($description)
    {
        $sql = "INSERT INTO comment (description) VALUES (?)";
        $request = $this->insert($sql, [$description]);
        return $request;
    }

    /**
     * Crear relación de comentario con transacción
     * @param int $conciliationId
     * @param int $empresaId
     * @param int $commentId
     * @param int $userId
     * @return int|false
     */
    public function createConciliationComment($conciliationId, $empresaId, $commentId, $userId)
    {
        $sql = "INSERT INTO conciliation_comment (conciliation_id, empresa_id, comment_id, user_id) 
                VALUES (?, ?, ?, ?)";
        
        return $this->insert($sql, [$conciliationId, $empresaId, $commentId, $userId]);
    }


    /**
     * Obtener información de empresa por ID
     * @param int $empresaId
     * @return array|false
     */
    public function getEnterpriseInfo($empresaId)
    {
        $sql = "SELECT id, name, `table` FROM empresa WHERE id = $empresaId AND status = 1";
        return $this->select($sql);
    }

    /**
     * Verificar si el usuario tiene acceso a la empresa de la transacción
     * @param int $userId
     * @param int $empresaId
     * @return bool
     */
    public function userHasAccessToEnterprise($userId, $empresaId)
    {
        // Verificar si es administrador (rol 1)
        $sql = "SELECT id_rol FROM usuario WHERE id = $userId";
        $user = $this->select($sql);
        
        if ($user && intval($user['id_rol']) === 1) {
            return true; // Administradores tienen acceso a todas las empresas
        }

        // Verificar acceso específico a la empresa
        $sql = "SELECT 1 FROM usuario_empresa WHERE user_id = $userId AND enterprise_id = $empresaId";
        $access = $this->select($sql);
        
        return $access !== false;
    }
}
