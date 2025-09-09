<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Mi Perfil - Banking ADN',
    'show_sidebar' => true,
    'show_navbar' => true,
    'show_footer' => true,
    'theme' => 'dark',
    'custom_css' => [],
    'custom_js' => [$data['page_functions_js']]
];

// Configurar breadcrumbs
$breadcrumbs = [
    ['title' => 'Inicio', 'url' => base_url()],
    ['title' => 'Mi Perfil', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Mi Perfil',
    'subtitle' => 'Gestiona tu información personal y configuración de empresa',
    'icon' => 'fas fa-user-circle'
];

// Capturar contenido de la vista
ob_start();
?>

<div class="container-fluid">
    <div class="row g-4">
        <!-- Información del Usuario -->
        <div class="col-lg-4">
            <div class="futuristic-card-compact">
                <div class="card-header-compact">
                    <h5 class="card-title-compact">
                        <i class="fas fa-user me-2"></i>
                        Información Personal
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <div class="user-avatar-large mx-auto mb-3">
                            <?= strtoupper(substr($data['infoUser']['name'] ?? 'U', 0, 2)) ?>
                        </div>
                        <h4 class="text-gradient mb-1"><?= $data['infoUser']['name'] ?? 'Usuario' ?></h4>
                        <p class="text-secondary mb-0">@<?= $data['infoUser']['username'] ?? 'username' ?></p>
                        <span class="badge bg-primary mt-2"><?= getRoleName($data['infoUser']['id_rol']) ?></span>
                    </div>
                    
                    <div class="user-info-details">
                        <div class="info-item mb-3">
                            <label class="form-label text-secondary">Tipo de Usuario</label>
                            <div class="info-value">
                                <?= $data['infoUser']['type'] == 1 ? 'Normal' : 'Especial' ?>
                            </div>
                        </div>
                        
                        <div class="info-item mb-3">
                            <label class="form-label text-secondary">Permisos de Eliminación</label>
                            <div class="info-value">
                                <?php if($data['infoUser']['delete_mov'] == 1): ?>
                                    <span class="text-success"><i class="fas fa-check me-1"></i>Puede eliminar transacciones</span>
                                <?php else: ?>
                                    <span class="text-warning"><i class="fas fa-times me-1"></i>No puede eliminar transacciones</span>
                                <?php endif; ?>
                            </div>
                        </div>
                        
                        <div class="info-item">
                            <label class="form-label text-secondary">Estado</label>
                            <div class="info-value">
                                <span class="status-active">Activo</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Selector de Empresa -->
        <div class="col-lg-8">
            <div class="futuristic-card-compact">
                <div class="card-header-compact">
                    <h5 class="card-title-compact">
                        <i class="fas fa-building me-2"></i>
                        Gestión de Empresas
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="alert alert-info mb-4" style="background: rgba(13, 202, 240, 0.1); border: 1px solid rgba(13, 202, 240, 0.3);">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>Empresa Actual:</strong> <?= $_SESSION['userData']['enterpriseName'] ?? 'No asignada' ?>
                        <br><small>Todos los datos del dashboard corresponden a esta empresa.</small>
                    </div>
                    
                    <?php if(!empty($data['userEnterprises']) && count($data['userEnterprises']) > 1): ?>
                    <div class="enterprise-selector">
                        <h6 class="mb-3">Cambiar a otra empresa:</h6>
                        <div class="row g-3">
                            <?php foreach($data['userEnterprises'] as $enterprise): ?>
                            <div class="col-md-6">
                                <div class="enterprise-card <?= $enterprise['enterprise_id'] == $data['currentEnterprise'] ? 'active' : '' ?>" 
                                     data-enterprise-id="<?= $enterprise['enterprise_id'] ?>">
                                    <div class="enterprise-icon">
                                        <i class="fas fa-building"></i>
                                    </div>
                                    <div class="enterprise-info">
                                        <h6 class="enterprise-name"><?= $enterprise['enterprise_name'] ?></h6>
                                        <p class="enterprise-rif">RIF: <?= $enterprise['rif'] ?></p>
                                        <?php if($enterprise['enterprise_id'] == $data['currentEnterprise']): ?>
                                            <span class="badge bg-success">Actual</span>
                                        <?php else: ?>
                                            <button class="btn btn-sm btn-primary-futuristic change-enterprise-btn" 
                                                    data-enterprise-id="<?= $enterprise['enterprise_id'] ?>">
                                                <i class="fas fa-exchange-alt me-1"></i>Cambiar
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                    <?php else: ?>
                    <div class="text-center py-4">
                        <i class="fas fa-building text-secondary" style="font-size: 3rem; opacity: 0.3;"></i>
                        <h6 class="mt-3 text-secondary">Solo tienes acceso a una empresa</h6>
                        <p class="text-secondary">No hay otras empresas disponibles para cambiar.</p>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Información de la Empresa Actual -->
            <div class="futuristic-card-compact mt-4">
                <div class="card-header-compact">
                    <h5 class="card-title-compact">
                        <i class="fas fa-info-circle me-2"></i>
                        Detalles de la Empresa Actual
                    </h5>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Nombre de la Empresa</label>
                            <div class="info-value"><?= $_SESSION['userData']['enterpriseName'] ?? 'N/A' ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">RIF</label>
                            <div class="info-value"><?= $_SESSION['userData']['rif'] ?? 'N/A' ?></div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Token de API</label>
                            <div class="info-value">
                                <code><?= substr($_SESSION['userData']['token'] ?? 'N/A', 0, 8) ?>...</code>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-secondary">Tabla de Datos</label>
                            <div class="info-value">
                                <code><?= $_SESSION['userData']['table'] ?? 'N/A' ?></code>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.user-avatar-large {
    width: 80px;
    height: 80px;
    background: var(--accent-gradient);
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 700;
    font-size: 1.8rem;
    box-shadow: var(--shadow-glow);
}

.user-info-details .info-item {
    padding: 0.75rem 0;
    border-bottom: 1px solid var(--glass-border);
}

.user-info-details .info-item:last-child {
    border-bottom: none;
}

.info-value {
    font-weight: 500;
    color: var(--text-primary);
    margin-top: 0.25rem;
}

.enterprise-card {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    padding: 1.5rem;
    transition: all 0.3s ease;
    cursor: pointer;
    position: relative;
    overflow: hidden;
}

.enterprise-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-glow);
    border-color: var(--accent-color);
}

.enterprise-card.active {
    background: rgba(102, 126, 234, 0.1);
    border-color: var(--accent-color);
    box-shadow: var(--shadow-glow);
}

.enterprise-card.active::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: var(--accent-gradient);
}

.enterprise-icon {
    width: 50px;
    height: 50px;
    background: var(--secondary-gradient);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    margin-bottom: 1rem;
}

.enterprise-name {
    color: var(--text-primary);
    font-weight: 600;
    margin-bottom: 0.5rem;
}

.enterprise-rif {
    color: var(--text-secondary);
    font-size: 0.9rem;
    margin-bottom: 1rem;
}

.change-enterprise-btn {
    font-size: 0.85rem;
    padding: 0.4rem 0.8rem;
}

@media (max-width: 768px) {
    .enterprise-card {
        text-align: center;
    }
    
    .user-avatar-large {
        width: 60px;
        height: 60px;
        font-size: 1.4rem;
    }
}
</style>

<?php
$content = ob_get_clean();

// Incluir el template principal
include __DIR__ . '/../Template/template.php';
?>
