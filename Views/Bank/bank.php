<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Banking ADN - Gestión de Cuentas Bancarias',
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
    ['title' => 'Cuentas Bancarias', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Gestión de Cuentas Bancarias',
    'subtitle' => 'Administra y controla todas las cuentas bancarias del sistema',
    'icon' => 'fas fa-university',
    'actions' => [
        [
            'title' => 'Nueva Cuenta',
            'url' => base_url() . '/bank/new',
            'icon' => 'fas fa-plus',
            'class' => 'btn-gradient-primary'
        ]
    ]
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

<div class="container-fluid px-4 py-4">
    <!-- Tarjeta principal con estilo futurista igual a home.php -->
    <div class="futuristic-card-compact slide-in-left">
        <div class="card-header-compact">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-inline-flex align-items-center">
                    <div class="icon-container me-3">
                        <i class="fas fa-university"></i>
                    </div>
                    <div class="text-start">
                        <h5 class="mb-0 card-title-compact">Gestión de Cuentas Bancarias</h5>
                        <small class="text-muted-futuristic">Administra y controla todas las cuentas bancarias del sistema</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url() ?>/bank/new" class="btn-primary-futuristic text-decoration-none">
                        <span class="btn-glow"></span>
                        <i class="fas fa-plus me-2"></i>
                        Nueva Cuenta
                    </a>
                </div>
            </div>
        </div>
        
        <div class="table-container-compact-no-padding">
            <table id="bank-list-table" class="futuristic-table-compact" role="grid"
                data-bs-toggle="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>CUENTA</th>
                        <th>EMPRESA</th>
                        <th>ID BANCO</th>
                        <th>PREFIJO</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
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
include __DIR__ . '/../Template/template.php';
?>