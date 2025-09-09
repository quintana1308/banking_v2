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
                    <a href="<?= base_url() ?>" class="sidebar-menu-link <?= $currentController == 'home' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <i class="fas fa-home"></i>
                        </div>
                        <span class="menu-text">Dashboard</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= base_url() ?>/transaccion" class="sidebar-menu-link <?= $currentController == 'transaccion' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <i class="fas fa-exchange-alt"></i>
                        </div>
                        <span class="menu-text">Transacciones</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
                <li class="sidebar-menu-item">
                    <a href="<?= base_url() ?>/bank" class="sidebar-menu-link <?= $currentController == 'bank' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <i class="fas fa-university"></i>
                        </div>
                        <span class="menu-text">Cuentas Bancarias</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
                <?php if(($_SESSION['userData']['ID_ROL'] ?? 0) <= 2) { ?>
                <li class="sidebar-menu-item">
                    <a href="<?= base_url() ?>/enterprise" class="sidebar-menu-link <?= $currentController == 'enterprise' ? 'active' : '' ?>">
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
        <?php if(($_SESSION['userData']['ID_ROL'] ?? 0) == 1) { ?>
        <!--<div class="sidebar-section">
            <div class="sidebar-section-title">
                <i class="fas fa-shield-alt me-2"></i>
                Administración
            </div>
            <ul class="sidebar-menu">
                <li class="sidebar-menu-item">
                    <a href="<?= base_url() ?>/usuario" class="sidebar-menu-link <?= $currentController == 'usuario' ? 'active' : '' ?>">
                        <div class="menu-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <span class="menu-text">Usuarios</span>
                        <div class="menu-indicator"></div>
                    </a>
                </li>
            </ul>
        </div>-->
        <?php } ?>

        <!-- Información del Usuario -->
        <div class="sidebar-user-info">
            <div class="user-card glass-effect">
                <div class="user-avatar-sidebar">
                    <?= strtoupper(substr($_SESSION['userData']['USUARIO'] ?? 'U', 0, 2)) ?>
                </div>
                <div class="user-details">
                    <div class="user-name-sidebar"><?= $_SESSION['userData']['USUARIO'] ?? 'Usuario' ?></div>
                    <div class="user-role-sidebar">
                        <?php 
                        $rol = $_SESSION['userData']['ID_ROL'] ?? 0;
                        echo $rol == 1 ? 'Administrador' : ($rol == 2 ? 'Gerente' : 'Usuario');
                        ?>
                    </div>
                </div>
                <div class="user-status">
                    <div class="status-indicator online"></div>
                </div>
            </div>
        </div>

        <!-- Botón de Cerrar Sesión -->
        <div class="sidebar-footer">
            <a href="<?= base_url() ?>/logout" class="logout-btn" 
               onclick="return confirm('¿Está seguro que desea cerrar sesión?')">
                <i class="fas fa-sign-out-alt me-2"></i>
                Cerrar Sesión
            </a>
        </div>
    </div>
</aside>

<style>
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
    transition: transform 0.3s ease;
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

/* Responsive */
@media (max-width: 768px) {
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
    
    // Función para cerrar sidebar
    function closeSidebar() {
        sidebar.classList.remove('open');
        if (sidebarOverlay) {
            sidebarOverlay.classList.remove('active');
        }
        document.body.style.overflow = '';
    }
    
    // Función para abrir sidebar
    function openSidebar() {
        sidebar.classList.add('open');
        if (sidebarOverlay) {
            sidebarOverlay.classList.add('active');
        }
        document.body.style.overflow = 'hidden';
    }
    
    // Event listeners
    if (sidebarClose && sidebar) {
        sidebarClose.addEventListener('click', closeSidebar);
    }
    
    // Cerrar sidebar al hacer clic en el overlay
    if (sidebarOverlay) {
        sidebarOverlay.addEventListener('click', closeSidebar);
    }
    
    // Función global para toggle desde navbar
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