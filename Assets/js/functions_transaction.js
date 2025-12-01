document.addEventListener('DOMContentLoaded', function () {

    // Define qué columnas ocultar según el tipo de usuario
    let hiddenColumns = [];

    if (typeUser == 1) {
        hiddenColumns = [7, 6];
    } else if (typeUser == 2) {
        hiddenColumns = []; 
    }

    // Ocultar columna de ACCIONES solo si el usuario NO tiene ningún permiso
    const hasDeletePermission = typeof canDeleteTransactions !== 'undefined' && canDeleteTransactions;
    const hasCommentPermission = typeof canComment !== 'undefined' && canComment;
    
    // Solo ocultar la columna si NO tiene permisos de eliminar NI de comentar
    if (!hasDeletePermission && !hasCommentPermission) {
        hiddenColumns.push(10); // Columna de acciones (ahora es índice 10)
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
                d.dateFrom = $('#filtroDateFrom').val();
                d.dateTo = $('#filtroDateTo').val();
                d.estado = $('#filtroEstado').val();
                d.monto = $('#filtroMonto').val();
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
            { data: 'bank' }, // 1 - NOMBRE DEL BANCO
            { data: 'account' }, //2 - NUMERO DE LA CUENTA
            { data: 'reference' }, //3 - REFERENCIA
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
            }, // 4 - FECHA DE LA TRANSACCION
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
            }, // 5 - MONTO DE LA TRANSACCION
            { data: 'responsible' }, // 6 - NOMBRE DEL RESPONSABLE
            {
                "data": "id",
                "render": function (data, type, row) {
                    if (row.id_user) {
                        return `<span class="badge bg-success" style="cursor: default;">${row.name_user}</span>`;
                    } else {
                        return `<button class="btn btn-primary btn-sm btn-asignar" data-id="${data}"
                        style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; margin-right: 4px; border-radius: 5px !important;">
                        <i class="fa-solid fa-hand"></i>
                        </button>`;
                    }
                },
                "orderable": false,
                "searchable": false
            }, // 7 - ASIGNADO
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
            }, // 8 - ESTADO
            {
                data: 'creation_date',
                render: function (data, type, row) {
                    if (data && data !== '') {
                        // Convertir la fecha y hora a formato legible
                        const dateTime = new Date(data);
                        if (!isNaN(dateTime.getTime())) {
                            const day = String(dateTime.getDate()).padStart(2, '0');
                            const month = String(dateTime.getMonth() + 1).padStart(2, '0');
                            const year = dateTime.getFullYear();
                            const hours = String(dateTime.getHours()).padStart(2, '0');
                            const minutes = String(dateTime.getMinutes()).padStart(2, '0');
                            
                            return `<div class="text-center">
                                        <div class="fw-bold">${day}/${month}/${year}</div>
                                        <small class="text-muted">${hours}:${minutes}</small>
                                    </div>`;
                        }
                    }
                    return '<span class="text-muted">-</span>';
                }
            }, // 9 - FECHA DE REGISTRO
            {
                data: null,
                orderable: false,
                searchable: false,
                render: function (data, type, row) {
                    let actions = '';
                    
                    // Botón de comentario
                    if (typeof canComment !== 'undefined' && canComment) {
                        const hasComment = row.has_comment || false;
                        const btnClass = hasComment ? 'btn-info' : 'btn-primary';
                        const iconClass = hasComment ? 'fa-eye' : 'fa-comment';
                        const title = hasComment ? 'Ver comentario' : 'Agregar comentario';
                        
                        actions += `<button class="btn ${btnClass} btn-sm btn-comment" 
                                           data-id="${row.id}" 
                                           data-bank="${row.bank}" 
                                           data-account="${row.account}" 
                                           data-reference="${row.reference}" 
                                           data-amount="${row.amount}"
                                           data-has-comment="${hasComment}"
                                           title="${title}"
                                           style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; margin-right: 4px; border-radius: 5px !important;">
                                        <i class="fas ${iconClass}"></i>
                                    </button>`;
                    } else {
                        // Usuario sin permisos para comentar, pero puede ver comentarios existentes
                        const hasComment = row.has_comment || false;
                        
                        if (hasComment) {
                            // Si hay comentario, mostrar botón para ver (solo lectura)
                            actions += `<button class="btn btn-info btn-sm btn-comment" 
                                               data-id="${row.id}" 
                                               data-bank="${row.bank}" 
                                               data-account="${row.account}" 
                                               data-reference="${row.reference}" 
                                               data-amount="${row.amount}"
                                               data-has-comment="true"
                                               data-readonly="true"
                                               title="Ver comentario (solo lectura)"
                                               style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center; margin-right: 4px; border-radius: 5px !important;">
                                            <i class="fas fa-eye"></i>
                                        </button>`;
                        } else {
                            // Si no hay comentario, mostrar icono deshabilitado
                            actions += `<span class="text-muted" title="Sin permisos para comentar" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center; margin-right: 4px;">
                                            <i class="fas fa-comment-slash"></i>
                                        </span>`;
                        }
                    }
                    
                    // Botón de eliminar
                    if (typeof canDeleteTransactions !== 'undefined' && canDeleteTransactions && row.can_delete) {
                        actions += `<button class="btn btn-danger btn-sm btn-delete" data-id="${row.id}" title="Eliminar transacción"
                                           style="width: 32px; height: 32px; padding: 0; display: inline-flex; align-items: center; justify-content: center;">
                                        <i class="fas fa-trash"></i>
                                    </button>`;
                    } else if (typeof canDeleteTransactions !== 'undefined' && canDeleteTransactions) {
                        // Usuario tiene permisos pero esta transacción específica no se puede eliminar
                        actions += '<span class="text-muted" title="Esta transacción no se puede eliminar" style="width: 32px; height: 32px; display: inline-flex; align-items: center; justify-content: center;"><i class="fas fa-ban"></i></span>';
                    }
                    // Si no tiene permisos de eliminar, no mostrar nada (solo comentarios)
                    
                    return actions;
                },
                className: 'text-center'
            } // 10 - Acciones (Comentarios + Eliminar)
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
        "order": [[4, "asc"]],
        "scrollX": true,
        "autoWidth": false,
        "error": function(xhr, error, code) {
            console.error('Error en DataTable:', error, code);
            console.error('Respuesta del servidor:', xhr.responseText);
        }
    });


    // Event listener se agregará después de la inicialización

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
        
        // Event listener para el botón de exportar Excel
        if (e.target && e.target.id === 'btnExportExcel') {
            exportToExcel();
        }
    });

    // Función para exportar a Excel
    function exportToExcel() {
        const btnExport = document.getElementById('btnExportExcel');
        if (!btnExport) return;

        // Mostrar indicador de carga en el botón
        const originalHTML = btnExport.innerHTML;
        btnExport.innerHTML = '<span class="btn-glow"></span><i class="fas fa-spinner fa-spin me-2"></i>Generando Excel...';
        btnExport.disabled = true;

        // Obtener los filtros actuales (los mismos que usa el DataTable)
        const filters = {
            bank: $('#filtroBank').val() || '',
            account: $('#filtroAccount').val() || '',
            reference: $('#filtroReference').val() || '',
            dateFrom: $('#filtroDateFrom').val() || '',
            dateTo: $('#filtroDateTo').val() || '',
            estado: $('#filtroEstado').val() || '',
            monto: $('#filtroMonto').val() || ''
        };

        // Realizar petición para exportar
        fetch(base_url + '/transaccion/exportToExcel', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(filters)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            
            // Si la respuesta es exitosa, descargar el archivo
            return response.blob();
        })
        .then(blob => {
            // Crear URL para el blob y descargar
            const url = window.URL.createObjectURL(blob);
            const a = document.createElement('a');
            a.style.display = 'none';
            a.href = url;
            
            // Generar nombre de archivo con timestamp
            const timestamp = new Date().toISOString().slice(0, 19).replace(/:/g, '-');
            a.download = `transacciones_${timestamp}.xlsx`;
            
            document.body.appendChild(a);
            a.click();
            window.URL.revokeObjectURL(url);
            document.body.removeChild(a);

            // Mostrar mensaje de éxito
            Swal.fire({
                title: '¡Exportación Exitosa!',
                text: 'El archivo Excel se ha descargado correctamente',
                icon: 'success',
                timer: 3000,
                showConfirmButton: false,
                toast: true,
                position: 'top-end'
            });
        })
        .catch(error => {
            console.error('Error al exportar:', error);
            
            // Mostrar mensaje de error
            Swal.fire({
                title: 'Error al Exportar',
                text: error.message || 'Ocurrió un error al generar el archivo Excel',
                icon: 'error',
                confirmButtonText: 'Entendido'
            });
        })
        .finally(() => {
            // Restaurar el botón
            btnExport.innerHTML = originalHTML;
            btnExport.disabled = false;
        });
    }

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
        const selectAccount = $('#filtroAccount');

        // Si no hay banco seleccionado, deshabilitar el select de cuenta
        if (!selectedBank || selectedBank === '') {
            selectAccount.prop('disabled', true)
                        .empty()
                        .append('<option value="">Seleccione un banco primero</option>');
            return;
        }

        // Habilitar el select de cuenta
        selectAccount.prop('disabled', false);

        // Filtrar cuentas según el banco seleccionado usando los datos originales
        let filteredAccounts = allAccounts;
        if (selectedBank && originalTransactionData.length > 0) {
            filteredAccounts = originalTransactionData
                .filter(item => item.bank === selectedBank)
                .map(item => item.account);
            filteredAccounts = [...new Set(filteredAccounts)];
        }

        // Actualizar select de cuentas
        selectAccount.empty().append('<option value="">Todas las cuentas</option>');
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

    // Para los demás select y campos de fecha
    $('#filtroAccount, #filtroDateFrom, #filtroDateTo, #filtroEstado').on('change', function () {
        tableTransaction.ajax.reload();
    });

    // Filtros de texto (reference y monto) pueden usar input + change
    $('#filtroReference, #filtroMonto').on('input change', function () {
        tableTransaction.ajax.reload();
    });

    $('#transaction-list-table').on('xhr.dt', function () {
        
        const jsonResponse = tableTransaction.ajax.json();
        if (!jsonResponse || !jsonResponse.data) {
            console.error('Error: No se recibieron datos válidos del servidor');
            return;
        }
        
        const data = jsonResponse.data;

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

            // Actualizar filtroAccount inicialmente - se mantendrá deshabilitado hasta seleccionar banco
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
                // Mostrar confirmación antes de procesar
                const result = await Swal.fire({
                    title: '¿Confirmar subida de archivo?',
                    text: `¿Estás seguro de que deseas procesar el archivo para ${strBanco} del período ${strMes}/${strAnio}?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Sí, procesar',
                    cancelButtonText: 'Cancelar',
                    background: '#19233adb',
                    color: '#fff',
                    customClass: {
                        popup: 'futuristic-popup',
                        confirmButton: 'btn-primary-futuristic',
                        cancelButton: 'btn-secondary-futuristic'
                    }
                });

                // Si el usuario cancela, no continuar
                if (!result.isConfirmed) {
                    return false;
                }

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

    // ============================================
    // FUNCIONALIDAD DE COMENTARIOS
    // ============================================

    // Variables globales para el modal de comentarios
    let currentTransactionId = null;
    let currentEmpresaId = null;
    let currentCommentId = null;

    // Event listener para el botón de comentarios
    $('#transaction-list-table tbody').on('click', '.btn-comment', function () {
        const $btn = $(this);
        const id = $btn.data('id');
        const bank = $btn.data('bank');
        const account = $btn.data('account');
        const reference = $btn.data('reference');
        const amount = $btn.data('amount');

        // Guardar datos actuales y referencia al botón
        currentTransactionId = id;
        currentEmpresaId = null; // Se obtiene del backend
        window.currentCommentButton = $btn;

        // Llenar información de la transacción en el modal
        $('#transactionBank').text(bank);
        $('#transactionAccount').text(account);
        $('#transactionReference').text(reference);
        $('#transactionAmount').text(amount);

        // Limpiar secciones del modal
        $('#createCommentSection').addClass('d-none');
        $('#viewCommentSection').addClass('d-none');
        $('#editCommentSection').addClass('d-none');
        $('#noPermissionSection').addClass('d-none');
        $('#saveCommentBtn').addClass('d-none');

        // Verificar si ya existe comentario
        checkExistingComment(id);

        // Mostrar modal
        $('#commentModal').modal('show');
    });

    // Función para verificar comentario existente
    function checkExistingComment(conciliationId) {
        fetch(`${base_url}/transaccion/getComment?conciliation_id=${conciliationId}`, {
            method: 'GET',
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) throw new Error("Error HTTP: " + response.status);
            return response.json();
        })
        .then(data => {
            if (data.status) {
                if (data.has_comment) {
                    // Mostrar comentario existente
                    showExistingComment(data.comment);
                    // Actualizar botón a "ver comentario"
                    if (window.currentCommentButton) {
                        updateCommentButton(window.currentCommentButton, true);
                    }
                } else {
                    // Mostrar formulario para crear comentario
                    if (data.can_comment) {
                        showCreateCommentForm();
                        // Actualizar botón a "agregar comentario"
                        if (window.currentCommentButton) {
                            updateCommentButton(window.currentCommentButton, false);
                        }
                    } else {
                        showNoPermissionMessage();
                    }
                }
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    background: 'rgba(13, 17, 23, 0.95)',
                    color: '#f0f6fc',
                    iconColor: '#dc3545',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Entendido'
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                title: 'Error',
                text: 'Error al verificar comentario: ' + error.message,
                icon: 'error',
                background: 'rgba(13, 17, 23, 0.95)',
                color: '#f0f6fc',
                iconColor: '#dc3545',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Entendido'
            });
        });
    }

    // Función para mostrar comentario existente
    function showExistingComment(comment) {
        $('#modalTitle').text('Ver Comentario');
        $('#commentUser').text(comment.user_name);
        $('#commentText').text(comment.description);
        
        // Guardar ID del comentario para edición
        currentCommentId = comment.comment_id;
        
        // Formatear fecha
        const date = new Date(comment.created_at);
        const formattedDate = date.toLocaleDateString('es-VE') + ' ' + date.toLocaleTimeString('es-VE');
        $('#commentDate').text(formattedDate);
        
        // Mostrar indicador de editado si aplica
        if (comment.updated_at && comment.updated_at !== comment.created_at) {
            // Formatear fecha de edición
            const editDate = new Date(comment.updated_at);
            const formattedEditDate = editDate.toLocaleDateString('es-VE') + ' ' + editDate.toLocaleTimeString('es-VE');
            $('#editedDate').text(formattedEditDate);
            $('#editedIndicator').removeClass('d-none');
        } else {
            $('#editedIndicator').addClass('d-none');
        }
        
        // Mostrar botón de editar solo si el usuario es el propietario
        if (parseInt(comment.user_id) === parseInt(currentUserId)) {
            $('#editCommentBtn').removeClass('d-none');
        } else {
            $('#editCommentBtn').addClass('d-none');
        }
        
        $('#viewCommentSection').removeClass('d-none');
    }

    // Función para mostrar formulario de crear comentario
    function showCreateCommentForm() {
        $('#modalTitle').text('Agregar Comentario');
        $('#commentDescription').val('');
        $('#charCount').text('0');
        $('#createCommentSection').removeClass('d-none');
        $('#saveCommentBtn').removeClass('d-none');
    }

    // Función para mostrar mensaje de sin permisos
    function showNoPermissionMessage() {
        $('#modalTitle').text('Sin Permisos');
        $('#noPermissionSection').removeClass('d-none');
    }

    // Contador de caracteres para el textarea
    $(document).on('input', '#commentDescription', function() {
        const length = $(this).val().length;
        $('#charCount').text(length);
        
        // Cambiar color si se acerca al límite
        if (length > 900) {
            $('#charCount').addClass('text-warning');
        } else {
            $('#charCount').removeClass('text-warning');
        }
    });

    // Event listener para guardar comentario
    $(document).on('click', '#saveCommentBtn', function() {
        const description = $('#commentDescription').val().trim();
        
        if (!description) {
            Swal.fire({
                title: 'Atención',
                text: 'Por favor ingresa un comentario',
                icon: 'warning',
                background: 'rgba(13, 17, 23, 0.95)',
                color: '#f0f6fc',
                iconColor: '#ffc107',
                confirmButtonColor: '#667eea',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        if (description.length > 1000) {
            Swal.fire({
                title: 'Atención',
                text: 'El comentario es demasiado largo (máx 1000 caracteres)',
                icon: 'warning',
                background: 'rgba(13, 17, 23, 0.95)',
                color: '#f0f6fc',
                iconColor: '#ffc107',
                confirmButtonColor: '#667eea',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Deshabilitar botón mientras se guarda
        const btnSave = $(this);
        const originalHTML = btnSave.html();
        btnSave.html('<span class="btn-glow"></span><i class="fas fa-spinner fa-spin me-2"></i>Guardando...').prop('disabled', true);

        // Enviar comentario
        fetch(`${base_url}/transaccion/createComment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                conciliation_id: currentTransactionId,
                description: description
            })
        })
        .then(response => {
            if (!response.ok) throw new Error("Error HTTP: " + response.status);
            return response.json();
        })
        .then(data => {
            if (data.status) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    background: 'rgba(13, 17, 23, 0.95)',
                    color: '#f0f6fc',
                    iconColor: '#28a745',
                    customClass: {
                        popup: 'swal2-show',
                        title: 'swal2-title',
                        content: 'swal2-content'
                    }
                });

                // Cerrar modal
                $('#commentModal').modal('hide');
                
                // Actualizar el botón de la transacción actual
                if (window.currentCommentButton) {
                    updateCommentButton(window.currentCommentButton, true);
                }
                
                // Recargar tabla para mostrar cambios
                tableTransaction.ajax.reload(null, false);
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    background: 'rgba(13, 17, 23, 0.95)',
                    color: '#f0f6fc',
                    iconColor: '#dc3545',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Entendido'
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                title: 'Error',
                text: 'Error al guardar comentario: ' + error.message,
                icon: 'error',
                background: 'rgba(13, 17, 23, 0.95)',
                color: '#f0f6fc',
                iconColor: '#dc3545',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Entendido'
            });
        })
        .finally(() => {
            // Restaurar botón
            btnSave.html(originalHTML).prop('disabled', false);
        });
    });

    // ============================================
    // EVENT LISTENERS PARA EDICIÓN DE COMENTARIOS
    // ============================================

    // Event listener para el botón de editar comentario
    $(document).on('click', '#editCommentBtn', function() {
        // Cambiar a modo edición
        $('#viewCommentSection').addClass('d-none');
        $('#editCommentSection').removeClass('d-none');
        
        // Llenar el textarea con el contenido actual
        const currentText = $('#commentText').text();
        $('#editCommentDescription').val(currentText);
        $('#editCharCount').text(currentText.length);
        
        // Cambiar título del modal
        $('#modalTitle').text('Editar Comentario');
    });

    // Event listener para cancelar edición
    $(document).on('click', '#cancelEditBtn', function() {
        // Volver a modo vista
        $('#editCommentSection').addClass('d-none');
        $('#viewCommentSection').removeClass('d-none');
        
        // Restaurar título del modal
        $('#modalTitle').text('Ver Comentario');
    });

    // Contador de caracteres para el textarea de edición
    $(document).on('input', '#editCommentDescription', function() {
        const length = $(this).val().length;
        $('#editCharCount').text(length);
        
        // Cambiar color si se acerca al límite
        if (length > 900) {
            $('#editCharCount').addClass('text-warning');
        } else {
            $('#editCharCount').removeClass('text-warning');
        }
    });

    // Event listener para actualizar comentario
    $(document).on('click', '#updateCommentBtn', function() {
        const description = $('#editCommentDescription').val().trim();
        
        if (!description) {
            Swal.fire({
                title: 'Atención',
                text: 'Por favor ingresa un comentario',
                icon: 'warning',
                background: 'rgba(13, 17, 23, 0.95)',
                color: '#f0f6fc',
                iconColor: '#ffc107',
                confirmButtonColor: '#667eea',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        if (description.length > 1000) {
            Swal.fire({
                title: 'Atención',
                text: 'El comentario es demasiado largo (máx 1000 caracteres)',
                icon: 'warning',
                background: 'rgba(13, 17, 23, 0.95)',
                color: '#f0f6fc',
                iconColor: '#ffc107',
                confirmButtonColor: '#667eea',
                confirmButtonText: 'Entendido'
            });
            return;
        }

        // Deshabilitar botón mientras se actualiza
        const btnUpdate = $(this);
        const originalHTML = btnUpdate.html();
        btnUpdate.html('<span class="btn-glow"></span><i class="fas fa-spinner fa-spin me-2"></i>Actualizando...').prop('disabled', true);

        // Enviar actualización
        fetch(`${base_url}/transaccion/updateComment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                comment_id: currentCommentId,
                description: description
            })
        })
        .then(response => {
            if (!response.ok) throw new Error("Error HTTP: " + response.status);
            return response.json();
        })
        .then(data => {
            if (data.status) {
                Swal.fire({
                    title: '¡Éxito!',
                    text: data.message,
                    icon: 'success',
                    timer: 2000,
                    timerProgressBar: true,
                    showConfirmButton: false,
                    background: 'rgba(13, 17, 23, 0.95)',
                    color: '#f0f6fc',
                    iconColor: '#28a745'
                });

                // Actualizar el texto del comentario en la vista
                $('#commentText').text(description);
                
                // Mostrar indicador de editado con fecha actual
                const now = new Date();
                const formattedNow = now.toLocaleDateString('es-VE') + ' ' + now.toLocaleTimeString('es-VE');
                $('#editedDate').text(formattedNow);
                $('#editedIndicator').removeClass('d-none');
                
                // Volver a modo vista
                $('#editCommentSection').addClass('d-none');
                $('#viewCommentSection').removeClass('d-none');
                $('#modalTitle').text('Ver Comentario');
                
            } else {
                Swal.fire({
                    title: 'Error',
                    text: data.message,
                    icon: 'error',
                    background: 'rgba(13, 17, 23, 0.95)',
                    color: '#f0f6fc',
                    iconColor: '#dc3545',
                    confirmButtonColor: '#dc3545',
                    confirmButtonText: 'Entendido'
                });
            }
        })
        .catch(error => {
            console.error("Error:", error);
            Swal.fire({
                title: 'Error',
                text: 'Error al actualizar comentario: ' + error.message,
                icon: 'error',
                background: 'rgba(13, 17, 23, 0.95)',
                color: '#f0f6fc',
                iconColor: '#dc3545',
                confirmButtonColor: '#dc3545',
                confirmButtonText: 'Entendido'
            });
        })
        .finally(() => {
            // Restaurar botón
            btnUpdate.html(originalHTML).prop('disabled', false);
        });
    });

}, false);

// ============================================
// FUNCIONES GLOBALES PARA COMENTARIOS
// ============================================


// Función para actualizar el botón de comentario
function updateCommentButton($button, hasComment) {
    if (hasComment) {
        // Cambiar a botón de "ver comentario"
        $button.removeClass('btn-primary').addClass('btn-info');
        $button.attr('title', 'Ver comentario');
        $button.attr('data-has-comment', 'true');
        $button.find('i').removeClass('fa-comment').addClass('fa-eye');
    } else {
        // Mantener como "agregar comentario"
        $button.removeClass('btn-info').addClass('btn-primary');
        $button.attr('title', 'Agregar comentario');
        $button.attr('data-has-comment', 'false');
        $button.find('i').removeClass('fa-eye').addClass('fa-comment');
    }
}

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