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
                <div class="d-inline-flex align-items-center">
                    <div class="icon-container me-3">
                        <i class="fas fa-edit"></i>
                    </div>
                    <div class="text-start">
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
                                            <option value="BCR" <?= ($data['bank']['banco'] == 'BCR') ? 'selected' : '' ?>>BCR - BANCARIBE</option>
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
                                            <option value="BAC" <?= ($data['bank']['banco'] == 'BAC') ? 'selected' : '' ?>>BAC - CARIBE</option>
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