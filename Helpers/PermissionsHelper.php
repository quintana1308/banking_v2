<?php

/**
 * Helper de Permisos - Sistema Banking ADN
 * Maneja toda la lógica de autorización basada en roles
 */

class PermissionsHelper 
{
    // Definición de roles
    const ROLE_ADMIN = 1;
    const ROLE_SUPPORT = 2;
    const ROLE_CLIENT = 3;

    /**
     * Verifica si el usuario tiene acceso a un módulo específico
     */
    public static function hasModuleAccess($module) 
    {
        $userRole = $_SESSION['userData']['ID_ROL'] ?? 0;
        
        switch($module) {
            case 'dashboard':
                return in_array($userRole, [self::ROLE_ADMIN, self::ROLE_SUPPORT, self::ROLE_CLIENT]);
            
            case 'transacciones':
                return in_array($userRole, [self::ROLE_ADMIN, self::ROLE_SUPPORT, self::ROLE_CLIENT]);
            
            case 'cuentas_bancarias':
                return in_array($userRole, [self::ROLE_ADMIN, self::ROLE_SUPPORT, self::ROLE_CLIENT]);
            
            case 'empresas':
                return $userRole == self::ROLE_ADMIN;
            
            case 'usuarios':
                return $userRole == self::ROLE_ADMIN;
            
            default:
                return false;
        }
    }

    /**
     * Verifica si el usuario puede ver todas las empresas o solo las asignadas
     */
    public static function canViewAllEnterprises() 
    {
        $userRole = $_SESSION['userData']['ID_ROL'] ?? 0;
        return $userRole == self::ROLE_ADMIN;
    }

    /**
     * Obtiene las empresas que puede ver el usuario actual
     */
    public static function getUserEnterprises() 
    {
        $userRole = $_SESSION['userData']['ID_ROL'] ?? 0;
        $userId = $_SESSION['idUser'] ?? 0;
        
        if ($userRole == self::ROLE_ADMIN) {
            // Admin puede ver todas las empresas
            return 'ALL';
        }
        
        // Para roles 2 y 3, obtener empresas desde usuario_empresa
        return $_SESSION['userData']['user_enterprises'] ?? [];
    }

    /**
     * Genera la condición WHERE para filtrar por empresas del usuario
     */
    public static function getEnterpriseWhereCondition($tableAlias = '') 
    {
        $userRole = $_SESSION['userData']['ID_ROL'] ?? 0;
        
        if ($userRole == self::ROLE_ADMIN) {
            return '1=1'; // Admin ve todo
        }
        
        $enterprises = self::getUserEnterprises();
        if (empty($enterprises)) {
            return '1=0'; // No tiene empresas asignadas
        }
        
        $enterpriseIds = implode(',', array_map('intval', $enterprises));
        $column = $tableAlias ? $tableAlias . '.id_enterprise' : 'id_enterprise';
        
        return "$column IN ($enterpriseIds)";
    }

    /**
     * Verifica si el usuario puede eliminar transacciones
     */
    public static function canDeleteTransactions() 
    {
        $userRole = $_SESSION['userData']['ID_ROL'] ?? 0;
        $deleteMovPermission = $_SESSION['userData']['delete_mov'] ?? 0;
        
        // Admin siempre puede eliminar
        if ($userRole == self::ROLE_ADMIN) {
            return true;
        }
        
        // Para otros roles, verificar el permiso específico
        return $deleteMovPermission == 1;
    }

    /**
     * Verifica si el usuario puede acceder a una empresa específica
     */
    public static function canAccessEnterprise($enterpriseId) 
    {
        $userRole = $_SESSION['userData']['ID_ROL'] ?? 0;
        
        if ($userRole == self::ROLE_ADMIN) {
            return true; // Admin puede acceder a todas
        }
        
        $userEnterprises = self::getUserEnterprises();
        return in_array($enterpriseId, $userEnterprises);
    }

    /**
     * Redirecciona si el usuario no tiene permisos
     */
    public static function requireModuleAccess($module) 
    {
        if (!self::hasModuleAccess($module)) {
            header('Location: ' . base_url() . '/error/permisos');
            exit;
        }
    }

    /**
     * Obtiene el nombre del rol del usuario
     */
    public static function getRoleName($roleId = null) 
    {
        if ($roleId === null) {
            $roleId = $_SESSION['userData']['ID_ROL'] ?? 0;
        }
        
        switch($roleId) {
            case self::ROLE_ADMIN:
                return 'Administrador';
            case self::ROLE_SUPPORT:
                return 'Soportista';
            case self::ROLE_CLIENT:
                return 'Cliente';
            default:
                return 'Usuario';
        }
    }

    /**
     * Verifica si el usuario puede ver el listado de empresas en el dashboard
     */
    public static function canViewEnterpriseList() 
    {
        $userRole = $_SESSION['userData']['ID_ROL'] ?? 0;
        return $userRole == self::ROLE_ADMIN;
    }

    /**
     * Obtiene los IDs de empresas como array para consultas IN
     */
    public static function getEnterpriseIdsArray() 
    {
        $userRole = $_SESSION['userData']['ID_ROL'] ?? 0;
        
        if ($userRole == self::ROLE_ADMIN) {
            return null; // Indica que puede ver todas
        }
        
        $enterprises = self::getUserEnterprises();
        return array_map('intval', $enterprises);
    }
}

/**
 * Funciones helper globales para usar en las vistas
 */

function hasModuleAccess($module) {
    return PermissionsHelper::hasModuleAccess($module);
}

function canViewAllEnterprises() {
    return PermissionsHelper::canViewAllEnterprises();
}

function canDeleteTransactions() {
    return PermissionsHelper::canDeleteTransactions();
}

function getUserEnterprises() {
    return PermissionsHelper::getUserEnterprises();
}

function requireModuleAccess($module) {
    PermissionsHelper::requireModuleAccess($module);
}

function getRoleName($roleId = null) {
    return PermissionsHelper::getRoleName($roleId);
}

function canViewEnterpriseList() {
    return PermissionsHelper::canViewEnterpriseList();
}

?>
