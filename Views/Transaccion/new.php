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

<!-- Overlay de carga -->
<div id="loading-overlay" class="loading-overlay d-none">
    <div class="futuristic-loader">
        <div class="loader-ring"></div>
        <div class="loader-ring"></div>
        <div class="loader-ring"></div>
        <div class="loader-text">Procesando archivo...</div>
    </div>
</div>

<!-- Contenido principal -->
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-12 col-lg-8">
            <!-- Tarjeta principal del formulario -->
            <div class="futuristic-card-compact glass-effect scale-in">
                <div class="card-header-compact">
                    <div class="d-flex align-items-center">
                        <div class="icon-container me-3">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <div>
                            <h5 class="mb-0 card-title-compact">Subir Archivo de Movimientos</h5>
                            <small class="text-muted">Selecciona el período y banco para cargar los movimientos</small>
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

<style>
/* Estilos específicos para la vista de subir movimientos */
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

.icon-container {
    width: 50px;
    height: 50px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 15px;
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-size: 1.2rem;
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

.btn-primary-futuristic {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    color: white;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    position: relative;
    overflow: hidden;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-primary-futuristic:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.3);
}

.btn-secondary-futuristic {
    background: rgba(255, 255, 255, 0.1);
    border: 1px solid rgba(255, 255, 255, 0.2);
    color: #e0e6ed;
    padding: 12px 24px;
    border-radius: 25px;
    font-weight: 600;
    transition: all 0.3s ease;
    cursor: pointer;
}

.btn-secondary-futuristic:hover {
    background: rgba(255, 255, 255, 0.2);
    transform: translateY(-1px);
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

.btn-primary-futuristic:hover .btn-glow {
    left: 100%;
}

.invalid-feedback-futuristic {
    color: #dc3545;
    font-size: 0.85rem;
    margin-top: 0.5rem;
    display: flex;
    align-items: center;
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

/* Animaciones y efectos */
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

/* Fondo animado */
.animated-background {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    pointer-events: none;
    z-index: -1;
    overflow: hidden;
}

.particle {
    position: absolute;
    width: 4px;
    height: 4px;
    background: linear-gradient(45deg, #667eea, #764ba2);
    border-radius: 50%;
    animation: float var(--duration) infinite linear;
    animation-delay: var(--delay);
}

.particle:nth-child(1) { top: 20%; left: 10%; }
.particle:nth-child(2) { top: 60%; left: 80%; }
.particle:nth-child(3) { top: 80%; left: 20%; }
.particle:nth-child(4) { top: 30%; left: 70%; }
.particle:nth-child(5) { top: 70%; left: 50%; }

@keyframes float {
    0%, 100% { transform: translateY(0px) rotate(0deg); opacity: 0.7; }
    50% { transform: translateY(-20px) rotate(180deg); opacity: 1; }
}

.geometric-line {
    position: absolute;
    width: 1px;
    height: 100px;
    background: linear-gradient(to bottom, transparent, #667eea, transparent);
    animation: slide var(--duration) infinite linear;
    animation-delay: var(--delay);
}

.geometric-line:nth-child(6) { top: 10%; left: 30%; }
.geometric-line:nth-child(7) { top: 40%; left: 60%; }
.geometric-line:nth-child(8) { top: 70%; left: 90%; }

@keyframes slide {
    0% { transform: translateX(-50px) rotate(45deg); opacity: 0; }
    50% { opacity: 0.5; }
    100% { transform: translateX(50px) rotate(45deg); opacity: 0; }
}

/* Overlay de carga */
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

@keyframes spin {
    to { transform: rotate(360deg); }
}

.loader-text {
    color: white;
    margin-top: 1rem;
    font-weight: 600;
}

.text-gradient {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.glass-effect {
    backdrop-filter: blur(20px);
    -webkit-backdrop-filter: blur(20px);
}
</style>

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

// Incluir la plantilla modular
include dirname(__DIR__) . '/Template/template.php';
?>