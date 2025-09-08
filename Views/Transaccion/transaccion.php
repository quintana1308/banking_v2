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
                    <div class="d-flex align-items-center">
                        <div class="icon-container me-3">
                            <i class="fas fa-filter"></i>
                        </div>
                        <div>
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
                        <div class="d-flex align-items-center">
                            <div class="icon-container me-3">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
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
                <div class="card-body p-0">
                    <div class="table-container-compact">
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
</div>



<style>
/* Estilos específicos para la vista de lista de movimientos */
.futuristic-card {
    background: rgba(255, 255, 255, 0.05);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(255, 255, 255, 0.1);
    border-radius: 20px;
    box-shadow: 0 8px 32px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    margin-bottom: 1rem;
}

.futuristic-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
}

.card-header-futuristic {
    padding: 1.5rem 2rem 1rem;
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.icon-container {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
}

.form-group-futuristic {
    margin-bottom: 1rem;
}

.form-label-futuristic {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 600;
    color: #e0e6ed;
    font-size: 0.9rem;
}

.input-container {
    position: relative;
}

.form-control-futuristic {
    width: 100%;
    padding: 12px 16px;
    background: rgba(255, 255, 255, 0.1);
    border: 2px solid rgba(255, 255, 255, 0.2);
    border-radius: 12px;
    font-size: 0.95rem;
    color: #e0e6ed;
    transition: all 0.3s ease;
    backdrop-filter: blur(10px);
}

.form-control-futuristic option {
    background: #2c3e50;
    color: #e0e6ed;
    padding: 8px;
}

.form-control-futuristic:focus {
    outline: none;
    border-color: #667eea;
    background: rgba(255, 255, 255, 0.15);
    color: #e0e6ed;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.input-border {
    position: absolute;
    bottom: 0;
    left: 0;
    width: 0;
    height: 2px;
    background: linear-gradient(90deg, #667eea, #764ba2);
    transition: width 0.3s ease;
}

.form-control-futuristic:focus + .input-border {
    width: 100%;
}

.btn-primary-futuristic {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-primary-futuristic:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.btn-primary-futuristic:hover .btn-glow {
    left: 100%;
}

.bank-indicator-futuristic {
    display: flex;
    align-items: center;
}

.text-muted-futuristic {
    color: #b8c5d1;
    font-size: 0.9rem;
}

.bank-badge {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 0.85rem;
    font-weight: 600;
    display: inline-flex;
    align-items: center;
    transition: all 0.3s ease;
}

.bank-badge:hover {
    transform: scale(1.05);
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

/* Estilos de tabla compactos de home.php aplicados */
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
    padding: 1rem;
    max-height: 600px;
    overflow-y: auto;
    overflow-x: hidden;
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
    padding: 1rem;
    text-align: left;
    border-bottom: 1px solid var(--glass-border, rgba(102, 126, 234, 0.2));
    font-size: 0.8rem;
    position: relative;
    z-index: 1;
}

.futuristic-table-compact td {
    padding: 1rem;
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

.futuristic-table-compact .amount-positive {
    color: #00ff88;
    font-weight: 600;
}

.futuristic-table-compact .amount-negative {
    color: #ff6b6b;
    font-weight: 600;
}

.futuristic-table-compact .status-active {
    color: #00ff88;
    font-weight: 500;
}

.futuristic-table-compact .status-pending {
    color: #ffa502;
    font-weight: 500;
}

/* Animaciones y efectos */
.scale-in {
    animation: scaleIn 0.5s ease-out forwards;
    opacity: 0;
}

@keyframes scaleIn {
    from {
        opacity: 0;
        transform: scale(0.9);
    }
    to {
        opacity: 1;
        transform: scale(1);
    }
}

/* Fondo animado */
.animated-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    border-radius: 50%;
    animation: float var(--duration) infinite linear;
    animation-delay: var(--delay);
}

.particle:nth-child(1) { top: 20%; left: 10%; }
.particle:nth-child(2) { top: 60%; left: 80%; }
.particle:nth-child(3) { top: 80%; left: 20%; }
.particle:nth-child(4) { top: 30%; left: 70%; }
.particle:nth-child(5) { top: 70%; left: 50%; }

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
    50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
}

.geometric-line {
    position: absolute;
    width: 1px;
    height: 100px;
    background: linear-gradient(to bottom, transparent, #667eea, transparent);
    animation: slide var(--duration) infinite linear;
    animation-delay: var(--delay);
}

.geometric-line:nth-child(6) { top: 10%; left: 30%; }
.geometric-line:nth-child(7) { top: 40%; left: 60%; }
.geometric-line:nth-child(8) { top: 70%; left: 90%; }

@keyframes slide {
    0% { transform: translateX(-50px) rotate(45deg); opacity: 0; }
    50% { opacity: 0.5; }
    100% { transform: translateX(50px) rotate(45deg); opacity: 0; }
}

.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.glass-effect {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}

/* Responsive */
@media (max-width: 768px) {
    .card-header-futuristic {
        flex-direction: column;
        text-align: center;
    }
    
    .bank-indicator-futuristic {
        margin-top: 1rem;
        justify-content: center;
    }
    
    .table-container-futuristic {
        font-size: 0.8rem;
    }
    
    .table-header-futuristic th,
    .table-body-futuristic td {
        padding: 0.5rem;
    }
}
</style>

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

<script src="<?= media() ?>/js/<?= $data["page_functions_js"] ?>"></script>
<?php
$content = ob_get_clean();

// Incluir la plantilla modular
include dirname(__DIR__) . '/Template/template.php';
?>