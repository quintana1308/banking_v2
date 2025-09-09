<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Banking ADN - Subir Movimientos',
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
    ['title' => 'Transacciones', 'url' => base_url() . '/transaccion'],
    ['title' => 'Subir Movimientos', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Subir Movimientos',
    'description' => 'Carga archivos de movimientos bancarios de forma rápida y segura',
    'show_button' => true,
    'button_text' => 'Lista de Movimientos',
    'button_url' => base_url() . '/transaccion',
    'button_icon' => 'fas fa-list'
];

// Contenido de la vista
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

<!-- Overlay de carga mejorado -->
<div id="loading-overlay" class="loading-overlay d-none">
    <div class="futuristic-loader">
        <div class="loader-ring"></div>
        <div class="loader-text">Procesando archivo...</div>
        <div class="loader-progress mt-3">
            <div class="progress-bar-futuristic">
                <div class="progress-fill" id="progressFill"></div>
            </div>
            <div class="progress-text mt-2" id="progressText">Preparando...</div>
        </div>
    </div>
</div>

<!-- Contenido principal -->
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <!-- Tarjeta principal del formulario -->
            <div class="futuristic-card-compact glass-effect scale-in">
                <div class="card-header-compact">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-inline-flex align-items-center">
                            <div class="icon-container me-3">
                                <i class="fas fa-cloud-upload-alt"></i>
                            </div>
                            <div class="text-start">
                                <h5 class="mb-0 card-title-compact">Subir Archivo de Movimientos</h5>
                                <small class="text-muted-futuristic">Selecciona el período y banco para cargar los movimientos</small>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-3">
                            <a href="<?= base_url() ?>/transaccion" class="btn-secondary-futuristic text-decoration-none">
                                <span class="btn-glow"></span>
                                <i class="fas fa-arrow-left me-2"></i>
                                Volver al Listado
                            </a>
                        </div>
                    </div>
                </div>

                <div class="card-body p-4">
                    <form name="formNewTransaction" id="formNewTransaction" class="futuristic-form">
                        <div class="row g-4">
                            <!-- Año -->
                            <div class="col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-calendar-alt me-2"></i>
                                        Año
                                    </label>
                                    <div class="input-container">
                                        <select class="form-control-futuristic" name="anio" id="anio" required>
                                            <?php foreach ($data['years'] as $year): ?>
                                            <option value="<?= $year ?>" <?= ($year == $data['currentYear']) ? 'selected' : '' ?>>
                                                <?= $year ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageAnio">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>

                            <!-- Mes -->
                            <div class="col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-calendar me-2"></i>
                                        Mes
                                    </label>
                                    <div class="input-container">
                                        <select class="form-control-futuristic" name="mes" id="mes" required>
                                            <?php foreach ($data['months'] as $index => $month): ?>
                                            <option value="<?= $index + 1 ?>" <?= ($index + 1 == $data['currentMonth']) ? 'selected' : '' ?>>
                                                <?= $month ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageMes">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>

                            <!-- Banco -->
                            <div class="col-12">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-university me-2"></i>
                                        Banco y Cuenta
                                    </label>
                                    <div class="input-container">
                                        <select class="form-control-futuristic" name="banco" id="banco" required>
                                            <?php foreach ($data['bank'] as $index => $bank): ?>
                                            <option value="<?= $bank['id'] ?>.<?= $bank['banco'] ?>">
                                                <?= $bank['name'] ?> - <?= $bank['account'] ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageBanco">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>

                            <!-- Archivo -->
                            <div class="col-12">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-file-upload me-2"></i>
                                        Archivo de Movimientos
                                    </label>
                                    <div class="file-upload-container">
                                        <div class="file-drop-zone" id="fileDropZone">
                                            <div class="file-drop-content">
                                                <i class="fas fa-cloud-upload-alt file-upload-icon"></i>
                                                <h6 class="file-upload-title">Arrastra tu archivo aquí</h6>
                                                <p class="file-upload-subtitle">o haz clic para seleccionar</p>
                                                <div class="file-upload-formats">
                                                    Formatos soportados: .xlsx, .xls, .csv
                                                </div>
                                            </div>
                                            <input type="file" class="file-input-hidden" name="archive" id="archive" 
                                                   accept=".xlsx,.xls,.csv" required>
                                        </div>
                                        <div class="file-preview d-none" id="filePreview">
                                            <div class="file-info">
                                                <i class="fas fa-file-excel file-icon"></i>
                                                <div class="file-details">
                                                    <div class="file-name" id="fileName"></div>
                                                    <div class="file-size" id="fileSize"></div>
                                                </div>
                                                <button type="button" class="btn-remove-file" id="removeFile">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageArchive">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Selecciona un archivo válido
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Botones de acción -->
                        <div class="form-actions mt-4">
                            <button type="button" class="btn-secondary-futuristic me-3" onclick="resetForm()">
                                <i class="fas fa-undo me-2"></i>
                                Limpiar
                            </button>
                            <button type="submit" class="btn-primary-futuristic">
                                <i class="fas fa-cloud-upload-alt me-2"></i>
                                Subir Archivo
                                <div class="btn-glow"></div>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Información adicional -->
            <div class="row mt-4 g-3">
                <div class="col-md-4">
                    <div class="info-card-compact glass-effect">
                        <div class="info-icon">
                            <i class="fas fa-info-circle"></i>
                        </div>
                        <div class="info-content">
                            <h6>Formatos Soportados</h6>
                            <p>Excel (.xlsx, .xls) y CSV</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card-compact glass-effect">
                        <div class="info-icon">
                            <i class="fas fa-shield-alt"></i>
                        </div>
                        <div class="info-content">
                            <h6>Seguridad</h6>
                            <p>Datos encriptados y seguros</p>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-card-compact glass-effect">
                        <div class="info-icon">
                            <i class="fas fa-clock"></i>
                        </div>
                        <div class="info-content">
                            <h6>Procesamiento</h6>
                            <p>Carga automática y rápida</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const fileDropZone = document.getElementById('fileDropZone');
    const fileInput = document.getElementById('archive');
    const filePreview = document.getElementById('filePreview');
    const fileName = document.getElementById('fileName');
    const fileSize = document.getElementById('fileSize');
    const removeFileBtn = document.getElementById('removeFile');
    const form = document.getElementById('formNewTransaction');
    const loadingOverlay = document.getElementById('loading-overlay');

    // Drag and drop functionality
    fileDropZone.addEventListener('dragover', function(e) {
        e.preventDefault();
        fileDropZone.classList.add('dragover');
    });

    fileDropZone.addEventListener('dragleave', function(e) {
        e.preventDefault();
        fileDropZone.classList.remove('dragover');
    });

    fileDropZone.addEventListener('drop', function(e) {
        e.preventDefault();
        fileDropZone.classList.remove('dragover');
        
        const files = e.dataTransfer.files;
        if (files.length > 0) {
            handleFileSelect(files[0]);
        }
    });

    fileInput.addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            handleFileSelect(e.target.files[0]);
        }
    });

    function handleFileSelect(file) {
        // Validate file type
        const allowedTypes = ['.xlsx', '.xls', '.csv'];
        const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
        
        if (!allowedTypes.includes(fileExtension)) {
            showError('messageArchive', 'Formato de archivo no válido. Solo se permiten archivos Excel (.xlsx, .xls) y CSV.');
            return;
        }

        // Show file preview
        fileName.textContent = file.name;
        fileSize.textContent = formatFileSize(file.size);
        fileDropZone.style.display = 'none';
        filePreview.classList.remove('d-none');
        
        // Clear any previous errors
        hideError('messageArchive');
    }

    removeFileBtn.addEventListener('click', function() {
        fileInput.value = '';
        fileDropZone.style.display = 'block';
        filePreview.classList.add('d-none');
    });

    function formatFileSize(bytes) {
        if (bytes === 0) return '0 Bytes';
        const k = 1024;
        const sizes = ['Bytes', 'KB', 'MB', 'GB'];
        const i = Math.floor(Math.log(bytes) / Math.log(k));
        return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
    }

    function showError(elementId, message) {
        const errorElement = document.getElementById(elementId);
        errorElement.innerHTML = '<i class="fas fa-exclamation-triangle me-1"></i>' + message;
        errorElement.classList.remove('d-none');
    }

    function hideError(elementId) {
        const errorElement = document.getElementById(elementId);
        errorElement.classList.add('d-none');
    }

    // Form validation and submission
    /*form.addEventListener('submit', function(e) {
        e.preventDefault();
        
        let isValid = true;
        
        // Validate required fields
        const requiredFields = ['anio', 'mes', 'banco', 'archive'];
        requiredFields.forEach(field => {
            const element = document.getElementById(field);
            const messageElement = document.getElementById('message' + field.charAt(0).toUpperCase() + field.slice(1));
            
            if (!element.value) {
                showError('message' + field.charAt(0).toUpperCase() + field.slice(1), 'El campo es obligatorio');
                isValid = false;
            } else {
                hideError('message' + field.charAt(0).toUpperCase() + field.slice(1));
            }
        });

        if (isValid) {
            // Show loading overlay
            loadingOverlay.classList.remove('d-none');
            
            // Simulate form submission (replace with actual AJAX call)
            setTimeout(() => {
                // Here you would normally submit the form via AJAX
                console.log('Form submitted successfully');
                loadingOverlay.classList.add('d-none');
                
                // Show success message or redirect
                alert('Archivo subido exitosamente');
            }, 2000);
        }
    });*/

    // Focus effects for form controls
    document.querySelectorAll('.form-control-futuristic').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentNode.classList.remove('focused');
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

function resetForm() {
    document.getElementById('formNewTransaction').reset();
    document.getElementById('fileDropZone').style.display = 'block';
    document.getElementById('filePreview').classList.add('d-none');
    
    // Hide all error messages
    document.querySelectorAll('.invalid-feedback-futuristic').forEach(error => {
        error.classList.add('d-none');
    });
}

// Estilos adicionales para la barra de progreso
const progressStyles = `
    .progress-bar-futuristic {
        width: 200px;
        height: 6px;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 3px;
        overflow: hidden;
        margin: 0 auto;
    }

    .progress-fill {
        height: 100%;
        background: linear-gradient(90deg, #667eea, #764ba2);
        width: 0%;
        transition: width 0.3s ease;
        border-radius: 3px;
    }

    .progress-text {
        color: rgba(255, 255, 255, 0.8);
        font-size: 0.9rem;
        text-align: center;
    }

    .loader-progress {
        text-align: center;
    }
`;

// Agregar estilos al head
const styleSheet = document.createElement('style');
styleSheet.textContent = progressStyles;
document.head.appendChild(styleSheet);
</script>


<script src="<?= media() ?>/js/<?= $data["page_functions_js"]?>"></script>
<?php
$content = ob_get_clean();

// Incluir la plantilla modular
include dirname(__DIR__) . '/Template/template.php';
?>