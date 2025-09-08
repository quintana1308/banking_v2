<?php

$botonVisualizar = 0;
$url_base = base_url();
$title = "Log Session";  // Si deseas un título específico para esta vista
$titleHeader = "Listado de Log Session";
//$descriptionHeader = "We are on a mission to help developers like you build successful projects for FREE.";
$urlHeader = base_url()."/home";
$buttonHeader = "Lista de boton";
ob_start(); // Inicia el almacenamiento en búfer para capturar el contenido

// El contenido específico de la vista home.php


?>

<div class="conatiner-fluid content-inner mt-n5 py-0">
    <div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h4 class="card-title">Lista de Log Sessión</h4>
                        </div>
                    </div>
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table id="user-list-table" class="table table-striped" role="grid"
                                data-bs-toggle="data-table">
                                <thead>
                                    <tr>
                                        <th>Nº</th>
                                        <th>RIF EMPRESA</th>
                                        <th>USUARIO</th>
                                        <th>FECHA</th>
                                        <th class="text-center" style="min-width: 100px">ACCIÓN</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($data['getLogSession'] as $key => $value) { ?>
                                    <tr>
                                        <td><?= $value['ID'] ?></td>
                                        <td><?= $value['RIF'] ?></td>
                                        <td><?= $value['USUARIO'] ?></td>
                                        <td><?= $value['FECHA'] ?></td>
                                        <td class="text-center">
                                            <div class="flex align-items-center list-user-action">
                                                <a class="btn btn-sm btn-icon btn-primary" data-bs-toggle="tooltip"
                                                    data-bs-placement="top" title="Detalle" href="<?= base_url() ?>/LogSession/show?id=<?= $value['ID']?>">
                                                    <span class="btn-inner">
                                                        <svg class="icon-20" width="20" viewBox="0 0 24 24" fill="none"
                                                            xmlns="http://www.w3.org/2000/svg">
                                                            <path
                                                                d="M1 12C2.8 7 7.3 4 12 4C16.7 4 21.2 7 23 12C21.2 17 16.7 20 12 20C7.3 20 2.8 17 1 12Z"
                                                                stroke="currentColor" stroke-width="1.5"
                                                                stroke-linecap="round" stroke-linejoin="round" />
                                                            <circle cx="12" cy="12" r="3" stroke="currentColor"
                                                                stroke-width="1.5" stroke-linecap="round"
                                                                stroke-linejoin="round" />
                                                        </svg>
                                                    </span>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                    <?php } ?>

                                </tbody>
                            </table>
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