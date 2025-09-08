<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Banking ADN - Editar Empresa',
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
    ['title' => 'Gestión', 'url' => ''],
    ['title' => 'Empresas', 'url' => base_url() . '/enterprise'],
    ['title' => 'Editar', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Editar Empresa',
    'subtitle' => 'Modifica los datos de la empresa seleccionada',
    'icon' => 'fas fa-edit',
    'actions' => [
        [
            'title' => 'Volver al Listado',
            'url' => base_url() . '/enterprise',
            'icon' => 'fas fa-arrow-left',
            'class' => 'btn-gradient-secondary'
        ]
    ]
];

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

<div class="container-fluid px-4 py-4">
    <div id="loading-content" class="d-none">
        <div class="loader simple-loader loadingNew">
            <div class="loader-body"></div>
        </div>
    </div>
    
    <!-- Tarjeta principal con estilo futurista -->
    <div class="futuristic-card-compact mb-4">
        <div class="card-header-compact">
            <div class="d-flex align-items-center justify-content-between">
                <div class="d-flex align-items-center">
                    <div class="icon-container me-3">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div>
                        <h5 class="mb-0 card-title-compact">Editar Empresa</h5>
                        <small class="text-muted-futuristic">Modifica los datos de la empresa seleccionada</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url() ?>/enterprise" class="btn-secondary-futuristic text-decoration-none">
                        <span class="btn-glow"></span>
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al Listado
                    </a>
                </div>
            </div>
        </div>
        
        <div class="futuristic-card-body">
            <form name="formEditEnterprise" id="formEditEnterprise">
                <input type="hidden" name="id" value="<?= $data['enterprise']['id'] ?>">
                <div class="row">
                    <div class="col-md-12" id="name">
                        <div class="form-group">
                            <label class="form-label">Nombre</label>
                            <input type="text" class="form-control mb-3" name="name" id="name" value="<?= $data['enterprise']['name'] ?>">
                            <div class="invalid-feedback d-none" id="messageName">
                                El campo es obligatorio
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" id="bd">
                        <div class="form-group">
                            <label class="form-label">BD</label>
                            <input type="text" class="form-control mb-3" name="bd" id="bd" value="<?= $data['enterprise']['bd'] ?>">
                            <div class="invalid-feedback d-none" id="messageBd">
                                El campo es obligatorio
                            </div>
                        </div>
                    </div>
                    <div class="col-md-3" id="rif">
                        <div class="form-group">
                            <label class="form-label">Rif</label>
                            <input type="text" class="form-control mb-3" name="rif" id="rif" value="<?= $data['enterprise']['rif'] ?>">
                            <div class="invalid-feedback d-none" id="messageRif">
                                El campo es obligatorio
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6" id="token">
                        <div class="form-group">
                            <label class="form-label">Token</label>
                            <input type="text" class="form-control mb-3" name="token" id="token" value="<?= $data['enterprise']['token'] ?>">
                            <div class="invalid-feedback d-none" id="messageToken">
                                El campo es obligatorio
                            </div>
                        </div>
                    </div>
                </div>
                <div class="d-flex justify-content-end mt-4">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-edit me-2"></i>
                        Actualizar Empresa
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Estilos futuristas para formulario de edición - exactos de bank.php */
.futuristic-card-compact {
    background: linear-gradient(135deg, 
        rgba(15, 23, 42, 0.95) 0%, 
        rgba(30, 41, 59, 0.9) 100%);
    backdrop-filter: blur(20px);
    border: 1px solid rgba(102, 126, 234, 0.2);
    border-radius: 16px;
    box-shadow: 0 12px 30px rgba(102, 126, 234, 0.15);
    overflow: hidden;
    transition: all 0.3s ease;
}

.futuristic-card-compact:hover {
    transform: translateY(-2px);
    box-shadow: 0 20px 40px rgba(102, 126, 234, 0.2);
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

/* Estilos para formularios */
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

/* Botón futurista */
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

/* Mensajes de validación */
.invalid-feedback {
    color: #ef4444;
    font-size: 0.875rem;
    margin-top: 0.25rem;
}

/* Loader styles */
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

/* Estilos del encabezado como transaccion.php */
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

/* Botón futurista secundario */
.btn-secondary-futuristic {
    position: relative;
    background: linear-gradient(135deg, #6c757d 0%, #495057 100%);
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
    box-shadow: 0 8px 25px rgba(108, 117, 125, 0.4);
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

.btn-secondary-futuristic:hover .btn-glow {
    left: 100%;
}

.card-title-compact {
    color: white;
    font-weight: 600;
    font-size: 1.1rem;
}

.card-header-compact {
    background: rgba(255, 255, 255, 0.05);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    padding: 1.5rem;
    border-radius: 16px 16px 0 0;
}

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

<script>
document.addEventListener('DOMContentLoaded', function() {
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

<script src="<?= media() ?>/js/<?= $data["page_functions_js"]?>"></script>

<?php
$content = ob_get_clean();

// Incluir el template principal
include_once __DIR__ . '/../Template/template.php';
?>