<?php
/**
 * Componente HEAD Futurista - Metadatos y enlaces CSS con estilo innovador
 */
?>

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

<!-- Variables CSS Futuristas -->
<style>
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
        color: var(--text-primary);
        overflow-x: hidden;
    }

    /* Efectos globales futuristas */
    * {
        box-sizing: border-box;
    }

    .text-gradient {
        background: var(--primary-gradient);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .glass-effect {
        background: var(--glass-bg);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border);
        box-shadow: var(--shadow-card);
    }
</style>

<!-- Meta Tags -->
<meta name="author" content="ADN Software - Banking ADN">
<meta name="robots" content="index, follow">
<meta name="theme-color" content="#667eea">
<meta name="description" content="<?= htmlspecialchars($templateConfig['description'] ?? 'Banking ADN - Sistema Bancario integral desarrollado por ADN Software') ?>">

<!-- Open Graph -->
<meta property="og:title" content="<?= htmlspecialchars($templateConfig['title'] ?? 'Banking ADN - Sistema Bancario') ?>">
<meta property="og:description" content="<?= htmlspecialchars($templateConfig['description'] ?? 'Sistema bancario integral con tecnología avanzada desarrollado por ADN Software') ?>">
<meta property="og:type" content="website">
<meta property="og:locale" content="es_ES">
<meta property="og:image" content="<?= media() ?>/images/auth/logo-adn-blanco.png">

<!-- Twitter Card -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= htmlspecialchars($templateConfig['title'] ?? 'Banking ADN - Sistema Bancario') ?>">
<meta name="twitter:description" content="<?= htmlspecialchars($templateConfig['description'] ?? 'Sistema bancario integral con tecnología avanzada desarrollado por ADN Software') ?>">
<meta name="twitter:image" content="<?= media() ?>/images/auth/logo-adn-blanco.png">
