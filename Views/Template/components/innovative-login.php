<!doctype html>
<html lang="es" dir="ltr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <title><?= htmlspecialchars($templateConfig['title'] ?? 'Banking ADN - Login') ?></title>
    
    <!-- Fuentes -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Orbitron:wght@400;700;900&family=Space+Grotesk:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <!-- Iconos -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        :root {
            --primary-gradient: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --secondary-gradient: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --accent-gradient: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --dark-gradient: linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 100%);
            --glass-bg: rgba(255, 255, 255, 0.1);
            --glass-border: rgba(255, 255, 255, 0.2);
            --text-primary: #ffffff;
            --text-secondary: rgba(255, 255, 255, 0.8);
            --shadow-glow: 0 0 40px rgba(102, 126, 234, 0.3);
            --shadow-card: 0 25px 50px rgba(0, 0, 0, 0.3);
        }

        body {
            font-family: 'Space Grotesk', sans-serif;
            background: var(--dark-gradient);
            min-height: 100vh;
            overflow-x: hidden;
            position: relative;
        }

        /* Fondo animado con partículas */
        .animated-background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 1;
            background: var(--dark-gradient);
        }

        .particle {
            position: absolute;
            background: var(--accent-gradient);
            border-radius: 50%;
            opacity: 0.6;
            animation: float 8s infinite ease-in-out;
        }

        .particle:nth-child(1) { width: 4px; height: 4px; top: 20%; left: 10%; animation-delay: 0s; }
        .particle:nth-child(2) { width: 6px; height: 6px; top: 60%; left: 20%; animation-delay: 2s; }
        .particle:nth-child(3) { width: 3px; height: 3px; top: 40%; left: 70%; animation-delay: 4s; }
        .particle:nth-child(4) { width: 5px; height: 5px; top: 80%; left: 80%; animation-delay: 1s; }
        .particle:nth-child(5) { width: 4px; height: 4px; top: 30%; left: 90%; animation-delay: 3s; }
        .particle:nth-child(6) { width: 7px; height: 7px; top: 70%; left: 5%; animation-delay: 5s; }

        @keyframes float {
            0%, 100% { transform: translateY(0px) translateX(0px) rotate(0deg); }
            25% { transform: translateY(-20px) translateX(10px) rotate(90deg); }
            50% { transform: translateY(-10px) translateX(-10px) rotate(180deg); }
            75% { transform: translateY(-30px) translateX(5px) rotate(270deg); }
        }

        /* Líneas geométricas animadas */
        .geometric-lines {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            z-index: 2;
            pointer-events: none;
        }

        .line {
            position: absolute;
            background: var(--primary-gradient);
            opacity: 0.1;
        }

        .line-1 {
            width: 2px;
            height: 100%;
            left: 15%;
            animation: slideDown 6s infinite linear;
        }

        .line-2 {
            width: 100%;
            height: 1px;
            top: 30%;
            animation: slideRight 8s infinite linear;
        }

        .line-3 {
            width: 1px;
            height: 100%;
            right: 25%;
            animation: slideUp 7s infinite linear;
        }

        @keyframes slideDown {
            0% { transform: translateY(-100%); }
            100% { transform: translateY(100vh); }
        }

        @keyframes slideRight {
            0% { transform: translateX(-100%); }
            100% { transform: translateX(100vw); }
        }

        @keyframes slideUp {
            0% { transform: translateY(100vh); }
            100% { transform: translateY(-100%); }
        }

        /* Contenedor principal */
        .login-container {
            position: relative;
            z-index: 10;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }

        /* Card principal */
        .login-card {
            background: var(--glass-bg);
            backdrop-filter: blur(20px);
            border: 1px solid var(--glass-border);
            border-radius: 24px;
            padding: 0;
            width: 100%;
            max-width: 900px;
            box-shadow: var(--shadow-card);
            overflow: hidden;
            position: relative;
            animation: cardEntrance 1s ease-out;
        }

        @keyframes cardEntrance {
            0% {
                opacity: 0;
                transform: translateY(50px) scale(0.9);
            }
            100% {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .login-content {
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 600px;
        }

        /* Panel izquierdo - Formulario */
        .form-panel {
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
        }

        .form-panel::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: var(--primary-gradient);
            opacity: 0.05;
            border-radius: 24px 0 0 24px;
        }

        .brand-section {
            text-align: center;
            margin-bottom: 40px;
            position: relative;
            z-index: 2;
        }

        .brand-logo {
            width: 120px;
            height: 80px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.05); }
        }

        .brand-logo img {
            max-width: 100%;
            max-height: 100%;
            object-fit: contain;
            filter: drop-shadow(0 0 20px rgba(102, 126, 234, 0.3));
        }

        .brand-title {
            font-family: 'Orbitron', monospace;
            font-size: 32px;
            font-weight: 900;
            color: var(--text-primary);
            margin-bottom: 8px;
            letter-spacing: 2px;
        }

        .brand-subtitle {
            color: var(--text-secondary);
            font-size: 16px;
            font-weight: 300;
        }

        /* Formulario */
        .login-form {
            position: relative;
            z-index: 2;
        }

        .form-group {
            margin-bottom: 25px;
            position: relative;
        }

        .form-label {
            display: block;
            color: var(--text-primary);
            font-weight: 500;
            margin-bottom: 8px;
            font-size: 14px;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .form-input {
            width: 100%;
            padding: 16px 20px;
            background: rgba(255, 255, 255, 0.05);
            border: 2px solid rgba(255, 255, 255, 0.1);
            border-radius: 12px;
            color: var(--text-primary);
            font-size: 16px;
            transition: all 0.3s ease;
            backdrop-filter: blur(10px);
        }

        .form-input:focus {
            outline: none;
            border-color: #667eea;
            background: rgba(255, 255, 255, 0.1);
            box-shadow: 0 0 20px rgba(102, 126, 234, 0.2);
            transform: translateY(-2px);
        }

        .form-input::placeholder {
            color: rgba(255, 255, 255, 0.5);
        }

        .password-toggle {
            position: absolute;
            right: 15px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-secondary);
            cursor: pointer;
            font-size: 18px;
            transition: color 0.3s ease;
        }

        .password-toggle:hover {
            color: var(--text-primary);
        }

        .form-options {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        .checkbox-container {
            display: flex;
            align-items: center;
        }

        .custom-checkbox {
            width: 20px;
            height: 20px;
            background: rgba(255, 255, 255, 0.1);
            border: 2px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            margin-right: 10px;
            position: relative;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .custom-checkbox.checked {
            background: var(--accent-gradient);
            border-color: #4facfe;
        }

        .custom-checkbox.checked::after {
            content: '✓';
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            color: white;
            font-size: 12px;
            font-weight: bold;
        }

        .checkbox-label {
            color: var(--text-secondary);
            font-size: 14px;
            cursor: pointer;
        }

        .forgot-link {
            color: #4facfe;
            text-decoration: none;
            font-size: 14px;
            transition: color 0.3s ease;
        }

        .forgot-link:hover {
            color: #00f2fe;
        }

        .login-button {
            width: 100%;
            padding: 18px;
            background: var(--accent-gradient);
            border: none;
            border-radius: 12px;
            color: white;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .login-button::before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.3), transparent);
            transition: left 0.5s;
        }

        .login-button:hover {
            transform: translateY(-3px);
            box-shadow: 0 15px 35px rgba(79, 172, 254, 0.4);
        }

        .login-button:hover::before {
            left: 100%;
        }

        .login-button:active {
            transform: translateY(-1px);
        }

        /* Panel derecho - Información */
        .info-panel {
            background: var(--primary-gradient);
            padding: 60px 50px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .info-panel::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 200%;
            height: 200%;
            background: radial-gradient(circle, rgba(255, 255, 255, 0.1) 0%, transparent 70%);
            animation: rotate 20s linear infinite;
        }

        @keyframes rotate {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .info-content {
            position: relative;
            z-index: 2;
        }

        .info-title {
            font-family: 'Orbitron', monospace;
            font-size: 28px;
            font-weight: 700;
            color: white;
            margin-bottom: 20px;
            line-height: 1.2;
        }

        .info-description {
            color: rgba(255, 255, 255, 0.9);
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 40px;
        }

        .features-list {
            list-style: none;
        }

        .feature-item {
            display: flex;
            align-items: center;
            margin-bottom: 20px;
            opacity: 0;
            animation: slideInRight 0.8s ease forwards;
        }

        .feature-item:nth-child(1) { animation-delay: 0.2s; }
        .feature-item:nth-child(2) { animation-delay: 0.4s; }
        .feature-item:nth-child(3) { animation-delay: 0.6s; }
        .feature-item:nth-child(4) { animation-delay: 0.8s; }

        @keyframes slideInRight {
            0% {
                opacity: 0;
                transform: translateX(30px);
            }
            100% {
                opacity: 1;
                transform: translateX(0);
            }
        }

        .feature-icon {
            width: 50px;
            height: 50px;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 15px;
            backdrop-filter: blur(10px);
        }

        .feature-icon i {
            color: white;
            font-size: 20px;
        }

        .feature-text h4 {
            color: white;
            font-size: 16px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .feature-text p {
            color: rgba(255, 255, 255, 0.8);
            font-size: 14px;
            margin: 0;
        }

        /* Loader */
        .loader {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: var(--dark-gradient);
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
            color: var(--text-primary);
            font-size: 18px;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .login-content {
                grid-template-columns: 1fr;
            }

            .info-panel {
                display: none;
            }

            .form-panel {
                padding: 40px 30px;
            }

            .brand-title {
                font-size: 24px;
            }

            .login-card {
                margin: 10px;
                border-radius: 16px;
            }
        }

        @media (max-width: 480px) {
            .form-panel {
                padding: 30px 20px;
            }

            .brand-icon {
                width: 60px;
                height: 60px;
            }

            .brand-icon i {
                font-size: 24px;
            }

            .brand-title {
                font-size: 20px;
            }
        }
    </style>
</head>

<body>
    <!-- Loader -->
    <div class="loader" id="loader">
        <div class="loader-content">
            <div class="loader-spinner"></div>
            <div class="loader-text">Cargando Banking ADN...</div>
        </div>
    </div>

    <!-- Fondo animado -->
    <div class="animated-background">
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
        <div class="particle"></div>
    </div>

    <!-- Líneas geométricas -->
    <div class="geometric-lines">
        <div class="line line-1"></div>
        <div class="line line-2"></div>
        <div class="line line-3"></div>
    </div>

    <!-- Contenedor principal -->
    <div class="login-container">
        <div class="login-card">
            <div class="login-content">
                <!-- Panel del formulario -->
                <div class="form-panel">
                    <!-- Marca -->
                    <div class="brand-section">
                        <div class="brand-logo">
                            <img src="<?= media() ?>/images/auth/logo-adn-blanco.png" alt="Banking ADN Logo" />
                        </div>
                        <h1 class="brand-title">Banking ADN</h1>
                        <p class="brand-subtitle">Sistema de Gestión Financiera</p>
                    </div>

                    <!-- Formulario -->
                    <form class="login-form" name="formLogin" id="formLogin" action="">
                        <div class="form-group">
                            <label class="form-label">Usuario</label>
                            <input type="text" 
                                   class="form-input" 
                                   id="txtUsername"
                                   name="txtUsername" 
                                   placeholder="Ingresa tu usuario"
                                   required>
                        </div>

                        <div class="form-group">
                            <label class="form-label">Contraseña</label>
                            <div style="position: relative;">
                                <input type="password" 
                                       class="form-input" 
                                       id="txtPassword"
                                       name="txtPassword" 
                                       placeholder="Ingresa tu contraseña"
                                       required>
                                <button type="button" class="password-toggle" id="togglePassword">
                                    <i class="fas fa-eye"></i>
                                </button>
                            </div>
                        </div>
                        <button type="submit" class="login-button">
                            <i class="fas fa-sign-in-alt" style="margin-right: 8px;"></i>
                            Iniciar Sesión
                        </button>
                    </form>
                </div>

                <!-- Panel de información -->
                <div class="info-panel">
                    <div class="info-content">
                        <h2 class="info-title">Sistema de Gestión Financiera</h2>
                        <p class="info-description">
                            Plataforma integral para la administración y control de operaciones bancarias 
                            empresariales con herramientas avanzadas de gestión.
                        </p>

                        <ul class="features-list">
                            <li class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-exchange-alt"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Gestión de Transacciones</h4>
                                    <p>Control completo de movimientos bancarios y conciliaciones</p>
                                </div>
                            </li>

                            <li class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-building"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Administración Empresarial</h4>
                                    <p>Gestión integral de empresas y cuentas bancarias</p>
                                </div>
                            </li>

                            <li class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-chart-line"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Reportes y Análisis</h4>
                                    <p>Dashboard con métricas financieras en tiempo real</p>
                                </div>
                            </li>

                            <li class="feature-item">
                                <div class="feature-icon">
                                    <i class="fas fa-shield-alt"></i>
                                </div>
                                <div class="feature-text">
                                    <h4>Seguridad Avanzada</h4>
                                    <p>Protección de datos con estándares bancarios internacionales</p>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="<?= media() ?>/js/functions_login.js"></script>

    <script>
        // Configuración global
        const base_url = "<?= base_url(); ?>";

        // Ocultar loader
        window.addEventListener('load', function() {
            setTimeout(() => {
                document.getElementById('loader').style.opacity = '0';
                setTimeout(() => {
                    document.getElementById('loader').style.display = 'none';
                }, 500);
            }, 1000);
        });

        // Toggle password
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

        // Custom checkbox
        document.getElementById('rememberCheckbox').addEventListener('click', function() {
            this.classList.toggle('checked');
        });

        // Efectos de focus en inputs
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('focus', function() {
                this.parentElement.classList.add('focused');
            });
            
            input.addEventListener('blur', function() {
                this.parentElement.classList.remove('focused');
            });
        });

        // Animación de partículas adicionales en hover
        document.querySelector('.login-button').addEventListener('mouseenter', function() {
            for (let i = 0; i < 5; i++) {
                setTimeout(() => {
                    createSparkle(this);
                }, i * 100);
            }
        });

        function createSparkle(element) {
            const sparkle = document.createElement('div');
            sparkle.style.position = 'absolute';
            sparkle.style.width = '4px';
            sparkle.style.height = '4px';
            sparkle.style.background = '#fff';
            sparkle.style.borderRadius = '50%';
            sparkle.style.pointerEvents = 'none';
            sparkle.style.animation = 'sparkle 0.6s ease-out forwards';
            
            const rect = element.getBoundingClientRect();
            sparkle.style.left = Math.random() * rect.width + 'px';
            sparkle.style.top = Math.random() * rect.height + 'px';
            
            element.appendChild(sparkle);
            
            setTimeout(() => {
                sparkle.remove();
            }, 600);
        }

        // CSS para animación de sparkles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes sparkle {
                0% {
                    opacity: 1;
                    transform: scale(0) translateY(0);
                }
                100% {
                    opacity: 0;
                    transform: scale(1) translateY(-20px);
                }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>
