<?php
/**
 * Configuración de la Plantilla Banking ADN
 * Este archivo contiene la configuración global para el sistema de plantillas
 */

class TemplateConfig {
    
    /**
     * Configuración por defecto de la plantilla
     */
    public static function getDefaultConfig() {
        return [
            'title' => 'Banking ADN',
            'description' => 'Sistema de gestión bancario',
            'theme' => 'dark',
            'language' => 'es',
            'showSidebar' => true,
            'showNavbar' => true,
            'showFooter' => true,
            'showBreadcrumbs' => false,
            'containerClass' => 'container-fluid',
            'contentClass' => 'content-inner mt-n5 py-0',
            'customCSS' => [],
            'customJS' => [],
            'pageJS' => null,
            'breadcrumbs' => [],
            'headerActions' => [],
            'bodyClass' => '',
            'metaTags' => [
                'viewport' => 'width=device-width, initial-scale=1, shrink-to-fit=no',
                'author' => 'Banking ADN Team',
                'robots' => 'index, follow'
            ]
        ];
    }
    
    /**
     * Configuraciones específicas por controlador
     */
    public static function getControllerConfig($controller = null) {
        $configs = [
            'home' => [
                'title' => 'Dashboard - Banking ADN',
                'description' => 'Panel de control principal del sistema bancario',
                'showBreadcrumbs' => false,
                'pageHeader' => [
                    'title' => 'Panel de Control',
                    'description' => 'Resumen general del sistema'
                ]
            ],
            
            'bank' => [
                'title' => 'Cuentas Bancarias - Banking ADN',
                'description' => 'Gestión de cuentas bancarias',
                'showBreadcrumbs' => true,
                'breadcrumbs' => [
                    ['title' => 'Cuentas Bancarias']
                ],
                'pageHeader' => [
                    'title' => 'Cuentas Bancarias',
                    'description' => 'Administra las cuentas bancarias del sistema',
                    'actions' => [
                        [
                            'text' => 'Nueva Cuenta',
                            'url' => base_url() . '/bank/new',
                            'class' => 'btn-primary',
                            'icon' => 'fas fa-plus'
                        ]
                    ]
                ]
            ],
            
            'transaccion' => [
                'title' => 'Movimientos - Banking ADN',
                'description' => 'Gestión de movimientos bancarios',
                'showBreadcrumbs' => true,
                'breadcrumbs' => [
                    ['title' => 'Movimientos']
                ],
                'pageHeader' => [
                    'title' => 'Movimientos Bancarios',
                    'description' => 'Consulta y gestiona los movimientos bancarios',
                    'actions' => [
                        [
                            'text' => 'Subir Movimiento',
                            'url' => base_url() . '/transaccion/newTransaction',
                            'class' => 'btn-primary',
                            'icon' => 'fas fa-upload'
                        ]
                    ]
                ]
            ],
            
            'enterprise' => [
                'title' => 'Empresas - Banking ADN',
                'description' => 'Gestión de empresas',
                'showBreadcrumbs' => true,
                'breadcrumbs' => [
                    ['title' => 'Empresas']
                ],
                'pageHeader' => [
                    'title' => 'Empresas',
                    'description' => 'Administra las empresas del sistema',
                    'actions' => [
                        [
                            'text' => 'Nueva Empresa',
                            'url' => base_url() . '/enterprise/new',
                            'class' => 'btn-primary',
                            'icon' => 'fas fa-building'
                        ]
                    ]
                ]
            ],
            
            'user' => [
                'title' => 'Usuarios - Banking ADN',
                'description' => 'Gestión de usuarios del sistema',
                'showBreadcrumbs' => true,
                'breadcrumbs' => [
                    ['title' => 'Usuarios']
                ],
                'pageHeader' => [
                    'title' => 'Usuarios',
                    'description' => 'Administra los usuarios del sistema',
                    'actions' => [
                        [
                            'text' => 'Nuevo Usuario',
                            'url' => base_url() . '/user/new',
                            'class' => 'btn-primary',
                            'icon' => 'fas fa-user-plus'
                        ]
                    ]
                ]
            ],
            
            'logsession' => [
                'title' => 'Log de Sesiones - Banking ADN',
                'description' => 'Registro de sesiones del sistema',
                'showBreadcrumbs' => true,
                'breadcrumbs' => [
                    ['title' => 'Log de Sesiones']
                ],
                'pageHeader' => [
                    'title' => 'Log de Sesiones',
                    'description' => 'Consulta el registro de sesiones de usuarios'
                ]
            ]
        ];
        
        return $configs[$controller] ?? [];
    }
    
    /**
     * Combina la configuración por defecto con la específica del controlador
     */
    public static function mergeConfig($controller = null, $customConfig = []) {
        $defaultConfig = self::getDefaultConfig();
        $controllerConfig = self::getControllerConfig($controller);
        
        return array_merge($defaultConfig, $controllerConfig, $customConfig);
    }
    
    /**
     * Configuraciones de tema
     */
    public static function getThemeConfig($theme = 'dark') {
        $themes = [
            'dark' => [
                'bodyClass' => 'theme-dark',
                'primaryColor' => '#3b82f6',
                'secondaryColor' => '#64748b'
            ],
            'light' => [
                'bodyClass' => 'theme-light',
                'primaryColor' => '#2563eb',
                'secondaryColor' => '#475569'
            ]
        ];
        
        return $themes[$theme] ?? $themes['dark'];
    }
    
    /**
     * Configuraciones de roles y permisos para la vista
     */
    public static function getRoleConfig($roleId = null) {
        $roles = [
            1 => [ // Administrador
                'name' => 'Administrador',
                'permissions' => ['all'],
                'menuItems' => ['home', 'transaccion', 'bank', 'enterprise', 'user', 'logsession']
            ],
            2 => [ // Supervisor
                'name' => 'Supervisor',
                'permissions' => ['view', 'edit'],
                'menuItems' => ['home', 'enterprise', 'user', 'logsession']
            ],
            3 => [ // Operador
                'name' => 'Operador',
                'permissions' => ['view'],
                'menuItems' => ['home', 'transaccion', 'bank']
            ]
        ];
        
        return $roles[$roleId] ?? $roles[3];
    }
}

/**
 * Función helper para obtener la configuración de la plantilla
 */
function getTemplateConfig($controller = null, $customConfig = []) {
    return TemplateConfig::mergeConfig($controller, $customConfig);
}

/**
 * Función helper para renderizar una vista con la nueva plantilla
 */
function renderTemplate($viewPath, $data = [], $controller = null) {
    // Extraer datos para que estén disponibles en las vistas
    extract($data);
    
    // Obtener configuración de la plantilla
    $templateConfig = getTemplateConfig($controller, $data['templateConfig'] ?? []);
    
    // Capturar el contenido de la vista
    ob_start();
    include $viewPath;
    $content = ob_get_clean();
    
    // Incluir la plantilla principal
    include __DIR__ . '/template.php';
}
?>
