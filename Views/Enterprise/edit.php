<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Banking ADN - Editar Empresa',
    'show_sidebar' => true,
    'show_navbar' => true,
    'show_footer' => true,
    'theme' => 'dark',
    'custom_css' => [],
    'custom_js' => []
];

// Configurar breadcrumbs
$breadcrumbs = [
    ['title' => 'Inicio', 'url' => base_url()],
    ['title' => 'Gestión', 'url' => ''],
    ['title' => 'Empresas', 'url' => base_url() . '/enterprise'],
    ['title' => 'Editar', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Editar Empresa',
    'subtitle' => 'Modifica los datos de la empresa seleccionada',
    'icon' => 'fas fa-edit',
    'actions' => [
        [
            'title' => 'Volver al Listado',
            'url' => base_url() . '/enterprise',
            'icon' => 'fas fa-arrow-left',
            'class' => 'btn-gradient-secondary'
        ]
    ]
];

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

<div class="container-fluid px-4 py-4">
    <div id="loading-content" class="d-none">
        <div class="loader simple-loader loadingNew">
            <div class="loader-body"></div>
        </div>
    </div>
    
    <!-- Tarjeta principal con estilo futurista -->
    <div class="futuristic-card-compact mb-4">
        <div class="card-header-compact">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-inline-flex align-items-center">
                    <div class="icon-container me-3">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="text-start">
                        <h5 class="mb-0 card-title-compact">Editar Empresa</h5>
                        <small class="text-muted-futuristic">Modifica los datos de la empresa seleccionada</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url() ?>/enterprise" class="btn-secondary-futuristic text-decoration-none">
                        <span class="btn-glow"></span>
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al Listado
                    </a>
                </div>
            </div>
        </div>
        
        <div class="futuristic-card-body">
            <form name="formEditEnterprise" id="formEditEnterprise">
                <input type="hidden" name="id" value="<?= $data['enterprise']['id'] ?>">
                <div class="row">
                    <div class="col-md-12" id="name">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control mb-3" name="name" id="name" value="<?= $data['enterprise']['name'] ?>">
                            <div class="invalid-feedback d-none" id="messageName">
                                El campo es obligatorio
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" id="bd">
                        <div class="form-group">
                            <label class="form-label">BD</label>
                            <input type="text" class="form-control mb-3" name="bd" id="bd" value="<?= $data['enterprise']['bd'] ?>">
                            <div class="invalid-feedback d-none" id="messageBd">
                                El campo es obligatorio
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" id="rif">
                        <div class="form-group">
                            <label class="form-label">Rif</label>
                            <input type="text" class="form-control mb-3" name="rif" id="rif" value="<?= $data['enterprise']['rif'] ?>">
                            <div class="invalid-feedback d-none" id="messageRif">
                                El campo es obligatorio
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="token">
                        <div class="form-group">
                            <label class="form-label">Token</label>
                            <input type="text" class="form-control mb-3" name="token" id="token" value="<?= $data['enterprise']['token'] ?>">
                            <div class="invalid-feedback d-none" id="messageToken">
                                El campo es obligatorio
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        Actualizar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script>
document.addEventListener('DOMContentLoaded', function() {
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
        }, 1500);
    });
});
</script>

<script src="<?= media() ?>/js/<?= $data["page_functions_js"]?>"></script>

<?php
$content = ob_get_clean();

// Incluir el template principal
include_once __DIR__ . '/../Template/template.php';
?>