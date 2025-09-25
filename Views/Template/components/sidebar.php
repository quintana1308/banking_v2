<?php
/**
 * Componente SIDEBAR Futurista - Menú lateral de navegación con estilo innovador
 */

// Obtener la ruta actual para marcar el elemento activo
$currentRoute = strtok($_SERVER['REQUEST_URI'], '?');
$currentController = explode('/', trim($currentRoute, '/'))[0] ?? 'home';
?>

<!-- Overlay para móviles -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<!-- Botón de colapso para escritorio -->
<button class="sidebar-collapse-btn d-none d-lg-flex" id="sidebarCollapseBtn" title="Contraer/Expandir Sidebar">
    <i class="fas fa-angle-left"></i>
</button>

<!-- Sidebar Futurista -->
<aside class="futuristic-sidebar glass-effect" id="sidebar">
    <!-- Header del Sidebar -->
    <div class="sidebar-header">
        <a href="<?= base_url() ?>/" class="sidebar-brand text-decoration-none">
            <div class="brand-icon">
                <i class="fas fa-dna"></i>
            </div>
            <div class="brand-info">
                <h4 class="brand-title text-gradient">BANKING ADN</h4>
                <p class="brand-subtitle">Sistema Bancario</p>
            </div>
        </a>
        <button class="sidebar-close-btn d-lg-none" id="sidebarClose">
            <i class="fas fa-times"></i>
        </button>
    </div>

    <!-- Contenido del Sidebar -->
    <div class="sidebar-content">
        
        <!-- Sección Principal -->
        <div class="sidebar-section">
            <div class="sidebar-section-title">
                <i class="fas fa-tachometer-alt me-2"></i>
                Navegación Principal
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="<?= base_url() ?>" class="sidebar-menu-link <?= $currentController == 'home' ? 'active' : '' ?>" data-tooltip="Dashboard">
                        <div class="menu-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <span class="menu-text">Dashboard</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= base_url() ?>/transaccion" class="sidebar-menu-link <?= $currentController == 'transaccion' ? 'active' : '' ?>" data-tooltip="Transacciones">
                        <div class="menu-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <span class="menu-text">Transacciones</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= base_url() ?>/bank" class="sidebar-menu-link <?= $currentController == 'bank' ? 'active' : '' ?>" data-tooltip="Cuentas Bancarias">
                        <div class="menu-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <span class="menu-text">Cuentas Bancarias</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
                <?php if(hasModuleAccess('empresas')) { ?>
                <li class="sidebar-menu-item">
                    <a href="<?= base_url() ?>/enterprise" class="sidebar-menu-link <?= $currentController == 'enterprise' ? 'active' : '' ?>" data-tooltip="Empresas">
                        <div class="menu-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <span class="menu-text">Empresas</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
                <?php } ?>
            </ul>
        </div>

        <!-- Sección de Herramientas -->
       <!-- <div class="sidebar-section">
            <div class="sidebar-section-title">
                <i class="fas fa-tools me-2"></i>
                Herramientas
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="#" class="sidebar-menu-link" onclick="exportData()">
                        <div class="menu-icon">
                            <i class="fas fa-download"></i>
                        </div>
                        <span class="menu-text">Exportar Datos</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
                
            </ul>
        </div>-->

        <!-- Sección de Administración (solo para admin) -->
        <?php if(hasModuleAccess('usuarios')) { ?>
        <div class="sidebar-section">
            <div class="sidebar-section-title">
                <i class="fas fa-shield-alt me-2"></i>
                Administración
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="<?= base_url() ?>/usuario/usuarios" class="sidebar-menu-link <?= $currentController == 'usuario' ? 'active' : '' ?>" data-tooltip="Gestión de Usuarios">
                        <div class="menu-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="menu-text">Gestión de Usuarios</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
            </ul>
        </div>
        <?php } ?>

        <!-- Información del Usuario -->
        <div class="sidebar-user-info">
            <a href="<?= base_url() ?>/usuario/perfil" class="user-card glass-effect w-100 text-decoration-none" data-tooltip="Perfil de Usuario">
                <div class="user-card-content w-100 d-flex align-items-center">
                    <div class="user-avatar-sidebar">
                        <?= strtoupper(substr($_SESSION['userData']['USUARIO'] ?? 'U', 0, 2)) ?>
                    </div>
                    <div class="user-details flex-grow-1 ps-2">
                        <div class="user-name-sidebar"><?= $_SESSION['userData']['USUARIO'] ?? 'Usuario' ?></div>
                        <div class="user-role-sidebar">
                            <?php 
                            $rol = $_SESSION['userData']['ID_ROL'] ?? 0;
                            echo $rol == 1 ? 'Administrador' : ($rol == 2 ? 'Soportista' : 'Cliente');
                            ?>
                        </div>
                    </div>
                    <div class="user-status d-flex align-items-center">
                        <div class="status-indicator online"></div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Botón de Cerrar Sesión -->
        <div class="sidebar-footer">
            <a href="#" class="logout-btn" onclick="confirmLogout(event)" data-tooltip="Cerrar Sesión">
                <i class="fas fa-sign-out-alt me-2"></i>
                <span>Cerrar Sesión</span>
            </a>
        </div>
    </div>
</aside>

<style>
/* Botón de colapso para escritorio */
.sidebar-collapse-btn {
    position: fixed;
    top: 20px;
    left: 290px;
    width: 32px;
    height: 32px;
    background: rgba(102, 126, 234, 0.3);
    backdrop-filter: blur(15px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 8px;
    color: rgba(255, 255, 255, 0.7);
    z-index: 1001;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    font-size: 0.9rem;
    opacity: 0.6;
}

.sidebar-collapse-btn:hover {
    background: rgba(102, 126, 234, 0.6);
    color: white;
    opacity: 1;
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

/* Posición del botón cuando sidebar está contraído */
.futuristic-sidebar.collapsed ~ .sidebar-collapse-btn {
    left: 80px;
}

/* Estilos específicos para el sidebar futurista */
.futuristic-sidebar {
    position: fixed;
    top: 0;
    left: 0;
    width: 280px;
    height: 100vh;
    backdrop-filter: blur(20px);
    border-right: 1px solid var(--glass-border);
    z-index: 999;
    overflow-y: auto;
    transition: all 0.3s ease;
}

/* Estado contraído del sidebar */
.futuristic-sidebar.collapsed {
    width: 70px;
}

.futuristic-sidebar.collapsed .brand-info,
.futuristic-sidebar.collapsed .menu-text,
.futuristic-sidebar.collapsed .sidebar-section-title,
.futuristic-sidebar.collapsed .user-details,
.futuristic-sidebar.collapsed .logout-btn span {
    opacity: 0;
    visibility: hidden;
    width: 0;
    overflow: hidden;
    transition: all 0.3s ease;
}

.futuristic-sidebar.collapsed .sidebar-menu-link {
    justify-content: center;
    padding: 0.75rem 0.5rem;
    margin: 0.25rem 0;
    width: 100%;
    display: -webkit-box;
    align-items: center;
}

.futuristic-sidebar.collapsed .menu-icon {
    margin: 0;
    width: 40px;
    height: 40px;
    min-width: 40px;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.futuristic-sidebar.collapsed .menu-icon i {
    font-size: 1.1rem;
    width: auto;
    height: auto;
    flex-shrink: 0;
}

.futuristic-sidebar.collapsed .menu-indicator {
    display: none;
}

.futuristic-sidebar.collapsed .user-card {
    justify-content: center;
    padding: 0.5rem;
    width: 100%;
    display: flex;
    align-items: center;
    margin: 0.25rem 0;
}

.futuristic-sidebar.collapsed .user-avatar-sidebar {
    margin: 0;
    width: 33px;
    height: 33px;
    min-width: 33px;
    min-height: 33px;
    flex-shrink: 0;
    font-size: 0.8rem;
}

.futuristic-sidebar.collapsed .logout-btn {
    justify-content: center;
    padding: 0px;
    width: 100%;
    display: flex;
    align-items: center;
    margin: 0.25rem 0;
    border-radius: 8px;
}

.futuristic-sidebar.collapsed .logout-btn i {
    margin: 0 !important;
    width: 40px;
    height: 40px;
    min-width: 40px;
    min-height: 40px;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
    font-size: 1.1rem;
}


/* Separador visual para secciones en modo contraído */
.futuristic-sidebar.collapsed .sidebar-section {
    position: relative;
}

.futuristic-sidebar.collapsed .sidebar-section:not(:first-child)::before {
    content: '';
    position: absolute;
    top: -1rem;
    left: 50%;
    transform: translateX(-50%);
    width: 30px;
    height: 1px;
    background: var(--glass-border);
    opacity: 0.5;
}

/* Ajustes adicionales para modo contraído */
.futuristic-sidebar.collapsed .sidebar-header {
    padding: 2rem 0.5rem 1rem;
    justify-content: center;
}

.futuristic-sidebar.collapsed .sidebar-brand {
    justify-content: center;
    display: contents;
}

.futuristic-sidebar.collapsed .sidebar-section {
    padding: 0 0.25rem;
}

.futuristic-sidebar.collapsed .sidebar-user-info {
    padding: 1rem 0.5rem;
}

.futuristic-sidebar.collapsed .sidebar-footer {
    padding: 1rem 0.5rem;
    border-top: none;
}

/* Tooltips para modo contraído */
.futuristic-sidebar.collapsed .sidebar-menu-link,
.futuristic-sidebar.collapsed .user-card,
.futuristic-sidebar.collapsed .logout-btn {
    position: relative;
}

.futuristic-sidebar.collapsed .sidebar-menu-link:hover::after,
.futuristic-sidebar.collapsed .user-card:hover::after,
.futuristic-sidebar.collapsed .logout-btn:hover::after {
    content: attr(data-tooltip);
    position: absolute;
    left: 100%;
    top: 50%;
    transform: translateY(-50%);
    background: var(--glass-bg);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border);
    color: var(--text-primary);
    padding: 0.5rem 1rem;
    border-radius: 8px;
    white-space: nowrap;
    z-index: 1002;
    margin-left: 10px;
    font-size: 0.9rem;
    box-shadow: var(--shadow-glow);
}

.sidebar-header {
    padding: 2rem 1.5rem 1rem;
    border-bottom: 1px solid var(--glass-border);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.sidebar-brand {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.brand-icon {
    width: 45px;
    height: 45px;
    background: var(--accent-gradient);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.brand-title {
    font-family: 'Orbitron', monospace;
    font-size: 1.1rem;
    font-weight: 900;
    margin: 0;
}

.brand-subtitle {
    font-size: 0.8rem;
    color: var(--text-secondary);
    margin: 0;
}

.sidebar-close-btn {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    color: var(--text-primary);
    width: 35px;
    height: 35px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.sidebar-close-btn:hover {
    background: var(--secondary-gradient);
    color: white;
}


.sidebar-content {
    padding: 1rem 0;
    height: calc(100% - 100px);
    display: flex;
    flex-direction: column;
}

.sidebar-section {
    margin-bottom: 2rem;
    padding: 0 1rem;
}

.sidebar-section-title {
    font-size: 0.8rem;
    color: var(--text-secondary);
    text-transform: uppercase;
    letter-spacing: 1px;
    margin-bottom: 1rem;
    padding: 0 0.5rem;
    font-weight: 600;
    display: flex;
    align-items: center;
}

.sidebar-menu {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-menu-item {
    margin-bottom: 0.5rem;
}

.sidebar-menu-link {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    color: var(--text-secondary);
    text-decoration: none;
    border-radius: 12px;
    transition: all 0.3s ease;
    position: relative;
    overflow: hidden;
}

.sidebar-menu-link:hover,
.sidebar-menu-link.active {
    background: rgba(102, 126, 234, 0.1);
    color: var(--text-primary);
    transform: translateX(5px);
}

/* Quitar efectos hover cuando sidebar está contraído */
.futuristic-sidebar.collapsed .sidebar-menu-link:hover {
    transform: none;
    background: none;
}

.sidebar-menu-link.active {
    background: var(--primary-gradient);
    color: white;
    box-shadow: var(--shadow-glow);
}

.menu-icon {
    width: 35px;
    height: 35px;
    display: flex;
    align-items: center;
    justify-content: center;
    background: var(--glass-bg);
    border-radius: 8px;
    transition: all 0.3s ease;
}

.sidebar-menu-link.active .menu-icon {
    background: rgba(255, 255, 255, 0.2);
}

.menu-text {
    flex: 1;
    font-weight: 500;
}

.menu-indicator {
    width: 3px;
    height: 20px;
    background: var(--accent-gradient);
    border-radius: 2px;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.sidebar-menu-link.active .menu-indicator {
    opacity: 1;
}

.sidebar-user-info {
    margin-top: auto;
    padding: 1rem;
}

.user-card {
    padding: 1rem;
    border-radius: 15px;
    display: flex;
    align-items: center;
    gap: 1rem;
}

.user-avatar-sidebar {
    width: 45px;
    height: 45px;
    background: var(--accent-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1rem;
    transition: all 0.3s ease;
}

.user-details {
    flex: 1;
}

.user-name-sidebar {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
    margin-bottom: 0.2rem;
}

.user-role-sidebar {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.user-status {
    display: flex;
    align-items: center;
}

.status-indicator {
    width: 8px;
    height: 8px;
    border-radius: 50%;
    background: #00ff88;
    box-shadow: 0 0 10px #00ff88;
}

.status-indicator.online {
    animation: pulse 2s infinite;
}

@keyframes pulse {
    0% { box-shadow: 0 0 0 0 rgba(0, 255, 136, 0.7); }
    70% { box-shadow: 0 0 0 10px rgba(0, 255, 136, 0); }
    100% { box-shadow: 0 0 0 0 rgba(0, 255, 136, 0); }
}

.sidebar-footer {
    padding: 1rem;
    border-top: 1px solid var(--glass-border);
}

.logout-btn {
    display: flex;
    align-items: center;
    justify-content: center;
    width: 100%;
    padding: 1rem;
    background: var(--secondary-gradient);
    color: white;
    text-decoration: none;
    border-radius: 12px;
    font-weight: 500;
    transition: all 0.3s ease;
}

.logout-btn:hover {
    transform: scale(1.02);
    box-shadow: var(--shadow-glow);
    color: white;
}

/* Quitar efectos hover del botón logout cuando sidebar está contraído */
.futuristic-sidebar.collapsed .logout-btn:hover {
    transform: none;
    box-shadow: none;
    background: var(--secondary-gradient);
}

/* Quitar efectos hover del avatar de usuario cuando sidebar está contraído */
.futuristic-sidebar.collapsed .user-card:hover {
    transform: none;
    box-shadow: none;
    background: none;
}

/* Responsive */
@media (max-width: 768px) {
    /* Ocultar botón de colapso en móvil */
    .sidebar-collapse-btn {
        display: none !important;
    }
    
    /* Resetear estado collapsed en móvil */
    .futuristic-sidebar.collapsed {
        width: 280px;
    }
    
    .futuristic-sidebar.collapsed .brand-info,
    .futuristic-sidebar.collapsed .menu-text,
    .futuristic-sidebar.collapsed .sidebar-section-title,
    .futuristic-sidebar.collapsed .user-details,
    .futuristic-sidebar.collapsed .logout-btn span {
        opacity: 1;
        visibility: visible;
        width: auto;
        overflow: visible;
    }
    
    /* Resetear tamaño del avatar en móvil */
    .futuristic-sidebar.collapsed .user-avatar-sidebar {
        width: 45px;
        height: 45px;
        min-width: 45px;
        min-height: 45px;
        font-size: 1rem;
    }
    
    .futuristic-sidebar {
        transform: translateX(-100%);
        z-index: 1050;
    }
    
    .futuristic-sidebar.open {
        transform: translateX(0);
    }
    
    /* Overlay para cerrar sidebar en móviles */
    .sidebar-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.5);
        z-index: 1040;
        opacity: 0;
        visibility: hidden;
        transition: all 0.3s ease;
    }
    
    .sidebar-overlay.active {
        opacity: 1;
        visibility: visible;
    }
}

@media (max-width: 576px) {
    .futuristic-sidebar {
        width: 100%;
    }
    
    .sidebar-header {
        padding: 1.5rem 1rem 0.75rem;
    }
    
    .brand-title {
        font-size: 1rem;
    }
    
    .sidebar-menu-link {
        padding: 0.75rem;
        font-size: 0.9rem;
    }
    
    .menu-icon {
        width: 30px;
        height: 30px;
    }
}

/* Scrollbar personalizado */
.futuristic-sidebar::-webkit-scrollbar {
    width: 4px;
}

.futuristic-sidebar::-webkit-scrollbar-track {
    background: transparent;
}

.futuristic-sidebar::-webkit-scrollbar-thumb {
    background: var(--accent-gradient);
    border-radius: 2px;
}

.futuristic-sidebar::-webkit-scrollbar-thumb:hover {
    background: var(--primary-gradient);
}
</style>

<script>
// Scripts para el sidebar futurista
document.addEventListener('DOMContentLoaded', function() {
    const sidebarClose = document.getElementById('sidebarClose');
    const sidebar = document.getElementById('sidebar');
    const sidebarOverlay = document.getElementById('sidebarOverlay');
    const sidebarCollapseBtn = document.getElementById('sidebarCollapseBtn');
    
    // Función para cerrar sidebar (móvil)
    function closeSidebar() {
        sidebar.classList.remove('open');
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }
    
    // Función para abrir sidebar (móvil)
    function openSidebar() {
        sidebar.classList.add('open');
        if (sidebarOverlay) {
            sidebarOverlay.classList.add('active');
        }
        document.body.style.overflow = 'hidden';
    }
    
    // Función para colapsar/expandir sidebar (escritorio)
    function toggleSidebarCollapse() {
        // Solo funcionar en escritorio
        if (window.innerWidth <= 768) return;
        
        sidebar.classList.toggle('collapsed');
        
        // Ajustar el contenido principal y el botón
        const mainContent = document.getElementById('mainContent');
        const isCollapsed = sidebar.classList.contains('collapsed');
        
        if (mainContent) {
            if (isCollapsed) {
                mainContent.style.marginLeft = '70px';
                // Ajustar posición del botón junto con el contenido
                sidebarCollapseBtn.style.left = '80px';
            } else {
                mainContent.style.marginLeft = '280px';
                // Ajustar posición del botón junto con el contenido
                sidebarCollapseBtn.style.left = '290px';
            }
        }
        
        // Guardar estado en localStorage
        localStorage.setItem('sidebarCollapsed', isCollapsed);
        
        // Actualizar icono del botón de colapso externo
        const collapseIcon = sidebarCollapseBtn.querySelector('i');
        if (isCollapsed) {
            collapseIcon.classList.remove('fa-angle-left');
            collapseIcon.classList.add('fa-angle-right');
            sidebarCollapseBtn.setAttribute('title', 'Expandir Sidebar');
        } else {
            collapseIcon.classList.remove('fa-angle-right');
            collapseIcon.classList.add('fa-angle-left');
            sidebarCollapseBtn.setAttribute('title', 'Contraer Sidebar');
        }
    }
    
    // Restaurar estado del sidebar desde localStorage
    function restoreSidebarState() {
        const isCollapsed = localStorage.getItem('sidebarCollapsed') === 'true';
        if (isCollapsed) {
            sidebar.classList.add('collapsed');
            
            // Ajustar el contenido principal y el botón
            const mainContent = document.getElementById('mainContent');
            if (mainContent && window.innerWidth > 768) {
                mainContent.style.marginLeft = '70px';
                // Ajustar posición del botón junto con el contenido
                sidebarCollapseBtn.style.left = '80px';
            }
            
            // Actualizar icono del botón de colapso externo
            const collapseIcon = sidebarCollapseBtn.querySelector('i');
            collapseIcon.classList.remove('fa-angle-left');
            collapseIcon.classList.add('fa-angle-right');
            sidebarCollapseBtn.setAttribute('title', 'Expandir Sidebar');
        }
    }
    
    // Event listeners
    if (sidebarClose && sidebar) {
        sidebarClose.addEventListener('click', closeSidebar);
    }
    
    // Botón de colapso externo
    if (sidebarCollapseBtn) {
        sidebarCollapseBtn.addEventListener('click', toggleSidebarCollapse);
    }
    
    // Cerrar sidebar al hacer clic en el overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
    
    // Restaurar estado al cargar la página
    restoreSidebarState();
    
    // Manejar redimensionamiento de ventana
    window.addEventListener('resize', function() {
        const mainContent = document.getElementById('mainContent');
        if (window.innerWidth <= 768) {
            // En móvil, resetear margin-left
            if (mainContent) {
                mainContent.style.marginLeft = '';
            }
        } else {
            // En escritorio, aplicar margin según estado del sidebar
            if (mainContent) {
                const isCollapsed = sidebar.classList.contains('collapsed');
                mainContent.style.marginLeft = isCollapsed ? '70px' : '280px';
                // Ajustar posición del botón junto con el contenido
                sidebarCollapseBtn.style.left = isCollapsed ? '80px' : '290px';
            }
        }
    });
    
    // Función global para toggle desde navbar (móvil)
    window.toggleSidebar = function() {
        if (sidebar.classList.contains('open')) {
            closeSidebar();
        } else {
            openSidebar();
        }
    };
    
    // Funciones para las herramientas
    window.exportData = function() {
        alert('Función de exportar datos - En desarrollo');
    };
    
    window.generateReport = function() {
        alert('Función de generar reportes - En desarrollo');
    };
    
    window.systemSettings = function() {
        alert('Función de configuración del sistema - En desarrollo');
    };
    
    window.systemBackup = function() {
        alert('Función de respaldo del sistema - En desarrollo');
    };
});
</script>