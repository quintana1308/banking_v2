<?php
/**
 * Plantilla Base del Sistema Banking ADN
 * Esta plantilla proporciona una estructura modular y reutilizable para todas las vistas
 */

// Configuración por defecto de la plantilla
$templateConfig = [
    'title' => $title ?? 'Banking ADN - Sistema Bancario',
    'description' => $description ?? 'Banking ADN - Sistema Bancario integral desarrollado por ADN Software',
    'theme' => $theme ?? 'dark',
    'showSidebar' => $showSidebar ?? true,
    'showNavbar' => $showNavbar ?? true,
    'showFooter' => $showFooter ?? true,
    'containerClass' => $containerClass ?? 'container-fluid',
    'contentClass' => $contentClass ?? 'content-inner mt-n5 py-0',
    'customCSS' => $customCSS ?? [],
    'customJS' => $customJS ?? [],
    'pageJS' => $pageJS ?? null,
    'breadcrumbs' => $breadcrumbs ?? [],
    'headerActions' => $headerActions ?? []
];

// Iniciar captura de contenido
ob_start();
?>

<!doctype html>
<html lang="es" dir="ltr" data-bs-theme="<?= $templateConfig['theme'] ?>" data-bs-theme-color="theme-color-default">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="<?= htmlspecialchars($templateConfig['description']) ?>">
    <title><?= htmlspecialchars($templateConfig['title']) ?></title>
    
    <?php include __DIR__ . '/components/head.php'; ?>
    
    <!-- CSS Personalizado -->
    <?php if (!empty($templateConfig['customCSS'])): ?>
        <?php foreach ($templateConfig['customCSS'] as $css): ?>
            <link rel="stylesheet" href="<?= $css ?>">
        <?php endforeach; ?>
    <?php endif; ?>
</head>

<body class="<?= $bodyClass ?? '' ?>">
    <!-- Loader -->
    <?php include __DIR__ . '/components/loader.php'; ?>
    
    <!-- Sidebar -->
    <?php if ($templateConfig['showSidebar']): ?>
        <?php include __DIR__ . '/components/sidebar.php'; ?>
    <?php endif; ?>

    <main class="main-content" id="mainContent">
        <!-- Navbar -->
        <?php if ($templateConfig['showNavbar']): ?>
            <?php include __DIR__ . '/components/navbar.php'; ?>
        <?php endif; ?>
        
        <!-- Contenido Principal -->
        <div class="<?= $templateConfig['containerClass'] ?>" id="mainContainer">
            <div class="<?= $templateConfig['contentClass'] ?>">
                <?php 
                // Mostrar alertas si existen
                if (isset($alerts) && !empty($alerts)): 
                    include __DIR__ . '/components/alerts.php';
                endif;
                
                // Contenido de la vista
                if (isset($content)) {
                    echo $content;
                } elseif (isset($viewContent)) {
                    echo $viewContent;
                }
                ?>
            </div>
        </div>

        <!-- Footer -->
        <?php if ($templateConfig['showFooter']): ?>
            <?php include __DIR__ . '/components/footer.php'; ?>
        <?php endif; ?>
    </main>

    <!-- Scripts -->
    <?php include __DIR__ . '/components/scripts.php'; ?>
    
    <!-- JavaScript Personalizado -->
    <?php if (!empty($templateConfig['customJS'])): ?>
        <?php foreach ($templateConfig['customJS'] as $js): ?>
            <script src="<?= $js ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
    
    <!-- Script específico de la página -->
    <?php if ($templateConfig['pageJS']): ?>
        <script src="<?= media() ?>/js/<?= $templateConfig['pageJS'] ?>"></script>
    <?php endif; ?>
    
    <!-- Scripts adicionales -->
    <?php if (isset($additionalScripts)): ?>
        <?= $additionalScripts ?>
    <?php endif; ?>
    
    <!-- CSS Responsive para Template -->
    <style>
        /* Estilos base del template */
        .main-content {
            margin-left: <?= $templateConfig['showSidebar'] ? '280px' : '0' ?>;
            padding-top: 20px; /* Sin padding-top para navbar en escritorio */
            transition: margin-left 0.3s ease;
            min-height: 100vh;
        }
        
        #mainContainer {
            padding: 1rem;
        }
        
        /* Responsive Design para móviles */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0 !important;
                padding-top: 70px !important; /* Espacio para navbar en móviles */
            }
            
            #mainContainer {
                padding: 0.5rem;
            }
            
            /* Ajustar contenido para móviles */
            .container-fluid {
                padding-left: 10px;
                padding-right: 10px;
            }
            
            /* Ocultar elementos decorativos en móviles para mejor rendimiento */
            .floating-particles,
            .geometric-lines {
                display: none;
            }
        }
        
        @media (max-width: 480px) {
            .main-content {
                padding-top: 60px !important; /* Espacio para navbar en móviles pequeños */
            }
            
            #mainContainer {
                padding: 0.25rem;
            }
            
            .container-fluid {
                padding-left: 5px;
                padding-right: 5px;
            }
        }
    </style>
</body>
</html>

<?php
// Finalizar captura y limpiar buffer
$templateOutput = ob_get_clean();
echo $templateOutput;
?>
