<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Banking ADN - Gestión de Usuarios',
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
    ['title' => 'Usuarios', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Gestión de Usuarios',
    'description' => 'Administra todos los usuarios del sistema',
    'show_button' => true,
    'button_text' => 'Nuevo Usuario',
    'button_url' => base_url() . '/usuario/newUsuario',
    'button_icon' => 'fas fa-user-plus'
];

// Contenido de la vista
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

<!-- Contenido principal -->
<div class="container-fluid p-4">
    <!-- Sección de tabla de usuarios -->
    <div class="row g-4">
        <div class="col-12">
            <div class="futuristic-card-compact glass-effect scale-in">
                <div class="card-header-compact">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-inline-flex align-items-center">
                            <div class="icon-container me-3">
                                <i class="fas fa-users"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="mb-0 card-title-compact">Lista de Usuarios</h5>
                                <small class="text-muted-futuristic">Gestiona todos los usuarios del sistema</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <a href="<?= base_url() ?>/usuario/newUsuario" class="btn-primary-futuristic text-decoration-none">
                                <span class="btn-glow"></span>
                                <i class="fas fa-user-plus me-2"></i>
                                Nuevo Usuario
                            </a>
                        </div>
                    </div>
                </div>
                <div class="table-container-compact-no-padding">
                    <table id="usuarios-table" class="futuristic-table-compact" role="grid">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>NOMBRE</th>
                                <th>USUARIO</th>
                                <th>ROL</th>
                                <th>EMPRESA</th>
                                <th>TIPO</th>
                                <th>ELIMINAR MOV.</th>
                                <th>STATUS</th>
                                <th>ACCIONES</th>
                            </tr>
                        </thead>
                        <tbody>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Focus effects for form controls
    document.querySelectorAll('.form-control-futuristic').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentNode.classList.remove('focused');
        });
    });
    
    // Enhanced table hover effects
    const tableRows = document.querySelectorAll('.table-body-futuristic tr');
    tableRows.forEach(row => {
        row.addEventListener('mouseenter', function() {
            this.style.transition = 'all 0.3s ease';
        });
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
        }, 1500);
    });
});
</script>

<script src="<?= media() ?>/js/<?= $data["page_functions_js"] ?>"></script>
<?php
$content = ob_get_clean();

// Incluir la plantilla modular
include dirname(__DIR__) . '/Template/template.php';
?>
