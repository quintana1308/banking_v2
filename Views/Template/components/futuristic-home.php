<?php
// Configuración de la plantilla para el home futurista
$templateConfig = [
    'page_title' => $data['title'] ?? 'Banking ADN - Dashboard',
    'show_sidebar' => true,
    'show_navbar' => true,
    'show_footer' => true,
    'theme' => 'dark',
    'custom_css' => [],
    'custom_js' => []
];
?>
<!DOCTYPE html>
<html lang="es" data-bs-theme="dark">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= $templateConfig['page_title'] ?></title>
    
    <!-- Fuentes futuristas -->
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Bootstrap CSS -->
    <link href="<?= media() ?>/css/bootstrap/bootstrap.min.css" rel="stylesheet">
    
    <style>
        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-gradient: linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-primary: #ffffff;
            --text-secondary: #b8c5d1;
            --shadow-glow: 0 8px 32px rgba(102, 126, 234, 0.3);
            --shadow-card: 0 8px 32px rgba(0, 0, 0, 0.3);
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background: var(--dark-gradient);
            color: var(--text-primary);
            min-height: 100vh;
            overflow-x: hidden;
        }

        /* Fondo animado */
        .animated-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -2;
            background: var(--dark-gradient);
        }

        .animated-background::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: 
                radial-gradient(circle at 20% 80%, rgba(120, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(255, 119, 198, 0.3) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(120, 219, 255, 0.3) 0%, transparent 50%);
            animation: backgroundShift 20s ease-in-out infinite;
        }

        @keyframes backgroundShift {
            0%, 100% { opacity: 1; transform: scale(1) rotate(0deg); }
            50% { opacity: 0.8; transform: scale(1.1) rotate(1deg); }
        }

        /* Partículas flotantes */
        .floating-particles {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: -1;
            pointer-events: none;
        }

        .particle {
            position: absolute;
            width: 4px;
            height: 4px;
            background: rgba(102, 126, 234, 0.6);
            border-radius: 50%;
            animation: float 15s infinite linear;
        }

        .particle:nth-child(2n) {
            background: rgba(255, 119, 198, 0.6);
            animation-duration: 20s;
        }

        .particle:nth-child(3n) {
            background: rgba(120, 219, 255, 0.6);
            animation-duration: 25s;
        }

        @keyframes float {
            0% {
                transform: translateY(100vh) rotate(0deg);
                opacity: 0;
            }
            10% {
                opacity: 1;
            }
            90% {
                opacity: 1;
            }
            100% {
                transform: translateY(-100px) rotate(360deg);
                opacity: 0;
            }
        }

        /* Contenedor principal */
        .dashboard-container {
            padding: 2rem;
            position: relative;
            z-index: 1;
        }

        /* Header del dashboard */
        .dashboard-header {
            text-align: center;
            margin-bottom: 3rem;
            position: relative;
        }

        .dashboard-title {
            font-family: 'Orbitron', monospace;
            font-size: 3rem;
            font-weight: 900;
            background: var(--primary-gradient);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
            margin-bottom: 1rem;
            text-shadow: 0 0 30px rgba(102, 126, 234, 0.5);
        }

        .dashboard-subtitle {
            font-size: 1.2rem;
            color: var(--text-secondary);
            font-weight: 300;
        }

        /* Cards futuristas */
        .futuristic-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: var(--shadow-card);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .futuristic-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.1), transparent);
            transition: left 0.5s;
        }

        .futuristic-card:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow-glow);
        }

        .futuristic-card:hover::before {
            left: 100%;
        }

        .card-header-futuristic {
            border-bottom: 1px solid var(--glass-border);
            padding-bottom: 1rem;
            margin-bottom: 1.5rem;
        }

        .card-title-futuristic {
            font-family: 'Orbitron', monospace;
            font-size: 1.5rem;
            font-weight: 700;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        /* Tabla futurista */
        .futuristic-table {
            width: 100%;
            border-collapse: collapse;
            background: transparent;
        }

        .futuristic-table th {
            background: var(--primary-gradient);
            color: white;
            padding: 1rem;
            text-align: left;
            font-weight: 600;
            border: none;
            font-size: 0.9rem;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .futuristic-table th:first-child {
            border-radius: 10px 0 0 0;
        }

        .futuristic-table th:last-child {
            border-radius: 0 10px 0 0;
        }

        .futuristic-table td {
            padding: 1rem;
            border-bottom: 1px solid var(--glass-border);
            color: var(--text-primary);
            font-size: 0.9rem;
        }

        .futuristic-table tr:hover {
            background: rgba(102, 126, 234, 0.1);
        }

        /* Widgets de estadísticas */
        .stats-widget {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 20px;
            padding: 2rem;
            text-align: center;
            position: relative;
            overflow: hidden;
            margin-bottom: 1.5rem;
            transition: all 0.3s ease;
        }

        .stats-widget::before {
            content: '';
            position: absolute;
            top: -50%;
            left: -50%;
            width: 200%;
            height: 200%;
            background: conic-gradient(from 0deg, transparent, rgba(102, 126, 234, 0.1), transparent);
            animation: rotate 10s linear infinite;
            z-index: -1;
        }

        @keyframes rotate {
            100% { transform: rotate(360deg); }
        }

        .stats-widget:hover {
            transform: scale(1.05);
            box-shadow: var(--shadow-glow);
        }

        .stats-icon {
            width: 60px;
            height: 60px;
            background: var(--accent-gradient);
            border-radius: 15px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 1rem;
            font-size: 24px;
            color: white;
        }

        .stats-number {
            font-family: 'Orbitron', monospace;
            font-size: 2.5rem;
            font-weight: 900;
            color: var(--text-primary);
            margin-bottom: 0.5rem;
        }

        .stats-label {
            font-size: 1rem;
            color: var(--text-secondary);
            font-weight: 500;
        }

        /* Colores para montos */
        .amount-positive {
            color: #00ff88;
            font-weight: 600;
        }

        .amount-negative {
            color: #ff4757;
            font-weight: 600;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }
            
            .dashboard-title {
                font-size: 2rem;
            }
            
            .futuristic-card {
                padding: 1.5rem;
            }
            
            .futuristic-table {
                font-size: 0.8rem;
            }
            
            .futuristic-table th,
            .futuristic-table td {
                padding: 0.75rem 0.5rem;
            }
        }

        /* Animaciones de entrada */
        .fade-in-up {
            animation: fadeInUp 0.8s ease-out forwards;
            opacity: 0;
            transform: translateY(30px);
        }

        .fade-in-up:nth-child(1) { animation-delay: 0.1s; }
        .fade-in-up:nth-child(2) { animation-delay: 0.2s; }
        .fade-in-up:nth-child(3) { animation-delay: 0.3s; }
        .fade-in-up:nth-child(4) { animation-delay: 0.4s; }

        @keyframes fadeInUp {
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>
    <!-- Fondo animado -->
    <div class="animated-background"></div>
    
    <!-- Partículas flotantes -->
    <div class="floating-particles">
        <?php for($i = 0; $i < 50; $i++): ?>
            <div class="particle" style="left: <?= rand(0, 100) ?>%; animation-delay: <?= rand(0, 15) ?>s;"></div>
        <?php endfor; ?>
    </div>

    <!-- Contenedor principal del dashboard -->
    <div class="dashboard-container">
        <!-- Header del dashboard -->
        <div class="dashboard-header fade-in-up">
            <h1 class="dashboard-title">Banking ADN</h1>
            <p class="dashboard-subtitle">Panel de Control Inteligente</p>
        </div>

        <!-- Contenido basado en roles -->
        <?php if($_SESSION['userData']['ID_ROL'] == 1 || $_SESSION['userData']['ID_ROL'] == 3) { ?>
        <div class="row fade-in-up">
            <div class="col-lg-9 col-md-8">
                <!-- Tabla de movimientos -->
                <div class="futuristic-card">
                    <div class="card-header-futuristic">
                        <h4 class="card-title-futuristic">
                            <i class="fas fa-chart-line me-2"></i>
                            Últimos 10 Movimientos
                        </h4>
                    </div>
                    <div class="table-responsive">
                        <table class="futuristic-table">
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>Banco</th>
                                    <th>Cuenta</th>
                                    <th>Referencia</th>
                                    <th>Fecha</th>
                                    <th>Monto</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['transaccion'] as $key => $value) { 
                                    $colorClass = $value['amount'] > 0 ? 'amount-positive' : 'amount-negative';
                                    ?>
                                <tr>
                                    <td><?= $value['id']; ?></td>
                                    <td><?= $value['bank']; ?></td>
                                    <td><?= $value['account']; ?></td>
                                    <td><?= $value['reference']; ?></td>
                                    <td><?= date('d/m/Y', strtotime($value['date'])); ?></td>
                                    <td class="<?= $colorClass ?>">
                                        <?= number_format($value['amount'], 2, ',', '.'); ?>
                                    </td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-4">
                <!-- Widget de estadísticas de movimientos -->
                <div class="stats-widget">
                    <div class="stats-icon">
                        <i class="fas fa-exchange-alt"></i>
                    </div>
                    <div class="stats-number"><?= $data['countTransaccion']['total']; ?></div>
                    <div class="stats-label">Movimientos Totales</div>
                </div>
            </div>
        </div>
        <?php } ?>

        <?php if($_SESSION['userData']['ID_ROL'] == 1 || $_SESSION['userData']['ID_ROL'] == 2) { ?>
        <div class="row fade-in-up">
            <div class="col-lg-9 col-md-8">
                <!-- Tabla de empresas -->
                <div class="futuristic-card">
                    <div class="card-header-futuristic">
                        <h4 class="card-title-futuristic">
                            <i class="fas fa-building me-2"></i>
                            Últimas 10 Empresas
                        </h4>
                    </div>
                    <div class="table-responsive">
                        <table class="futuristic-table">
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>Nombre</th>
                                    <th>BD</th>
                                    <th>RIF</th>
                                    <th>Token</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['enterprise'] as $key => $value) { ?>
                                <tr>
                                    <td><?= $value['id']; ?></td>
                                    <td><?= $value['name']; ?></td>
                                    <td><?= $value['bd']; ?></td>
                                    <td><?= $value['rif']; ?></td>
                                    <td><?= $value['token']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Tabla de cuentas bancarias -->
                <div class="futuristic-card">
                    <div class="card-header-futuristic">
                        <h4 class="card-title-futuristic">
                            <i class="fas fa-university me-2"></i>
                            Últimas 10 Cuentas Bancarias
                        </h4>
                    </div>
                    <div class="table-responsive">
                        <table class="futuristic-table">
                            <thead>
                                <tr>
                                    <th>Nº</th>
                                    <th>Banco</th>
                                    <th>Cuenta</th>
                                    <th>Empresa</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($data['bank'] as $key => $value) { ?>
                                <tr>
                                    <td><?= $value['id']; ?></td>
                                    <td><?= $value['name']; ?></td>
                                    <td><?= $value['account']; ?></td>
                                    <td><?= $value['enterprise']; ?></td>
                                </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
            
            <div class="col-lg-3 col-md-4">
                <!-- Widget de estadísticas de cuentas bancarias -->
                <div class="stats-widget">
                    <div class="stats-icon">
                        <i class="fas fa-credit-card"></i>
                    </div>
                    <div class="stats-number"><?= $data['countBank']['total']; ?></div>
                    <div class="stats-label">Cuentas Bancarias</div>
                </div>

                <!-- Widget de estadísticas de empresas -->
                <div class="stats-widget">
                    <div class="stats-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <div class="stats-number"><?= $data['countEnterprise']['total']; ?></div>
                    <div class="stats-label">Empresas Registradas</div>
                </div>
            </div>
        </div>
        <?php } ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="<?= media() ?>/js/bootstrap/bootstrap.bundle.min.js"></script>
    
    <!-- Script personalizado para funciones específicas de la página -->
    <script src="<?= media() ?>/js/<?= $data["page_functions_js"]?>"></script>
    
    <script>
        // Inicialización de efectos
        document.addEventListener('DOMContentLoaded', function() {
            // Agregar más partículas dinámicamente
            const particlesContainer = document.querySelector('.floating-particles');
            
            function createParticle() {
                const particle = document.createElement('div');
                particle.className = 'particle';
                particle.style.left = Math.random() * 100 + '%';
                particle.style.animationDelay = Math.random() * 15 + 's';
                particle.style.animationDuration = (Math.random() * 10 + 15) + 's';
                particlesContainer.appendChild(particle);
                
                // Remover partícula después de la animación
                setTimeout(() => {
                    if (particle.parentNode) {
                        particle.parentNode.removeChild(particle);
                    }
                }, 25000);
            }
            
            // Crear partículas periódicamente
            setInterval(createParticle, 2000);
            
            // Efecto de hover para las tablas
            const tableRows = document.querySelectorAll('.futuristic-table tbody tr');
            tableRows.forEach(row => {
                row.addEventListener('mouseenter', function() {
                    this.style.transform = 'scale(1.02)';
                    this.style.transition = 'all 0.3s ease';
                });
                
                row.addEventListener('mouseleave', function() {
                    this.style.transform = 'scale(1)';
                });
            });
        });
    </script>
</body>
</html>
