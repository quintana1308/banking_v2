<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Banking ADN - Lista de Movimientos',
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
    ['title' => 'Transacciones', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Lista de Movimientos',
    'description' => 'Gestiona y consulta todos los movimientos bancarios del sistema',
    'show_button' => true,
    'button_text' => 'Subir Movimiento',
    'button_url' => base_url() . '/transaccion/newTransaction',
    'button_icon' => 'fas fa-cloud-upload-alt'
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
    <!-- Sección de filtros -->
    <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="futuristic-card-compact glass-effect scale-in">
                <div class="card-header-compact">
                    <div class="d-inline-flex align-items-center">
                        <div class="icon-container me-3">
                            <i class="fas fa-filter"></i>
                        </div>
                        <div class="text-start">
                            <h5 class="mb-0 card-title-compact">Filtros de Búsqueda</h5>
                            <small class="text-muted">Filtra los movimientos por diferentes criterios</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <div class="row g-3">
                        <div class="col-md-3">
                            <div class="form-group-futuristic">
                                <label class="form-label-futuristic">
                                    <i class="fas fa-university me-2"></i>
                                    Banco
                                </label>
                                <div class="input-container">
                                    <select id="filtroBank" class="form-control-futuristic">
                                        <option value="">Todos</option>
                                    </select>
                                    <div class="input-border"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group-futuristic">
                                <label class="form-label-futuristic">
                                    <i class="fas fa-credit-card me-2"></i>
                                    Cuenta
                                </label>
                                <div class="input-container">
                                    <select id="filtroAccount" class="form-control-futuristic">
                                        <option value="">Todas</option>
                                    </select>
                                    <div class="input-border"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group-futuristic">
                                <label class="form-label-futuristic">
                                    <i class="fas fa-hashtag me-2"></i>
                                    Referencia
                                </label>
                                <div class="input-container">
                                    <input type="text" id="filtroReference" class="form-control-futuristic" placeholder="Buscar...">
                                    <div class="input-border"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group-futuristic">
                                <label class="form-label-futuristic">
                                    <i class="fas fa-calendar me-2"></i>
                                    Fecha
                                </label>
                                <div class="input-container">
                                    <input type="month" id="filtroDate" class="form-control-futuristic">
                                    <div class="input-border"></div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group-futuristic">
                                <label class="form-label-futuristic">
                                    <i class="fas fa-check-circle me-2"></i>
                                    Estado
                                </label>
                                <div class="input-container">
                                    <select id="filtroEstado" class="form-control-futuristic">
                                        <option value="">Todos</option>
                                        <option value="conciliados">Conciliados</option>
                                        <option value="no_conciliados">No Conciliados</option>
                                        <option value="parcial">Parcial</option>
                                        <option value="asignados">Asignados</option>
                                    </select>
                                    <div class="input-border"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Sección de chequeo de cuenta -->
   <!-- <div class="row g-4 mb-4">
        <div class="col-12">
            <div class="futuristic-card glass-effect scale-in">
                <div class="card-header-futuristic">
                    <div class="d-flex align-items-center">
                        <div class="icon-container me-3">
                            <i class="fas fa-search"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 text-gradient">Chequeo de Cuenta</h5>
                            <small class="text-muted">Selecciona una cuenta específica para revisar</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="formFilterTransaction" class="futuristic-form">
                        <div class="row g-3 align-items-end">
                            <div class="col-md-8">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-university me-2"></i>
                                        Cuenta Bancaria
                                    </label>
                                    <div class="input-container">
                                        <select id="filterAccount" name="filterAccount" class="form-control-futuristic" required>
                                            <option value="">-- Seleccione Cuenta --</option>
                                            <?php foreach ($data['accounts'] as $account) { ?>
                                                <option value="<?= $account['id_bank']; ?>-<?= $account['account']; ?>"><?= $account['name']; ?> - <?= $account['account']; ?></option>
                                            <?php } ?>
                                        </select>
                                        <div class="input-border"></div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <button type="submit" class="btn-primary-futuristic w-100">
                                    <i class="fas fa-search me-2"></i>
                                    Chequear
                                    <div class="btn-glow"></div>
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>-->

    <!-- Sección de tabla de movimientos -->
    <div class="row g-4">
        <div class="col-12">
            <div class="futuristic-card-compact glass-effect scale-in">
                <div class="card-header-compact">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-inline-flex align-items-center">
                            <div class="icon-container me-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="mb-0 card-title-compact">Lista de Movimientos</h5>
                                <small class="text-muted-futuristic">Gestiona y visualiza todas las transacciones</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <button id="btnReloadTable" class="btn-primary-futuristic text-decoration-none" title="Recargar tabla">
                                <span class="btn-glow"></span>
                                <i class="fas fa-sync-alt me-2"></i>
                                Recargar
                            </button>
                            <a href="<?= base_url() ?>/transaccion/newTransaction" class="btn-primary-futuristic text-decoration-none">
                                <span class="btn-glow"></span>
                                <i class="fas fa-plus me-2"></i>
                                Subir Movimientos
                            </a>
                            <!--<div class="bank-indicator-futuristic">
                                <small class="text-muted-futuristic me-2">Banco activo:</small>
                                <div>
                                    <div class="d-flex align-items-center">
                                        <span id="selectedBankDisplay" class="bank-badge">
                                            <i class="fas fa-university me-1"></i>
                                            Todos los bancos
                                        </span>
                                    </div>
                                </div>
                            </div>-->
                        </div>
                    </div>
                </div>
                <div class="table-container-compact-no-padding">
                        <table id="transaction-list-table" class="futuristic-table-compact" role="grid" data-bs-toggle="data-table">
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>BANCO</th>
                                    <th>CUENTA</th>
                                    <th>REFERENCIA</th>
                                    <th>FECHA</th>
                                    <th>MONTO</th>
                                    <th>RESPONSABLE</th>
                                    <th>ASIGNADO</th>
                                    <th>ESTADO</th>
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