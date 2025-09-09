<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $data['page_title'] ?? 'Acceso Denegado' ?> - Banking ADN</title>
    <link href="<?= media() ?>/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= media() ?>/css/futuristic-system.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&display=swap" rel="stylesheet">
</head>
<body class="error-page">
    <div class="container-fluid h-100">
        <div class="row h-100 justify-content-center align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="futuristic-card-compact text-center">
                    <div class="error-icon mb-4">
                        <i class="fas fa-shield-alt" style="font-size: 4rem; color: var(--accent-color);"></i>
                    </div>
                    
                    <h1 class="error-code text-gradient mb-3" style="font-family: 'Orbitron', monospace; font-size: 3rem; font-weight: 900;">
                        <?= $data['error_code'] ?? '403' ?>
                    </h1>
                    
                    <h2 class="error-title mb-3" style="color: var(--text-primary); font-size: 1.5rem;">
                        <?= $data['error_title'] ?? 'Acceso Denegado' ?>
                    </h2>
                    
                    <p class="error-message mb-4" style="color: var(--text-secondary); font-size: 1.1rem;">
                        <?= $data['error_message'] ?? 'No tienes permisos para acceder a este módulo.' ?>
                    </p>
                    
                    <div class="error-details mb-4">
                        <div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); color: var(--text-primary);">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Información:</strong> Tu rol actual no tiene permisos para acceder a esta funcionalidad. 
                            Contacta al administrador del sistema si necesitas acceso.
                        </div>
                    </div>
                    
                    <div class="error-actions">
                        <a href="<?= base_url() ?>" class="btn btn-primary-futuristic me-3">
                            <i class="fas fa-home me-2"></i>
                            Ir al Dashboard
                        </a>
                        <button onclick="history.back()" class="btn btn-secondary-futuristic">
                            <i class="fas fa-arrow-left me-2"></i>
                            Volver Atrás
                        </button>
                    </div>
                    
                    <div class="user-info mt-4 pt-4" style="border-top: 1px solid var(--glass-border);">
                        <small style="color: var(--text-secondary);">
                            <i class="fas fa-user me-1"></i>
                            Usuario: <strong><?= $_SESSION['userData']['USUARIO'] ?? 'N/A' ?></strong> |
                            <i class="fas fa-user-tag me-1 ms-2"></i>
                            Rol: <strong><?= getRoleName() ?></strong>
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .error-page {
            background: var(--bg-primary);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        
        .error-icon {
            animation: pulse 2s infinite;
        }
        
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.05); }
            100% { transform: scale(1); }
        }
        
        .error-code {
            text-shadow: 0 0 20px rgba(102, 126, 234, 0.5);
        }
        
        .btn-primary-futuristic:hover,
        .btn-secondary-futuristic:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-glow);
        }
        
        .futuristic-card-compact {
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            box-shadow: var(--shadow-card);
        }
    </style>

    <script src="<?= media() ?>/js/bootstrap.bundle.min.js"></script>
</body>
</html>
