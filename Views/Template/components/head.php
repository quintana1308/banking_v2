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

    /* Estilos unificados del sistema */
    .futuristic-card-compact {
        background: linear-gradient(135deg, 
            rgba(15, 23, 42, 0.95) 0%, 
            rgba(30, 41, 59, 0.9) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border, rgba(102, 126, 234, 0.2));
        border-radius: 16px;
        overflow: hidden;
        transition: all 0.3s ease;
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.15);
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

    .futuristic-card-header {
        background: linear-gradient(135deg, 
            rgba(102, 126, 234, 0.1) 0%, 
            rgba(118, 75, 162, 0.1) 100%);
        border-bottom: 1px solid rgba(102, 126, 234, 0.2);
        padding: 1.5rem;
    }

    .futuristic-card-title {
        color: #e2e8f0;
        font-size: 1.25rem;
        font-weight: 600;
        margin: 0;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }

    .futuristic-card-body {
        padding: 2rem;
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
        margin: 0;
        border-spacing: 0;
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

    .futuristic-table-compact th:first-child {
        border-top-left-radius: 0;
    }

    .futuristic-table-compact th:last-child {
        border-top-right-radius: 0;
    }

    .account-number {
        font-family: 'Courier New', monospace;
        color: #e2e8f0;
        font-weight: 500;
    }

    .bank-prefix {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-size: 0.8rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

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

    .btn-secondary-futuristic:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.4);
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

    .btn-action:active {
        transform: translateY(-1px) scale(0.95);
        transition: all 0.1s ease;
    }

    /* Formularios */
    .form-label {
        color: #cbd5e1;
        font-weight: 500;
        margin-bottom: 0.5rem;
        font-size: 0.9rem;
    }

    .form-control, .form-select {
        background: rgba(15, 23, 42, 0.6);
        border: 1px solid rgba(102, 126, 234, 0.3);
        border-radius: 8px;
        color: #e2e8f0;
        padding: 0.75rem 1rem;
        transition: all 0.3s ease;
    }

    .form-control:focus, .form-select:focus {
        background: rgba(15, 23, 42, 0.8);
        border-color: rgba(102, 126, 234, 0.6);
        box-shadow: 0 0 0 0.2rem rgba(102, 126, 234, 0.25);
        color: #e2e8f0;
    }

    .form-control::placeholder {
        color: #64748b;
    }

    .form-select option {
        background: #1e293b;
        color: #e2e8f0;
    }

    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 0.75rem 2rem;
        border-radius: 25px;
        font-weight: 600;
        font-size: 1rem;
        transition: all 0.3s ease;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 25px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
    }

    .invalid-feedback {
        color: #ef4444;
        font-size: 0.875rem;
        margin-top: 0.25rem;
    }

    /* Loaders */
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

    .loader-text {
        color: #ffffff;
        font-size: 18px;
        font-weight: 500;
    }

    .loadingNew {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        z-index: 9999;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    /* Estilos para tarjetas compactas del dashboard */
    .metric-card-compact {
        background: linear-gradient(135deg, 
            rgba(15, 23, 42, 0.9) 0%, 
            rgba(30, 41, 59, 0.8) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border, rgba(102, 126, 234, 0.2));
        border-radius: 12px;
        padding: 1rem;
        display: flex;
        align-items: center;
        gap: 1rem;
        transition: all 0.3s ease;
        height: 80px;
    }

    .metric-card-compact:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.2);
        border-color: rgba(102, 126, 234, 0.4);
    }

    .metric-icon-compact {
        width: 40px;
        height: 40px;
        border-radius: 8px;
        background: linear-gradient(135deg, var(--primary-gradient, #667eea), var(--secondary-gradient, #764ba2));
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
        color: white;
        flex-shrink: 0;
    }

    .metric-content-compact {
        flex: 1;
    }

    .metric-number-compact {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--text-gradient, #667eea);
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .metric-label-compact {
        font-size: 0.875rem;
        color: rgba(255, 255, 255, 0.7);
        font-weight: 500;
    }

    /* Tarjetas de contenido compactas */
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
        max-height: 400px;
        overflow-y: auto;
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

    /* Responsive adjustments para tarjetas compactas */
    @media (max-width: 768px) {
        .metric-card-compact {
            height: auto;
            padding: 0.75rem;
        }
        
        .metric-number-compact {
            font-size: 1.25rem;
        }
        
        .futuristic-table-compact {
            font-size: 0.75rem;
        }
        
        .futuristic-table-compact th,
        .futuristic-table-compact td {
            padding: 0.5rem;
        }
    }

    .loader-body {
        width: 50px;
        height: 50px;
        border: 3px solid rgba(102, 126, 234, 0.3);
        border-top: 3px solid #667eea;
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    @keyframes spin {
        0% { transform: rotate(0deg); }
        100% { transform: rotate(360deg); }
    }

    /* Formularios futuristas específicos */
    .form-group-futuristic {
        margin-bottom: 1.5rem;
    }

    .form-label-futuristic {
        display: flex;
        align-items: center;
        font-weight: 600;
        color: var(--text-gradient, #667eea);
        margin-bottom: 0.75rem;
        font-size: 0.9rem;
    }

    .form-select-futuristic {
        background: linear-gradient(135deg, 
            rgba(15, 23, 42, 0.9) 0%, 
            rgba(30, 41, 59, 0.8) 100%);
        backdrop-filter: blur(10px);
        border: 1px solid var(--glass-border, rgba(102, 126, 234, 0.2));
        border-radius: 8px;
        padding: 0.75rem 1rem;
        color: rgba(255, 255, 255, 0.9);
        font-size: 0.9rem;
        transition: all 0.3s ease;
        width: 100%;
    }

    .form-select-futuristic:focus {
        outline: none;
        border-color: var(--primary-gradient, #667eea);
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: rgba(102, 126, 234, 0.05);
    }

    .file-upload-futuristic {
        position: relative;
        border: 2px dashed var(--glass-border, rgba(102, 126, 234, 0.3));
        border-radius: 12px;
        padding: 2rem;
        text-align: center;
        background: linear-gradient(135deg, 
            rgba(15, 23, 42, 0.5) 0%, 
            rgba(30, 41, 59, 0.3) 100%);
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .file-upload-futuristic:hover {
        border-color: var(--primary-gradient, #667eea);
        background: rgba(102, 126, 234, 0.05);
    }

    .file-input-futuristic {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-upload-display {
        pointer-events: none;
        color: rgba(255, 255, 255, 0.7);
    }

    .file-upload-display i {
        color: var(--primary-gradient, #667eea);
    }

    .form-actions-futuristic {
        margin-top: 2rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
    }

    .btn-futuristic-primary {
        position: relative;
        background: linear-gradient(135deg, var(--primary-gradient, #667eea), var(--secondary-gradient, #764ba2));
        border: none;
        border-radius: 8px;
        padding: 0.75rem 2rem;
        color: white;
        font-weight: 600;
        font-size: 0.9rem;
        cursor: pointer;
        transition: all 0.3s ease;
        overflow: hidden;
        display: flex;
        align-items: center;
    }

    .btn-futuristic-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
    }

    .btn-futuristic-secondary {
        background: transparent;
        border: 1px solid var(--glass-border, rgba(102, 126, 234, 0.3));
        border-radius: 8px;
        padding: 0.75rem 2rem;
        color: rgba(255, 255, 255, 0.8);
        font-weight: 500;
        font-size: 0.9rem;
        text-decoration: none;
        cursor: pointer;
        transition: all 0.3s ease;
        display: flex;
        align-items: center;
    }

    .btn-futuristic-secondary:hover {
        border-color: var(--primary-gradient, #667eea);
        background: rgba(102, 126, 234, 0.1);
        color: white;
        text-decoration: none;
    }

    .invalid-feedback-futuristic {
        color: #ff6b6b;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
    }

    .futuristic-loader {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(15, 23, 42, 0.9);
        backdrop-filter: blur(10px);
        z-index: 9999;
    }

    .loader-ring {
        width: 60px;
        height: 60px;
        border: 3px solid rgba(102, 126, 234, 0.2);
        border-top: 3px solid var(--primary-gradient, #667eea);
        border-radius: 50%;
        animation: spin 1s linear infinite;
    }

    /* Estilos específicos para new.php */
    .futuristic-background {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: linear-gradient(135deg, #0c0c0c 0%, #1a1a2e 100%);
        z-index: -2;
    }

    .floating-particles {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    }

    .particle {
        position: absolute;
        width: 3px;
        height: 3px;
        background: linear-gradient(45deg, #667eea, #764ba2);
        border-radius: 50%;
        animation: float 6s infinite ease-in-out;
        opacity: 0.7;
    }

    @keyframes float {
        0%, 100% { 
            transform: translateY(0px) rotate(0deg); 
            opacity: 0.7; 
        }
        50% { 
            transform: translateY(-20px) rotate(180deg); 
            opacity: 1; 
        }
    }

    .geometric-lines {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: -1;
    }

    .geometric-line {
        position: absolute;
        width: 1px;
        height: 100px;
        background: linear-gradient(to bottom, transparent, #667eea, transparent);
        animation: slide 8s infinite linear;
        opacity: 0.3;
    }

    .geometric-line:nth-child(1) { top: 10%; left: 20%; animation-delay: 0s; }
    .geometric-line:nth-child(2) { top: 50%; left: 60%; animation-delay: 2s; }
    .geometric-line:nth-child(3) { top: 80%; left: 30%; animation-delay: 4s; }

    @keyframes slide {
        0% { 
            transform: translateX(-50px) rotate(45deg); 
            opacity: 0; 
        }
        50% { 
            opacity: 0.3; 
        }
        100% { 
            transform: translateX(50px) rotate(45deg); 
            opacity: 0; 
        }
    }

    .loading-overlay {
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.8);
        backdrop-filter: blur(10px);
        display: flex;
        align-items: center;
        justify-content: center;
        z-index: 9999;
    }

    .futuristic-loader {
        text-align: center;
    }

    .loader-ring {
        display: inline-block;
        width: 40px;
        height: 40px;
        border: 3px solid rgba(102, 126, 234, 0.3);
        border-radius: 50%;
        border-top-color: #667eea;
        animation: spin 1s ease-in-out infinite;
        margin: 0 5px;
    }

    .loader-ring:nth-child(2) {
        animation-delay: -0.3s;
    }

    .loader-ring:nth-child(3) {
        animation-delay: -0.6s;
    }

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

    .glass-effect {
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
    }

    .futuristic-form {
        position: relative;
    }

    .form-group-futuristic {
        margin-bottom: 1.5rem;
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

    .invalid-feedback-futuristic {
        color: #dc3545;
        font-size: 0.85rem;
        margin-top: 0.5rem;
        display: flex;
        align-items: center;
    }

    .file-upload-container {
        margin-top: 0.5rem;
    }

    .file-drop-zone {
        border: 2px dashed rgba(102, 126, 234, 0.3);
        border-radius: 15px;
        padding: 2rem;
        text-align: center;
        background: rgba(255, 255, 255, 0.05);
        transition: all 0.3s ease;
        cursor: pointer;
        position: relative;
        overflow: hidden;
    }

    .file-drop-zone:hover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.05);
        transform: translateY(-2px);
    }

    .file-drop-zone.dragover {
        border-color: #667eea;
        background: rgba(102, 126, 234, 0.1);
        transform: scale(1.02);
    }

    .file-upload-icon {
        font-size: 2.5rem;
        color: #667eea;
        margin-bottom: 1rem;
    }

    .file-upload-title {
        color: #e0e6ed;
        margin-bottom: 0.5rem;
        font-weight: 600;
    }

    .file-upload-subtitle {
        color: #b8c5d1;
        margin-bottom: 1rem;
        font-size: 0.9rem;
    }

    .file-upload-formats {
        font-size: 0.8rem;
        color: #8a9ba8;
    }

    .file-input-hidden {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }

    .file-preview {
        margin-top: 1rem;
        padding: 1rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 12px;
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    .file-info {
        display: flex;
        align-items: center;
        gap: 1rem;
    }

    .file-icon {
        font-size: 2rem;
        color: #28a745;
    }

    .file-details {
        flex: 1;
    }

    .file-name {
        font-weight: 600;
        color: #e0e6ed;
    }

    .file-size {
        font-size: 0.85rem;
        color: #b8c5d1;
    }

    .btn-remove-file {
        background: none;
        border: none;
        color: #dc3545;
        font-size: 1.2rem;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 50%;
        transition: all 0.3s ease;
    }

    .btn-remove-file:hover {
        background: rgba(220, 53, 69, 0.1);
        transform: scale(1.1);
    }

    .form-actions {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        padding-top: 1.5rem;
        border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .header-actions {
        display: flex;
        align-items: center;
    }

    .btn-back-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        color: white;
        padding: 8px 16px;
        border-radius: 20px;
        font-weight: 500;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        font-size: 0.85rem;
        transition: all 0.3s ease;
        box-shadow: 0 2px 8px rgba(102, 126, 234, 0.2);
    }

    .btn-back-header:hover {
        transform: translateY(-1px);
        box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
        color: white;
        text-decoration: none;
    }

    .info-card-compact {
        background: linear-gradient(135deg, 
            rgba(15, 23, 42, 0.95) 0%, 
            rgba(30, 41, 59, 0.9) 100%);
        backdrop-filter: blur(20px);
        border: 1px solid var(--glass-border, rgba(102, 126, 234, 0.2));
        border-radius: 16px;
        padding: 1.5rem;
        text-align: center;
        transition: all 0.3s ease;
        height: 100%;
    }

    .info-card-compact:hover {
        transform: translateY(-2px);
        box-shadow: 0 12px 30px rgba(102, 126, 234, 0.15);
    }

    .info-icon {
        width: 50px;
        height: 50px;
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
        color: white;
        font-size: 1.2rem;
    }

    .info-content h6 {
        color: #e0e6ed;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .info-content p {
        color: #b8c5d1;
        font-size: 0.9rem;
        margin: 0;
    }

    /* Responsive Design para new.php */
    @media (max-width: 768px) {
        .card-header-compact {
            padding: 1rem;
        }
        
        .card-header-compact .d-flex {
            flex-direction: column;
            align-items: flex-start !important;
            gap: 1rem;
        }
        
        .header-actions {
            width: 100%;
            justify-content: center;
        }
        
        .btn-back-header {
            padding: 10px 20px;
            font-size: 0.9rem;
            width: auto;
        }
    }

    @media (max-width: 576px) {
        .btn-back-header {
            font-size: 0.85rem;
            padding: 8px 16px;
        }
        /* Responsive Design */
        @media (max-width: 768px) {
            .card-header-compact .d-flex.align-items-center.gap-3 {
                flex-direction: column !important;
                gap: 0.75rem !important;
                align-items: stretch !important;
            }
            
            .card-header-compact .d-flex.align-items-center.justify-content-between {
                flex-direction: column !important;
                align-items: stretch !important;
                gap: 1rem !important;
            }
            
            .card-header-compact .d-flex.align-items-center:first-child {
                flex-direction: column !important;
                align-items: center !important;
                text-align: center !important;
                gap: 1rem !important;
            }
            
            .card-header-compact .btn-primary-futuristic,
            .card-header-compact .btn-secondary-futuristic {
                width: 100% !important;
                justify-content: center !important;
            }
            
            .card-header-compact .icon-container {
                flex-shrink: 0 !important;
                align-self: center !important;
                width: 45px !important;
                height: 45px !important;
            }
        }     .form-actions-futuristic {
            flex-direction: column;
        }
        
        .btn-futuristic-primary,
        .btn-futuristic-secondary {
            justify-content: center;
            width: 100%;
        }
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
