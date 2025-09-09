<?php
// Configuración para usar el sistema de template modular con CSS externo
$templateConfig = [
    'page_title' => 'Banking ADN - Dashboard Inteligente',
    'show_sidebar' => true,
    'show_navbar' => true,
    'show_footer' => true,
    'theme' => 'dark',
    'custom_css' => [
        'futuristic-dashboard.css'
    ],
    'custom_js' => []
];

// Configurar breadcrumbs
$breadcrumbs = [
    ['title' => 'Inicio', 'url' => base_url()],
    ['title' => 'Dashboard', 'url' => '', 'active' => true]
];

// Configurar header de página (omitido para usar hero personalizado)
$pageHeader = false;

// Los datos ahora vienen directamente de la base de datos
// Ya no necesitamos calcular manualmente

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

<!-- Contenedor principal del dashboard -->
<div class="dashboard-container">
    <!-- Grid de métricas principales - Compactas -->
    <div class="row g-3 mb-4 fade-in-up">
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card-compact scale-in">
                <div class="metric-icon-compact">
                    <i class="fas fa-exchange-alt"></i>
                </div>
                <div class="metric-content-compact">
                    <div class="metric-number-compact"><?= $data['countTransaccion']['total']; ?></div>
                    <div class="metric-label-compact">Movimientos</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card-compact scale-in">
                <div class="metric-icon-compact text-success">
                    <i class="fas fa-arrow-up"></i>
                </div>
                <div class="metric-content-compact">
                    <div class="metric-number-compact"><?= $data['countIngresos']['total']; ?></div>
                    <div class="metric-label-compact">Ingresos</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card-compact scale-in">
                <div class="metric-icon-compact text-danger">
                    <i class="fas fa-arrow-down"></i>
                </div>
                <div class="metric-content-compact">
                    <div class="metric-number-compact"><?= $data['countEgresos']['total']; ?></div>
                    <div class="metric-label-compact">Egresos</div>
                </div>
            </div>
        </div>
        <div class="col-xl-3 col-lg-6 col-md-6">
            <div class="metric-card-compact scale-in">
                <div class="metric-icon-compact text-info">
                    <i class="fas fa-university"></i>
                </div>
                <div class="metric-content-compact">
                    <div class="metric-number-compact"><?= $data['countBank']['total']; ?></div>
                    <div class="metric-label-compact">Cuentas</div>
                </div>
            </div>
        </div>
    </div>

    <!-- Contenido principal reorganizado -->
    <?php if($_SESSION['userData']['ID_ROL'] == 1 || $_SESSION['userData']['ID_ROL'] == 2 || $_SESSION['userData']['ID_ROL'] == 3) { ?>
    <div class="row g-3 fade-in-up mb-4">
        <div class="col-12">
            <!-- Tabla de movimientos recientes - Compacta -->
            <div class="futuristic-card-compact slide-in-left">
                <div class="card-header-compact">
                    <h5 class="card-title-compact">
                        <i class="fas fa-chart-line me-2"></i>
                        Movimientos Recientes
                    </h5>
                </div>
                <div class="table-container-compact">
                    <table class="futuristic-table-compact">
                        <thead>
                            <tr>
                                <th>Banco</th>
                                <th>Cuenta</th>
                                <th>Referencia</th>
                                <th>Fecha</th>
                                <th>Monto</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $limitedTransactions = array_slice($data['transaccion'], 0, 8);
                            foreach ($limitedTransactions as $key => $value) { 
                                $colorClass = $value['amount'] > 0 ? 'amount-positive' : 'amount-negative';
                                $statusClass = $value['amount'] > 0 ? 'status-active' : 'status-pending';
                                $statusText = $value['amount'] > 0 ? 'Ingreso' : 'Egreso';
                                ?>
                            <tr>
                                <td><?= $value['bank']; ?></td>
                                <td><?= $value['account']; ?></td>
                                <td><?= $value['reference']; ?></td>
                                <td><?= date('d/m/Y', strtotime($value['date'])); ?></td>
                                <td class="<?= $colorClass ?> text-end">
                                    <?= $value['amount'] > 0 ? '+' : '' ?><?= number_format($value['amount'], 2, ',', '.'); ?>
                                </td>
                                <td class="<?= $statusClass ?>"><?= $statusText ?></td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <?php } ?>

    
    <div class="row g-4 fade-in-up">
    <?php if($_SESSION['userData']['ID_ROL'] == 1) { ?>
        <div class="col-lg-6">
            <!-- Tabla de empresas - Compacta -->
            <div class="futuristic-card-compact slide-in-left">
                <div class="card-header-compact">
                    <h5 class="card-title-compact">
                        <i class="fas fa-building me-2"></i>
                        Empresas
                    </h5>
                </div>
                <div class="table-container-compact">
                    <table class="futuristic-table-compact">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Empresa</th>
                                <th>RIF</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $limitedEnterprises = array_slice($data['enterprise'], 0, 6);
                            foreach ($limitedEnterprises as $key => $value) { ?>
                            <tr>
                                <td><span class="text-gradient">#<?= str_pad($value['id'], 3, '0', STR_PAD_LEFT); ?></span></td>
                                <td><?= $value['name']; ?></td>
                                <td><?= $value['rif']; ?></td>
                                <td class="status-active">Activa</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php } ?>
        <?php if($_SESSION['userData']['ID_ROL'] == 1 || $_SESSION['userData']['ID_ROL'] == 2 || $_SESSION['userData']['ID_ROL'] == 3) { ?>                        
        <div class="col-lg-6">
            <!-- Tabla de cuentas bancarias - Compacta -->
            <div class="futuristic-card-compact slide-in-left">
                <div class="card-header-compact">
                    <h5 class="card-title-compact">
                        <i class="fas fa-credit-card me-2"></i>
                        Cuentas Bancarias
                    </h5>
                </div>
                <div class="table-container-compact">
                    <table class="futuristic-table-compact">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Banco</th>
                                <th>Cuenta</th>
                                <th>Estado</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php 
                            $limitedBanks = array_slice($data['bank'], 0, 6);
                            foreach ($limitedBanks as $key => $value) { ?>
                            <tr>
                                <td><span class="text-gradient">#<?= str_pad($value['id'], 3, '0', STR_PAD_LEFT); ?></span></td>
                                <td><?= $value['name']; ?></td>
                                <td>****<?= substr($value['account'], -4); ?></td>
                                <td class="status-active">Activa</td>
                            </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>
    
</div>


<script>
    // Efectos futuristas mejorados
    document.addEventListener('DOMContentLoaded', function() {
        // Crear partículas dinámicamente
        const particlesContainer = document.querySelector('.floating-particles');
        
        function createParticle() {
            const particle = document.createElement('div');
            particle.className = 'particle';
            particle.style.left = Math.random() * 100 + '%';
            particle.style.animationDelay = Math.random() * 20 + 's';
            particle.style.animationDuration = (Math.random() * 10 + 20) + 's';
            particlesContainer.appendChild(particle);
            
            setTimeout(() => {
                if (particle.parentNode) {
                    particle.parentNode.removeChild(particle);
                }
            }, 30000);
        }
        
        // Crear partículas periódicamente
        setInterval(createParticle, 3000);
        
        // Efectos de hover mejorados para las tablas compactas
        const tableRows = document.querySelectorAll('.futuristic-table-compact tbody tr');
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
            }, 1500); // Mostrar loader por 1.5 segundos
        });
    });
</script>

<?php
$content = ob_get_clean();

// Incluir el template principal
include __DIR__ . '/Template/template.php';
?>