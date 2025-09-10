<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Banking ADN - Nuevo Usuario',
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
    ['title' => 'Usuarios', 'url' => base_url() . '/usuario/usuarios'],
    ['title' => 'Nuevo Usuario', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Crear Nuevo Usuario',
    'description' => 'Registra un nuevo usuario en el sistema',
    'show_button' => true,
    'button_text' => 'Volver a Lista',
    'button_url' => base_url() . '/usuario/usuarios',
    'button_icon' => 'fas fa-arrow-left'
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

<!-- Contenido principal -->
<div class="container-fluid p-4">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="futuristic-card-compact glass-effect scale-in">
                <div class="card-header-compact">
                    <div class="d-inline-flex align-items-center">
                        <div class="icon-container me-3">
                            <i class="fas fa-user-plus"></i>
                        </div>
                        <div class="text-start">
                            <h5 class="mb-0 card-title-compact">Formulario de Nuevo Usuario</h5>
                            <small class="text-muted-futuristic">Complete todos los campos para crear el usuario</small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="formNewUsuario" class="futuristic-form">
                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-user me-2"></i>
                                        Nombre Completo
                                    </label>
                                    <div class="input-container">
                                        <input type="text" class="form-control-futuristic" name="name" id="name" required>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageName">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>

                            <!-- Usuario -->
                            <div class="col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-at me-2"></i>
                                        Nombre de Usuario
                                    </label>
                                    <div class="input-container">
                                        <input type="text" class="form-control-futuristic" name="username" id="username" required>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageUsername">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>

                            <!-- Contraseña -->
                            <div class="col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-lock me-2"></i>
                                        Contraseña
                                    </label>
                                    <div class="input-container position-relative">
                                        <input type="password" class="form-control-futuristic" name="password" id="password" required>
                                        <button type="button" class="password-toggle-btn" onclick="togglePassword('password')">
                                            <i class="fas fa-eye" id="password-icon"></i>
                                        </button>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messagePassword">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>

                            <!-- Rol -->
                            <div class="col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-user-tag me-2"></i>
                                        Rol
                                    </label>
                                    <div class="input-container">
                                        <select class="form-control-futuristic" name="id_rol" id="id_rol" required>
                                            <option value="">-- Seleccione Rol --</option>
                                            <?php foreach ($data['roles'] as $rol): ?>
                                            <option value="<?= $rol['id'] ?>"><?= $rol['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageRol">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>

                            <!-- Empresas (Multiselect) -->
                            <div class="col-12">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-building me-2"></i>
                                        Empresas Asignadas
                                    </label>
                                    <small class="text-muted-futuristic d-block mb-2">
                                        Seleccione una o más empresas. La primera seleccionada será la empresa principal.
                                    </small>
                                    <div class="input-container">
                                        <select class="form-control-futuristic" name="empresas[]" id="empresas" multiple required>
                                            <?php foreach ($data['empresas'] as $empresa): ?>
                                            <option value="<?= $empresa['id'] ?>"><?= $empresa['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageEmpresas">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Debe seleccionar al menos una empresa
                                    </div>
                                    <div class="selected-enterprises mt-2" id="selectedEnterprises">
                                        <!-- Aquí se mostrarán las empresas seleccionadas -->
                                    </div>
                                </div>
                            </div>

                            <!-- Tipo de Usuario -->
                            <div class="col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-users-cog me-2"></i>
                                        Tipo de Usuario
                                    </label>
                                    <div class="input-container">
                                        <select class="form-control-futuristic" name="type" id="type" required>
                                            <option value="">-- Seleccione Tipo --</option>
                                            <option value="1">Normal</option>
                                            <option value="2">Especial</option>
                                        </select>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageType">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
                                    </div>
                                </div>
                            </div>

                            <!-- Puede Eliminar Movimientos -->
                            <div class="col-12">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-trash-alt me-2"></i>
                                        Permisos de Eliminación
                                    </label>
                                    <div class="form-check-container mt-2">
                                        <div class="form-check-futuristic">
                                            <input type="checkbox" class="form-check-input-futuristic" name="delete_mov" id="delete_mov" value="1">
                                            <label class="form-check-label-futuristic" for="delete_mov">
                                                Puede eliminar transacciones del listado
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="col-12">
                                <div class="d-flex gap-3 justify-content-end mt-4">
                                    <a href="<?= base_url() ?>/usuario/usuarios" class="btn-secondary-futuristic">
                                        <i class="fas fa-times me-2"></i>
                                        Cancelar
                                    </a>
                                    <button type="submit" class="btn-primary-futuristic">
                                        <span class="btn-glow"></span>
                                        <i class="fas fa-save me-2"></i>
                                        Crear Usuario
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.password-toggle-btn {
    position: absolute;
    right: 15px;
    top: 50%;
    transform: translateY(-50%);
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 5px;
    border-radius: 4px;
    transition: all 0.3s ease;
    z-index: 10;
}

.password-toggle-btn:hover {
    color: var(--primary-color);
    background: rgba(102, 126, 234, 0.1);
}

.password-toggle-btn:focus {
    outline: none;
    color: var(--primary-color);
}

/* Estilos para multiselect de empresas */
.selected-enterprises {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.enterprise-tag {
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 20px;
    padding: 0.4rem 0.8rem;
    font-size: 0.85rem;
    color: var(--text-primary);
    display: flex;
    align-items: center;
    gap: 0.5rem;
    transition: all 0.3s ease;
}

.enterprise-tag.primary {
    background: rgba(102, 126, 234, 0.2);
    border-color: var(--accent-color);
    color: var(--accent-color);
}

.enterprise-tag .remove-btn {
    background: none;
    border: none;
    color: var(--text-secondary);
    cursor: pointer;
    padding: 0;
    width: 16px;
    height: 16px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.7rem;
    transition: all 0.3s ease;
}

.enterprise-tag .remove-btn:hover {
    background: rgba(255, 0, 0, 0.2);
    color: #ff4757;
}

.enterprise-tag .primary-badge {
    background: var(--accent-color);
    color: white;
    font-size: 0.7rem;
    padding: 0.1rem 0.4rem;
    border-radius: 10px;
    font-weight: 500;
}

/* Estilos para dropdown multiselect */
.multiselect-btn {
    text-align: left;
    cursor: pointer;
    position: relative;
}

.multiselect-dropdown {
    position: absolute;
    top: 100%;
    left: 0;
    right: 0;
    background: var(--glass-bg);
    border: 1px solid var(--glass-border);
    border-radius: 12px;
    backdrop-filter: blur(10px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
    z-index: 1000;
    max-height: 200px;
    overflow-y: auto;
    margin-top: 5px;
}

.multiselect-option {
    padding: 0.75rem 1rem;
    border-bottom: 1px solid var(--glass-border);
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.multiselect-option:last-child {
    border-bottom: none;
}

.multiselect-option:hover {
    background: rgba(102, 126, 234, 0.1);
}

.multiselect-option input[type="checkbox"] {
    width: 16px;
    height: 16px;
    accent-color: var(--accent-color);
}

.multiselect-option label {
    color: var(--text-primary);
    cursor: pointer;
    margin: 0;
    flex: 1;
}
</style>

<script>
// Función para mostrar/ocultar contraseña
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const icon = document.getElementById(inputId + '-icon');
    
    if (input.type === 'password') {
        input.type = 'text';
        icon.classList.remove('fa-eye');
        icon.classList.add('fa-eye-slash');
    } else {
        input.type = 'password';
        icon.classList.remove('fa-eye-slash');
        icon.classList.add('fa-eye');
    }
}

document.addEventListener('DOMContentLoaded', function() {
    // Focus effects for form controls
    document.querySelectorAll('.form-control-futuristic').forEach(input => {
        input.addEventListener('focus', function() {
            this.parentNode.classList.add('focused');
        });
        
        input.addEventListener('blur', function() {
            this.parentNode.classList.remove('focused');
        });
    });
    
    // Inicializar multiselect de empresas
    initializeMultiselect();
    
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

// Variables globales para el multiselect
let selectedEnterprises = [];
let enterpriseData = {};

// Inicializar datos de empresas
<?php foreach ($data['empresas'] as $empresa): ?>
enterpriseData['<?= $empresa['id'] ?>'] = '<?= $empresa['name'] ?>';
<?php endforeach; ?>

// Inicializar multiselect
function initializeMultiselect() {
    const select = document.getElementById('empresas');
    
    // Crear interfaz personalizada
    createCustomMultiselect();
    
    // Event listener para el select original (por si se usa programáticamente)
    select.addEventListener('change', function() {
        updateSelectedFromSelect();
    });
    
    // Inicializar con valores preseleccionados
    updateSelectedDisplay();
    updateOriginalSelect();
}

// Crear interfaz personalizada para multiselect
function createCustomMultiselect() {
    const container = document.getElementById('selectedEnterprises');
    const selectContainer = document.querySelector('#empresas').parentNode;
    
    // Crear botón para abrir dropdown
    const dropdownBtn = document.createElement('button');
    dropdownBtn.type = 'button';
    dropdownBtn.className = 'form-control-futuristic multiselect-btn';
    dropdownBtn.innerHTML = '<i class="fas fa-building me-2"></i>Seleccionar empresas...';
    dropdownBtn.onclick = toggleDropdown;
    
    // Crear dropdown
    const dropdown = document.createElement('div');
    dropdown.className = 'multiselect-dropdown';
    dropdown.id = 'enterpriseDropdown';
    dropdown.style.display = 'none';
    
    // Agregar opciones al dropdown
    Object.keys(enterpriseData).forEach(id => {
        const option = document.createElement('div');
        option.className = 'multiselect-option';
        option.innerHTML = `
            <input type="checkbox" id="enterprise_${id}" value="${id}" onchange="toggleEnterprise('${id}', this)" ${selectedEnterprises.includes(id) ? 'checked' : ''}>
            <label for="enterprise_${id}">${enterpriseData[id]}</label>
        `;
        dropdown.appendChild(option);
    });
    
    // Insertar elementos
    selectContainer.insertBefore(dropdownBtn, selectContainer.querySelector('.input-border'));
    selectContainer.appendChild(dropdown);
}

// Toggle dropdown
function toggleDropdown() {
    const dropdown = document.getElementById('enterpriseDropdown');
    dropdown.style.display = dropdown.style.display === 'none' ? 'block' : 'none';
}

// Manejar selección de empresa
function toggleEnterprise(enterpriseId, checkbox) {
    if (checkbox.checked) {
        if (!selectedEnterprises.includes(enterpriseId)) {
            selectedEnterprises.push(enterpriseId);
        }
    } else {
        selectedEnterprises = selectedEnterprises.filter(id => id !== enterpriseId);
    }
    
    updateSelectedDisplay();
    updateOriginalSelect();
}

// Actualizar visualización de empresas seleccionadas
function updateSelectedDisplay() {
    const container = document.getElementById('selectedEnterprises');
    container.innerHTML = '';
    
    selectedEnterprises.forEach((id, index) => {
        const tag = document.createElement('div');
        tag.className = `enterprise-tag ${index === 0 ? 'primary' : ''}`;
        tag.innerHTML = `
            <span>${enterpriseData[id]}</span>
            ${index === 0 ? '<span class="primary-badge">Principal</span>' : ''}
            <button type="button" class="remove-btn" onclick="removeEnterprise('${id}')">
                <i class="fas fa-times"></i>
            </button>
        `;
        container.appendChild(tag);
    });
    
    // Actualizar texto del botón
    const btn = document.querySelector('.multiselect-btn');
    if (btn) {
        if (selectedEnterprises.length === 0) {
            btn.innerHTML = '<i class="fas fa-building me-2"></i>Seleccionar empresas...';
        } else {
            btn.innerHTML = `<i class="fas fa-building me-2"></i>${selectedEnterprises.length} empresa(s) seleccionada(s)`;
        }
    }
}

// Remover empresa
function removeEnterprise(enterpriseId) {
    selectedEnterprises = selectedEnterprises.filter(id => id !== enterpriseId);
    
    // Desmarcar checkbox
    const checkbox = document.getElementById(`enterprise_${enterpriseId}`);
    if (checkbox) checkbox.checked = false;
    
    updateSelectedDisplay();
    updateOriginalSelect();
}

// Actualizar select original
function updateOriginalSelect() {
    const select = document.getElementById('empresas');
    
    // Limpiar selecciones
    Array.from(select.options).forEach(option => {
        option.selected = false;
    });
    
    // Seleccionar empresas activas
    selectedEnterprises.forEach(id => {
        const option = select.querySelector(`option[value="${id}"]`);
        if (option) option.selected = true;
    });
}

// Actualizar desde select original
function updateSelectedFromSelect() {
    const select = document.getElementById('empresas');
    selectedEnterprises = Array.from(select.selectedOptions).map(option => option.value);
    updateSelectedDisplay();
}

// Cerrar dropdown al hacer clic fuera
document.addEventListener('click', function(e) {
    const dropdown = document.getElementById('enterpriseDropdown');
    const btn = document.querySelector('.multiselect-btn');
    
    if (dropdown && !dropdown.contains(e.target) && e.target !== btn) {
        dropdown.style.display = 'none';
    }
});
</script>

<script src="<?= media() ?>/js/<?= $data["page_functions_js"] ?>"></script>
<?php
$content = ob_get_clean();

// Incluir la plantilla modular
include dirname(__DIR__) . '/Template/template.php';
?>
