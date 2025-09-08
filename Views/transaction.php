<?php
// Configuración para usar el sistema de template modular con CSS externo
$templateConfig = [
    'page_title' => 'Banking ADN - Subir Movimientos',
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
    ['title' => 'Transacciones', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = false;

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
    <!-- Loader futurista -->
    <div id="loading-content" class="d-none">
        <div class="futuristic-loader">
            <div class="loader-ring"></div>
            <div class="loader-text">Procesando...</div>
        </div>
    </div>

    <!-- Formulario de subida de movimientos -->
    <div class="row g-3 fade-in-up">
        <div class="col-12">
            <div class="futuristic-card-compact">
                <div class="card-header-compact">
                    <h5 class="card-title-compact">
                        <i class="fas fa-upload me-2"></i>
                        Subir Movimientos Bancarios
                    </h5>
                </div>
                <div class="card-body-compact">
                    <form name="formNewTransaction" id="formNewTransaction">
                        <div class="row g-3">
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Año
                                    </label>
                                    <select class="form-select-futuristic" name="anio" id="anio">
                                        <?php foreach ($data['years'] as $year): ?>
                                        <option value="<?= $year ?>"
                                            <?= ($year == $data['currentYear']) ? 'selected' : '' ?>>
                                            <?= $year ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback-futuristic d-none" id="messageAnio">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-3 col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-calendar me-2"></i>
                                        Mes
                                    </label>
                                    <select class="form-select-futuristic" name="mes" id="mes">
                                        <?php foreach ($data['months'] as $index => $month): ?>
                                        <option value="<?= $index + 1 ?>"
                                            <?= ($index + 1 == $data['currentMonth']) ? 'selected' : '' ?>>
                                            <?= $month ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback-futuristic d-none" id="messageMes">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>
                            <div class="col-lg-6 col-md-12">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-university me-2"></i>
                                        Banco y Cuenta
                                    </label>
                                    <select class="form-select-futuristic" name="banco" id="banco">
                                        <?php foreach ($data['bank'] as $index => $bank): ?>
                                        <option value="<?= $bank['id'] ?>.<?= $bank['banco'] ?>">
                                            <?= $bank['name']  ?> - <?= $bank['account']  ?>
                                        </option>
                                        <?php endforeach; ?>
                                    </select>
                                    <div class="invalid-feedback-futuristic d-none" id="messageBanco">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-file-upload me-2"></i>
                                        Archivo de Movimientos
                                    </label>
                                    <div class="file-upload-futuristic">
                                        <input type="file" class="file-input-futuristic" required name="archive" id="archive">
                                        <div class="file-upload-display">
                                            <i class="fas fa-cloud-upload-alt fa-2x mb-2"></i>
                                            <p class="mb-1">Arrastra tu archivo aquí o haz clic para seleccionar</p>
                                            <small class="text-muted">Formatos soportados: CSV, XLS, XLSX</small>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageArchive">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="form-actions-futuristic">
                            <button type="submit" class="btn-futuristic-primary">
                                <i class="fas fa-upload me-2"></i>
                                Subir Archivo
                                <div class="btn-glow"></div>
                            </button>
                            <a href="<?= base_url() ?>/transaccion/list" class="btn-futuristic-secondary">
                                <i class="fas fa-list me-2"></i>
                                Ver Movimientos
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
/* Estilos específicos para formularios futuristas */
.card-body-compact {
    padding: 2rem;
}

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

.btn-glow {
    position: absolute;
    top: 0;
    left: -100%;
    width: 100%;
    height: 100%;
    background: linear-gradient(90deg, transparent, rgba(255, 255, 255, 0.2), transparent);
    transition: left 0.5s ease;
}

.btn-futuristic-primary:hover .btn-glow {
    left: 100%;
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

.loader-text {
    margin-top: 1rem;
    color: var(--primary-gradient, #667eea);
    font-weight: 600;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive adjustments */
@media (max-width: 768px) {
    .card-body-compact {
        padding: 1.5rem;
    }
    
    .form-actions-futuristic {
        flex-direction: column;
    }
    
    .btn-futuristic-primary,
    .btn-futuristic-secondary {
        justify-content: center;
        width: 100%;
    }
}
</style>

<script>
// Efectos futuristas para el formulario
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
    setInterval(createParticle, 4000);
    
    // Efectos para el input de archivo
    const fileInput = document.getElementById('archive');
    const fileUploadDisplay = document.querySelector('.file-upload-display');
    
    if (fileInput && fileUploadDisplay) {
        fileInput.addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                fileUploadDisplay.innerHTML = `
                    <i class="fas fa-file-check fa-2x mb-2 text-success"></i>
                    <p class="mb-1 text-success">${file.name}</p>
                    <small class="text-muted">Archivo seleccionado correctamente</small>
                `;
            }
        });
    }
    
    // Efectos de focus para los selects
    const selects = document.querySelectorAll('.form-select-futuristic');
    selects.forEach(select => {
        select.addEventListener('focus', function() {
            this.parentElement.style.transform = 'translateY(-2px)';
        });
        
        select.addEventListener('blur', function() {
            this.parentElement.style.transform = 'translateY(0)';
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
        }, 1500);
    });
});
</script>

<style>
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

<script src="<?= media() ?>/js/<?= $data["page_functions_js"]?>"></script>

<?php
$content = ob_get_clean();

// Incluir el template principal
include __DIR__ . '/Template/template.php';
?>