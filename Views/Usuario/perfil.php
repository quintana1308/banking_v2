<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Mi Perfil - Banking ADN',
    'show_sidebar' => true,
    'show_navbar' => true,
    'show_footer' => true,
    'theme' => 'dark',
    'custom_css' => [
        'futuristic-dashboard.css'
    ],
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

<!-- Loader -->
<div class="loader" id="loader">
    <div class="loader-content">
        <div class="loader-spinner"></div>
        <div class="loader-text">Cargando Página...</div>
    </div>
</div>

<!-- Fondo animado futurista -->
<div class="futuristic-background"></div>

<!-- Partículas flotantes -->
<div class="floating-particles">
    <?php for($i = 0; $i < 25; $i++): ?>
        <div class="particle" style="left: <?= rand(0, 100) ?>%; animation-delay: <?= rand(0, 20) ?>s;"></div>
    <?php endfor; ?>
</div>

<!-- Líneas geométricas -->
<div class="geometric-lines">
    <div class="geometric-line"></div>
    <div class="geometric-line"></div>
    <div class="geometric-line"></div>
</div>

<div class="container-fluid">
    <div class="row g-4">
        <!-- Información del Usuario -->
        <div class="col-lg-4">
            <div class="futuristic-card-compact">
                <div class="card-header-compact">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-inline-flex align-items-center">
                            <div class="icon-container me-3">
                                <i class="fas fa-user"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="mb-0 card-title-compact">Información Personal</h5>
                                <small class="text-muted-futuristic">Datos del usuario y configuración de cuenta</small>
                            </div>
                        </div>
                    </div>
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
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-inline-flex align-items-center">
                            <div class="icon-container me-3">
                                <i class="fas fa-building"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="mb-0 card-title-compact">Gestión de Empresas</h5>
                                <small class="text-muted-futuristic">Selecciona y administra las empresas asignadas</small>
                            </div>
                        </div>
                    </div>
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
                            <div class="col-md-6 mb-3" <?php if($enterprise['enterprise_id'] == $data['currentEnterprise']): ?>style="order: -1;"<?php endif; ?>>
                                <div class="enterprise-card <?= $enterprise['enterprise_id'] == $data['currentEnterprise'] ? 'active' : '' ?>" 
                                     data-enterprise-id="<?= $enterprise['enterprise_id'] ?>"
                                     <?php if($enterprise['enterprise_id'] != $data['currentEnterprise']): ?>
                                     onclick="changeEnterprise(<?= $enterprise['enterprise_id'] ?>, '<?= $enterprise['enterprise_name'] ?>')"
                                     style="cursor: pointer;"
                                     <?php endif; ?>>
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
                                                    data-enterprise-id="<?= $enterprise['enterprise_id'] ?>"
                                                    data-enterprise-name="<?= $enterprise['enterprise_name'] ?>"
                                                    onclick="changeEnterprise(<?= $enterprise['enterprise_id'] ?>, '<?= $enterprise['enterprise_name'] ?>')">
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
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-inline-flex align-items-center">
                            <div class="icon-container me-3">
                                <i class="fas fa-info-circle"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="mb-0 card-title-compact">Detalles de la Empresa Actual</h5>
                                <small class="text-muted-futuristic">Información completa de la empresa seleccionada</small>
                            </div>
                        </div>
                    </div>
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
                                <code><?= $_SESSION['userData']['token'] ?></code>
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


<script>
    // Efectos futuristas mejorados
    document.addEventListener('DOMContentLoaded', function() {
        // Crear partículas dinámicamente
        const particlesContainer = document.querySelector('.floating-particles');
        
        // Animación de entrada para las tarjetas
        const cards = document.querySelectorAll('.futuristic-card-compact');
        cards.forEach((card, index) => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s cubic-bezier(0.4, 0, 0.2, 1)';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 200 + (index * 100));
        });

        // Ocultar loader después de cargar la página
        window.addEventListener('load', function() {
            setTimeout(() => {
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.style.opacity = '0';
                    setTimeout(() => {
                        loader.style.display = 'none';
                    }, 500);
                }
            }, 1500); // Mostrar loader por 1.5 segundos
        });
    });

    // Función para cambiar de empresa
    function changeEnterprise(enterpriseId, enterpriseName) {
        // Prevenir propagación del evento si se hace clic en el botón
        event.stopPropagation();
        
        Swal.fire({
            title: '¿Cambiar de empresa?',
            text: `¿Deseas cambiar a la empresa "${enterpriseName}"?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Sí, cambiar',
            cancelButtonText: 'Cancelar',
            background: '#19233adb',
            color: '#fff',
            customClass: {
                popup: 'futuristic-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loader mientras se procesa
                const loader = document.getElementById('loader');
                if (loader) {
                    loader.style.display = 'flex';
                    loader.style.opacity = '1';
                }
                
                // Enviar petición para cambiar empresa
                fetch('<?= base_url() ?>/usuario/cambiarEmpresa', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: `enterprise_id=${enterpriseId}`
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'success') {
                        // Ocultar loader
                        if (loader) {
                            loader.style.opacity = '0';
                            setTimeout(() => {
                                loader.style.display = 'none';
                            }, 500);
                        }

                        Swal.fire({
                            title: '¡Empresa cambiada!',
                            text: data.message,
                            icon: 'success',
                            background: '#19233adb',
                            color: '#fff',
                            customClass: {
                                popup: 'futuristic-popup'
                            }
                        }).then(() => {
                            // Recargar la página para actualizar la información
                            window.location.reload();
                        });
                    } else {
                        // Ocultar loader en caso de error
                        if (loader) {
                            loader.style.opacity = '0';
                            setTimeout(() => {
                                loader.style.display = 'none';
                            }, 500);
                        }
                        
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            background: '#19233adb',
                            color: '#fff',
                            customClass: {
                                popup: 'futuristic-popup'
                            }
                        });
                    }
                })
                .catch(error => {
                    // Ocultar loader en caso de error
                    if (loader) {
                        loader.style.opacity = '0';
                        setTimeout(() => {
                            loader.style.display = 'none';
                        }, 500);
                    }
                    
                    Swal.fire({
                        title: 'Error de conexión',
                        text: 'No se pudo conectar con el servidor',
                        icon: 'error',
                        background: '#19233adb',
                        color: '#fff',
                        customClass: {
                            popup: 'futuristic-popup'
                        }
                    });
                });
            }
        });
    }
</script>

<?php
$content = ob_get_clean();

// Incluir el template principal
include __DIR__ . '/../Template/template.php';
?>
