<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Banking ADN - Editar Usuario',
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
    ['title' => 'Editar Usuario', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Editar Usuario',
    'description' => 'Modifica la información del usuario seleccionado',
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
                            <i class="fas fa-user-edit"></i>
                        </div>
                        <div class="text-start">
                            <h5 class="mb-0 card-title-compact">Editar Usuario</h5>
                            <small class="text-muted-futuristic">Modifica los datos del usuario: <?= $data['usuario']['name'] ?></small>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4">
                    <form id="formEditUsuario" class="futuristic-form">
                        <input type="hidden" name="id" id="id" value="<?= $data['usuario']['id'] ?>">
                        
                        <div class="row g-3">
                            <!-- Nombre -->
                            <div class="col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-user me-2"></i>
                                        Nombre Completo
                                    </label>
                                    <div class="input-container">
                                        <input type="text" class="form-control-futuristic" name="name" id="name" value="<?= $data['usuario']['name'] ?>" required>
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
                                        <input type="text" class="form-control-futuristic" name="username" id="username" value="<?= $data['usuario']['username'] ?>" required>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageUsername">
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
                                            <option value="<?= $rol['id'] ?>" <?= ($rol['id'] == $data['usuario']['id_rol']) ? 'selected' : '' ?>>
                                                <?= $rol['name'] ?>
                                            </option>
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
                                            <option value="<?= $empresa['id'] ?>" <?= (in_array($empresa['id'], array_column($data['userEnterprises'], 'id_enterprise'))) ? 'selected' : '' ?>>
                                                <?= $empresa['name'] ?>
                                            </option>
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
                                            <option value="1" <?= ($data['usuario']['type'] == 1) ? 'selected' : '' ?>>Normal</option>
                                            <option value="2" <?= ($data['usuario']['type'] == 2) ? 'selected' : '' ?>>Especial</option>
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
                            <div class="col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-trash-alt me-2"></i>
                                        Permisos de Eliminación
                                    </label>
                                    <div class="form-check-container mt-2">
                                        <div class="form-check-futuristic">
                                            <input type="checkbox" class="form-check-input-futuristic" name="delete_mov" id="delete_mov" value="1" <?= ($data['usuario']['delete_mov'] == 1) ? 'checked' : '' ?>>
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
                                        Actualizar Usuario
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

<script>
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
    
    // Inicializar multiselect
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

// Variables globales para multiselect
let selectedEnterprises = [];
let enterpriseData = {};

// Inicializar datos de empresas
<?php foreach ($data['empresas'] as $empresa): ?>
enterpriseData['<?= $empresa['id'] ?>'] = '<?= $empresa['name'] ?>';
<?php endforeach; ?>

// Inicializar empresas seleccionadas del usuario
<?php foreach ($data['userEnterprises'] as $userEnterprise): ?>
selectedEnterprises.push('<?= $userEnterprise['enterprise_id'] ?>');
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
