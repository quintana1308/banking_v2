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

                            <!-- Empresa -->
                            <div class="col-md-6">
                                <div class="form-group-futuristic">
                                    <label class="form-label-futuristic">
                                        <i class="fas fa-building me-2"></i>
                                        Empresa
                                    </label>
                                    <div class="input-container">
                                        <select class="form-control-futuristic" name="id_enterprise" id="id_enterprise" required>
                                            <option value="">-- Seleccione Empresa --</option>
                                            <?php foreach ($data['empresas'] as $empresa): ?>
                                            <option value="<?= $empresa['id'] ?>"><?= $empresa['name'] ?></option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="input-border"></div>
                                    </div>
                                    <div class="invalid-feedback-futuristic d-none" id="messageEmpresa">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        El campo es obligatorio
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

<script src="<?= media() ?>/js/<?= $data["page_functions_js"] ?>"></script>
<?php
$content = ob_get_clean();

// Incluir la plantilla modular
include dirname(__DIR__) . '/Template/template.php';
?>
