<?php
/**
 * Componente NAVBAR Futurista - Barra de navegación superior con estilo innovador
 */
?>

<!-- Navbar Futurista -->
<nav class="futuristic-navbar glass-effect">
    <div class="container-fluid">
        <div class="d-flex justify-content-between align-items-center w-100">
            
            <!-- Botón toggle sidebar para móviles -->
            <button class="sidebar-toggle-btn d-lg-none" id="sidebarToggle">
                <i class="fas fa-bars"></i>
            </button>
            
            <!-- Perfil de Usuario -->
            <div class="navbar-actions d-flex align-items-center">
                <div class="dropdown">
                    <button class="user-profile-btn" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <?= strtoupper(substr($_SESSION['userData']['USUARIO'] ?? 'U', 0, 2)) ?>
                        </div>
                        <div class="user-info d-none d-md-block">
                            <div class="user-name"><?= $_SESSION['userData']['USUARIO'] ?? 'Usuario' ?></div>
                            <div class="user-role">
                                <?php 
                                $rol = $_SESSION['userData']['ID_ROL'] ?? 0;
                                echo $rol == 1 ? 'Administrador' : ($rol == 2 ? 'Gerente' : 'Usuario');
                                ?>
                            </div>
                        </div>
                        <i class="fas fa-chevron-down ms-2"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end futuristic-dropdown">
                        <li class="dropdown-header">
                            <div class="user-profile-header">
                                <div class="user-avatar-large">
                                    <?= strtoupper(substr($_SESSION['userData']['USUARIO'] ?? 'U', 0, 2)) ?>
                                </div>
                                <div>
                                    <div class="fw-bold text-gradient"><?= $_SESSION['userData']['USUARIO'] ?? 'Usuario' ?></div>
                                    <small class="text-muted">
                                        <?php 
                                        $rol = $_SESSION['userData']['ID_ROL'] ?? 0;
                                        echo $rol == 1 ? 'Administrador' : ($rol == 2 ? 'Gerente' : 'Usuario');
                                        ?>
                                    </small>
                                </div>
                            </div>
                        </li>
                        <!--<li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item-futuristic" href="<?= base_url() ?>/user/profile">
                                <i class="fas fa-user me-2"></i>
                                Mi Perfil
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item-futuristic" href="<?= base_url() ?>/user/settings">
                                <i class="fas fa-cog me-2"></i>
                                Configuración
                            </a>
                        </li>
                        <?php if(($_SESSION['userData']['ID_ROL'] ?? 0) == 1) { ?>
                        <li>
                            <a class="dropdown-item-futuristic" href="<?= base_url() ?>/admin">
                                <i class="fas fa-shield-alt me-2"></i>
                                Panel Admin
                            </a>
                        </li>
                        <?php } ?>-->
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <a class="dropdown-item-futuristic text-danger" href="#" 
                               onclick="confirmLogout(event)">
                                <i class="fas fa-sign-out-alt me-2"></i>
                                Cerrar Sesión
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</nav>

<style>
/* Estilos específicos para el navbar futurista */
.futuristic-navbar {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    z-index: 1000;
    padding: 1rem 0;
    backdrop-filter: blur(20px);
    border-bottom: 1px solid var(--glass-border);
    box-shadow: var(--shadow-card);
    transition: all 0.3s ease;
    /* Ocultar en escritorio por defecto */
    display: none;
}

/* Mostrar navbar solo en móviles */
@media (max-width: 768px) {
    .futuristic-navbar {
        display: block !important;
        padding: 0.75rem 0;
    }
}

.navbar-brand-futuristic {
    font-family: 'Orbitron', monospace;
    font-size: 1.5rem;
    font-weight: 900;
    text-decoration: none;
    color: var(--text-primary);
}

.brand-text {
    background: var(--primary-gradient);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.page-title-futuristic {
    font-family: 'Orbitron', monospace;
    font-size: 1.2rem;
    font-weight: 700;
    margin: 0;
}

.navbar-nav-futuristic {
    display: flex;
    gap: 2rem;
    align-items: center;
}

.nav-link-futuristic {
    color: var(--text-secondary);
    text-decoration: none;
    font-weight: 500;
    transition: all 0.3s ease;
    position: relative;
    padding: 0.5rem 1rem;
    border-radius: 25px;
}

.nav-link-futuristic:hover {
    color: var(--text-primary);
    background: rgba(102, 126, 234, 0.1);
}

.nav-link-futuristic::after {
    content: '';
    position: absolute;
    bottom: -2px;
    left: 50%;
    width: 0;
    height: 2px;
    background: var(--accent-gradient);
    transition: all 0.3s ease;
    transform: translateX(-50%);
}

.nav-link-futuristic:hover::after {
    width: 80%;
}

.btn-futuristic-icon {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    color: var(--text-primary);
    width: 45px;
    height: 45px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
    position: relative;
}

.btn-futuristic-icon:hover {
    background: var(--primary-gradient);
    transform: scale(1.05);
    box-shadow: var(--shadow-glow);
}

.notification-badge {
    position: absolute;
    top: -5px;
    right: -5px;
    background: var(--secondary-gradient);
    color: white;
    font-size: 0.7rem;
    font-weight: 600;
    width: 18px;
    height: 18px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
}

.user-profile-btn {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    color: var(--text-primary);
    padding: 0.5rem 1rem;
    border-radius: 25px;
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: all 0.3s ease;
}

.user-profile-btn:hover {
    background: rgba(102, 126, 234, 0.1);
    border-color: var(--primary-gradient);
}

.user-avatar {
    width: 35px;
    height: 35px;
    background: var(--accent-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 0.9rem;
}

.user-avatar-large {
    width: 45px;
    height: 45px;
    background: var(--accent-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
}

.user-info {
    text-align: left;
}

.user-name {
    font-size: 0.9rem;
    font-weight: 600;
    color: var(--text-primary);
}

.user-role {
    font-size: 0.8rem;
    color: var(--text-secondary);
}

.futuristic-dropdown {
    background: linear-gradient(135deg, 
        rgba(15, 23, 42, 0.95) 0%, 
        rgba(30, 41, 59, 0.9) 100%);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 15px;
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.15);
    padding: 0.5rem 0;
    min-width: 280px;
}

.dropdown-item-futuristic {
    color: var(--text-secondary);
    padding: 0.75rem 1.5rem;
    transition: all 0.3s ease;
    border: none;
    background: none;
    text-decoration: none;
    display: block;
    width: 100%;
}

.dropdown-item-futuristic:hover {
    background: rgba(102, 126, 234, 0.1);
    color: var(--text-primary);
}

.user-profile-header {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem 1.5rem;
}

.notification-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 0.5rem 0;
}

.notification-icon {
    width: 35px;
    height: 35px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 0.8rem;
}

.notification-content p {
    font-size: 0.9rem;
    color: var(--text-primary);
}

.btn-futuristic-sm {
    background: var(--primary-gradient);
    color: white;
    border: none;
    padding: 0.5rem 1.5rem;
    border-radius: 25px;
    font-size: 0.9rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.3s ease;
}

.btn-futuristic-sm:hover {
    transform: scale(1.05);
    box-shadow: var(--shadow-glow);
}

.sidebar-toggle-btn {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    color: var(--text-primary);
    width: 40px;
    height: 40px;
    border-radius: 8px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.3s ease;
}

.sidebar-toggle-btn:hover {
    background: var(--primary-gradient);
    color: white;
}

/* Ajustes responsive para elementos del navbar */
@media (max-width: 576px) {
    .user-profile-btn {
        padding: 0.4rem 0.8rem;
        font-size: 0.85rem;
    }
    
    .user-avatar {
        width: 30px;
        height: 30px;
        font-size: 0.8rem;
    }
    
    .futuristic-dropdown {
        min-width: 250px;
        right: 5px !important;
    }
}
</style>

<script>
// Script para toggle del sidebar en móviles
document.addEventListener('DOMContentLoaded', function() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('sidebar');
    
    if (sidebarToggle && sidebar) {
        sidebarToggle.addEventListener('click', function() {
            if (window.toggleSidebar) {
                window.toggleSidebar();
            } else {
                sidebar.classList.toggle('open');
            }
        });
    }
});
</script>
