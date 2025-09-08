<?php

$botonVisualizar = 1;
$url_base = base_url();
$title = "Log Session";  // Si deseas un título específico para esta vista
$titleHeader = "Información del Log Session";
//$descriptionHeader = "We are on a mission to help developers like you build successful projects for FREE.";
$urlHeader = base_url()."/LogSession";
$buttonHeader = "Lista de LogSession";
ob_start(); // Inicia el almacenamiento en búfer para capturar el contenido

// El contenido específico de la vista home.php


?>

<div class="conatiner-fluid content-inner mt-n5 py-0">
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex flex-wrap align-items-center justify-content-between">
                        <div class="d-flex flex-wrap align-items-center">
                            <div class="profile-img position-relative me-3 mb-3 mb-lg-0 profile-logo profile-logo1">
                                <img src="<?= media() ?>/images/avatars/01.png" alt="User-Profile"
                                    class="theme-color-default-img img-fluid rounded-pill avatar-100">
                            </div>
                            <div class="d-flex flex-wrap align-items-center mb-3 mb-sm-0">
                                <h4 class="me-2 h4"><?= $data['infoLogSession']['LOG_USUARIO'] ?></h4>
                                <span> - <?= $data['infoLogSession']['NOMBRE_EMPRESA'] ?></span>
                            </div>
                        </div>
                        <!--<ul class="d-flex nav nav-pills mb-0 text-center profile-tab" data-toggle="slider-tab"
                            id="profile-pills-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active show" data-bs-toggle="tab" href="#profile-feed" role="tab"
                                    aria-selected="false">Feed</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#profile-activity" role="tab"
                                    aria-selected="false">Activity</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#profile-friends" role="tab"
                                    aria-selected="false">Friends</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" data-bs-toggle="tab" href="#profile-profile" role="tab"
                                    aria-selected="false">Profile</a>
                            </li>
                        </ul>-->
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-12">
            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Información</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-1"><strong>Usuario:</strong> <?= $data['infoLogSession']['LOG_USUARIO'] ?>
                            </div>
                            <div class="mb-1"><strong>IP:</strong> <?= $data['infoLogSession']['LOG_IP'] ?></div>
                            <div class="mb-1"><strong>Versión:</strong> <?= $data['infoLogSession']['LOG_VERSION'] ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-1"><strong>Fecha:</strong> <?= $data['infoLogSession']['LOG_FECHA'] ?></div>
                            <div class="mb-1"><strong>Sistema Operativo:</strong>
                                <?= $data['infoLogSession']['LOG_SISTEMA_OPERATIVO'] ?></div>
                            <div class="mb-1"><strong>.EXE:</strong> <?= $data['infoLogSession']['LOG_EXE'] ?></div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <div class="header-title">
                        <h4 class="card-title">Empresa</h4>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="mb-1"><strong>Rif:</strong> <?= $data['infoLogSession']['RIF_EMPRESA'] ?></div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1"><strong>Nombre:</strong> <?= $data['infoLogSession']['NOMBRE_EMPRESA'] ?>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="mb-1"><strong>Cliente:</strong>
                                <?php if($data['infoLogSession']['CLIENTE_EMPRESA'] == 1){ ?>
                                <span class="badge bg-success">true</span>
                                <?php }else{ ?>
                                <span class="badge bg-danger">false</span>
                                <?php } ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php

$content = ob_get_clean();  // Captura todo el contenido generado por la vista

$scripts = [
    '<script src="/"></script>',
    '<script src="/"></script>',
];


// Incluye el layout principal (app.php) que contiene el <div class="container">
include 'views/layouts/app.php';
?>