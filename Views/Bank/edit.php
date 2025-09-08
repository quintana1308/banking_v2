<?php
// Configuración para usar el sistema de template modular
$templateConfig = [
    'page_title' => 'Banking ADN - Editar Cuenta Bancaria',
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
    ['title' => 'Cuentas Bancarias', 'url' => base_url() . '/bank'],
    ['title' => 'Editar Cuenta', 'url' => '', 'active' => true]
];

// Configurar header de página
$pageHeader = [
    'title' => 'Editar Cuenta Bancaria',
    'subtitle' => 'Modifica los datos de la cuenta bancaria seleccionada',
    'icon' => 'fas fa-edit',
    'actions' => [
        [
            'title' => 'Volver al Listado',
            'url' => base_url() . '/bank',
            'icon' => 'fas fa-arrow-left',
            'class' => 'btn-gradient-secondary'
        ]
    ]
];

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
                        <h5 class="mb-0 card-title-compact">Editar Cuenta Bancaria</h5>
                        <small class="text-muted-futuristic">Modifica los datos de la cuenta bancaria seleccionada</small>
                    </div>
                </div>
                <div class="d-flex align-items-center gap-3">
                    <a href="<?= base_url() ?>/bank" class="btn-secondary-futuristic text-decoration-none">
                        <span class="btn-glow"></span>
                        <i class="fas fa-arrow-left me-2"></i>
                        Volver al Listado
                    </a>
                </div>
            </div>
        </div>
        
        <div class="futuristic-card-body">
            <form name="formEditBank" id="formEditBank">
                            <input type="hidden" name="id" value="<?= $data['bank']['id'] ?>">
                            <div class="row">
                                <div class="col-md-6" id="name">
                                    <div class="form-group">
                                        <label class="form-label">Nombre</label>
                                        <input type="text" class="form-control mb-3" name="name" id="name" value="<?= $data['bank']['name'] ?>">
                                        <div class="invalid-feedback d-none" id="messageName">
                                            El campo es obligatorio
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6" id="account">
                                    <div class="form-group">
                                        <label class="form-label">Cuenta</label>
                                        <input type="number" class="form-control mb-3" name="account" id="account" value="<?= $data['bank']['account'] ?>">
                                        <div class="invalid-feedback d-none" id="messageAccount">
                                            El campo es obligatorio
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-2" id="idBank">
                                    <div class="form-group">
                                        <label class="form-label">ID Banco</label>
                                        <input type="number" class="form-control mb-3" name="id_bank" id="id_bank" value="<?= $data['bank']['id_bank'] ?>">
                                        <div class="invalid-feedback d-none" id="messageIdBank">
                                            El campo es obligatorio
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-5" id="enterprise">
                                    <div class="form-group">
                                        <label class="form-label">Empresa</label>
                                        <select class="form-select mb-3 shadow-none" name="id_enterprise" id="id_enterprise">
                                            <?php foreach ($data['enterprise'] as $index => $enterprise): ?>
                                            <option value="<?= $enterprise['id']; ?>" <?= ($data['bank']['id_enterprise'] == $enterprise['id']) ? 'selected' : '' ?>>
                                                <?= $enterprise['name']; ?>
                                            </option>
                                            <?php endforeach; ?>
                                        </select>
                                        <div class="invalid-feedback d-none" id="messageEnterprise">
                                            El campo es obligatorio
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="col-md-5" id="prex">
                                    <div class="form-group">
                                        <label class="form-label">Prefijo</label>
                                        <select class="form-select mb-3 shadow-none" name="prefix" id="prefix">
                                            <option value="BTC" <?= ($data['bank']['banco'] == 'BTC') ? 'selected' : '' ?>>BTC - BICENTENARIO</option>
                                            <option value="BNC" <?= ($data['bank']['banco'] == 'BNC') ? 'selected' : '' ?>>BNC - BNC</option>
                                            <option value="BDT" <?= ($data['bank']['banco'] == 'BDT') ? 'selected' : '' ?>>BDT - DEL TESORO</option>
                                            <option value="BCM" <?= ($data['bank']['banco'] == 'BCM') ? 'selected' : '' ?>>BCM - BANCAMIGA</option>
                                            <option value="BCO" <?= ($data['bank']['banco'] == 'BCO') ? 'selected' : '' ?>>BCO - BANESCO</option>
                                            <option value="SFT" <?= ($data['bank']['banco'] == 'SFT') ? 'selected' : '' ?>>SFT - SOFITASA</option>
                                            <option value="VNZ" <?= ($data['bank']['banco'] == 'VNZ') ? 'selected' : '' ?>>VNZ - VENEZUELA</option>
                                            <option value="PRV" <?= ($data['bank']['banco'] == 'PRV') ? 'selected' : '' ?>>PRV - PROVINCIAL</option>
                                            <option value="MRC" <?= ($data['bank']['banco'] == 'MRC') ? 'selected' : '' ?>>MRC - MERCANTIL</option>
                                            <option value="ACT" <?= ($data['bank']['banco'] == 'ACT') ? 'selected' : '' ?>>ACT - ACTIVO</option>
                                            <option value="PLZ" <?= ($data['bank']['banco'] == 'PLZ') ? 'selected' : '' ?>>PLZ - PLAZA</option>
                                            <option value="BPL" <?= ($data['bank']['banco'] == 'BPL') ? 'selected' : '' ?>>BPL - BANPLUS</option>
                                            <option value="TSR" <?= ($data['bank']['banco'] == 'TSR') ? 'selected' : '' ?>>TSR - TESORO</option>
                                        </select>
                                        <div class="invalid-feedback d-none" id="messagePrex">
                                            El campo es obligatorio
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="d-flex justify-content-end mt-4">
                                <button type="submit" class="btn btn-primary">
                                    <i class="fas fa-edit me-2"></i>
                                    Actualizar Cuenta
                                </button>
                            </div>
            </form>
        </div>
    </div>
</div>

<style>
/* Estilos futuristas para formulario de edición de banco */
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