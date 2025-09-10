<!doctype html>
<html lang="es" dir="ltr" data-bs-theme="dark" data-bs-theme-color="theme-color-default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Banking ADN - Sistema Bancario integral desarrollado por ADN Software">
    <title>Página No Encontrada - Banking ADN</title>
    
    <!-- Favicon ADN Banking -->
    <link rel="shortcut icon" href="<?= media() ?>/images/favicon.ico" />
    <link rel="icon" type="image/x-icon" href="<?= media() ?>/images/favicon.ico" />
    <link rel="apple-touch-icon" href="<?= media() ?>/images/favicon.ico" />

    <!-- Fuentes Futuristas -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <!-- CSS Futurista Principal -->
    <link href="<?= media() ?>/css/futuristic-dashboard.css" rel="stylesheet">

    <!-- Bootstrap CSS -->
    <link href="<?= media() ?>/css/bootstrap/bootstrap.min.css" rel="stylesheet">

    <!-- CSS Core (opcional para compatibilidad) -->
    <link rel="stylesheet" href="<?= media() ?>/css/core/libs.min.css" />
</head>
<body class="error-page"
    style="font-family: 'Space Grotesk', sans-serif; 
           background: linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 100%); 
           color: #ffffff; 
           overflow-x: hidden;"
>
    <!-- loader Start -->
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body">
            </div>
        </div>
    </div>
    <!-- loader END -->
    
    <div class="container-fluid h-100">
        <div class="row h-100 justify-content-center align-items-center">
            <div class="col-md-6 col-lg-5">
                <div class="futuristic-card-compact text-center p-4">
                    <div class="error-icon mb-4">
                        <i class="fas fa-exclamation-triangle" style="font-size: 4rem; color: #667eea;"></i>
                    </div>
                    
                    <h1 class="error-code text-gradient mb-3" style="font-family: 'Orbitron', monospace; font-size: 3rem; font-weight: 900;">
                        404
                    </h1>
                    
                    <h2 class="error-title mb-3" style="color: #e2e8f0; font-size: 1.5rem;">
                        Página No Encontrada
                    </h2>
                    
                    <p class="error-message mb-4" style="color: rgba(255, 255, 255, 0.8); font-size: 1.1rem;">
                        La página que buscas no existe o ha sido movida.
                    </p>
                    
                    <div class="error-details mb-4">
                        <div class="alert alert-warning" style="background: rgba(255, 193, 7, 0.1); border: 1px solid rgba(255, 193, 7, 0.3); color: #e2e8f0;">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>Información:</strong> Verifica que la URL esté correcta o regresa al dashboard principal.
                        </div>
                    </div>
                    
                    <div class="error-actions">
                        <a href="<?= base_url() ?>" class="btn-primary-futuristic text-decoration-none me-3">
                            <span class="btn-glow"></span>
                            <i class="fas fa-home me-2"></i>
                            Ir al Dashboard
                        </a>
                        <button onclick="history.back()" class="btn-secondary-futuristic">
                            <span class="btn-glow"></span>
                            <i class="fas fa-arrow-left me-2"></i>
                            Volver Atrás
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .error-page {
            background: linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 100%);
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
        
        .text-gradient {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        
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

        .btn-secondary-futuristic {
            position: relative;
            background: linear-gradient(135deg, #51cf66 0%, #40c057 100%);
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

        .btn-secondary-futuristic:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(81, 207, 102, 0.4);
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

        .btn-primary-futuristic:hover .btn-glow,
        .btn-secondary-futuristic:hover .btn-glow {
            left: 100%;
        }
        
        .futuristic-card-compact {
            backdrop-filter: blur(20px);
            border: 1px solid rgba(102, 126, 234, 0.2);
            box-shadow: 0 12px 30px rgba(102, 126, 234, 0.15);
        }
    </style>

    <!-- Scripts -->
    <script src="<?= media() ?>/js/bootstrap/bootstrap.bundle.min.js"></script>
    <script src="<?= media() ?>/js/core/libs.min.js"></script>
</body>
</html>