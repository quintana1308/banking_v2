<?php
/**
 * Componente LOGIN-LAYOUT - Layout específico para páginas de autenticación
 * Este componente proporciona un diseño especializado para login/registro
 */
?>

<!doctype html>
<html lang="es" dir="ltr" data-bs-theme="<?= $templateConfig['theme'] ?? 'dark' ?>" data-bs-theme-color="theme-color-default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= htmlspecialchars($templateConfig['description'] ?? 'Sistema de autenticación Banking ADN') ?>">
    <title><?= htmlspecialchars($templateConfig['title'] ?? 'Login - Banking ADN') ?></title>
    
    <!-- Favicon -->
    <link rel="shortcut icon" href="<?= media() ?>/images/favicon.ico" />

    <!-- CSS Core -->
    <link rel="stylesheet" href="<?= media() ?>/css/core/libs.min.css" />
    <link rel="stylesheet" href="<?= media() ?>/css/hope-ui.min.css?v=2.0.0" />
    <link rel="stylesheet" href="<?= media() ?>/css/custom.min.css?v=2.0.0" />
    <link rel="stylesheet" href="<?= media() ?>/css/dark.min.css"/>
    <link rel="stylesheet" href="<?= media() ?>/css/rtl.min.css"/>
    <link rel="stylesheet" href="<?= media() ?>/css/customizer.min.css" />

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- CSS Personalizado para Login -->
    <style>
        .login-wrapper {
            min-height: 100vh;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }
        
        .login-wrapper::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('<?= media() ?>/images/auth/pattern.png') repeat;
            opacity: 0.1;
            z-index: 1;
        }
        
        .login-container {
            position: relative;
            z-index: 2;
        }
        
        .login-card {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.95);
            border: 1px solid rgba(255, 255, 255, 0.2);
            box-shadow: 0 25px 45px rgba(0, 0, 0, 0.1);
        }
        
        .login-brand {
            margin-bottom: 2rem;
        }
        
        .login-brand img {
            max-width: 180px;
            height: auto;
            filter: drop-shadow(0 4px 8px rgba(0, 0, 0, 0.1));
        }
        
        .login-form .form-control {
            border-radius: 12px;
            border: 2px solid #e2e8f0;
            padding: 12px 16px;
            font-size: 16px;
            transition: all 0.3s ease;
        }
        
        .login-form .form-control:focus {
            border-color: #3b82f6;
            box-shadow: 0 0 0 3px rgba(59, 130, 246, 0.1);
        }
        
        .login-btn {
            border-radius: 12px;
            padding: 12px 32px;
            font-weight: 600;
            font-size: 16px;
            background: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
            border: none;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }
        
        .login-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
        }
        
        .login-btn::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
            transition: left 0.5s;
        }
        
        .login-btn:hover::before {
            left: 100%;
        }
        
        .login-side-image {
            background: linear-gradient(135deg, rgba(59, 130, 246, 0.8), rgba(30, 64, 175, 0.8)),
                        url('<?= media() ?>/images/auth/tipos-de-banco.jpeg');
            background-size: cover;
            background-position: center;
            position: relative;
        }
        
        .login-side-content {
            position: relative;
            z-index: 2;
            color: white;
            padding: 3rem;
        }
        
        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            opacity: 0;
            animation: slideInLeft 0.6s ease forwards;
        }
        
        .feature-item:nth-child(2) { animation-delay: 0.2s; }
        .feature-item:nth-child(3) { animation-delay: 0.4s; }
        .feature-item:nth-child(4) { animation-delay: 0.6s; }
        
        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 1rem;
            backdrop-filter: blur(10px);
        }
        
        @keyframes slideInLeft {
            from {
                opacity: 0;
                transform: translateX(-30px);
            }
            to {
                opacity: 1;
                transform: translateX(0);
            }
        }
        
        .floating-shapes {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .shape {
            position: absolute;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            animation: float 6s ease-in-out infinite;
        }
        
        .shape:nth-child(1) {
            width: 80px;
            height: 80px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .shape:nth-child(2) {
            width: 120px;
            height: 120px;
            top: 60%;
            left: 80%;
            animation-delay: 2s;
        }
        
        .shape:nth-child(3) {
            width: 60px;
            height: 60px;
            top: 80%;
            left: 20%;
            animation-delay: 4s;
        }
        
        @keyframes float {
            0%, 100% {
                transform: translateY(0px) rotate(0deg);
            }
            50% {
                transform: translateY(-20px) rotate(180deg);
            }
        }
        
        @media (max-width: 768px) {
            .login-side-image {
                display: none !important;
            }
            
            .login-container {
                padding: 1rem;
            }
        }
    </style>

    <!-- CSS Personalizado adicional -->
    <?php if (!empty($templateConfig['customCSS'])): ?>
        <?php foreach ($templateConfig['customCSS'] as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body class="<?= $templateConfig['bodyClass'] ?? '' ?>">
    <!-- Loader -->
    <div id="loading">
        <div class="loader simple-loader">
            <div class="loader-body">
                <div class="loader-icon">
                    <svg class="icon-32" width="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path opacity="0.4" d="M16.191 2H7.81C4.77 2 3 3.78 3 6.83V17.16C3 20.26 4.77 22 7.81 22H16.191C19.28 22 21 20.26 21 17.16V6.83C21 3.78 19.28 2 16.191 2Z" fill="currentColor"></path>
                        <path fill-rule="evenodd" clip-rule="evenodd" d="M8.07996 6.6499V6.6599C7.64896 6.6599 7.29996 7.0099 7.29996 7.4399C7.29996 7.8699 7.64896 8.2199 8.07996 8.2199H11.069C11.5 8.2199 11.85 7.8699 11.85 7.4289C11.85 6.9999 11.5 6.6499 11.069 6.6499H8.07996ZM15.92 12.7399H8.07996C7.64896 12.7399 7.29996 12.3899 7.29996 11.9599C7.29996 11.5299 7.64896 11.1789 8.07996 11.1789H15.92C16.35 11.1789 16.7 11.5299 16.7 11.9599C16.7 12.3899 16.35 12.7399 15.92 12.7399ZM15.92 17.3099H8.07996C7.77996 17.3499 7.48996 17.1999 7.32996 16.9499C7.16996 16.6899 7.16996 16.3599 7.32996 16.1099C7.48996 15.8499 7.77996 15.7099 8.07996 15.7399H15.92C16.319 15.7799 16.62 16.1199 16.62 16.5299C16.62 16.9289 16.319 17.2699 15.92 17.3099Z" fill="currentColor"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <div class="login-wrapper">
        <!-- Formas flotantes decorativas -->
        <div class="floating-shapes">
            <div class="shape"></div>
            <div class="shape"></div>
            <div class="shape"></div>
        </div>

        <div class="container-fluid login-container">
            <div class="row min-vh-100 align-items-center">
                <!-- Formulario de Login -->
                <div class="col-lg-6 col-md-8 mx-auto">
                    <div class="card login-card border-0">
                        <div class="card-body p-5">
                            <!-- Logo y Título -->
                            <div class="text-center login-brand">
                                <img src="<?= media() ?>/images/auth/logo-adn-blanco.png" alt="Banking ADN Logo" class="mb-3">
                                <h2 class="h3 text-dark mb-2">¡Bienvenido de nuevo!</h2>
                                <p class="text-muted">Inicia sesión para acceder a tu panel de control</p>
                            </div>

                            <!-- Alertas -->
                            <div id="login-alerts"></div>

                            <!-- Formulario -->
                            <form class="login-form" name="formLogin" id="formLogin" action="">
                                <div class="mb-4">
                                    <label for="txtUsername" class="form-label text-dark fw-semibold">
                                        <i class="fas fa-user me-2"></i>Usuario
                                    </label>
                                    <input type="text" 
                                           class="form-control" 
                                           id="txtUsername"
                                           name="txtUsername" 
                                           placeholder="Ingresa tu usuario"
                                           required>
                                </div>

                                <div class="mb-4">
                                    <label for="txtPassword" class="form-label text-dark fw-semibold">
                                        <i class="fas fa-lock me-2"></i>Contraseña
                                    </label>
                                    <div class="position-relative">
                                        <input type="password" 
                                               class="form-control" 
                                               id="txtPassword"
                                               name="txtPassword" 
                                               placeholder="Ingresa tu contraseña"
                                               required>
                                        <button type="button" 
                                                class="btn btn-link position-absolute end-0 top-50 translate-middle-y pe-3"
                                                id="togglePassword"
                                                style="border: none; background: none;">
                                            <i class="fas fa-eye text-muted"></i>
                                        </button>
                                    </div>
                                </div>

                                <div class="d-flex justify-content-between align-items-center mb-4">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="rememberMe">
                                        <label class="form-check-label text-muted" for="rememberMe">
                                            Recordarme
                                        </label>
                                    </div>
                                    <a href="#" class="text-decoration-none">¿Olvidaste tu contraseña?</a>
                                </div>

                                <button type="submit" class="btn btn-primary login-btn w-100 mb-3">
                                    <i class="fas fa-sign-in-alt me-2"></i>
                                    Iniciar Sesión
                                </button>
                            </form>

                            <!-- Footer del formulario -->
                            <div class="text-center mt-4">
                                <small class="text-muted">
                                    © <?= date('Y') ?> Banking ADN. Todos los derechos reservados.
                                </small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Panel lateral con información -->
                <div class="col-lg-6 d-none d-lg-block">
                    <div class="login-side-image min-vh-100 d-flex align-items-center">
                        <div class="login-side-content w-100">
                            <h1 class="display-4 fw-bold mb-4">Banking ADN</h1>
                            <p class="lead mb-5">Sistema inteligente de gestión bancaria que revoluciona la forma de manejar tus finanzas.</p>
                            
                            <div class="features">
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-shield-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Seguridad Avanzada</h5>
                                        <p class="mb-0 opacity-75">Protección de datos con encriptación de nivel bancario</p>
                                    </div>
                                </div>
                                
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-chart-line"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Análisis Inteligente</h5>
                                        <p class="mb-0 opacity-75">Reportes y análisis en tiempo real de tus transacciones</p>
                                    </div>
                                </div>
                                
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-mobile-alt"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Acceso Multiplataforma</h5>
                                        <p class="mb-0 opacity-75">Disponible en web, móvil y tablet</p>
                                    </div>
                                </div>
                                
                                <div class="feature-item">
                                    <div class="feature-icon">
                                        <i class="fas fa-headset"></i>
                                    </div>
                                    <div>
                                        <h5 class="mb-1">Soporte 24/7</h5>
                                        <p class="mb-0 opacity-75">Asistencia técnica disponible las 24 horas</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="<?= media() ?>/js/core/libs.min.js"></script>
    <script src="<?= media() ?>/js/core/external.min.js"></script>
    <script src="<?= media() ?>/js/plugins/setting.js"></script>
    <script src="<?= media() ?>/js/hope-ui.js" defer></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <!-- Script específico de login -->
    <script src="<?= media() ?>/js/functions_login.js"></script>

    <!-- JavaScript personalizado -->
    <?php if (!empty($templateConfig['customJS'])): ?>
        <?php foreach ($templateConfig['customJS'] as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>

    <script>
        // Configuración global
        const base_url = "<?= base_url(); ?>";
        
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const password = document.getElementById('txtPassword');
            const icon = this.querySelector('i');
            
            if (password.type === 'password') {
                password.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                password.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });

        // Animación de entrada
        document.addEventListener('DOMContentLoaded', function() {
            const card = document.querySelector('.login-card');
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            
            setTimeout(() => {
                card.style.transition = 'all 0.6s ease';
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
            }, 100);
        });
    </script>
</body>
</html>
