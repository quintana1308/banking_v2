document.addEventListener('DOMContentLoaded',function(){

    // Define qué columnas ocultar según el tipo de usuario
    let hiddenColumns = [];

    if (typeUser == 1) {
        hiddenColumns = [4, 7, 9, 10,11,12]; // Por ejemplo: bank, account, client_id
    } else if (typeUser == 2) {
        hiddenColumns = [1, 2, 7, 8, 10,11,12]; // Por ejemplo: client_name, amount
    }else if(typeUser == 3){
		hiddenColumns = [4, 7, 9,11,12];
	}

	tableTransaction = $('#transaction-list-table').DataTable( { 
        "aProcessing":true,
        "aServerSide":true,
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
        "ajax":{
            "url": " "+base_url+"/transaccion/getMovimientos",
            "data":function (d) {
                // incluir filtros personalizados
                d.bank = $('#filtroBank').val();
                d.account = $('#filtroAccount').val();
                d.reference = $('#filtroReference').val();
                d.date = $('#filtroDate').val();
				d.estado = $('#filtroEstado').val();
            }
        },
        "deferLoading": 0, 
        "columns":[
            { data:"id"}, //0
            { data: 'bank' }, //1
            { data: 'account' }, //2
            { data: 'reference' }, //3
            { data: 'date' }, // 5
            {
                "data": "amount",
                "render": function(data, type, row) {
                    let color = parseFloat(data) >= 0 ? 'green' : 'red';
                    return '<span style="color:' + color + ';">' + data + '</span>';
                }
            }, // 6
            { data: 'responsible'}, // 8
			{
				"data": "id",
				"render": function(data, type, row) {
					if (row.id_user) {
						return `<span class="badge bg-success" style="cursor: default;">${row.name_user}</span>`;
					} else {
						return `<button class="btn btn-primary btn-sm btn-asignar" data-id="${data}">Asignar</button>`;
					}
				},
				"orderable": false,
				"searchable": false
			},
			{ data: 'autocon' }, //11
			{ data: 'coincidence' } //12
        ],
        columnDefs: [
            { targets: hiddenColumns, visible: false } // Ocultar bank, account, client_id
        ],
		"rowCallback": function(row, data, index) {
			
			 // Asegura que ambos sean null (no undefined, no 0)
			if (data.autocon === null && data.coincidence === null) {
				// Limpia cualquier color previamente aplicado
				$('td', row).each(function () {
					this.style.setProperty('background-color', '', 'important');
				});
				return;
			}
			
			let color = '';

			if (data.autocon == 1 && data.coincidence == 0) {
				color = '#5bd278'; // verde claro
			} else if (data.autocon == 0 && data.coincidence == 1) {
				color = '#ffe9a6'; // amarillo claro
			} else if (data.autocon == 0 && data.coincidence == 0) {
				color = '#f08790'; // rojo claro
			}

			if (color == '#5bd278') {
				$('td', row).css('background-color', color);
				$('td', row).css('color', 'white');
			}else if(color == '#ffe9a6'){
				$('td', row).css('background-color', color);
			}else{
				$('td', row).css('background-color', color);
				$('td', row).css('color', 'white');
			}
		},
        "resonsieve":"true",
        "bDestroy": true,
        "iDisplayLength": 50,
        "order":[[0,"asc"]]  
    });

    // Para los select
    $('#filtroBank, #filtroAccount, #filtroDate, #filtroEstado').on('change', function () {
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

        // Obtener valores únicos
        const banks = [...new Set(data.map(item => item.bank))];
        const accounts = [...new Set(data.map(item => item.account))];

        // Actualizar filtroBank
        const selectBank = $('#filtroBank').empty().append('<option value="">Todos</option>');
        banks.forEach(bank => {
            if (bank) {
                const selected = bank === selectedBank ? 'selected' : '';
                selectBank.append(`<option value="${bank}" ${selected}>${bank}</option>`);
            }
        });

        // Actualizar filtroAccount
        const selectAccount = $('#filtroAccount').empty().append('<option value="">Todas</option>');
        accounts.forEach(account => {
            if (account) {
                const selected = account === selectedAccount ? 'selected' : '';
                selectAccount.append(`<option value="${account}" ${selected}>${account}</option>`);
            }
        });
    });

    const editableColumns = {
        reference: 3,
        date: 5,
        amount: 6,
    };

    $('#transaction-list-table tbody').on('dblclick', 'td', function () {	
		
		const cell = tableTransaction.cell(this);
        const rowData = tableTransaction.row(this).data();
        const columnIndex = cell.index().column;
		
        const columnName = Object.keys(editableColumns).find(key => editableColumns[key] === columnIndex);
	
        if (!columnName) return;
        const currentValue = cell.data();
		
        // Inputs para reference, date, amount
        const inputType = (columnName === 'date') ? 'date' : 'text';
        if ($(this).find('input').length > 0) return;

        $(this).html(`<input type="${inputType}" class="form-control form-control-sm" value="${currentValue}" />`);
        const input = $(this).find('input');
        input.focus();

        input.on('blur keyup', function (e) {
            if (e.type === 'blur' || (e.type === 'keyup' && e.key === 'Enter')) {
                const newValue = input.val();
                if (newValue !== currentValue) {
                    updateTransactionField(rowData.id, columnName, newValue, cell);
                } else {
                    cell.data(currentValue).draw();
                }
            }
        });
    });


    if(document.querySelector("#formNewTransaction")){
		let formNewTransaction = document.querySelector("#formNewTransaction");
		formNewTransaction.onsubmit = function(e) {
			e.preventDefault();

			let strAnio = document.querySelector('#anio').value;
            let strMes = document.querySelector('#mes').value;
            let strBanco = document.querySelector('#banco').value;
            let strArchive = document.querySelector('#archive').value;

			if(strAnio == "" || strMes == "" || strBanco == "" || strArchive == "")
			{
				Swal.fire("Por favor", "Todos los campos son obligatorios.", "error");
				return false;
			}else{

				// Mostrar loader
				let divLoading = document.querySelector("#loading-content");
				divLoading.classList.remove('d-none');

				var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
				var ajaxUrl = base_url+'/transaccion/setTransaction'; 
				var formData = new FormData(formNewTransaction);
				request.open("POST",ajaxUrl,true);
				request.send(formData);
				request.onreadystatechange = function(){
					if(request.readyState != 4) return;

					// Ocultar loader
					divLoading.classList.add('d-none');

					if(request.status == 200){
						var objData = JSON.parse(request.responseText);
						if(objData.status)
						{	
							Swal.fire('Exito',objData.msg,'success');
						}else{
							Swal.fire('Atención',objData.msg,'error');
						}
					}else{
						Swal.fire("Atención","El archivo no se proceso de manera correcta", "warning");
						console.warn("Advertencia: Probablemente no hay creditos para generar el archivo.");
					}
					divLoading.style.display = "none";
					return false;
				}
			}
		}
	}
	
	if(document.querySelector("#formFilterTransaction")){
		
		let formFilterTransaction = document.querySelector("#formFilterTransaction");
		formFilterTransaction.onsubmit = function(e) {
			e.preventDefault();
			
			let strAccount = document.querySelector('#filterAccount').value;

			if(strAccount == "" )
			{
				Swal.fire("Por favor", "La cuenta es obligatoria para chequear los movimientos bancarios.", "error");
				return false;
			}else{
				
				// Mostrar loader
				let divLoading = document.querySelector("#loading");
				divLoading.classList.remove('d-none');

				var request = (window.XMLHttpRequest) ? new XMLHttpRequest() : new ActiveXObject('Microsoft.XMLHTTP');
				var ajaxUrl = base_url+'/transaccion/checkTransaccion'; 
				var formData = new FormData(formFilterTransaction);
				request.open("POST",ajaxUrl,true);
				request.send(formData);
				request.onreadystatechange = function(){
					if(request.readyState != 4) return;

					// Ocultar loader
					divLoading.classList.add('d-none');

					if(request.status == 200){
						var objData = JSON.parse(request.responseText);
						if(objData.status)
						{	
							let content = '';
							content += 'Resultado de Movimientos <br>';
							content += 'Consolidados: ' + objData.msg.completos + '.<br>';
							content += 'Coincidieron: ' + objData.msg.parciales + '.<br>';
							content += 'No consolidados: ' + objData.msg.sin_coincidencia + '.';

							Swal.fire('Completado', content, 'success').then(() => {
								window.location = base_url + '/transaccion';
							});
						}else{
							Swal.fire('Atención',objData.msg,'error');
							//console.log(objData.msg);
						}
					}else{
						Swal.fire("Atención","El chequeo no se realizo de manera correcta", "warning");
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
				Swal.fire('Asignado', data.message, 'success');
				tableTransaction.ajax.reload();
			} else {
				Swal.fire('Error', data.message, 'error');
			}
		})
		.catch((error) => {
			console.error("Error en fetch:", error);
			Swal.fire('Error', 'Error en la solicitud: ' + error.message, 'error');
		});
	});

},false);

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
