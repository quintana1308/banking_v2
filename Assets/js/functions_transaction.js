document.addEventListener('DOMContentLoaded', function () {

    // Define qué columnas ocultar según el tipo de usuario
    let hiddenColumns = [];

    if (typeUser == 1) {
        hiddenColumns = [7, 6];
    } else if (typeUser == 2) {
        hiddenColumns = []; 
    }

    // Agregar columna de eliminar (índice 9) a columnas ocultas si el usuario no tiene permisos
    if (typeof canDeleteTransactions !== 'undefined' && !canDeleteTransactions) {
        hiddenColumns.push(9); // Columna de acciones/eliminar
    }

    tableTransaction = $('#transaction-list-table').DataTable({
        "aProcessing": true,
        "aServerSide": true,
        "language": {
            "processing": "Procesando...",
            "lengthMenu": "Mostrar _MENU_ registros",
            "zeroRecords": "No se encontraron resultados",
            "emptyTable": "Ningún dato disponible en esta tabla",
            "infoEmpty": "Mostrando registros del 0 al 0 de un total de 0 registros",
            "infoFiltered": "(filtrado de un total de _MAX_ registros)",
            "search": "Buscar:",
            "infoThousands": ",",
            "loadingRecords": "Cargando...",
            "paginate": {
                "first": "Primero",
                "last": "Último",
                "next": ">",
                "previous": "<"
            },
            "info": "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros"
        },
        "ajax": {
            "url": " " + base_url + "/transaccion/getMovimientos",
            "data": function (d) {
                // incluir filtros personalizados
                d.bank = $('#filtroBank').val();
                d.account = $('#filtroAccount').val();
                d.reference = $('#filtroReference').val();
                d.date = $('#filtroDate').val();
                d.estado = $('#filtroEstado').val();
            }
        },
        "deferLoading": 0,
        "columns": [
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row, meta) {
                  // Numeración continua considerando paginación (serverSide)
                  const start = tableTransaction.page.info().start;
                  return start + meta.row + 1;
                }
            }, //0
            { data: 'bank' }, //1
            { data: 'account' }, //2
            { data: 'reference' }, //3
            { 
                data: 'date',
                render: function (data, type, row) {
                    if (data && data !== '') {
                        // Dividir la fecha manualmente para evitar problemas de zona horaria
                        const dateParts = data.split('-');
                        if (dateParts.length === 3) {
                            const year = parseInt(dateParts[0]);
                            const month = parseInt(dateParts[1]);
                            const day = parseInt(dateParts[2]);
                            
                            // Validar que sean números válidos
                            if (!isNaN(year) && !isNaN(month) && !isNaN(day)) {
                                const dayStr = String(day).padStart(2, '0');
                                const monthStr = String(month).padStart(2, '0');
                                return `${dayStr}/${monthStr}/${year}`;
                            }
                        }
                    }
                    return data;
                }
            }, // 4
            {
                "data": "amount",
                "className": "text-end",
                "render": function (data, type, row) {
                    const num = Number(data);
                    const formatted = isNaN(num)
                        ? data
                        : num.toLocaleString('es-VE', { minimumFractionDigits: 2, maximumFractionDigits: 2 });
                    const color = !isNaN(num) && num >= 0 ? 'green' : 'red';
                    return '<span class="d-block" style="color:' + color + ';">' + formatted + '</span>';
                }
            }, // 5
            { data: 'responsible' }, // 6
            {
                "data": "id",
                "render": function (data, type, row) {
                    if (row.id_user) {
                        return `<span class="badge bg-success" style="cursor: default;">${row.name_user}</span>`;
                    } else {
                        return `<button class="btn btn-primary btn-sm btn-asignar" data-id="${data}">Asignar</button>`;
                    }
                },
                "orderable": false,
                "searchable": false
            }, // 7
            { 
                data: 'status_name',
                render: function (data, type, row) {
                    let icon = '';
                    let colorClass = '';
                    
                    // Definir iconos según el status_id
                    switch (parseInt(row.status_id)) {
                        case 1: // No conciliado
                            icon = 'fas fa-times-circle';
                            colorClass = 'text-danger';
                            break;
                        case 2: // Conciliado 
                            icon = 'fas fa-check-circle';
                            colorClass = 'text-success';
                            break;
                        case 3: // Parcial
                            icon = 'fas fa-spinner';
                            colorClass = 'text-info';
                            break;
                        case 4: // Asignado
                            icon = 'fa-solid fa-hand';
                            colorClass = 'text-info';
                            break;
                        default:
                            icon = 'fas fa-times-circle';
                            colorClass = 'text-danger';
                    }
                    
                    return `<i class="${icon} ${colorClass}" title="${data}"></i> <span class="ms-1">${data}</span>`;
                }
            }, // 8
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    if (row.can_delete) {
                        return `<button class="btn btn-danger btn-sm btn-delete" data-id="${row.id}" title="Eliminar transacción">
                                    <i class="fas fa-trash"></i>
                                </button>`;
                    } else {
                        return '<span class="text-muted">-</span>';
                    }
                },
                className: 'text-center'
            } // 9
        ],
        columnDefs: [
            { targets: hiddenColumns, visible: false }, // Ocultar bank, account
        ],
        "rowCallback": function (row, data, index) {
            const start = tableTransaction.page.info().start;
            $('td:eq(0)', row).html(start + index + 1);
            
            // Limpiar cualquier color de fondo previo
            $('td', row).each(function () {
                this.style.setProperty('background-color', '', 'important');
                this.style.setProperty('color', '', 'important');
            });
        },
        "responsive": {
            "details": {
                "type": 'column',
                "target": 'tr'
            }
        },
        "searching": false,
        "bDestroy": true,
        "iDisplayLength": 50,
        "order": [[5, "desc"]],
        "scrollX": true,
        "autoWidth": false
    });

    // Función para recargar el DataTable
    function reloadTransactionTable() {
        if (tableTransaction) {
            // Mostrar indicador de carga en el botón
            const btnReload = document.getElementById('btnReloadTable');
            if (btnReload) {
                const originalHTML = btnReload.innerHTML;
                btnReload.innerHTML = '<span class="btn-glow"></span><i class="fas fa-spinner fa-spin me-2"></i>Recargando...';
                btnReload.disabled = true;
                
                // Recargar los datos del DataTable
                tableTransaction.ajax.reload(function() {
                    // Restaurar el botón después de la recarga
                    btnReload.innerHTML = originalHTML;
                    btnReload.disabled = false;
                }, false); // false mantiene la posición actual de la página
            }
        }
    }

    // Event listener para el botón de recarga
    document.addEventListener('click', function(e) {
        if (e.target && e.target.id === 'btnReloadTable') {
            reloadTransactionTable();
        }
    });

    // Variables globales para almacenar todos los datos originales
    let originalTransactionData = [];
    let allBanks = [];
    let allAccounts = [];
    let isFirstLoad = true;

    // Función para actualizar el indicador visual del banco seleccionado
    function updateBankIndicator() {
        const selectedBank = $('#filtroBank').val();
        const displayElement = $('#selectedBankDisplay');

        if (selectedBank && selectedBank !== '') {
            // Banco específico seleccionado
            const shortName = selectedBank.length > 20 ? selectedBank.substring(0, 20) + '...' : selectedBank;
            displayElement.html(`<i class="fas fa-university me-1"></i>${shortName}`);
            displayElement.removeClass('bg-primary').addClass('bg-success');
        } else {
            // Todos los bancos
            displayElement.html(`<i class="fas fa-university me-1"></i>Todos los bancos`);
            displayElement.removeClass('bg-success').addClass('bg-primary');
        }
    }

    // Función para actualizar el filtro de cuentas basado en el banco seleccionado
    function updateAccountFilter() {
        const selectedBank = $('#filtroBank').val();
        const selectedAccount = $('#filtroAccount').val();

        // Filtrar cuentas según el banco seleccionado usando los datos originales
        let filteredAccounts = allAccounts;
        if (selectedBank && originalTransactionData.length > 0) {
            filteredAccounts = originalTransactionData
                .filter(item => item.bank === selectedBank)
                .map(item => item.account);
            filteredAccounts = [...new Set(filteredAccounts)];
        }

        // Actualizar select de cuentas
        const selectAccount = $('#filtroAccount').empty().append('<option value="">Todas</option>');
        filteredAccounts.forEach(account => {
            if (account) {
                const selected = account === selectedAccount ? 'selected' : '';
                selectAccount.append(`<option value="${account}" ${selected}>${account}</option>`);
            }
        });

        // Actualizar indicador visual del banco
        updateBankIndicator();
    }

    // Evento especial para el filtro de banco (filtrado en cascada)
    $('#filtroBank').on('change', function () {
        updateAccountFilter();
        tableTransaction.ajax.reload();
    });

    // Para los demás select
    $('#filtroAccount, #filtroDate, #filtroEstado').on('change', function () {
        tableTransaction.ajax.reload();
    });

    // Filtro de texto (reference) sí puede usar input + change
    $('#filtroReference').on('input change', function () {
        tableTransaction.ajax.reload();
    });

    $('#transaction-list-table').on('xhr.dt', function () {
        
        const data = tableTransaction.ajax.json().data;

        // Obtener valores seleccionados actualmente
        const selectedBank = $('#filtroBank').val();
        const selectedAccount = $('#filtroAccount').val();

        // Solo en la primera carga, almacenar todos los datos originales
        if (isFirstLoad) {
            // Almacenar todos los datos originales para filtrado en cascada
            originalTransactionData = data;

            // Obtener todos los bancos y cuentas únicos
            allBanks = [...new Set(data.map(item => item.bank))];
            allAccounts = [...new Set(data.map(item => item.account))];

            // Actualizar filtroBank con todos los bancos
            const selectBank = $('#filtroBank').empty().append('<option value="">Todos</option>');
            allBanks.forEach(bank => {
                if (bank) {
                    const selected = bank === selectedBank ? 'selected' : '';
                    selectBank.append(`<option value="${bank}" ${selected}>${bank}</option>`);
                }
            });

            // Actualizar filtroAccount inicialmente con todas las cuentas
            updateAccountFilter();

            // Marcar que ya no es la primera carga
            isFirstLoad = false;
        }
    });

    if (document.querySelector("#formNewTransaction")) {
        let formNewTransaction = document.querySelector("#formNewTransaction");
        formNewTransaction.onsubmit = async function (e) {
            e.preventDefault();

            let strAnio = document.querySelector('#anio').value;
            let strMes = document.querySelector('#mes').value;
            let strBanco = document.querySelector('#banco').value;
            let strArchive = document.querySelector('#archive').value;

            if (strAnio == "" || strMes == "" || strBanco == "" || strArchive == "") {
                Swal.fire({
                    title: "Por favor",
                    text: "Todos los campos son obligatorios.",
                    icon: "error",
                    background: '#19233adb',
                    color: '#fff',
                    customClass: {
                        popup: 'futuristic-popup'
                    }
                });
                return false;
            } else {

                try {
                    // Mostrar overlay de carga con progreso
                    const loadingOverlay = document.querySelector("#loading-overlay");
                    const loaderText = loadingOverlay.querySelector('.loader-text');
                    const progressFill = document.querySelector('#progressFill');
                    const progressText = document.querySelector('#progressText');
                    
                    loadingOverlay.classList.remove('d-none');
                    loaderText.textContent = 'Subiendo archivo...';
                    
                    // Simular progreso de subida
                    if (progressFill && progressText) {
                        progressFill.style.width = '30%';
                        progressText.textContent = 'Validando archivo...';
                    }

                    let formData = new FormData(formNewTransaction);

                    const response = await fetch(base_url + '/transaccion/setTransaction', {
                        method: 'POST',
                        body: formData
                    });
                    
                    if (!response.ok) {
                        throw new Error("Error HTTP: " + response.status);
                    }

                    // Cambiar mensaje y progreso durante el procesamiento
                    loaderText.textContent = 'Procesando movimientos...';
                    if (progressFill && progressText) {
                        progressFill.style.width = '70%';
                        progressText.textContent = 'Insertando registros...';
                    }
                    
                    const objData = await response.json();
                    
                    // Completar progreso
                    if (progressFill && progressText) {
                        progressFill.style.width = '100%';
                        progressText.textContent = 'Completado';
                    }

                    // Ocultar overlay de carga
                    loadingOverlay.classList.add('d-none');

                    if (objData.status) {
                        // Mostrar resultado detallado si está disponible
                        let message = objData.msg;
                        if (objData.data && objData.data.processed) {
                            message += `\n\nMovimientos procesados: ${objData.data.processed}`;
                            if (objData.data.inserted) {
                                message += `\nMovimientos insertados: ${objData.data.inserted}`;
                            }
                            if (objData.data.duplicated) {
                                message += `\nMovimientos duplicados: ${objData.data.duplicated}`;
                            }
                        }
                        
                        Swal.fire({
                            title: 'Éxito',
                            text: message,
                            icon: 'success',
                            timer: 2000,
                            background: '#19233adb',
                            color: '#fff',
                            customClass: {
                                popup: 'futuristic-popup'
                            }
                        }).then(() => {
                            window.location = base_url + '/transaccion';
                        });
                    } else {
                        Swal.fire('Atención', objData.msg, 'error');
                    }

                } catch (error) {
                    // Ocultar overlay de carga en caso de error
                    const loadingOverlay = document.querySelector("#loading-overlay");
                    if (loadingOverlay) {
                        loadingOverlay.classList.add('d-none');
                    }
                    
                    Swal.fire({
                        title: 'Error',
                        text: 'Error en la solicitud: ' + error.message,
                        icon: 'error',
                        timer: 3000,
                        timerProgressBar: true,
                        showConfirmButton: true
                    });
                }

            }
        }
    }

    if (document.querySelector("#formFilterTransaction")) {

        let formFilterTransaction = document.querySelector("#formFilterTransaction");
        formFilterTransaction.onsubmit = function (e) {
            e.preventDefault();

            let strAccount = document.querySelector('#filterAccount').value;

            if (strAccount == "") {
                Swal.fire("Por favor", "La cuenta es obligatoria para chequear los movimientos bancarios.", "error");
                return false;
            } else {

                // Mostrar loader
                let divLoading = document.querySelector("#loading");
                divLoading.classList.remove('d-none');

                var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
                var ajaxUrl = base_url + '/transaccion/checkTransaccion';
                var formData = new FormData(formFilterTransaction);
                request.open("POST", ajaxUrl, true);
                request.send(formData);
                request.onreadystatechange = function () {
                    if (request.readyState != 4) return;

                    // Ocultar loader
                    divLoading.classList.add('d-none');

                    if (request.status == 200) {
                        var objData = JSON.parse(request.responseText);
                        if (objData.status) {
                            let content = '';
                            content += 'Resultado de Movimientos <br>';
                            content += 'Consolidados: ' + objData.msg.completos + '.<br>';
                            content += 'Coincidieron: ' + objData.msg.parciales + '.<br>';
                            content += 'No consolidados: ' + objData.msg.sin_coincidencia + '.';

                            Swal.fire({
                                title: 'Completado',
                                html: content,
                                icon: 'success',
                                background: '#19233adb',
                                color: '#fff',
                                customClass: {
                                    popup: 'futuristic-popup'
                                }
                            }).then(() => {
                                window.location = base_url + '/transaccion';
                            });
                        } else {
                            Swal.fire({
                                title: 'Atención',
                                text: objData.msg,
                                icon: 'error',
                                background: '#19233adb',
                                color: '#fff',
                                customClass: {
                                    popup: 'futuristic-popup'
                                }
                            });
                        }
                    } else {
                        Swal.fire({
                            title: "Atención",
                            text: "El archivo no se procesó de manera correcta",
                            icon: "warning",
                            background: '#19233adb',
                            color: '#fff',
                            customClass: {
                                popup: 'futuristic-popup'
                            }
                        });
                    }
                    divLoading.style.display = "none";
                    return false;
                }
            }
        }
    }

    $('#transaction-list-table tbody').on('click', '.btn-asignar', function () {
        const id = $(this).data('id');

        fetch(base_url + '/transaccion/asignarUsuario', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ id: id })
        })
            .then(response => {
                if (!response.ok) throw new Error("Error HTTP: " + response.status);
                return response.json();
            })
            .then(data => {
                if (data.status) {

                    Swal.fire({
                        title: 'Asignado',
                        text: data.message,
                        icon: 'success',
                        timer: 1000,
                        timerProgressBar: true,
                        showConfirmButton: false
                    });

                    tableTransaction.ajax.reload(null, false);
                } else {
                    Swal.fire('Error', data.message, 'error');
                }
            })
            .catch((error) => {
                console.error("Error en fetch:", error);
                Swal.fire('Error', 'Error en la solicitud: ' + error.message, 'error');
            });
    });

    // Event listener para el botón de eliminar transacciones
    $('#transaction-list-table tbody').on('click', '.btn-delete', function () {
        const id = $(this).data('id');
        const row = $(this).closest('tr');
        const rowData = tableTransaction.row(row).data();

        Swal.fire({
            title: '¿Eliminar transacción?',
            html: `
                <div class="text-start">
                    <p><strong>Banco:</strong> ${rowData.bank}</p>
                    <p><strong>Cuenta:</strong> ${rowData.account}</p>
                    <p><strong>Referencia:</strong> ${rowData.reference}</p>
                    <p><strong>Monto:</strong> ${rowData.amount}</p>
                </div>
                <div class="alert alert-warning mt-3">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    Esta acción no se puede deshacer
                </div>
            `,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#dc3545',
            cancelButtonColor: '#6c757d',
            confirmButtonText: 'Sí, eliminar',
            cancelButtonText: 'Cancelar',
            background: '#19233adb',
            color: '#fff',
            customClass: {
                popup: 'futuristic-popup'
            }
        }).then((result) => {
            if (result.isConfirmed) {
                // Mostrar loader en el botón
                const btnDelete = $(this);
                const originalHTML = btnDelete.html();
                btnDelete.html('<i class="fas fa-spinner fa-spin"></i>').prop('disabled', true);

                fetch(base_url + '/transaccion/deleteTransaction', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    },
                    body: JSON.stringify({ id: id })
                })
                .then(response => {
                    if (!response.ok) throw new Error("Error HTTP: " + response.status);
                    return response.json();
                })
                .then(data => {
                    if (data.status) {
                        Swal.fire({
                            title: '¡Eliminado!',
                            text: data.message,
                            icon: 'success',
                            timer: 2000,
                            timerProgressBar: true,
                            showConfirmButton: false,
                            background: 'var(--glass-bg)',
                            color: 'var(--text-primary)',
                            customClass: {
                                popup: 'futuristic-popup'
                            }
                        });

                        // Recargar la tabla
                        tableTransaction.ajax.reload(null, false);
                    } else {
                        Swal.fire({
                            title: 'Error',
                            text: data.message,
                            icon: 'error',
                            background: 'var(--glass-bg)',
                            color: 'var(--text-primary)',
                            customClass: {
                                popup: 'futuristic-popup'
                            }
                        });
                        
                        // Restaurar el botón
                        btnDelete.html(originalHTML).prop('disabled', false);
                    }
                })
                .catch((error) => {
                    console.error("Error en fetch:", error);
                    Swal.fire({
                        title: 'Error',
                        text: 'Error en la solicitud: ' + error.message,
                        icon: 'error',
                        background: 'var(--glass-bg)',
                        color: 'var(--text-primary)',
                        customClass: {
                            popup: 'futuristic-popup'
                        }
                    });
                    
                    // Restaurar el botón
                    btnDelete.html(originalHTML).prop('disabled', false);
                });
            }
        });
    });

}, false);

function updateTransactionField(id, field, value, cell, displayText = null) {
    fetch(base_url + '/transaccion/updateField', {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: JSON.stringify({
            id: id,
            field: field,
            value: value
        })
    })
        .then(res => res.json())
        .then(data => {
            if (data.status) {
                // Mostrar texto si viene (para responsable)
                cell.data(displayText ?? value).draw();
                Swal.fire('Actualizado', data.message, 'success');
            } else {
                Swal.fire('Error', data.message, 'error');
                cell.data(cell.data()).draw();
            }
        })
        .catch(() => {
            Swal.fire('Error', 'Ocurrió un error al actualizar', 'error');
            cell.data(cell.data()).draw();
        });
}