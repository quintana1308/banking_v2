<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Banking ADN - Gestión de Empresas',
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
    ['title' => 'Empresas', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Gestión de Empresas',
    'subtitle' => 'Administra y controla todas las empresas del sistema',
    'icon' => 'fas fa-building',
    'actions' => []
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
    <!-- Tarjeta principal con estilo futurista igual a bank.php -->
    <div class="futuristic-card-compact slide-in-left">
        <div class="card-header-compact">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3">
                        <i class="fas fa-building"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 card-title-compact">Gestión de Empresas</h5>
                        <small class="text-muted-futuristic">Administra y controla todas las empresas del sistema</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url() ?>/enterprise/new" class="btn-primary-futuristic text-decoration-none">
                        <span class="btn-glow"></span>
                        <i class="fas fa-plus me-2"></i>
                        Nueva Empresa
                    </a>
                </div>
            </div>
        </div>
        
        <div class="table-container-compact-no-padding">
            <table id="enterprise-list-table" class="futuristic-table-compact" role="grid"
                data-bs-toggle="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>NOMBRE</th>
                        <th>BD</th>
                        <th>RIF</th>
                        <th>TOKEN</th>
                        <th>TABLA</th>
                        <th>ACCIONES</th>
                    </tr>
                </thead>
                <tbody>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
/* Estilos exactos de bank.php para tarjetas y tablas */
.futuristic-card-compact {
    background: linear-gradient(135deg, 
        rgba(15, 23, 42, 0.95) 0%, 
        rgba(30, 41, 59, 0.9) 100%);
    backdrop-filter: blur(20px);
    border: 1px solid var(--glass-border, rgba(102, 126, 234, 0.2));
    border-radius: 16px;
    overflow: hidden;
    transition: all 0.3s ease;
}

.futuristic-card-compact:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.15);
}

.card-header-compact {
    padding: 1rem 1.5rem;
    border-bottom: 1px solid var(--glass-border, rgba(102, 126, 234, 0.2));
    background: rgba(102, 126, 234, 0.05);
}

.card-title-compact {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--text-gradient, #667eea);
    margin: 0;
    display: flex;
    align-items: center;
}

.table-container-compact {
    padding: 0;
    max-height: 600px;
    overflow-y: auto;
}

.table-container-compact-no-padding {
    padding: 1rem;
    margin: 0;
    max-height: 600px;
    overflow-y: auto;
    border-radius: 0 0 16px 16px;
}

.futuristic-table-compact {
    width: 100%;
    border-collapse: collapse;
    font-size: 0.875rem;
}

.futuristic-table-compact th {
    background: rgba(102, 126, 234, 0.1);
    color: rgba(255, 255, 255, 0.9);
    font-weight: 600;
    padding: 0.75rem;
    text-align: left;
    border-bottom: 1px solid var(--glass-border, rgba(102, 126, 234, 0.2));
    font-size: 0.8rem;
}

.futuristic-table-compact td {
    padding: 0.75rem;
    border-bottom: 1px solid rgba(102, 126, 234, 0.1);
    color: rgba(255, 255, 255, 0.8);
    transition: all 0.3s ease;
}

.futuristic-table-compact tbody tr:hover {
    background: rgba(102, 126, 234, 0.08);
}

.futuristic-table-compact .text-gradient {
    background: linear-gradient(135deg, #667eea, #764ba2);
    background-clip: text;
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    font-weight: 600;
}

/* Eliminar espacios en la tabla */
.futuristic-table-compact {
    margin: 0;
    border-spacing: 0;
}

.futuristic-table-compact th:first-child {
    border-top-left-radius: 0;
}

.futuristic-table-compact th:last-child {
    border-top-right-radius: 0;
}

/* Estilos adicionales para elementos de la tabla */
.enterprise-token {
    font-family: 'Courier New', monospace;
    color: #e2e8f0;
    font-weight: 500;
}

.enterprise-bd {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 0.25rem 0.5rem;
    border-radius: 6px;
    font-size: 0.8rem;
    font-weight: 600;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

/* Estilos del encabezado como transaccion.php */
.icon-container {
    width: 45px;
    height: 45px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.text-muted-futuristic {
    color: rgba(255, 255, 255, 0.6);
    font-size: 0.875rem;
}

/* Botón futurista con glow effect */
.btn-primary-futuristic {
    position: relative;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 0.75rem 1.5rem;
    border-radius: 25px;
    font-size: 0.875rem;
    font-weight: 600;
    transition: all 0.3s ease;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    overflow: hidden;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.btn-primary-futuristic:hover {
    transform: translateY(-2px);
    box-shadow: 0 12px 25px rgba(102, 126, 234, 0.4);
    color: white;
    text-decoration: none;
}

.btn-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s;
}

.btn-primary-futuristic:hover .btn-glow {
    left: 100%;
}

/* Botones de acción futuristas con iconos modernos */
.btn-action {
    position: relative;
    border: none;
    color: white;
    padding: 0;
    border-radius: 10px;
    font-size: 0.875rem;
    font-weight: 500;
    transition: all 0.3s ease;
    margin: 0 0.3rem;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 38px;
    height: 38px;
    overflow: hidden;
}

.btn-action::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border-radius: 10px;
    padding: 2px;
    background: linear-gradient(135deg, transparent, rgba(255, 255, 255, 0.1), transparent);
    mask: linear-gradient(#fff 0 0) content-box, linear-gradient(#fff 0 0);
    mask-composite: exclude;
}

.btn-action:hover {
    transform: translateY(-3px) scale(1.1);
    color: white;
    text-decoration: none;
}

.btn-action i {
    font-size: 1rem;
    z-index: 1;
}

.btn-edit {
    background: linear-gradient(135deg, #06b6d4 0%, #0891b2 100%);
    box-shadow: 0 4px 15px rgba(6, 182, 212, 0.3);
}

.btn-edit:hover {
    box-shadow: 0 8px 25px rgba(6, 182, 212, 0.5);
}

.btn-delete {
    background: linear-gradient(135deg, #f43f5e 0%, #e11d48 100%);
    box-shadow: 0 4px 15px rgba(244, 63, 94, 0.3);
}

.btn-delete:hover {
    box-shadow: 0 8px 25px rgba(244, 63, 94, 0.5);
}

/* Efecto de pulso para los botones */
.btn-action:active {
    transform: translateY(-1px) scale(0.95);
    transition: all 0.1s ease;
}
</style>

<script src="<?= media() ?>/js/<?= $data["page_functions_js"]?>"></script>

<style>
/* Loader */
.loader {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 9999;
    transition: opacity 0.5s ease;
}

.loader-content {
    text-align: center;
}

.loader-spinner {
    width: 60px;
    height: 60px;
    border: 3px solid rgba(255, 255, 255, 0.1);
    border-top: 3px solid #667eea;
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 0 auto 20px;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

.loader-text {
    color: #ffffff;
    font-size: 18px;
    font-weight: 500;
}
</style>

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

<?php
$content = ob_get_clean();

// Incluir el template principal
include __DIR__ . '/../Template/template.php';
?>