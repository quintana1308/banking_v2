<?php

$botonVisualizar = 1;
$url_base = base_url();
$title = "Banking ADN";  // Si deseas un título específico para esta vista
$titleHeader = "Listado de Movimientos";
//$descriptionHeader = "We are on a mission to help developers like you build successful projects for FREE.";
$urlHeader = base_url()."/transaccion/newTransaction";
$buttonHeader = "Subir Movimiento";
ob_start(); // Inicia el almacenamiento en búfer para capturar el contenido

// El contenido específico de la vista home.php
?>

<div class="conatiner-fluid content-inner mt-n5 py-0">
    <div>
        <div class="row mb-3">
			<div class="col-sm-12">
                <div class="card">
					<form id="formFilterTransaction">
						<div class="row card-body">
							<div class="col-sm-8">
								<label for="filtroBank">Cuenta:</label>
								<select id="filterAccount" name="filterAccount" class="form-select" required>
									<option value="">-- Seleccione Cuenta --</option>
									<?php foreach($data['accounts'] as $account){ ?>
									<option value="<?= $account['id_bank']; ?>-<?= $account['account']; ?>"><?= $account['name']; ?> - <?= $account['account']; ?></option>
									<?php }?>
								</select>
							</div>
							<div class="col-sm-4">
								<button type="submit" class="btn btn-primary">Chequear</button>
							</div>
						</div>
					</form>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="card">
                    <div class="row card-body">
                        <div class="col-sm-3">
                            <label for="filtroBank" class="mb-2">Filtrar por banco:</label>
                            <select id="filtroBank" class="form-select">
                                <option value="">Todos</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <label for="filtroAccount" class="mb-2">Filtrar por cuenta:</label>
                            <select id="filtroAccount" class="form-select">
                                <option value="">Todas</option>
                            </select>
                        </div>
                        <div class="col-sm-2">
                            <label for="filtroReference" class="mb-2">Filtrar por referencia:</label>
                            <input type="text" id="filtroReference" class="form-control">
                        </div>
                        <div class="col-sm-2">
                            <label for="filtroDate" class="mb-2">Filtrar por fecha:</label>
                            <input type="date" id="filtroDate" class="form-control">
                        </div>
						<div class="col-sm-2">
                            <label for="filtroDate" class="mb-2">Estados:</label>
                            <select id="filtroEstado" class="form-control">
							  <option value="">Todos</option>
							  <option value="consolidados">Consolidados</option>
							  <option value="no_consolidados">No consolidados</option>
							  <option value="coincidieron">Coincidieron</option>
							</select>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <div class="header-title">
                            <h5 class="card-title">Lista de Movimientos</h5>
                        </div>
                    </div>
                    <div class="card-body px-0">
                        <div class="table-responsive">
                            <table id="transaction-list-table" class="table table-striped" role="grid"
                                data-bs-toggle="data-table">
                                <thead>
                                    <tr style="font-size: 14px;">
                                        <th>Nº</th>
                                        <th>BANCO</th>
                                        <th>CUENTA</th>
                                        <th>REFERENCIA</th>
                                        <th>DATE</th>
                                        <th>MONTO</th>
                                        <th>RESPONSABLE</th>
										<th>ASIGNADO</th>
                                        <th>ESTADO</th>
                                        <!--<th>ACCIÓN</th>-->
                                    </tr>
                                </thead>
                                <tbody style="font-size: 14px;">
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?= media() ?>/js/<?= $data["page_functions_js"]?>"></script>
<?php
$content = ob_get_clean();  // Captura todo el contenido generado por la vista

// Incluye el layout principal (app.php) que contiene el <div class="container">
include dirname(__DIR__) . '/layouts/app.php';

?>